@extends('layouts.app')

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
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left">No</th>
                                    <th class="px-5 py-3 text-left">Nomor</th>
                                    <th class="px-5 py-3 text-left">Perihal</th>
                                    <th class="px-5 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="h-full">
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
                                                    <svg class="w-4 h-4 transition-transform"
                                                        :class="open ? 'rotate-180' : ''" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>

                                                <div x-show="open" x-cloak
                                                    x-transition:enter="transition ease-out duration-100"
                                                    class="absolute right-0 mt-2 w-52 origin-top-right bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg z-[60] overflow-hidden">

                                                    <div class="py-1 flex flex-col text-left">
                                                        <div
                                                            class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase border-b border-gray-100 dark:border-gray-700">
                                                            Pilih Dokumen
                                                        </div>

                                                        <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}"
                                                            target="_blank"
                                                            class="flex items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                                </path>
                                                            </svg>
                                                            Cetak NOTDIN
                                                        </a>

                                                        {{-- 2. Cetak SPT (Jika ada) --}}
                                                        @if ($nota->spt)
                                                            <a href="{{ route('nota.cetakSpt', $nota->id) }}"
                                                                target="_blank"
                                                                class="flex items-center px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                                                                Cetak SPT
                                                            </a>
                                                        @else
                                                            <button
                                                                @click="openModalSpt({{ $nota->id }}, '{{ $nota->nomor_urut }}')"
                                                                class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">
                                                                Buat SPT
                                                            </button>
                                                        @endif

                                                        {{-- 3. Cetak SPPD (Jika ada) --}}
                                                        @if ($nota->sppd)
                                                            <a href="{{ route('nota.cetakSPPD', $nota->id) }}"
                                                                target="_blank"
                                                                class="flex items-center px-4 py-2 text-sm text-purple-600 hover:bg-purple-50">
                                                                Cetak SPPD
                                                            </a>
                                                        @else
                                                            <button
                                                                @click="openModalSppd({{ $nota->id }}, '{{ $nota->nomor_urut }}', '{{ $nota->spt?->nomor_spt ?? '' }}')"
                                                                class="flex items-center px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50">
                                                                Buat SPPD
                                                            </button>
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
            </div>
        </x-common.component-card>
    </div>
    <div x-data="{ open: false, notaId: null, nomorUrut: '' }"
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
    <div x-data="{ openSppd: false, notaId: null, nomorUrut: '', nomorSpt: '' }"
        @open-modal-sppd.window="openSppd = true; notaId = $event.detail.id; nomorUrut = $event.detail.nomor; nomorSpt = $event.detail.spt">

        <div x-show="openSppd" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="openSppd = false"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">

                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Form SPPD</h3>

                    <div class="space-y-1 mb-4">
                        <p class="text-sm text-gray-500">Nota Dinas: <span x-text="nomorUrut"
                                class="font-mono text-blue-600"></span></p>
                        <p class="text-sm text-gray-500">Nomor SPT: <span x-text="nomorSpt"
                                class="font-mono text-blue-600"></span></p>
                    </div>

                    <form :action="`/sppd/store/${notaId}`" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor SPPD</label>
                            <input type="text" name="nomor_sppd" required
                                :value="'000.1.2.3/' + '    ' + '/BPKAD/2026'"
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
