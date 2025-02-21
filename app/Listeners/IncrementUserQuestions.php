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
        // Ostatak koda (nakon debagovanja, ukloni ili komentariši dd() liniju)
        Log::info('IncrementUserQuestions listener triggered');
        Log::debug('OrderCreated event payload: ' . print_r($event->payload, true));

        $payload = $event->payload;
        
        // Pokušaj prvo da pročitaš user_email, a ako nije definisan, fallback na billing_address.email
        $email = $payload['data']['attributes']['user_email'] 
            ?? ($payload['data']['attributes']['billing_address']['email'] ?? null);
        
        // Pokušaj prvo direktno, pa unutar first_order_item
        $variantId = $payload['data']['attributes']['variant_id'] 
            ?? ($payload['data']['attributes']['first_order_item']['variant_id'] ?? null);

        Log::debug("Parsed email: {$email}, variantId: {$variantId}");

        // Nađi korisnika po email-u
        $user = User::where('email', $email)->first();
        if (! $user) {
            Log::warning("IncrementUserQuestions: User not found for email: {$email}");
            return;
        }

        // Ažuriraj num_of_questions_left
        if ($variantId == '681064' || $variantId == 681064) {
            $user->num_of_questions_left += 20;
        } elseif ($variantId == '681065' || $variantId == 681065) {
            $user->num_of_questions_left += 500;
        }

        $user->save();
        Log::info("IncrementUserQuestions: Updated user {$email} with num_of_questions_left: " . $user->num_of_questions_left);
    }
}
