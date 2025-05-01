<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminEmail
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (! $user || $user->email !== 'dev@dijagnoza.com') {
            abort(403);
        }

        return $next($request);
    }
}
