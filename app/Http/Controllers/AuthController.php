<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('pages.auth.signin');
    }

    public function authenticated(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|min:2',
            'password' => 'required|min:6'
        ], [
            'username.required' => 'Username wajib diisi',
            'username.min' => 'Username minimal 2 karakter',
            'password.required' => 'Password minimal 2 karakter',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        if (Auth::attempt($data)) {
            return redirect('/');
        }

        return redirect()->route('login')->with(['error' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
