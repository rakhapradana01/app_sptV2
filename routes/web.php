<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SPPDController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SubKegiatanController;

Route::middleware(['auth'])->group(function () {

    // ======================
    // SUPER ADMIN (FULL)
    // ======================
    Route::middleware(['auth'])->group(function () {

        Route::get('/', function () {
            return view('pages.dashboard.index');
        })->name('dashboard');

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('pegawai', PegawaiController::class);
            Route::resource('sub-kegiatan', SubKegiatanController::class);
        });
    });

    // ======================
    // NOTA DINAS (SUPER ADMIN + KASUBID + Kabid)
    // ======================
    Route::middleware('role:super_admin,kepala_sub_bidang,kepala_bidang')->group(function () {

        Route::resource('nota-dinas', NotaDinasController::class)
            ->parameters(['nota-dinas' => 'nota']);

        Route::patch(
            '/nota-dinas/{nota}/approve-kasubid',
            [NotaDinasController::class, 'approveKasubid']
        )->name('nota-dinas.approve-kasubid');
        Route::patch('/nota-dinas/{id}/revisi-kabid', [NotaDinasController::class, 'revisiKabid'])->name('nota-dinas.revisi-kabid');
        Route::patch('/nota-dinas/{id}/reject-kabid', [NotaDinasController::class, 'rejectKabid'])->name('nota-dinas.reject-kabid');
        Route::get(
            '/nota-dinas/{nota}/preview',
            [NotaDinasController::class, 'preview']
        )->name('nota-dinas.preview');

        Route::post(
            '/nota-dinas/{nota}/pegawai',
            [NotaDinasController::class, 'storePegawai']
        )->name('nota-dinas.pegawai.store');

        Route::delete(
            '/nota-dinas/{nota}/pegawai/{pegawai}',
            [NotaDinasController::class, 'destroyPegawai']
        )->name('nota-dinas.pegawai.destroy');

        Route::get('/nota-dinas/{nota}/cetak', [NotaDinasController::class, 'cetakNotaDinas'])
            ->name('nota.cetakNotaDinas');

        Route::post('/sppd/store/{notaId}', [SPPDController::class, 'store'])->name('nota.storeSppd');
        Route::get('/sppd/cetak/{id}', [SPPDController::class, 'cetakSPPD'])->name('nota.cetakSPPD');
        
        //Arsip
        Route::get('/arsip', [NotaDinasController::class, 'arsip'])->name('arsip');
    });

    // ======================
    // APPROVE KABID
    // ======================
    Route::middleware('role:super_admin,kepala_bidang')->group(function () {

        Route::patch(
            '/nota-dinas/{nota}/approve-kabid',
            [NotaDinasController::class, 'approveKabid']
        )
            ->name('nota-dinas.approve-kabid');
    });

    // ======================
    // SPT (SUPER ADMIN + KASUBID)
    // ======================
    Route::middleware('role:super_admin,kepala_sub_bidang')->group(function () {

        Route::resource('spt', SPTController::class);
        Route::post('/spt/store/{nota_id}', [SPTController::class, 'store'])->name('spt.store');
        Route::get('/spt/cetak/{id}', [App\Http\Controllers\SPTController::class, 'cetakSpt'])->name('nota.cetakSpt');
        Route::get('/nota-dinas/{id}/cetak-spt', [SPTController::class, 'cetakSpt'])->name('nota.cetakSpt');
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticated'])
    ->name('login.authenticated');
