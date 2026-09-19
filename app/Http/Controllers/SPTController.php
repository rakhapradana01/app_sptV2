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
        $query = Spt::with(['pegawais', 'subKegiatan', 'notaDinas']);

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('sub_bidang_id', $user->sub_bidang_id);
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin', 'user'])) {
                if ($user->bidang_id) {
                    $query->where('bidang_id', $user->bidang_id);
                }
            } elseif ($user->role->name === 'kepala_badan') {
                if ($user->dinas_id) {
                    $query->where('dinas_id', $user->dinas_id);
                }
            }
        }

        $spts = $query->latest()
            ->paginate(10);

        return view('pages.spt.index', compact('spts'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // Ambil Nota Dinas yang sudah di-ACC Kaban atau Kabid
        $queryNota = NotaDinas::with(['pegawais', 'subKegiatan'])
            ->whereIn('status', [NotaDinas::DISETUJUI_KABAN, NotaDinas::DISETUJUI_KABID])
            ->orderBy('created_at', 'desc');

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if ($user->sub_bidang_id) {
                    $queryNota->where('sub_bidang_id', $user->sub_bidang_id);
                } else {
                    $queryNota->whereRaw('1 = 0');
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin', 'user'])) {
                if ($user->bidang_id) {
                    $queryNota->where('bidang_id', $user->bidang_id);
                }
            } elseif ($user->role->name === 'kepala_badan') {
                if ($user->dinas_id) {
                    $queryNota->where('dinas_id', $user->dinas_id);
                }
            }
        }
        $notaDinasList = $queryNota->get();

        $querySub = SubKegiatan::query();
        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if ($user->sub_bidang_id) {
                    $querySub->where('sub_bidang_id', $user->sub_bidang_id);
                } else {
                    $querySub->whereRaw('1 = 0');
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin', 'user'])) {
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

        $selectedNotaId = $request->query('nota_id');

        return view('pages.spt.create', compact('subKegiatans', 'pegawais', 'notaDinasList', 'selectedNotaId'));
    }

    public function storeMandiri(Request $request)
    {
        $validated = $request->validate([
            'nota_dinas_id'   => 'nullable|exists:nota_dinas,id',
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

        // Jika ada nota dinas, ambil dinas/bidang/sub bidang dari nota dinas jika user kosong
        $nota = !empty($validated['nota_dinas_id']) ? NotaDinas::find($validated['nota_dinas_id']) : null;

        $spt = Spt::create([
            'nota_dinas_id'  => $validated['nota_dinas_id'] ?? null,
            'nomor_spt'      => $validated['nomor_spt'],
            'jenis_anggaran' => $validated['jenis_anggaran'],
            'tahun_anggaran' => $validated['tahun_anggaran'],
            'tanggal_mulai'  => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'lokasi'         => $validated['lokasi'],
            'kegiatan'       => $validated['kegiatan'],
            'sub_kegiatan_id' => $validated['sub_kegiatan_id'] ?? null,
            'dinas_id'       => $user->dinas_id ?? $nota?->dinas_id ?? null,
            'bidang_id'      => $user->bidang_id ?? $nota?->bidang_id ?? null,
            'sub_bidang_id'  => $user->sub_bidang_id ?? $nota?->sub_bidang_id ?? null,
        ]);

        $spt->pegawais()->sync($validated['pegawai_ids']);

        return redirect()->route('spt.index')->with('success', 'SPT berhasil dibuat dan siap dicetak bertandatangan Kepala Badan!');
    }

    public function cetakSptMandiri($id)
    {
        $spt = Spt::with(['pegawais', 'subKegiatan', 'notaDinas'])->findOrFail($id);

        $pdf = Pdf::loadView('pages.spt.pdf_standalone', compact('spt'))
            ->setPaper('a4', 'portrait');
        $fileName = 'SPT-' . str_replace('/', '-', $spt->nomor_spt) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function destroyMandiri($id)
    {
        $spt = Spt::findOrFail($id);

        $spt->pegawais()->detach();
        $spt->delete();

        return redirect()->route('spt.index')->with('success', 'SPT berhasil dihapus.');
    }
}
