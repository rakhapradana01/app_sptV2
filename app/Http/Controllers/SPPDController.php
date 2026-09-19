<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SPPDController extends Controller
{
    // =====================
    // ALUR EXISTING: via Nota Dinas
    // =====================

    public function store(Request $request, $notaId)
    {
        $request->validate([
            'nomor_sppd'      => 'required|string',
            'alat_angkutan'   => 'required|string',
            'tempat_berangkat' => 'required|string',
            'tempat_tujuan'   => 'required|string',
            'tanggal_sppd'    => 'required|date',
            'tempat_tujuan_2' => 'nullable|string'
        ]);
        $nota = NotaDinas::findOrFail($notaId);
        $user = auth()->user();

        Sppd::create([
            'nota_dinas_id'    => $nota->id,
            'nomor_sppd'       => $request->nomor_sppd,
            'alat_angkutan'    => $request->alat_angkutan,
            'tempat_berangkat' => $request->tempat_berangkat,
            'tempat_tujuan'    => $request->tempat_tujuan,
            'tanggal_sppd'     => $request->tanggal_sppd,
            'tempat_tujuan_2'  => $request->tempat_tujuan_2,
            'dinas_id'         => $user->dinas_id ?? null,
            'bidang_id'        => $user->bidang_id ?? null,
            'sub_bidang_id'    => $user->sub_bidang_id ?? null,
        ]);
        return redirect()->back()->with('success', 'Data SPPD berhasil dibuat.');
    }

    public function cetakSPPD($id)
    {
        $nota = NotaDinas::with(['spt', 'pegawais', 'sppd'])->findOrFail($id);

        if (!$nota->sppd) {
            return back()->with('error', 'Data SPPD belum diinput.');
        }

        $start = \Carbon\Carbon::parse($nota->tanggal_mulai)->startOfDay();
        $end = \Carbon\Carbon::parse($nota->tanggal_selesai ?: $nota->tanggal_mulai)->startOfDay();

        $lamaHari = (int) $start->diffInDays($end) + 1;

        $pdf = Pdf::loadView('pages.sppd.pdf', compact('nota', 'lamaHari'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SPPD-' . str_replace('/', '-', $nota->sppd->nomor_sppd) . '.pdf';

        return $pdf->stream($fileName);
    }

    // =====================
    // JALUR MANDIRI (Standalone)
    // =====================

    public function index()
    {
        $user = auth()->user();
        $query = Sppd::with(['pegawais', 'spt', 'notaDinas']);

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

        $sppds = $query->latest()
            ->paginate(10);

        return view('pages.sppd.index', compact('sppds'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        // Load semua SPT yang bisa diakses user (baik dari Nota Dinas maupun standalone)
        $querySpt = Spt::with('pegawais')
            ->orderBy('created_at', 'desc');

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if ($user->sub_bidang_id) {
                    $querySpt->where('sub_bidang_id', $user->sub_bidang_id);
                } else {
                    $querySpt->whereRaw('1 = 0');
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin', 'user'])) {
                if ($user->bidang_id) {
                    $querySpt->where('bidang_id', $user->bidang_id);
                }
            } elseif ($user->role->name === 'kepala_badan') {
                if ($user->dinas_id) {
                    $querySpt->where('dinas_id', $user->dinas_id);
                }
            }
        }

        $spts = $querySpt->get();
        $selectedSptId = $request->query('spt_id');

        return view('pages.sppd.create', compact('spts', 'selectedSptId'));
    }

    public function storeMandiri(Request $request)
    {
        $validated = $request->validate([
            'spt_id'           => 'required|exists:spts,id',
            'nomor_sppd'       => 'required|string',
            'alat_angkutan'    => 'required|string',
            'tempat_berangkat' => 'required|string',
            'tempat_tujuan'    => 'required|string',
            'tempat_tujuan_2'  => 'nullable|string',
            'tanggal_sppd'     => 'required|date',
            // Override optional — jika dikosongkan, ambil dari SPT
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'kegiatan'         => 'nullable|string',
            'pegawai_ids'      => 'nullable|array',
            'pegawai_ids.*'    => 'exists:pegawais,id',
        ]);

        // Ambil data dari SPT terpilih
        $spt = Spt::with('pegawais')->findOrFail($validated['spt_id']);

        $user = auth()->user();
        $sppd = Sppd::create([
            'nota_dinas_id'    => $spt->nota_dinas_id ?? null,
            'spt_id'           => $spt->id,
            'nomor_sppd'       => $validated['nomor_sppd'],
            'nomor_spt_ref'    => $spt->nomor_spt,
            'alat_angkutan'    => $validated['alat_angkutan'],
            'tempat_berangkat' => $validated['tempat_berangkat'],
            'tempat_tujuan'    => $validated['tempat_tujuan'],
            'tempat_tujuan_2'  => $validated['tempat_tujuan_2'] ?? null,
            'tanggal_sppd'     => $validated['tanggal_sppd'],
            // Jika user override, pakai nilai user; jika tidak, ambil dari SPT
            'tanggal_mulai'    => $validated['tanggal_mulai'] ?? $spt->tanggal_mulai,
            'tanggal_selesai'  => $validated['tanggal_selesai'] ?? $spt->tanggal_selesai,
            'kegiatan'         => !empty($validated['kegiatan']) ? $validated['kegiatan'] : $spt->kegiatan,
            'dinas_id'         => $user->dinas_id ?? $spt->dinas_id ?? null,
            'bidang_id'        => $user->bidang_id ?? $spt->bidang_id ?? null,
            'sub_bidang_id'    => $user->sub_bidang_id ?? $spt->sub_bidang_id ?? null,
        ]);

        // Sync pegawai dari request jika ada, jika tidak default dari SPT
        $pegawaiIds = !empty($validated['pegawai_ids'])
            ? $validated['pegawai_ids']
            : $spt->pegawais->pluck('id')->toArray();
        $sppd->pegawais()->sync($pegawaiIds);

        return redirect()->route('sppd.index')->with('success', 'SPPD berhasil dibuat dari SPT!');
    }

    public function cetakSPPDMandiri($id)
    {
        $sppd = Sppd::with(['pegawais', 'spt', 'notaDinas'])->findOrFail($id);

        $lamaHari = $sppd->lama_hari;

        $pdf = Pdf::loadView('pages.sppd.pdf_standalone', compact('sppd', 'lamaHari'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SPPD-' . str_replace('/', '-', $sppd->nomor_sppd) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function destroyMandiri($id)
    {
        $sppd = Sppd::findOrFail($id);

        $sppd->pegawais()->detach();
        $sppd->delete();

        return redirect()->route('sppd.index')->with('success', 'SPPD berhasil dihapus.');
    }
}
