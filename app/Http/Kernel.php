<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /* --------------------------------------------------
     |  Global HTTP middleware
     |-------------------------------------------------- */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /* --------------------------------------------------
     |  Middleware grupe
     |-------------------------------------------------- */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /* --------------------------------------------------
     |  Alias-i (umesto starog $routeMiddleware)
     |-------------------------------------------------- */
    protected $middlewareAliases = [
        'auth'          => \App\Http\Middleware\Authenticate::class,
        'guest'         => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'verified'      => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'throttle'      => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        /* Filament */
        'filament.admin'=> \App\Http\Middleware\EnsureAdminEmail::class,

        /* Naš limiter za neregistrovane korisnike */
        'guest.limit' => \App\Http\Middleware\GuestLimit::class,
    ];

    /* --------------------------------------------------
     |  Artisan komande
     |-------------------------------------------------- */
    protected $commands = [
        \App\Console\Commands\GenerateSitemap::class,
    ];
}
