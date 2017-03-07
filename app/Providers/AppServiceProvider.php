<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        DB::listen(function ($query) {
//            Log::debug('--------------------------------------------');
//            Log::debug('sql: '.$query->sql);
//            Log::debug('bindings: '.$query->bindings);
//            Log::debug('time: '.$query->time);
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
