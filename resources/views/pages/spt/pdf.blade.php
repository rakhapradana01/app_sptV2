<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SPT</title>

    <style>
        @page {
            size: A4 portrait;
            /* margin: 0.8cm 3.0cm 1.0cm 3.0cm; */
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            background-color: #fff !important;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            color: #000;
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

        /* Kop Surat */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            background-color: #fff !important;
        }

        .header-table tr,
        .header-table td,
        .header-table th {
            background-color: #fff !important;
        }

        .logo {
            width: 80px;
        }

        /* JUDUL SPT */
        .document-title {
            text-align: center;
            margin-bottom: 8px;
            background-color: #fff;
        }

        .document-title .title {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
        }

        /* TABEL DASAR & STRUKTUR LUAR */
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }

        .main-table > tbody > tr > td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* TABEL PETUGAS DI DALAM KOLOM KANAN */
        .list-petugas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            page-break-inside: avoid;
        }

        .list-petugas td {
            vertical-align: top;
            padding: 1px 0;
        }

        /* BAGIAN PENUTUP & TANDA TANGAN */
        .footer-section {
            width: 100%;
            page-break-inside: avoid;
            break-inside: avoid;
            margin-top: 8px;
        }

        .ttd-container {
            margin-top: 15px;
            width: 100%;
            position: relative;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd-box {
            float: right;
            width: 48%;
            text-align: left;
        }

        .paraf-container {
            clear: both;
            margin-top: 8px;
            width: 28%;
            margin-left: 18%;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .paraf-box {
            width: 100%;
        }

        .paraf-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            background-color: #fff;
        }

        .paraf-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            height: 26px;
        }

        .paraf-table tr:first-child td {
            height: auto;
        }

        .paraf-table tr:not(:first-child) td:first-child,
        .paraf-table tr:not(:first-child) td:last-child {
            width: 50%;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table class="header-table">
        @include('components.kop-surat')
    </table>

    {{-- JUDUL DAN NOMOR SPT --}}
    <div class="document-title">
        <div class="title">
            SURAT PERINTAH TUGAS
        </div>

        @php
            $parts = explode('/', $nota->spt->nomor_spt);
        @endphp

        <div style="line-height: 1.4;">
            NOMOR :
            {{ $parts[0] ?? '' }}/
            <span style="display: inline-block; min-width: 1.5cm; text-align: center; border-bottom: 1px solid transparent; vertical-align: bottom;">
                {{ $parts[1] ?? '' }}
            </span>
            /{{ $parts[2] ?? '' }}/{{ $parts[3] ?? '' }}
        </div>
    </div>

    {{-- TABEL UTAMA SPT --}}
    <table class="main-table">
        {{-- DASAR --}}
        <tr>
            <td style="width: 100px;">Dasar</td>
            <td style="width: 15px;" class="text-center">:</td>
            <td style="text-align: justify;">
                {{ $nota->spt->jenis_anggaran }}
                Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Kalimantan Selatan
                Tahun Anggaran {{ $nota->spt->tahun_anggaran ?? date('Y') }}.
            </td>
        </tr>

        {{-- MEMERINTAHKAN --}}
        <tr>
            <td colspan="3" class="text-center font-bold" style="padding: 10px 0 6px 0;">
                MEMERINTAHKAN :
            </td>
        </tr>

        {{-- KEPADA --}}
        <tr>
            <td style="width: 100px;">Kepada</td>
            <td style="width: 15px;" class="text-center">:</td>
            <td>
                @php
                    $listPegawai = $nota->pegawais->values();
                @endphp

                @foreach ($listPegawai as $index => $pegawai)
                    <table class="list-petugas">
                        <tr>
                            <td style="width: 25px;">{{ $index + 1 }}.</td>
                            <td style="width: 95px;">Nama</td>
                            <td style="width: 15px;" class="text-center">:</td>
                            <td class="font-bold">{{ $pegawai->nama }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Pangkat/Gol</td>
                            <td class="text-center">:</td>
                            <td>{{ $pegawai->pangkat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>NIP</td>
                            <td class="text-center">:</td>
                            <td>{{ $pegawai->nip ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Jabatan</td>
                            <td class="text-center">:</td>
                            <td>{{ $pegawai->jabatan ?? '-' }}</td>
                        </tr>
                    </table>
                @endforeach
            </td>
        </tr>

        {{-- UNTUK --}}
        <tr>
            <td style="width: 100px; padding-top: 8px;">Untuk</td>
            <td style="width: 15px; padding-top: 8px;" class="text-center">:</td>
            <td style="padding-top: 8px; text-align: justify;">
                1. {{ $nota->perihal }} di {{ $nota->lokasi }}.<br>

                2. Waktu Pelaksanaan
                @if (is_null($nota->tanggal_selesai) || $nota->tanggal_mulai == $nota->tanggal_selesai)
                    tanggal {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }}
                @else
                    tanggal {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }}
                    s/d
                    {{ \Carbon\Carbon::parse($nota->tanggal_selesai)->translatedFormat('d F Y') }}
                @endif
                .<br>

                3. Melaporkan hasil pelaksanaan tugas kepada yang memberikan tugas.
            </td>
        </tr>
    </table>

    {{-- BAGIAN PENUTUP & TANDA TANGAN --}}
    <div class="footer-section">
        {{-- TANDA TANGAN KEPALA BADAN --}}
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="margin-bottom: 5px;">
                    Banjarbaru,&nbsp;&nbsp;&nbsp;&nbsp;
                    {{ $nota->tanggal_mulai
                        ? \Carbon\Carbon::parse($nota->tanggal_mulai)->subDay()->translatedFormat('F Y')
                        : \Carbon\Carbon::now()->translatedFormat('F Y') }}
                </div>
                <div>KEPALA BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</div>
                <div>PROVINSI KALIMANTAN SELATAN,</div>
                <br><br><br>
                <div class="font-bold underline">H. FATKHAN, SE, MM</div>
                <div>Pembina Tingkat I (IV/b)</div>
                <div>NIP. 19750518 201001 1 001</div>
            </div>
        </div>

        {{-- PARAF HIRARKI --}}
        <div class="paraf-container">
            <div class="paraf-box">
                <table class="paraf-table">
                    <tr>
                        <td colspan="2" class="text-center font-bold">Paraf Hirarki</td>
                    </tr>
                    <tr>
                        <td>Sekretaris</td>
                        <td style="text-align: center; vertical-align: middle; height: 26px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Kabid</td>
                        <td style="text-align: center; vertical-align: middle; height: 26px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Kasubid</td>
                        <td style="text-align: center; vertical-align: middle; height: 26px;">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</body>

</html>
