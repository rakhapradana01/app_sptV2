@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Rekap Perjalanan Pegawai" />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Filter (1 Column) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Filter Visualisasi -->
            <x-common.component-card>
                <x-slot name="title">
                    <span class="text-sm font-bold text-gray-800 dark:text-white">Filter Visualisasi</span>
                </x-slot>
                <div class="space-y-4">
                    <div>
                        <label for="rekap-select-tahun-visualisasi" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Pilih Tahun</label>
                        <select id="rekap-select-tahun-visualisasi"
                            class="w-full text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>Tahun {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-common.component-card>

            <!-- Export Form -->
            <x-common.component-card>
                <x-slot name="title">
                    <span class="text-sm font-bold text-gray-800 dark:text-white">Unduh Rekap Excel</span>
                </x-slot>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Rentang Bulan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <select id="rekap-select-bulan-awal"
                                class="text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <select id="rekap-select-bulan-akhir"
                                class="text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-2 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="rekap-select-tahun-export" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Tahun</label>
                        <select id="rekap-select-tahun-export"
                            class="w-full text-xs rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-2">
                        <a id="rekap-export-btn" href="#"
                            class="w-full justify-center text-xs inline-flex items-center gap-1.5 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>
            </x-common.component-card>
        </div>

        <!-- Main Content Area (3 Columns) -->
        <div class="lg:col-span-3">
            <x-common.component-card>
                <x-slot name="title">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-base font-bold text-gray-900 dark:text-white">Rekap Perjalanan Pegawai (Tahunan)</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-normal">
                                Jumlah nota dinas per pegawai pada tahun
                                <span id="rekap-label-tahun" class="font-semibold text-blue-600 dark:text-blue-400">{{ $tahun }}</span>
                            </p>
                        </div>
                        <div id="rekap-spinner" class="hidden">
                            <svg class="animate-spin w-4 h-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                    </div>
                </x-slot>

                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-700/60">
                    <table class="w-full min-w-[900px] border-collapse text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-700 dark:text-gray-300 font-semibold text-xs uppercase tracking-wider">
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="px-4 py-3.5 text-center w-12">No</th>
                                <th class="px-4 py-3.5 min-w-[180px]">Nama Pegawai / NIP</th>
                                <th class="px-2 py-3.5 text-center w-10">Jan</th>
                                <th class="px-2 py-3.5 text-center w-10">Feb</th>
                                <th class="px-2 py-3.5 text-center w-10">Mar</th>
                                <th class="px-2 py-3.5 text-center w-10">Apr</th>
                                <th class="px-2 py-3.5 text-center w-10">Mei</th>
                                <th class="px-2 py-3.5 text-center w-10">Jun</th>
                                <th class="px-2 py-3.5 text-center w-10">Jul</th>
                                <th class="px-2 py-3.5 text-center w-10">Ags</th>
                                <th class="px-2 py-3.5 text-center w-10">Sep</th>
                                <th class="px-2 py-3.5 text-center w-10">Okt</th>
                                <th class="px-2 py-3.5 text-center w-10">Nov</th>
                                <th class="px-2 py-3.5 text-center w-10">Des</th>
                                <th class="px-3 py-3.5 text-center w-16 bg-blue-50/50 dark:bg-blue-900/10 font-bold text-blue-700 dark:text-blue-400">Total</th>
                            </tr>
                        </thead>
                        <tbody id="rekap-tbody" class="divide-y divide-gray-100 dark:divide-gray-700/50 text-gray-600 dark:text-gray-300">
                            @forelse($rekapPegawai as $index => $pegawai)
                                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition duration-150">
                                    <td class="px-4 py-4 text-center font-bold text-gray-400 dark:text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $pegawai->nama }}</div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">NIP. {{ $pegawai->nip ?? '-' }}</div>
                                    </td>
                                    @foreach(['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'ags', 'sep', 'okt', 'nov', 'des'] as $mKey)
                                        <td class="px-2 py-4 text-center font-medium">
                                            @if($pegawai->$mKey > 0)
                                                <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $pegawai->$mKey }}</span>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-3 py-4 text-center bg-blue-50/30 dark:bg-blue-900/5 font-bold">
                                        @if($pegawai->total > 5)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 rounded-full border border-amber-200/50">
                                                {{ $pegawai->total }}
                                            </span>
                                        @elseif($pegawai->total > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full border border-blue-200/50">
                                                {{ $pegawai->total }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="py-12 text-center text-gray-400 dark:text-gray-500 italic">
                                        Belum ada data untuk tahun ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const REKAP_TAHUNAN_URL = '{{ route('dashboard.rekap.tahunan') }}';
    const EXPORT_URL = '{{ route('dashboard.export') }}';
    
    // Elements for export
    const selectBulanAwal  = document.getElementById('rekap-select-bulan-awal');
    const selectBulanAkhir = document.getElementById('rekap-select-bulan-akhir');
    const selectTahunExport = document.getElementById('rekap-select-tahun-export');
    const exportBtn        = document.getElementById('rekap-export-btn');

    // Elements for yearly recap visualization
    const selectTahunVis   = document.getElementById('rekap-select-tahun-visualisasi');
    const tbody            = document.getElementById('rekap-tbody');
    const labelTahun       = document.getElementById('rekap-label-tahun');
    const spinner          = document.getElementById('rekap-spinner');

    function updateExportLink() {
        exportBtn.href = `${EXPORT_URL}?bulan_awal=${selectBulanAwal.value}&bulan_akhir=${selectBulanAkhir.value}&tahun=${selectTahunExport.value}`;
    }

    function cellHtml(count) {
        return count > 0 
            ? `<span class="text-blue-600 dark:text-blue-400 font-bold">${count}</span>` 
            : `<span class="text-gray-300 dark:text-gray-600">-</span>`;
    }

    function totalBadgeHtml(count) {
        if (count > 5) {
            return `<span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400 rounded-full border border-amber-200/50">${count}</span>`;
        }
        if (count > 0) {
            return `<span class="inline-flex items-center px-2 py-0.5 text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-full border border-blue-200/50">${count}</span>`;
        }
        return `<span class="text-gray-300 dark:text-gray-600">0</span>`;
    }

    function loadRekapTahunan() {
        spinner.classList.remove('hidden');
        tbody.style.opacity = '0.4';

        fetch(`${REKAP_TAHUNAN_URL}?tahun=${selectTahunVis.value}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            labelTahun.textContent = data.tahun;
            if (data.pegawais.length === 0) {
                tbody.innerHTML = `<tr><td colspan="15" class="py-12 text-center text-gray-400 dark:text-gray-500 italic">Belum ada data untuk tahun ini.</td></tr>`;
                return;
            }
            tbody.innerHTML = data.pegawais.map((p, i) => `
                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition duration-150">
                    <td class="px-4 py-4 text-center font-bold text-gray-400 dark:text-gray-500">${i + 1}</td>
                    <td class="px-4 py-4">
                        <div class="font-semibold text-gray-900 dark:text-white">${p.nama}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">NIP. ${p.nip}</div>
                    </td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.jan)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.feb)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.mar)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.apr)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.mei)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.jun)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.jul)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.ags)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.sep)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.okt)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.nov)}</td>
                    <td class="px-2 py-4 text-center font-medium">${cellHtml(p.des)}</td>
                    <td class="px-3 py-4 text-center bg-blue-50/30 dark:bg-blue-900/5 font-bold">${totalBadgeHtml(p.total)}</td>
                </tr>
            `).join('');
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="15" class="py-8 text-center text-red-400 italic">Gagal memuat data.</td></tr>`;
        })
        .finally(() => {
            spinner.classList.add('hidden');
            tbody.style.opacity = '1';
        });
    }

    // Export Event Listeners
    selectBulanAwal.addEventListener('change', () => {
        if (parseInt(selectBulanAwal.value) > parseInt(selectBulanAkhir.value)) {
            selectBulanAkhir.value = selectBulanAwal.value;
        }
        updateExportLink();
    });
    selectBulanAkhir.addEventListener('change', () => {
        if (parseInt(selectBulanAkhir.value) < parseInt(selectBulanAwal.value)) {
            selectBulanAwal.value = selectBulanAkhir.value;
        }
        updateExportLink();
    });
    selectTahunExport.addEventListener('change', updateExportLink);

    // Visualisation Event Listeners
    selectTahunVis.addEventListener('change', loadRekapTahunan);

    updateExportLink();
})();
</script>
@endpush
