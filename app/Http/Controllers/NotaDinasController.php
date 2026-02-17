<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class NotaDinasController extends Controller
{
    public function index(){
        $roles = Role::paginate(2);
        return view('pages.nota_dinas.index', compact('roles'));
    }
}
