@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Monev: {{ $pptk->nama }}">
            <div class="space-y-6">

                <!-- Dropdown Pilih Sub Kegiatan -->
                <div class="form-group">
                    <label class="block mb-2 font-bold text-gray-700">Pilih Sub Kegiatan:</label>
                    <select class="form-control" id="sub-kegiatan">
                        @foreach ($pptk->subKegiatans as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->nama_kegiatan }}</option>
                        @endforeach
                    </select>
               </div>

                <hr class="my-4">

                <!-- Container Tampilan -->
                <div id="container-uraian">
                    <h4 class="mb-4 text-lg font-semibold text-blue-600" id="nama-sub-kegiatan">Memuat...</h4>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px] border-collapse" id="table-uraian">
                            <thead class="bg-light">
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="px-5 py-3 text-left sm:px-6">Uraian</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Koefisien</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Digunakan</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Anggaran</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Realisasi</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Progres</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi melalui JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            const $subKegiatan = $('#sub-kegiatan');
            const $tableBody = $('#table-uraian tbody');
            const $namaSub = $('#nama-sub-kegiatan');

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            }

            function fetchUraian(id) {
                if (!id) return;

                $namaSub.text($subKegiatan.find('option:selected').text());
                $tableBody.html('<tr><td colspan="6" class="py-10 text-center">Memuat data...</td></tr>');

                $.ajax({
                    url: `/monev/${id}`,
                    method: 'GET',
                    success: function(response) {
                        let html = '';
                        if (response.length > 0) {
                            response.forEach(item => {
                                const okTotal = parseFloat(item.ok_total) || 0;
                                const okTerpakai = parseFloat(item.ok_terpakai) || 0;
                                const persen = okTotal > 0 ? (okTerpakai / okTotal) * 100 : 0;

                                html += `
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="px-5 py-3 text-left sm:px-6">${item.uraian}</td>
                                        <td class="px-5 py-3 text-left sm:px-6">${okTotal}</td>
                                        <td class="px-5 py-3 text-left sm:px-6">${okTerpakai}</td>
                                        <td class="px-5 py-3 text-left sm:px-6">${formatCurrency(item.total_anggaran)}</td>
                                        <td class="px-5 py-3 text-left sm:px-6 text-success">${formatCurrency(item.anggaran_terpakai)}</td>
                                        <td class="px-5 py-3 text-left sm:px-6">
                                            <div class="flex items-center gap-2">
                                                <div class="progress flex-grow" style="height: 10px; background-color: #e9ecef; border-radius: 5px; overflow: hidden;">
                                                    <div class="progress-bar bg-info"
                                                        style="width: ${persen}%; height: 100%; background-color: #0dcaf0;"></div>
                                                </div>
                                                <small class="font-bold">${Math.round(persen)}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = '<tr><td colspan="6" class="py-10 text-center italic text-gray-500">Tidak ada data uraian.</td></tr>';
                        }
                        $tableBody.html(html);
                    },
                    error: function() {
                        $tableBody.html('<tr><td colspan="6" class="py-10 text-center text-danger">Gagal mengambil data.</td></tr>');
                    }
                });
            }

            // On Change
            $subKegiatan.on('change', function() {
                fetchUraian($(this).val());
            });

            // Initial Load (First Option)
            const firstId = $subKegiatan.val();
            if (firstId) {
                fetchUraian(firstId);
            }
        });
    </script>
@endpush
