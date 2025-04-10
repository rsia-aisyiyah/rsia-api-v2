<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:aes', 'claim:role,pegawai|dokter'])->prefix('undangan')->group(function () {
    // ==================== PENERIMA UNDANGAN
    Orion::resource('penerima', \App\Http\Controllers\Orion\RsiaPenerimaUndanganController::class)->only(['search'])->parameters(['penerima' => 'base64_no_surat']);        // INFO : selesai
    Route::apiResource('penerima', \App\Http\Controllers\v2\RsiaPenerimaUndanganController::class)->only(['store', 'show'])->parameters(['penerima' => 'base64_no_surat']); // TODO : cek lagi   
    Route::get('penerima/{base64_no_surat}/proof', [\App\Http\Controllers\v2\RsiaPenerimaUndanganController::class, 'proofDownload'])->name('penerima.proof');              // INFO : selesai
    // ==================== KEHADIRAN RAPAT
    Route::apiResource('kehadiran', \App\Http\Controllers\v2\RsiaKehadiranRapatController::class)->only(['store', 'show'])->parameters(['kehadiran' => 'base64_no_surat']); // TODO : cek lagi
});

Route::middleware(['auth:aes', 'claim:role,pegawai|dokter'])->group(function () {
    Orion::resource('undangan', \App\Http\Controllers\Orion\RsiaUndanganController::class)->only('search')->parameters(['undangan' => 'base64_no_surat']);                  // INFO : selesai
    Route::apiResource('undangan', \App\Http\Controllers\v2\RsiaUndanganController::class)->only(['show'])->parameters(['undangan' => 'base64_no_surat']);                  // TODO : cek lagi
    Route::get('undangan/{base64_no_surat}/download', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'download'])->name('undangan.download');                     // INFO : selesai
    Route::get('undangan/{base64_no_surat}/notulen', [\App\Http\Controllers\v2\RsiaUndanganController::class, 'notulen'])->name('undangan.notulen');                        // INFO : selesai
    Route::resource('agenda', \App\Http\Controllers\v2\AgendaController::class)->only(['index']);
});
