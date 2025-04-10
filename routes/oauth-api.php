<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::middleware(['throttle'])->group(function () {
    Route::post('oauth/token', [AccessTokenController::class, 'issueToken'])->name('passport.token');
});
