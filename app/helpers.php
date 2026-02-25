<?php

if (!function_exists('terbilang')) {
    function terbilang($angka)
    {
        $angka = abs($angka);
        $huruf = ["", "satu", "dua", "tiga", "empat", "lima",
                  "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

        if ($angka < 12) {
            return $huruf[$angka];
        } elseif ($angka < 20) {
            return terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            return terbilang(intval($angka / 10)) . " puluh " . terbilang($angka % 10);
        }

        return $angka;
    }
}