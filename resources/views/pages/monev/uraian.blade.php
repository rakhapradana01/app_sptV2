@extends('layouts.app')
@section('title', 'Uraian & OK Tujuan Perjalanan Dinas')

@section('content')
    <x-common.page-breadcrumb pageTitle="Uraian & OK Perjalanan Dinas" />

    <div class="space-y-6">
        <!-- Filter & Header Card -->
        <x-common.component-card>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <label for="select-sub-kegiatan" class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">
                        Pilih Sub Kegiatan:
                    </label>
                    <select id="select-sub-kegiatan" class="w-full text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        @forelse ($allSubKegiatans as $sub)
                            <option value="{{ $sub->id }}" {{ $selectedSubId == $sub->id ? 'selected' : '' }}>
                                {{ $sub->nomor_rekening }} - {{ $sub->nama_kegiatan }} (PPTK: {{ $sub->pegawai?->nama ?? $sub->owner?->name ?? '-' }})
                            </option>
                        @empty
                            <option value="">-- Tidak ada Sub Kegiatan --</option>
                        @endforelse
                    </select>
                </div>

                @if ($selectedSubKegiatan)
                    <div class="flex items-end pt-2 md:pt-6">
                        <x-ui.button id="btn-tambah" @click="$dispatch('open-uraian-modal')" variant="primary" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Uraian
                        </x-ui.button>
                    </div>
                @endif
            </div>
        </x-common.component-card>

        @if ($selectedSubKegiatan)
            @php
                $totalKoefisien = $selectedSubKegiatan->uraians->sum('ok_total');
                $totalOkTerpakai = $selectedSubKegiatan->uraians->sum('ok_terpakai');
                $sisaOk = $totalKoefisien - $totalOkTerpakai;
                
                $totalPagu = $selectedSubKegiatan->pagu ?? $selectedSubKegiatan->uraians->sum('total_anggaran');
                $totalRealisasi = $selectedSubKegiatan->realisasi ?? $selectedSubKegiatan->uraians->sum('anggaran_terpakai');
                $sisaPagu = $totalPagu - $totalRealisasi;
            @endphp

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pagu & Realisasi Anggaran</span>
                        <div id="kpi-pagu-total" class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">
                            Rp {{ number_format($totalPagu, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <span id="kpi-pagu-realisasi" class="text-emerald-600 dark:text-emerald-400 font-semibold">Realisasi: Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</span>
                            <span>•</span>
                            <span id="kpi-pagu-sisa">Sisa: Rp {{ number_format($sisaPagu, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Monitoring OK (Orang / Kali)</span>
                        <div id="kpi-ok-total" class="text-xl font-bold text-gray-900 dark:text-white mt-0.5">
                            {{ $totalKoefisien }} <span class="text-sm font-medium text-gray-500">OK Total</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <span id="kpi-ok-terpakai" class="text-blue-600 dark:text-blue-400 font-semibold">Terpakai: {{ $totalOkTerpakai }} OK</span>
                            <span>•</span>
                            <span id="kpi-ok-sisa" class="{{ $sisaOk < 0 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400' }}">Sisa: {{ $sisaOk }} OK</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700/60 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">PPTK / Penanggung Jawab</span>
                        <div id="kpi-pptk-nama" class="text-base font-bold text-gray-900 dark:text-white mt-0.5">
                            {{ $selectedSubKegiatan->pegawai?->nama ?? $selectedSubKegiatan->owner?->name ?? '-' }}
                        </div>
                        <div id="kpi-pptk-jabatan" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $selectedSubKegiatan->pegawai?->jabatan ?? 'Penanggung Jawab Sub Kegiatan' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Main Uraian -->
            <x-common.component-card title="Daftar Uraian & OK Tujuan Perjalanan Dinas">
                <div id="container-uraian" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400" id="nama-sub-kegiatan">
                            {{ $selectedSubKegiatan->nomor_rekening }} — {{ $selectedSubKegiatan->nama_kegiatan }}
                        </h4>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm text-left border-collapse" id="table-uraian">
                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700">
                                <tr>
                                    <th class="px-4 py-3.5 text-center w-12">No</th>
                                    <th class="px-5 py-3.5">Uraian / Tujuan Perjalanan</th>
                                    <th class="px-4 py-3.5 text-center">OK Total</th>
                                    <th class="px-4 py-3.5 text-center">Terpakai</th>
                                    <th class="px-4 py-3.5 text-center">Sisa OK</th>
                                    <th class="px-4 py-3.5 text-right">Harga Satuan</th>
                                    <th class="px-4 py-3.5 text-right">Total Anggaran</th>
                                    <th class="px-4 py-3.5 text-right">Realisasi</th>
                                    <th class="px-4 py-3.5 text-right">Sisa Pagu</th>
                                    <th class="px-4 py-3.5 text-center w-28">Progres</th>
                                    <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Loaded via JS Ajax for dynamic update -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-common.component-card>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center text-gray-500 border border-gray-100 dark:border-gray-700">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-base font-medium">Belum ada Sub Kegiatan yang dipilih atau tidak ada data tersedia.</p>
            </div>
        @endif
    </div>

    @if ($selectedSubKegiatan)
        <!-- Modal Form Uraian (Tambah / Edit) -->
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

                        document.getElementById('modal-uraian-id').value = data.id;
                        document.getElementById('modal-sub-id').value = data.sub_kegiatan_id;
                        document.getElementById('input-uraian').value = data.uraian;
                        document.getElementById('input-ok-total').value = data.ok_total;
                        document.getElementById('input-ok-terpakai').value = data.ok_terpakai;
                        document.getElementById('input-harga-satuan').value = data.harga_satuan;
                        document.getElementById('input-total-anggaran').value = data.total_anggaran;
                        document.getElementById('input-anggaran-terpakai').value = data.anggaran_terpakai;
                    } else {
                        title.innerText = 'Tambah Uraian Baru';
                        form.action = '{{ route('uraian.store') }}';
                        methodField.value = 'POST';
                        form.reset();
                        document.getElementById('modal-sub-id').value = $('#select-sub-kegiatan').val() || '{{ $selectedSubKegiatan->id }}';
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
                    <input type="hidden" name="sub_kegiatan_id" id="modal-sub-id" value="{{ $selectedSubKegiatan->id }}">

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Uraian / Tujuan Perjalanan Dinas</label>
                            <input type="text" name="uraian" id="input-uraian"
                                class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required placeholder="Contoh: Monitoring dan Evaluasi Pelaksanaan Program Kebidangan ke Kab/Kota">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">OK Total (Target Perjalanan)</label>
                                <input type="number" name="ok_total" id="input-ok-total"
                                    class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    required min="0" step="1" placeholder="Misal: 10">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">OK Terpakai (Realisasi)</label>
                                <input type="number" name="ok_terpakai" id="input-ok-terpakai"
                                    class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                    value="0" required min="0" step="1">
                            </div>
                        </div>

                        <div>
                            <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Harga Satuan (Rp per OK)</label>
                            <input type="number" name="harga_satuan" id="input-harga-satuan"
                                class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                                required min="0" placeholder="Misal: 1500000">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Total Anggaran (Otomatis)</label>
                                <input type="number" name="total_anggaran" id="input-total-anggaran"
                                    class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    readonly required>
                            </div>
                            <div>
                                <label class="block mb-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Anggaran Terpakai (Otomatis)</label>
                                <input type="number" name="anggaran_terpakai" id="input-anggaran-terpakai"
                                    class="w-full px-4 py-2 text-sm border rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    readonly required value="0">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <x-ui.button type="button" @click="open = false" variant="outline">
                            Batal
                        </x-ui.button>
                        <x-ui.button type="submit" variant="primary">
                            Simpan Uraian
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </x-ui.modal>

        <!-- Modal Riwayat SPJ (Perjalanan Dinas yang Menggunakan Uraian Ini) -->
        <x-ui.modal @open-history-modal.window="
                open = true; 
                const data = $event.detail || {};
                $nextTick(() => {
                    document.getElementById('history-title').innerText = 'Riwayat Perjalanan Dinas & SPJ: ' + data.uraian;
                    
                    const tbody = document.getElementById('history-table-body');
                    tbody.innerHTML = '';
                    
                    if (data.spj_rincians && data.spj_rincians.length > 0) {
                        data.spj_rincians.forEach((rincian, index) => {
                            const sptNomor = rincian.nota_dinas?.spt?.nomor_spt || '-';
                            const sptId = rincian.nota_dinas?.spt?.id;
                            const pegawaiNama = rincian.pegawai?.nama || '-';
                            const pegawaiNip = rincian.pegawai?.nip || '-';
                            const pegawaiJabatan = rincian.pegawai?.jabatan || '-';
                            const totalBiaya = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(rincian.total || 0);
                            
                            let actionHtml = '-';
                            if (sptId) {
                                actionHtml = `<a href='/spj/${sptId}' class='px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-semibold rounded-lg transition inline-flex items-center gap-1'>
                                    <svg class='w-3.5 h-3.5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 12a3 3 0 11-6 0 3 3 0 016 0z'></path><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'></path></svg>
                                    Detail SPJ
                                </a>`;
                            }

                            tbody.innerHTML += `
                                <tr class='border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50'>
                                    <td class='px-4 py-3 text-center'>${index + 1}</td>
                                    <td class='px-4 py-3'>
                                        <div class='font-medium text-gray-900 dark:text-white'>${pegawaiNama}</div>
                                        <div class='text-xs text-gray-400'>NIP. ${pegawaiNip}</div>
                                    </td>
                                    <td class='px-4 py-3 text-gray-600 dark:text-gray-300'>
                                        <div class='text-xs font-medium'>${pegawaiJabatan}</div>
                                    </td>
                                    <td class='px-4 py-3 font-semibold text-blue-600 dark:text-blue-400'>${totalBiaya}</td>
                                    <td class='px-4 py-3 text-center'>${actionHtml}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan='5' class='py-8 text-center text-gray-500 italic'>
                                    Belum ada SPJ atau perjalanan dinas yang tercatat menggunakan pagu uraian ini.
                                </td>
                            </tr>
                        `;
                    }
                });
            " class="max-w-[800px]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center gap-2" id="history-title">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Riwayat Perjalanan Dinas & SPJ
                    </h3>
                </div>
                
                <div class="overflow-x-auto max-h-[400px]">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                            <tr class="text-gray-500 font-semibold uppercase tracking-wider text-xs border-b border-gray-100 dark:border-gray-800">
                                <th class="px-4 py-3 text-center w-12">No</th>
                                <th class="px-4 py-3 text-left">Pegawai</th>
                                <th class="px-4 py-3 text-left">Jabatan</th>
                                <th class="px-4 py-3 text-left">Total SPJ</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body">
                            <!-- Filled dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-6">
                    <x-ui.button type="button" @click="open = false" variant="outline">
                        Tutup
                    </x-ui.button>
                </div>
            </div>
        </x-ui.modal>
    @endif
@endsection

@push('scripts')
    <script>
        $(function () {
            const $subKegiatanSelect = $('#select-sub-kegiatan');
            const $tableBody = $('#table-uraian tbody');

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            }

            function fetchUraian(id) {
                if (!id) return;

                $tableBody.html('<tr><td colspan="11" class="py-10 text-center text-gray-500">Memuat data uraian...</td></tr>');

                $.ajax({
                    url: `/monev/${id}`,
                    method: 'GET',
                    success: function (response) {
                        let uraians = [];
                        let subMeta = null;

                        if (Array.isArray(response)) {
                            uraians = response;
                        } else if (response && response.uraians) {
                            uraians = response.uraians;
                            subMeta = response.sub_kegiatan;
                        }

                        // Calculate dynamic KPI stats
                        let totalKoefisien = 0;
                        let totalOkTerpakai = 0;
                        let totalAnggaranSum = 0;
                        let totalRealisasiSum = 0;

                        uraians.forEach(item => {
                            totalKoefisien += (parseFloat(item.ok_total) || 0);
                            totalOkTerpakai += (parseFloat(item.ok_terpakai) || 0);
                            totalAnggaranSum += (parseFloat(item.total_anggaran) || 0);
                            totalRealisasiSum += (parseFloat(item.anggaran_terpakai) || 0);
                        });

                        const sisaOk = totalKoefisien - totalOkTerpakai;
                        const totalPagu = subMeta ? subMeta.pagu : totalAnggaranSum;
                        const totalRealisasi = subMeta ? subMeta.realisasi : totalRealisasiSum;
                        const sisaPagu = totalPagu - totalRealisasi;

                        // Update KPI cards in DOM dynamically
                        $('#kpi-pagu-total').text(formatCurrency(totalPagu));
                        $('#kpi-pagu-realisasi').text('Realisasi: ' + formatCurrency(totalRealisasi));
                        $('#kpi-pagu-sisa').text('Sisa: ' + formatCurrency(sisaPagu));

                        $('#kpi-ok-total').html(`${Math.round(totalKoefisien)} <span class="text-sm font-medium text-gray-500">OK Total</span>`);
                        $('#kpi-ok-terpakai').text(`Terpakai: ${Math.round(totalOkTerpakai)} OK`);
                        $('#kpi-ok-sisa').text(`Sisa: ${Math.round(sisaOk)} OK`)
                            .attr('class', sisaOk < 0 ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400');

                        if (subMeta) {
                            $('#kpi-pptk-nama').text(subMeta.pptk_nama || '-');
                            $('#kpi-pptk-jabatan').text(subMeta.pptk_jabatan || 'Penanggung Jawab Sub Kegiatan');
                            $('#nama-sub-kegiatan').text(`${subMeta.nomor_rekening} — ${subMeta.nama_kegiatan}`);
                        }

                        $('#modal-sub-id').val(id);

                        let html = '';
                        if (uraians.length > 0) {
                            uraians.forEach((item, index) => {
                                const okTotal = Math.round(parseFloat(item.ok_total)) || 0;
                                const okTerpakai = Math.round(parseFloat(item.ok_terpakai)) || 0;
                                const totalAnggaran = Math.round(parseFloat(item.total_anggaran)) || 0;
                                const anggaranTerpakai = Math.round(parseFloat(item.anggaran_terpakai)) || 0;
                                
                                const sisaKoefisien = okTotal - okTerpakai;
                                const sisaAnggaran = totalAnggaran - anggaranTerpakai;
                                const persen = okTotal > 0 ? Math.min(100, Math.round((okTerpakai / okTotal) * 100)) : 0;

                                html += `
                                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/60 dark:hover:bg-gray-800/60 transition">
                                        <td class="px-4 py-3.5 text-center font-medium text-gray-500">${index + 1}</td>
                                        <td class="px-5 py-3.5 font-medium text-gray-900 dark:text-white max-w-xs">${item.uraian}</td>
                                        <td class="px-4 py-3.5 text-center font-bold text-gray-800 dark:text-gray-200">${okTotal}</td>
                                        <td class="px-4 py-3.5 text-center font-semibold text-blue-600 dark:text-blue-400">${okTerpakai}</td>
                                        <td class="px-4 py-3.5 text-center font-semibold ${sisaKoefisien < 0 ? 'text-red-600' : 'text-emerald-600 dark:text-emerald-400'}">${sisaKoefisien}</td>
                                        <td class="px-4 py-3.5 text-right font-mono text-gray-600 dark:text-gray-400">${formatCurrency(item.harga_satuan || 0)}</td>
                                        <td class="px-4 py-3.5 text-right font-semibold font-mono text-gray-900 dark:text-white">${formatCurrency(totalAnggaran)}</td>
                                        <td class="px-4 py-3.5 text-right font-semibold font-mono text-emerald-600 dark:text-emerald-400">${formatCurrency(anggaranTerpakai)}</td>
                                        <td class="px-4 py-3.5 text-right font-semibold font-mono ${sisaAnggaran < 0 ? 'text-red-600' : 'text-gray-700 dark:text-gray-300'}">${formatCurrency(sisaAnggaran)}</td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="flex items-center gap-2 justify-center">
                                                <div class="w-16 bg-gray-200 dark:bg-gray-700 h-2 rounded-full overflow-hidden">
                                                    <div class="bg-blue-600 h-full rounded-full" style="width: ${persen}%"></div>
                                                </div>
                                                <span class="text-xs font-bold text-gray-700 dark:text-gray-300">${persen}%</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" class="btn-history p-1.5 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition" 
                                                    title="Lihat Riwayat SPJ/Perjalanan" data-item='${JSON.stringify(item).replace(/'/g, "&apos;")}'>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                                <button type="button" class="btn-edit-uraian p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-md transition" 
                                                    title="Edit Uraian" data-item='${JSON.stringify(item).replace(/'/g, "&apos;")}'>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form action="/monev/uraian/${item.id}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus uraian ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition" title="Hapus Uraian">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            html = '<tr><td colspan="11" class="py-10 text-center italic text-gray-500">Belum ada data uraian untuk sub kegiatan ini. Klik tombol "+ Tambah Uraian" untuk menambahkan.</td></tr>';
                        }
                        $tableBody.html(html);
                    },
                    error: function () {
                        $tableBody.html('<tr><td colspan="11" class="py-10 text-center text-red-500">Gagal mengambil data uraian.</td></tr>');
                    }
                });
            }

            // Sub Kegiatan Select Change Event (Lazy load without full page refresh!)
            $subKegiatanSelect.on('change', function () {
                const subId = $(this).val();
                if (subId) {
                    // Update URL parameter silently without reloading page
                    const newUrl = window.location.pathname + '?sub_kegiatan_id=' + subId;
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    // Lazy load content via AJAX dynamically
                    fetchUraian(subId);
                }
            });

            // Initial load of selected sub-kegiatan uraian
            const currentSubId = '{{ $selectedSubId }}';
            if (currentSubId) {
                fetchUraian(currentSubId);
            }

            // Click Edit Uraian
            $(document).on('click', '.btn-edit-uraian', function () {
                const item = $(this).data('item');
                window.dispatchEvent(new CustomEvent('open-uraian-modal', { detail: item }));
            });

            // Click History
            $(document).on('click', '.btn-history', function () {
                const item = $(this).data('item');
                window.dispatchEvent(new CustomEvent('open-history-modal', { detail: item }));
            });

            // Auto Calculate Total Anggaran & Anggaran Terpakai
            $(document).on('input', '#input-ok-total, #input-ok-terpakai, #input-harga-satuan', function () {
                const harga = parseFloat($('#input-harga-satuan').val()) || 0;
                const okTotal = parseFloat($('#input-ok-total').val()) || 0;
                const okTerpakai = parseFloat($('#input-ok-terpakai').val()) || 0;

                $('#input-total-anggaran').val(okTotal * harga);
                $('#input-anggaran-terpakai').val(okTerpakai * harga);
            });
        });
    </script>
@endpush
