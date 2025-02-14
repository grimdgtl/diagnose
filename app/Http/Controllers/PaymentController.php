<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\User;

class PaymentController extends Controller
{
    public function create(Request $request)
    {
        // Proveri da li je korisnik ulogovan
        $user = $request->user();
        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // U zavisnosti od tipa proizvoda (basic / pro), uzmi odgovarajući variant_id
        $planType = $request->input('product');
        if ($planType === 'basic') {
            $variantId = env('LEMON_SQUEEZY_BASIC_VARIANT_ID');
        } elseif ($planType === 'pro') {
            $variantId = env('LEMON_SQUEEZY_PRO_VARIANT_ID');
        } else {
            return response()->json(['error' => 'Unknown product type'], 400);
        }

        // Opcioni client_reference_id (može da se zove i custom.user_id i sl.)
        // Ovo je važno da bismo kasnije preko webhooks-a znali za kog user-a je porudžbina
        $clientReferenceId = $user->id;

        // Pripremamo podatke za kreiranje Checkout-a
        $checkoutData = [
            'data' => [
                'type' => 'checkouts',
                'attributes' => [
                    'variant_id' => $variantId,

                    // custom polja - lemon squeezy dopušta da proslediš bilo šta
                    // npr. user_id, ili neke reference
                    'custom' => [
                        'client_reference_id' => $clientReferenceId,
                    ],

                    // Gde će Lemon Squeezy da vrati korisnika kad uspe plaćanje
                    // i gde da ga vrati kad otkaže
                    'success_url' => route('payment.success'),
                    'cancel_url'  => route('profile.subscription'),
                ]
            ]
        ];

        try {
            // Napravi HTTP POST ka Lemonsqueezy API
            $response = Http::withToken(env('LEMON_SQUEEZY_API_KEY'))
                ->post('https://api.lemonsqueezy.com/v1/checkouts', $checkoutData);

            if ($response->successful()) {
                // U JSON odgovoru tražimo URL, najčešće je `data.attributes.url` ili `checkout_url`
                $checkoutUrl = $response->json()['data']['attributes']['url'] 
                    ?? null;

                if ($checkoutUrl) {
                    return response()->json(['checkout_url' => $checkoutUrl]);
                } else {
                    return response()->json(['error' => 'No checkout_url found'], 500);
                }
            } else {
                Log::error('Lemon Squeezy create checkout error', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);
                return response()->json(['error' => 'Failed to create checkout'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Lemon Squeezy exception: '.$e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    /**
     * Ovaj metod će prikazati "Thank you" stranicu nakon uspešne kupovine,
     * ali NAPOMENA: stvarno ažuriranje broja pitanja se obično radi putem webhooks-a.
     * (Osim ako baš želiš da se osloniš samo na success callback.)
     */
    public function success(Request $request)
    {
        // Ovde možeš da prikažeš "Hvala na kupovini" ili da redirektuješ na dashboard
        return view('payment.success');
        // return redirect()->route('dashboard')->with('status', 'Uspešno ste kupili paket!');
    }

    /**
     * Webhook metod - obično /webhook/lemon-squeezy
     * Ovde Lemon Squeezy šalje obaveštenja kad je porudžbina plaćena, otkazana, refund itd.
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

    // Ako je potpis OK, parsiramo payload
    $data = json_decode($payload, true);

    \Log::info('Lemon Squeezy webhook payload', $data);

    $eventType  = $data['meta']['event_name'] ?? null;
    $attributes = $data['data']['attributes'] ?? [];
    $clientRef  = $attributes['custom']['client_reference_id'] ?? null;

    if (!$clientRef) {
        return response('No client reference', 200);
    }

    $user = User::find($clientRef);
    if (!$user) {
        return response('User not found', 200);
    }

    // Umesto "order_paid", koristi se "order_created" + provera statusa
    if ($eventType === 'order_created') {
        if (!empty($attributes['status']) && $attributes['status'] === 'paid') {
            // Pogledaj variant_id (koji su kupili)
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
    }

    return response('OK', 200);
    }

}

