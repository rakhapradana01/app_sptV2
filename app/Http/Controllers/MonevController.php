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
    private function checkBidangAuthorization($modelBidangId, $modelSubBidangId = null)
    {
        $user = auth()->user();
        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id || $modelSubBidangId != $user->sub_bidang_id) {
                    abort(403, 'Unauthorized action.');
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id && $modelBidangId != $user->bidang_id) {
                    abort(403, 'Unauthorized action.');
                }
            }
        }
    }

    /**
     * Halaman Utama Monitoring Uraian & OK Tujuan Perjalanan Dinas
     */
    public function uraianIndex(Request $request)
    {
        $user = auth()->user();
        $dinasId = $user?->dinas_id;

        $subKegiatanQuery = SubKegiatan::with(['owner', 'pegawai', 'uraians'])
            ->where('dinas_id', $dinasId);

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                $subKegiatanQuery->where('sub_bidang_id', $user->sub_bidang_id);
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id) {
                    $subKegiatanQuery->where('bidang_id', $user->bidang_id);
                }
            }
        }

        $allSubKegiatans = $subKegiatanQuery->get();

        $selectedSubId = $request->query('sub_kegiatan_id');
        if (!$selectedSubId && $request->query('pptk_id')) {
            $pptkSub = $allSubKegiatans->where('pegawai_kasubid_id', $request->query('pptk_id'))->first();
            if ($pptkSub) {
                $selectedSubId = $pptkSub->id;
            }
        }

        if (!$selectedSubId && $allSubKegiatans->isNotEmpty()) {
            $selectedSubId = $allSubKegiatans->first()->id;
        }

        $selectedSubKegiatan = $allSubKegiatans->firstWhere('id', $selectedSubId);

        return view('pages.monev.uraian', compact('allSubKegiatans', 'selectedSubKegiatan', 'selectedSubId'));
    }

    public function pptkRekap($id)
    {
        return redirect()->route('monev.uraian.index', ['pptk_id' => $id]);
    }

    public function subKegiatanShow($id)
    {
        return redirect()->route('monev.uraian.index', ['sub_kegiatan_id' => $id]);
    }

    public function getBySubActivityId($id)
    {
        $sub = SubKegiatan::with(['owner', 'pegawai'])->findOrFail($id);

        $user = auth()->user();
        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id || $sub->sub_bidang_id != $user->sub_bidang_id) {
                    return response()->json(['sub_kegiatan' => null, 'uraians' => []]);
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id && $sub->bidang_id != $user->bidang_id) {
                    return response()->json(['sub_kegiatan' => null, 'uraians' => []]);
                }
            }
        }

        $uraians = Uraian::with(['spjRincians.pegawai', 'spjRincians.notaDinas.spt'])
            ->where('sub_kegiatan_id', $id)
            ->get();

        return response()->json([
            'sub_kegiatan' => [
                'id' => $sub->id,
                'nomor_rekening' => $sub->nomor_rekening,
                'nama_kegiatan' => $sub->nama_kegiatan,
                'pagu' => (float)($sub->pagu ?? $uraians->sum('total_anggaran')),
                'realisasi' => (float)($sub->realisasi ?? $uraians->sum('anggaran_terpakai')),
                'pptk_nama' => $sub->pegawai?->nama ?? $sub->owner?->name ?? '-',
                'pptk_jabatan' => $sub->pegawai?->jabatan ?? 'Penanggung Jawab Sub Kegiatan',
            ],
            'uraians' => $uraians
        ]);
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
        $this->checkBidangAuthorization($sub->bidang_id, $sub->sub_bidang_id);

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
        $this->checkBidangAuthorization($uraian->subKegiatan->bidang_id ?? null, $uraian->subKegiatan->sub_bidang_id ?? null);
        $subTarget = SubKegiatan::findOrFail($request->sub_kegiatan_id);
        $this->checkBidangAuthorization($subTarget->bidang_id, $subTarget->sub_bidang_id);

        $uraian->update($validated);

        return redirect()->back()->with('success', 'Uraian berhasil diperbarui');
    }

    public function destroyUraian($id)
    {
        $uraian = Uraian::findOrFail($id);

        // PROTEKSI: Cek bidang sebelum menghapus
        $this->checkBidangAuthorization($uraian->subKegiatan->bidang_id ?? null, $uraian->subKegiatan->sub_bidang_id ?? null);

        $uraian->delete();

        return redirect()->back()->with('success', 'Uraian berhasil dihapus');
    }
}