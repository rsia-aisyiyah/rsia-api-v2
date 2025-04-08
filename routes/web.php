<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth'])->name('dashboard');
    Route::get('/notifikasi/pasien', [\App\Http\Controllers\web\NotifikasiPasienController::class, 'index'])->name('notifikasi.pasien');
    Route::get('/notifikasi/pasien/jadwal-dokter', [\App\Http\Controllers\web\JadwalDokterController::class, 'index'])->name('notifikasi.pasien.jadwal-dokter');
    Route::post('/notifikasi/pasien/jadwal-dokter/send', [\App\Http\Controllers\web\JadwalDokterController::class, 'store'])->name('notifikasi.pasien.jadwal-dokter.send');

    // Group route
    Route::group(['prefix' => 'app'], function () {
        Route::group(['prefix' => 'client'], function () {
            Route::get('', [\App\Http\Controllers\web\HandleClietController::class, 'index'])->name('oauth.client.index');
            Route::get('/create', [\App\Http\Controllers\web\HandleClietController::class, 'create'])->name('oauth.client.create');
            Route::post('/store', [\App\Http\Controllers\web\HandleClietController::class, 'store'])->name('oauth.client.store');
            Route::get('/{client_id}/edit', [\App\Http\Controllers\web\HandleClietController::class, 'edit'])->name('oauth.client.edit');
            Route::put('/{client_id}/put', [\App\Http\Controllers\web\HandleClietController::class, 'update'])->name('oauth.client.update');
            Route::delete('/{client_id}/destroy', [\App\Http\Controllers\web\HandleClietController::class, 'destroy'])->name('oauth.client.destroy');
        });

        Route::group(['prefix' => 'token'], function () {
            Route::get('', [\App\Http\Controllers\web\HandleTokensController::class, 'index'])->name('oauth.token.index');
            Route::post('{id}/revoke', [\App\Http\Controllers\web\HandleTokensController::class, 'revoke'])->name('oauth.token.revoke');
            Route::delete('{id}/destroy', [\App\Http\Controllers\web\HandleTokensController::class, 'destroy'])->name('oauth.token.destroy');

            Route::post('delete/expired', [\App\Http\Controllers\web\HandleTokensController::class, 'deleteExpired'])->name('oauth.token.delete.expired');
            Route::post('delete/revoked', [\App\Http\Controllers\web\HandleTokensController::class, 'deleteRevoked'])->name('oauth.token.delete.revoked');
        });
    });
});

require __DIR__ . '/auth.php';

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
// Route::get('/horizon/{view?}', [\App\Http\Controllers\HorizonCustomHomeController::class, 'index'])->where('view', '(.*)')->name('horizon.index');