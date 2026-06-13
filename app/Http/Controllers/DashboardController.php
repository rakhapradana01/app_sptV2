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
        $awalBulan = $now->copy()->startOfMonth()->toDateString();
        $akhirBulan = $now->copy()->endOfMonth()->toDateString();
        $namaBulan = $now->translatedFormat('F Y');

        $rekapPegawai = Pegawai::withCount([
            // Hitung nota dinas di bulan berjalan berdasarkan tanggal_mulai
            'notaDinas' => function ($query) use ($awalBulan, $akhirBulan) {
                $query->whereBetween('nota_dinas.tanggal_mulai', [$awalBulan, $akhirBulan]);
            }
        ])
            ->orderBy('nota_dinas_count', 'desc')
            ->take(10)
            ->get();

        // 1. Total Sub Kegiatan
        $totalSubKegiatan = SubKegiatan::count();

        // 2. Total PPTK
        $totalPptk = Pegawai::whereHas('subKegiatans')->count();

        // 3. Total Pagu (Total Anggaran Uraian)
        $totalPagu = Uraian::sum('total_anggaran');

        // 4. Total Realisasi (Anggaran Terpakai Uraian)
        $totalRealisasi = Uraian::sum('anggaran_terpakai');

        // 5. Sisa Anggaran
        $sisaAnggaran = $totalPagu - $totalRealisasi;

        // 6. Persentase Realisasi Anggaran
        $persenRealisasi = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100) : 0;

        // 7. Total OK Target & Terpakai
        $okTotal = Uraian::sum('ok_total');
        $okTerpakai = Uraian::sum('ok_terpakai');
        $persenOk = $okTotal > 0 ? round(($okTerpakai / $okTotal) * 100) : 0;

        // 8. Total SPT
        $totalSpt = Spt::count();

        // 9. Sub Kegiatan Budget Breakdown
        $subKegiatans = SubKegiatan::with(['pegawai', 'uraians'])
            ->get()
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
        $recentActivities = NotaDinas::with(['subKegiatan', 'kepada', 'spt'])
            ->orderBy('created_at', 'desc')
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
            'recentActivities',
            'rekapPegawai', // <-- Variabel baru
            'namaBulan'
        ));
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

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
