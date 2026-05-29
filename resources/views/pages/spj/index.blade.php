@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Surat Pertanggung Jawaban (SPJ)" />

    <div class="space-y-6">
        <x-common.component-card title="Daftar SPJ">
            <div class="overflow-x-auto">
                <table id="spj-table" class="w-full min-w-[800px] border-collapse">
                    <thead class="bg-light">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">No</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Nomor SPT</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Nama Pegawai</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">OK</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Tujuan</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700">Tanggal</th>
                            <th class="px-5 py-3 text-left sm:px-6 font-semibold text-gray-700 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" class="py-10 text-center">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>

    {{-- Modal Edit SPT --}}
    <div x-data="{ openEditSpt: false, sptId: null, nomorSpt: '' }"
        @open-edit-spt-modal.window="openEditSpt = true; sptId = $event.detail.id; nomorSpt = $event.detail.nomor">

        <div x-show="openEditSpt" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="openEditSpt = false"></div>

                <div
                    class="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl transform transition-all max-w-lg w-full z-50 p-6">
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
        $(function () {
            const $tableBody = $('#spj-table tbody');

            function fetchSPJ() {
                $tableBody.html('<tr><td colspan="6" class="py-10 text-center">Memuat data...</td></tr>');

                $.ajax({
                    url: "{{ route('spj.index') }}",
                    method: 'GET',
                    success: function (response) {
                        let html = '';
                        if (response.length > 0) {
                            response.forEach((item, index) => {
                                const notaDinas = item.nota_dinas || {};
                                const pegawais = notaDinas.pegawais ? notaDinas.pegawais.map(p => p.nama).join('<br>') : '-';
                                const lokasi = notaDinas.lokasi || '-';
                                const tanggal = notaDinas.tanggal || '-';
                                const ok = notaDinas.pegawais ? notaDinas.pegawais.length : '-';
                                
                                const nomorSpt = item.nomor_spt || '';
                                const hasRealNomor = nomorSpt.trim() !== '' && !(/\s{3,}/.test(nomorSpt));
                                
                                let nomorSptHtml = '';
                                if (hasRealNomor) {
                                    nomorSptHtml = `<span class="font-medium text-gray-700 dark:text-gray-300">${nomorSpt}</span>`;
                                } else {
                                    nomorSptHtml = `<span class="px-2 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 rounded-full">Nomor SPT</span> <span class="text-xs text-gray-400 font-mono block mt-1">${nomorSpt || '-'}</span>`;
                                }

                                let detailBtn = '';
                                if (hasRealNomor) {
                                    detailBtn = `
                                        <a href="/spj/${item.id}" class="inline-flex items-center text-blue-600 hover:text-blue-900 font-medium transition-colors">
                                            <i class="fa fa-eye mr-1.5"></i> Detail
                                        </a>
                                    `;
                                } else {
                                    detailBtn = `
                                        <span class="inline-flex items-center text-gray-400 dark:text-gray-600 font-medium cursor-not-allowed" title="Harap isi nomor SPT terlebih dahulu">
                                            <i class="fa fa-lock mr-1.5"></i> Detail
                                        </span>
                                    `;
                                }

                                html += `
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400 text-center">${index + 1}</td>
                                        <td class="px-5 py-4 text-sm">${nomorSptHtml}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${pegawais}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${ok}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${lokasi}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${tanggal}</td>
                                        <td class="px-5 py-4 text-sm text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                ${detailBtn}
                                                <button onclick="openEditSptModal(${item.id}, '${nomorSpt.replace(/'/g, "\\'")}')" class="inline-flex items-center text-amber-600 hover:text-amber-900 font-medium transition-colors">
                                                    <i class="fa fa-edit mr-1.5"></i> Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = '<tr><td colspan="6" class="py-10 text-center italic text-gray-500">Tidak ada data SPJ.</td></tr>';
                        }
                        $tableBody.html(html);
                    },
                    error: function () {
                        $tableBody.html('<tr><td colspan="6" class="py-10 text-center text-red-500">Gagal mengambil data.</td></tr>');
                    }
                });
            }

            window.openEditSptModal = function(id, nomor) {
                window.dispatchEvent(new CustomEvent('open-edit-spt-modal', {
                    detail: {
                        id: id,
                        nomor: nomor
                    }
                }));
            }

            // Initial load
            fetchSPJ();
        });
    </script>
@endpush