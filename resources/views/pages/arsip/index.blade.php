@extends('layouts.app')
@section('title', 'Arsip Nota Dinas')
@section('content')
    <x-common.page-breadcrumb pageTitle="Arsip Nota Dinas" />
    <div class="space-y-6 min-h-screen flex flex-col">
        <x-common.component-card title="Daftar Cetak Dokumen Terarsip" class="flex-1 flex flex-col">
            <div class="h-[calc(100vh-120px)] flex flex-col">
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] flex-1 flex flex-col">
                    <div class="max-w-full overflow-x-auto custom-scrollbar flex-1">
                        <table class="w-full min-w-[1102px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">No</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perihal</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="h-full">
                                @forelse ($notaDinas as $nota)
                                    <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                        <td class="px-5 py-4">{{ $notaDinas->firstItem() + $loop->index }}</td>
                                        <td
                                            class="px-5 py-4 sm:px-6 whitespace-nowrap font-mono text-sm text-gray-600 dark:text-gray-400">
                                            900.1 /
                                            <span
                                                style="display:inline-block; min-width:60px; border-bottom:1px solid #999; text-align:center;">
                                                {{ $nota->nomor_urut ?? '' }}
                                            </span>
                                            / BPKAD / {{ date('Y') }}
                                        </td>
                                        <td class="px-5 py-4">{{ $nota->perihal }}</td>
                                        <td class="px-5 py-4 text-center">

                                            {{-- Dropdown Opsi Persis Seperti Daftar Utama --}}
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" @click.away="open = false"
                                                    class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                    <span class="text-xs font-semibold">Cetak</span>
                                                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                                    class="absolute right-0 mt-2 w-52 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">

                                                    <div class="py-1 flex flex-col text-left">
                                                        <div
                                                            class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100 dark:border-gray-700">
                                                            Pilih Dokumen
                                                        </div>

                                                        <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}" target="_blank"
                                                            class="flex items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                                </path>
                                                            </svg>
                                                            NOTDIN
                                                        </a>

                                                        {{-- 2. Cetak SPT (Jika ada) --}}
                                                        @if ($nota->spt)
                                                            <a href="{{ route('spt.cetakMandiri', $nota->spt->id) }}"
                                                                target="_blank"
                                                                class="flex items-center px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                                    </path>
                                                                </svg>
                                                                SPT
                                                            </a>
                                                        @else
                                                            <a href="{{ route('spt.create', ['nota_id' => $nota->id]) }}"
                                                                class="flex items-center px-4 py-2 text-sm text-blue-600 font-medium hover:bg-blue-50">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                </svg>
                                                                Buat SPT
                                                            </a>
                                                        @endif

                                                        {{-- 3. Cetak SPPD (Jika ada) --}}
                                                        @php
                                                            $sppdDoc = $nota->sppd ?? $nota->spt?->sppd_efektif;
                                                        @endphp
                                                        @if ($sppdDoc)
                                                            <a href="{{ route('sppd.cetakMandiri', $sppdDoc->id) }}" target="_blank"
                                                                class="flex items-center px-4 py-2 text-sm text-purple-600 hover:bg-purple-50">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                                    </path>
                                                                </svg>
                                                                SPPD
                                                            </a>
                                                        @elseif ($nota->spt)
                                                            <a href="{{ route('sppd.create', ['spt_id' => $nota->spt->id]) }}"
                                                                class="flex items-center px-4 py-2 text-sm text-amber-600 font-medium hover:bg-amber-50">
                                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                </svg>
                                                                Buat SPPD
                                                            </a>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada dokumen Nota Dinas yang disetujui Kepala Badan (ACC Kaban).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <x-ui.pagination :paginator="$notaDinas" />
            </div>
        </x-common.component-card>

        {{-- ===== SECTION SPT MANDIRI ===== --}}
        <x-common.component-card title="Persetujuan Paraf SPT Mandiri">
            <div class="flex flex-col">

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

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <table class="w-full min-w-[860px]">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">No</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor SPT</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kegiatan</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pegawai</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Paraf</th>
                                    <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spts as $spt)
                                    @php
                                        $uRole = auth()->user()->role->name ?? '';
                                        $st = $spt->status ?? 'draft';
                                    @endphp
                                    <tr
                                        class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                                        <td class="px-5 py-4 text-sm text-gray-500">
                                            {{ $spts->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="font-mono text-sm text-blue-700 dark:text-blue-400 font-medium">
                                                {{ $spt->nomor_spt }}
                                            </span>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $spt->jenis_anggaran }} –
                                                {{ $spt->tahun_anggaran ?? date('Y') }}</div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm line-clamp-2 max-w-[200px]">{{ $spt->kegiatan }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $spt->lokasi ?? '-' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($spt->pegawais->take(2) as $p)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                                        {{ $p->nama }}
                                                    </span>
                                                @endforeach
                                                @if ($spt->pegawais->count() > 2)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                        +{{ $spt->pegawais->count() - 2 }} lagi
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-xs">
                                            @if ($st === 'draft' || empty($st))
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                                    Draft
                                                </span>
                                            @elseif ($st === 'diajukan_kabid')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                                    Paraf Kasubid ✓ (Menunggu Kabid)
                                                </span>
                                            @elseif ($st === 'diajukan_sekban')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                    Paraf Kabid ✓ (Menunggu Sekban)
                                                </span>
                                            @elseif ($st === 'diajukan_kaban')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                    Paraf Sekban ✓ (Menunggu Kaban)
                                                </span>
                                            @elseif ($st === 'disetujui_kaban')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                                    Lengkap ✓ Disetujui Kaban
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open" @click.away="open = false"
                                                    class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                    <span class="text-xs font-semibold">Aksi</span>
                                                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                                                    class="absolute right-0 mt-2 w-52 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">
                                                    <div class="py-1 flex flex-col text-left">

                                                        <div
                                                            class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100 dark:border-gray-700">
                                                            Persetujuan Paraf
                                                        </div>

                                                        {{-- Paraf Kasubid --}}
                                                        @if (($st === 'draft' || empty($st)) && in_array($uRole, ['super_admin', 'kepala_sub_bidang']))
                                                            <form action="{{ route('spt.approveKasubid', $spt->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Paraf Kasubid
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Paraf Kabid --}}
                                                        @if ($st === 'diajukan_kabid' && in_array($uRole, ['super_admin', 'kepala_bidang']))
                                                            <form action="{{ route('spt.approveKabid', $spt->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Paraf Kabid
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- Paraf Sekban --}}
                                                        @if ($st === 'diajukan_sekban' && in_array($uRole, ['super_admin', 'sekretaris_badan']))
                                                            <form action="{{ route('spt.approveSekban', $spt->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Paraf Sekban
                                                                </button>
                                                            </form>
                                                        @endif

                                                        {{-- ACC Kaban --}}
                                                        @if ($st === 'diajukan_kaban' && in_array($uRole, ['super_admin', 'kepala_badan']))
                                                            <form action="{{ route('spt.approveKaban', $spt->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="flex items-center w-full px-4 py-2 text-sm text-emerald-600 font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    ACC Kaban
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                                                        {{-- Cetak SPT --}}
                                                        <a href="{{ route('spt.cetakMandiri', $spt->id) }}" target="_blank"
                                                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                            </svg>
                                                            Cetak SPT
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400">
                                            Belum ada SPT mandiri.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">
                    <x-ui.pagination :paginator="$spts" />
                </div>
            </div>
        </x-common.component-card>

    </div>
    <div x-data="{ open: false, notaId: null, nomor_ur: '' }" x-effect="document.body.style.overflow = open ? 'hidden' : ''"
        @open-modal-spt.window="open = true; notaId = $event.detail.id; nomor_ur = $event.detail.nomor">

        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Form SPT</h3>
                    <p class="text-sm text-gray-500 mb-4">Nota Dinas: <span x-text="nomor_ur"
                            class="font-mono text-blue-600"></span></p>

                    <form :action="`/spt/store/${notaId}`" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SPT</label>
                            <input type="text" name="nomor_spt" required placeholder="090/001/SPT/2026"
                                value="800.1.11.1/       /BPKAD/2026"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                                <input type="number" name="tahun_anggaran" value="{{ date('Y') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sumber
                                    Anggaran</label>
                                <select name="jenis_anggaran"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    <option value="DPA">DPA (Murni)</option>
                                    <option value="DPPA">DPPA (Perubahan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="open = false"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">Batal</button>
                            <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div x-data="{ openSppd: false, notaId: null, nomor_ur: '', nomorSpt: '' }"
        x-effect="document.body.style.overflow = openSppd ? 'hidden' : ''"
        @open-modal-sppd.window="openSppd = true; notaId = $event.detail.id; nomor_ur = $event.detail.nomor; nomorSpt = $event.detail.spt">

        <div x-show="openSppd" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="openSppd = false"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">

                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Form SPPD</h3>

                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-500">Nota Dinas: <span x-text="nomor_ur"
                                class="font-mono text-blue-600"></span></p>
                        <p class="text-sm text-gray-500">Nomor SPT: <span x-text="nomorSpt"
                                class="font-mono text-blue-600"></span></p>
                    </div>

                    <form :action="`/sppd/store/${notaId}`" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SPPD</label>
                            <input type="text" name="nomor_sppd" required :value="'800.1.11.1/' + '    ' + '/BPKAD/2026'"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alat Angkutan</label>
                            <select name="alat_angkutan"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="mobil">Mobil</option>
                                <option value="pesawat dan mobil">Pesawat dan Mobil</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Berangkat</label>
                                <input type="text" name="tempat_berangkat" value="Banjarbaru" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan</label>
                                <input type="text" name="tempat_tujuan" placeholder="Contoh: Jakarta" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tujuan
                                    Ke-2</label>
                                <input type="text" name="tempat_tujuan_2" placeholder="Contoh: Jakarta"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                Pembuatan</label>
                            <input type="date" name="tanggal_sppd" value="{{ date('Y-m-d') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="openSppd = false"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">Batal</button>
                            <x-ui.button type="submit" variant="primary">Simpan & Cetak</x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalSpt(id, nomor) {
            window.dispatchEvent(new CustomEvent('open-modal-spt', {
                detail: {
                    id: id,
                    nomor: nomor
                }
            }));
        }

        function openModalSppd(id, nomor, spt) {
            window.dispatchEvent(new CustomEvent('open-modal-sppd', {
                detail: {
                    id: id,
                    nomor: nomor,
                    spt: spt
                }
            }));
        }
    </script>
@endsection