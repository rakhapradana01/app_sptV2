<?php

namespace App\Http\Controllers;

use App\Models\SubBidang;
use App\Models\Dinas;
use Illuminate\Http\Request;

class SubBidangController extends Controller
{
    public function index()
    {
        $subBidangs = SubBidang::with('bidang.dinas')->latest()->paginate(10);
        $dinas = Dinas::orderBy('nama_dinas')->get();
        return view('pages.master.sub_bidang.index', compact('subBidangs', 'dinas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required|exists:bidangs,id',
            'nama_sub_bidang' => 'required|string|max:255'
        ]);

        SubBidang::create($request->all());

        return redirect()->route('sub-bidang.index')->with('success', 'Sub Bidang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $subBidang = SubBidang::findOrFail($id);
        $request->validate([
            'bidang_id' => 'required|exists:bidangs,id',
            'nama_sub_bidang' => 'required|string|max:255'
        ]);

        $subBidang->update($request->all());

        return redirect()->route('sub-bidang.index')->with('success', 'Sub Bidang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $subBidang = SubBidang::findOrFail($id);
        $subBidang->delete();

        return redirect()->route('sub-bidang.index')->with('success', 'Sub Bidang berhasil dihapus.');
    }
}
