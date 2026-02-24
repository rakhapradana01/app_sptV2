<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SubKegiatanController;

Route::middleware(['auth'])->group(function () {

    // =========================
    // SUPER ADMIN ONLY
    // =========================
    Route::middleware('role:super_admin')->group(function () {

        Route::get('/', function () {
            return view('pages.dashboard.index', ['title' => 'Dashboard']);
        })->name('dashboard');

        Route::resource('pegawai', PegawaiController::class);
        Route::resource('sub-kegiatan', SubKegiatanController::class);
    });

    // =========================
    // SEMUA ROLE YANG BOLEH NOTA DINAS
    // =========================
    Route::middleware('role:super_admin,kepala_sub_bidang,kepala_bidang,user')
        ->group(function () {

            Route::resource('nota-dinas', NotaDinasController::class)
                ->parameters([
                    'nota-dinas' => 'nota'
                ]);

            Route::patch('/nota-dinas/{nota}/kirim-kasubid',
                [NotaDinasController::class, 'kirimKasubid'])
                ->name('nota-dinas.kirim-kasubid');

            Route::patch('/nota-dinas/{nota}/approve-kasubid',
                [NotaDinasController::class, 'approveKasubid'])
                ->name('nota-dinas.approve-kasubid');

            Route::patch('/nota-dinas/{nota}/approve-kabid',
                [NotaDinasController::class, 'approveKabid'])
                ->name('nota-dinas.approve-kabid');
        });

    // =========================
    // LOGOUT & SPT (SEMUA LOGIN)
    // =========================
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/spt', [SPTController::class, 'index'])
        ->name('spt.index');
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticated'])
    ->name('login.authenticated');