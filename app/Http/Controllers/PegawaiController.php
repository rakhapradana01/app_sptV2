<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Dinas;
use App\Models\Bidang;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PegawaiController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Pegawai::with(['dinas', 'bidang'])->latest();

        if ($user) {
            if ($user->dinas_id) {
                $query->where('dinas_id', $user->dinas_id);
                $bidangs = Bidang::where('dinas_id', $user->dinas_id)->get();
                $dinas = Dinas::where('id', $user->dinas_id)->get();
            } else {
                $bidangs = Bidang::all();
                $dinas = Dinas::all();
            }
            if ($user->bidang_id) {
                $query->where('bidang_id', $user->bidang_id);
            }
        } else {
            $bidangs = Bidang::all();
            $dinas = Dinas::all();
        }

        $pegawais = $query->paginate(10);
        return view('pages.master.pegawai.index', compact('pegawais', 'dinas', 'bidangs'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama'     => 'required',
            'nip'      => 'required|unique:pegawais,nip',
            'pangkat'  => 'required',
            'jabatan'  => 'required',
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id'=> 'nullable|exists:bidangs,id',
        ]);

        $data = $request->all();
        if ($user && $user->dinas_id) {
            $data['dinas_id'] = $user->dinas_id;
        }
        if ($user && $user->bidang_id) {
            $data['bidang_id'] = $user->bidang_id;
        }

        Pegawai::create($data);

        return redirect()->route('pegawai.index')
            ->with('success', 'Pegawai berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'nip'      => 'required|string|unique:pegawais,nip,' . $id,
            'pangkat'  => 'required|string',
            'jabatan'  => 'nullable|string',
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id'=> 'nullable|exists:bidangs,id',
        ]);
        $pegawai = Pegawai::findOrFail($id);

        $data = [
            'nama'     => $request->nama,
            'nip'      => $request->nip,
            'pangkat'  => $request->pangkat,
            'jabatan'  => $request->jabatan,
            'bidang_id'=> $request->bidang_id,
        ];

        $user = auth()->user();
        if (!$user->dinas_id) {
            $data['dinas_id'] = $request->dinas_id;
        }
        if ($user && $user->bidang_id) {
            $data['bidang_id'] = $user->bidang_id;
        }

        $pegawai->update($data);

        return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();
        return redirect()->route('pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    /**
     * Import pegawai dari file Excel (.xlsx / .xls)
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:2048',
        ], [
            'file_excel.required' => 'File Excel wajib dipilih.',
            'file_excel.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file_excel.max'      => 'Ukuran file maksimal 2 MB.',
        ]);

        $user        = auth()->user();
        $filePath    = $request->file('file_excel')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, true);

        $berhasil         = 0;
        $gagal            = [];
        $nipSudahDiproses = [];

        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex === 1) continue;

            $nama       = trim($row['A'] ?? '');
            $nip        = trim((string) ($row['B'] ?? ''));
            $pangkat    = trim($row['C'] ?? '');
            $jabatan    = trim($row['D'] ?? '');
            $bidangNama = trim($row['E'] ?? '');

            if (empty($nama) && empty($nip)) continue;

            if (empty($nama) || empty($nip) || empty($pangkat)) {
                $gagal[] = "Baris {$rowIndex}: Nama, NIP, dan Pangkat wajib diisi.";
                continue;
            }

            if (in_array($nip, $nipSudahDiproses)) {
                $gagal[] = "Baris {$rowIndex}: NIP '{$nip}' muncul duplikat dalam file.";
                continue;
            }

            if (Pegawai::where('nip', $nip)->exists()) {
                $gagal[] = "Baris {$rowIndex}: NIP '{$nip}' sudah terdaftar di sistem.";
                continue;
            }

            $bidangId = $user->bidang_id ?? null;
            if (!$bidangId && !empty($bidangNama)) {
                $bidangRecord = Bidang::where('nama_bidang', 'like', "%{$bidangNama}%")
                    ->when($user->dinas_id, fn($q) => $q->where('dinas_id', $user->dinas_id))
                    ->first();
                $bidangId = $bidangRecord?->id;
            }

            $nipSudahDiproses[] = $nip;

            Pegawai::create([
                'nama'     => $nama,
                'nip'      => $nip,
                'pangkat'  => $pangkat,
                'jabatan'  => $jabatan ?: null,
                'dinas_id' => $user->dinas_id ?? null,
                'bidang_id'=> $bidangId,
            ]);

            $berhasil++;
        }

        $pesan = "Import selesai: {$berhasil} pegawai berhasil ditambahkan.";
        if (!empty($gagal)) {
            $pesan .= ' ' . count($gagal) . ' baris dilewati.';
            return redirect()->route('pegawai.index')
                ->with('import_success', $pesan)
                ->with('import_errors', $gagal);
        }

        return redirect()->route('pegawai.index')->with('success', $pesan);
    }

    /**
     * Unduh template Excel untuk import pegawai
     */
    public function downloadTemplate()
    {
        $user        = auth()->user();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Pegawai');

        $headers = ['A' => 'Nama', 'B' => 'NIP', 'C' => 'Pangkat', 'D' => 'Jabatan', 'E' => 'Bidang'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}1", $label);
        }

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $bidangContoh = $user->bidang?->nama_bidang ?? 'BMD';
        $contoh = [
            ['Ahmad Fauzi', '198501012010011001', 'Penata Muda III/a', 'Staf', $bidangContoh],
            ['Siti Rahayu', '199002152015012002', 'Penata III/c', 'Kepala Sub Bagian', $bidangContoh],
        ];
        foreach ($contoh as $i => $baris) {
            $row = $i + 2;
            foreach (['A', 'B', 'C', 'D', 'E'] as $j => $col) {
                $sheet->setCellValue("{$col}{$row}", $baris[$j]);
            }
            $bg = $i % 2 === 0 ? 'EFF6FF' : 'FFFFFF';
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DBEAFE']]],
            ]);
        }

        $noteRow = 5;
        if ($user->bidang_id) {
            $note = "* Kolom Bidang otomatis diisi '{$bidangContoh}' saat import — tidak perlu diubah.";
        } else {
            $note = '* Isi kolom Bidang dengan nama bidang yang terdaftar di sistem (opsional). Kosongkan jika tidak ada.';
        }
        $sheet->mergeCells("A{$noteRow}:E{$noteRow}");
        $sheet->setCellValue("A{$noteRow}", $note);
        $sheet->getStyle("A{$noteRow}")->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer   = new Xlsx($spreadsheet);
        $filename = 'template_import_pegawai.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
