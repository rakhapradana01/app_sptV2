@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- ============================================================ --}}
        {{-- HEADER — identitas resmi, bukan sapaan kasual --}}
        {{-- ============================================================ --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-6 py-4 border-l-4 border-[#1B3A5C]">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Sistem Monitoring &middot; Pemerintah Provinsi Kalimantan Selatan
                    </p>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">
                        Surat Perintah Tugas &amp; Surat Pertanggungjawaban
                    </h1>
                </div>

                <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded px-3 py-1.5 w-fit">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Data per {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RINGKASAN ANGGARAN — kartu datar, aksen garis kiri, tanpa gradient --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Pagu Anggaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 border-l-4 border-l-[#1B3A5C] p-5">
                <div class="flex items-start justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Total Pagu Anggaran
                    </span>
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3 tabular-nums">
                    Rp {{ number_format($totalPagu, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    {{ $totalSubKegiatan }} sub-kegiatan PPTK aktif
                </p>
            </div>

            {{-- Realisasi --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 border-l-4 border-l-emerald-700 p-5">
                <div class="flex items-start justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Total Realisasi SPJ
                    </span>
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3 tabular-nums">
                    Rp {{ number_format($totalRealisasi, 0, ',', '.') }}
                </p>
                <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ $persenRealisasi }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex justify-between">
                        <span>Terealisasi</span>
                        <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $persenRealisasi }}%</span>
                    </p>
                </div>
            </div>

            {{-- Sisa Anggaran --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 border-l-4 border-l-amber-600 p-5">
                <div class="flex items-start justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Sisa Pagu Anggaran
                    </span>
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3 tabular-nums">
                    Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                    Dari total alokasi PPTK
                </p>
            </div>

            {{-- Kuota OK --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 border-l-4 border-l-[#6B4A8A] p-5">
                <div class="flex items-start justify-between">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        Kuota OK Perjalanan
                    </span>
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.66-.13-1.28-.36-1.86M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.66.13-1.28.36-1.86a5 5 0 019.28 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-3 tabular-nums">
                    {{ $okTerpakai }} <span class="text-sm font-normal text-gray-400">/ {{ $okTotal }} OK</span>
                </p>
                <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                        <div class="bg-[#6B4A8A] h-1.5 rounded-full" style="width: {{ $persenOk }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex justify-between">
                        <span>Terpakai</span>
                        <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $persenOk }}%</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- KONTEN UTAMA --}}
        {{-- ============================================================ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tabel Realisasi Sub-Kegiatan --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 dark:text-white">Alokasi &amp; Realisasi Sub-Kegiatan</h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">5 sub-kegiatan dengan pagu anggaran terbesar</p>
                    </div>
                    <a href="{{ route('sub-kegiatan.index') }}" class="text-xs font-semibold text-[#1B3A5C] dark:text-blue-400 hover:underline whitespace-nowrap">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[11px] uppercase tracking-wider text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left font-semibold px-6 py-2.5">Sub-Kegiatan</th>
                                <th class="text-right font-semibold px-6 py-2.5">Realisasi / Pagu</th>
                                <th class="text-right font-semibold px-6 py-2.5 w-40">Progres</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($subKegiatans as $sub)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    <td class="px-6 py-3.5 align-top">
                                        <span class="inline-block text-[10px] font-semibold text-[#1B3A5C] dark:text-blue-400 px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded">
                                            {{ $sub['nomor_rekening'] }}
                                        </span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-1 leading-snug">{{ $sub['nama_kegiatan'] }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">PPTK: {{ $sub['pptk_nama'] }}</p>
                                    </td>
                                    <td class="px-6 py-3.5 align-top text-right tabular-nums">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($sub['realisasi'], 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">dari Rp {{ number_format($sub['pagu'], 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-6 py-3.5 align-top">
                                        <div class="flex items-center gap-2 justify-end">
                                            <div class="w-20 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                                <div class="bg-[#1B3A5C] h-1.5 rounded-full" style="width: {{ $sub['persen'] }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 w-9 text-right">{{ $sub['persen'] }}%</span>
                                        </div>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 text-right mt-1">Sisa Rp {{ number_format($sub['sisa'], 0, ',', '.') }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-10 text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada data sub-kegiatan atau uraian pagu yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pengajuan Nota Dinas / SPT</p>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentActivities as $act)
                        @php
                            $statusClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                            $statusLabel = 'Draft';
                            if ($act->status === 'disetujui_kaban') {
                                $statusClass = 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400';
                                $statusLabel = 'Selesai';
                            } elseif (str_starts_with($act->status, 'diajukan')) {
                                $statusClass = 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                $statusLabel = 'Diajukan';
                            }
                        @endphp
                        <div class="px-6 py-3.5">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500">
                                    {{ \Carbon\Carbon::parse($act->tanggal)->translatedFormat('d M Y') }}
                                </span>
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <h4 class="text-xs font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $act->perihal }}</h4>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5 line-clamp-1">
                                Subkeg: {{ $act->subKegiatan->nama_kegiatan ?? '-' }}
                            </p>
                            <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-100 dark:border-gray-700/50">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 line-clamp-1">
                                    Tujuan: {{ $act->kepada->nama ?? '-' }}
                                </span>
                                @if($act->spt)
                                    <a href="{{ route('nota.cetakSpt', $act->id) }}" target="_blank" class="text-[10px] font-semibold text-[#1B3A5C] dark:text-blue-400 hover:underline whitespace-nowrap">
                                        Lihat SPT
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-sm text-gray-400 dark:text-gray-500">
                            Belum ada pengajuan dokumen terbaru.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection