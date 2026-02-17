@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Daftar Sub Kegiatan">
            <x-ui.button size="sm" @click="$dispatch('open-profile-create-modal')">Tambah</x-ui.button>

            <div class="overflow-x-auto mt-4">
                <table class="w-full min-w-[600px] border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6">No</th>
                            <th class="px-5 py-3 text-left sm:px-6">PPTK</th>
                            <th class="px-5 py-3 text-left sm:px-6">Nomor Rekening</th>
                            <th class="px-5 py-3 text-left sm:px-6">Nama Program</th>
                            <th class="px-5 py-3 text-left sm:px-6">Koefisien</th>
                            <th class="px-5 py-3 text-left sm:px-6">Harga Satuan</th>
                            <th class="px-5 py-3 text-left sm:px-6">Pagu</th>
                            <th class="px-5 py-3 text-left sm:px-6">Realisasi</th>
                            <th class="px-5 py-3 text-left sm:px-6">Sisa</th>
                            <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($subKegiatan as $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800 dark:text-white">
                                <td class="px-5 py-4 sm:px-6">{{ $subKegiatan->firstItem() + $loop->index }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->pegawai->nama ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->nomor_rekening }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->nama_kegiatan }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->koefisien }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->harga_satuan }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->pagu }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->realisasi ?? '-' }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->pagu - ($item->realisasi ?? 0) }}</td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <x-ui.button variant="yellow" size="sm">Edit</x-ui.button>
                                    <form action="#" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button variant="red" size="sm">Hapus</x-ui.button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-ui.modal x-data="{ open: false }" @open-profile-create-modal.window="open = true" :isOpen="false"
                class="max-w-[700px]">
                <div
                    class="relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Tambah Sub Kegiatan</h4>
                    <form method="POST" action="{{ route('sub-kegiatan.store') }}"
                        class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Pegawai</label>
                            <select name="pegawai_kasubid_id"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                                <option value="">-- Pilih --</option>
                                @foreach ($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Program</label>
                            <input type="text" name="nama_kegiatan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Harga Satuan</label>
                            <input type="number" name="harga_satuan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Koefisien</label>
                            <input type="number" name="koefisien"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Pagu</label>
                            <input type="number" name="pagu"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div class="lg:col-span-2 flex justify-end gap-2 mt-2">
                            <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg">Batal</button>
                            <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <div class="mt-4">
                <x-ui.pagination :paginator="$subKegiatan" />
            </div>
        </x-common.component-card>
    </div>
@endsection
