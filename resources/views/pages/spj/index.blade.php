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
                                html += `
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400 text-center">${index + 1}</td>
                                        <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300 font-medium">${item.nomor_spt || '-'}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${pegawais}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${ok}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${lokasi}</td>
                                        <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">${tanggal}</td>
                                        <td class="px-5 py-4 text-sm text-center">
                                            <div class="flex items-center justify-center gap-3">
                                                <a href="/spj/${item.id}" class="inline-flex items-center text-blue-600 hover:text-blue-900 font-medium transition-colors">
                                                    <i class="fa fa-eye mr-1.5"></i> Detail
                                                </a>
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

            // Initial load
            fetchSPJ();
        });
    </script>
@endpush