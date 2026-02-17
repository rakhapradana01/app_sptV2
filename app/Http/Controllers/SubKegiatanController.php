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
        return view('pages.master.sub_kegiatan.index', compact('subKegiatan','pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_kasubid_id' => 'required|exists:pegawais,id',
            'nomor_rekening' => 'required|string',
            'nama_kegiatan' => 'required|string',
            'pagu' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'koefisien' => 'required|numeric',
        ]);

        SubKegiatan::create([
            'pegawai_kasubid_id' => $request->pegawai_kasubid_id,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_kegiatan' => $request->nama_kegiatan,
            'harga_satuan' => $request->harga_satuan,
            'koefisien' => $request->koefisien ?? 1,
            'pagu' => $request->pagu,
        ]);

        return redirect()->route('sub-kegiatan.index')->with('success', 'Sub Kegiatan berhasil ditambahkan.');
    }
}
