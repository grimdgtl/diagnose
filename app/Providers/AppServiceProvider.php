<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LemonSqueezy\Laravel\LemonSqueezyServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(LemonSqueezyServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
