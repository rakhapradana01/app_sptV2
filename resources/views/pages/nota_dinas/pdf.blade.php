<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Dinas</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.5cm 1.5cm;
        }

        .page-break {
            page-break-before: always;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
        }

        /* HEADER */

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
        }

        .kop-text {
            text-align: center;
            line-height: 1.4;
        }

        .garis {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            margin-top: 8px;
            margin-bottom: 20px;
        }

        /* JUDUL */

        .judul {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        /* INFO SURAT */

        .table-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .table-info td {
            padding: 3px;
            vertical-align: top;
        }

        /* ISI */

        .isi p {
            text-align: justify;
            line-height: 1.6;
            text-indent: 40px;
        }

        /* TTD */

        .ttd {
            width: 100%;
            margin-top: 40px;
        }

        /* DISPOSISI */

        .disposisi {
            margin-top: 40px;
        }

        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
        }

        .disposisi-table td {
            border: 1px solid black;
            padding: 6px;
        }

        .disposisi-table tr td {
            padding: 1px;
        }

        .space-half {
            height: 10px;
        }

        .garis-tipis {
            border-top: 1px solid #555;
            margin: 6px 0;
        }
    </style>


</head>

<body>

    <!-- HEADER -->

    <table class="header">
        @include('components.kop-surat')
    </table>

    <!-- JUDUL -->

    <div class="judul">
        NOTA DINAS
    </div>

    <!-- INFO SURAT -->

    <table class="table-info">

        <tr>
            <td width="120">Kepada</td>
            <td width="10">:</td>
            <td>

                Yth. {{ $nota->kepada->jabatan ?? '-' }}

                @if ($nota->melalui)
                    <br>Melalui {{ $nota->melalui->jabatan }}
                @endif

            </td>
        </tr>

        <tr>
            <td>Dari</td>
            <td>:</td>
            <td>{{ $nota->dari->jabatan ?? '-' }}</td>
        </tr>


        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($nota->tanggal)->translatedFormat('d F Y') }}</td>
        </tr>

        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td>
                900.1 /
                <span style="display:inline-block; min-width:80px;">
                    {{ $nota->nomor_urut }}
                </span>
                / BPKAD / {{ date('Y') }}
            </td>
        </tr>

        <tr>
            <td>Sifat</td>
            <td>:</td>
            <td>{{ $nota->sifat }}</td>
        </tr>

        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>{{ $nota->lampiran }}</td>
        </tr>

        <tr>
            <td>Hal</td>
            <td>:</td>
            <td>{{ $nota->perihal }}</td>
        </tr>

    </table>

    <div class="garis-tipis"></div>
    <!-- ISI -->

    <div class="isi">

        <p>
            Sehubungan dengan kegiatan yang akan dilaksanakan di
            <b>{{ $nota->lokasi }}</b> pada tanggal
            <b>
                {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }}
                @if ($nota->tanggal_selesai)
                    s.d {{ \Carbon\Carbon::parse($nota->tanggal_selesai)->translatedFormat('d F Y') }}
                @endif
            </b>,
            bersama ini disampaikan hal-hal sebagai berikut.
        </p>

        <p>
            @php
                $grouped = $nota->pegawais->groupBy('jabatan')->map->count();
            @endphp

            Diusulkan <b>{{ $nota->pegawais->count() }} orang pegawai</b> (
            @foreach ($grouped as $jabatan => $jumlah)
                {{ $jumlah }} {{ $jabatan }}@if (!$loop->last)
                    ,
                @endif
            @endforeach
            )
            untuk melaksanakan perjalanan dinas {{ Str::lcfirst($nota->kegiatan) }}.
        </p>

        <p>
            Pembebanan biaya perjalanan dinas dibebankan pada
            <b>{{ $nota->subKegiatan->nomor_rekening }}</b> -
            <b>{{ $nota->subKegiatan->nama_kegiatan ?? '-' }}</b>.
        </p>

        <p>
            Demikian Nota Dinas ini disampaikan untuk menjadi perhatian dan sebagaimana mestinya.
        </p>

    </div>

    <!-- TTD -->

    <table class="ttd">

        <tr>

            <td width="60%"></td>

            <td width="40%" style="text-align:center">

                {{ $nota->dari->jabatan ?? '' }}

                <br><br><br><br>

                <b style="white-space: nowrap;">
                    {{ $nota->dari->nama ?? '' }}
                </b><br>

                @if (isset($nota->dari->pangkat))
                    {{ $nota->dari->pangkat }}<br>
                @endif

                @if (isset($nota->dari->nip))
                    NIP. {{ $nota->dari->nip }}
                @endif

            </td>

        </tr>

    </table>

    <!-- DISPOSISI -->
    <!-- DISPOSISI -->
    <div class="page-break"></div>

    <!-- KOTAK KEPALA BIDANG -->
    <table class="disposisi-table">

        <tr>
            <td>
                <b>{{ $nota->melalui->jabatan ?? 'Sekretaris' }}</b>
            </td>
        </tr>

        <tr class="space-half">
            <td style="border:none"></td>
        </tr>

        <tr>
            <td>
                Mohon persetujuan Kaban
                <br>
                @if ($nota->pegawais->count())

                    <p>Adapun pegawai yang diusulkan adalah sebagai berikut :</p>

                    <ol>

                        @foreach ($nota->pegawais as $pegawai)
                            <li>
                                {{ $pegawai->nama }} - {{ $pegawai->jabatan }}
                            </li>
                        @endforeach

                    </ol>

                @endif

                <br><br><br><br>
            </td>
        </tr>

        <tr class="space-half">
            <td style="border:none"></td>
        </tr>

        <tr>
            <td>
                Tanggal dan Jam Disposisi :
                {{ \Carbon\Carbon::parse($nota->updated_at)->format('Y-m-d H:i:s') }}
            </td>
        </tr>

    </table>


    <br>


    <!-- KOTAK KEPALA BADAN -->
    <table class="disposisi-table">

        <tr>
            <td>
                <b>KEPALA BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH</b>
            </td>
        </tr>

        <tr class="space-half">
            <td style="border:none"></td>
        </tr>

        <tr>
            <td style="height:120px; vertical-align:top; padding:6px;">

                @if ($nota->status == \App\Models\NotaDinas::DISETUJUI_KABAN)

                    Setuju {{ $nota->pegawais->count() }} Orang<br>
                    Dengan Nama / NIP:<br>

                    @foreach ($nota->pegawais as $i => $pegawai)
                        {{ $i + 1 }}. {{ $pegawai->nama }} / {{ $pegawai->nip }}<br>
                    @endforeach

                    <br>

                    Tanggal Berangkat :
                    {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y') }}

                    <br>

                    Tanggal Kembali :
                    {{ \Carbon\Carbon::parse($nota->tanggal_selesai)->translatedFormat('d F Y') }}

                    <br>

                    Lamanya :
                    {{ \Carbon\Carbon::parse($nota->tanggal_mulai)->diffInDays($nota->tanggal_selesai) + 1 }} Hari

                @endif

            </td>
        </tr>

    </table>
</body>

</html>
