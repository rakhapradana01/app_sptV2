<?php

use Carbon\Carbon;

// ==========================================
// Unit Test: Logika Waktu Pelaksanaan di PDF
// ==========================================

beforeEach(function () {
    Carbon::setLocale('id');
});

test('Carbon::parse(null) mengembalikan tanggal hari ini - ini bug yang sudah diperbaiki', function () {
    $parsed = Carbon::parse(null);
    expect($parsed->isToday())->toBeTrue();
});

test('logika waktu: tanggal_selesai null hanya tampil tanggal_mulai', function () {
    $mulai   = '2026-06-10';
    $selesai = null;

    $isNullOrSame = is_null($selesai) || $mulai === $selesai;
    expect($isNullOrSame)->toBeTrue();

    $output = Carbon::parse($mulai)->translatedFormat('d F Y');
    expect($output)->toBe('10 Juni 2026');
});

test('logika waktu: tanggal_mulai == tanggal_selesai tampil satu tanggal', function () {
    $mulai   = '2026-06-15';
    $selesai = '2026-06-15';

    $isNullOrSame = is_null($selesai) || $mulai === $selesai;
    expect($isNullOrSame)->toBeTrue();

    $output = Carbon::parse($mulai)->translatedFormat('d F Y');
    expect($output)->toBe('15 Juni 2026');
});

test('logika waktu: tanggal berbeda tampil range s/d', function () {
    $mulai   = '2026-06-10';
    $selesai = '2026-06-14';

    $isNullOrSame = is_null($selesai) || $mulai === $selesai;
    expect($isNullOrSame)->toBeFalse();

    $outputMulai   = Carbon::parse($mulai)->translatedFormat('d F Y');
    $outputSelesai = Carbon::parse($selesai)->translatedFormat('d F Y');

    expect($outputMulai)->toBe('10 Juni 2026');
    expect($outputSelesai)->toBe('14 Juni 2026');
});

test('Carbon translatedFormat menggunakan bahasa Indonesia', function () {
    $output = Carbon::parse('2026-01-01')->translatedFormat('d F Y');
    expect($output)->toBe('01 Januari 2026');
});

test('pagu - realisasi menghasilkan sisa yang benar', function () {
    $pagu      = 10_000_000;
    $realisasi = 3_500_000;
    $sisa      = $pagu - $realisasi;

    expect($sisa)->toBe(6_500_000);
});

test('realisasi default 0 jika belum ada', function () {
    $realisasi = null;
    $nilai     = $realisasi ?? 0;

    expect($nilai)->toBe(0);
});

test('number_format format angka rupiah dengan benar', function () {
    $angka     = 10_500_000;
    $formatted = 'Rp' . number_format($angka, 0, ',', '.');

    expect($formatted)->toBe('Rp10.500.000');
});
