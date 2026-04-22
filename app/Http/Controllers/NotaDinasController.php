<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Pegawai;
use App\Models\Role;
use App\Models\SubKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class NotaDinasController extends Controller
{
    public function cetakNotaDinas(NotaDinas $nota)
    {
        $pdf = Pdf::loadView('pages.nota_dinas.pdf', compact('nota'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('nota_dinas.pdf');
    }


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

    public function createPegawai($notaId)
    {
        $nota = NotaDinas::findOrFail($notaId);
        $pegawai = Pegawai::all();

        return view('nota-dinas.pegawai.create', compact('nota', 'pegawai'));
    }
    public function storePegawai(Request $request, $notaId)
    {
        $nota = NotaDinas::findOrFail($notaId);

        $nota->pegawais()->syncWithoutDetaching($request->pegawai_ids);

        return back()->with('success', 'Pegawai ditambahkan');
    }
    public function destroyPegawai($notaId, $pegawaiId)
    {
        $nota = NotaDinas::findOrFail($notaId);

        $nota->pegawais()->detach($pegawaiId);

        return back()->with('success', 'Pegawai dihapus');
    }

    public function create()
    {
        $subKegiatans = SubKegiatan::all();

        $kepalaBadan = Pegawai::where('jabatan', 'Kepala Badan')->get();
        $kepalaBidang = Pegawai::where('jabatan', 'Kepala Bidang')->get();
        $kasubid = Pegawai::where('jabatan', 'like', 'Kepala Sub Bidang%')->get();
        $staff = Pegawai::all();
        $subKegiatans = SubKegiatan::all();

        return view('pages.nota_dinas.create', compact(
            'kepalaBadan',
            'kepalaBidang',
            'kasubid',
            'staff',
            'subKegiatans'
        ));
    }

    public function store(Request $request)
    {
        $nomor = '900.1 /          / BPKAD / ' . Carbon::now()->year;

        $validated = $request->validate([
            'sub_kegiatan_id' => 'required|exists:sub_kegiatans,id',
            'tanggal' => 'required|date',
            'kepada_id' => 'required|exists:pegawais,id',
            'dari_id' => 'required|exists:pegawais,id',
            'melalui_id' => 'nullable|exists:pegawais,id',
            'perihal' => 'required|string',
            'lokasi' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date',
            'pegawai_ids' => 'nullable|array',
            'pegawai_ids.*' => 'exists:pegawais,id',
            'kegiatan' => 'required|string',
            'asal_undangan' => 'required|string',
            'sifat' => 'nullable|string',
            'lampiran' => 'nullable|string'
        ]);

        $nota = NotaDinas::create([
            'nomor_urut' => $nomor,
            'asal_undangan' => $validated['asal_undangan'],
            'sub_kegiatan_id' => $validated['sub_kegiatan_id'],
            'tanggal' => $validated['tanggal'],
            'kepada_id' => $validated['kepada_id'],
            'dari_id' => $validated['dari_id'],
            'melalui_id' => $validated['melalui_id'] ?? null,
            'perihal' => $validated['perihal'],
            'lokasi' => $validated['lokasi'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'kegiatan' => $validated['kegiatan'],
            'status' => NotaDinas::DIAJUKAN_KABID,
            'sifat' => $validated['sifat'],
            'lampiran' => $validated['lampiran']
        ]);

        if (!empty($validated['pegawai_ids'])) {
            $nota->pegawais()->syncWithoutDetaching($validated['pegawai_ids']);
        }

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Nota dinas berhasil disimpan.');
    }

    public function approveKasubid(NotaDinas $nota)
    {
        if (
            Auth::user()->role->name != 'kepala_sub_bidang'
            && Auth::user()->role->name != 'super_admin'
        ) {
            abort(403);
        }

        if ($nota->status != NotaDinas::DRAFT) {
            return back()->with('error', 'Status tidak valid');
        }

        $nota->update([
            'status' => NotaDinas::DIAJUKAN_KABID
        ]);

        return back()->with('success', 'Berhasil diajukan ke Kabid');
    }

    public function revisiKabid(Request $request, $id)
    {
        $request->validate([
            'revisi' => 'required|string|min:5',
        ]);

        $nota = NotaDinas::findOrFail($id);

        $nota->update([
            'status' => NotaDinas::REVISI_KABID, // Status berubah jadi revisi
            'revisi' => $request->revisi,        // Simpan pesan revisinya
        ]);

        return redirect()->route('nota-dinas.index')->with('warning', 'Nota dikembalikan ke Kasubid untuk diperbaiki.');
    }

    public function rejectKabid($id)
    {
        $nota = NotaDinas::findOrFail($id);
        $nota->update(['status' => 'ditolak']);

        return redirect()->route('nota-dinas.index')->with('error', 'Nota dinas telah ditolak.');
    }
    public function approveKabid(NotaDinas $nota)
    {
        if (
            Auth::user()->role->name != 'kepala_bidang'
            && Auth::user()->role->name != 'super_admin'
        ) {
            abort(403);
        }

        if ($nota->status != NotaDinas::DIAJUKAN_KABID) {
            return back()->with('error', 'Status tidak valid');
        }

        $nota->update([
            'status' => NotaDinas::DISETUJUI_KABID
        ]);

        return redirect()->route('nota-dinas.index')
            ->with('success', 'Telah Disetujui');
    }

    public function preview(NotaDinas $nota)
    {
        $nota->load('pegawais');

        $grouped = $nota->pegawais
            ->groupBy('jabatan')
            ->map(function ($items) {
                return $items->count();
            });

        $pegawais = Pegawai::all();

        return view('pages.nota_dinas.preview', [
            'nota' => $nota,
            'groupedPegawai' => $grouped,
            'pegawais' => $pegawais
        ]);
    }
}
