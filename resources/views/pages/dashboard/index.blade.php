@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <!-- Header Section with Greeting -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Selamat Datang di Sistem Monitoring SPT & SPJ
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                </p>
            </div>
            

            <div class="flex items-center gap-2 text-xs font-semibold px-3 py-1.5 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg w-fit">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Sistem Berjalan Aktif
            </div>
        </div>

        <!-- 4 Premium Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Total Pagu Anggaran -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-indigo-500/25 transition duration-300 transform hover:-translate-y-1">
                <div class="absolute -right-4 -bottom-4 opacity-15">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H7c0-2.76 2.24-5 5-5s5 2.24 5 5c0 1.04-.42 1.99-1.07 2.75z"/>
                    </svg>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-100">Total Pagu Anggaran</span>
                    <span class="p-2 bg-white/10 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
                <h3 class="text-2xl font-black">Rp {{ number_format($totalPagu, 0, ',', '.') }}</h3>
                <p class="text-xs text-indigo-100 mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Dari {{ $totalSubKegiatan }} Sub-Kegiatan PPTK
                </p>
            </div>

            <!-- Card 2: Total Realisasi -->
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-emerald-500/25 transition duration-300 transform hover:-translate-y-1">
                <div class="absolute -right-4 -bottom-4 opacity-15">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Total Realisasi SPJ</span>
                    <span class="p-2 bg-white/10 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </span>
                </div>
                <h3 class="text-2xl font-black">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</h3>
                <div class="mt-2">
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: {{ $persenRealisasi }}%"></div>
                    </div>
                    <p class="text-xs text-emerald-100 mt-1.5 flex justify-between items-center">
                        <span>Sudah terealisasi</span>
                        <span class="font-bold">{{ $persenRealisasi }}%</span>
                    </p>
                </div>
            </div>

            <!-- Card 3: Sisa Pagu Anggaran -->
            <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-amber-500/25 transition duration-300 transform hover:-translate-y-1">
                <div class="absolute -right-4 -bottom-4 opacity-15">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-100">Sisa Pagu Anggaran</span>
                    <span class="p-2 bg-white/10 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </span>
                </div>
                <h3 class="text-2xl font-black">Rp {{ number_format($sisaAnggaran, 0, ',', '.') }}</h3>
                <p class="text-xs text-amber-100 mt-2 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Dari total alokasi PPTK
                </p>
            </div>

            <!-- Card 4: Target Kuota OK -->
            <div class="relative overflow-hidden bg-gradient-to-br from-violet-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-violet-500/25 transition duration-300 transform hover:-translate-y-1">
                <div class="absolute -right-4 -bottom-4 opacity-15">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                </div>
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-semibold uppercase tracking-wider text-violet-100">Kuota OK Perjalanan</span>
                    <span class="p-2 bg-white/10 rounded-lg text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </span>
                </div>
                <h3 class="text-2xl font-black">{{ $okTerpakai }} <span class="text-sm font-normal text-violet-100">/ {{ $okTotal }} OK</span></h3>
                <div class="mt-2">
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: {{ $persenOk }}%"></div>
                    </div>
                    <p class="text-xs text-violet-100 mt-1.5 flex justify-between items-center">
                        <span>Target Orang Kali Terpakai</span>
                        <span class="font-bold">{{ $persenOk }}%</span>
                    </p>
                </div>
            </div>
        </div>
    

        <!-- 2 Column Layout: Sub Kegiatan progress & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Sub Kegiatan Budget Breakdown (2/3 width) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Alokasi & Realisasi Sub-Kegiatan</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Top 5 Sub-Kegiatan berdasarkan pagu anggaran</p>
                    </div>
                    <a href="{{ route('sub-kegiatan.index') }}" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 font-bold hover:underline">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="space-y-5">
                    @forelse($subKegiatans as $sub)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl hover:bg-gray-100/50 dark:hover:bg-gray-700/30 transition duration-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 rounded">
                                        {{ $sub['nomor_rekening'] }}
                                    </span>
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mt-1.5">{{ $sub['nama_kegiatan'] }}</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">PPTK: <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $sub['pptk_nama'] }}</span></p>
                                </div>
                                <div class="text-right sm:self-start">
                                    <span class="text-sm font-extrabold text-blue-700 dark:text-blue-400">Rp {{ number_format($sub['realisasi'], 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 block">dari Rp {{ number_format($sub['pagu'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $sub['persen'] }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-gray-400 mt-1">
                                    <span>Sisa Anggaran: Rp {{ number_format($sub['sisa'], 0, ',', '.') }}</span>
                                    <span class="font-bold text-gray-600 dark:text-gray-300">{{ $sub['persen'] }}% Terpakai</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500 italic">Belum ada data sub-kegiatan atau uraian pagu yang terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Side: Recent Travel Documents / Activities (1/3 width) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Dokumen Nota Dinas / SPT yang diajukan</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($recentActivities as $act)
                        @php
                            $statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            $statusLabel = 'Draft';
                            if($act->status === 'disetujui_kaban') {
                                $statusClass = 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 border border-green-200/50';
                                $statusLabel = 'Selesai';
                            } elseif(str_starts_with($act->status, 'diajukan')) {
                                $statusClass = 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200/50';
                                $statusLabel = 'Diajukan';
                            }
                        @endphp
                        <div class="p-3 bg-gray-50/50 dark:bg-gray-800/40 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 transition duration-200 border border-gray-100/50 dark:border-gray-700/50 flex flex-col gap-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($act->tanggal)->translatedFormat('d M Y') }}
                                </span>
                                <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md uppercase tracking-wider {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $act->perihal }}</h4>
                                <p class="text-[10px] text-gray-400 mt-0.5 line-clamp-1">Subkeg: {{ $act->subKegiatan->nama_kegiatan ?? '-' }}</p>
                            </div>
                            <div class="flex justify-between items-center pt-1 border-t border-gray-100 dark:border-gray-700/50 mt-1">
                                <span class="text-[9px] text-gray-400 font-medium line-clamp-1">Tujuan: {{ $act->kepada->nama ?? '-' }}</span>
                                @if($act->spt)
                                    <a href="{{ route('nota.cetakSpt', $act->id) }}" target="_blank" class="text-[9px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        Lihat SPT
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500 italic">Belum ada pengajuan dokumen terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
