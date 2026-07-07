<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Dinas;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $queryUser = User::with(['role', 'dinas', 'bidang', 'subBidang', 'pegawai'])->latest();
        $queryPegawai = Pegawai::orderBy('nama');

        if ($user && $user->dinas_id) {
            $queryUser->where('dinas_id', $user->dinas_id);
            $queryPegawai->where('dinas_id', $user->dinas_id);
            $dinas = Dinas::where('id', $user->dinas_id)->get();
        } else {
            $dinas = Dinas::orderBy('nama_dinas')->get();
        }

        $users = $queryUser->paginate(10);
        $roles = Role::whereNotIn('name', ['super_admin'])->get();
        $pegawais = $queryPegawai->get();
        
        return view('pages.master.users.index', compact('users', 'roles', 'dinas', 'pegawais'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:2|unique:users,username',
            'password' => 'required|string|min:6',
            'role_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', ['super_admin'])
            ],
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidangs,id',
            'pegawai_id' => 'nullable|exists:pegawais,id',
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $dinasId = auth()->user()->dinas_id ?? $request->dinas_id;

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'dinas_id' => $dinasId,
            'bidang_id' => $request->bidang_id,
            'sub_bidang_id' => $request->sub_bidang_id,
            'pegawai_id' => $request->pegawai_id,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User account successfully created.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:2|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', ['super_admin'])
            ],
            'dinas_id' => 'nullable|exists:dinas,id',
            'bidang_id' => 'nullable|exists:bidangs,id',
            'sub_bidang_id' => 'nullable|exists:sub_bidangs,id',
            'pegawai_id' => 'nullable|exists:pegawais,id',
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $dinasId = auth()->user()->dinas_id ?? $request->dinas_id;

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role_id' => $request->role_id,
            'dinas_id' => $dinasId,
            'bidang_id' => $request->bidang_id,
            'sub_bidang_id' => $request->sub_bidang_id,
            'pegawai_id' => $request->pegawai_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User account successfully updated.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent self-deletion
        if (auth()->id() == $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User account successfully deleted.');
    }
}
