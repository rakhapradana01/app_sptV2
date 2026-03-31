<table class="header-table">
    <tr>
        <td style="width: 80px;">
            <img src="{{ public_path('/images/logo.png') }}" class="logo">
        </td>

        <td class="kop-text">
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

        <td style="width: 80px;"></td>
    </tr>
</table>

<div class="garis-kop"></div>

<style>
    .header-table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    .logo {
        width: 70px;
    }

    .kop-text {
        text-align: center;
        line-height: 1.3;
    }

    .kop-judul-atas {
        font-size: 12pt;
        font-weight: bold;
    }

    .kop-judul-bawah {
        font-size: 14pt;
        font-weight: bold;
    }

    .kop-alamat {
        font-size: 10pt;
        font-weight: normal;
        /* ini penting biar alamat gak ikut bold */
    }

    .garis-kop {
        border-top: 3px solid black;
        border-bottom: 1px solid black;
        margin-top: 6px;
        margin-bottom: 15px;
    }
</style>
