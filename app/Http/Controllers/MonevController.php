<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class MonevController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pptkRekap($id)
    {
        // 1. Ambil data asli PPTK dan Sub Kegiatannya
        $pptk = Pegawai::with('subKegiatans')->findOrFail($id);

        // 2. Tambahkan data Uraian Dummy ke setiap Sub Kegiatan
        $pptk->subKegiatans->map(function ($sub) use ($id) {
            $sub->uraians = collect([
                (object)[
                    'nama_uraian' => 'Uraian Dummy 1 untuk ' . $sub->nama_kegiatan,
                    'koefisien' => 100,
                    'koef_digunakan' => 45,
                    'anggaran' => 5000000,
                    'anggaran_digunakan' => 2250000,
                ],
                (object)[
                    'nama_uraian' => 'Uraian Dummy 2 untuk ' . $sub->nama_kegiatan,
                    'koefisien' => 50,
                    'koef_digunakan' => 10,
                    'anggaran' => 2000000,
                    'anggaran_digunakan' => 400000,
                ]
            ]);
            return $sub;
        });

        return view('pages.monev.pptk-rekap', compact('pptk'));
    }

    // Menampilkan Detail satu Sub Kegiatan
    public function subKegiatanShow($id)
    {
        $sub = SubKegiatan::with('pegawai')->findOrFail($id);

        return view('pages.monev.sub-kegiatan-detail', compact('sub'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
