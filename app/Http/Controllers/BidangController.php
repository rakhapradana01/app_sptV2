<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Dinas;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index()
    {
        $bidangs = Bidang::with('dinas')->latest()->paginate(10);
        $dinas = Dinas::orderBy('nama_dinas')->get();
        return view('pages.master.bidang.index', compact('bidangs', 'dinas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dinas_id' => 'required|exists:dinas,id',
            'nama_bidang' => 'required|string|max:255'
        ]);

        Bidang::create($request->all());

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $bidang = Bidang::findOrFail($id);
        $request->validate([
            'dinas_id' => 'required|exists:dinas,id',
            'nama_bidang' => 'required|string|max:255'
        ]);

        $bidang->update($request->all());

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $bidang = Bidang::findOrFail($id);
        $bidang->delete();

        return redirect()->route('bidang.index')->with('success', 'Bidang berhasil dihapus.');
    }
}
