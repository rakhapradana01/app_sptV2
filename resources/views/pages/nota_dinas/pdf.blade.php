<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Dinas</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1cm 1.5cm;
        }

        .page-break {
            page-break-before: always;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            margin: 0;
        }

        /* HEADER */

        .header {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .logo {
            width: 80px;
        }

        .kop-text {
            text-align: center;
            line-height: 1.25;
        }

        .garis {
            border-top: 3px solid black;
            border-bottom: 1px solid black;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        /* JUDUL */

        .judul {
            text-align: center;
            font-weight: bold;
            margin-top: 2px;
            margin-bottom: 6px;
        }

        /* INFO SURAT */

        .table-info {
            width: 100%;
            margin-bottom: 4px;
        }

        .table-info td {
            padding: 0 2px;
            line-height: 1.05;
            vertical-align: top;
        }

        /* ISI */

        .isi p {
            text-align: justify;
            line-height: 1.2;
            text-indent: 30px;
            margin: 2px 0;
        }

        .pegawai-table {
            width: 100%;
            border-collapse: collapse;
            margin: 2px 0 5px;
            font-size: 9.5pt;
        }

        .pegawai-table th,
        .pegawai-table td {
            border: 1px solid black;
            padding: 2px 5px;
            text-align: left;
            vertical-align: top;
        }

        .pegawai-table th {
            background: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        /* TTD */

        .ttd {
            width: 100%;
            margin-top: 6px;
            page-break-inside: avoid;
        }

        /* DISPOSISI (kotak kosong untuk tanda tangan basah) */

        .disposisi-title {
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            margin-top: 6px;
            margin-bottom: 4px;
        }

        .disposisi-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .disposisi-table td {
            border: 1px solid black;
            padding: 4px;
        }

        .disposisi-header {
            background: #f2f2f2;
            text-align: center;
            vertical-align: middle;
        }

        .disposisi-box {
            height: 140px;
            vertical-align: top;
        }

        .garis-tipis {
            border-top: 1px solid #555;
            margin: 4px 0;
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

    @php
        $konteks = trim((string) $nota->asal_undangan);
        $konteks = preg_replace('/^sehubungan\s+dengan\s*/i', '', $konteks);
        $konteks = rtrim($konteks, " .,");
        if ($konteks === '') {
            $konteks = $nota->perihal ?: 'pelaksanaan tugas';
        }

        $kegiatan = rtrim((string) $nota->kegiatan, " .");
        $tanggalMulai = $nota->tanggal_mulai
            ? \Carbon\Carbon::parse($nota->tanggal_mulai)->translatedFormat('d F Y')
            : '-';
        $tanggalSelesai = $nota->tanggal_selesai
            ? \Carbon\Carbon::parse($nota->tanggal_selesai)->translatedFormat('d F Y')
            : null;
        $tanggalText = $tanggalSelesai ? "{$tanggalMulai} s.d. {$tanggalSelesai}" : $tanggalMulai;
        $tahunAnggaran = \Carbon\Carbon::parse($nota->tanggal)->format('Y');
        $tembusan = $nota->tembusan ?: ($nota->melalui->jabatan ?? '-');
        $lokasiText = $nota->lokasi ? " di {$nota->lokasi}" : '';
        $kegiatanText = $kegiatan !== '' ? ", yaitu {$kegiatan}" : '';
    @endphp

    <table class="table-info">

        <tr>
            <td width="100">Yth</td>
            <td width="10">:</td>
            <td>{{ $nota->kepada->jabatan ?? '-' }}</td>
        </tr>

        <tr>
            <td>Dari</td>
            <td>:</td>
            <td>{{ $nota->dari->jabatan ?? '-' }}</td>
        </tr>

        <tr>
            <td>Tembusan</td>
            <td>:</td>
            <td>{{ $tembusan }}</td>
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
                / BPKAD / {{ $tahunAnggaran }}
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
            Sehubungan dengan {{ $konteks }}, bersama ini disampaikan hal-hal sebagai berikut.
        </p>

        <p>
            Kegiatan dilaksanakan pada tanggal <b>{{ $tanggalText }}</b>{{ $lokasiText }}{{ $kegiatanText }}.
        </p>

        @if ($nota->pegawais->count())
            <p>
                Adapun pegawai yang ditugaskan adalah sebagai berikut:
            </p>

            <table class="pegawai-table">
                <thead>
                    <tr>
                        <th width="5%" style="text-align: center;">No</th>
                        <th width="45%">Nama</th>
                        <th width="50%">Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nota->pegawais as $pegawai)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td>{{ $pegawai->nama }}</td>
                            <td>{{ $pegawai->jabatan }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p>
            Perjalanan dinas dimaksud dibebankan pada
            @if ($nota->subKegiatan)
                Sub Kegiatan <b>{{ $nota->subKegiatan->nomor_rekening }} -
                    {{ $nota->subKegiatan->nama_kegiatan }}</b> pada
            @endif
            DPA Badan Pengelolaan Keuangan dan Aset Daerah Provinsi Kalimantan Selatan Tahun Anggaran
            {{ $tahunAnggaran }}.
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

                <div style="height: 42px;"></div>

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

    <div class="disposisi-title">LEMBAR DISPOSISI</div>

    <table class="disposisi-table">
        <tr>
            <td class="disposisi-header" width="33.33%"><b>KEPALA BIDANG PAD.</b></td>
            <td class="disposisi-header" width="33.33%"><b>SEKRETARIS BPKAD.</b></td>
            <td class="disposisi-header" width="33.34%"><b>KEPALA BPKAD.</b></td>
        </tr>
        <tr>
            <td class="disposisi-box"></td>
            <td class="disposisi-box"></td>
            <td class="disposisi-box"></td>
        </tr>
    </table>

</body>

</html>
