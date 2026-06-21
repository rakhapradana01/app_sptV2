<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->paginate(10);
        $roles = Role::whereNotIn('name', ['super_admin', 'admin'])->get();
        return view('pages.master.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:2|unique:users,username',
            'password' => 'required|string|min:6',
            'role_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', ['super_admin', 'admin'])
            ],
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
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
                \Illuminate\Validation\Rule::exists('roles', 'id')->whereNotIn('name', ['super_admin', 'admin'])
            ],
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun lain.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role_id' => $request->role_id,
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
