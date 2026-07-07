<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Sppd;
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
        $query = Sppd::with('pegawais')
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

        $sppds = $query->latest()
            ->paginate(10);

        return view('pages.sppd.index', compact('sppds'));
    }

    public function create()
    {
        $user = auth()->user();
        $queryPeg = Pegawai::orderBy('nama');
        if ($user && $user->bidang_id) {
            $queryPeg->where('bidang_id', $user->bidang_id);
        }
        $pegawais = $queryPeg->get();
 
         return view('pages.sppd.create', compact('pegawais'));
    }

    public function storeMandiri(Request $request)
    {
        $validated = $request->validate([
            'nomor_sppd'       => 'required|string',
            'nomor_spt_ref'    => 'nullable|string',
            'alat_angkutan'    => 'required|string',
            'tempat_berangkat' => 'required|string',
            'tempat_tujuan'    => 'required|string',
            'tempat_tujuan_2'  => 'nullable|string',
            'tanggal_sppd'     => 'required|date',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'kegiatan'         => 'required|string',
            'pegawai_ids'      => 'required|array|min:1',
            'pegawai_ids.*'    => 'exists:pegawais,id',
        ]);

        $user = auth()->user();
        $sppd = Sppd::create([
            'nota_dinas_id'    => null,
            'nomor_sppd'       => $validated['nomor_sppd'],
            'nomor_spt_ref'    => $validated['nomor_spt_ref'] ?? null,
            'alat_angkutan'    => $validated['alat_angkutan'],
            'tempat_berangkat' => $validated['tempat_berangkat'],
            'tempat_tujuan'    => $validated['tempat_tujuan'],
            'tempat_tujuan_2'  => $validated['tempat_tujuan_2'] ?? null,
            'tanggal_sppd'     => $validated['tanggal_sppd'],
            'tanggal_mulai'    => $validated['tanggal_mulai'],
            'tanggal_selesai'  => $validated['tanggal_selesai'] ?? null,
            'kegiatan'         => $validated['kegiatan'],
            'dinas_id'         => $user->dinas_id ?? null,
            'bidang_id'        => $user->bidang_id ?? null,
            'sub_bidang_id'    => $user->sub_bidang_id ?? null,
        ]);

        $sppd->pegawais()->sync($validated['pegawai_ids']);

        return redirect()->route('sppd.index')->with('success', 'SPPD Mandiri berhasil dibuat!');
    }

    public function cetakSPPDMandiri($id)
    {
        $sppd = Sppd::with('pegawais')->findOrFail($id);

        if (!$sppd->isStandalone()) {
            return back()->with('error', 'Gunakan fitur cetak dari halaman Arsip untuk SPPD yang berasal dari Nota Dinas.');
        }

        $lamaHari = $sppd->lama_hari;

        $pdf = Pdf::loadView('pages.sppd.pdf_standalone', compact('sppd', 'lamaHari'))
            ->setPaper('a4', 'portrait');

        $fileName = 'SPPD-' . str_replace('/', '-', $sppd->nomor_sppd) . '.pdf';

        return $pdf->stream($fileName);
    }

    public function destroyMandiri($id)
    {
        $sppd = Sppd::findOrFail($id);

        if (!$sppd->isStandalone()) {
            return back()->with('error', 'SPPD ini terhubung ke Nota Dinas dan tidak dapat dihapus dari sini.');
        }

        $sppd->pegawais()->detach();
        $sppd->delete();

        return redirect()->route('sppd.index')->with('success', 'SPPD berhasil dihapus.');
    }
}
