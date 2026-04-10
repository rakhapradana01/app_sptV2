<table class="header-table">
    <tr>
        <td style="width: 15%; text-align: left;">
            <img src="{{ public_path('/images/logo.png') }}" class="logo">
        </td>

        <td style="width: 70%;" class="kop-text">
            <div class="kop-judul-atas">
                PEMERINTAH PROVINSI KALIMANTAN SELATAN
            </div>
            <div class="kop-judul-bawah">
                BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH
            </div>
            <div class="kop-alamat">
                Jl. Raya Dharma Praja Kawasan Perkantoran Pemprov Kalsel<br>
                BANJARBARU
            </div>
        </td>

        <td style="width: 15%;"></td>
    </tr>
</table>

<div class="garis-kop"></div>

<style>
    .header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .logo {
        width: 70px;
    }

    /* Paksa teks judul agar tidak pecah/turun ke bawah */
    .kop-judul-atas,
    .kop-judul-bawah {
        white-space: nowrap;
        font-weight: bold;
    }

    .kop-judul-atas {
        font-size: 14pt;
        /* Sesuaikan ukuran */
    }

    .kop-judul-bawah {
        font-size: 16pt;
    }

    .kop-text {
        text-align: center;
        /* Berikan padding agar tidak mepet logo jika teks terlalu panjang */
        padding: 0 10px;
    }

    .kop-alamat {
        font-size: 10pt;
        font-weight: normal;
    }

    .garis-kop {
        border-top: 3px solid black;
        border-bottom: 1px solid black;
        height: 2px;
        /* Memberi jarak antar garis */
        margin-top: 6px;
        margin-bottom: 15px;
    }
</style>
