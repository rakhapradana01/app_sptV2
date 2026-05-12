@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Sub Kegiatan" />
    <div class="space-y-6">
        <x-common.component-card title="Monev: {{ $pptk->nama }}">
            <div class="space-y-6">

                <!-- Dropdown Pilih Sub Kegiatan -->
                <div class="form-group">
                    <label class="block mb-2 font-bold text-gray-700">Pilih Sub Kegiatan:</label>
                    <div class="flex justify-between">
                        <select class="form-control" id="sub-kegiatan">
                            @foreach ($pptk->subKegiatans as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->nomor_rekening }} - {{ $sub->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                        <x-ui.button id="btn-tambah" @click="$dispatch('open-uraian-modal')">
                            Tambah
                        </x-ui.button>
                    </div>
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
                                    <th class="px-5 py-3 text-left sm:px-6">OK</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Digunakan</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Sisa OK</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Anggaran</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Realisasi</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Sisa Angg</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Progres</th>
                                    <th class="px-5 py-3 text-left sm:px-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </div>

    <!-- Modal Uraian (Tambah/Edit) -->
    <x-ui.modal @open-uraian-modal.window="
            open = true; 
            const data = $event.detail || {};
            $nextTick(() => {
                const form = document.getElementById('form-uraian');
                const title = document.getElementById('modal-title');
                const methodField = document.getElementById('form-method');

                if (data.id) {
                    title.innerText = 'Edit Uraian';
                    form.action = `/monev/uraian/${data.id}`;
                    methodField.value = 'PUT';

                    // Fill form
                    document.getElementById('modal-uraian-id').value = data.id;
                    document.getElementById('modal-sub-id').value = data.sub_kegiatan_id;
                    document.getElementById('input-uraian').value = data.uraian;
                    document.getElementById('input-ok-total').value = data.ok_total;
                    document.getElementById('input-ok-terpakai').value = data.ok_terpakai;
                    document.getElementById('input-harga-satuan').value = data.harga_satuan;
                    document.getElementById('input-total-anggaran').value = data.total_anggaran;
                    document.getElementById('input-anggaran-terpakai').value = data.anggaran_terpakai;
                } else {
                    title.innerText = 'Tambah Uraian';
                    form.action = '{{ route('uraian.store') }}';
                    methodField.value = 'POST';
                    form.reset();
                    document.getElementById('modal-sub-id').value = document.getElementById('sub-kegiatan').value;
                    document.getElementById('modal-uraian-id').value = '';
                    document.getElementById('input-ok-terpakai').value = 0;
                    document.getElementById('input-anggaran-terpakai').value = 0;
                }
            });
        " class="max-w-[600px]">
        <div class="p-6">
            <h3 class="mb-4 text-xl font-bold text-gray-800 dark:text-white" id="modal-title">Tambah Uraian</h3>
            <form id="form-uraian" action="{{ route('uraian.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="id" id="modal-uraian-id">
                <input type="hidden" name="sub_kegiatan_id" id="modal-sub-id">

                <div class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Uraian</label>
                        <input type="text" name="uraian" id="input-uraian"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            required placeholder="Masukkan uraian kegiatan">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">OK Total
                                (Koefisien)</label>
                            <input type="number" name="ok_total" id="input-ok-total"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required min="0" step="0.01">
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">OK Terpakai</label>
                            <input type="number" name="ok_terpakai" id="input-ok-terpakai"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                value="0" required min="0" step="0.01">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Harga Satuan</label>
                        <input type="number" name="harga_satuan" id="input-harga-satuan"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                            required min="0">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Total Anggaran</label>
                            <input type="number" name="total_anggaran" id="input-total-anggaran"
                                class="w-full px-4 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                readonly required>
                        </div>
                        <div>
                            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-300">Anggaran Terpakai</label>
                            <input type="number" name="anggaran_terpakai" id="input-anggaran-terpakai"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                value="0" required min="0">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <x-ui.button type="button" @click="open = false" variant="outline">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        Simpan
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
@endsection

@push('scripts')
    <script>
        $(function () {
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
                $tableBody.html('<tr><td colspan="9" class="py-10 text-center">Memuat data...</td></tr>');

                $.ajax({
                    url: `/monev/${id}`,
                    method: 'GET',
                    success: function (response) {
                        let html = '';
                        if (response.length > 0) {
                            response.forEach(item => {
                                const okTotal = parseFloat(item.ok_total) || 0;
                                const okTerpakai = parseFloat(item.ok_terpakai) || 0;
                                const totalAnggaran = parseFloat(item.total_anggaran) || 0;
                                const anggaranTerpakai = parseFloat(item.anggaran_terpakai) || 0;
                                
                                const sisaKoefisien = okTotal - okTerpakai;
                                const sisaAnggaran = totalAnggaran - anggaranTerpakai;
                                const persen = okTotal > 0 ? (okTerpakai / okTotal) * 100 : 0;

                                html += `
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="px-5 py-3 text-left sm:px-6">${item.uraian}</td>
                                            <td class="px-5 py-3 text-left sm:px-6">${okTotal}</td>
                                            <td class="px-5 py-3 text-left sm:px-6">${okTerpakai}</td>
                                            <td class="px-5 py-3 text-left sm:px-6">${sisaKoefisien}</td>
                                            <td class="px-5 py-3 text-left sm:px-6">${formatCurrency(totalAnggaran)}</td>
                                            <td class="px-5 py-3 text-left sm:px-6 text-success">${formatCurrency(anggaranTerpakai)}</td>
                                            <td class="px-5 py-3 text-left sm:px-6 text-danger">${formatCurrency(sisaAnggaran)}</td>
                                            <td class="px-5 py-3 text-left sm:px-6">
                                                <div class="flex items-center gap-2">
                                                    <div class="progress flex-grow" style="height: 10px; background-color: #e9ecef; border-radius: 5px; overflow: hidden;">
                                                        <div class="progress-bar bg-info"
                                                            style="width: ${persen}%; height: 100%; background-color: #0dcaf0;"></div>
                                                    </div>
                                                    <small class="font-bold">${Math.round(persen)}%</small>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3 text-left sm:px-6">
                                                <div class="flex items-center gap-2">
                                                    <button class="btn-edit-uraian px-3 py-1.5 text-xs font-medium bg-yellow-400 text-yellow-900 rounded-md hover:bg-yellow-500 transition" 
                                                        data-item='${JSON.stringify(item)}'>
                                                        Edit
                                                    </button>
                                                    <form action="/monev/uraian/${item.id}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                            });
                        } else {
                            html = '<tr><td colspan="9" class="py-10 text-center italic text-gray-500">Tidak ada data uraian.</td></tr>';
                        }
                        $tableBody.html(html);
                    },
                    error: function () {
                        $tableBody.html('<tr><td colspan="9" class="py-10 text-center text-danger">Gagal mengambil data.</td></tr>');
                    }
                });
            }

            // On Change
            $subKegiatan.on('change', function () {
                fetchUraian($(this).val());
            });

            // Initial Load (First Option)
            const firstId = $subKegiatan.val();
            if (firstId) {
                fetchUraian(firstId);
            }

            // Click Edit Uraian
            $(document).on('click', '.btn-edit-uraian', function () {
                const item = $(this).data('item');
                window.dispatchEvent(new CustomEvent('open-uraian-modal', { detail: item }));
            });

            // Hitung Otomatis (Total & Terpakai)
            $(document).on('input', '#input-ok-total, #input-ok-terpakai, #input-harga-satuan', function () {
                const harga = parseFloat($('#input-harga-satuan').val()) || 0;

                // Hitung Total Anggaran
                const okTotal = parseFloat($('#input-ok-total').val()) || 0;
                $('#input-total-anggaran').val(okTotal * harga);

                // Hitung Anggaran Terpakai
                const okTerpakai = parseFloat($('#input-ok-terpakai').val()) || 0;
                $('#input-anggaran-terpakai').val(okTerpakai * harga);
            });
        });
    </script>
@endpush