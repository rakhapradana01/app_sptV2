<?php

namespace App\Http\Controllers;

use App\Models\SubKegiatan;
use Illuminate\Http\Request;

class SubKegiatanController extends Controller
{
    public function index()
    {
        $query = SubKegiatan::query();
        $user  = auth()->user();
        $role  = $user?->role?->name;

        // Filter berdasarkan hierarki role
        if ($role === 'kepala_sub_bidang') {
            // Kasubid hanya melihat sub kegiatan di sub bidangnya sendiri
            $query->where('sub_bidang_id', $user->sub_bidang_id);
        } elseif (in_array($role, ['admin', 'kepala_bidang'])) {
            // Admin & Kabid melihat semua sub kegiatan di bidangnya
            if ($user->bidang_id) {
                $query->where('bidang_id', $user->bidang_id);
            }
        } elseif ($role === 'kepala_badan') {
            // Kaban melihat semua sub kegiatan di dinasnya
            $query->where('dinas_id', $user->dinas_id);
        }
        // super_admin: tidak ada filter, lihat semua

        $subKegiatan = $query->paginate(10);

        return view('pages.master.sub_kegiatan.index', compact('subKegiatan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nama_kegiatan'  => 'required|string|max:255',
            'nomor_rekening' => 'required|string',
            'harga_satuan'   => 'nullable|integer|min:0',
            'koefisien'      => 'nullable|integer|min:0',
            'pagu'           => 'nullable|integer|min:0',
        ]);

        $validated['harga_satuan'] = $validated['harga_satuan'] ?? 0;
        $validated['koefisien']    = $validated['koefisien'] ?? 0;
        $validated['pagu']         = $validated['pagu'] ?? 0;

        // Auto-fill: kasubid yang login adalah pemilik sub kegiatan
        $validated['user_id']       = $user->id;
        $validated['dinas_id']      = $user->dinas_id;
        $validated['bidang_id']     = $user->bidang_id;
        $validated['sub_bidang_id'] = $user->sub_bidang_id;

        SubKegiatan::create($validated);

        return redirect()->back()->with('success', 'Sub Kegiatan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_kasubid_id' => 'nullable|exists:pegawais,id',
            'nama_kegiatan'      => 'required|string',
            'nomor_rekening'     => 'required|string',
            'harga_satuan'       => 'nullable|integer|min:0',
            'koefisien'          => 'nullable|integer|min:0',
            'pagu'               => 'nullable|integer|min:0',
        ]);

        $sub = SubKegiatan::findOrFail($id);

        $sub->update([
            'nama_kegiatan'      => $request->nama_kegiatan,
            'nomor_rekening'     => $request->nomor_rekening,
            'harga_satuan'       => $request->harga_satuan ?? 0,
            'koefisien'          => $request->koefisien ?? 0,
            'pagu'               => $request->pagu ?? 0,
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