<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotaDinasController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\SPTController;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard.index', ['title' => 'Dashboard']);
    })->name('dashboard')->middleware('role:admin,super_admin');

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
});


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticated'])->name('login.authenticated');
