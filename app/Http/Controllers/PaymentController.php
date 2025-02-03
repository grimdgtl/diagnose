<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PaymentController extends Controller
{
    /**
     * Prikaži listu planova (20 pitanja, unlimited/500 pitanja).
     */
    public function showPlans()
    {
        return view('payment.plans');
    }

    /**
     * Prima zahtev za kupovinu. Validira koji plan je izabran
     * i čuva ga u sesiji radi preusmerenja na checkout.
     */
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

    /**
     * Formira URL do LemonSqueezy Hosted Checkouts (prosleđuje success/cancel i user_id kroz metadata).
     */
    public function redirectToLemonSqueezyCheckout(Request $request)
    {
        $planId = session('selected_plan_id');
        if (!$planId) {
            return redirect()->route('plans.show')->with('error', 'Plan not selected');
        }

        // Uzmemo ID-eve proizvoda iz .env
        $productId20        = env('PLAN_20_ID');
        $productIdUnlimited = env('PLAN_UNLIMITED_ID');

        // Adrese za success i cancel
        $successUrl = route('plans.thank-you'); 
        $cancelUrl  = route('plans.cancel');

        // Tvoj subdomen na LemonSqueezy:
        $lemonStoreDomain = 'dijagnoza-app.lemonsqueezy.com';

        switch ($planId) {
            case 'plan_20':
                // One-time kupovina (Starter)
                $checkoutUrl = "https://{$lemonStoreDomain}/buy/{$productId20}"
                    . "?success={$successUrl}"
                    . "&cancel={$cancelUrl}"
                    . "&metadata[user_id]=" . Auth::id();
                break;

            case 'plan_unlimited':
                // Pretplata ili one-time za “unlimited”/“Pro”
                $checkoutUrl = "https://{$lemonStoreDomain}/buy/{$productIdUnlimited}"
                    . "?success={$successUrl}"
                    . "&cancel={$cancelUrl}"
                    . "&metadata[user_id]=" . Auth::id();
                break;

            default:
                return redirect()->route('plans.show')->with('error', 'Invalid plan');
        }

        return redirect()->away($checkoutUrl);
    }

    /**
     * Webhook od LemonSqueezy – ovde ažuriramo bazu nakon naplate.
     */
    public function webhook(Request $request)
    {
        // 1) Verifikacija potpisa (OBAVEZNO u produkciji)
        $signature = $request->header('X-Signature') ?? null;
        if (!$this->verifyLemonSignature($signature, $request->getContent())) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // 2) Uzmemo payload
        $payload = $request->all();

        // Neke prodaje stižu kao "order_paid", neke (pretplate) kao "subscription_payment_success"
        $eventType = $payload['meta']['event_name'] ?? null;
        if (! in_array($eventType, ['order_created', 'subscription_payment_success'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // Izvučemo user_id i product_id
        $userId    = $payload['data']['attributes']['meta']['user_id'] ?? null;
        $productId = $payload['data']['attributes']['product_id'] ?? null;

        if (!$userId || !$productId) {
            return response()->json(['error' => 'Missing user_id or product_id'], 400);
        }

        // Pronađemo korisnika
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Uporedimo productId sa onim iz .env
        $envProductId20        = env('PLAN_20_ID');
        $envProductIdUnlimited = env('PLAN_UNLIMITED_ID');

        if ($productId == $envProductId20) {
            // Dodeli 20 pitanja
            $user->num_of_questions_left += 20;
            $user->subscription_type = 'starter';
            $user->save();

        } elseif ($productId == $envProductIdUnlimited) {
            // Dodeli 500 pitanja (ili stavi subscription_type='unlimited')
            $user->num_of_questions_left += 500;
            $user->subscription_type = 'unlimited';
            $user->save();
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Posle uspešne kupovine, LemonSqueezy će redirektovati ovde (success URL).
     * Ako želimo odmah na dashboard, samo radimo redirect().
     */
    public function thankYou()
    {
        return redirect()->route('dashboard')
            ->with('status', 'Uspešna kupovina! Pitanja su dodeljena.');
    }

    /**
     * Ako želiš i Cancel stranicu u kontroleru umesto closure-a u web.php.
     */
    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Verifikacija potpisa iz LemonSqueezy.
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
