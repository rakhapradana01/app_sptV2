<div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
    <div>
        <p class="text-gray-500">Nama Pegawai</p>
        <p class="font-semibold text-gray-900">{{ $spt->pegawais_efektif->first()->nama ?? '-' }}</p>
    </div>
    <div>
        <p class="text-gray-500">Tujuan</p>
        <p class="font-semibold text-gray-900">{{ $spt->lokasi_efektif ?? '-' }}</p>
    </div>
    <div>
        <p class="text-gray-500">Tanggal</p>
        <p class="font-semibold text-gray-900">
            {{ $spt->tanggal_mulai_efektif ? \Carbon\Carbon::parse($spt->tanggal_mulai_efektif)->format('d M Y') : ' ' }}
            -
            {{ $spt->tanggal_selesai_efektif ? \Carbon\Carbon::parse($spt->tanggal_selesai_efektif)->format('d M Y') : ' ' }}
        </p>
    </div>
    <div class="flex justify-between items-center mb-4">
        <h5 class="font-bold text-gray-800 dark:text-white">Rincian Biaya Perjalanan</h5>
        @if($spt->spj_rincians_efektif->isNotEmpty())
            <a :href="'{{ route('spj.exportExcel', $spt->id) }}?penerima_id=' + penerimaId + '&tanggal_kuitansi=' + tanggalKuitansi"
                target="_blank"
                class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center gap-2 shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Ekspor SPJ (Excel)
            </a>
        @endif
    </div>
</div>

