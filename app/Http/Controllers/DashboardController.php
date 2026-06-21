<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Uraian;
use App\Models\Spt;
use App\Models\NotaDinas;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $user = auth()->user();

        // 1. Total Sub Kegiatan
        $subKegiatanQuery = SubKegiatan::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $subKegiatanQuery->where('bidang_id', $user->bidang_id);
        }
        $totalSubKegiatan = $subKegiatanQuery->count();

        // 2. Total PPTK
        $pptkQuery = Pegawai::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $pptkQuery->whereHas('subKegiatans', function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id);
            });
        } else {
            $pptkQuery->whereHas('subKegiatans');
        }
        $totalPptk = $pptkQuery->count();

        // 3. Total Pagu (Total Anggaran Uraian)
        $paguQuery = Uraian::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $paguQuery->whereHas('subKegiatan', function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id);
            });
        }
        $totalPagu = $paguQuery->sum('total_anggaran');

        // 4. Total Realisasi (Anggaran Terpakai Uraian)
        $realisasiQuery = Uraian::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $realisasiQuery->whereHas('subKegiatan', function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id);
            });
        }
        $totalRealisasi = $realisasiQuery->sum('anggaran_terpakai');

        // 5. Sisa Anggaran
        $sisaAnggaran = $totalPagu - $totalRealisasi;

        // 6. Persentase Realisasi Anggaran
        $persenRealisasi = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100) : 0;

        // 7. Total OK Target & Terpakai
        $okTotalQuery = Uraian::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $okTotalQuery->whereHas('subKegiatan', function ($q) use ($user) {
                $q->where('bidang_id', $user->bidang_id);
            });
        }
        $okTotal = $okTotalQuery->sum('ok_total');
        $okTerpakai = $okTotalQuery->sum('ok_terpakai');
        $persenOk = $okTotal > 0 ? round(($okTerpakai / $okTotal) * 100) : 0;

        // 8. Total SPT
        $sptQuery = Spt::query();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $sptQuery->where('bidang_id', $user->bidang_id);
        }
        $totalSpt = $sptQuery->count();

        // 9. Sub Kegiatan Budget Breakdown
        $querySub = SubKegiatan::with(['pegawai', 'uraians']);
        if ($user && !in_array($user->role->name, ['super_admin', 'admin'])) {
            $querySub->where('bidang_id', $user->bidang_id);
        }
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $querySub->where('bidang_id', $user->bidang_id);
        }

        $subKegiatans = $querySub->get()
            ->map(function ($sub) {
                $pagu = $sub->uraians->sum('total_anggaran');
                $realisasi = $sub->uraians->sum('anggaran_terpakai');
                $sisa = $pagu - $realisasi;
                $persen = $pagu > 0 ? round(($realisasi / $pagu) * 100) : 0;

                return [
                    'id' => $sub->id,
                    'nomor_rekening' => $sub->nomor_rekening,
                    'nama_kegiatan' => $sub->nama_kegiatan,
                    'pptk_nama' => $sub->pegawai->nama ?? '-',
                    'pagu' => $pagu,
                    'realisasi' => $realisasi,
                    'sisa' => $sisa,
                    'persen' => $persen,
                ];
            })
            ->sortByDesc('pagu')
            ->take(5);

        // 10. Aktivitas Terbaru (Nota Dinas)
        $recentQuery = NotaDinas::with(['subKegiatan', 'kepada', 'spt']);
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            $recentQuery->where('bidang_id', $user->bidang_id);
        }
        $recentActivities = $recentQuery->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('pages.dashboard.index', compact(
            'totalSubKegiatan',
            'totalPptk',
            'totalPagu',
            'totalRealisasi',
            'sisaAnggaran',
            'persenRealisasi',
            'okTotal',
            'okTerpakai',
            'persenOk',
            'totalSpt',
            'subKegiatans',
            'recentActivities'
        ));
    }

    /**
     * Halaman Rekap Perjalanan Pegawai (dipindah dari Dashboard ke Monev)
     */
    public function rekapPegawaiPage(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $rekapPegawai = Pegawai::withCount([
            'notaDinas as jan' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 1),
            'notaDinas as feb' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 2),
            'notaDinas as mar' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 3),
            'notaDinas as apr' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 4),
            'notaDinas as mei' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 5),
            'notaDinas as jun' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 6),
            'notaDinas as jul' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 7),
            'notaDinas as ags' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 8),
            'notaDinas as sep' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 9),
            'notaDinas as okt' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 10),
            'notaDinas as nov' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 11),
            'notaDinas as des' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 12),
            'notaDinas as total' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun),
        ])
            ->orderBy('total', 'desc')
            ->get();

        return view('pages.monev.rekap-pegawai', compact('rekapPegawai', 'tahun'));
    }

    /**
     * AJAX endpoint: rekap perjalanan pegawai per tahun (breakdown bulanan)
     * GET /dashboard/rekap-pegawai-tahunan?tahun=2026
     */
    public function rekapByTahun(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);

        $pegawais = Pegawai::withCount([
            'notaDinas as jan' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 1),
            'notaDinas as feb' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 2),
            'notaDinas as mar' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 3),
            'notaDinas as apr' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 4),
            'notaDinas as mei' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 5),
            'notaDinas as jun' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 6),
            'notaDinas as jul' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 7),
            'notaDinas as ags' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 8),
            'notaDinas as sep' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 9),
            'notaDinas as okt' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 10),
            'notaDinas as nov' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 11),
            'notaDinas as des' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun)->whereMonth('nota_dinas.tanggal_mulai', 12),
            'notaDinas as total' => fn($q) => $q->whereYear('nota_dinas.tanggal_mulai', $tahun),
        ])
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($p) => [
                'nama'  => $p->nama,
                'nip'   => $p->nip ?? '-',
                'jan'   => $p->jan,
                'feb'   => $p->feb,
                'mar'   => $p->mar,
                'apr'   => $p->apr,
                'mei'   => $p->mei,
                'jun'   => $p->jun,
                'jul'   => $p->jul,
                'ags'   => $p->ags,
                'sep'   => $p->sep,
                'okt'   => $p->okt,
                'nov'   => $p->nov,
                'des'   => $p->des,
                'total' => $p->total,
            ]);

        return response()->json([
            'tahun'    => $tahun,
            'pegawais' => $pegawais,
        ]);
    }

    /**
     * AJAX endpoint: rekap perjalanan pegawai per bulan & tahun
     * GET /dashboard/rekap-pegawai?bulan=5&tahun=2026
     */
    public function rekapByBulan(Request $request)
    {
        $bulanAwal = (int) $request->get('bulan_awal', $request->get('bulan', now()->month));
        $bulanAkhir = (int) $request->get('bulan_akhir', $request->get('bulan', now()->month));
        $tahun = (int) $request->get('tahun', now()->year);

        if ($bulanAwal > $bulanAkhir) {
            $temp = $bulanAwal;
            $bulanAwal = $bulanAkhir;
            $bulanAkhir = $temp;
        }

        $tanggalAwal = Carbon::createFromDate($tahun, $bulanAwal, 1)->startOfMonth();
        $tanggalAkhir = Carbon::createFromDate($tahun, $bulanAkhir, 1)->endOfMonth();

        $awalBulan = $tanggalAwal->toDateString();
        $akhirBulan = $tanggalAkhir->toDateString();

        if ($bulanAwal === $bulanAkhir) {
            $namaBulan = $tanggalAwal->translatedFormat('F Y');
        } else {
            $namaBulan = $tanggalAwal->translatedFormat('F') . ' - ' . $tanggalAkhir->translatedFormat('F Y');
        }

        $pegawais = Pegawai::withCount([
            'notaDinas' => function ($query) use ($awalBulan, $akhirBulan) {
                $query->whereBetween('nota_dinas.tanggal_mulai', [$awalBulan, $akhirBulan]);
            }
        ])
            ->orderBy('nota_dinas_count', 'desc')
            ->take(10)
            ->get()
            ->map(fn($p) => [
                'nama'             => $p->nama,
                'nip'              => $p->nip ?? '-',
                'jabatan'          => $p->jabatan ?? '-',
                'nota_dinas_count' => $p->nota_dinas_count,
            ]);

        return response()->json([
            'namaBulan' => $namaBulan,
            'pegawais'  => $pegawais,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bulanAwal = (int) $request->get('bulan_awal', now()->month);
        $bulanAkhir = (int) $request->get('bulan_akhir', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        if ($bulanAwal > $bulanAkhir) {
            $temp = $bulanAwal;
            $bulanAwal = $bulanAkhir;
            $bulanAkhir = $temp;
        }

        $tanggalAwal = Carbon::createFromDate($tahun, $bulanAwal, 1)->startOfMonth();
        $tanggalAkhir = Carbon::createFromDate($tahun, $bulanAkhir, 1)->endOfMonth();

        $awalBulan = $tanggalAwal->toDateString();
        $akhirBulan = $tanggalAkhir->toDateString();

        $notaDinasList = \App\Models\NotaDinas::with('pegawais')
            ->whereBetween('tanggal_mulai', [$awalBulan, $akhirBulan])
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Perjalanan Dinas');

        // Headers
        $headers = ['Tanggal Berangkat', 'Nama Pegawai', 'NIP', 'Keperluan', 'Tujuan'];
        foreach ($headers as $colIndex => $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter . '1', $header);
            
            $sheet->getStyle($colLetter . '1')->getFont()->setBold(true);
            $sheet->getStyle($colLetter . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFE2EFDA'); // Subtle light green background
        }

        $rowNum = 2;
        foreach ($notaDinasList as $nota) {
            foreach ($nota->pegawais as $pegawai) {
                $sheet->setCellValue('A' . $rowNum, Carbon::parse($nota->tanggal_mulai)->format('d-m-Y'));
                $sheet->setCellValue('B' . $rowNum, $pegawai->nama);
                $sheet->setCellValue('C' . $rowNum, $pegawai->nip ?? '-');
                $sheet->setCellValue('D' . $rowNum, $nota->kegiatan ?? $nota->perihal ?? '-');
                $sheet->setCellValue('E' . $rowNum, $nota->lokasi ?? '-');
                $rowNum++;
            }
        }

        foreach (range(1, 5) as $colIndex) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        if ($rowNum > 2) {
            $sheet->getStyle('A1:E' . ($rowNum - 1))->applyFromArray($styleArray);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        if ($bulanAwal === $bulanAkhir) {
            $namaBulanFile = $tanggalAwal->translatedFormat('F_Y');
        } else {
            $namaBulanFile = $tanggalAwal->translatedFormat('F') . '_s.d_' . $tanggalAkhir->translatedFormat('F_Y');
        }
        $filename = 'Rekap_Perjalanan_Pegawai_' . $namaBulanFile . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
