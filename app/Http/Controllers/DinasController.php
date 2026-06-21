<?php

namespace App\Http\Controllers;

use App\Models\Dinas;
use Illuminate\Http\Request;

class DinasController extends Controller
{
    public function index()
    {
        $dinas = Dinas::latest()->paginate(10);
        return view('pages.master.dinas.index', compact('dinas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas'
        ]);

        Dinas::create($request->all());

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dinas = Dinas::findOrFail($id);
        $request->validate([
            'nama_dinas' => 'required|string|max:255|unique:dinas,nama_dinas,' . $id
        ]);

        $dinas->update($request->all());

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $dinas = Dinas::findOrFail($id);
        $dinas->delete();

        return redirect()->route('dinas.index')->with('success', 'Dinas berhasil dihapus.');
    }
}
