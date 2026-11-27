<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SPT</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0.5cm 1.5cm 1cm 1.5cm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            background: #fff !important;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #000;
        }

        @media screen {
            body {
                width: 18cm;
                margin: 0 auto;
                background: #fff;
            }
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .underline {
            text-decoration: underline;
        }

        /* KOP SURAT */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            background: #fff !important;
        }

        .header-table tr,
        .header-table td,
        .header-table th {
            background: #fff !important;
        }

        .logo {
            width: 80px;
        }

        /* JUDUL SPT */
        .document-title {
            text-align: center;
            margin-bottom: 8px;
        }

        .document-title .title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }

        /* TABEL UTAMA */
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table>tbody>tr>td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* DAFTAR PEGAWAI */
        .list-petugas {
            width: 100%;
            border-collapse: collapse;
            font-size: 12pt;
            line-height: 1.50;
        }

        .list-petugas tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .list-petugas td {
            vertical-align: top;
            padding: 0;
            height: 15px;
        }

        .list-petugas .spacer td {
            height: 4px;
        }

        /* PEMBAGIAN HALAMAN */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .pegawai-page {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* BAGIAN PENUTUP */
        .footer-section {
            width: 100%;
            margin-top: 8px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd-container {
            width: 100%;
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd-box {
            width: 48%;
            text-align: left;
        }

        .paraf-container {
            width: 28%;
            margin-top: 8px;
            margin-left: 18%;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .paraf-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            background: #fff;
        }

        .paraf-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            height: 26px;
        }

        .paraf-table tr:first-child td {
            height: auto;
        }

        /* HALAMAN TERAKHIR */
        .penutup-page {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media print {
            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>

<body>

    @php
        $parts = explode('/', $spt->nomor_spt);

        $semuaPegawai = $spt->pegawais->values();
        $jumlahPegawai = $semuaPegawai->count();

        // Jika pegawai 1 - 4 orang, seluruh konten (termasuk Untuk, TTD, dan Paraf) cukup di halaman pertama.
        $hanyaSatuHalaman = $jumlahPegawai >= 1 && $jumlahPegawai <= 4;

        $pegawaiHalamanPertama = $semuaPegawai->slice(0, 8);
        $kelompokPegawaiLanjutan = $hanyaSatuHalaman ? collect() : $semuaPegawai->slice(8)->chunk(10);

        // Counter berlanjut setelah halaman pertama.
        $nomorUrutPegawai = $pegawaiHalamanPertama->count() + 1;

        // Tanggal pelaksanaan.
        $tglMulai = $spt->tanggal_mulai_efektif;
        $tglSelesai = $spt->tanggal_selesai_efektif;
    @endphp

    {{-- HALAMAN PERTAMA: KOP, DASAR, DAN MAKSIMAL 8 PEGAWAI --}}
    <div class="pegawai-page">

        <table class="header-table">
            @include('components.kop-surat')
        </table>

        <div class="document-title">
            <div class="title">
                SURAT PERINTAH TUGAS
            </div>

            <div style="line-height: 1.4;">
                NOMOR :
                {{ $parts[0] ?? '' }}/
                <span style="
                    display: inline-block;
                    min-width: 1.5cm;
                    text-align: center;
                    border-bottom: 1px solid transparent;
                    vertical-align: bottom;
                ">
                    {{ $parts[1] ?? '' }}
                </span>
                /{{ $parts[2] ?? '' }}/{{ $parts[3] ?? '' }}
            </div>
        </div>

        <table class="main-table">
            <tr>
                <td style="width: 100px;">Dasar</td>
                <td style="width: 15px;" class="text-center">:</td>
                <td style="text-align: justify;">
                    {{ $spt->jenis_anggaran }}
                    Badan Pengelolaan Keuangan dan Aset Daerah
                    Provinsi Kalimantan Selatan
                    Tahun Anggaran {{ $spt->tahun_anggaran ?? date('Y') }}.
                </td>
            </tr>

            <tr>
                <td colspan="3" class="text-center font-bold" style="padding: 10px 0 6px;">
                    MEMERINTAHKAN :
                </td>
            </tr>
        </table>

        {{-- KELOMPOK PEGAWAI HALAMAN 1 (MAKSIMAL 8 ORANG) --}}
        @if ($pegawaiHalamanPertama->isNotEmpty())
            <table class="main-table">
                <tr>
                    <td style="width: 100px; padding-top: 8px;">
                        Kepada
                    </td>
                    <td style="width: 15px; padding-top: 8px;" class="text-center">
                        :
                    </td>
                    <td style="padding-top: 8px;">

                        <table class="list-petugas">
                            @foreach ($pegawaiHalamanPertama as $index => $pegawai)
                                <tr>
                                    <td style="width: 25px;">
                                        {{ $index + 1 }}.
                                    </td>
                                    <td style="width: 115px;">Nama</td>
                                    <td style="width: 15px; text-align: center;">:</td>
                                    <td>{{ $pegawai->nama }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>NIP</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Pangkat/Golongan</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->pangkat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Jabatan</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->jabatan ?? '-' }}</td>
                                </tr>

                                @if (!$loop->last)
                                    <tr class="spacer">
                                        <td colspan="4"></td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>

                    </td>
                </tr>
            </table>
        @else
            <p>Data pegawai tidak tersedia.</p>
        @endif

        {{-- JIKA PEGAWAI 1 - 4 ORANG: UNTUK, TTD, DAN PARAF LANGSUNG DI LEMBAR PERTAMA --}}
        @if ($hanyaSatuHalaman)
            <table class="main-table" style="margin-top: 6px;">
                <tr>
                    <td style="width: 100px; padding-top: 8px;">
                        Untuk
                    </td>
                    <td style="width: 15px; padding-top: 8px;" class="text-center">
                        :
                    </td>
                    <td style="padding-top: 8px; text-align: justify;">
                        1. {{ $spt->kegiatan_efektif }}
                        di {{ $spt->lokasi_efektif }}.
                        <br>

                        2. Waktu Pelaksanaan
                        @if (is_null($tglSelesai) || $tglMulai == $tglSelesai)
                            tanggal
                            {{ \Carbon\Carbon::parse($tglMulai)->translatedFormat('d F Y') }}
                        @else
                            tanggal
                            {{ \Carbon\Carbon::parse($tglMulai)->translatedFormat('d F Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($tglSelesai)->translatedFormat('d F Y') }}
                        @endif
                        .<br>

                        3. Melaporkan hasil pelaksanaan tugas
                        kepada yang memberikan tugas.
                    </td>
                </tr>
            </table>

            <div class="footer-section">
                <div class="ttd-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 52%;"></td>
                            <td style="width: 48%; vertical-align: top; text-align: left;">
                                <div style="margin-bottom: 1px;">
                                    Banjarbaru,&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ optional($spt->notaDinas)->tanggal_mulai
            ? \Carbon\Carbon::parse($spt->notaDinas->tanggal_mulai)
                ->subDay()->translatedFormat('F Y')
            : \Carbon\Carbon::now()->translatedFormat('F Y') }}
                                </div>

                                <div>
                                    KEPALA BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH
                                </div>
                                <div>
                                    PROVINSI KALIMANTAN SELATAN,
                                </div>

                                <br><br>

                                <div class="font-bold underline">
                                    H. FATKHAN, SE, MM
                                </div>
                                <div>Pembina Tingkat I (IV/b)</div>
                                <div>NIP. 19750518 201001 1 001</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="paraf-container">
                    <table class="paraf-table">
                        <tr>
                            <td colspan="2" class="text-center font-bold">
                                Paraf Hirarki
                            </td>
                        </tr>
                        <tr>
                            <td>Sekretaris</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Kabid</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Kasubid</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

    </div>

    {{-- HALAMAN LANJUTAN & HALAMAN PENUTUP (JIKA PEGAWAI > 4 ATAU KOSONG) --}}
    @unless ($hanyaSatuHalaman)
        @foreach ($kelompokPegawaiLanjutan as $pegawais)

            <div class="page-break"></div>

            <table class="main-table">
                <tr>
                    <td style="width: 100px; padding-top: 8px;"></td>
                    <td style="width: 15px; padding-top: 8px;"></td>
                    <td style="padding-top: 8px;">

                        <table class="list-petugas">
                            @foreach ($pegawais as $pegawai)
                                <tr>
                                    <td style="width: 25px;">
                                        {{ $nomorUrutPegawai++ }}.
                                    </td>
                                    <td style="width: 115px;">Nama</td>
                                    <td style="width: 15px; text-align: center;">:</td>
                                    <td>{{ $pegawai->nama }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>NIP</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->nip ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Pangkat/Golongan</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->pangkat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Jabatan</td>
                                    <td class="text-center">:</td>
                                    <td>{{ $pegawai->jabatan ?? '-' }}</td>
                                </tr>

                                @if (!$loop->last)
                                    <tr class="spacer">
                                        <td colspan="4"></td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>

                    </td>
                </tr>
            </table>

        @endforeach

        {{-- HALAMAN PENUTUP --}}
        <div class="page-break"></div>

        <div class="penutup-page">

            <table class="main-table">
                <tr>
                    <td style="width: 100px; padding-top: 8px;">
                        Untuk
                    </td>
                    <td style="width: 15px; padding-top: 8px;" class="text-center">
                        :
                    </td>
                    <td style="padding-top: 8px; text-align: justify;">

                        1. {{ $spt->kegiatan_efektif }}
                        di {{ $spt->lokasi_efektif }}.
                        <br>

                        2. Waktu Pelaksanaan

                        @if (is_null($tglSelesai) || $tglMulai == $tglSelesai)
                            tanggal
                            {{ \Carbon\Carbon::parse($tglMulai)->translatedFormat('d F Y') }}
                        @else
                            tanggal
                            {{ \Carbon\Carbon::parse($tglMulai)->translatedFormat('d F Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($tglSelesai)->translatedFormat('d F Y') }}
                        @endif

                        .<br>

                        3. Melaporkan hasil pelaksanaan tugas
                        kepada yang memberikan tugas.

                    </td>
                </tr>
            </table>

            {{-- TANDA TANGAN KEPALA BADAN --}}
            <div class="footer-section">

                <div class="ttd-container">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            {{-- Kolom kosong di kiri sebagai pendorong ke kanan --}}
                            <td style="width: 52%;"></td>

                            {{-- Kolom tanda tangan di kanan --}}
                            <td style="width: 48%; vertical-align: top; text-align: left;">
                                <div style="margin-bottom: 5px;">
                                    Banjarbaru,&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{ optional($spt->notaDinas)->tanggal_mulai
            ? \Carbon\Carbon::parse($spt->notaDinas->tanggal_mulai)
                ->subDay()->translatedFormat('F Y')
            : \Carbon\Carbon::now()->translatedFormat('F Y') }}
                                </div>

                                <div>
                                    KEPALA BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH
                                </div>
                                <div>
                                    PROVINSI KALIMANTAN SELATAN,
                                </div>
                                <br><br><br>

                                <div class="font-bold underline">
                                    H. FATKHAN, SE, MM
                                </div>
                                <div>Pembina Tingkat I (IV/b)</div>
                                <div>NIP. 19750518 201001 1 001</div>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- PARAF HIRARKI --}}
                <div class="paraf-container">
                    <table class="paraf-table">
                        <tr>
                            <td colspan="2" class="text-center font-bold">
                                Paraf Hirarki
                            </td>
                        </tr>
                        <tr>
                            <td>Sekretaris</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Kabid</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Kasubid</td>
                            <td style="text-align: center;">&nbsp;</td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>
    @endunless

</body>

</html>