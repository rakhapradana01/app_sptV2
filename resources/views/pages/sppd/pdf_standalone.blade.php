<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SPD Standalone</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .main-table td {
            border: 1px solid black;
            padding: 5px 10px;
            vertical-align: top;
        }

        .no-border-table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border-table td {
            border: none !important;
            padding: 1px;
        }

        .text-center {
            text-align: center;
        }

        .text-underline {
            text-decoration: underline;
        }

        /* Teknik Pecah Halaman */
        .page-break {
            page-break-after: always;
        }

        .last-page {
            page-break-after: avoid;
        }
    </style>
</head>

<body>
    @foreach ($sppd->pegawais as $p)
        <div class="{{ $loop->last ? 'last-page' : 'page-break' }}">
            {{-- Bagian Kop Surat --}}
            <table>
                @include('components.kop-surat')
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="width: 55%;"></td>
                    <td>
                        <table class="no-border-table" style="font-size: 9pt;">
                            <tr>
                                <td style="width: 80px;">Lembar ke</td>
                                <td>: </td>
                            </tr>
                            <tr>
                                <td>Kode No</td>
                                <td>: </td>
                            </tr>
                            <tr>
                                <td>Nomor</td>
                                <td>:</td>
                                <td class="px-5 py-4 sm:px-6 whitespace-nowrap font-mono text-sm text-gray-600 dark:text-gray-400">
                                    {{ $sppd->nomor_sppd }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="text-center" style="margin: 10px 0;">
                <strong style="font-size: 11pt;" class="text-underline">SURAT PERJALANAN DINAS (SPD)</strong>
            </div>

            <table class="main-table">
                <tr>
                    <td style="width: 30px;" class="text-center">1.</td>
                    <td style="width: 40%;">Pejabat Pembuat Komitmen</td>
                    <td>{{ $sppd->pejabat_ppk ?? 'Kepala Bidang Perencanaan Anggaran Daerah Selaku Kuasa Pengguna Anggaran' }}</td>
                </tr>
                <tr>
                    <td class="text-center">2.</td>
                    <td>Nama/NIP Pegawai yang melaksanakan perjalanan dinas</td>
                    <td>{{ $p->nama }}<br>NIP. {{ $p->nip }}</td>
                </tr>
                <tr>
                    <td class="text-center">3.</td>
                    <td>
                        a. Pangkat dan Golongan<br>
                        b. Jabatan / Instansi<br>
                        c. Tingkat Biaya Perjalanan Dinas
                    </td>
                    <td>
                        a. {{ $p->pangkat ?? '-' }}<br>
                        b. {{ $p->jabatan ?? '-' }}<br>
                        c. {{ $p->tingkat_biaya ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">4.</td>
                    <td>Maksud Perjalanan Dinas</td>
                    <td>{{ $sppd->kegiatan }}</td>
                </tr>
                <tr>
                    <td class="text-center">5.</td>
                    <td>Alat Angkut yang dipergunakan</td>
                    <td>{{ $sppd->alat_angkutan }}</td>
                </tr>
                <tr>
                    <td class="text-center">6.</td>
                    <td>
                        a. Tempat Berangkat<br>
                        b. Tempat Tujuan
                    </td>
                    <td>
                        a. {{ $sppd->tempat_berangkat }}<br>
                        b. {{ $sppd->tempat_tujuan }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">7.</td>
                    <td>
                        a. Lamanya Perjalanan Dinas<br>
                        b. Tanggal Berangkat<br>
                        c. Tanggal Harus Kembali/Tiba di Tempat Baru
                    </td>
                    <td>
                        a. {{ $lamaHari }} Hari<br>
                        b. {{ \Carbon\Carbon::parse($sppd->tanggal_mulai)->translatedFormat('d F Y') }}<br>
                        c. {{ \Carbon\Carbon::parse($sppd->tanggal_selesai ?: $sppd->tanggal_mulai)->translatedFormat('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">8.</td>
                    <td>Pengikut: Nama</td>
                    <td>Keterangan</td>
                </tr>
                <tr>
                    <td class="text-center">9.</td>
                    <td>
                        Pembebanan Anggaran<br>
                        a. Instansi<br>
                        b. Akun
                    </td>
                    <td>
                        <br>
                        a. {{ $sppd->instansi ?? 'DPA - SKPD BPKAD PROV KALSEL TA ' . date('Y') }}<br>
                        b. {{ $sppd->akun ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="text-center">10.</td>
                    <td>Keterangan Lain-lain</td>
                    <td></td>
                </tr>
            </table>

            <table style="width: 100%; margin-top: 25px;">
                <tr>
                    <td style="width: 55%;"></td>
                    <td class="text-center">
                        Dikeluarkan di: {{ $sppd->tempat_berangkat }} <br>
                        Pada Tanggal: {{ \Carbon\Carbon::parse($sppd->tanggal_sppd)->translatedFormat('d F Y') }}
                        <br><br>
                        <strong>Kuasa Pengguna Anggaran</strong>
                        <br><br><br><br><br>
                        <strong class="text-underline">ADYA FERINA, S.E., M.Ak.</strong><br>
                        NIP. 19860206 201101 2 005
                    </td>
                </tr>
            </table>
        </div>

        <div class="last-page">
            <table class="main-table">
                @php
                    $tglMulai = \Carbon\Carbon::parse($sppd->tanggal_mulai);
                    $tglSelesai = \Carbon\Carbon::parse($sppd->tanggal_selesai ?: $sppd->tanggal_mulai);
                @endphp

                <tr>
                    <td style="width:50%; height:130px;"></td>

                    <td style="width:50%; vertical-align:top;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:5px; vertical-align:top;"><b>I.</b></td>
                                <td>
                                    Berangkat dari : <br>
                                    (Tempat Kedudukan)
                                    <br>
                                    {{ $sppd->tempat_berangkat }}<br>

                                    Ke : {{ $sppd->tempat_tujuan }}<br>

                                    Pada Tanggal :
                                    {{ $tglMulai->translatedFormat('d F Y') }}

                                    <br>
                                    Pejabat Pelaksana Teknis Kegiatan
                                    <br><br><br><br>

                                    <b>......................................................</b><br>
                                    NIP. ..................................................
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align:top; height:150px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:20px;"><b>II.</b></td>
                                <td>
                                    Tiba di : {{ $sppd->tempat_tujuan }}<br>
                                    Pada Tanggal : {{ $tglMulai->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="vertical-align:top; height:160px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:5px;"></td>
                                <td>
                                    Berangkat dari : {{ $sppd->tempat_tujuan }}<br>
                                    Ke : {{ $sppd->tempat_berangkat }}<br>
                                    Pada Tanggal : {{ $tglSelesai->translatedFormat('d F Y') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align:top; height:160px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:20px;"><b>III.</b></td>
                                <td>
                                    Tiba di : {{ $sppd->tempat_tujuan_2 ?? '' }}<br>
                                    Pada Tanggal :
                                    {{ $sppd->tempat_tujuan_2 ? $tglMulai->copy()->addDay()->translatedFormat('d F Y') : '' }}
                                    <br><br><br>
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="vertical-align:top; height:160px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:5px;"></td>
                                <td>
                                    Berangkat dari : {{ $sppd->tempat_tujuan_2 ?? '' }}<br>
                                    Ke : {{ $sppd->tempat_berangkat }}<br>
                                    Pada Tanggal :
                                    {{ $sppd->tempat_tujuan_2 ? $tglMulai->copy()->addDays(2)->translatedFormat('d F Y') : '' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="vertical-align:top; height:160px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:20px;"><b>IV.</b></td>
                                <td>
                                    Tiba di :<br>
                                    Pada Tanggal :
                                </td>
                            </tr>
                        </table>
                    </td>

                    <td style="vertical-align:top; height:160px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:5px;"></td>
                                <td style="text-align:justify;">
                                    Telah diperiksa, dengan keterangan bahwa perjalanan tersebut di atas
                                    benar dilakukan atas perintahnya dan semata-mata untuk kepentingan
                                    jabatan dalam waktu yang sesingkat-singkatnya.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="height:20px;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:20px;"><b>V.</b></td>
                                <td>Catatan Lain-Lain</td>
                            </tr>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td colspan="2" style="vertical-align:top;">
                        <table class="no-border-table">
                            <tr>
                                <td style="width:20px;"><b>VI.</b></td>
                                <td>
                                    PERHATIAN:<br>
                                    PPK yang menerbitkan SPD, Pegawai yang melakukann perjalanan dinas, para pejabat
                                    yang mengesahkan
                                    tanggal berangkat/tiba, serta bendahara pengeluaran bertanggung jawab berdasarkan
                                    peraturan-peraturan
                                    Keuangan Negara apabila negara menderita rugi akibat kesalahan, kelalaian, dan
                                    kealpaannya
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </div>
        <table style="width: 100%; margin-top: 0px;">
            <tr>
                <td style="width: 55%;"></td>
                <td class="text-center">
                    Dikeluarkan di: {{ $sppd->tempat_berangkat }} <br>
                    Pada Tanggal: {{ \Carbon\Carbon::parse($sppd->tanggal_sppd)->translatedFormat('d F Y') }}
                    <br><br>
                    <strong>Kuasa Pengguna Anggaran</strong>
                    <br><br><br><br><br>
                    <strong class="text-underline">ADYA FERINA, S.E., M.Ak.</strong><br>
                    NIP. 19860206 201101 2 005
                </td>
            </tr>
        </table>
    @endforeach
</body>

</html>
