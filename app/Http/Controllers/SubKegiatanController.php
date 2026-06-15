<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class SubKegiatanController extends Controller
{
    public function index()
    {
        $subKegiatan = SubKegiatan::paginate(10);
        // dd($subKegiatan->all());
        $pegawais = Pegawai::paginate(10);
        return view('pages.master.sub_kegiatan.index', compact('subKegiatan', 'pegawais'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_kasubid_id' => 'required|exists:pegawais,id',
            'nama_kegiatan' => 'required|string',
            'nomor_rekening' => 'required|string'
        ]);

        $sub = SubKegiatan::findOrFail($id);

        $sub->update([
            'pegawai_kasubid_id' => $request->pegawai_kasubid_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'nomor_rekening' => $request->nomor_rekening,
        ]);

        return response()->json([
            'success' => 'Sub Kegiatan Berhasil Dirubah!'
        ]);
    }

    public function show($id)
    {
        return response()->json(
            SubKegiatan::findOrFail($id)
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'pegawai_kasubid_id' => 'required|exists:pegawais,id',
            'nama_kegiatan' => 'required|string',
            'nomor_rekening' => 'required|string',
        ]);

        SubKegiatan::create([
            'pegawai_kasubid_id' => $request->pegawai_kasubid_id,
            'nama_kegiatan' => $request->nama_kegiatan,
            'nomor_rekening' => $request->nomor_rekening,
            'harga_satuan' => 0,
            'koefisien' => 0,
            'pagu' => 0,
        ]);

        return redirect()->route('sub-kegiatan.index')->with('success', 'Sub Kegiatan berhasil ditambahkan.');
    }
}
