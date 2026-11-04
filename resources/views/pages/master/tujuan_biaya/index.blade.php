@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Standar Harga Satuan (SHS)" />
    <div class="space-y-6">
        <x-common.component-card title="Master Standar Harga Satuan" desc="Kelola pagu biaya perjalanan dinas berdasarkan lokasi tujuan sesuai Pergub.">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-medium text-red-800 dark:text-red-300">Terjadi kesalahan:</p>
                            <ul class="mt-1 space-y-1 text-sm text-red-600 dark:text-red-400">
                                @foreach($errors->all() as $error)
                                    <li>&bull; {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Inline Form Tambah Data --}}
            <div x-data="{
                    bjbPergi: {{ old('transport_bjb_rumah_bandara', 0) }},
                    bjbPulang: {{ old('transport_bjb_bandara_rumah', 0) }},
                    tujPergi: {{ old('transport_tujuan_bandara_hotel', 0) }},
                    tujPulang: {{ old('transport_tujuan_hotel_bandara', 0) }},
                    totalBjb() {
                        return (parseFloat(this.bjbPergi) || 0) + (parseFloat(this.bjbPulang) || 0);
                    },
                    totalTujuan() {
                        return (parseFloat(this.tujPergi) || 0) + (parseFloat(this.tujPulang) || 0);
                    },
                    totalTransport() {
                        return this.totalBjb() + this.totalTujuan();
                    },
                    formatRupiah(val) {
                        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
                    }
                 }"
                 class="rounded-xl border border-blue-100 bg-blue-50/50 p-5 dark:border-blue-900/50 dark:bg-blue-900/10">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-blue-100/80 pb-3 dark:border-blue-900/40">
                    <h4 class="flex items-center gap-2 text-sm font-semibold text-blue-800 dark:text-blue-300">
                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Data SHS Baru (Sesuai Standar Pergub)
                    </h4>
                    <span class="text-xs text-blue-600 dark:text-blue-400">Komponen: UH, Tiket, Penginapan (Pagu Maksimal), dan Transportasi Rute</span>
                </div>

                <form method="POST" action="{{ route('master-shs.store') }}" class="space-y-4">
                    @csrf

                    {{-- Baris 1: Identitas & Pagu Umum --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        {{-- Lokasi Tujuan --}}
                        <div class="lg:col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Lokasi Tujuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="lokasi_tujuan" value="{{ old('lokasi_tujuan') }}"
                                   placeholder="Contoh: Jakarta, Surabaya, Luar Daerah..."
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500" required />
                        </div>

                        {{-- Tahun Anggaran --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Tahun Anggaran <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="tahun_anggaran" value="{{ old('tahun_anggaran', date('Y')) }}"
                                   min="2020" max="2099"
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                        </div>

                        {{-- Uang Harian --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Uang Harian (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="uang_harian" value="{{ old('uang_harian') }}"
                                   min="0" step="1000" placeholder="0"
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                        </div>

                        {{-- Tiket Pesawat --}}
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Tiket Pesawat PP (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="tiket_pesawat" value="{{ old('tiket_pesawat') }}"
                                   min="0" step="1000" placeholder="0"
                                   class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" required />
                        </div>
                    </div>

                    {{-- Baris 2: Penginapan (Pagu Maksimal) --}}
                    <div class="rounded-xl border border-teal-200 bg-teal-50/40 p-4 dark:border-teal-800 dark:bg-teal-900/10">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex h-2 w-2 rounded-full bg-teal-500"></span>
                            <h5 class="text-xs font-semibold uppercase tracking-wider text-teal-800 dark:text-teal-300">
                                Penginapan (Pagu Maksimal)
                            </h5>
                        </div>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="sm:col-span-2">
                                <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Pagu Maksimal Penginapan per Malam (Rp) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rp</span>
                                    <input type="number" name="penginapan" value="{{ old('penginapan', 0) }}"
                                           min="0" step="1000" placeholder="0"
                                           class="h-11 w-full rounded-lg border border-teal-300 bg-white pl-10 pr-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 dark:border-teal-700 dark:bg-gray-900 dark:text-white" required />
                                </div>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 leading-relaxed sm:pt-4">
                                Batas pagu tertinggi biaya hotel/penginapan. Jika ada perbedaan tarif per golongan (Gol IV, Gol III, dll), detail tarif dapat ditambahkan di halaman kelola detail.
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Transportasi (Kota Banjarbaru & Kota Tujuan) --}}
                    <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 dark:border-amber-800 dark:bg-amber-900/10">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-2 w-2 rounded-full bg-amber-500"></span>
                                <h5 class="text-xs font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                                    Tarif Transportasi Perjalanan Dinas
                                </h5>
                            </div>
                            <div class="rounded-lg bg-amber-100/80 px-3 py-1 font-mono text-xs font-bold text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">
                                Total Pagu Transport: <span x-text="formatRupiah(totalTransport())"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            {{-- Transport Kota Banjarbaru (Kedudukan) --}}
                            <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3.5 dark:border-amber-900/40 dark:bg-gray-900/50">
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300">1. Kota Banjarbaru (Tempat Kedudukan)</span>
                                    <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="'Subtotal: ' + formatRupiah(totalBjb())"></span>
                                </div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Rumah &rarr; Bandara (Rp)
                                        </label>
                                        <input type="number" name="transport_bjb_rumah_bandara"
                                               x-model.number="bjbPergi"
                                               min="0" step="1000" placeholder="0"
                                               class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Bandara &rarr; Rumah (Rp)
                                        </label>
                                        <input type="number" name="transport_bjb_bandara_rumah"
                                               x-model.number="bjbPulang"
                                               min="0" step="1000" placeholder="0"
                                               class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>
                                </div>
                            </div>

                            {{-- Transport Kota Tujuan --}}
                            <div class="rounded-lg border border-amber-200/80 bg-white/70 p-3.5 dark:border-amber-900/40 dark:bg-gray-900/50">
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="text-xs font-bold text-amber-900 dark:text-amber-300">2. Kota Tujuan</span>
                                    <span class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="'Subtotal: ' + formatRupiah(totalTujuan())"></span>
                                </div>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Bandara &rarr; Hotel &rarr; Acara (Rp)
                                        </label>
                                        <input type="number" name="transport_tujuan_bandara_hotel"
                                               x-model.number="tujPergi"
                                               min="0" step="1000" placeholder="0"
                                               class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                            Hotel &rarr; Bandara (Rp)
                                        </label>
                                        <input type="number" name="transport_tujuan_hotel_bandara"
                                               x-model.number="tujPulang"
                                               min="0" step="1000" placeholder="0"
                                               class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Aktif & Tombol Submit --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 pt-2">
                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800" />
                            Status Aktif (Dapat digunakan di SPJ / SPT)
                        </label>

                        <x-ui.button size="sm" type="submit">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Simpan Data SHS
                        </x-ui.button>
                    </div>
                </form>
            </div>

            {{-- Filter & Search --}}
            <div class="flex flex-wrap items-end gap-3">
                <form method="GET" action="{{ route('master-shs.index') }}" class="flex flex-wrap items-end gap-3 flex-1">
                    {{-- Search --}}
                    <div class="min-w-[200px] flex-1">
                        <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Cari Lokasi</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Ketik lokasi tujuan..."
                                   class="h-10 w-full rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                        </div>
                    </div>

                    {{-- Filter Tahun --}}
                    <div class="min-w-[140px]">
                        <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tahun Anggaran</label>
                        <select name="tahun_anggaran"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" {{ request('tahun_anggaran') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status --}}
                    <div class="min-w-[120px]">
                        <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <select name="status"
                                class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            <option value="">Semua</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <x-ui.button size="sm" type="submit" variant="outline">
                            <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </x-ui.button>
                        @if(request()->hasAny(['search', 'tahun_anggaran', 'status']))
                            <a href="{{ route('master-shs.index') }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Data SHS --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1000px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-12">No</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi Tujuan</th>
                                <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Tahun</th>
                                <th class="px-5 py-3.5 sm:px-6 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Uang Harian</th>
                                <th class="px-5 py-3.5 sm:px-6 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiket Pesawat PP</th>
                                <th class="px-5 py-3.5 sm:px-6 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penginapan</th>
                                <th class="px-5 py-3.5 sm:px-6 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transportasi</th>
                                <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-24">Status</th>
                                <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($masterShs as $shs)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500">
                                        {{ $masterShs->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 font-medium text-gray-900 dark:text-white">
                                        {{ $shs->lokasi_tujuan }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center text-sm text-gray-600 dark:text-gray-300">
                                        <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-300 dark:ring-indigo-500/30">
                                            {{ $shs->tahun_anggaran }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-right font-mono text-sm text-gray-600 dark:text-gray-300">
                                        Rp {{ number_format($shs->uang_harian, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-right font-mono text-sm text-gray-600 dark:text-gray-300">
                                        Rp {{ number_format($shs->tiket_pesawat, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-right">
                                        <div class="font-mono text-sm font-semibold text-teal-700 dark:text-teal-400">
                                            Rp {{ number_format($shs->penginapan, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 dark:text-gray-500">Pagu Maks / malam</div>
                                        @if($shs->penginapan_count > 0)
                                            <span class="inline-flex items-center gap-1 mt-1 rounded bg-teal-50 px-1.5 py-0.5 text-[10px] font-medium text-teal-700 ring-1 ring-inset ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-300">
                                                {{ $shs->penginapan_count }} tarif golongan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-right">
                                        <div class="font-mono text-sm font-semibold text-amber-700 dark:text-amber-400">
                                            Rp {{ number_format($shs->total_transport, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 flex flex-col items-end gap-0.5 mt-0.5">
                                            <span title="Rumah-Bandara + Bandara-Rumah">BJB: Rp {{ number_format($shs->total_transport_bjb, 0, ',', '.') }}</span>
                                            <span title="Bandara-Hotel-Acara + Hotel-Bandara">Tujuan: Rp {{ number_format($shs->total_transport_tujuan, 0, ',', '.') }}</span>
                                        </div>
                                        @if($shs->transportasi_count > 0)
                                            <span class="inline-flex items-center gap-1 mt-1 rounded bg-amber-50 px-1.5 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">
                                                +{{ $shs->transportasi_count }} tarif rute lain
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center">
                                        <form method="POST" action="{{ route('master-shs.toggle-status', $shs->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Klik untuk {{ $shs->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                                @if($shs->is_active)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-500/30 cursor-pointer hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-500 ring-1 ring-inset ring-gray-300/50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-600/50 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                        Nonaktif
                                                    </span>
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('master-shs.show', $shs->id) }}"
                                               class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 transition hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:ring-blue-500/30 dark:hover:bg-blue-900/50">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Detail / Edit
                                            </a>

                                            <form action="{{ route('master-shs.destroy', $shs->id) }}" method="POST"
                                                  onsubmit="return confirm('Yakin hapus data SHS {{ addslashes($shs->lokasi_tujuan) }} ({{ $shs->tahun_anggaran }})? Semua detail penginapan & transportasi juga akan terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-700/10 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:ring-red-500/30 dark:hover:bg-red-900/50">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p>Belum ada data Standar Harga Satuan.</p>
                                            <p class="text-xs">Gunakan form di atas untuk menambahkan data.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <x-ui.pagination :paginator="$masterShs" />

        </x-common.component-card>
    </div>
@endsection
