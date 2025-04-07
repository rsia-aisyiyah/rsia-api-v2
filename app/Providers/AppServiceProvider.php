<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ========== SFTP STORAGE
        \Illuminate\Support\Facades\Storage::extend('sftp', function ($app, $config) {
            return new \League\Flysystem\Filesystem(new \League\Flysystem\Sftp\SftpAdapter($config));
        });

        // ========== CONFIG LOCALE
        config(['app.locale' => 'id']);
        
        setlocale(LC_TIME, 'id_ID.utf8');
        setlocale(LC_ALL, 'IND');

        \Carbon\Carbon::setLocale('id');

        // ========== FORCE HTTPS
        if($this->app->environment('production')) {
            $this->app['request']->server->set('HTTPS','on');
        }

        // ========== PASSPORT SCOPES
        Passport::tokensCan(config('passport.scopes'));
    }
}
