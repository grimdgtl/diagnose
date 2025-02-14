<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PaymentController extends Controller
{
    /**
     * Prikazuje "Thank you" stranicu nakon uspešne kupovine.
     */
    public function success(Request $request)
    {
        return view('payment.success');
    }

    /**
     * Webhook metod za obradu događaja od Lemon Squeezy.
     *
     * Očekivani event: order_created sa statusom "paid".
     * Koristi se "customer_email" iz payload-a za identifikaciju korisnika,
     * a variant ID se uzima iz "first_order_item.variant_id".
     */
    public function webhook(Request $request)
    {
        $secret = env('LEMON_SQUEEZY_WEBHOOK_SECRET');
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        // Provera potpisa
        $computedSignature = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($computedSignature, $signature)) {
            Log::error('Invalid webhook signature.');
            return response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);
        Log::info('Lemon Squeezy webhook payload:', $data);

        $eventType = $data['meta']['event_name'] ?? null;
        $attributes = $data['data']['attributes'] ?? [];

        // Koristi "customer_email" iz payload-a
        $userEmail = $attributes['customer_email'] ?? null;
        if (!$userEmail) {
            Log::warning('No customer email in webhook payload.');
            return response('No customer email', 200);
        }

        // Pronađi korisnika
        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            Log::warning('User not found for email: ' . $userEmail);
            return response('User not found', 200);
        }

        // Proveri da li je narudžbina uspešno plaćena
        if ($eventType === 'order_created' && ($attributes['status'] ?? null) === 'paid') {
            // Izvuci variant ID iz first_order_item
            $orderItem = $attributes['first_order_item'] ?? null;
            $variantId = $orderItem['variant_id'] ?? null;

            if (!$variantId) {
                Log::warning('Variant ID not found in webhook payload.');
                return response('Variant ID not found', 200);
            }

            // Ažuriraj korisnika u zavisnosti od paketa
            if ($variantId == env('LEMON_SQUEEZY_BASIC_VARIANT_ID')) {
                $user->subscription_type = 'basic';
                $user->num_of_questions_left += 20;
            } elseif ($variantId == env('LEMON_SQUEEZY_PRO_VARIANT_ID')) {
                $user->subscription_type = 'unlimited';
                $user->num_of_questions_left = 500;
            }

            $user->save();
            Log::info('User updated:', [
                'email' => $user->email,
                'subscription_type' => $user->subscription_type,
                'num_of_questions_left' => $user->num_of_questions_left,
            ]);
        }

        return response('OK', 200);
    }
}