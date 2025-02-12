<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // da možemo logovati
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
                    // metadata treba da se prosledi ovako:
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
     * Obavezno podesite route da ga zove LemonSqueezy i unestite Webhook Secret u .env (LEMONSQUEEZY_WEBHOOK_SECRET).
     */
    public function webhook(Request $request)
    {
        // 1) Provera potpisa
        // LemonSqueezy šalje potpis u 'X-Lemon-Signature'.
        $signature = $request->header('X-Lemon-Signature') ?? null;
        if (!$this->verifyLemonSignature($signature, $request->getContent())) {
            Log::warning("LemonSqueezy webhook signature failed.");
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 2) Uzmemo payload i event_type
        $payload = $request->all();
        $eventType = $payload['meta']['event_name'] ?? null;

        // Za debug: zapišite koji event je stigao
        Log::info("LemonSqueezy Webhook: event = " . $eventType, $payload);

        // 3) Da li je order plaćen ili subscription plaćen?
        //   Kod single-purchase je "order_paid"
        //   Kod pretplate je "subscription_payment_success"
        if (! in_array($eventType, ['order_paid', 'subscription_payment_success'])) {
            // Ako nije jedan od ova dva eventa, samo ga ignorišemo
            return response()->json(['status' => 'ignored'], 200);
        }

        // 4) Izvučemo user_id iz meta, product_id iz data
        // Napomena: ako ste gore prosledili "checkout[metadata][user_id]", ovde se nalazi:
        // payload['data']['attributes']['meta']['user_id']
        $attributes = $payload['data']['attributes'] ?? [];
        $userId = $attributes['meta']['user_id'] ?? null;
        $productId = $attributes['product_id'] ?? null;

        if (!$userId || !$productId) {
            Log::warning("LemonSqueezy webhook: userId ili productId je prazan");
            return response()->json(['error' => 'Missing user_id or product_id'], 400);
        }

        // 5) Pronađemo korisnika
        $user = User::find($userId);
        if (!$user) {
            Log::warning("LemonSqueezy webhook: user not found, ID = $userId");
            return response()->json(['error' => 'User not found'], 404);
        }

        // 6) Uporedimo productId sa onim iz .env
        $envProductId20        = env('PLAN_20_ID');
        $envProductIdUnlimited = env('PLAN_UNLIMITED_ID');

        // Možda je ID u bazi string, obratite pažnju
        // Inače poredite direktno (===), ili konvertujte $productId
        if ($productId == $envProductId20) {
            // Dodeli 20 pitanja
            $user->num_of_questions_left += 20;
            $user->subscription_type = 'starter';
            $user->save();

        } elseif ($productId == $envProductIdUnlimited) {
            // Dodeli 500 pitanja i stavi subscription
            $user->num_of_questions_left += 500;
            $user->subscription_type = 'unlimited';
            $user->save();
        } else {
            // U slučaju da stigne neki drugi productId
            Log::info("LemonSqueezy webhook: product_id nije prepoznat => $productId");
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
     * Verifikacija potpisa iz LemonSqueezy: hashed payload + secret = dobijeni potpis?
     */
    private function verifyLemonSignature($signature, $payloadRaw)
    {
        $secret = env('LEMONSQUEEZY_WEBHOOK_SECRET');
        if (!$secret) {
            return false;
        }

        // Prvo SHA-256 HMAC
        $computedSignature = hash_hmac('sha256', $payloadRaw, $secret);
        // Uporedimo s onim što je došlo u "X-Lemon-Signature"
        return hash_equals($computedSignature, $signature);
    }
}
