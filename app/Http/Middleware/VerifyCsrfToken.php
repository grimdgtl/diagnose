<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'lemon-squeezy-webhook',
    ];

    public function handle($request, Closure $next)
    {
        \Log::info('VerifyCsrfToken middleware triggered for path: ' . $request->getPathInfo());
        return parent::handle($request, $next);
    }
}
