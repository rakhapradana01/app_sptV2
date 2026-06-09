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
                            <th class="px-5 py-3 text-left sm:px-6">Nama Program</th>
                            <th class="px-5 py-3 text-left sm:px-6">Koefisien (OK)</th>
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
                                <td class="px-5 py-4 sm:px-6">{{ $item->nama_kegiatan }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $item->koefisien }}</td>
                                <td class="px-5 py-4 sm:px-6">Rp{{ number_format($item->pagu, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 text-green-600 dark:text-green-400 font-semibold">Rp{{ number_format($item->realisasi ?? 0, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 text-red-600 dark:text-red-400 font-semibold">Rp{{ number_format($item->pagu - ($item->realisasi ?? 0), 0, ',', '.') }}</td>
                                <td class="px-5 py-4 sm:px-6 flex gap-2">
                                    <x-ui.button size="sm" type="button" onclick="editData({{ $item->id }})">
                                        Edit
                                    </x-ui.button>
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
                class="max-w-[500px]">
                <div
                    class="relative w-full max-w-[500px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">
                    <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">Tambah Sub Kegiatan</h4>
                    <form method="POST" action="{{ route('sub-kegiatan.store') }}"
                        class="space-y-4">
                        @csrf
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Pegawai (Pemilik)</label>
                            <select name="pegawai_kasubid_id"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                                <option value="">-- Pilih --</option>
                                @foreach ($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-400">Nama Program</label>
                            <input type="text" name="nama_kegiatan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white"
                                placeholder="Masukkan nama program">
                        </div>

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg dark:text-white dark:border-gray-700">Batal</button>
                            <x-ui.button type="submit" variant="primary">Simpan</x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <x-ui.modal x-data="{ open: false }" @open-edit-modal.window="open = true" :isOpen="false"
                class="max-w-[500px]">
                <div class="relative w-full max-w-[500px] rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">

                    <h4 class="mb-4 text-2xl font-semibold text-gray-800 dark:text-white/90">
                        Edit Sub Kegiatan
                    </h4>

                    <form id="formEdit" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_id">

                        <div>
                            <label class="block mb-1 text-sm font-medium dark:text-gray-400">Pilih Pegawai (Pemilik)</label>
                            <select id="edit_pegawai_kasubid_id"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                                <option value="">-- Pilih --</option>
                                @foreach ($pegawais as $pegawai)
                                    <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium dark:text-gray-400">Nama Program</label>
                            <input type="text" id="edit_nama_kegiatan"
                                class="h-11 w-full rounded-lg border px-4 text-sm dark:bg-gray-800 dark:text-white">
                        </div>

                        <div class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="open = false" class="px-4 py-2 border rounded-lg dark:text-white dark:border-gray-700">
                                Batal
                            </button>

                            <x-ui.button type="submit" variant="primary">
                                Update
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </x-ui.modal>

            <div class="mt-4">
                <x-ui.pagination :paginator="$subKegiatan" />
            </div>
        </x-common.component-card>
    </div>
    <script>
        function editData(id) {
            fetch(`/sub-kegiatan/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('edit_id').value = data.id;
                    document.getElementById('edit_pegawai_kasubid_id').value = data.pegawai_kasubid_id;
                    document.getElementById('edit_nama_kegiatan').value = data.nama_kegiatan;

                    window.dispatchEvent(new CustomEvent('open-edit-modal'));
                });
        }

        document.getElementById('formEdit').addEventListener('submit', function(e) {
            e.preventDefault();

            let id = document.getElementById('edit_id').value;

            fetch(`/sub-kegiatan/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        pegawai_kasubid_id: document.getElementById('edit_pegawai_kasubid_id').value,
                        nama_kegiatan: document.getElementById('edit_nama_kegiatan').value,
                    })
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.success);
                    location.reload();
                });
        });
    </script>
@endsection
