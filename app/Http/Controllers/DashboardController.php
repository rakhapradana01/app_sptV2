<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Uraian;
use App\Models\Spt;
use App\Models\NotaDinas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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
            'recentActivities'
        ));
    }
}
