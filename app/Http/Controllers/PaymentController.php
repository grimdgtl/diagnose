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
     */
    public function webhook(Request $request)
    {
        Log::info('🔔 Webhook received:', $request->all());

        // Koristi Laravel `config()` umesto `env()`
        $secret = config('services.lemonsqueezy.webhook_secret'); 
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        if (!$secret || !$signature) {
            Log::error('❌ Webhook error: Missing signature or secret.');
            return response('Invalid request', 400);
        }

        // Provera potpisa
        $computedSignature = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($computedSignature, $signature)) {
            Log::error('❌ Invalid webhook signature.');
            return response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);
        if (!$data) {
            Log::error('❌ Invalid JSON payload received.');
            return response('Invalid JSON', 400);
        }

        Log::info('✅ Webhook payload:', $data);

        $eventType = $data['meta']['event_name'] ?? null;
        $attributes = $data['data']['attributes'] ?? [];

        // Koristi "customer_email" iz payload-a za identifikaciju korisnika
        $userEmail = $attributes['customer_email'] ?? null;
        if (!$userEmail) {
            Log::warning('⚠️ Webhook: No customer email in payload.');
            return response('No customer email', 200);
        }

        // Pronađi korisnika u bazi
        $user = User::where('email', $userEmail)->first();
        if (!$user) {
            Log::warning('⚠️ User not found for email: ' . $userEmail);
            return response('User not found', 200);
        }

        // Proveri da li je narudžbina uspešno plaćena
        if ($eventType === 'order_created' && ($attributes['status'] ?? null) === 'paid') {
            $orderItem = $attributes['first_order_item'] ?? null;
            $variantId = $orderItem['variant_id'] ?? null;

            if (!$variantId) {
                Log::warning('⚠️ Webhook: Variant ID not found in payload.');
                return response('Variant ID not found', 200);
            }

            // Ažuriraj korisnika u zavisnosti od paketa
            if ($variantId == config('services.lemonsqueezy.basic_variant')) {
                $user->subscription_type = 'basic';
                $user->num_of_questions_left += 20;
            } elseif ($variantId == config('services.lemonsqueezy.pro_variant')) {
                $user->subscription_type = 'unlimited';
                $user->num_of_questions_left = 500;
            }

            $user->save();
            Log::info('✅ User updated:', [
                'email' => $user->email,
                'subscription_type' => $user->subscription_type,
                'num_of_questions_left' => $user->num_of_questions_left,
            ]);
        }

        return response('OK', 200);
    }
}
