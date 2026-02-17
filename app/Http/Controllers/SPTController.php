<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class SPTController extends Controller
{
    public function index()
    {
        $role = Role::paginate(2);

        notify()
            ->success()
            ->title('⚡️ Laravel Notify is awesome!')
            ->send();
        return view('pages.spt.index', [
            'roles' => $role
        ]);
    }
}
