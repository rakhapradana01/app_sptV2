<?php

namespace App\Http\Controllers;

use App\Models\MasterShs;
use App\Models\MasterShsPenginapan;
use App\Models\MasterShsTransportasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterShsController extends Controller
{
    /**
     * Tampilkan daftar Master SHS dengan form inline di atas tabel.
     */
    public function index(Request $request)
    {
        $query = MasterShs::withCount(['penginapan', 'transportasi']);

        // Filter pencarian
        if ($search = $request->input('search')) {
            $query->where('lokasi_tujuan', 'like', "%{$search}%");
        }

        // Filter tahun anggaran
        if ($tahun = $request->input('tahun_anggaran')) {
            $query->where('tahun_anggaran', $tahun);
        }

        // Filter status
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status'));
        }

        $masterShs = $query->orderBy('tahun_anggaran', 'desc')
                           ->orderBy('lokasi_tujuan', 'asc')
                           ->paginate(15)
                           ->withQueryString();

        // Daftar tahun unik untuk dropdown filter
        $tahunList = MasterShs::select('tahun_anggaran')
                              ->distinct()
                              ->orderBy('tahun_anggaran', 'desc')
                              ->pluck('tahun_anggaran');

        return view('pages.master.tujuan_biaya.index', compact('masterShs', 'tahunList'));
    }

    /**
     * Simpan data Master SHS baru (inline form).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_tujuan'                  => 'required|string|max:255',
            'tahun_anggaran'                 => 'required|digits:4|integer|min:2020|max:2099',
            'uang_harian'                    => 'required|numeric|min:0',
            'tiket_pesawat'                  => 'required|numeric|min:0',
            'penginapan'                     => 'required|numeric|min:0',
            'transport_bjb_rumah_bandara'    => 'nullable|numeric|min:0',
            'transport_bjb_bandara_rumah'    => 'nullable|numeric|min:0',
            'transport_tujuan_bandara_hotel' => 'nullable|numeric|min:0',
            'transport_tujuan_hotel_bandara' => 'nullable|numeric|min:0',
            'is_active'                      => 'nullable|boolean',
        ]);

        // Cek duplikasi lokasi + tahun
        $exists = MasterShs::where('lokasi_tujuan', $validated['lokasi_tujuan'])
                            ->where('tahun_anggaran', $validated['tahun_anggaran'])
                            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'lokasi_tujuan' => "Lokasi \"{$validated['lokasi_tujuan']}\" untuk tahun anggaran {$validated['tahun_anggaran']} sudah ada.",
            ]);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['transport_bjb_rumah_bandara'] = $validated['transport_bjb_rumah_bandara'] ?? 0;
        $validated['transport_bjb_bandara_rumah'] = $validated['transport_bjb_bandara_rumah'] ?? 0;
        $validated['transport_tujuan_bandara_hotel'] = $validated['transport_tujuan_bandara_hotel'] ?? 0;
        $validated['transport_tujuan_hotel_bandara'] = $validated['transport_tujuan_hotel_bandara'] ?? 0;

        MasterShs::create($validated);

        return redirect()->route('master-shs.index')
                         ->with('success', 'Data SHS berhasil ditambahkan.');
    }

    /**
     * Tampilkan halaman detail/edit satu Master SHS beserta tabel penginapan & transportasi.
     */
    public function show($id)
    {
        $shs = MasterShs::with(['penginapan', 'transportasi'])->findOrFail($id);
        return view('pages.master.tujuan_biaya.show', compact('shs'));
    }

    /**
     * Update data utama Master SHS.
     */
    public function update(Request $request, $id)
    {
        $shs = MasterShs::findOrFail($id);

        $validated = $request->validate([
            'lokasi_tujuan'  => [
                'required', 'string', 'max:255',
                Rule::unique('master_shs')
                    ->where('tahun_anggaran', $request->input('tahun_anggaran', $shs->tahun_anggaran))
                    ->ignore($shs->id),
            ],
            'tahun_anggaran'                 => 'required|digits:4|integer|min:2020|max:2099',
            'uang_harian'                    => 'required|numeric|min:0',
            'tiket_pesawat'                  => 'required|numeric|min:0',
            'penginapan'                     => 'required|numeric|min:0',
            'transport_bjb_rumah_bandara'    => 'nullable|numeric|min:0',
            'transport_bjb_bandara_rumah'    => 'nullable|numeric|min:0',
            'transport_tujuan_bandara_hotel' => 'nullable|numeric|min:0',
            'transport_tujuan_hotel_bandara' => 'nullable|numeric|min:0',
            'is_active'                      => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['transport_bjb_rumah_bandara'] = $validated['transport_bjb_rumah_bandara'] ?? 0;
        $validated['transport_bjb_bandara_rumah'] = $validated['transport_bjb_bandara_rumah'] ?? 0;
        $validated['transport_tujuan_bandara_hotel'] = $validated['transport_tujuan_bandara_hotel'] ?? 0;
        $validated['transport_tujuan_hotel_bandara'] = $validated['transport_tujuan_hotel_bandara'] ?? 0;

        $shs->update($validated);

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Data SHS berhasil diperbarui.');
    }

    /**
     * Hapus Master SHS (cascade ke detail penginapan & transportasi).
     */
    public function destroy($id)
    {
        $shs = MasterShs::findOrFail($id);
        $shs->delete();

        return redirect()->route('master-shs.index')
                         ->with('success', "Data SHS \"{$shs->lokasi_tujuan}\" berhasil dihapus.");
    }

    /**
     * Toggle status aktif/nonaktif.
     */
    public function toggleStatus($id)
    {
        $shs = MasterShs::findOrFail($id);
        $shs->update(['is_active' => !$shs->is_active]);

        $status = $shs->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "SHS \"{$shs->lokasi_tujuan}\" berhasil {$status}.");
    }

    // ==========================================
    // DETAIL PENGINAPAN
    // ==========================================

    public function storePenginapan(Request $request, $shsId)
    {
        $shs = MasterShs::findOrFail($shsId);

        $validated = $request->validate([
            'golongan_kelas' => 'required|string|max:100',
            'tarif'          => 'required|numeric|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        // Cek duplikasi golongan
        $exists = $shs->penginapan()
                       ->where('golongan_kelas', $validated['golongan_kelas'])
                       ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'golongan_kelas' => "Golongan/kelas \"{$validated['golongan_kelas']}\" sudah ada untuk lokasi ini.",
            ]);
        }

        $shs->penginapan()->create($validated);

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif penginapan berhasil ditambahkan.');
    }

    public function updatePenginapan(Request $request, $shsId, $penginapanId)
    {
        $shs = MasterShs::findOrFail($shsId);
        $penginapan = $shs->penginapan()->findOrFail($penginapanId);

        $validated = $request->validate([
            'golongan_kelas' => [
                'required', 'string', 'max:100',
                Rule::unique('master_shs_penginapan')
                    ->where('master_shs_id', $shs->id)
                    ->ignore($penginapan->id),
            ],
            'tarif'      => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $penginapan->update($validated);

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif penginapan berhasil diperbarui.');
    }

    public function destroyPenginapan($shsId, $penginapanId)
    {
        $shs = MasterShs::findOrFail($shsId);
        $shs->penginapan()->findOrFail($penginapanId)->delete();

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif penginapan berhasil dihapus.');
    }

    // ==========================================
    // DETAIL TRANSPORTASI
    // ==========================================

    public function storeTransportasi(Request $request, $shsId)
    {
        $shs = MasterShs::findOrFail($shsId);

        $validated = $request->validate([
            'rute'                => 'required|string|max:255',
            'jenis_transportasi'  => 'nullable|string|max:100',
            'tarif'               => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string|max:500',
        ]);

        // Cek duplikasi rute + jenis
        $exists = $shs->transportasi()
                       ->where('rute', $validated['rute'])
                       ->where('jenis_transportasi', $validated['jenis_transportasi'] ?? null)
                       ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'rute' => "Rute \"{$validated['rute']}\" dengan jenis transportasi ini sudah ada.",
            ]);
        }

        $shs->transportasi()->create($validated);

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif transportasi berhasil ditambahkan.');
    }

    public function updateTransportasi(Request $request, $shsId, $transportasiId)
    {
        $shs = MasterShs::findOrFail($shsId);
        $transportasi = $shs->transportasi()->findOrFail($transportasiId);

        $validated = $request->validate([
            'rute' => [
                'required', 'string', 'max:255',
                Rule::unique('master_shs_transportasi')
                    ->where('master_shs_id', $shs->id)
                    ->where('jenis_transportasi', $request->input('jenis_transportasi'))
                    ->ignore($transportasi->id),
            ],
            'jenis_transportasi' => 'nullable|string|max:100',
            'tarif'              => 'required|numeric|min:0',
            'keterangan'         => 'nullable|string|max:500',
        ]);

        $transportasi->update($validated);

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif transportasi berhasil diperbarui.');
    }

    public function destroyTransportasi($shsId, $transportasiId)
    {
        $shs = MasterShs::findOrFail($shsId);
        $shs->transportasi()->findOrFail($transportasiId)->delete();

        return redirect()->route('master-shs.show', $shs->id)
                         ->with('success', 'Tarif transportasi berhasil dihapus.');
    }
}
