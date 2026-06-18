<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MonevController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SPPDController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {

    // ======================
    // SUPER ADMIN (FULL)
    // ======================
    Route::middleware(['auth'])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/rekap-pegawai', [DashboardController::class, 'rekapByBulan'])->name('dashboard.rekap');
        Route::get('/dashboard/export-rekap', [DashboardController::class, 'exportExcel'])->name('dashboard.export');

        Route::middleware('role:super_admin')->group(function () {
            Route::resource('pegawai', PegawaiController::class);
            Route::resource('sub-kegiatan', SubKegiatanController::class);
            Route::get('/sub-kegiatan/{id}', [SubKegiatanController::class, 'show']);
            Route::put('/sub-kegiatan/{id}', [SubKegiatanController::class, 'update']);
        });
    });

    Route::get('/monev/{id}', [MonevController::class, 'getBySubActivityId']);
    Route::post('/monev/uraian', [MonevController::class, 'storeUraian'])->name('uraian.store');
    Route::put('/monev/uraian/{id}', [MonevController::class, 'updateUraian'])->name('uraian.update');
    Route::delete('/monev/uraian/{id}', [MonevController::class, 'destroyUraian'])->name('uraian.destroy');

    // ======================
    // NOTA DINAS (SUPER ADMIN + KASUBID + Kabid)
    // ======================
    Route::middleware('role:super_admin,kepala_sub_bidang,kepala_bidang')->group(function () {

        Route::get('/monev/pptk/{id}', [MonevController::class, 'pptkRekap'])->name('monev.pptk.rekap');
        Route::get('/monev/sub-kegiatan/{id}', [MonevController::class, 'subKegiatanDetail'])->name('monev.sub-kegiatan.show');

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

        Route::get('/spj', [App\Http\Controllers\SPJController::class, 'index'])->name('spj.index');
        Route::get('/spj/{id}', [App\Http\Controllers\SPJController::class, 'show'])->name('spj.show');
        Route::post('/spj/{id}/rincian', [App\Http\Controllers\SPJController::class, 'storeRincian'])->name('spj.rincian.store');
        Route::get('/spj/{id}/export-excel', [App\Http\Controllers\SPJController::class, 'exportExcel'])->name('spj.exportExcel');
        Route::put('/spt/{id}/update-nomor', [SPTController::class, 'updateNomor'])->name('spt.updateNomor');
    });

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

        // SPT via Nota Dinas (existing)
        Route::post('/spt/store/{nota_id}', [SPTController::class, 'store'])->name('spt.store');
        Route::get('/spt/cetak/{id}', [SPTController::class, 'cetakSpt'])->name('nota.cetakSpt');
        Route::get('/nota-dinas/{id}/cetak-spt', [SPTController::class, 'cetakSpt'])->name('nota.cetakSptAlt');
        Route::put('/spt/{id}/update-nomor', [SPTController::class, 'updateNomor'])->name('spt.updateNomor');

        // SPT Mandiri (standalone)
        Route::get('/spt', [SPTController::class, 'index'])->name('spt.index');
        Route::get('/spt/buat', [SPTController::class, 'create'])->name('spt.create');
        Route::post('/spt', [SPTController::class, 'storeMandiri'])->name('spt.storeMandiri');
        Route::get('/spt/{id}/cetak-mandiri', [SPTController::class, 'cetakSptMandiri'])->name('spt.cetakMandiri');
        Route::delete('/spt/{id}/hapus-mandiri', [SPTController::class, 'destroyMandiri'])->name('spt.destroyMandiri');

        // SPPD Mandiri (standalone)
        Route::get('/sppd', [SPPDController::class, 'index'])->name('sppd.index');
        Route::get('/sppd/buat', [SPPDController::class, 'create'])->name('sppd.create');
        Route::post('/sppd', [SPPDController::class, 'storeMandiri'])->name('sppd.storeMandiri');
        Route::get('/sppd/{id}/cetak-mandiri', [SPPDController::class, 'cetakSPPDMandiri'])->name('sppd.cetakMandiri');
        Route::delete('/sppd/{id}/hapus-mandiri', [SPPDController::class, 'destroyMandiri'])->name('sppd.destroyMandiri');
    });

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticated'])
    ->name('login.authenticated');
