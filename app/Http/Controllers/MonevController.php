<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SubKegiatan;
use App\Models\Uraian;
use Illuminate\Http\Request;

class MonevController extends Controller
{
    /**
     * Helper untuk validasi akses bidang (Don't Repeat Yourself)
     */
    private function checkBidangAuthorization($modelBidangId)
    {
        $user = auth()->user();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            if (!$user->bidang_id || $modelBidangId != $user->bidang_id) {
                abort(403, 'Unauthorized action.');
            }
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function pptkRekap($id)
    {
        $user = auth()->user();

        $pptk = Pegawai::findOrFail($id);
        $pptk->load([
            'subKegiatans' => function ($query) use ($user) {
                if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
                    $query->where('bidang_id', $user->bidang_id);
                }
            }
        ]);

        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            if ($pptk->subKegiatans->isEmpty()) {
                abort(403, 'Unauthorized action. Anda tidak memiliki hak akses pada data di bidang ini.');
            }
        }

        return view('pages.monev.pptk-rekap', compact('pptk'));
    }

    public function getBySubActivityId($id)
    {
        
        $sub = SubKegiatan::findOrFail($id);

        // Gunakan try-catch atau sesuaikan response jika ingin mengembalikan JSON kosong saat unauthorized
        $user = auth()->user();
        if ($user && in_array($user->role->name, ['kepala_bidang', 'kepala_sub_bidang'])) {
            if (!$user->bidang_id || $sub->bidang_id != $user->bidang_id) {
                return response()->json([]);
            }
        }

        $result = Uraian::with(['spjRincians.pegawai', 'spjRincians.notaDinas.spt'])
            ->where('sub_kegiatan_id', $id)
            ->get();

        return response()->json($result);
    }

    // Menampilkan Detail satu Sub Kegiatan
    public function subKegiatanShow($id)
    {
        $sub = SubKegiatan::with('pegawai')->findOrFail($id);

        // Validasi bidang untuk sub kegiatan
        $this->checkBidangAuthorization($sub->bidang_id);

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

        // PROTEKSI: Cek apakah sub kegiatan target sesuai dengan bidang user
        $sub = SubKegiatan::findOrFail($request->sub_kegiatan_id);
        $this->checkBidangAuthorization($sub->bidang_id);

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

        // PROTEKSI: Cek bidang dari Uraian yang lama dan Sub Kegiatan yang baru
        $this->checkBidangAuthorization($uraian->subKegiatan->bidang_id ?? null);
        $subTarget = SubKegiatan::findOrFail($request->sub_kegiatan_id);
        $this->checkBidangAuthorization($subTarget->bidang_id);

        $uraian->update($validated);

        return redirect()->back()->with('success', 'Uraian berhasil diperbarui');
    }

    public function destroyUraian($id)
    {
        $uraian = Uraian::findOrFail($id);

        // PROTEKSI: Cek bidang sebelum menghapus
        $this->checkBidangAuthorization($uraian->subKegiatan->bidang_id ?? null);

        $uraian->delete();

        return redirect()->back()->with('success', 'Uraian berhasil dihapus');
    }
}