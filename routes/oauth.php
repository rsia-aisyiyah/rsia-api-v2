<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\HandleTokensController;
use App\Http\Controllers\Auth\AuthorizationController;
use Laravel\Passport\Http\Controllers\ScopeController;
use Laravel\Passport\Http\Controllers\ClientController;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\TransientTokenController;
use Laravel\Passport\Http\Controllers\DenyAuthorizationController;
use Laravel\Passport\Http\Controllers\PersonalAccessTokenController;
use Laravel\Passport\Http\Controllers\ApproveAuthorizationController;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController;


Route::middleware(['web'])->group(function () {
    // Routes untuk Token Management
    Route::prefix('app/token')->group(function () {
        Route::get('/', [HandleTokensController::class, 'index'])->name('oauth.token.index');
        Route::post('/delete/expired', [HandleTokensController::class, 'deleteExpired'])->name('oauth.token.delete.expired');
        Route::post('/delete/revoked', [HandleTokensController::class, 'deleteRevoked'])->name('oauth.token.delete.revoked');
        Route::delete('/{id}/destroy', [HandleTokensController::class, 'destroy'])->name('oauth.token.destroy');
        Route::post('/{id}/revoke', [HandleTokensController::class, 'revoke'])->name('oauth.token.revoke');
    });

    // Routes untuk OAuth Authorization
    Route::get('oauth/authorize', [AuthorizationController::class, 'authorize'])->name('passport.authorizations.authorize');
    Route::post('oauth/authorize', [ApproveAuthorizationController::class, 'approve'])->name('passport.authorizations.approve');
    Route::delete('oauth/authorize', [DenyAuthorizationController::class, 'deny'])->name('passport.authorizations.deny');
    
    // Routes untuk OAuth Clients
    Route::prefix('oauth/clients')->group(function () {
        Route::get('/', [ClientController::class, 'forUser'])->name('passport.clients.index');
        Route::post('/', [ClientController::class, 'store'])->name('passport.clients.store');
        Route::put('/{client_id}', [ClientController::class, 'update'])->name('passport.clients.update');
        Route::delete('/{client_id}', [ClientController::class, 'destroy'])->name('passport.clients.destroy');
    });
    
    // Routes untuk Personal Access Tokens
    Route::prefix('oauth/personal-access-tokens')->group(function () {
        Route::get('/', [PersonalAccessTokenController::class, 'forUser'])->name('passport.personal.tokens.index');
        Route::post('/', [PersonalAccessTokenController::class, 'store'])->name('passport.personal.tokens.store');
        Route::delete('/{token_id}', [PersonalAccessTokenController::class, 'destroy'])->name('passport.personal.tokens.destroy');
    });
    
    // Routes untuk Scopes
    Route::get('oauth/scopes', [ScopeController::class, 'all'])->name('passport.scopes.index');
    
    // Routes untuk Token Handling
    Route::post('oauth/token/refresh', [TransientTokenController::class, 'refresh'])->name('passport.token.refresh');
    Route::get('oauth/tokens', [AuthorizedAccessTokenController::class, 'forUser'])->name('passport.tokens.index');
    Route::delete('oauth/tokens/{token_id}', [AuthorizedAccessTokenController::class, 'destroy'])->name('passport.tokens.destroy');
});


Route::post('oauth/token', [AccessTokenController::class, 'issueToken'])->name('passport.token')->middleware('throttle');
