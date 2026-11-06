<!-- Kuitansi -->
<div x-show="activeTab === 'kuitansi'" x-cloak x-transition>
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 bg-blue-50 dark:bg-blue-950/40 p-4 rounded-2xl border border-blue-100 dark:border-blue-900">
        <div>
            <h5 class="font-bold text-gray-800 dark:text-white">Preview Kuitansi Pembayaran</h5>
            <p class="text-xs text-gray-500 mt-0.5">Pilih pegawai yang menandatangani kuitansi ini.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Penerima
                    Kuitansi:</label>
                <select x-model="penerimaId" @change="
                                const sel = $el.options[$el.selectedIndex];
                                changePenerima(sel.value, sel.getAttribute('data-nama'), sel.getAttribute('data-nip'));
                            "
                    class="text-xs py-1 px-3 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    @foreach($spt->pegawais_efektif as $pegawai)
                        <option value="{{ $pegawai->id }}" data-nama="{{ $pegawai->nama }}"
                            data-nip="{{ $pegawai->nip ?? '-' }}">{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Tanggal
                    Kuitansi:</label>
                <input type="date" x-model="tanggalKuitansi"
                    class="text-xs py-1 px-3 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>
            <span
                class="px-3 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full dark:bg-blue-900 dark:text-blue-200">
                Rek: {{ ($subKegiatan->nomor_rekening ?? '') . '.5.1.02.04.01.0001' }}
            </span>
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-8 max-w-4xl mx-auto font-sans relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-bl-full pointer-events-none">
        </div>

        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-100 dark:border-gray-800 pb-6 mb-6">
            <div>
                <h4 class="font-bold text-lg text-gray-900 dark:text-white tracking-wide uppercase">
                    KUITANSI PEMBAYARAN</h4>
                <p class="text-xs text-gray-400 mt-0.5">PEMERINTAH PROVINSI KALIMANTAN SELATAN</p>
            </div>
            <div
                class="mt-4 md:mt-0 text-left md:text-right text-xs space-y-1 text-gray-500 dark:text-gray-400">
                <p>Tahun Anggaran: <span
                        class="font-semibold text-gray-800 dark:text-white">{{ Carbon\Carbon::parse($tanggalMulai)->format('Y') }}</span>
                </p>
                <p>Nomor Rekening: <span
                        class="font-semibold text-gray-800 dark:text-white">{{ ($subKegiatan->nomor_rekening ?? '') . '.5.1.02.04.01.0001' }}</span>
                </p>
            </div>
        </div>

        <div class="space-y-4 text-sm text-gray-700 dark:text-gray-300">
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2 border-b border-gray-50 dark:border-gray-800">
                <span class="font-medium text-gray-400">Telah terima dari</span>
                <span class="md:col-span-3 font-semibold text-gray-900 dark:text-white">Bendahara
                    Pengeluaran Pembantu Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Kalimantan
                    Selatan</span>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2 border-b border-gray-50 dark:border-gray-800">
                <span class="font-medium text-gray-400">Uang Sebesar</span>
                <span class="md:col-span-3 font-bold text-blue-600 dark:text-blue-400 text-lg">Rp
                    {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2 border-b border-gray-50 dark:border-gray-800">
                <span class="font-medium text-gray-400">Terbilang</span>
                <span
                    class="md:col-span-3 italic font-semibold text-gray-800 dark:text-gray-200 capitalize">"{{ spj_terbilang($grandTotal) }}
                    rupiah"</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 py-2">
                <span class="font-medium text-gray-400">Untuk Pembayaran</span>
                <span class="md:col-span-3 text-gray-900 dark:text-white leading-relaxed">
                    Pembayaran perjalanan {{ $isJakarta ? 'keluar' : 'dalam' }} provinsi Kalimantan
                    Selatan dalam rangka {{ $kegiatan ?? '' }} ke
                    {{ $lokasi ?? '' }} dengan no SPT : {{ $spt->nomor_spt ?? '' }} a.n
                    <span
                        x-text="penerimaNama + '{{ $countPegawai > 1 ? ' dkk' : '' }}'"></span>{{ $opText }}
                </span>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
            <div class="text-right text-xs text-gray-500 dark:text-gray-400 mb-6">
                Banjarbaru, <span x-text="formatTanggalIndo(tanggalKuitansi)"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div
                    class="text-center p-4 border border-gray-50 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-12">
                        Bendahara Pengeluaran Pembantu</p>
                    <p class="font-bold underline text-gray-900 dark:text-white text-sm">
                        {{ $bendaharaNama }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">NIP. {{ $bendaharaNip }}</p>
                </div>
                <div
                    class="text-center p-4 border border-gray-50 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-12">Yang
                        Menerima</p>
                    <p class="font-bold underline text-gray-900 dark:text-white text-sm"
                        x-text="penerimaNama"></p>
                    <p class="text-xs text-gray-400 mt-1">NIP. <span x-text="penerimaNip"></span></p>
                </div>
                <div
                    class="text-center p-4 border border-gray-50 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-12">Setuju
                        Dibayar: KPA</p>
                    <p class="font-bold underline text-gray-900 dark:text-white text-sm">{{ $kpaNama }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">NIP. {{ $kpaNip }}</p>
                </div>
                <div
                    class="text-center p-4 border border-gray-50 dark:border-gray-800 rounded-xl bg-gray-50/50 dark:bg-gray-800/30">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-12">
                        Mengetahui: PPTK</p>
                    <p class="font-bold underline text-gray-900 dark:text-white text-sm">{{ $pptkNama }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">NIP. {{ $pptkNip }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

