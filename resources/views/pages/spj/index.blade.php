@extends('layouts.app')
@section('title', 'SPJ')

@section('content')
    <x-common.page-breadcrumb pageTitle="Surat Pertanggung Jawaban (SPJ)" />

    <div class="space-y-6">
        <x-common.component-card title="Daftar SPJ">

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
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">No</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor SPT</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Pegawai</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-20">OK</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tujuan</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3.5 sm:px-6 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spt as $item)
                                @php
                                    $notaDinas = $item->notaDinas;
                                    $pegawaisList = ($notaDinas && $notaDinas->pegawais && $notaDinas->pegawais->count()) 
                                        ? $notaDinas->pegawais 
                                        : ($item->pegawais ?? collect());
                                    $ok = $pegawaisList->count() ?: '-';
                                    $lokasi = $notaDinas->lokasi ?? $item->lokasi ?? '-';
                                    
                                    if ($notaDinas && $notaDinas->tanggal) {
                                        $tanggal = $notaDinas->tanggal;
                                    } elseif ($item->tanggal_mulai) {
                                        $tanggal = \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y');
                                    } else {
                                        $tanggal = '-';
                                    }

                                    $nomorSpt = trim($item->nomor_spt ?? '');
                                    $hasRealNomor = $item->has_real_nomor;
                                @endphp
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-500">
                                        {{ $spt->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        @if ($hasRealNomor)
                                            <span class="font-mono text-sm text-blue-700 dark:text-blue-400 font-medium">
                                                {{ $nomorSpt }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                                Nomor SPT Belum Lengkap
                                            </span>
                                            <div class="text-xs text-gray-400 font-mono mt-0.5">
                                                {{ $nomorSpt ?: '-' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-600 dark:text-gray-300">
                                        @if($pegawaisList->count())
                                            <div class="space-y-0.5">
                                                @foreach($pegawaisList as $p)
                                                    <div class="truncate max-w-[220px]">{{ $p->nama }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        {{ $ok }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $lokasi }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $tanggal }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($hasRealNomor)
                                                <a href="{{ route('spj.show', $item->id) }}"
                                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Detail
                                                </a>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-500 rounded-lg cursor-not-allowed"
                                                      title="Harap isi nomor SPT terlebih dahulu">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                    </svg>
                                                    Detail
                                                </span>
                                            @endif

                                            <button type="button"
                                                    onclick="openEditSptModal({{ $item->id }}, '{{ addslashes($nomorSpt) }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit No
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada data SPJ yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <x-ui.pagination :paginator="$spt" />
            </div>
        </x-common.component-card>
    </div>

    {{-- Modal Edit SPT --}}
    <div x-data="{ openEditSpt: false, sptId: null, nomorSpt: '' }"
        x-effect="document.body.style.overflow = openEditSpt ? 'hidden' : ''"
        @open-edit-spt-modal.window="openEditSpt = true; sptId = $event.detail.id; nomorSpt = $event.detail.nomor">

        <div x-show="openEditSpt" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="openEditSpt = false"></div>

                <div class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Input / Edit Nomor SPT</h3>

                    <form :action="`/spt/${sptId}/update-nomor`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SPT</label>
                            <input type="text" name="nomor_spt" x-model="nomorSpt" required
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white form-control">
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="openEditSpt = false"
                                class="px-4 py-2 text-gray-500 hover:text-gray-700">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.openEditSptModal = function(id, nomor) {
            window.dispatchEvent(new CustomEvent('open-edit-spt-modal', {
                detail: {
                    id: id,
                    nomor: nomor
                }
            }));
        };
    </script>
@endpush