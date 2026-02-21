<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SPTController;
use App\Http\Controllers\SubKegiatanController;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard.index', ['title' => 'Dashboard']);
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    // SPT Route
    Route::get('/spt', [SPTController::class, 'index'])->name('spt.index');

    //auth user, kasubid, kabid, kaban
    Route::resource('nota-dinas', NotaDinasController::class)
        ->parameters([
            'nota-dinas' => 'nota'
        ]);

    //harus auth super admin
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('sub-kegiatan', SubKegiatanController::class);
    Route::patch('/nota-dinas/{nota}/kirim-kasubid', [NotaDinasController::class, 'kirimKasubid'])
        ->name('nota-dinas.kirim-kasubid');

    Route::patch('/nota-dinas/{nota}/approve-kasubid', [NotaDinasController::class, 'approveKasubid'])
        ->name('nota-dinas.approve-kasubid');

    Route::patch('/nota-dinas/{nota}/approve-kabid', [NotaDinasController::class, 'approveKabid'])
        ->name('nota-dinas.approve-kabid');
        
});


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticated'])->name('login.authenticated');
