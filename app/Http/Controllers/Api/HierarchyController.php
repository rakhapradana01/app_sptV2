<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\SubBidang;

class HierarchyController extends Controller
{
    public function getBidangs($dinas_id = null)
    {
        if ($dinas_id && $dinas_id !== 'all') {
            $bidangs = Bidang::where('dinas_id', $dinas_id)->orderBy('nama_bidang')->get();
        } else {
            $bidangs = Bidang::orderBy('nama_bidang')->get();
        }
        return response()->json($bidangs);
    }

    public function getSubBidangs($bidang_id = null)
    {
        if ($bidang_id && $bidang_id !== 'all') {
            $subBidangs = SubBidang::where('bidang_id', $bidang_id)->orderBy('nama_sub_bidang')->get();
        } else {
            $subBidangs = SubBidang::orderBy('nama_sub_bidang')->get();
        }
        return response()->json($subBidangs);
    }
}

