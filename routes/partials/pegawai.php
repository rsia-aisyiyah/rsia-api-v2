<?php

use Orion\Facades\Orion;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:aes', 'claim:role,pegawai|dokter'])->group(function () {

  Route::get('pegawai/get', [\App\Http\Controllers\v2\PegawaiController::class, 'get'])->name('pegawai.get');
  
  // ==================== PEGAWAI
  Orion::resource('pegawai', \App\Http\Controllers\Orion\PegawaiController::class)->only(['search', 'show']);
  Route::resource('pegawai', \App\Http\Controllers\v2\PegawaiController::class)->except(['create', 'edit', 'show'])->parameters(['pegawai' => 'nik']);

  // ==================== UPDATE PROFILE PEGAWAI
  Orion::belongsToManyResource('pegawai', 'cuti', \App\Http\Controllers\Orion\CutiPegawaiController::class)->only(['search'])->parameters(['pegawai' => 'nik', 'cuti' => 'id_cuti']);
  Route::get('pegawai/{nik}/cuti/counter', [\App\Http\Controllers\v2\CutiPegawaiController::class, 'counterCuti'])->name('pegawai.cuti.counter');
  Route::apiResource('pegawai.cuti', \App\Http\Controllers\v2\CutiPegawaiController::class)->except(['create', 'edit'])->parameters(['pegawai' => 'nik', 'cuti' => 'id_cuti']);

  // ==================== UPDATE PROFILE PEGAWAI
  Route::post('pegawai/{pegawai}/profile', [\App\Http\Controllers\v2\PegawaiController::class, 'updateProfile'])
    ->name('pegawai.update-profile');

  // ==================== BERKAS PEGAWAI
  Route::resource('pegawai.berkas', \App\Http\Controllers\v2\BerkasPegawaiController::class)
    ->except(['create', 'edit'])
    ->parameters(['pegawai' => 'nik', 'berkas' => 'kode_berkas']);

  // ==================== JASA PEGAWAI
  Route::get('pegawai/{nik}/jasa/medis', [\App\Http\Controllers\v2\JasaPegawaiController::class, 'jm'])->middleware(['detail-user'])->name('pegawai.jasa-medis');
  Route::get('pegawai/{nik}/jasa/pelayanan', [\App\Http\Controllers\v2\JasaPegawaiController::class, 'jaspel'])->middleware(['detail-user'])->name('pegawai.jasa-pelayanan');

  // ==================== JADWAL PEGAWAI
  Orion::resource('pegawai.jadwal', \App\Http\Controllers\Orion\JadwalPegawaiController::class)->only('search')
    ->parameters(['pegawai' => 'nik', 'jadwal' => 'id']);
  Route::resource('pegawai.jadwal', \App\Http\Controllers\v2\JadwalPegawaiController::class)->only(['index'])
    ->parameters(['pegawai' => 'nik', 'jadwal' => 'id']);

  // ==================== PRESENSI PEGAWAI
  Orion::belongsToManyResource('pegawai', 'presensi', \App\Http\Controllers\Orion\PresensiKaryawanController::class)->only(['index', 'search']);
  Route::resource('pegawai/{pegawai}/presensi/temporary', \App\Http\Controllers\v2\RsiaTemporaryPresensiController::class)->only(['index']);
});
