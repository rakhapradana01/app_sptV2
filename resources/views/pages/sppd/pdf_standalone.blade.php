<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>SPD Standalone</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.0cm 1.5cm;
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
            padding: 4px 8px;
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
    </style>
</head>

<body>
    @foreach ($sppd->pegawais as $p)
        {{-- SPPD DEPAN (Halaman 1) --}}
        <div class="page-break">
            {{-- Bagian Kop Surat --}}
            <table>
                @include('components.kop-surat')
            </table>

            <table style="width: 100%;">
                <tr>
                    <td style="width: 45%;"></td>
                    <td>
                        <table class="no-border-table" style="font-size: 9pt;">
                            <tr>
                                <td style="width: 55px;">Lembar ke</td>
                                <td style="width: 10px; text-align: center;">:</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Kode No</td>
                                <td style="text-align: center;">:</td>
                                <td></td>
                            </tr>
                            @php
                                $rawSppd = trim($sppd->nomor_sppd ?? '');
                                $isiInputan = '';
                                $tahun = '2026';

                                if (!empty($rawSppd)) {
                                    if (str_contains($rawSppd, '/')) {
                                        $parts = explode('/', $rawSppd);
                                        $isiInputan = trim($parts[1] ?? '');
                                        if (!empty($parts[3])) {
                                            $tahun = trim($parts[3]);
                                        }
                                    } else {
                                        if (!in_array($rawSppd, ['900.1.2.3', '800.1.11.1', '000.1.2.3'])) {
                                            $isiInputan = $rawSppd;
                                        }
                                    }
                                }

                                if ($isiInputan === '-' || $isiInputan === '.') {
                                    $isiInputan = '';
                                }
                            @endphp
                            <tr>
                                <td>Nomor</td>
                                <td style="text-align: center;">:</td>
                                <td style="white-space: nowrap;">
                                    @if (!empty($isiInputan))
                                        800.1.11.1/{{ $isiInputan }}/BPKAD/{{ $tahun }}
                                    @else
                                        900.1.2.3/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/BPKAD/{{ $tahun }}
                                    @endif
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
                    <td>Pengikut:</td>
                    <td></td>
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

            <table style="width: 100%; margin-top: 20px;">
                <tr>
                    <td style="width: 55%;"></td>
                    <td class="text-center">
                        Dikeluarkan di: {{ $sppd->tempat_berangkat }} <br>
                        Pada Tanggal: {{ \Carbon\Carbon::parse($sppd->tanggal_mulai)->subDay()->translatedFormat('d F Y') }}
                        <br><br>
                        <strong>Kuasa Pengguna Anggaran</strong>
                        <br><br><br><br><br>
                        <strong class="text-underline">ADYA FERINA, S.E., M.Ak.</strong><br>
                        NIP. 19860206 201101 2 005
                    </td>
                </tr>
            </table>
        </div>

        @endforeach

    {{-- SPPD BELAKANG (1 Lembar di Akhir) --}}
    @php
        $tglMulai = \Carbon\Carbon::parse($sppd->tanggal_mulai);
        $tglSelesai = \Carbon\Carbon::parse($sppd->tanggal_selesai ?: $sppd->tanggal_mulai);
    @endphp
    <div>
        <table class="main-table">

            <tr>
                <td style="width:50%; height:95px;"></td>

                <td style="width:50%; vertical-align:top;">
                    <table class="no-border-table">
                        <tr>
                            <td style="width:5px; vertical-align:top;"><b>I.</b></td>
                            <td>
                                Berangkat dari:                                 {{ $sppd->tempat_berangkat }}<br>

                                Ke : {{ $sppd->tempat_tujuan }}<br>

                                Pada Tanggal :
                                {{ $tglMulai->translatedFormat('d F Y') }}

                                <br>
                                Pejabat Pelaksana Teknis Kegiatan
                                <br><br><br><br>

                                <b>{{ auth()->user()->pegawai->nama ?? auth()->user()->name }}</b><br>
                                NIP. {{ auth()->user()->pegawai->nip ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="vertical-align:top; height:110px;">
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

                <td style="vertical-align:top; height:110px;">
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
                <td style="vertical-align:top; height:110px;">
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

                <td style="vertical-align:top; height:110px;">
                    <table class="no-border-table">
                        <tr>
                            <td style="width:5px;"></td>
                            <td>
                                Berangkat dari : {{ $sppd->tempat_tujuan_2 ?? '' }}<br>
                                Ke : {{ $sppd->tempat_berangkat_2 ??''}}<br>
                                Pada Tanggal :
                                {{ $sppd->tempat_tujuan_2 ? $tglMulai->copy()->addDays(2)->translatedFormat('d F Y') : '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="vertical-align:top; height:110px;">
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

                <td style="vertical-align:top; height:110px;">
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

        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="width: 55%;"></td>
                <td class="text-center">
                    Dikeluarkan di: {{ $sppd->tempat_berangkat }} <br>
                    Pada Tanggal: {{ \Carbon\Carbon::parse($sppd->tanggal_mulai)->subDay()->translatedFormat('d F Y') }}
                    <br><br>
                    <strong>Kuasa Pengguna Anggaran</strong>
                    <br><br><br><br><br>
                    <strong class="text-underline">ADYA FERINA, S.E., M.Ak.</strong><br>
                    NIP. 19860206 201101 2 005
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
