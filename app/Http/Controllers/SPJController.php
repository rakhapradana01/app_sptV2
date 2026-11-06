<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use App\Models\Spt;
use App\Models\SpjRincian;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Carbon\Carbon;

class SPJController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Spt::with(['notaDinas.pegawais', 'pegawais']);

        if ($user) {
            if ($user->role->name === 'kepala_sub_bidang') {
                if (!$user->sub_bidang_id) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('sub_bidang_id', $user->sub_bidang_id);
                }
            } elseif (in_array($user->role->name, ['kepala_bidang', 'admin'])) {
                if ($user->bidang_id) {
                    $query->where('bidang_id', $user->bidang_id);
                }
            }
        }

        $spt = $query->latest()->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json($spt);
        }

        return view('pages.spj.index', compact('spt'));
    }

    public function show($id)
    {
        $spt = Spt::with([
            'notaDinas.pegawais',
            'notaDinas.spjRincians.pegawai',
            'notaDinas.subKegiatan.pegawai',
            'notaDinas.subKegiatan.uraians',
            'pegawais',
            'spjRincians.pegawai',
            'subKegiatan.pegawai',
            'subKegiatan.uraians'
        ])->findOrFail($id);

        if (!$spt->has_real_nomor) {
            return redirect()->route('spj.index')->with('error', 'Harap isi nomor SPT terlebih dahulu sebelum mengakses/membuat SPJ.');
        }

        // --- TAMBAHAN LUAS: Ambil data lengkap master_shs untuk referensi ---
        $lokasiEfektif = $spt->lokasi_efektif;
        $tahunAnggaran = $spt->tahun_anggaran;

        // Daftar opsi uang harian dari master_shs
        $uangHarianOptions = [];
        $shsByLokasi = [];
        // Referensi maksimum biaya dari master_shs
        $maxTiketPesawat = 0;
        $maxPenginapan = 0;
        $maxTransport = 0;

        if ($lokasiEfektif && $tahunAnggaran) {
            $masterShsRecords = \App\Models\MasterShs::where('tahun_anggaran', $tahunAnggaran)
                ->where('is_active', true)
                ->get();

            foreach ($masterShsRecords as $ms) {
                $lokasi = $ms->lokasi_tujuan;
                // Opsi uang harian
                if (!isset($uangHarianOptions[$lokasi])) {
                    $uangHarianOptions[$lokasi] = $ms->uang_harian ?? 0;
                }
                $totalTrans = (float) ($ms->transport_bjb_rumah_bandara + $ms->transport_bjb_bandara_rumah + $ms->transport_tujuan_bandara_hotel + $ms->transport_tujuan_hotel_bandara);
                $shsByLokasi[$lokasi] = [
                    'uang_harian' => (float) ($ms->uang_harian ?? 0),
                    'tiket_pesawat' => (float) ($ms->tiket_pesawat ?? 0),
                    'penginapan' => (float) ($ms->penginapan ?? 0),
                    'transport' => $totalTrans,
                    'bjb_pergi' => (float) ($ms->transport_bjb_rumah_bandara ?? 0),
                    'bjb_pulang' => (float) ($ms->transport_bjb_bandara_rumah ?? 0),
                    'tuj_pergi' => (float) ($ms->transport_tujuan_bandara_hotel ?? 0),
                    'tuj_pulang' => (float) ($ms->transport_tujuan_hotel_bandara ?? 0),
                ];
                // Referensi maksimum biaya (ambil record pertama yang cocok)
                if ($maxTiketPesawat == 0) {
                    $maxTiketPesawat = $ms->tiket_pesawat ?? 0;
                }
                if ($maxPenginapan == 0) {
                    $maxPenginapan = $ms->penginapan ?? 0;
                }
                // Total transport dari master_shs
                if ($maxTransport == 0) {
                    $maxTransport = $totalTrans;
                }
            }
        }
        // Default jika tidak ada data
        if (empty($uangHarianOptions)) {
            $uangHarianOptions = ['' => '-- Pilih Lokasi Terlebih Dahulu --'];
            $maxTiketPesawat = 0;
            $maxPenginapan = 0;
            $maxTransport = 0;
        }
        // ------------------------------------------

        return view('pages.spj.show', compact('spt', 'uangHarianOptions', 'maxTiketPesawat', 'maxPenginapan', 'maxTransport', 'lokasiEfektif', 'tahunAnggaran', 'shsByLokasi'));
    }

    public function storeRincian(Request $request, $id)
    {
        $spt = Spt::findOrFail($id);

        // --- TAMBAHAN: Cari standar uang harian dan referensi maksimum dari master_shs ---
        $lokasiEfektif = $spt->lokasi_efektif; // ambil dari SPT atau NotaDinas
        $tahunAnggaran = $spt->tahun_anggaran;

        $standardUangHarian = 0;
        $maxTiketPesawat = 0;
        $maxPenginapan = 0;
        $maxTransport = 0;

        if ($lokasiEfektif && $tahunAnggaran) {
            $masterShs = \App\Models\MasterShs::where('lokasi_tujuan', $lokasiEfektif)
                ->where('tahun_anggaran', $tahunAnggaran)
                ->where('is_active', true)
                ->first();

            if ($masterShs) {
                $standardUangHarian = $masterShs->uang_harian ?? 0;
                $maxTiketPesawat = $masterShs->tiket_pesawat ?? 0;
                $maxPenginapan = $masterShs->penginapan ?? 0;
                // Total transport dari master_shs
                $maxTransport = $masterShs->transport_bjb_rumah_bandara + $masterShs->transport_bjb_bandara_rumah
                    + $masterShs->transport_tujuan_bandara_hotel + $masterShs->transport_tujuan_hotel_bandara;
            }
        }
        // --------------------------------------------------------------

        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'uraian_id' => 'nullable|exists:uraians,id',
            'kode_rekening' => 'nullable|string',
            'jumlah_hari' => 'required|integer',
            'uang_harian' => 'required|numeric',
            'tiket_pesawat_pergi' => ['nullable', 'numeric', 'max:' . $maxTiketPesawat],
            'tiket_pesawat_pulang' => ['nullable', 'numeric', 'max:' . $maxTiketPesawat],
            'transport' => ['nullable', 'numeric', 'max:' . $maxTransport],
            'penginapan' => ['nullable', 'numeric', 'max:' . $maxPenginapan],
        ]);

        $uangHarian = $request->uang_harian;
        // Gunakan standar dari master_shs jika belum diisi atau sesuai kebutuhan
        // Jika user menginput nilai, gunakan nilai user; jika tidak, gunakan standar
        // Di sini kita tetapkan nilai awal, dan user bisa mengubahnya di form
        $effectiveUangHarian = $uangHarian ?: $standardUangHarian;

        $total = ($request->jumlah_hari * $effectiveUangHarian) +
            ($request->tiket_pesawat_pergi ?? 0) +
            ($request->tiket_pesawat_pulang ?? 0) +
            ($request->transport ?? 0) +
            ($request->penginapan ?? 0);

        SpjRincian::updateOrCreate(
            [
                'nota_dinas_id' => $spt->nota_dinas_id,
                'spt_id'        => $spt->isStandalone() ? $spt->id : null,
                'pegawai_id'    => $request->pegawai_id
            ],
            [
                'kode_rekening' => $request->kode_rekening,
                'uraian_id' => $request->uraian_id,
                'jumlah_hari' => $request->jumlah_hari,
                'uang_harian' => $effectiveUangHarian,
                'tiket_pesawat_pergi' => $request->tiket_pesawat_pergi ?? 0,
                'tiket_pesawat_pulang' => $request->tiket_pesawat_pulang ?? 0,
                'transport' => $request->transport ?? 0,
                'penginapan' => $request->penginapan ?? 0,
                'total' => $total,
            ]
        );

        return redirect()->back()->with('success', 'Rincian berhasil disimpan');
    }

    public function exportExcel($id)
    {
        $spt = Spt::with([
            'notaDinas.pegawais',
            'notaDinas.spjRincians.pegawai',
            'notaDinas.subKegiatan.pegawai',
            'pegawais',
            'spjRincians.pegawai',
            'subKegiatan.pegawai'
        ])->findOrFail($id);

        $templatePath = resource_path('tamplates/tamplate_spj.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template SPJ Excel tidak ditemukan.');
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($templatePath);

        // Effective Standalone Fields
        $isStandalone = $spt->isStandalone();
        $pegawais = $spt->pegawais_efektif;
        $rincians = $spt->spj_rincians_efektif;
        $subKegiatan = $spt->subKegiatan ?? $spt->notaDinas?->subKegiatan;
        $nomorRekening = $subKegiatan?->nomor_rekening ?? '';
        $kegiatan = $spt->kegiatan_efektif;
        $lokasi = $spt->lokasi_efektif;
        $tanggalMulai = $spt->tanggal_mulai_efektif;
        $tanggalSelesai = $spt->tanggal_selesai_efektif;
        $sppd = $spt->sppd_efektif;
        $nomorSppd = $sppd?->nomor_sppd ?? '';

        $isJakarta = $isStandalone ? (\Illuminate\Support\Str::contains(strtolower($lokasi), 'jakarta')) : (($spt->notaDinas->jenis_perjalanan ?? '') === 'luar_daerah');

        $kuitansiTemplate = $spreadsheet->getSheetByName('Kuitansi_bu_adya');
        $rincianTemplate = $spreadsheet->getSheetByName('R.Perjaldin_bpkad');
        $dprTemplate = $spreadsheet->getSheetByName('DPR_jkt');
        $tandaTerimaSheet = $spreadsheet->getSheetByName('TT_bpkad');

        if ($rincians->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada rincian biaya SPJ yang diisi.');
        }

        $bendaharaNama = 'NORMILA SARI, SE';
        $bendaharaNip = '19801221 201001 2 003';
        $kpaNama = 'ADYA FERINA, S.E., M.Ak';
        $kpaNip = '19860206 201101 2 005';
        $pptkNama = $subKegiatan->pegawai->nama ?? 'YENNI NURRAHMI,  SE., M.M';
        $pptkNip = $subKegiatan->pegawai->nip ?? '19810503 200501 2 017';

        $count = count($rincians);
        $maxSlots = 6;
        $deletedCount = ($maxSlots - $count) * 5;
        $newTotalRow = 34 - $deletedCount;

        // 1. Process Kuitansi Sheet (Single sheet with Grand Total of all employees)
        if ($kuitansiTemplate) {
            $penerimaId = request('penerima_id');
            $firstPegawai = $pegawais->where('id', $penerimaId)->first() ?? $pegawais->first();
            $countPegawai = count($pegawais);

            $tanggalKuitansiRaw = request('tanggal_kuitansi');
            $tanggalKuitansi = $tanggalKuitansiRaw ? Carbon::parse($tanggalKuitansiRaw) : Carbon::parse($tanggalSelesai ?? $tanggalMulai);

            // Rename to 'Kuitansi'
            $kuitansiTemplate->setTitle('Kuitansi');

            // Fill Kuitansi
            $tanggalFormat = $tanggalKuitansi->translatedFormat('d F Y');
            $kuitansiTemplate->setCellValue('I2', $tanggalFormat);
            $kuitansiTemplate->setCellValue('D7', ($nomorRekening ? $nomorRekening . '.5.1.02.04.01.0001' : ''));
            $kuitansiTemplate->setCellValue('D8', Carbon::parse($tanggalMulai)->format('Y'));
            $kuitansiTemplate->setCellValue('D12', "='TT_bpkad'!E" . $newTotalRow);

            // Set terbilang in D13 from total
            $grandTotalValue = $rincians->sum('total');
            $terbilangText = $this->terbilang($grandTotalValue) . " Rupiah";
            $kuitansiTemplate->setCellValue('D13', $terbilangText);

            $kuitansiTemplate->setCellValue('D17', "='TT_bpkad'!E" . $newTotalRow);
            $kuitansiTemplate->setCellValue('D21', "='TT_bpkad'!E" . $newTotalRow);
            $kuitansiTemplate->setCellValue('D25', "='TT_bpkad'!E" . $newTotalRow);

            $penerimaText = $firstPegawai ? ($firstPegawai->nama . ($countPegawai > 1 ? " dkk" : "")) : '';
            $opText = " (" . $countPegawai . " OP)";
            $buatPembayaran = "Pembayaran perjalanan " . ($isJakarta ? 'keluar' : 'dalam') . " provinsi Kalimantan Selatan  " . ($kegiatan ?? '') . " ke " . ($lokasi ?? '') . " dengan no SPT : " . ($spt->nomor_spt ?? '') . " a.n " . $penerimaText . $opText;
            $kuitansiTemplate->setCellValue('D14', $buatPembayaran);
            $kuitansiTemplate->setCellValue('D27', $subKegiatan->nama_kegiatan ?? '');

            $bulanTahunFormat = "Banjarbaru, " . $tanggalKuitansi->translatedFormat('d F Y');
            $kuitansiTemplate->setCellValue('F30', $bulanTahunFormat);

            $kuitansiTemplate->setCellValue('F37', $firstPegawai ? $firstPegawai->nama : '');
            $kuitansiTemplate->setCellValue('F38', ($firstPegawai && $firstPegawai->nip) ? "NIP. " . $firstPegawai->nip : '');

            $kuitansiTemplate->setCellValue('F46', $pptkNama);
            $kuitansiTemplate->setCellValue('F47', "NIP. " . $pptkNip);
        }

        // 2. Process Personal Sheets for each employee who has SPJ rincian
        foreach ($rincians as $rincian) {
            $pegawai = $rincian->pegawai;
            if (!$pegawai)
                continue;

            // Short name for sheet title (limit to 20 chars for safety)
            $shortName = substr(preg_replace('/[^A-Za-z0-9 _]/', '', $pegawai->nama), 0, 20);

            // A. Clone and Fill Rincian Perjalanan Dinas (Always cloned for all employees)
            if ($rincianTemplate) {
                $rincianSheet = clone $rincianTemplate;
                $rincianSheet->setTitle('Rincian_' . $shortName);
                $spreadsheet->addSheet($rincianSheet);

                // Fill Rincian Perjalanan Dinas
                $rincianSheet->setCellValue('B12', ': ' . ($nomorSppd ?: '800.1.11.1/    /BPKAD/' . Carbon::parse($tanggalMulai)->format('Y')));
                $rincianSheet->setCellValue('B13', ': ' . Carbon::parse($tanggalMulai)->translatedFormat('d F Y'));
                $rincianSheet->setCellValue('E16', $rincian->jumlah_hari * $rincian->uang_harian);
                $rincianSheet->setCellValue('E17', $rincian->tiket_pesawat_pergi + $rincian->tiket_pesawat_pulang);
                $rincianSheet->setCellValue('E18', $rincian->penginapan ?? 0); // Biaya Penginapan
                $rincianSheet->setCellValue('E19', $rincian->transport);
                $rincianSheet->setCellValue('E20', '=SUM(E16:E19)');

                $tanggalRampungFormat = "Banjarbaru, " . Carbon::parse($tanggalMulai)->translatedFormat('F Y');
                $rincianSheet->setCellValue('E22', $tanggalRampungFormat);
                $rincianSheet->setCellValue('E24', '=E20');
                $rincianSheet->setCellValue('A24', '=E20');
                $rincianSheet->setCellValue('D35', '=E20');
                $rincianSheet->setCellValue('D36', '=E20');

                // Signatures
                $rincianSheet->setCellValue('A29', $bendaharaNama);
                $rincianSheet->setCellValue('A30', "NIP. " . $bendaharaNip);
                $rincianSheet->setCellValue('E29', $pegawai->nama);
                $rincianSheet->setCellValue('E30', $pegawai->nip ? "NIP. " . $pegawai->nip : '');

                // Rampung Section
                $rincianSheet->setCellValue('D35', '=E24');
                $rincianSheet->setCellValue('D36', '=E24');
                $rincianSheet->setCellValue('D37', '=D35-D36');

                $rincianSheet->setCellValue('E44', $kpaNama);
                $rincianSheet->setCellValue('E45', "NIP. " . $kpaNip);
            }

            // B. Clone and Fill Daftar Pengeluaran Riil (Only for Jakarta trips)
            if ($isJakarta && $dprTemplate) {
                $dprSheet = clone $dprTemplate;
                $dprSheet->setTitle('DPR_' . $shortName);
                $spreadsheet->addSheet($dprSheet);

                $sppdNomor = "Berdasarkan Surat Perintah Perjalanan Dinas Nomor : " . ($nomorSppd ?: '800.1.11.1/    /BPKAD/' . Carbon::parse($tanggalMulai)->format('Y')) . ", tanggal " . Carbon::parse($tanggalMulai)->translatedFormat('d F Y') . " dengan ini kami menyatakan dengan sesungguhnya bahwa :";
                $uraianDpr = "Biaya Taksi :\nTempat Kedudukan (Banjarbaru) - (" . ($lokasi ?? '') . ") PP";
                $tanggalRampungFormat = "Banjarbaru, " . Carbon::parse($tanggalMulai)->translatedFormat('d F Y');

                // Fill DPR - Page 1
                $dprSheet->setCellValue('C4', ': ' . $pegawai->nama);
                $dprSheet->setCellValue('C5', $pegawai->nip ? ': ' . $pegawai->nip : ': -');
                $dprSheet->setCellValue('C6', ': ' . ($pegawai->jabatan ?? '-'));
                $dprSheet->setCellValue('A7', $sppdNomor);
                $dprSheet->setCellValue('C11', $uraianDpr);
                $dprSheet->setCellValue('E11', $rincian->transport ?? 0);
                $dprSheet->setCellValue('E12', '=SUM(E11:E11)');
                $dprSheet->setCellValue('D19', $tanggalRampungFormat);
                $dprSheet->setCellValue('D25', $pegawai->nama);
                $dprSheet->setCellValue('D26', $pegawai->nip ? "NIP. " . $pegawai->nip : '');
                $dprSheet->setCellValue('B25', $kpaNama);
                $dprSheet->setCellValue('B26', "NIP. " . $kpaNip);

                // Fill DPR - Page 2
                $dprSheet->setCellValue('C31', ': ' . $pegawai->nama);
                $dprSheet->setCellValue('C32', $pegawai->nip ? ': ' . $pegawai->nip : ': -');
                $dprSheet->setCellValue('C33', ': ' . ($pegawai->jabatan ?? '-'));
                $dprSheet->setCellValue('A34', $sppdNomor);
                $dprSheet->setCellValue('C38', $uraianDpr);
                $dprSheet->setCellValue('E38', $rincian->transport ?? 0);
                $dprSheet->setCellValue('E39', '=SUM(E38:E38)');
                $dprSheet->setCellValue('D46', $tanggalRampungFormat);
                $dprSheet->setCellValue('D52', $pegawai->nama);
                $dprSheet->setCellValue('D53', $pegawai->nip ? "NIP. " . $pegawai->nip : '');
                $dprSheet->setCellValue('B52', $kpaNama);
                $dprSheet->setCellValue('B53', "NIP. " . $kpaNip);
            }
        }

        // 3. Process Tanda Terima Sheet (TT_bpkad)
        if ($tandaTerimaSheet) {
            // Fill dynamic trip title
            $tripTitle = "Pembayaran perjalanan " . ($isJakarta ? 'keluar' : 'dalam') . " provinsi Kalimantan Selatan " . ($kegiatan ?? '') . " ke " . ($lokasi ?? '') . " dengan no SPT : " . ($spt->nomor_spt ?? '');
            $tandaTerimaSheet->setCellValue('A1', $tripTitle);

            $count = count($rincians);

            // Fill each slot
            for ($i = 0; $i < $count; $i++) {
                $rincian = $rincians[$i];
                $pegawai = $rincian->pegawai;
                $shortName = substr(preg_replace('/[^A-Za-z0-9 _]/', '', $pegawai->nama), 0, 20);
                $rincianSheetName = 'Rincian_' . $shortName;
                $startRow = 4 + ($i * 5);

                $tandaTerimaSheet->setCellValue('A' . $startRow, ($i + 1) . '.');
                $tandaTerimaSheet->setCellValue('B' . $startRow, $pegawai->nama ?? '');

                // Link formulas to the employee's personal Rincian sheet
                // Uang Harian
                $tandaTerimaSheet->setCellValue('C' . $startRow, 'Uang Harian ');
                $tandaTerimaSheet->setCellValue('E' . $startRow, "='" . $rincianSheetName . "'!E16");

                // Tiket Pesawat
                $tandaTerimaSheet->setCellValue('C' . ($startRow + 1), 'Tiket Pesawat (PP)');
                $tandaTerimaSheet->setCellValue('E' . ($startRow + 1), "='" . $rincianSheetName . "'!E17");

                // Biaya Penginapan
                $tandaTerimaSheet->setCellValue('C' . ($startRow + 2), 'Biaya Penginapan ');
                $tandaTerimaSheet->setCellValue('E' . ($startRow + 2), "='" . $rincianSheetName . "'!E18");

                // Transport
                $tandaTerimaSheet->setCellValue('C' . ($startRow + 3), 'Transport ');
                $tandaTerimaSheet->setCellValue('E' . ($startRow + 3), "='" . $rincianSheetName . "'!E19");

                // Jumlah Row
                $tandaTerimaSheet->setCellValue('D' . ($startRow + 4), 'Jumlah');
                $tandaTerimaSheet->setCellValue('E' . ($startRow + 4), "=SUM(E" . $startRow . ":E" . ($startRow + 3) . ")");
            }

            // Delete unused rows
            $maxSlots = 6;
            if ($count < $maxSlots) {
                $unusedStart = 4 + ($count * 5);
                $rowsToDelete = (4 + ($maxSlots * 5)) - $unusedStart;
                if ($rowsToDelete > 0) {
                    $tandaTerimaSheet->removeRow($unusedStart, $rowsToDelete);
                }
            }

            // Set the TOTAL formula dynamically
            $deletedCount = ($maxSlots - $count) * 5;
            $newTotalRow = 34 - $deletedCount;
            $tandaTerimaSheet->setCellValue('D' . $newTotalRow, 'TOTAL');

            $jumlahCells = [];
            for ($i = 0; $i < $count; $i++) {
                $jumlahCells[] = 'E' . (8 + ($i * 5));
            }
            $tandaTerimaSheet->setCellValue('E' . $newTotalRow, '=SUM(' . implode(',', $jumlahCells) . ')');

            // Signatures shift as well
            $newPptkNameRow = 41 - $deletedCount;
            $newPptkNipRow = 42 - $deletedCount;

            $tandaTerimaSheet->setCellValue('D' . $newPptkNameRow, $pptkNama);
            $tandaTerimaSheet->setCellValue('D' . $newPptkNipRow, "NIP. " . $pptkNip);
        }

        // 4. Remove master template sheets so they are not included in final Excel
        if ($rincianTemplate) {
            $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($rincianTemplate));
        }
        if ($dprTemplate) {
            $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($dprTemplate));
        }

        // Write and download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        $filename = 'SPJ_' . str_replace('/', '_', $spt->nomor_spt) . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $terbilang = "";
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $terbilang = $this->terbilang(floor($angka / 10)) . " Puluh " . $this->terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " Seratus " . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = $this->terbilang(floor($angka / 100)) . " Ratus " . $this->terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " Seribu " . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = $this->terbilang(floor($angka / 1000)) . " Ribu " . $this->terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = $this->terbilang(floor($angka / 1000000)) . " Juta " . $this->terbilang($angka % 1000000);
        }
        return trim(preg_replace('/\s+/', ' ', $terbilang));
    }
}
