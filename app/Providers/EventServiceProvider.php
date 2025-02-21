<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LemonSqueezy\Laravel\Events\OrderCreated;
use App\Listeners\IncrementUserQuestions;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Kad stigne webhook i “OrderCreated” event,
        // poziva se IncrementUserQuestions listener:
        OrderCreated::class => [
            IncrementUserQuestions::class,
        ],
    ];

    public function boot()
    {
        parent::boot();

        // Ovde ide eventualna custom logika, ako treba
    }
}
