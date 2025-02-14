<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class PaymentController extends Controller
{
    public function createCheckout(Request $request)
{
    $storeId = env('LEMON_SQUEEZY_STORE_ID');
    $apiKey = env('LEMON_SQUEEZY_API_KEY');

    if (!$request->has('product')) {
        return response()->json(['error' => 'No product selected'], 400);
    }

    $productType = $request->input('product');
    $productId = $productType === 'pro' ? env('LEMON_SQUEEZY_PRO_ID') : env('LEMON_SQUEEZY_BASIC_ID');

    \Log::info('Sending request to Lemon Squeezy', [
        'api_key' => substr($apiKey, 0, 4) . '****',
        'store_id' => $storeId,
        'product' => $productType,
        'product_id' => $productId
    ]);

    // ✅ NOVA STRUKTURA PODATAKA ZA API
    $response = Http::withToken($apiKey)->post('https://api.lemonsqueezy.com/v1/checkouts', [
        'data' => [
            'type' => 'checkouts',
            'relationships' => [
                'store' => [
                    'data' => [
                        'type' => 'stores',
                        'id' => (string) $storeId // 🔥 Lemon Squeezy očekuje STRING, ne int
                    ]
                ],
                'variant' => [
                    'data' => [
                        'type' => 'variants',
                        'id' => (string) $productId // 🔥 Takođe mora biti STRING
                    ]
                ]
            ]
        ]
    ]);

    \Log::info('Lemon Squeezy API response', [
        'status' => $response->status(),
        'response' => $response->json()
    ]);

    if ($response->failed()) {
        \Log::error('Failed to create checkout', [
            'status' => $response->status(),
            'response' => $response->json()
        ]);
        return response()->json(['error' => 'Failed to create checkout'], 500);
    }

    return response()->json([
        'checkout_url' => $response->json('data.attributes.url')
    ]);
}



    public function paymentSuccess(Request $request)
    {
        $user = Auth::user();
        $product = $request->input('product'); 

        if (!$user) {
            return redirect()->route('login')->with('error', 'You need to be logged in.');
        }

        if ($product === 'pro') {
            $user->num_of_questions_left = 500; 
        } else {
            $user->num_of_questions_left += 20; 
        }

        $user->subscription_expires_at = Carbon::now()->addMonth(); 
        $user->save();

        return redirect()->route('home')->with('success', 'Payment successful! Questions added.');
    }

    public function handleWebhook(Request $request)
{
    \Log::info('Webhook received', ['data' => $request->all()]);

    $event = $request->input('meta.event_name'); // "order_created"
    $data = $request->input('data.attributes');

    if ($event === 'order_created' && isset($data['user_email'])) {
        $user = User::where('email', $data['user_email'])->first();

        if ($user) {
            if ($data['variant_id'] == env('LEMON_SQUEEZY_PRO_ID')) {
                $user->num_of_questions_left = 500; // PRO paket daje 500 pitanja
            } else {
                $user->num_of_questions_left += 20; // Basic paket dodaje 20 pitanja
            }

            $user->subscription_expires_at = Carbon::now()->addMonth(); // Pitanja važe mesec dana
            $user->save();

            \Log::info('User subscription updated', ['user_id' => $user->id]);
        }
    }

    return response()->json(['status' => 'success']); // Laravel vraća 200 OK
}

}
