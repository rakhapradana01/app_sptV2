@extends('layouts.app')

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
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left sm:px-6">No</th>
                                <th class="px-5 py-3 text-left sm:px-6">Nomor</th>
                                <th class="px-5 py-3 text-left sm:px-6">Perihal</th>
                                <th class="px-5 py-3 text-left sm:px-6">Tujuan</th>
                                <th class="px-5 py-3 text-left sm:px-6">Status</th>
                                <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notaDinas as $nota)
                                <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $notaDinas->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->nomor_urut }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->perihal }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->lokasi }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $nota->status }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{-- @if (auth()->user()->role->name == 'user' && $nota->isFinal())
                                            Tombol Cetak
                                        @endif --}}

                                        {{-- @if ($nota->status == 'disetujui_kabid')
                                            <a href="{{ route('nota.cetak', $nota->id) }}">Cetak Nota</a>
                                            <a href="{{ route('spt.cetak', $nota->id) }}">Cetak SPT</a>
                                        @endif --}}

                                        @if (optional(auth()->user()->role)->name === 'kepala_sub_bidang' &&
                                                $nota->status === \App\Models\NotaDinas::DISETUJUI_KABID)
                                            <div class="flex flex-col gap-2 mt-2">

                                                <a href="{{ route('nota.cetakNotaDinas', $nota->id) }}" target="_blank"
                                                    class="px-4 py-2 bg-blue-600 text-white rounded text-center">
                                                    NOTDIN
                                                </a>

                                                @if ($nota->spt)
                                                    {{-- Jika SPT sudah ada, tampilkan tombol CETAK --}}
                                                    <a href="{{ route('nota.cetakSpt', $nota->id) }}" target="_blank"
                                                        class="px-4 py-2 bg-green-600 text-white rounded text-center text-xs font-bold hover:bg-green-700 transition">
                                                        SPT
                                                    </a>
                                                @else
                                                    {{-- Jika SPT belum ada, tampilkan tombol BUAT --}}
                                                    <button
                                                        @click="openModalSpt({{ $nota->id }}, '{{ $nota->nomor_urut }}')"
                                                        class="px-4 py-2 bg-gray-500 text-white rounded text-center text-xs font-bold hover:bg-gray-600 transition">
                                                        BUAT SPT
                                                    </button>
                                                @endif

                                                @if ($nota->sppd)
                                                    <a href="{{ route('nota.cetakSPPD', $nota->id) }}" target="_blank"
                                                        class="px-4 py-2 bg-purple-600 text-white rounded text-center text-xs font-bold hover:bg-purple-700 transition">
                                                        SPPD
                                                    </a>
                                                @else
                                                    <button
                                                        @click="openModalSppd({{ $nota->id }}, {{ json_encode($nota->nomor_urut) }}, {{ json_encode($nota->spt ? $nota->spt->nomor_spt : '') }})"
                                                        class="px-4 py-2 bg-yellow-500 text-white rounded text-center text-xs font-bold hover:bg-yellow-600 transition">
                                                        BUAT SPPD
                                                    </button>
                                                @endif

                                            </div>
                                        @endif

                                        @if (auth()->user()->role->name == 'kepala_sub_bidang' && $nota->status == 'draft')
                                            <form action="{{ route('nota-dinas.approve-kasubid', $nota->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button size="sm" variant="success">Kirim
                                                    Kabid</x-ui.button>
                                            </form>
                                        @endif


                                        @if (auth()->user()->role->name == 'kepala_bidang' && $nota->status == 'diajukan_kabid')
                                            <a href="{{ route('nota-dinas.preview', $nota->id) }}">
                                                <x-ui.button size="sm" variant="primary">
                                                    Lihat & Approve
                                                </x-ui.button>
                                            </a>
                                        @endif

                                        @if (auth()->user()->role->name == 'super_admin')
                                            <div class="flex gap-2">

                                                @if ($nota->status == 'draft')
                                                    <form action="{{ route('nota-dinas.kirim-kasubid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm">Kirim</x-ui.button>
                                                    </form>
                                                @endif

                                                @if ($nota->status == 'diajukan_kasubid')
                                                    <form action="{{ route('nota-dinas.approve-kasubid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm" variant="success">Approve
                                                            Kasubid</x-ui.button>
                                                    </form>
                                                @endif

                                                @if ($nota->status == 'disetujui_kasubid')
                                                    <form action="{{ route('nota-dinas.approve-kabid', $nota->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <x-ui.button size="sm" variant="primary">Approve
                                                            Kabid</x-ui.button>
                                                    </form>
                                                @endif

                                            </div>
                                        @endif

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

    <div x-data="{ open: false, notaId: null, nomorUrut: '' }"
        @open-modal-spt.window="open = true; notaId = $event.detail.id; nomorUrut = $event.detail.nomor">

        {{-- Overlay --}}
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>

                {{-- Modal Content --}}
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
                            <x-ui.button type="submit" variant="primary">Simpan & Generate</x-ui.button>
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
