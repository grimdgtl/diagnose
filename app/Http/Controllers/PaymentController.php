<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PaymentController extends Controller
{
    /**
     * Kreira checkout preko Lemon Squeezy API-ja.
     */
    public function create(Request $request)
    {
        // Proveri da li je korisnik ulogovan
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Odredi variant_id na osnovu tipa proizvoda (basic / pro)
        $planType = $request->input('product');
        if ($planType === 'basic') {
            $variantId = env('LEMON_SQUEEZY_BASIC_VARIANT_ID');
        } elseif ($planType === 'pro') {
            $variantId = env('LEMON_SQUEEZY_PRO_VARIANT_ID');
        } else {
            return response()->json(['error' => 'Unknown product type'], 400);
        }

        // Sastavi payload koristeći relationships sa potrebnim ključevima:
        // - "store": mora sadržati "type" (stores) i "id" (kao string)
        // - "variant": mora sadržati "type" (variants) i "id" (kao string)
        $checkoutData = [
            'data' => [
                'type' => 'checkouts',
                'relationships' => [
                    'store' => [
                        'data' => [
                            'type' => 'stores',
                            'id'   => (string) env('LEMON_SQUEEZY_STORE'),
                        ],
                    ],
                    'variant' => [
                        'data' => [
                            'type' => 'variants',
                            'id'   => (string) $variantId,
                        ],
                    ],
                ],
            ],
        ];

        try {
            // Pošalji zahtev ka Lemon Squeezy API-ju
            $response = Http::withToken(env('LEMON_SQUEEZY_API_KEY'))
                ->post('https://api.lemonsqueezy.com/v1/checkouts', $checkoutData);

            if ($response->successful()) {
                // Očekuje se da API vrati URL checkout stranice u data.attributes.url
                $checkoutUrl = $response->json()['data']['attributes']['url'] ?? null;

                if ($checkoutUrl) {
                    return response()->json(['checkout_url' => $checkoutUrl]);
                } else {
                    return response()->json(['error' => 'No checkout_url found'], 500);
                }
            } else {
                Log::error('Lemon Squeezy create checkout error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return response()->json(['error' => 'Failed to create checkout'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Lemon Squeezy exception: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

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
     * Napomena: Pošto se u checkout payload-u ne šalju custom podaci,
     * koristi se email kupca (customer_email) iz webhook payload-a
     * za identifikaciju korisnika.
     */
    public function webhook(Request $request)
    {
        $secret = env('LEMON_SQUEEZY_WEBHOOK_SECRET');
        $signature = $request->header('X-Signature');
        $payload = $request->getContent();

        $computedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($computedSignature, $signature)) {
            return response('Invalid signature', 400);
        }

        $data = json_decode($payload, true);
        Log::info('Lemon Squeezy webhook payload', $data);

        $eventType  = $data['meta']['event_name'] ?? null;
        $attributes = $data['data']['attributes'] ?? [];

        // Pokušaj da izvučeš email kupca
        $customerEmail = $attributes['customer_email'] ?? null;

        if (!$customerEmail) {
            Log::warning('No customer email in webhook payload.');
            return response('No customer email', 200);
        }

        $user = User::where('email', $customerEmail)->first();
        if (!$user) {
            Log::warning('User not found for email: ' . $customerEmail);
            return response('User not found', 200);
        }

        if ($eventType === 'order_created' && !empty($attributes['status']) && $attributes['status'] === 'paid') {
            $variantId = $attributes['variant_id'] ?? null;

            if ($variantId == env('LEMON_SQUEEZY_BASIC_VARIANT_ID')) {
                $user->subscription_type = 'basic';
                $user->num_of_questions_left += 20;
            } elseif ($variantId == env('LEMON_SQUEEZY_PRO_VARIANT_ID')) {
                $user->subscription_type = 'unlimited';
                $user->num_of_questions_left = 500;
            }

            $user->save();
        }

        return response('OK', 200);
    }
}
