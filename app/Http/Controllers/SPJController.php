<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SPJController extends Controller
{
    public function index()
    {
        // Data dummy untuk table SPJ
        $spjs = [
            [
                'id' => 1,
                'nomor_spt' => '001/SPT/2026',
                'nama_pegawai' => 'Rakha Pradana',
                'tujuan' => 'Jakarta',
                'tanggal' => '2026-05-12',
            ],
            [
                'id' => 2,
                'nomor_spt' => '002/SPT/2026',
                'nama_pegawai' => 'Andi Wijaya',
                'tujuan' => 'Surabaya',
                'tanggal' => '2026-05-15',
            ],
        ];

        return view('pages.spj.index', compact('spjs'));
    }

    public function show($id)
    {
        // Data dummy untuk detail SPJ
        $spj = [
            'id' => $id,
            'nomor_spt' => '001/SPT/2026',
            'nama_pegawai' => 'Rakha Pradana',
            'tujuan' => 'Jakarta',
            'tanggal' => '2026-05-12',
        ];

        return view('pages.spj.show', compact('spj'));
    }
}
