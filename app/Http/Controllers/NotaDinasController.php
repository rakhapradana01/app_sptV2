<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class NotaDinasController extends Controller
{
    public function index()
    {
        $notaDinas = NotaDinas::with([
            'subKegiatan',
            'kepada',
            'melalui'
        ])->latest()->paginate(10);

        $subKegiatans = SubKegiatan::all();
        $pegawais = Pegawai::all();

        return view('pages.nota_dinas.index', compact(
            'notaDinas',
            'subKegiatans',
            'pegawais'
        ));
    }

    public function create()
    {
        $subKegiatans = SubKegiatan::all();
        $pegawais = Pegawai::all();

        return view('pages.nota_dinas.create', compact(
            'subKegiatans',
            'pegawais'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_kegiatan_id' => 'required|exists:sub_kegiatans,id',
            'tanggal' => 'required|date',
            'kepada_id' => 'required|exists:pegawais,id',
            'dari_id' => 'required|exists:pegawais,id',
            'melalui_id' => 'nullable|exists:pegawais,id',
            'perihal' => 'required|string',
            'lokasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
        ]);

        $nota = NotaDinas::create([
            'sub_kegiatan_id' => $validated['sub_kegiatan_id'],
            'tanggal' => $validated['tanggal'],
        'kepada_id' => $validated['kepada_id'],
            'dari_id' => $validated['dari_id'],
            'melalui_id' => $validated['melalui_id'] ?? null,
            'perihal' => $validated['perihal'],
            'lokasi' => $validated['lokasi'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status' => 'draft',
        ]);

        if ($request->pegawai_ids) {
            $nota->pegawais()->attach($request->pegawai_ids);
        }

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Nota dinas berhasil disimpan.');
    }
}
