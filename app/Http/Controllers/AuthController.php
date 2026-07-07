<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dinas;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        $dinas = Dinas::orderBy('nama_dinas')->get();

        return view('pages.auth.signin', compact('dinas'));
    }

    public function authenticated(Request $request)
    {
        // 1. Validasi awal: Jika bukan superadmin00, dinas wajib diisi
        $isSuperAdmin = $request->input('username') === 'superadmin00';
        
        $rules = [
            'username' => 'required|min:2',
            'password' => 'required|min:6',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi.',
            'username.min'      => 'Username minimal 2 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ];

        if (!$isSuperAdmin) {
            $rules['dinas'] = 'required|exists:dinas,id';
            $messages['dinas.required'] = 'Silakan pilih Dinas terlebih dahulu.';
            $messages['dinas.exists'] = 'Dinas tidak valid.';
        }

        $data = $request->validate($rules, $messages);

        // 2. Cari user
        if ($isSuperAdmin) {
            $user = User::where('username', 'superadmin00')->first();
        } else {
            $user = User::where('username', $data['username'])
                        ->where('dinas_id', $data['dinas'])
                        ->first();
        }

        // Jika kombinasi tidak cocok di database
        if (!$user) {
            return redirect()->route('login')
                ->withInput($request->only('username', 'dinas'))
                ->with(['error' => $isSuperAdmin ? 'Akun Super Admin tidak terdaftar.' : 'Username tidak terdaftar di Dinas yang dipilih.']);
        }

        // 3. Jika user ditemukan di dinas tersebut, baru kita lakukan verifikasi password
        $credentials = [
            'username' => $data['username'],
            'password' => $data['password']
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Pengalihan halaman berdasarkan role
            if ($user->role->name === 'super_admin') {
                return redirect()->route('dashboard');
            }

            if ($user->role->name === 'admin') {
                return redirect()->route('sub-kegiatan.index');
            }

            return redirect()->route('nota-dinas.index');
        }

        // Jika password salah
        return redirect()->route('login')
            ->withInput($request->only('username', 'dinas'))
            ->with(['error' => 'Username atau password salah.']);
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
        $dinas = Dinas::orderBy('nama_dinas')->get();
        return view('pages.auth.signup', compact('roles', 'dinas'));
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
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidangs,id',
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role_id' => $request->role_id,
            'dinas_id' => $request->dinas_id,
            'bidang_id' => $request->bidang_id,
            'sub_bidang_id' => $request->sub_bidang_id,
        ]);

        Auth::login($user);

        if ($user->role->name === 'super_admin') {
            return redirect()->route('dashboard');
        }

        if ($user->role->name === 'admin') {
            return redirect()->route('sub-kegiatan.index');
        }

        return redirect()->route('nota-dinas.index');
    }
}