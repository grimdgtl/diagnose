<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LemonSqueezy\Laravel\LemonSqueezyServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(LemonSqueezyServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
