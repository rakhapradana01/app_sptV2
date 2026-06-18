@extends('layouts.app')

@section('content')
    @php
        $isStandalone = $spt->isStandalone();
        $isJakarta = $isStandalone ? (\Illuminate\Support\Str::contains(strtolower($spt->lokasi ?? ''), 'jakarta')) : (($spt->notaDinas?->jenis_perjalanan ?? '') === 'luar_daerah');
        $grandTotal = $spt->spj_rincians_efektif->sum('total');
        $countPegawai = count($spt->pegawais_efektif);
        $firstPegawai = $spt->pegawais_efektif->first();
        $penerimaText = $firstPegawai ? ($firstPegawai->nama . ($countPegawai > 1 ? " dkk" : "")) : '';
        $opText = " (" . $countPegawai . " OP)";

        // Effective field variables (standalone or via Nota Dinas)
        $tanggalMulai   = $spt->tanggal_mulai_efektif;
        $tanggalSelesai = $spt->tanggal_selesai_efektif;
        $lokasi         = $spt->lokasi_efektif;
        $kegiatan       = $spt->kegiatan_efektif;

        $buatPembayaran = "Pembayaran perjalanan " . ($isJakarta ? 'keluar' : 'dalam') . " provinsi Kalimantan Selatan dalam rangka " . ($kegiatan ?? '') . " ke " . ($lokasi ?? '') . " dengan no SPT : " . ($spt->nomor_spt ?? '') . " a.n " . $penerimaText . $opText;

        $bendaharaNama = 'NORMILA SARI, SE';
        $bendaharaNip = '19801221 201001 2 003';
        $kpaNama = 'ADYA FERINA, S.E., M.Ak';
        $kpaNip = '19860206 201101 2 005';
        $subKegiatan = $spt->subKegiatan ?? $spt->notaDinas?->subKegiatan;
        $pptkNama = $subKegiatan?->pegawai?->nama ?? 'YENNI NURRAHMI,  SE., M.M';
        $pptkNip = $subKegiatan?->pegawai?->nip ?? '19810503 200501 2 017';

        if (!function_exists('spj_terbilang')) {
            function spj_terbilang($angka)
            {
                $angka = abs($angka);
                $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
                $terbilang = "";
                if ($angka < 12) {
                    $terbilang = " " . $baca[$angka];
                } else if ($angka < 20) {
                    $terbilang = spj_terbilang($angka - 10) . " belas";
                } else if ($angka < 100) {
                    $terbilang = spj_terbilang(floor($angka / 10)) . " puluh " . spj_terbilang($angka % 10);
                } else if ($angka < 200) {
                    $terbilang = " seratus " . spj_terbilang($angka - 100);
                } else if ($angka < 1000) {
                    $terbilang = spj_terbilang(floor($angka / 100)) . " ratus " . spj_terbilang($angka % 100);
                } else if ($angka < 2000) {
                    $terbilang = " seribu " . spj_terbilang($angka - 1000);
                } else if ($angka < 1000000) {
                    $terbilang = spj_terbilang(floor($angka / 1000)) . " ribu " . spj_terbilang($angka % 1000);
                } else if ($angka < 1000000000) {
                    $terbilang = spj_terbilang(floor($angka / 1000000)) . " juta " . spj_terbilang($angka % 1000000);
                }
                return trim(preg_replace('/\s+/', ' ', $terbilang));
            }
        }
    @endphp
    <x-common.page-breadcrumb pageTitle="Detail SPJ" />

    <div class="space-y-6" x-data="{ 
                                activeTab: 'kuitansi',
                                showRincianModal: false,
                                isEditRincian: false,
                                showSptModal: false,
                                penerimaId: '{{ $firstPegawai->id ?? '' }}',
                                penerimaNama: '{{ $firstPegawai->nama ?? '-' }}',
                                penerimaNip: '{{ $firstPegawai->nip ?? '-' }}',
                                tanggalKuitansi: '{{ \Carbon\Carbon::parse($spt->tanggal_selesai_efektif ?? $spt->tanggal_mulai_efektif)->format("Y-m-d") }}',
                                changePenerima(id, nama, nip) {
                                    this.penerimaId = id;
                                    this.penerimaNama = nama;
                                    this.penerimaNip = nip;
                                },
                                formatTanggalIndo(dateStr) {
                                    if (!dateStr) return '';
                                    const months = [
                                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    const parts = dateStr.split('-');
                                    if (parts.length !== 3) return dateStr;
                                    const day = parseInt(parts[2], 10);
                                    const month = months[parseInt(parts[1], 10) - 1];
                                    const year = parts[0];
                                    return `${day} ${month} ${year}`;
                                },
                                formatBulanTahunIndo(dateStr) {
                                    if (!dateStr) return '';
                                    const months = [
                                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    const parts = dateStr.split('-');
                                    if (parts.length !== 3) return dateStr;
                                    const month = months[parseInt(parts[1], 10) - 1];
                                    const year = parts[0];
                                    return `${month} ${year}`;
                                },
                                rincianForm: { 
                                    pegawai_id: '', 
                                    uraian_id: '',
                                    uraian: '', 
                                    jumlah_hari: '{{ $spt->durasi_hari }}', 
                                    uang_harian: '', 
                                    tiket_pesawat_pergi: 0, 
                                    tiket_pesawat_pulang: 0, 
                                    transport: 0,
                                    penginapan: 0,
                                    kode_rekening: '{{ $subKegiatan->nomor_rekening ?? '' }}' 
                                },
                                openAddRincian(item = null) {
                                    this.isEditRincian = false;
                                    this.rincianForm = item ? { ...item } : { 
                                        pegawai_id: '', 
                                        uraian_id: '',
                                        uraian: '', 
                                        jumlah_hari: '{{ $spt->durasi_hari }}', 
                                        uang_harian: '', 
                                        tiket_pesawat_pergi: 0, 
                                        tiket_pesawat_pulang: 0, 
                                        transport: 0,
                                        penginapan: 0,
                                        kode_rekening: '{{ $subKegiatan->nomor_rekening ?? '' }}' 
                                    };
                                    this.showRincianModal = true;
                                },
                                openEditRincian(item) {
                                    this.isEditRincian = true;
                                    this.rincianForm = { ...item };
                                    this.showRincianModal = true;
                                }
                            }">
        @if(!$spt->has_real_nomor)
            <div class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 rounded-2xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg text-amber-700 dark:text-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-800 dark:text-amber-200 text-sm">Nomor SPT Belum Diisi Resmi</h4>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">SPT ini masih menggunakan nomor template/placeholder. Silakan input nomor SPT resmi.</p>
                    </div>
                </div>
                <button @click="showSptModal = true" class="px-4 py-2 text-xs font-semibold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                    Input Nomor SPT Resmi
                </button>
            </div>
        @endif

        <x-common.component-card>
            <x-slot name="title">
                <div class="flex items-center gap-2">
                    <span>SPJ: {{ $spt->nomor_spt }}</span>
                    <button @click="showSptModal = true" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" title="Edit Nomor SPT">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                </div>
            </x-slot>
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

            <div class="mt-8">
                <!-- Tab Headers -->
                <div class="flex flex-wrap border-b border-gray-200 dark:border-gray-700 gap-1">
                    <button @click="activeTab = 'kuitansi'"
                        :class="activeTab === 'kuitansi' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Kuitansi
                    </button>
                    <button @click="activeTab = 'rincian'"
                        :class="activeTab === 'rincian' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Rincian
                    </button>
                    <button @click="activeTab = 'pengeluaran_rill'"
                        :class="activeTab === 'pengeluaran_rill' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="px-4 py-2 font-medium text-sm border-b-2 transition-all">
                        Daftar pengeluaran rill
                    </button>
                </div>

                <!-- Tab Content -->
                <div
                    class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 min-h-[300px]">
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

                    <!-- Rincian -->
                    <div x-show="activeTab === 'rincian'" x-cloak x-transition>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 dark:bg-gray-700">
                                    <tr>
                                        <th class="p-2 text-left">No</th>
                                        <th class="p-2 text-left">Uraian</th>
                                        <th class="p-2 text-right">Jumlah</th>
                                        <th class="p-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($spt->pegawais_efektif as $index => $pegawai)
                                        @php
                                            $rincian = $spt->spj_rincians_efektif->where('pegawai_id', $pegawai->id)->first();
                                        @endphp
                                        <tr class="border-b">
                                            <td class="p-2">{{ $index + 1 }}</td>
                                            <td class="p-2">
                                                <p class="font-medium text-gray-900">{{ $pegawai->nama }}</p>
                                                <p class="text-xs text-gray-500">{{ $pegawai->nip }}</p>
                                            </td>
                                            <td class="p-2 text-right font-semibold text-blue-600">
                                                {{ $rincian ? 'Rp ' . number_format($rincian->total, 0, ',', '.') : 'Rp -' }}
                                            </td>
                                            <td class="p-2 text-center">
                                                <button @click="openAddRincian({ 
                                                                                        pegawai_id: {{ $pegawai->id }}, 
                                                                                        uraian_id: '{{ $rincian->uraian_id ?? '' }}',
                                                                                        uraian: '{{ $pegawai->nama }}', 
                                                                                        jumlah_hari: '{{ $rincian->jumlah_hari ?? floor($spt->durasi_hari) }}',
                                                                                        uang_harian: '{{ $rincian->uang_harian ?? '' }}',
                                                                                        tiket_pesawat_pergi: '{{ $rincian->tiket_pesawat_pergi ?? 0 }}',
                                                                                        tiket_pesawat_pulang: '{{ $rincian->tiket_pesawat_pulang ?? 0 }}',
                                                                                        transport: '{{ $rincian->transport ?? 0 }}',
                                                                                        penginapan: '{{ $rincian->penginapan ?? 0 }}',
                                                                                        kode_rekening: @js($subKegiatan?->nomor_rekening),
                                                                                    })"
                                                    class="text-green-600 hover:text-green-800 p-1"
                                                    title="{{ $rincian ? 'Edit Rincian' : 'Tambah Rincian' }}">
                                                    @if($rincian)
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M12 4.5v15m7.5-7.5h-15" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $grandTotal = $spt->spj_rincians_efektif->sum('total');
                                    @endphp
                                    <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                                        <td colspan="2" class="p-2 text-right">TOTAL</td>
                                        <td class="p-2 text-right text-blue-600">Rp
                                            {{ number_format($grandTotal, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pengeluaran Rill -->
                    <div x-show="activeTab === 'pengeluaran_rill'" x-cloak x-transition>
                        <div class="flex justify-between items-center mb-6">
                            <h5 class="font-bold text-gray-800 dark:text-white">Daftar Pengeluaran Riil (DPR)</h5>
                            <span
                                class="px-3 py-1 text-xs font-semibold {{ $isJakarta ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-200' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }} rounded-full">
                                {{ $isJakarta ? 'Perjalanan Luar Daerah / Luar Provinsi' : 'Perjalanan Dalam Daerah / Dalam Provinsi' }}
                            </span>
                        </div>

                        @if(!$isJakarta)
                            <div
                                class="bg-gray-100 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center max-w-2xl mx-auto space-y-3">
                                <div
                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto text-gray-400 text-xl">
                                    <svg class="w-6 h-6 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h6 class="font-bold text-gray-800 dark:text-white text-base">Tidak Memerlukan DPR</h6>
                                <p class="text-sm text-gray-500 max-w-md mx-auto leading-relaxed">
                                    Daftar Pengeluaran Riil (DPR) taksi bandara hanya diterbitkan dan berlaku untuk perjalanan
                                    dinas luar provinsi/daerah. Perjalanan dalam daerah/provinsi ini tidak memerlukan pelaporan
                                    pengeluaran riil.
                                </p>
                            </div>
                        @else
                            <div class="space-y-6 max-w-4xl mx-auto">
                                @php $hasDpr = false; @endphp
                                @foreach($spt->pegawais_efektif as $pegawai)
                                    @php
                                        $rincian = $spt->spj_rincians_efektif->where('pegawai_id', $pegawai->id)->first();
                                    @endphp
                                    @if($rincian && ($rincian->transport > 0))
                                        @php $hasDpr = true; @endphp
                                        <div
                                            class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm p-6 space-y-6 relative overflow-hidden">
                                            <div
                                                class="absolute top-0 right-0 w-24 h-24 bg-amber-500/5 rounded-bl-full pointer-events-none">
                                            </div>

                                            <div
                                                class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-4">
                                                <div>
                                                    <h6 class="font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wide">
                                                        SURAT PERNYATAAN DAFTAR PENGELUARAN RIIL</h6>
                                                    <p class="text-xs text-gray-400 mt-0.5">Pegawai: <span
                                                            class="font-semibold text-gray-700 dark:text-gray-300">{{ $pegawai->nama }}</span>
                                                    </p>
                                                </div>
                                                <span
                                                    class="text-xs font-semibold text-amber-600 bg-amber-50 dark:bg-amber-950/40 dark:text-amber-300 px-2.5 py-1 rounded">Halaman
                                                    1 & 2 Aktif</span>
                                            </div>

                                            <p
                                                class="text-xs text-gray-500 leading-relaxed bg-gray-50 dark:bg-gray-800/40 p-4 rounded-xl border border-gray-50 dark:border-gray-800">
                                                "Berdasarkan Surat Perintah Perjalanan Dinas Nomor :
                                                <strong>{{ $spt->sppd_efektif?->nomor_sppd ?? '800.1.11.1/    /BPKAD/' . Carbon\Carbon::parse($tanggalMulai)->format('Y') }}</strong>,
                                                tanggal
                                                <strong>{{ Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}</strong>
                                                dengan ini kami menyatakan dengan sesungguhnya bahwa biaya di bawah ini benar-benar
                                                dikeluarkan untuk taksi bandara."
                                            </p>

                                            <div class="overflow-x-auto">
                                                <table class="w-full text-xs">
                                                    <thead>
                                                        <tr class="bg-gray-50 dark:bg-gray-800 text-gray-400 uppercase tracking-wider">
                                                            <th class="p-3 text-left">No</th>
                                                            <th class="p-3 text-left">Uraian Pengeluaran</th>
                                                            <th class="p-3 text-right">Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                                            <td class="p-3 font-semibold text-gray-900 dark:text-white">1</td>
                                                            <td class="p-3 text-gray-900 dark:text-white leading-relaxed">
                                                                Biaya Taksi : Tempat Kedudukan (Banjarbaru) -
                                                                ({{ $lokasi }}) PP
                                                            </td>
                                                            <td class="p-3 text-right font-bold text-amber-600 dark:text-amber-400">
                                                                Rp {{ number_format($rincian->transport, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                        <tr class="bg-gray-50/50 dark:bg-gray-800/20 font-bold">
                                                            <td colspan="2" class="p-3 text-right text-gray-500 uppercase">Total
                                                                Pengeluaran Riil</td>
                                                            <td class="p-3 text-right text-amber-600 dark:text-amber-400">
                                                                Rp {{ number_format($rincian->transport, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs">
                                                <div
                                                    class="text-center p-3 border border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-lg">
                                                    <p class="font-semibold text-gray-400 uppercase tracking-wider mb-8">Mengetahui:
                                                        Kuasa Pengguna Anggaran</p>
                                                    <p class="font-bold underline text-gray-900 dark:text-white">{{ $kpaNama }}</p>
                                                    <p class="text-gray-400 mt-0.5">NIP. {{ $kpaNip }}</p>
                                                </div>
                                                <div
                                                    class="text-center p-3 border border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-800/10 rounded-lg">
                                                    <p class="text-gray-400 mb-1">Banjarbaru,
                                                        {{ Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d F Y') }}
                                                    </p>
                                                    <p class="font-semibold text-gray-400 uppercase tracking-wider mb-7">Pelaksana SPPD
                                                    </p>
                                                    <p class="font-bold underline text-gray-900 dark:text-white">{{ $pegawai->nama }}
                                                    </p>
                                                    <p class="text-gray-400 mt-0.5">NIP. {{ $pegawai->nip ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$hasDpr)
                                    <div
                                        class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-8 text-center max-w-2xl mx-auto">
                                        <p class="text-sm text-gray-500">Belum ada rincian biaya transport yang diisi untuk pegawai.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Modal Rincian -->
                <div x-show="showRincianModal"
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                    x-cloak x-transition>
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
                        @click.away="showRincianModal = false">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white"
                                x-text="isEditRincian ? 'Edit Rincian Biaya' : 'Tambah Rincian Biaya'"></h3>
                            <button @click="showRincianModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('spj.rincian.store', $spt->id) }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            <input type="hidden" name="pegawai_id" x-bind:value="rincianForm.pegawai_id">

                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode
                                        Rekening</label>
                                    <input type="text" name="kode_rekening" x-model="rincianForm.kode_rekening"
                                        class="form-control w-full bg-gray-50" readonly>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Pagu Uraian (Mengurangi OK)
                                    </label>
                                    <select name="uraian_id" x-model="rincianForm.uraian_id" class="form-control w-full">
                                        <option value="">-- Tanpa Pagu Uraian --</option>
                                        @foreach(($subKegiatan->uraians ?? []) as $uraian)
                                            <option value="{{ $uraian->id }}">
                                                {{ $uraian->uraian }} (Sisa: {{ $uraian->ok_total - $uraian->ok_terpakai }} OK)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uraian
                                        Biaya</label>
                                    <input type="text" name="uraian" x-model="rincianForm.uraian"
                                        class="form-control w-full" placeholder="Contoh: Uang Harian">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah
                                        Hari</label>
                                    <input type="number" name="jumlah_hari" x-model="rincianForm.jumlah_hari"
                                        class="form-control w-full" placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Uang
                                        Harian</label>
                                    <select name="uang_harian" x-model="rincianForm.uang_harian"
                                        class="form-control w-full">
                                        <option value="">--Pilih--</option>
                                        <option value="380000">Kalimantan Selatan (Rp 380.000)</option>
                                        <option value="530000">Jakarta (Rp 530.000)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiket
                                        Pesawat Pergi</label>
                                    <input type="number" name="tiket_pesawat_pergi"
                                        x-model="rincianForm.tiket_pesawat_pergi" class="form-control w-full"
                                        placeholder="0">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tiket
                                        Pesawat Pulang</label>
                                    <input type="number" name="tiket_pesawat_pulang"
                                        x-model="rincianForm.tiket_pesawat_pulang" class="form-control w-full"
                                        placeholder="0">
                                </div>
                                <div class="col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Transport</label>
                                    <input type="number" name="transport" x-model="rincianForm.transport"
                                        class="form-control w-full" placeholder="0">
                                </div>
                                <div class="col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Penginapan</label>
                                    <input type="number" name="penginapan" x-model="rincianForm.penginapan"
                                        class="form-control w-full" placeholder="0">
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showRincianModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit SPT -->
                <div x-show="showSptModal"
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm"
                    x-cloak x-transition>
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
                        @click.away="showSptModal = false">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Input / Edit Nomor SPT</h3>
                            <button @click="showSptModal = false" class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form action="{{ route('spt.updateNomor', $spt->id) }}" method="POST" class="p-6 space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor SPT</label>
                                <input type="text" name="nomor_spt" required value="{{ $spt->nomor_spt }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white form-control">
                            </div>
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="showSptModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </div>
@endsection