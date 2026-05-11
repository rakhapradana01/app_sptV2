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
        // Ambil data pegawai beserta sub kegiatannya
        $pptk = Pegawai::with('subKegiatans')->findOrFail($id);

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
