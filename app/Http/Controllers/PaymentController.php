<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Prikaz stranice sa ponudom paketa (20 pitanja ili unlimited).
     */
    public function showPlans()
    {
        return view('payment.plans');
    }

    /**
     * Kreiranje checkout linka i redirekcija na LemonSqueezy
     */
    public function buyPlan(Request $request)
    {
        $planType = $request->input('plan_type'); // '20' ili 'unlimited'
        $user = Auth::user();

        // Ovde treba da generišeš link ka Lemonsqueezy checkout-u, npr.
        // 1) U zavisnosti od $planType, odaberi productId ili “priceId”
        // 2) Kreiraj parametre za Lemonsqueezy
        // 3) Redirektuj na generisani URL

        // Ovo je DEMO primer, u praksi konfigurišeš "price_id", "checkout_id" ili "embed" link
        if ($planType === '20') {
            // Starter plan
            $checkoutUrl = 'https://checkout.lemonsqueezy.com/demo-starter-plan?email='
                         . urlencode($user->email);
        } else {
            // unlimited
            $checkoutUrl = 'https://checkout.lemonsqueezy.com/demo-pro-plan?email='
                         . urlencode($user->email);
        }

        // Ponekad se traži param za success_url, cancel_url...
        // npr. ?success_url=https://tvoj-app/plans/thank-you&cancel_url=https://tvoj-app/plans
        // U nekim slučajevima se to setuje u Lemonsqueezy product settings.

        // Za sada: 
        return redirect($checkoutUrl);
    }

    /**
     * "Thank you" stranica (nakon uspešne kupovine)
     */
    public function thankYou()
    {
        return view('payment.thank-you');
    }

    /**
     * Webhook ruta
     */
    public function webhook(Request $request)
    {
        // 1) Ovde dobijamo JSON payload od Lemonsqueezy
        // 2) Potvrdi signaturu (ako je definisan 'webhook secret')

        // $payload = $request->all(); // ili json_decode($request->getContent(), true);

        // primer: $email = $payload['data']['attributes']['customer_email'];
        // pronadješ user-a i postaviš mu plan:
        // $user = User::where('email', $email)->first();
        // if ($planType == '20') {
        //     $user->num_of_questions_left = 20;
        // } else {
        //     $user->num_of_questions_left = 999999; // unlimited
        // }
        // $user->save();

        return response('OK', 200);
    }
}
