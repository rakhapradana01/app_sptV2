<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Uraian;
use Illuminate\Http\Request;

class MonevController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pptkRekap($id)
    {

        $pptk = Pegawai::with('subKegiatans')->findOrFail($id);
        return view('pages.monev.pptk-rekap', compact('pptk'));
    }


   public function getBySubActivityId($id){
        $result  = Uraian::with(['spjRincians.pegawai', 'spjRincians.notaDinas.spt'])->where('sub_kegiatan_id', $id)->get();
        return response()->json($result);
   }

    // Menampilkan Detail satu Sub Kegiatan
    public function subKegiatanShow($id)
    {
        $sub = SubKegiatan::with('pegawai')->findOrFail($id);

        return view('pages.monev.sub-kegiatan-detail', compact('sub'));
    }

    public function storeUraian(Request $request)
    {
        $validated = $request->validate([
            'sub_kegiatan_id' => 'required|exists:sub_kegiatans,id',
            'uraian' => 'required|string',
            'ok_total' => 'required|numeric',
            'ok_terpakai' => 'numeric',
            'harga_satuan' => 'required|numeric',
            'total_anggaran' => 'required|numeric',
            'anggaran_terpakai' => 'numeric',
        ]);

        Uraian::create($validated);

        return redirect()->back()->with('success', 'Uraian berhasil ditambahkan');
    }

    public function updateUraian(Request $request, $id)
    {
        $validated = $request->validate([
            'sub_kegiatan_id' => 'required|exists:sub_kegiatans,id',
            'uraian' => 'required|string',
            'ok_total' => 'required|numeric',
            'ok_terpakai' => 'numeric',
            'harga_satuan' => 'required|numeric',
            'total_anggaran' => 'required|numeric',
            'anggaran_terpakai' => 'numeric',
        ]);

        $uraian = Uraian::findOrFail($id);
        $uraian->update($validated);

        return redirect()->back()->with('success', 'Uraian berhasil diperbarui');
    }

    public function destroyUraian($id)
    {
        $uraian = Uraian::findOrFail($id);
        $uraian->delete();

        return redirect()->back()->with('success', 'Uraian berhasil dihapus');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
