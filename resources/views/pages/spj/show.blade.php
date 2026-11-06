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
        tanggalKuitansi: '{{ \Carbon\Carbon::parse($spt->tanggal_selesai_efektif ?? $spt->tanggal_mulai_efektif)->format('Y-m-d') }}',
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
        selectedUangHarian: '',
        paguPanduan: {
            tiket: {{ (float) ($maxTiketPesawat ?? 0) }},
            transport: {{ (float) ($maxTransport ?? 0) }},
            penginapan: {{ (float) ($maxPenginapan ?? 0) }}
        },
        showTransportBreakdown: false,
        transportBreakdown: {
            bjb_pergi: 0,
            bjb_pulang: 0,
            tuj_pergi: 0,
            tuj_pulang: 0,
        },
        syncTransportFromBreakdown() {
            this.rincianForm.transport = (parseFloat(this.transportBreakdown.bjb_pergi) || 0) +
                                         (parseFloat(this.transportBreakdown.bjb_pulang) || 0) +
                                         (parseFloat(this.transportBreakdown.tuj_pergi) || 0) +
                                         (parseFloat(this.transportBreakdown.tuj_pulang) || 0);
        },
        onUangHarianChange(event) {
            const sel = event.target;
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) return;

            const val = parseFloat(opt.value) || 0;
            this.rincianForm.uang_harian = val;
            this.selectedUangHarian = opt.value;

            // Perbarui nilai pagu panduan secara reaktif dari dataset option
            if (opt.dataset.tiket !== undefined) {
                this.paguPanduan.tiket = parseFloat(opt.dataset.tiket) || 0;
            }
            if (opt.dataset.transport !== undefined) {
                this.paguPanduan.transport = parseFloat(opt.dataset.transport) || 0;
            }
            if (opt.dataset.penginapan !== undefined) {
                this.paguPanduan.penginapan = parseFloat(opt.dataset.penginapan) || 0;
            }

            // Otomatis isi breakdown rute & jumlahkan transport jika tersedia
            if (opt.dataset.bjbPergi !== undefined && (parseFloat(opt.dataset.transport) > 0)) {
                this.transportBreakdown.bjb_pergi = parseFloat(opt.dataset.bjbPergi) || 0;
                this.transportBreakdown.bjb_pulang = parseFloat(opt.dataset.bjbPulang) || 0;
                this.transportBreakdown.tuj_pergi = parseFloat(opt.dataset.tujPergi) || 0;
                this.transportBreakdown.tuj_pulang = parseFloat(opt.dataset.tujPulang) || 0;
                this.syncTransportFromBreakdown();
            } else if (parseFloat(opt.dataset.transport) > 0 && (!this.rincianForm.transport || this.rincianForm.transport == 0)) {
                this.rincianForm.transport = parseFloat(opt.dataset.transport);
            }
        },
        @php
            $defaultUangHarian = $uangHarianOptions[$lokasiEfektif ?? ''] ?? (!empty($uangHarianOptions) ? reset($uangHarianOptions) : 0);
        @endphp
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
        },
        get subtotalUangHarian() {
            return (parseInt(this.rincianForm.jumlah_hari) || 0) * (parseFloat(this.rincianForm.uang_harian) || 0);
        },
        get subtotalTiket() {
            return (parseFloat(this.rincianForm.tiket_pesawat_pergi) || 0) + (parseFloat(this.rincianForm.tiket_pesawat_pulang) || 0);
        },
        get totalRincianLive() {
            return this.subtotalUangHarian +
                   this.subtotalTiket +
                   (parseFloat(this.rincianForm.transport) || 0) +
                   (parseFloat(this.rincianForm.penginapan) || 0);
        },
        openAddRincian(item = null) {
            this.isEditRincian = false;
            this.showTransportBreakdown = false;
            this.transportBreakdown = { bjb_pergi: 0, bjb_pulang: 0, tuj_pergi: 0, tuj_pulang: 0 };
            const defaultUang = '{{ $defaultUangHarian }}';
            const initialUang = (item && item.uang_harian && item.uang_harian !== '0') ? item.uang_harian : defaultUang;
            this.selectedUangHarian = initialUang;
            this.rincianForm = item ? { 
                ...item,
                uang_harian: initialUang
            } : { 
                pegawai_id: '', 
                uraian_id: '',
                uraian: '', 
                jumlah_hari: '{{ $spt->durasi_hari }}', 
                uang_harian: defaultUang, 
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
            this.showTransportBreakdown = false;
            this.transportBreakdown = { bjb_pergi: 0, bjb_pulang: 0, tuj_pergi: 0, tuj_pulang: 0 };
            this.rincianForm = { ...item };
            this.selectedUangHarian = item.uang_harian || '';
            this.showRincianModal = true;
        }
    }">
        @include('pages.spj.partials.alert-nomor-spt')

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

            @include('pages.spj.partials.summary-info')

            <div class="mt-8">
                @include('pages.spj.partials.tab-nav')

                <!-- Tab Content -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 min-h-[300px]">
                    @include('pages.spj.partials.tab-kuitansi')
                    @include('pages.spj.partials.tab-rincian')
                    @include('pages.spj.partials.tab-pengeluaran-rill')
                </div>

                @include('pages.spj.partials.modal-rincian')
                @include('pages.spj.partials.modal-edit-spt')
            </div>
        </x-common.component-card>
    </div>
@endsection
