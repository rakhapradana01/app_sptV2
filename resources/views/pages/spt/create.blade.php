@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Buat SPT" />

    <div class="space-y-6">
        <x-common.component-card title="Form SPT">

            @if ($errors->any())
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('spt.storeMandiri') }}" class="space-y-6">
                @csrf

                {{-- INFORMASI SPT --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Informasi SPT</h4>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nomor SPT <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="nomor_spt" required
                                value="{{ old('nomor_spt', '800.1.11.1/      /BPKAD/' . date('Y')) }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Sub Kegiatan
                            </label>
                            <select name="sub_kegiatan_id"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Sub Kegiatan (opsional) --</option>
                                @foreach ($subKegiatans as $sub)
                                    <option value="{{ $sub->id }}" {{ old('sub_kegiatan_id') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->nomor_rekening }} - {{ $sub->nama_kegiatan }} 
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Sumber Anggaran <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_anggaran" required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="DPA" {{ old('jenis_anggaran') == 'DPA' ? 'selected' : '' }}>DPA (Murni)</option>
                                <option value="DPPA" {{ old('jenis_anggaran') == 'DPPA' ? 'selected' : '' }}>DPPA (Perubahan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tahun Anggaran <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="tahun_anggaran" required min="2020" max="2099"
                                value="{{ old('tahun_anggaran', date('Y')) }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- DETAIL TUGAS --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Detail Tugas</h4>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Uraian Kegiatan / Tugas <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="kegiatan" required rows="3"
                            placeholder="Contoh: Menghadiri Rapat Koordinasi Penganggaran Daerah..."
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ old('kegiatan') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Lokasi / Kota Tujuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi" required
                            value="{{ old('lokasi') }}"
                            placeholder="Contoh: Jakarta"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" required
                                value="{{ old('tanggal_mulai') }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal Selesai <span class="text-gray-400 font-normal">(jika lebih dari 1 hari)</span>
                            </label>
                            <input type="date" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai') }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- DAFTAR PEGAWAI --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-4">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                        Pegawai yang Diperintah <span class="text-rose-500">*</span>
                    </h4>

                    <div x-data="{ search: '' }">
                        <input type="text" x-model="search" placeholder="Cari nama pegawai..."
                            class="w-full mb-3 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">

                        <div class="max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            @foreach ($pegawais as $pegawai)
                                <label x-show="search === '' || '{{ strtolower($pegawai->nama) }}'.includes(search.toLowerCase())"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer border-b border-gray-100 dark:border-gray-700/50 last:border-0 transition">
                                    <input type="checkbox" name="pegawai_ids[]" value="{{ $pegawai->id }}"
                                        {{ in_array($pegawai->id, old('pegawai_ids', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-blue-600 rounded">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $pegawai->nama }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $pegawai->jabatan }} — {{ $pegawai->pangkat }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @error('pegawai_ids')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- AKSI --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('spt.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </a>
                    <x-ui.button type="submit" variant="primary">
                        Simpan SPT
                    </x-ui.button>
                </div>
            </form>

        </x-common.component-card>
    </div>
@endsection
