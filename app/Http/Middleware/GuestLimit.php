<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TempQuestion;
use App\Models\TempAdvisorMessage;
use App\Models\TempAdvisorChat;

class GuestLimit
{
    public function handle(Request $request, Closure $next)
    {
        /* ---------------- DIJAGNOZA ---------------- */
        if ($id = $request->session()->get('temp_id')) {
            if (TempQuestion::where('temp_id', $id)->count() >= 2) {
                return redirect()->route('register')
                     ->with('error', 'Registruj se da bi postavio nova pitanja.');
            }
            if ($request->routeIs('guest.wizard-form', 'guest.store-temp-data')) {
                return redirect()->route('dashboard');
            }
        }

        /* ---------------- SAVETNIK (gost) ----------------- */
        if ($aid = $request->session()->get('advisor_temp_id')) {
            // 1. Pronađi chat za tog gosta
            $chat = TempAdvisorChat::where('temp_id', $aid)->first();

            // 2. Ako chat postoji i korisnik je već poslao ≥ 2 poruke → registracija
            if ($chat && TempAdvisorMessage::where('chat_id', $chat->id)
                                          ->where('role', 'user')
                                          ->count() >= 2) {
                return redirect()->route('register')
                         ->with('error', 'Registruj se da bi nastavio konverzaciju.');
            }

            // 3. Ako ideš u wizard, a već imaš otvoren chat, preusmjeri na taj chat
            if ($request->routeIs('advisor.guest.wizard', 'advisor.guest.wizard.store') && $chat) {
                return redirect()->route('advisor.guest.chat', $chat->id);
            }
        }

        return $next($request);
    }
}
