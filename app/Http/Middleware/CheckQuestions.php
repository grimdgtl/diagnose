<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckQuestions
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->num_of_questions_left <= 0 || now()->greaterThan($user->subscription_expires_at)) {
            return response()->json(['error' => 'You need to buy more questions.'], 403);
        }

        return $next($request);
    }
}
