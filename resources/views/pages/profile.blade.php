@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <x-common.page-breadcrumb pageTitle="Profil Saya" />

    {{-- Alert Notifikasi --}}
    @if (session('success'))
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300">
            <svg class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            <div class="flex items-center gap-2 mb-2 font-semibold text-sm">
                <svg class="h-5 w-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc pl-7 text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
        {{-- Sisi Kiri: Ringkasan Akun & Data Kepegawaian --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Kartu Info Profil --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col items-center text-center">
                    {{-- Avatar Initial --}}
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-tr from-brand-600 to-brand-400 text-3xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500 dark:border-gray-900" title="Aktif"></span>
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h3>
                    <p class="text-xs font-mono text-gray-500 dark:text-gray-400">
                        {{ '@' . $user->username }}
                    </p>

                    <div class="mt-3">
                        @php
                            $roleName = $user->role->name ?? 'user';
                            $roleBadges = [
                                'super_admin'       => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                'admin'             => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                'kepala_badan'      => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                'sekretaris_badan'  => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800',
                                'kepala_bidang'     => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                'kepala_sub_bidang' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-900/30 dark:text-sky-300 dark:border-sky-800',
                                'user'              => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
                            ];
                            $badgeStyle = $roleBadges[$roleName] ?? $roleBadges['user'];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeStyle }}">
                            {{ ucwords(str_replace('_', ' ', $roleName)) }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-5 dark:border-gray-800">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">
                        Unit Kerja & Instansi
                    </h4>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-gray-400 dark:text-gray-500">Dinas / SKPD</dt>
                            <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->dinas->nama_dinas ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 dark:text-gray-500">Bidang</dt>
                            <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->bidang->nama_bidang ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 dark:text-gray-500">Sub Bidang</dt>
                            <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ $user->subBidang->nama_sub_bidang ?? '-' }}
                            </dd>
                        </div>
                        @if($user->pegawai)
                            <div class="pt-2 border-t border-dashed border-gray-100 dark:border-gray-800">
                                <dt class="text-gray-400 dark:text-gray-500">Data Pegawai Terkait</dt>
                                <dd class="mt-0.5 font-medium text-gray-800 dark:text-gray-200">
                                    {{ $user->pegawai->nama }}
                                </dd>
                                <dd class="text-[11px] text-gray-500 dark:text-gray-400">
                                    NIP: {{ $user->pegawai->nip ?? '-' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Info Box Kebijakan Role --}}
                <div class="mt-5 rounded-xl bg-gray-50 p-3.5 text-xs text-gray-600 dark:bg-gray-800/60 dark:text-gray-400 border border-gray-200/80 dark:border-gray-700/60">
                    <div class="flex items-start gap-2">
                        <svg class="h-4 w-4 shrink-0 text-brand-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>
                            Role dan Unit Kerja diatur secara terpusat oleh Super Admin. Jika terdapat penyesuaian instansi atau jabatan, silakan hubungi Administrator.
                        </span>
                    </div>
                </div>

                @if($roleName === 'super_admin')
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2 text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 dark:bg-brand-900/30 dark:text-brand-300 dark:hover:bg-brand-900/50 rounded-xl transition">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Kelola Semua Pengguna (Menu Akun)</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Kartu Tanda Tangan / Paraf Digital --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Tanda Tangan Digital (Paraf)
                    </h4>
                </div>

                @if($user->signature_path && file_exists(public_path($user->signature_path)))
                    <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 text-center dark:border-gray-800 dark:bg-gray-800/40">
                        <img src="{{ asset($user->signature_path) }}" alt="Tanda Tangan {{ $user->name }}" class="h-20 max-w-full mx-auto object-contain">
                        <p class="mt-2 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Tanda tangan aktif pada sistem
                        </p>
                    </div>

                    <form action="{{ route('profile.deleteSignature') }}" method="POST" class="mt-3" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-1.5 py-1.5 px-3 text-xs font-medium text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:text-rose-400 dark:hover:bg-rose-950/40 rounded-lg transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Tanda Tangan
                        </button>
                    </form>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4 text-center dark:border-amber-900/30 dark:bg-amber-900/10">
                        <svg class="w-8 h-8 mx-auto text-amber-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">Belum Ada Tanda Tangan</p>
                        <p class="mt-1 text-[11px] text-amber-700 dark:text-amber-400">
                            Upload file tanda tangan pada form di samping agar dapat digunakan pada paraf dokumen.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sisi Kanan: Form Update Profil & Keamanan --}}
        <div class="lg:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 lg:p-8 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900"
                 x-data="{
                    showPassword: false,
                    showConfirmPassword: false,
                    sigPreview: null,
                    handleSigChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.sigPreview = URL.createObjectURL(file);
                        } else {
                            this.sigPreview = null;
                        }
                    }
                 }">

                <div class="border-b border-gray-100 pb-5 dark:border-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        Perbarui Informasi Profil
                    </h3>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Kelola nama, username login, tanda tangan digital, serta kata sandi akun Anda.
                    </p>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Bagian 1: Data Identitas Akun --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400 mb-4 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Data Akun
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="w-full rounded-xl border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                       placeholder="Nama Lengkap ASN">
                            </div>

                            {{-- Username --}}
                            <div>
                                <label for="username" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Username Login <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="username"
                                       name="username"
                                       value="{{ old('username', $user->username) }}"
                                       required
                                       class="w-full rounded-xl border border-gray-300 bg-transparent px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                       placeholder="username_anda">
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 2: Upload Tanda Tangan / Paraf --}}
                    <div class="border-t border-gray-100 pt-6 dark:border-gray-800">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400 mb-4 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Unggah Tanda Tangan Digital (Paraf)
                        </h4>

                        <div class="space-y-3">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                Pilih Berkas Tanda Tangan (Opsional)
                            </label>
                            <input type="file"
                                   name="signature"
                                   accept="image/png,image/jpeg,image/jpg"
                                   @change="handleSigChange($event)"
                                   class="block w-full text-xs text-gray-600 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-xl file:border-0
                                          file:text-xs file:font-semibold
                                          file:bg-brand-50 file:text-brand-700
                                          hover:file:bg-brand-100
                                          dark:file:bg-brand-900/30 dark:file:text-brand-300
                                          border border-gray-300 dark:border-gray-700 rounded-xl p-2
                                          bg-white dark:bg-gray-800/50">

                            <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                Format didukung: <strong>PNG, JPG, JPEG</strong> (maks. 1 MB). Disarankan menggunakan format PNG dengan latar transparan agar hasil cetak pada nota/SPT terlihat bersih dan rapi.
                            </p>

                            {{-- Live Preview Sebelum Diupload --}}
                            <template x-if="sigPreview">
                                <div class="mt-3 p-3 bg-brand-50/50 dark:bg-brand-900/10 border border-brand-200 dark:border-brand-800/40 rounded-xl">
                                    <p class="text-xs font-semibold text-brand-700 dark:text-brand-300 mb-2">Pratinjau Berkas Baru:</p>
                                    <img :src="sigPreview" class="h-16 max-w-full object-contain mx-auto bg-white/70 dark:bg-gray-800/70 p-1 rounded-lg border border-gray-200 dark:border-gray-700">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Bagian 3: Ganti Password --}}
                    <div class="border-t border-gray-100 pt-6 dark:border-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-brand-600 dark:text-brand-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Ganti Password (Opsional)
                            </h4>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                            Kosongkan kedua kolom di bawah jika Anda tidak ingin mengubah password saat ini.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Password Baru --}}
                            <div>
                                <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'"
                                           id="password"
                                           name="password"
                                           class="w-full rounded-xl border border-gray-300 bg-transparent px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                           placeholder="Minimal 6 karakter">
                                    <button type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Konfirmasi Password Baru --}}
                            <div>
                                <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Ulangi Password Baru
                                </label>
                                <div class="relative">
                                    <input :type="showConfirmPassword ? 'text' : 'password'"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="w-full rounded-xl border border-gray-300 bg-transparent px-3.5 py-2.5 pr-10 text-sm text-gray-900 placeholder-gray-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-700 dark:text-white dark:focus:border-brand-500"
                                           placeholder="Ulangi password baru">
                                    <button type="button"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                        <svg x-show="!showConfirmPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg x-show="showConfirmPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi Simpan --}}
                    <div class="border-t border-gray-100 pt-6 dark:border-gray-800 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}"
                           class="px-5 py-2.5 text-xs font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-600 rounded-xl shadow-md shadow-brand-500/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
