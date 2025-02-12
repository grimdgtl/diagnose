<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PaymentController extends Controller
{
    public function showPlans()
    {
        return view('payment.plans');
    }

    public function buyPlan(Request $request)
    {
        $request->validate([
            'plan_type' => 'required|in:20,unlimited',
        ]);

        if ($request->plan_type === '20') {
            session(['selected_plan_id' => 'plan_20']);
        } else {
            session(['selected_plan_id' => 'plan_unlimited']);
        }

        return redirect()->route('payment.redirectToCheckout');
    }

    public function redirectToLemonSqueezyCheckout(Request $request)
    {
        $planId = session('selected_plan_id');
        if (!$planId) {
            return redirect()->route('plans.show')->with('error', 'Plan not selected');
        }

        // Uzmemo ID-eve proizvoda iz .env
        $productId20        = env('PLAN_20_ID');
        $productIdUnlimited = env('PLAN_UNLIMITED_ID');

        $successUrl = route('plans.thank-you'); 
        $cancelUrl  = route('plans.cancel');
        $lemonStoreDomain = 'dijagnoza-app.lemonsqueezy.com';

        switch ($planId) {
            case 'plan_20':
                $checkoutUrl = "https://{$lemonStoreDomain}/buy/{$productId20}"
                    . "?success={$successUrl}"
                    . "&cancel={$cancelUrl}"
                    // Uključujemo i metapodatke
                    . "&checkout[metadata][user_id]=" . Auth::id();
                break;

            case 'plan_unlimited':
                $checkoutUrl = "https://{$lemonStoreDomain}/buy/{$productIdUnlimited}"
                    . "?success={$successUrl}"
                    . "&cancel={$cancelUrl}"
                    . "&checkout[metadata][user_id]=" . Auth::id();
                break;

            default:
                return redirect()->route('plans.show')->with('error', 'Invalid plan');
        }

        return redirect()->away($checkoutUrl);
    }

    /**
     * Webhook endpoint.
     * Podesite ga u LemonSqueezy i unesite LEMONSQUEEZY_WEBHOOK_SECRET u .env
     */
    public function webhook(Request $request)
    {
        // 1) Provera potpisa
        $signature = $request->header('X-Lemon-Signature') ?? null;
        if (!$this->verifyLemonSignature($signature, $request->getContent())) {
            Log::warning("LemonSqueezy webhook signature failed.");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 2) Uzmemo payload i event_type
        $payload = $request->all();
        $eventType = $payload['meta']['event_name'] ?? null;

        // Log radi pregleda
        Log::info("LemonSqueezy Webhook: event = " . $eventType, $payload);

        // 3) Dozvoljavamo 'order_created', 'order_paid', 'subscription_payment_success'
        if (! in_array($eventType, ['order_created', 'order_paid', 'subscription_payment_success'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // Proverimo da li je status = "paid"
        $attributes = $payload['data']['attributes'] ?? [];
        $status = $attributes['status'] ?? null;
        if ($status !== 'paid') {
            Log::info("LemonSqueezy webhook: order not paid, status = $status");
            return response()->json(['status' => 'ignored'], 200);
        }

        // 4) Izvučemo user_id iz meta i product_id iz first_order_item (po defaultu)
        $userId = $attributes['meta']['user_id'] ?? null;
        $productId = $attributes['first_order_item']['product_id'] ?? null; // iz "first_order_item"

        if (!$userId || !$productId) {
            Log::warning("LemonSqueezy webhook: userId ili productId je prazan");
            return response()->json(['error' => 'Missing user_id or product_id'], 400);
        }

        // 5) Nađemo korisnika u bazi
        $user = User::find($userId);
        if (!$user) {
            Log::warning("LemonSqueezy webhook: user not found, ID = $userId");
            return response()->json(['error' => 'User not found'], 404);
        }

        // 6) Uporedimo sa vrednostima iz .env
        $envProductId20        = env('PLAN_20_ID');
        $envProductIdUnlimited = env('PLAN_UNLIMITED_ID');

        // Poređenje (konvertujemo productId u string ako su različiti tipovi)
        if ((string)$productId === (string)$envProductId20) {
            // Dodeli 20 pitanja
            $user->num_of_questions_left += 20;
            $user->subscription_type = 'starter';
            $user->save();
        } elseif ((string)$productId === (string)$envProductIdUnlimited) {
            // Dodeli 500 pitanja
            $user->num_of_questions_left += 500;
            $user->subscription_type = 'unlimited';
            $user->save();
        } else {
            Log::info("LemonSqueezy webhook: product_id nije prepoznat => {$productId}");
        }

        return response()->json(['status' => 'success']);
    }

    public function thankYou()
    {
        return redirect()->route('dashboard')
            ->with('status', 'Uspešna kupovina! Pitanja su dodeljena.');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Verifikacija LemonSqueezy potpisa: HMAC SHA-256 sa vašim LEMONSQUEEZY_WEBHOOK_SECRET
     */
    private function verifyLemonSignature($signature, $payloadRaw)
    {
        $secret = env('LEMONSQUEEZY_WEBHOOK_SECRET');
        if (!$secret) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payloadRaw, $secret);
        return hash_equals($computedSignature, $signature);
    }
}
