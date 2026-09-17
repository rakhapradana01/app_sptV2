<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use App\Models\Bidang;
use App\Models\SubBidang;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SubKegiatanController extends Controller
{
    public function index()
    {
        $this->syncSubBidangDanPptk();

        $query = SubKegiatan::query();
        $user = auth()->user();
        $role = $user?->role?->name;

        // Filter berdasarkan hierarki role
        if ($role === 'kepala_sub_bidang') {
            // Kasubid hanya melihat sub kegiatan di sub bidangnya
            // sub_bidang_id sudah diisi saat import Excel dan di-sync oleh syncSubBidangDanPptk()
            if ($user->sub_bidang_id) {
                $query->where('sub_bidang_id', $user->sub_bidang_id);
            } else {
                // Jika masih belum punya sub_bidang_id, refresh dulu dari DB (setelah sync)
                $user->refresh();
                if ($user->sub_bidang_id) {
                    $query->where('sub_bidang_id', $user->sub_bidang_id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        } elseif (in_array($role, ['admin', 'kepala_bidang'])) {
            // Admin & Kabid melihat semua sub kegiatan di bidangnya
            if ($user->bidang_id) {
                $query->where('bidang_id', $user->bidang_id);
            }
        } elseif ($role === 'kepala_badan') {
            // Kaban melihat semua sub kegiatan di dinasnya
            if ($user->dinas_id) {
                $query->where('dinas_id', $user->dinas_id);
            }
        }
        // super_admin: tidak ada filter, lihat semua

        $subKegiatan = $query->paginate(10);
        $dinas = Dinas::orderBy('nama_dinas')->get();

        $subBidangs = collect();
        if ($user?->bidang_id) {
            $subBidangs = \App\Models\SubBidang::where('bidang_id', $user->bidang_id)->orderBy('nama_sub_bidang')->get();
        }

        return view('pages.master.sub_kegiatan.index', compact('subKegiatan', 'dinas', 'subBidangs'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'nama_kegiatan' => 'required|string|max:255',
            'nomor_rekening' => 'required|string',
            'harga_satuan' => 'nullable|integer|min:0',
            'koefisien' => 'nullable|integer|min:0',
            'pagu' => 'nullable|integer|min:0',
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidangs,id',
        ];

        $validated = $request->validate($rules);

        $validated['harga_satuan'] = $validated['harga_satuan'] ?? 0;
        $validated['koefisien'] = $validated['koefisien'] ?? 0;
        $validated['pagu'] = $validated['pagu'] ?? 0;

        // Auto-fill dinas_id, bidang_id, sub_bidang_id dari user yang login jika tersedia
        $validated['dinas_id'] = $user->dinas_id ?? ($validated['dinas_id'] ?? null);
        $validated['bidang_id'] = $user->bidang_id ?? ($validated['bidang_id'] ?? null);
        $validated['sub_bidang_id'] = $user->sub_bidang_id ?? ($validated['sub_bidang_id'] ?? null);

        // Tentukan PPTK / Kasubid sesuai Sub Bidang
        $kasubidUser = null;
        if (!empty($validated['sub_bidang_id'])) {
            $kasubidUser = \App\Models\User::where('sub_bidang_id', $validated['sub_bidang_id'])
                ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                ->with('pegawai')
                ->first()
                ?? \App\Models\User::where('sub_bidang_id', $validated['sub_bidang_id'])->with('pegawai')->first();
        }

        if ($kasubidUser) {
            $validated['user_id'] = $kasubidUser->id;
            $validated['pegawai_kasubid_id'] = $kasubidUser->pegawai_id ?? $kasubidUser->pegawai?->id;
        } else {
            $validated['user_id'] = $user->id;
            $validated['pegawai_kasubid_id'] = $user->pegawai_id ?? $user->pegawai?->id;
        }

        SubKegiatan::create($validated);

        return redirect()->back()->with('success', 'Sub Kegiatan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_kasubid_id' => 'nullable|exists:pegawais,id',
            'sub_bidang_id'      => 'nullable|exists:sub_bidangs,id',
            'nama_kegiatan'      => 'required|string',
            'nomor_rekening'     => 'required|string',
            'harga_satuan'       => 'nullable|integer|min:0',
            'koefisien'          => 'nullable|integer|min:0',
            'pagu'               => 'nullable|integer|min:0',
        ]);

        $sub = SubKegiatan::findOrFail($id);

        $subBidangId = $request->has('sub_bidang_id') ? $request->sub_bidang_id : $sub->sub_bidang_id;
        $kasubidUser = null;
        if ($subBidangId) {
            $kasubidUser = \App\Models\User::where('sub_bidang_id', $subBidangId)
                ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                ->with('pegawai')
                ->first()
                ?? \App\Models\User::where('sub_bidang_id', $subBidangId)->with('pegawai')->first();
        }

        $userId = $kasubidUser ? $kasubidUser->id : $sub->user_id;
        $pegawaiId = $request->pegawai_kasubid_id
            ?? ($kasubidUser ? ($kasubidUser->pegawai_id ?? $kasubidUser->pegawai?->id) : $sub->pegawai_kasubid_id);

        $sub->update([
            'nama_kegiatan'      => $request->nama_kegiatan,
            'nomor_rekening'     => $request->nomor_rekening,
            'harga_satuan'       => $request->harga_satuan ?? 0,
            'koefisien'          => $request->koefisien ?? 0,
            'pagu'               => $request->pagu ?? 0,
            'sub_bidang_id'      => $subBidangId,
            'user_id'            => $userId,
            'pegawai_kasubid_id' => $pegawaiId,
        ]);

        return response()->json([
            'success' => 'Sub Kegiatan Berhasil Diubah!'
        ]);
    }

    public function show($id)
    {
        return response()->json(SubKegiatan::with(['owner', 'pegawai', 'subBidang'])->findOrFail($id));
    }

    public function destroy($id){
        $subkeg = SubKegiatan::findOrFail($id);
        $subkeg->delete();

        return redirect()->route('sub-kegiatan.index')->with('success', 'Sub Kegiatan berhasil dihapus.');
    }

    /**
     * Import Sub Kegiatan dari file Excel (.xlsx / .xls)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel'    => 'required|file|mimes:xlsx,xls|max:2048',
            'dinas_id'      => 'nullable|exists:dinas,id',
            'bidang_id'     => 'nullable|exists:bidangs,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidangs,id',
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $user = auth()->user();

        // Target dinas, bidang, sub_bidang default dari request (modal) atau profil user
        $defaultDinasId     = $user->dinas_id ?? ($request->filled('dinas_id') ? $request->dinas_id : null);
        $defaultBidangId    = $user->bidang_id ?? ($request->filled('bidang_id') ? $request->bidang_id : null);
        $defaultSubBidangId = $user->sub_bidang_id ?? ($request->filled('sub_bidang_id') ? $request->sub_bidang_id : null);

        $filePath    = $request->file('file_excel')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        // Deteksi kolom secara dinamis dari header baris 1
        $headerMap = [
            'rekening'   => 'A',
            'kegiatan'   => 'B',
            'koefisien'  => 'C',
            'pagu'       => 'D',
            'sub_bidang' => 'E',
            'bidang'     => 'F',
            'pptk'       => null,
        ];

        if (isset($rows[1])) {
            foreach ($rows[1] as $colKey => $headerText) {
                $h = strtolower(trim((string)$headerText));
                if (str_contains($h, 'rekening')) {
                    $headerMap['rekening'] = $colKey;
                } elseif (str_contains($h, 'sub_bidang') || str_contains($h, 'sub bidang') || str_contains($h, 'subbagian') || str_contains($h, 'sub bagian')) {
                    $headerMap['sub_bidang'] = $colKey;
                } elseif (str_contains($h, 'kegiatan') || str_contains($h, 'program') || str_contains($h, 'uraian')) {
                    $headerMap['kegiatan'] = $colKey;
                } elseif (str_contains($h, 'koefisien') || $h === 'ok') {
                    $headerMap['koefisien'] = $colKey;
                } elseif (str_contains($h, 'pagu') || str_contains($h, 'anggaran') || str_contains($h, 'nominal')) {
                    $headerMap['pagu'] = $colKey;
                } elseif (str_contains($h, 'bidang')) {
                    $headerMap['bidang'] = $colKey;
                } elseif (str_contains($h, 'pptk') || str_contains($h, 'kasubid')) {
                    $headerMap['pptk'] = $colKey;
                }
            }
        }

        $berhasil              = 0;
        $gagal                 = [];
        $rekeningSudahDiproses = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) continue; // Lewati header

            $nomorRekening = trim((string) ($row[$headerMap['rekening']] ?? ''));
            $namaKegiatan  = trim((string) ($row[$headerMap['kegiatan']] ?? ''));
            $koefisienRaw  = $headerMap['koefisien'] ? ($row[$headerMap['koefisien']] ?? 0) : 0;
            $paguRaw       = $headerMap['pagu'] ? ($row[$headerMap['pagu']] ?? 0) : 0;
            $subBidangNama = $headerMap['sub_bidang'] ? trim((string) ($row[$headerMap['sub_bidang']] ?? '')) : '';
            $bidangNama    = $headerMap['bidang'] ? trim((string) ($row[$headerMap['bidang']] ?? '')) : '';
            $pptkNama      = $headerMap['pptk'] ? trim((string) ($row[$headerMap['pptk']] ?? '')) : '';

            // Lewati baris kosong total
            if (empty($nomorRekening) && empty($namaKegiatan)) {
                continue;
            }

            // Validasi kolom wajib
            if (empty($nomorRekening) || empty($namaKegiatan)) {
                $gagal[] = "Baris {$rowIndex}: Nomor Rekening dan Nama Program/Sub Kegiatan wajib diisi.";
                continue;
            }

            // Sanitasi koefisien
            $koefisien = 0;
            if (is_numeric($koefisienRaw)) {
                $koefisien = (int) $koefisienRaw;
            } else {
                $koefClean = preg_replace('/[^0-9]/', '', (string) $koefisienRaw);
                $koefisien = !empty($koefClean) ? (int) $koefClean : 0;
            }

            // Sanitasi pagu
            $pagu = 0;
            if (is_numeric($paguRaw)) {
                $pagu = (int) round((float) $paguRaw);
            } else {
                $paguClean = preg_replace('/[^\d,\.]/', '', (string) $paguRaw);
                if (str_contains($paguClean, ',') && !str_contains($paguClean, '.')) {
                    $paguClean = str_replace(',', '.', $paguClean);
                } elseif (str_contains($paguClean, '.') && str_contains($paguClean, ',')) {
                    $paguClean = str_replace('.', '', $paguClean);
                    $paguClean = str_replace(',', '.', $paguClean);
                }
                $pagu = !empty($paguClean) ? (int) round((float) $paguClean) : 0;
            }

            // Cari bidang jika dicantumkan di excel atau gunakan default
            $dinasId  = $defaultDinasId;
            $bidangId = $defaultBidangId;
            if (!$bidangId && !empty($bidangNama)) {
                $bidangRecord = Bidang::where('nama_bidang', 'like', "%{$bidangNama}%")
                    ->when($dinasId, fn($q) => $q->where('dinas_id', $dinasId))
                    ->first();
                if ($bidangRecord) {
                    $bidangId = $bidangRecord->id;
                    $dinasId  = $dinasId ?? $bidangRecord->dinas_id;
                }
            }

            // Resolusi Sub Bidang dan PPTK secara cerdas dari teks Excel
            $subBidangId          = null;
            $subKegiatanUserId    = null;
            $subKegiatanPegawaiId = null;

            if (!empty($subBidangNama)) {
                [$subBidangId, $subKegiatanUserId, $subKegiatanPegawaiId] = $this->resolveSubBidangAndPptk($subBidangNama, $bidangId, $dinasId);
            }

            // Jika ada kolom PPTK spesifik di baris Excel, override PPTK
            if (!empty($pptkNama)) {
                $pptkUser = \App\Models\User::where('name', 'like', "%{$pptkNama}%")
                    ->orWhere('username', $pptkNama)
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$pptkNama}%")->orWhere('nip', $pptkNama))
                    ->first();
                if ($pptkUser) {
                    $subKegiatanUserId    = $pptkUser->id;
                    $subKegiatanPegawaiId = $pptkUser->pegawai_id ?? $pptkUser->pegawai?->id;
                    if (!$subBidangId && $pptkUser->sub_bidang_id) {
                        $subBidangId = $pptkUser->sub_bidang_id;
                    }
                }
            }

            // Jika belum dapat sub_bidang_id, gunakan default dari modal / profil
            if (!$subBidangId && $defaultSubBidangId) {
                $subBidangId = $defaultSubBidangId;
            }

            // Jika belum dapat PPTK tapi sub_bidang_id ada, cari Kasubid dari Sub Bidang
            if (!$subKegiatanUserId && $subBidangId) {
                $kasubidUser = \App\Models\User::where('sub_bidang_id', $subBidangId)
                    ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                    ->with('pegawai')
                    ->first()
                    ?? \App\Models\User::where('sub_bidang_id', $subBidangId)->with('pegawai')->first();

                if ($kasubidUser) {
                    $subKegiatanUserId    = $kasubidUser->id;
                    $subKegiatanPegawaiId = $kasubidUser->pegawai_id ?? $kasubidUser->pegawai?->id;
                }
            }

            // Fallback jika tidak ada kasubid di sub bidang
            if (!$subKegiatanUserId) {
                $subKegiatanUserId    = $user->id;
                $subKegiatanPegawaiId = $user->pegawai_id ?? $user->pegawai?->id;
            }

            // Cek duplikasi dalam file excel yang sama
            $dupKey = strtolower($nomorRekening) . '_' . ($dinasId ?? '0') . '_' . ($bidangId ?? '0');
            if (in_array($dupKey, $rekeningSudahDiproses)) {
                $gagal[] = "Baris {$rowIndex}: Nomor Rekening '{$nomorRekening}' muncul duplikat dalam file.";
                continue;
            }
            $rekeningSudahDiproses[] = $dupKey;

            // Update jika nomor rekening sudah ada, atau buat baru
            $subExisting = SubKegiatan::where('nomor_rekening', $nomorRekening)
                ->when($dinasId, fn($q) => $q->where('dinas_id', $dinasId))
                ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                ->first();

            if ($subExisting) {
                $subExisting->update([
                    'nama_kegiatan'      => $namaKegiatan,
                    'koefisien'          => $koefisien,
                    'harga_satuan'       => 0,
                    'pagu'               => $pagu,
                    'sub_bidang_id'      => $subBidangId ?? $subExisting->sub_bidang_id,
                    'user_id'            => $subKegiatanUserId,
                    'pegawai_kasubid_id' => $subKegiatanPegawaiId,
                ]);
            } else {
                SubKegiatan::create([
                    'nomor_rekening'     => $nomorRekening,
                    'nama_kegiatan'      => $namaKegiatan,
                    'koefisien'          => $koefisien,
                    'harga_satuan'       => 0,
                    'pagu'               => $pagu,
                    'dinas_id'           => $dinasId,
                    'bidang_id'          => $bidangId,
                    'sub_bidang_id'      => $subBidangId,
                    'user_id'            => $subKegiatanUserId,
                    'pegawai_kasubid_id' => $subKegiatanPegawaiId,
                ]);
            }

            $berhasil++;
        }

        $pesan = "Import selesai: {$berhasil} Sub Kegiatan berhasil ditambahkan.";
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' baris dilewati.';
            return redirect()->route('sub-kegiatan.index')
                ->with('import_success', $pesan)
                ->with('import_errors', $gagal);
        }

        return redirect()->route('sub-kegiatan.index')->with('success', $pesan);
    }

    /**
     * Unduh template Excel untuk import Sub Kegiatan
     */
    public function downloadTemplate()
    {
        $user        = auth()->user();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Sub Kegiatan');

        $headers = [
            'A' => 'Nomor Rekening',
            'B' => 'Nama Program / Sub Kegiatan',
            'C' => 'Koefisien (OK)',
            'D' => 'Pagu Anggaran (Rp)',
            'E' => 'Sub Bidang',
            'F' => 'Bidang',
            'G' => 'PPTK (Opsional)',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}1", $label);
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A7F3D0']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $bidangContoh    = $user->bidang?->nama_bidang ?? 'PAD';
        $subBidangContoh = $user->subBidang?->nama_sub_bidang ?? 'Sub Bidang PAD 1';

        $contoh = [
            ['5.02.02.1.01.0024', 'Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun', 10, 50000000, $subBidangContoh, $bidangContoh, ''],
            ['5.02.02.1.01.0025', 'Penyusunan Standar Operasional Prosedur Pelayanan', 5, 25000000, $subBidangContoh, $bidangContoh, ''],
        ];

        foreach ($contoh as $i => $baris) {
            $row = $i + 2;
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $j => $col) {
                $sheet->setCellValue("{$col}{$row}", $baris[$j]);
            }
            $bg = $i % 2 === 0 ? 'F0FDF4' : 'FFFFFF';
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1FAE5']]],
            ]);
        }

        $noteRow = 5;
        $note = '* Catatan: Kolom Nomor Rekening dan Nama Program/Sub Kegiatan wajib diisi. Kolom Koefisien dan Pagu opsional (diisi angka). PPTK otomatis disesuaikan dengan Kasubid dari Sub Bidang masing-masing.';
        $sheet->mergeCells("A{$noteRow}:G{$noteRow}");
        $sheet->setCellValue("A{$noteRow}", $note);
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer   = new Xlsx($spreadsheet);
        $filename = 'template_import_sub_kegiatan.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Resolusi Sub Bidang dan PPTK (Kasubid) dari teks Excel
     */
    private function resolveSubBidangAndPptk($subBidangNama, $bidangId = null, $dinasId = null)
    {
        $raw = trim((string)$subBidangNama);
        if (empty($raw)) {
            return [null, null, null];
        }

        $subBidang   = null;
        $pegawai     = null;
        $kasubidUser = null;

        // 1. Pemetaan cerdas jika berisi "Perencanaan Anggaran" / "PAD" (dengan angka romawi atau arab)
        if (preg_match('/(perencanaan\s*anggaran|pad)/i', $raw)) {
            if (preg_match('/\b(iii|3)\b/i', $raw)) {
                $subBidang = SubBidang::where('nama_sub_bidang', 'like', '%PAD 3%')
                    ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                    ->first();
                $pegawai = \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%III%')->first();
            } elseif (preg_match('/\b(ii|2)\b/i', $raw)) {
                $subBidang = SubBidang::where('nama_sub_bidang', 'like', '%PAD 2%')
                    ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                    ->first();
                $pegawai = \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%II%')->first();
            } elseif (preg_match('/\b(i|1)\b/i', $raw)) {
                $subBidang = SubBidang::where('nama_sub_bidang', 'like', '%PAD 1%')
                    ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                    ->first();
                $pegawai = \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%I%')
                    ->where('jabatan', 'not like', '%II%')
                    ->where('jabatan', 'not like', '%III%')
                    ->first();
            }
        }

        // 2. Coba cari Pegawai berdasarkan Jabatan (misal: "Kepala Sub Bidang...")
        if (!$pegawai) {
            $pegawai = \App\Models\Pegawai::where('jabatan', 'like', "%{$raw}%")
                ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                ->first();
        }

        // 3. Coba cari SubBidang langsung
        if (!$subBidang) {
            $subBidang = SubBidang::where('nama_sub_bidang', 'like', "%{$raw}%")
                ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                ->first();
        }

        // 4. Coba cari SubBidang dengan menghilangkan awalan "Kepala", "Sub Bidang", "Sub Bagian"
        if (!$subBidang) {
            $clean = trim(preg_replace('/^(kepala\s+)?(sub\s*(bidang|bagian))\s*/i', '', $raw));
            if (!empty($clean)) {
                $subBidang = SubBidang::where('nama_sub_bidang', 'like', "%{$clean}%")
                    ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                    ->first();
            }
        }

        // 5. Coba cari berdasarkan nama Kasubid/Pegawai jika teks adalah nama orang
        if (!$pegawai) {
            $pegawai = \App\Models\Pegawai::where('nama', 'like', "%{$raw}%")
                ->when($bidangId, fn($q) => $q->where('bidang_id', $bidangId))
                ->first();
        }

        // 6. Hubungkan ke User Kasubid
        if ($pegawai) {
            $kasubidUser = \App\Models\User::where('pegawai_id', $pegawai->id)->first();
            if ($kasubidUser && !$subBidang && $kasubidUser->sub_bidang_id) {
                $subBidang = SubBidang::find($kasubidUser->sub_bidang_id);
            }
        }

        if (!$kasubidUser && $subBidang) {
            $kasubidUser = \App\Models\User::where('sub_bidang_id', $subBidang->id)
                ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                ->with('pegawai')
                ->first()
                ?? \App\Models\User::where('sub_bidang_id', $subBidang->id)->with('pegawai')->first();
        }

        $finalSubBidangId = $subBidang?->id;
        $finalUserId      = $kasubidUser?->id;
        $finalPegawaiId   = $pegawai?->id ?? ($kasubidUser?->pegawai_id ?? $kasubidUser?->pegawai?->id);

        return [$finalSubBidangId, $finalUserId, $finalPegawaiId];
    }

    /**
     * Sinkronisasi Sub Kegiatan lama agar PPTK sesuai dengan Kasubid Sub Bidang
     */
    private function syncSubBidangDanPptk()
    {
        try {
            // Ambil Sub Bidang PAD 1, 2, 3 dari database
            $sbPad1 = SubBidang::where('nama_sub_bidang', 'like', '%PAD 1%')->first();
            $sbPad2 = SubBidang::where('nama_sub_bidang', 'like', '%PAD 2%')->first();
            $sbPad3 = SubBidang::where('nama_sub_bidang', 'like', '%PAD 3%')->first();

            // Pegawai Kasubid PAD masing-masing
            $pegPad1 = \App\Models\Pegawai::where('nama', 'like', '%KHARIS%')->first()
                ?? \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%I%')
                    ->where('jabatan', 'not like', '%II%')
                    ->where('jabatan', 'not like', '%III%')
                    ->first();
            $pegPad2 = \App\Models\Pegawai::where('nama', 'like', '%ARIEF%')->first()
                ?? \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%II%')->first();
            $pegPad3 = \App\Models\Pegawai::where('nama', 'like', '%YENNI%')->first()
                ?? \App\Models\Pegawai::where('jabatan', 'like', '%Perencanaan Anggaran%III%')->first();

            // -------------------------------------------------------
            // STEP 1: Hubungkan akun user kasubid PAD ke sub_bidang_id & pegawai_id
            // PENTING: hanya cocokkan via username yang spesifik
            // Catatan: 'kasubid3' adalah akun BMD (bukan PAD 3). Akun PAD 3 adalah 'kasubidpad3'.
            // -------------------------------------------------------
            if ($sbPad1) {
                \App\Models\User::whereIn('username', ['kasubid1', 'kasubidpad1'])->update([
                    'sub_bidang_id' => $sbPad1->id,
                    'bidang_id'     => $sbPad1->bidang_id,
                    'pegawai_id'    => $pegPad1?->id,
                ]);
            }

            if ($sbPad2) {
                \App\Models\User::whereIn('username', ['kasubid2', 'kasubidpad2'])->update([
                    'sub_bidang_id' => $sbPad2->id,
                    'bidang_id'     => $sbPad2->bidang_id,
                    'pegawai_id'    => $pegPad2?->id,
                ]);
            }

            if ($sbPad3) {
                \App\Models\User::where('username', 'kasubidpad3')->update([
                    'sub_bidang_id' => $sbPad3->id,
                    'bidang_id'     => $sbPad3->bidang_id,
                    'pegawai_id'    => $pegPad3?->id,
                ]);
            }

            // -------------------------------------------------------
            // STEP 2: Untuk user kepala_sub_bidang yang sub_bidang_id masih null,
            // gunakan jabatan pegawai — cek III dulu, lalu II, baru I
            // -------------------------------------------------------
            \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                ->whereNull('sub_bidang_id')
                ->with('pegawai')
                ->get()
                ->each(function ($u) use ($sbPad1, $sbPad2, $sbPad3) {
                    $jbt = strtoupper($u->pegawai?->jabatan ?? '');
                    if (empty($jbt)) return;

                    if (str_contains($jbt, 'III') || str_contains($jbt, 'PAD 3')) {
                        if ($sbPad3) $u->update(['sub_bidang_id' => $sbPad3->id, 'bidang_id' => $sbPad3->bidang_id]);
                    } elseif (str_contains($jbt, 'II') || str_contains($jbt, 'PAD 2')) {
                        if ($sbPad2) $u->update(['sub_bidang_id' => $sbPad2->id, 'bidang_id' => $sbPad2->bidang_id]);
                    } elseif (
                        str_contains($jbt, ' I ') || str_ends_with($jbt, ' I') ||
                        str_contains($jbt, 'PAD 1') || str_contains($jbt, 'DAERAH I')
                    ) {
                        if ($sbPad1) $u->update(['sub_bidang_id' => $sbPad1->id, 'bidang_id' => $sbPad1->bidang_id]);
                    }
                });

            // -------------------------------------------------------
            // STEP 3: Pemetaan nomor rekening Sub Kegiatan PAD ke Sub Bidang yg tepat
            // -------------------------------------------------------
            $rekMap = [
                '5.02.02.1.01.0001' => $sbPad2,
                '5.02.02.1.01.0002' => $sbPad2,
                '5.02.02.1.01.0003' => $sbPad2,
                '5.02.02.1.01.0004' => $sbPad2,
                '5.02.02.1.01.0007' => $sbPad1,
                '5.02.02.1.01.0008' => $sbPad1,
                '5.02.02.1.01.0009' => $sbPad1,
                '5.02.02.1.01.0011' => $sbPad2,
                '5.02.02.1.01.0013' => $sbPad3,
                '5.02.02.1.02.0002' => $sbPad3,
                '5.02.02.1.02.0003' => $sbPad3,
                '5.02.02.1.02.0011' => $sbPad3,
                '5.02.02.1.06.0002' => $sbPad1,
            ];

            foreach ($rekMap as $rek => $sb) {
                if (!$sb) continue;
                SubKegiatan::where('nomor_rekening', $rek)
                    ->where(function ($q) use ($sb) {
                        $q->whereNull('sub_bidang_id')
                          ->orWhere('sub_bidang_id', '!=', $sb->id);
                    })
                    ->update([
                        'sub_bidang_id' => $sb->id,
                        'bidang_id'     => $sb->bidang_id,
                        'dinas_id'      => $sb->bidang?->dinas_id,
                    ]);
            }

            // -------------------------------------------------------
            // STEP 4: Perbarui sub kegiatan — pastikan user_id & pegawai_kasubid_id
            // diisi dari kasubid pemilik sub bidang tersebut
            // -------------------------------------------------------
            $adminRoleIds = \App\Models\Role::whereIn('name', ['admin', 'super_admin'])->pluck('id')->toArray();
            $allSubBidangs = array_filter([$sbPad1, $sbPad2, $sbPad3]);

            foreach ($allSubBidangs as $sb) {
                $kasubid = \App\Models\User::where('sub_bidang_id', $sb->id)
                    ->whereHas('role', fn($q) => $q->where('name', 'kepala_sub_bidang'))
                    ->first()
                    ?? \App\Models\User::where('sub_bidang_id', $sb->id)->first();

                if (!$kasubid) continue;

                $pegawaiId = $kasubid->pegawai_id ?? $kasubid->pegawai?->id;

                SubKegiatan::where('sub_bidang_id', $sb->id)
                    ->where(function ($q) use ($adminRoleIds, $kasubid) {
                        $q->whereNull('user_id')
                          ->orWhere('user_id', '!=', $kasubid->id)
                          ->orWhereHas('owner', fn($subQ) => $subQ->whereIn('role_id', $adminRoleIds));
                    })
                    ->update([
                        'user_id'            => $kasubid->id,
                        'pegawai_kasubid_id' => $pegawaiId,
                        'bidang_id'          => $sb->bidang_id,
                        'dinas_id'           => $sb->bidang?->dinas_id,
                    ]);
            }

        } catch (\Throwable $e) {
            \Log::warning('syncSubBidangDanPptk warning: ' . $e->getMessage());
        }
    }
}