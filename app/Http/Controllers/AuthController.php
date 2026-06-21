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

            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->role->name === 'super_admin') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('nota-dinas.index');
        }

        return redirect()->route('login')
            ->with(['error' => 'Username atau password salah']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function register()
    {
        $roles = \App\Models\Role::whereNotIn('name', ['super_admin', 'admin'])->get();
        return view('pages.auth.signup', compact('roles'));
    }

    public function storeRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:2|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', ['super_admin', 'admin'])
            ],
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        Auth::login($user);

        if ($user->role->name === 'super_admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('nota-dinas.index');
    }
}
