<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Spt;
use App\Models\SubKegiatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SPTController extends Controller
{
    // =====================
    // ALUR EXISTING: via Nota Dinas
    // =====================

    public function cetakSpt($id)
    {
        $nota = NotaDinas::with(['spt', 'pegawais'])->findOrFail($id);
        if (!$nota->spt) {
            return back()->with('error', 'Data SPT belum dibuat untuk Nota Dinas ini.');
        }
        $pdf = Pdf::loadView('pages.spt.pdf', compact('nota'))
            ->setPaper('a4', 'portrait');
        $fileName = 'SPT-' . str_replace('/', '-', $nota->spt->nomor_spt) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function store(Request $request, $nota_id)
    {
        $request->validate([
            'nomor_spt'     => 'required',
            'jenis_anggaran' => 'required|in:DPA,DPPA',
        ]);

        $user = auth()->user();
        Spt::updateOrCreate(
            ['nota_dinas_id' => $nota_id],
            [
                'nomor_spt'      => $request->nomor_spt,
                'jenis_anggaran' => $request->jenis_anggaran,
                'tahun_anggaran' => $request->tahun_anggaran ?? date('Y'),
                'dinas_id'       => $user->dinas_id ?? null,
                'bidang_id'      => $user->bidang_id ?? null,
                'sub_bidang_id'  => $user->sub_bidang_id ?? null,
            ]
        );

        return redirect()->route('arsip')->with('success', 'SPT Berhasil Dibuat!');
    }

    public function updateNomor(Request $request, $id)
    {
        $request->validate([
            'nomor_spt' => 'required|string',
        ]);

        $spt = Spt::findOrFail($id);
        $spt->update([
            'nomor_spt' => $request->nomor_spt,
        ]);

        return redirect()->back()->with('success', 'Nomor SPT Berhasil Diperbarui!');
    }

    // =====================
    // JALUR MANDIRI (Standalone)
    // =====================

    public function index()
    {
        $user = auth()->user();
        $query = Spt::with(['pegawais', 'subKegiatan'])
            ->whereNull('nota_dinas_id');

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('sub_bidang_id', $user->sub_bidang_id);
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id) {
                    $query->where('bidang_id', $user->bidang_id);
                }
            }
        }

        $spts = $query->latest()
            ->paginate(10);

        return view('pages.spt.index', compact('spts'));
    }

    public function create()
    {
        $querySub = SubKegiatan::query();
        $user = auth()->user();
        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id) {
                    $querySub->whereRaw('1 = 0');
                } else {
                    $querySub->where('sub_bidang_id', $user->sub_bidang_id);
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id) {
                    $querySub->where('bidang_id', $user->bidang_id);
                }
            }
        }
        $subKegiatans = $querySub->get();
        $queryPeg = Pegawai::orderBy('nama');
        if ($user && $user->bidang_id) {
            $queryPeg->where('bidang_id', $user->bidang_id);
        }
        $pegawais = $queryPeg->get();

        return view('pages.spt.create', compact('subKegiatans', 'pegawais'));
    }

    public function storeMandiri(Request $request)
    {
        $validated = $request->validate([
            'nomor_spt'       => 'required|string',
            'jenis_anggaran'  => 'required|in:DPA,DPPA',
            'tahun_anggaran'  => 'required|digits:4',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi'          => 'required|string',
            'kegiatan'        => 'required|string',
            'sub_kegiatan_id' => 'nullable|exists:sub_kegiatans,id',
            'pegawai_ids'     => 'required|array|min:1',
            'pegawai_ids.*'   => 'exists:pegawais,id',
        ]);

        $user = auth()->user();
        $spt = Spt::create([
            'nota_dinas_id'  => null,
            'nomor_spt'      => $validated['nomor_spt'],
            'jenis_anggaran' => $validated['jenis_anggaran'],
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'tanggal_mulai'  => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'lokasi'         => $validated['lokasi'],
            'kegiatan'       => $validated['kegiatan'],
            'sub_kegiatan_id' => $validated['sub_kegiatan_id'] ?? null,
            'dinas_id'       => $user->dinas_id ?? null,
            'bidang_id'      => $user->bidang_id ?? null,
            'sub_bidang_id'  => $user->sub_bidang_id ?? null,
        ]);

        $spt->pegawais()->sync($validated['pegawai_ids']);

        return redirect()->route('spt.index')->with('success', 'SPT Mandiri berhasil dibuat!');
    }

    public function cetakSptMandiri($id)
    {
        $spt = Spt::with(['pegawais', 'subKegiatan'])->findOrFail($id);

        if (!$spt->isStandalone()) {
            return back()->with('error', 'Gunakan fitur cetak dari halaman Arsip untuk SPT yang berasal dari Nota Dinas.');
        }

        $pdf = Pdf::loadView('pages.spt.pdf_standalone', compact('spt'))
            ->setPaper('a4', 'portrait');
        $fileName = 'SPT-' . str_replace('/', '-', $spt->nomor_spt) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function destroyMandiri($id)
    {
        $spt = Spt::findOrFail($id);

        if (!$spt->isStandalone()) {
            return back()->with('error', 'SPT ini terhubung ke Nota Dinas dan tidak dapat dihapus dari sini.');
        }

        $spt->pegawais()->detach();
        $spt->delete();

        return redirect()->route('spt.index')->with('success', 'SPT berhasil dihapus.');
    }
}
