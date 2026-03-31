<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Spt;
use Illuminate\Http\Request;

class SPTController extends Controller
{
    public function cetakSpt($id)
    {
        $nota = NotaDinas::with(['spt', 'pegawais'])->findOrFail($id);
        if (!$nota->spt) {
            return back()->with('error', 'Data SPT belum dibuat untuk Nota Dinas ini.');
        }
        $pdf = Pdf::loadView('pages.spt.pdf', compact('nota'))
            ->setPaper('a4', 'portrait');
        $fileName = 'SPT-' . str_replace('/', '-', $nota->spt->nomor_spt) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function store(Request $request, $nota_id)
    {
        $request->validate([
            'nomor_spt' => 'required|unique:spts,nomor_spt',
            'jenis_anggaran' => 'required|in:DPA,DPPA',
        ]);

        Spt::create([
            'nota_dinas_id' => $nota_id,
            'nomor_spt' => $request->nomor_spt,
            'jenis_anggaran' => $request->jenis_anggaran,
        ]);

        return view('pages.nota_dinas.index')
            ->with('success', 'Data SPT berhasil dibuat.');
    }
}
