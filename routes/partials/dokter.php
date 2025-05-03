<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function ($router) {
    Orion::resource('dokter', \App\Http\Controllers\Orion\DokterController::class)->only(['search', 'index', 'show'])
        ->parameters(['dokter' => 'nik']);
});
