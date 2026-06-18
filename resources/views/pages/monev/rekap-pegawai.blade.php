@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Perjalanan Pegawai" />

    <div class="space-y-6">
        <x-common.component-card>
            <x-slot name="title">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <span class="text-base font-bold">Rekap Perjalanan Pegawai</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">
                            Jumlah nota dinas per pegawai pada bulan
                            <span id="rekap-label-bulan" class="font-semibold text-blue-600 dark:text-blue-400">{{ $namaBulan }}</span>
                        </p>
                    </div>

                    {{-- Filter Bulan & Tahun --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <select id="rekap-select-bulan-awal"
                            class="text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-xs text-gray-400">s/d</span>
                        <select id="rekap-select-bulan-akhir"
                            class="text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                        <select id="rekap-select-tahun"
                            class="text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        {{-- Spinner --}}
                        <div id="rekap-spinner" class="hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                        {{-- Export Excel --}}
                        <a id="rekap-export-btn" href="#"
                            class="text-xs inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </x-slot>

            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/60">
                <table class="w-full min-w-[600px] border-collapse text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-700 dark:text-gray-300 font-semibold text-xs uppercase tracking-wider">
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="px-6 py-3.5 text-center w-16">No</th>
                            <th class="px-6 py-3.5">Nama Pegawai / NIP</th>
                            <th class="px-6 py-3.5 text-center w-40">Total Perjalanan Dinas</th>
                        </tr>
                    </thead>
                    <tbody id="rekap-tbody" class="divide-y divide-gray-100 dark:divide-gray-700/50 text-gray-600 dark:text-gray-300">
                        @forelse($rekapPegawai as $index => $pegawai)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition duration-150">
                                <td class="px-6 py-4 text-center font-bold text-gray-400 dark:text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->nama }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">NIP. {{ $pegawai->nip ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pegawai->nota_dinas_count > 5)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 rounded-full border border-amber-200/50">
                                            {{ $pegawai->nota_dinas_count }} Kali
                                        </span>
                                    @elseif($pegawai->nota_dinas_count > 0)
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full border border-blue-200/50">
                                            {{ $pegawai->nota_dinas_count }} Kali
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500 rounded-full">
                                            0 Kali
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                    Belum ada data untuk bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-common.component-card>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const REKAP_URL = '{{ route('dashboard.rekap') }}';
    const EXPORT_URL = '{{ route('dashboard.export') }}';
    const selectBulanAwal  = document.getElementById('rekap-select-bulan-awal');
    const selectBulanAkhir = document.getElementById('rekap-select-bulan-akhir');
    const selectTahun      = document.getElementById('rekap-select-tahun');
    const tbody            = document.getElementById('rekap-tbody');
    const label            = document.getElementById('rekap-label-bulan');
    const spinner          = document.getElementById('rekap-spinner');
    const exportBtn        = document.getElementById('rekap-export-btn');

    function updateExportLink() {
        exportBtn.href = `${EXPORT_URL}?bulan_awal=${selectBulanAwal.value}&bulan_akhir=${selectBulanAkhir.value}&tahun=${selectTahun.value}`;
    }

    function badgeHtml(count) {
        if (count > 5) return `<span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 rounded-full border border-amber-200/50">${count} Kali</span>`;
        if (count > 0) return `<span class="inline-flex items-center px-3 py-1 text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full border border-blue-200/50">${count} Kali</span>`;
        return `<span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-700/60 dark:text-gray-500 rounded-full">0 Kali</span>`;
    }

    function loadRekap() {
        spinner.classList.remove('hidden');
        tbody.style.opacity = '0.4';

        fetch(`${REKAP_URL}?bulan_awal=${selectBulanAwal.value}&bulan_akhir=${selectBulanAkhir.value}&tahun=${selectTahun.value}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            label.textContent = data.namaBulan;
            if (data.pegawais.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3" class="py-12 text-center text-gray-400 dark:text-gray-500 italic">Belum ada data untuk periode ini.</td></tr>`;
                return;
            }
            tbody.innerHTML = data.pegawais.map((p, i) => `
                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition duration-150">
                    <td class="px-6 py-4 text-center font-bold text-gray-400 dark:text-gray-500">${i + 1}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900 dark:text-white">${p.nama}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">NIP. ${p.nip}</div>
                    </td>
                    <td class="px-6 py-4 text-center">${badgeHtml(p.nota_dinas_count)}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="3" class="py-8 text-center text-red-400 italic">Gagal memuat data.</td></tr>`;
        })
        .finally(() => {
            spinner.classList.add('hidden');
            tbody.style.opacity = '1';
        });
    }

    selectBulanAwal.addEventListener('change', () => {
        if (parseInt(selectBulanAwal.value) > parseInt(selectBulanAkhir.value)) selectBulanAkhir.value = selectBulanAwal.value;
        updateExportLink(); loadRekap();
    });
    selectBulanAkhir.addEventListener('change', () => {
        if (parseInt(selectBulanAkhir.value) < parseInt(selectBulanAwal.value)) selectBulanAwal.value = selectBulanAkhir.value;
        updateExportLink(); loadRekap();
    });
    selectTahun.addEventListener('change', () => { updateExportLink(); loadRekap(); });

    updateExportLink();
})();
</script>
@endpush
