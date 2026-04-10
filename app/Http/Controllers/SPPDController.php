<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Sppd;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SPPDController extends Controller
{
    public function store(Request $request, $notaId)
    {
        $request->validate([
            'nomor_sppd' => 'required|string',
            'alat_angkutan' => 'required|string',
            'tempat_berangkat' => 'required|string',
            'tempat_tujuan' => 'required|string',
            'tanggal_sppd' => 'required|date',
        ]);
        $nota = NotaDinas::findOrfail($notaId);

        Sppd::create([
            'nota_dinas_id'    => $nota->id,
            'nomor_sppd'       => $request->nomor_sppd,
            'alat_angkutan'    => $request->alat_angkutan,
            'tempat_berangkat' => $request->tempat_berangkat,
            'tempat_tujuan'    => $request->tempat_tujuan,
            'tanggal_sppd'     => $request->tanggal_sppd,
        ]);
        return redirect()->back()->with('success', 'Data SPPD berhasil dibuat.');
    }
    public function cetakSPPD($id)
    {
        $nota = NotaDinas::with(['spt', 'pegawais', 'sppd'])->findOrFail($id);

        if (!$nota->sppd) {
            return back()->with('error', 'Data SPPD belum diinput.');
        }

        // PAKSA ke startOfDay agar jam tidak ikut dihitung desimal
        $start = \Carbon\Carbon::parse($nota->sppd->tanggal_sppd)->startOfDay();
        $end = \Carbon\Carbon::parse($nota->sppd->tanggal_kembali)->startOfDay();

        // Gunakan (int) untuk memastikan angka bulat
        // diffInDays akan menghasilkan 2 jika tgl 10 ke 12, lalu + 1 jadi 3.
        $lamaHari = (int) $start->diffInDays($end) + 1;

        $pdf = Pdf::loadView('pages.sppd.pdf', compact('nota', 'lamaHari'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SPPD-' . str_replace('/', '-', $nota->sppd->nomor_sppd) . '.pdf';

        return $pdf->stream($fileName);
    }
}
