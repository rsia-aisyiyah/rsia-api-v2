<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['user-aes'])->prefix('user')->group(function () {
    Route::prefix('menu')->group(function () {
        Route::apiResource('filetrack', \App\Http\Controllers\v2\RsiaMasterMenuFiletrackController::class)->only('index')->parameters(['filetrack' => 'id']);
        Route::get('/{id}', [\App\Http\Controllers\v2\RsiaMenuController::class, 'show'])->name('menu.show');
    });
});
