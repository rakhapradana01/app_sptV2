<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil user yang sedang login.
     */
    public function edit()
    {
        /** @var User $user */
        $user = auth()->user();
        $user->load(['role', 'pegawai', 'dinas', 'bidang', 'subBidang']);

        return view('pages.profile', compact('user'));
    }

    /**
     * Simpan perubahan profil user yang sedang login.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|min:2|max:50|unique:users,username,' . $user->id,
            'password'  => 'nullable|string|min:6|confirmed',
            'signature' => 'nullable|image|mimes:jpeg,jpg,png|max:1024',
        ], [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username tersebut sudah digunakan oleh akun lain.',
            'password.min'        => 'Password minimal harus 6 karakter.',
            'password.confirmed'  => 'Konfirmasi password baru tidak cocok.',
            'signature.image'     => 'File tanda tangan harus berupa gambar.',
            'signature.mimes'     => 'Format file tanda tangan harus JPEG, JPG, atau PNG.',
            'signature.max'       => 'Ukuran file tanda tangan maksimal 1 MB.',
        ]);

        $user->name     = $request->name;
        $user->username = $request->username;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('signature')) {
            // Hapus file tanda tangan lama jika ada di server
            if ($user->signature_path && file_exists(public_path($user->signature_path))) {
                @unlink(public_path($user->signature_path));
            }

            $sigDir = public_path('images/signatures');
            if (!is_dir($sigDir)) {
                @mkdir($sigDir, 0755, true);
            }

            $file     = $request->file('signature');
            $filename = 'sig_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($sigDir, $filename);

            $user->signature_path = 'images/signatures/' . $filename;
        }

        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Hapus tanda tangan/paraf digital user.
     */
    public function deleteSignature()
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->signature_path && file_exists(public_path($user->signature_path))) {
            @unlink(public_path($user->signature_path));
        }

        $user->signature_path = null;
        $user->save();

        return redirect()->route('profile.edit')
            ->with('success', 'Tanda tangan digital berhasil dihapus.');
    }
}
