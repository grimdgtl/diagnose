<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\OrderCreated;
use App\Models\User;
use Carbon\Carbon;

class IncrementUserQuestions
{
    public function handle(OrderCreated $event)
    {
        // Iz payload-a eventa izdvojiš email, variant_id, i sl.
        $payload = $event->payload;
        
        $email = $payload['data']['attributes']['billing_address']['email'] ?? null;
        $variantId = $payload['data']['attributes']['variant_id'] ?? null;

        // Nađi user-a u bazi
        $user = User::where('email', $email)->first();
        if (! $user) {
            return;
        }

        // Ažuriraj num_of_questions_left (+20 / +500)
        if ($variantId == '681064') {
            $user->num_of_questions_left += 20;
            // $user->questions_expires_at = Carbon::now()->addMonth(); // ako želiš "važi 30 dana"
        } elseif ($variantId == '681065') {
            $user->num_of_questions_left += 500;
            // $user->questions_expires_at = Carbon::now()->addMonth();
        }

        $user->save();
    }
}
