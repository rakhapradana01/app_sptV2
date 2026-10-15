@extends('layouts.app')
@section('title', 'SPT')
@section('content')
    <x-common.page-breadcrumb pageTitle="SPT Mandiri" />

    <div class="space-y-6">
        <x-common.component-card title="Daftar SPT Mandiri">
            <div class="px-2 mb-4">
                @if (in_array(auth()->user()->role->name, ['super_admin', 'kepala_sub_bidang']))
                    <a href="{{ route('spt.create') }}">
                        <x-ui.button size="sm">+ Buat SPT Baru</x-ui.button>
                    </a>
                @endif
            </div>

            @if (session('success'))
                <div class="mx-2 mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mx-2 mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[900px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">No</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Nomor SPT</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Kegiatan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Tujuan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Pegawai</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Status Paraf</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spts as $spt)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500">
                                        {{ $spts->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="font-mono text-sm text-blue-700 dark:text-blue-400 font-medium">
                                            {{ $spt->nomor_spt }}
                                        </span>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $spt->jenis_anggaran }} – {{ $spt->tahun_anggaran ?? date('Y') }}</div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <p class="text-sm line-clamp-2 max-w-[200px]">{{ $spt->kegiatan }}</p>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm">
                                        {{ $spt->lokasi ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm">
                                        @if ($spt->tanggal_mulai)
                                            {{ \Carbon\Carbon::parse($spt->tanggal_mulai)->translatedFormat('d M Y') }}
                                            @if ($spt->tanggal_selesai && $spt->tanggal_selesai != $spt->tanggal_mulai)
                                                <span class="text-gray-400">s/d</span><br>
                                                {{ \Carbon\Carbon::parse($spt->tanggal_selesai)->translatedFormat('d M Y') }}
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($spt->pegawais->take(2) as $p)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                                    {{ $p->nama }}
                                                </span>
                                            @endforeach
                                            @if ($spt->pegawais->count() > 2)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    +{{ $spt->pegawais->count() - 2 }} lagi
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-xs">
                                        @php
                                            $st = $spt->status ?? 'draft';
                                        @endphp
                                        @if ($st === 'draft' || empty($st))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                Draft
                                            </span>
                                        @elseif ($st === 'diajukan_kabid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                                Paraf Kasubid ✓ (Menunggu Kabid)
                                            </span>
                                        @elseif ($st === 'diajukan_sekban')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                Paraf Kabid ✓ (Menunggu Sekban)
                                            </span>
                                        @elseif ($st === 'diajukan_kaban')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                Paraf Sekban ✓ (Menunggu Kaban)
                                            </span>
                                        @elseif ($st === 'disetujui_kaban')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                                Lengkap Disetujui Kaban ✓
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center">
                                        @php
                                            $uRole = auth()->user()->role->name ?? '';
                                            $st    = $spt->status ?? 'draft';
                                        @endphp
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.away="open = false"
                                                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                <span class="text-xs font-semibold">Aksi</span>
                                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                                class="absolute right-0 mt-2 w-52 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">
                                                <div class="py-1 flex flex-col text-left">

                                                    <div class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100 dark:border-gray-700">
                                                        Persetujuan Paraf
                                                    </div>

                                                    {{-- Paraf Kasubid --}}
                                                    @if (($st === 'draft' || empty($st)) && in_array($uRole, ['super_admin', 'kepala_sub_bidang']))
                                                        <form action="{{ route('spt.approveKasubid', $spt->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Paraf Kasubid
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Paraf Kabid --}}
                                                    @if ($st === 'diajukan_kabid' && in_array($uRole, ['super_admin', 'kepala_bidang']))
                                                        <form action="{{ route('spt.approveKabid', $spt->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Paraf Kabid
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- Paraf Sekban --}}
                                                    @if ($st === 'diajukan_sekban' && in_array($uRole, ['super_admin', 'sekretaris_badan']))
                                                        <form action="{{ route('spt.approveSekban', $spt->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                Paraf Sekban
                                                            </button>
                                                        </form>
                                                    @endif

                                                    {{-- ACC Kaban --}}
                                                    @if ($st === 'diajukan_kaban' && in_array($uRole, ['super_admin', 'kepala_badan']))
                                                        <form action="{{ route('spt.approveKaban', $spt->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-emerald-600 font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                ACC Kaban
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                                    {{-- Cetak --}}
                                                    <a href="{{ route('spt.cetakMandiri', $spt->id) }}" target="_blank"
                                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                        </svg>
                                                        Cetak SPT
                                                    </a>

                                                    {{-- Hapus --}}
                                                    @if (in_array($uRole, ['super_admin', 'kepala_sub_bidang']))
                                                        <form action="{{ route('spt.destroyMandiri', $spt->id) }}" method="POST"
                                                            onsubmit="return confirm('Hapus SPT ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center text-sm text-gray-400">
                                        Belum ada SPT mandiri. <a href="{{ route('spt.create') }}" class="text-blue-600 hover:underline">Buat sekarang</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <x-ui.pagination :paginator="$spts" />
        </x-common.component-card>
    </div>
@endsection