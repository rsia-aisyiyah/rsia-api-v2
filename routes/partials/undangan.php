<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:aes', 'claim:role,pegawai|dokter'])->prefix('undangan')->group(function () {
    // ==================== PENERIMA UNDANGAN
    Orion::resource('penerima', \App\Http\Controllers\Orion\RsiaPenerimaUndanganController::class)->only(['search']);
    Route::apiResource('penerima', \App\Http\Controllers\v2\RsiaPenerimaUndanganController::class)->only(['store', 'show'])->parameters(['penerima' => 'undangan_id']); // TODO : cek lagi   
    
    // ==================== KEHADIRAN RAPAT
    Route::apiResource('kehadiran', \App\Http\Controllers\v2\RsiaKehadiranRapatController::class)->only(['store', 'show'])->parameters(['kehadiran' => 'undangan_id']); // TODO : cek lagi
});

Route::middleware(['auth:aes', 'claim:role,pegawai|dokter'])->group(function () {
    Orion::resource('undangan', \App\Http\Controllers\Orion\RsiaUndanganController::class)->only('search', 'show')->parameters(['undangan' => 'base64_no_surat']);
    // Route::apiResource('undangan', \App\Http\Controllers\v2\RsiaUndanganController::class)->only(['show'])->parameters(['undangan' => 'base64_no_surat']);                  // TODO : cek lagi
    Route::get('undangan/{undangan_id}/qr', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'qrContent'])->name('undangan.qr');
    Route::get('undangan/{undangan_id}/proof', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'proof'])->name('penerima.proof');
    Route::get('undangan/{undangan_id}/download', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'download'])->name('undangan.download');
    Route::get('undangan/{undangan_id}/notulen', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'notulen'])->name('undangan.notulen');
    Route::resource('agenda', \App\Http\Controllers\v2\AgendaController::class)->only(['index']);
});
