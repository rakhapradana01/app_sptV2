<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Spt;
use Illuminate\Http\Request;

use Carbon\Carbon;

class SPJController extends Controller
{
    public function index(Request $request)
    {
        $spt = Spt::with(['notaDinas.pegawais'])->get();

        if ($request->ajax()) {
            return response()->json($spt);
        }

        return view('pages.spj.index', compact('spt'));
    }

    public function show($id)
    {
        $spt = Spt::with(['notaDinas.pegawais'])->findOrFail($id);

        return view('pages.spj.show', compact('spt'));
    }
}
