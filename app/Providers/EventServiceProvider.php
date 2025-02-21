<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use LemonSqueezy\Laravel\Events\OrderCreated;
use App\Listeners\IncrementUserQuestions;

/**
 * EventServiceProvider za upravljanje događajima u aplikaciji.
 * Registruje listener-e za događaje, uključujući LemonSqueezy webhook-ove.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Niz događaja i odgovarajućih listener-a koji se pozivaju.
     * 
     * @var array
     */
    protected $listen = [
        // Registracija listener-a za događaj kada se kreira narudžbina preko LemonSqueezy
        // Listener IncrementUserQuestions ažurira korisničke kredite za pitanja
        OrderCreated::class => [
            IncrementUserQuestions::class,
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Ovde možeš dodati custom logiku za dodatne događaje ili proširenja, ako je potrebno
    }
}