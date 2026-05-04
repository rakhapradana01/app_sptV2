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
            'tempat_tujuan_2' => 'string'
        ]);
        $nota = NotaDinas::findOrfail($notaId);

        Sppd::create([
            'nota_dinas_id'    => $nota->id,
            'nomor_sppd'       => $request->nomor_sppd,
            'alat_angkutan'    => $request->alat_angkutan,
            'tempat_berangkat' => $request->tempat_berangkat,
            'tempat_tujuan'    => $request->tempat_tujuan,
            'tanggal_sppd'     => $request->tanggal_sppd,
            'tempat_tujuan_2'  => $request->tempat_tujuan_2
        ]);
        return redirect()->back()->with('success', 'Data SPPD berhasil dibuat.');
    }
    public function cetakSPPD($id)
    {
        $nota = NotaDinas::with(['spt', 'pegawais', 'sppd'])->findOrFail($id);

        if (!$nota->sppd) {
            return back()->with('error', 'Data SPPD belum diinput.');
        }


        $start = \Carbon\Carbon::parse($nota->sppd->tanggal_sppd)->startOfDay();
        $end = \Carbon\Carbon::parse($nota->sppd->tanggal_kembali)->startOfDay();

        $lamaHari = (int) $start->diffInDays($end) + 1;

        $pdf = Pdf::loadView('pages.sppd.pdf', compact('nota', 'lamaHari'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SPPD-' . str_replace('/', '-', $nota->sppd->nomor_sppd) . '.pdf';

        return $pdf->stream($fileName);
    }
}
