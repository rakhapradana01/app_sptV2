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
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('spt.cetakMandiri', $spt->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                                Cetak
                                            </a>
                                            @if (in_array(auth()->user()->role->name, ['super_admin', 'kepala_sub_bidang']))
                                                <form action="{{ route('spt.destroyMandiri', $spt->id) }}" method="POST"
                                                    onsubmit="return confirm('Hapus SPT ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-rose-500 hover:bg-rose-600 rounded-lg transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400">
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