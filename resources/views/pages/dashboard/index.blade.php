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
        {{-- NOTIFIKASI PARAF PENDING --}}
        {{-- ============================================================ --}}
        @if($totalPendingNotif > 0 || $myPendingNotaDinas->count() > 0)
        <div class="space-y-3">

            {{-- Panel: Menunggu aksi user ini --}}
            @if($totalPendingNotif > 0)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/>
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-amber-800 dark:text-amber-300">
                            {{ $totalPendingNotif }} Dokumen Menunggu Paraf Anda
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    {{-- Nota Dinas pending --}}
                    @foreach($pendingNotaDinas as $nota)
                    @php
                        $notaStatusLabel = match($nota->status) {
                            'draft'           => ['label' => 'Menunggu Paraf Kasubid', 'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                            'diajukan_kabid'  => ['label' => 'Menunggu Paraf Kabid',   'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'],
                            'diajukan_sekban' => ['label' => 'Menunggu Paraf Sekban',  'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300'],
                            'diajukan_kaban'  => ['label' => 'Menunggu ACC Kaban',     'color' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'],
                            default           => ['label' => $nota->status,             'color' => 'bg-gray-100 text-gray-700'],
                        };
                    @endphp
                    <a href="{{ route('nota-dinas.index') }}" class="flex items-start gap-3 bg-white dark:bg-gray-800 rounded-lg border border-amber-100 dark:border-amber-800/30 px-3 py-2.5 hover:border-amber-300 dark:hover:border-amber-600 transition group">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white line-clamp-1">{{ $nota->perihal }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">Nota Dinas &middot; {{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d M Y') }}</p>
                            <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $notaStatusLabel['color'] }}">
                                {{ $notaStatusLabel['label'] }}
                            </span>
                        </div>
                        <svg class="w-3 h-3 text-gray-300 group-hover:text-amber-400 transition flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach

                    {{-- SPT pending --}}
                    @foreach($pendingSptsNotif as $sptNotif)
                    @php
                        $sptStatusLabel = match($sptNotif->status) {
                            'draft'           => ['label' => 'Menunggu Paraf Kasubid', 'color' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                            'diajukan_kabid'  => ['label' => 'Menunggu Paraf Kabid',   'color' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300'],
                            'diajukan_sekban' => ['label' => 'Menunggu Paraf Sekban',  'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300'],
                            'diajukan_kaban'  => ['label' => 'Menunggu ACC Kaban',     'color' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300'],
                            default           => ['label' => $sptNotif->status,         'color' => 'bg-gray-100 text-gray-700'],
                        };
                    @endphp
                    <a href="{{ route('arsip') }}" class="flex items-start gap-3 bg-white dark:bg-gray-800 rounded-lg border border-amber-100 dark:border-amber-800/30 px-3 py-2.5 hover:border-amber-300 dark:hover:border-amber-600 transition group">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white line-clamp-1">{{ $sptNotif->nomor_spt ?? 'SPT (Draft)' }}</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">SPT &middot; {{ $sptNotif->kegiatan_efektif ?? $sptNotif->kegiatan ?? '-' }}</p>
                            <span class="inline-block mt-1 px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $sptStatusLabel['color'] }}">
                                {{ $sptStatusLabel['label'] }}
                            </span>
                        </div>
                        <svg class="w-3 h-3 text-gray-300 group-hover:text-amber-400 transition flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Panel: Dokumen milik user sedang diproses --}}
            @if($myPendingNotaDinas->count() > 0)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="text-sm font-bold text-blue-800 dark:text-blue-300">Dokumen Anda Sedang Diproses</span>
                </div>

                <div class="space-y-2">
                    @foreach($myPendingNotaDinas as $nota)
                    @php
                        $trackSteps = [
                            'diajukan_kabid'  => ['step' => 1, 'label' => 'Di Kabid',   'color' => 'text-amber-600'],
                            'revisi_kabid'    => ['step' => 1, 'label' => 'Revisi Kabid','color' => 'text-rose-600'],
                            'diajukan_sekban' => ['step' => 2, 'label' => 'Di Sekban',  'color' => 'text-indigo-600'],
                            'revisi_sekban'   => ['step' => 2, 'label' => 'Revisi Sekban','color' => 'text-rose-600'],
                            'diajukan_kaban'  => ['step' => 3, 'label' => 'Di Kaban',   'color' => 'text-purple-600'],
                            'revisi_kaban'    => ['step' => 3, 'label' => 'Revisi Kaban','color' => 'text-rose-600'],
                        ];
                        $track = $trackSteps[$nota->status] ?? ['step' => 0, 'label' => $nota->status, 'color' => 'text-gray-500'];
                    @endphp
                    <a href="{{ route('nota-dinas.index') }}" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg border border-blue-100 dark:border-blue-800/30 px-3 py-2.5 hover:border-blue-300 transition group">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white line-clamp-1">{{ $nota->perihal }}</p>
                            <div class="flex items-center gap-3 mt-1.5">
                                {{-- Progress steps --}}
                                @foreach(['Kasubid','Kabid','Sekban','Kaban'] as $i => $step)
                                <div class="flex items-center gap-1">
                                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[8px] font-bold
                                        {{ $track['step'] > $i ? 'bg-emerald-500 text-white' : ($track['step'] === $i ? 'bg-amber-400 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400') }}">
                                        {{ $track['step'] > $i ? '✓' : ($i + 1) }}
                                    </span>
                                    <span class="text-[9px] {{ $track['step'] > $i ? 'text-emerald-600 dark:text-emerald-400' : ($track['step'] === $i ? 'text-amber-600' : 'text-gray-400') }}">{{ $step }}</span>
                                </div>
                                @if($i < 3)
                                <div class="w-4 h-px {{ $track['step'] > $i ? 'bg-emerald-400' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <svg class="w-3 h-3 text-gray-300 group-hover:text-blue-400 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        @endif

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
                                    <a href="{{ route('arsip') }}" class="text-[10px] font-semibold text-[#1B3A5C] dark:text-blue-400 hover:underline whitespace-nowrap">
                                        Lihat di Arsip
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