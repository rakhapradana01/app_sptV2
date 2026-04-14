<!DOCTYPE html>
<html>

<head>
    <title>Surat Perintah Tugas</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
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
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
        }

        .header-text h2 {
            margin: 0;
            font-size: 13pt;
        }

        .header-text h1 {
            margin: 0;
            font-size: 15pt;
        }

        .header-text p {
            margin: 0;
            font-size: 9pt;
        }

        /* Isi Surat */
        .content-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .content-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .list-petugas {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        /* Tanda Tangan */
        .ttd-container {
            margin-top: 40px;
            width: 100%;
            position: relative;
        }

        .ttd-box {
            float: right;
            width: 40%;
            text-align: left;
        }
    </style>
</head>

<body>
    <table class="header-table">
        @include('components.kop-surat')
    </table>

    <div class="text-center">
        <div class="font-bold underline" style="font-size: 12pt;">SURAT PERINTAH TUGAS</div>
        {{-- <div>
            @php
                $parts = explode('/', $nota->spt->nomor_spt);
            @endphp

            NOMOR : {{ $parts[0] ?? '' }}/
            <span style="display: inline-block; min-width: 1.5cm; text-align: center;">
                {{ $parts[1] ?? '' }}
            </span>
            /{{ $parts[2] ?? '' }}/{{ $parts[3] ?? '' }}
        </div> --}}
    </div>

    <table class="content-table">
        <tr>
            <td style="width: 15%;">Dasar</td>
            <td style="width: 2%;">:</td>
            <td style="text-align: justify;">{{ $nota->spt->jenis_anggaran }} SKPD Badan Pengelolaan Keuangan dan Aset
                Daerah Provinsi Kalimantan Selatan Tahun Anggaran {{ $nota->spt->tahun_anggaran ?? date('Y') }}.</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center font-bold" style="padding: 15px 0;">MEMERINTAHKAN :</td>
        </tr>
        <tr>
            <td>Kepada</td>
            <td>:</td>
            <td>
                @foreach ($nota->pegawais as $index => $pegawai)
                    <table class="list-petugas">
                        <tr>
                            <td style="width: 25px;">{{ $index + 1 }}.</td>
                            <td style="width: 80px;">Nama</td>
                            <td style="width: 10px;">:</td>
                            <td class="font-bold">{{ $pegawai->nama }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>NIP</td>
                            <td>:</td>
                            <td>{{ $pegawai->nip }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Pangkat/Gol</td>
                            <td>:</td>
                            <td>{{ $pegawai->pangkat }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Jabatan</td>
                            <td>:</td>
                            <td>{{ $pegawai->jabatan }}</td>
                        </tr>
                    </table>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">Untuk</td>
            <td style="padding-top: 10px;">:</td>
            <td style="padding-top: 10px; text-align: justify;">
                1. {{ $nota->kegiatan }} di {{ $nota->lokasi }}.<br>

                2. Waktu Pelaksanaan
                @if ($nota->tanggal_mulai == $nota->tanggal_selesai)
                    {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }}
                @else
                    {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }} s/d
                    {{ \Carbon\Carbon::parse($nota->tanggal_selesai)->translatedFormat('d F Y') }}
                @endif
                .<br>

                3. Melaporkan hasil pelaksanaan tugas kepada yang memberikan tugas.
            </td>
        </tr>
    </table>

    <div class="ttd-container">
        <div class="ttd-box">
            <div style="margin-bottom: 5px;">Banjarbaru,
                <span
                    style="display: inline-block; min-width: 0.8cm; border-bottom: 1px dotted #000; text-align: center;">
                    &nbsp;
                </span>
                {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </div>
            <div>KEPALA BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</div>
            <div>PROVINSI KALIMANTAN SELATAN,</div>
            <br><br><br><br>
            <div class="font-bold underline">H. FATKHAN, SE, MM</div>
            <div>Pembina Tingkat I (IV/b)</div>
            <div>NIP. 19750518 201001 1 001</div>
        </div>
    </div>
</body>

</html>
