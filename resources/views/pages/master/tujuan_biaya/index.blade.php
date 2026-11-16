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

            {{-- Tombol Tambah Data SHS --}}
            <div class="flex justify-end">
                <a href="{{ route('master-shs.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data SHS
                </a>
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
