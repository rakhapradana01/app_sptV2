@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Arsip Nota Dinas" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Cetak Dokumen Terarsip">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left">No</th>
                                <th class="px-5 py-3 text-left">Nomor</th>
                                <th class="px-5 py-3 text-left">Perihal</th>
                                <th class="px-5 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notaDinas as $nota)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                    <td class="px-5 py-4">{{ $notaDinas->firstItem() + $loop->index }}</td>
                                    <td class="px-5 py-4 font-mono text-sm">{{ $nota->nomor_urut }}</td>
                                    <td class="px-5 py-4">{{ $nota->perihal }}</td>
                                    <td class="px-5 py-4 text-center">
                                        
                                        {{-- Dropdown Opsi Persis Seperti Daftar Utama --}}
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.away="open = false"
                                                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                <span class="text-xs font-semibold">Cetak</span>
                                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                class="absolute right-0 mt-2 w-52 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">
                                                
                                                <div class="py-1 flex flex-col text-left">
                                                    <div class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100 dark:border-gray-700">
                                                        Pilih Dokumen
                                                    </div>

                                                    {{-- 1. Cetak NOTDIN (Selalu ada) --}}
                                                    <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}" target="_blank"
                                                        class="flex items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                        </svg>
                                                        Cetak NOTDIN
                                                    </a>

                                                    {{-- 2. Cetak SPT (Jika ada) --}}
                                                    @if ($nota->spt)
                                                        <a href="{{ route('nota.cetakSpt', $nota->id) }}" target="_blank"
                                                            class="flex items-center px-4 py-2 text-sm text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            Cetak SPT
                                                        </a>
                                                    @endif

                                                    {{-- 3. Cetak SPPD (Jika ada) --}}
                                                    @if ($nota->sppd)
                                                        <a href="{{ route('nota.cetakSPPD', $nota->id) }}" target="_blank"
                                                            class="flex items-center px-4 py-2 text-sm text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                            </svg>
                                                            Cetak SPPD
                                                        </a>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <x-ui.pagination :paginator="$notaDinas" />
        </x-common.component-card>
    </div>
@endsection