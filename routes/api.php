<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HierarchyController;

Route::get('/bidangs/{dinas_id?}', [HierarchyController::class, 'getBidangs']);
Route::get('/sub-bidangs/{bidang_id?}', [HierarchyController::class, 'getSubBidangs']);

