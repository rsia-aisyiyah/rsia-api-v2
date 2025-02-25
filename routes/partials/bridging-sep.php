<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::middleware(['user-aes', 'detail-user', 'claim:role,pegawai|dokter'])->group(function ($router) {
    Orion::resource('sep', \App\Http\Controllers\Orion\BridgingSepController::class)->only(['search', 'show'])->parameters(['sep' => 'no_sep']);

    Route::get('/sep/download/{bulan}/{jenis}', [\App\Http\Controllers\v2\BerkasKlaimDownload::class, 'get']);
    Route::get('/sep/grade-3/pdf', [\App\Http\Controllers\v2\PasienGradeIIIController::class, 'index']);
    Route::post('/sep/download', [\App\Http\Controllers\v2\BerkasKlaimDownload::class, 'download']);

    Route::get('/sep/{no_sep}/print', [\App\Http\Controllers\v2\BerkasKlaimController2::class, 'print']);
    // Route::get('/sep/{no_sep}/print2', [\App\Http\Controllers\v2\BerkasKlaimController2::class, 'print']);
    Route::get('/sep/{no_sep}/export', [\App\Http\Controllers\v2\BerkasKlaimController2::class, 'export']);
    Route::delete('/sep/{no_sep}/delete', [\App\Http\Controllers\v2\BerkasKlaimController2::class, 'delete']);
    Route::get('/sep/{no_sep}/klaim/sync', [\App\Http\Controllers\v2\KlaimController::class, 'sync']);


    Route::resource('/sep/{no_sep}/klaim/status', \App\Http\Controllers\v2\RsiaStatusKlaimController::class)->only(['store']);
    Route::resource('/sep/{no_sep}/klaim/logs', \App\Http\Controllers\v2\RsiaLogStatusKlaim::class)->only(['index']);
    Route::post('/sep/klaim/status/search', [\App\Http\Controllers\v2\StatusKlaimSepController::class, 'search']);

    Route::resource('/sep/{no_sep}/klaim/latest', \App\Http\Controllers\v2\HasilGroupingController::class)->only(['index']);
});

Route::middleware(['user-aes', 'detail-user', 'claim:role,pegawai|dokter'])->group(function ($router) {
    Route::get('klaim/bupel', [\App\Http\Controllers\RsiaKlaimBupelRsController::class, 'index']);
    Route::post('klaim/bupel', [\App\Http\Controllers\RsiaKlaimBupelRsController::class, 'update']);
});
