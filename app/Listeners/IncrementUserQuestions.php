<?php

namespace App\Listeners;

use LemonSqueezy\Laravel\Events\OrderCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncrementUserQuestions
{
    public function handle(OrderCreated $event)
    {
        $payload = $event->payload;
        $orderIdentifier = $payload['data']['attributes']['identifier'] ?? null;

        \Log::debug("Processing OrderCreated event for order: " . $orderIdentifier);

        // Provera da li je događaj već obrađen
        if ($orderIdentifier && \Cache::has('order_processed_' . $orderIdentifier)) {
            \Log::warning("Order " . $orderIdentifier . " je već obrađen. Preskačem.");
            return;
        }

        // Nastavi sa logikom
        $email = $payload['data']['attributes']['user_email']
            ?? ($payload['data']['attributes']['billing_address']['email'] ?? null);
        $variantId = $payload['data']['attributes']['variant_id']
            ?? ($payload['data']['attributes']['first_order_item']['variant_id'] ?? null);

        $user = User::where('email', $email)->first();
        if (! $user) {
            \Log::warning("User not found for email: {$email}");
            return;
        }

        if ($variantId == '714137' || $variantId == 714137) {
            $user->num_of_questions_left += 20;
            $user->questions_expires_at = Carbon::now()->addDays(30); // Pitanja važe 30 dana
        } elseif ($variantId == '714199' || $variantId == 714199) {
            $user->num_of_questions_left += 500;
            $user->questions_expires_at = Carbon::now()->addDays(30); // Pitanja važe 30 dana
        }

        $user->save();
        \Log::info("Updated user {$email} with num_of_questions_left: " . $user->num_of_questions_left . 
                   ", expires at: " . $user->questions_expires_at);

        // Označi narudžbinu kao obrađenu, na primer na sat vremena
        if ($orderIdentifier) {
            \Cache::put('order_processed_' . $orderIdentifier, true, now()->addHour());
        }
    }
}