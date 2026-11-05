@extends('layouts.app')
@section('title', 'Nota Dinas')
@section('content')
    <x-common.page-breadcrumb pageTitle="Nota Dinas" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Nota Dinas">

            <div class="px-2">
                @if (in_array(auth()->user()->role->name, ['super_admin', 'kepala_sub_bidang']))
                    <a href="{{ route('nota-dinas.create') }}">
                        <x-ui.button size="sm">Tambah</x-ui.button>
                    </a>
                @endif
            </div>
            <div
                class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[1102px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800 bg-gray-50/75 dark:bg-gray-900/50">
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">No</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perihal</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tujuan</th>
                                <th class="px-5 py-3.5 sm:px-6 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notaDinas as $nota)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition">
                                    <td class="px-5 py-4 sm:px-6 ">
                                        {{ $notaDinas->firstItem() + $loop->index }}
                                    </td>
                                    <td
                                        class="px-5 py-4 sm:px-6 whitespace-nowrap font-mono text-sm text-gray-600 dark:text-gray-400">
                                        900.1 /
                                        <span
                                            style="display:inline-block; min-width:60px; border-bottom:1px solid #999; text-align:center;">
                                            {{ $nota->nomor_urut ?? '' }}
                                        </span>
                                        / BPKAD / {{ date('Y') }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->perihal }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->lokasi }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 text-center">
                                        <div x-data="{ open: false }" class="relative inline-block text-left">
                                            <button @click="open = !open" @click.away="open = false"
                                                class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                                <span class="text-xs font-semibold">Opsi</span>
                                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <div x-show="open" x-cloak
                                                x-transition:enter="transition ease-out duration-100"
                                                class="absolute right-0 mt-2 w-56 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">

                                                <div class="py-1 flex flex-col text-left">
                                                    @php
                                                        $role = auth()->user()->role->name;
                                                    @endphp

                                                    <a href="{{ route('nota-dinas.preview', $nota->id) }}"
                                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                        Lihat Dokumen
                                                    </a>

                                                    <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}" target="_blank"
                                                        class="flex items-center px-4 py-2 text-sm text-emerald-600 font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                        Cetak PDF
                                                    </a>

                                                    @if (!$nota->spt)
                                                        <a href="{{ route('spt.create', ['nota_id' => $nota->id]) }}"
                                                            class="flex items-center px-4 py-2 text-sm text-blue-600 font-medium hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Buat SPT
                                                        </a>
                                                    @elseif (!$nota->sppd)
                                                        <a href="{{ route('sppd.create', ['spt_id' => $nota->spt->id]) }}"
                                                            class="flex items-center px-4 py-2 text-sm text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                            Buat SPPD
                                                        </a>
                                                    @endif

                                                    @if (in_array($role, ['kepala_sub_bidang', 'super_admin', 'admin', 'kepala_bidang']))
                                                        <a href="{{ route('nota-dinas.edit', $nota->id) }}"
                                                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                            Edit Data
                                                        </a>

                                                        <form action="{{ route('nota-dinas.destroy', $nota->id) }}"
                                                            method="POST" onsubmit="return confirm('Hapus data ini?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">Hapus</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                        Belum ada data nota dinas.
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
    </div>

    <div x-data="{ open: false, notaId: null, nomorUrut: '' }"
        x-effect="document.body.style.overflow = open ? 'hidden' : ''"
        @open-modal-spt.window="open = true; notaId = $event.detail.id; nomorUrut = $event.detail.nomor">

        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Form SPT</h3>
                    <p class="text-sm text-gray-500 mb-4">Nota Dinas: <span x-text="nomorUrut"
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
    <div x-data="{ openSppd: false, notaId: null, nomor_urut: '', nomorSpt: '' }"
        x-effect="document.body.style.overflow = openSppd ? 'hidden' : ''"
        @open-modal-sppd.window="openSppd = true; notaId = $event.detail.id; nomor_urut = $event.detail.nomor; nomorSpt = $event.detail.spt">

        <div x-show="openSppd" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="openSppd = false"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">

                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Form SPPD</h3>

                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-500">Nota Dinas: <span x-text="nomor_urut"
                                class="font-mono text-blue-600"></span></p>
                        <p class="text-sm text-gray-500">Nomor SPT: <span x-text="nomorSpt"
                                class="font-mono text-blue-600"></span></p>
                    </div>

                    <form :action="`/sppd/store/${notaId}`" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SPPD</label>
                            <input type="text" name="nomor_sppd" required
                                :value="'800.1.11.1/' + '    ' + '/BPKAD/2026'"
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
