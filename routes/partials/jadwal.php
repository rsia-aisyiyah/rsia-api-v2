<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function ($router) {
    Orion::resource('jadwal', \App\Http\Controllers\Orion\JadwalDokterController::class)->only(['search', 'index']);
});
