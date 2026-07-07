@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pegawai" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Pegawai">
            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button size="sm" @click="$dispatch('open-profile-create-modal')">
                    <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah
                </x-ui.button>
                <button type="button" @click="$dispatch('open-import-modal')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-500 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 transition hover:bg-emerald-100 dark:border-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import Excel
                </button>
                <a href="{{ route('pegawai.template') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh Template
                </a>
            </div>
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6">No</th>
                                <th class="px-5 py-3 text-left sm:px-6">Nama</th>
                                <th class="px-5 py-3 text-left sm:px-6">NIP</th>
                                <th class="px-5 py-3 text-left sm:px-6">Pangkat</th>
                                <th class="px-5 py-3 text-left sm:px-6">Jabatan</th>
                                <th class="px-5 py-3 text-left sm:px-6">Dinas</th>
                                <th class="px-5 py-3 text-left sm:px-6">Bidang</th>
                                <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawais as $pegawai)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawais->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->nama }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->nip }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->pangkat }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->jabatan ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->dinas->nama_dinas ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $pegawai->bidang->nama_bidang ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <x-ui.button variant="yellow" 
                                                @click="$dispatch('open-profile-edit-modal', {
                                                    id: '{{ $pegawai->id }}',
                                                    nama: '{{ addslashes($pegawai->nama) }}',
                                                    nip: '{{ $pegawai->nip }}',
                                                    pangkat: '{{ addslashes($pegawai->pangkat) }}',
                                                    jabatan: '{{ addslashes($pegawai->jabatan) }}',
                                                    dinas_id: '{{ $pegawai->dinas_id }}',
                                                    bidang_id: '{{ $pegawai->bidang_id }}'
                                                })">
                                                Edit
                                            </x-ui.button>

                                            <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin hapus?')">
                                                @csrf
                                                @method('DELETE')

                                                <x-ui.button variant="red" type="submit">
                                                    Hapus
                                                </x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </div>
            </div>
            <x-ui.modal x-data="{ open: false }" @open-profile-create-modal.window="open = true" :isOpen="false"
                class="max-w-[700px]">
                <div
                    class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <div class="px-2 pr-14">
                        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Tambah Pegawai
                        </h4>
                    </div>
                    <form method="POST" action="{{ route('pegawai.store') }}" class="flex flex-col">
                        @csrf
                        <div class="px-2 overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama
                                    </label>
                                    <input type="text" name="nama"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nip
                                    </label>
                                    <input type="text" name="nip"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Pangkat
                                    </label>
                                    <input type="text" name="pangkat"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jabatan
                                    </label>
                                    <input type="text" name="jabatan"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                @if(!auth()->user()->dinas_id)
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Dinas
                                    </label>
                                    <select name="dinas_id" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <option value="">-- Pilih Dinas --</option>
                                        @foreach($dinas as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(auth()->user()->bidang_id)
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Bidang
                                    </label>
                                    <div class="flex h-11 items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        🏢 {{ auth()->user()->bidang->nama_bidang ?? '-' }}
                                        <span class="ml-2 text-xs text-gray-400">(otomatis sesuai bidang Anda)</span>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Bidang
                                    </label>
                                    <select name="bidang_id" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <option value="">-- Pilih Bidang --</option>
                                        @foreach($bidangs as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-6 lg:justify-end">
                            <button @click="open = false" type="button"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                Close
                            </button>
                            <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            {{-- Modal Edit Pegawai --}}
            <x-ui.modal x-data="{ 
                open: false, 
                id: '', 
                nama: '', 
                nip: '', 
                pangkat: '', 
                jabatan: '', 
                dinas_id: '', 
                bidang_id: '',
                actionUrl: ''
            }" 
            @open-profile-edit-modal.window="
                open = true;
                id = $event.detail.id;
                nama = $event.detail.nama;
                nip = $event.detail.nip;
                pangkat = $event.detail.pangkat;
                jabatan = $event.detail.jabatan;
                dinas_id = $event.detail.dinas_id;
                bidang_id = $event.detail.bidang_id;
                actionUrl = '/pegawai/' + id;
            " 
            :isOpen="false"
            class="max-w-[700px]">
                <div class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <div class="px-2 pr-14">
                        <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                            Edit Pegawai
                        </h4>
                    </div>
                    <form method="POST" :action="actionUrl" class="flex flex-col">
                        @csrf
                        @method('PUT')
                        <div class="px-2 overflow-y-auto custom-scrollbar">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama
                                    </label>
                                    <input type="text" name="nama" x-model="nama"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nip
                                    </label>
                                    <input type="text" name="nip" x-model="nip"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Pangkat
                                    </label>
                                    <input type="text" name="pangkat" x-model="pangkat"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Jabatan
                                    </label>
                                    <input type="text" name="jabatan" x-model="jabatan"
                                        class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                </div>

                                @if(!auth()->user()->dinas_id)
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Dinas
                                    </label>
                                    <select name="dinas_id" x-model="dinas_id" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <option value="">-- Pilih Dinas --</option>
                                        @foreach($dinas as $d)
                                            <option value="{{ $d->id }}">{{ $d->nama_dinas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                @if(auth()->user()->bidang_id)
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Bidang
                                    </label>
                                    <div class="flex h-11 items-center rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        🏢 {{ auth()->user()->bidang->nama_bidang ?? '-' }}
                                        <span class="ml-2 text-xs text-gray-400">(otomatis sesuai bidang Anda)</span>
                                    </div>
                                </div>
                                @else
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Bidang
                                    </label>
                                    <select name="bidang_id" x-model="bidang_id" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2 text-sm text-gray-800 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        <option value="">-- Pilih Bidang --</option>
                                        @foreach($bidangs as $b)
                                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 mt-6 lg:justify-end">
                            <button @click="open = false" type="button"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                Close
                            </button>
                            <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            {{-- Flash: Import Result --}}
            @if(session('import_success'))
            <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('import_success') }}</p>
                        @if(session('import_errors'))
                        <ul class="mt-2 space-y-1 text-sm text-red-600 dark:text-red-400">
                            @foreach(session('import_errors') as $err)
                                <li>&bull; {{ $err }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <x-ui.pagination :paginator="$pegawais" />
        </x-common.component-card>
    </div>

    {{-- Modal Import Excel --}}
    <x-ui.modal x-data="{ open: false }" @open-import-modal.window="open = true" :isOpen="false" class="max-w-[500px]">
        <div class="no-scrollbar relative w-full max-w-[500px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-8">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Import Data Pegawai</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unggah file Excel untuk menambah banyak pegawai sekaligus</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pegawai.import') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf


                {{-- File input --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih File Excel</label>
                    <input type="file" name="file_excel" accept=".xlsx,.xls" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/30 dark:file:text-emerald-400 dark:text-gray-400 border border-gray-300 dark:border-gray-700 rounded-lg p-1 bg-gray-50 dark:bg-gray-900" />
                    @error('file_excel')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="open = false"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </x-ui.modal>

@endsection
