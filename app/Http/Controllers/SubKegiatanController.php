<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class SubKegiatanController extends Controller
{
    public function index()
    {
        
        $query = SubKegiatan::query();
        $user = auth()->user();

        if ($user) {
            // Jika dia Admin, hanya lihat sub kegiatan di bidangnya
            if ($user->role->name === 'admin') {
                $query->where('bidang_id', $user->bidang_id);
            }
            // Jika dia Super Admin, skip filter (bisa melihat semua data)
        }

        $subKegiatan = $query->paginate(10);
        $pegawais = Pegawai::all(); // Mengambil semua pegawai tanpa pagination untuk kebutuhan dropdown select

        return view('pages.master.sub_kegiatan.index', compact('subKegiatan', 'pegawais'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Validasi disamakan dengan properti 'name' yang ada pada form HTML Blade Anda
        $validated = $request->validate([
            'pegawai_kasubid_id' => 'required|exists:pegawais,id',
            'nama_kegiatan' => 'required|string|max:255',
            'nomor_rekening' => 'required|string',
        ]);

        // 2. Proteksi & Auto-Fill data relasi dinas/bidang dari user yang sedang login
        if ($user && in_array($user->role->name, ['admin', 'super_admin'])) {

            $validated['dinas_id'] = $user->dinas_id;
            $validated['bidang_id'] = $user->bidang_id;
            $validated['sub_bidang_id'] = $user->sub_bidang_id;

            // Simpan ke database
            SubKegiatan::create($validated);

            return redirect()->back()->with('success', 'Sub Kegiatan berhasil ditambahkan!');
        }

        abort(403, 'Anda tidak memiliki akses untuk menambah Sub Kegiatan.');
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
            'success' => 'Sub Kegiatan Berhasil Diubah!'
        ]);
    }

    public function show($id)
    {
        return response()->json(SubKegiatan::findOrFail($id));
    }
}