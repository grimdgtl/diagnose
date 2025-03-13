<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportMail;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
    /**
     * Prikaz forme za podršku.
     */

    /**
     * Obrada podataka iz forme za podršku.
     */
    public function submitSupportForm(Request $request)
    {
        // Validacija
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Priprema email-a
        $supportData = [
            'subject' => $request->subject,
            'message' => $request->message,
            'user'    => auth()->user(),
        ];

        try {
            Mail::to('support@tvojapp.com')->send(new SupportMail($supportData));
            Log::info("Podrška email uspešno poslat od korisnika ID: " . auth()->id());
            return redirect()->back()->with('success', 'Vaša poruka je uspešno poslata.');
        } catch (\Exception $e) {
            Log::error("Greška prilikom slanja podrške email-a: " . $e->getMessage());
            return redirect()->back()->with('error', 'Došlo je do greške prilikom slanja poruke. Pokušajte kasnije.');
        }
    }

    public function showSupportForm()
    {
        $faqs = [
            [
                'question' => 'Kako funkcioniše Dijagnoza aplikacija?',
                'answer' => 'Dijagnoza koristi veštačku inteligenciju za analizu vašeg opisa problema, upozoravajućih lampica i koda greške (ako ga imate) kako bi pružila preciznu dijagnozu i smernice za rešavanje problema.',
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Da li je dijagnoza koju dobijem 100% tačna?',
                'answer' => 'Dijagnoza pruža visok nivo preciznosti, ali u nekim slučajevima može biti potrebna dodatna potvrda od strane stručnog mehaničara, posebno za složenije kvarove.',
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Šta ako ne mogu sam da popravim problem?',
                'answer' => 'Ako popravka zahteva profesionalnu pomoć, aplikacija će vam preporučiti najbliže servise gde možete dobiti stručnu uslugu.',
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Kako se naplaćuju dodatna pitanja u aplikaciji?',
                'answer' => 'Aplikacija nudi osnovna pitanja besplatno, a za dodatna pitanja možete kupiti jedan od dva paketa: 20 pitanja za 300 RSD ili 500 pitanja za 999 RSD i paketi važe mesec dana od trenutka kupovine. PLaćanje se vrši isključivo karticom.',
                'link' => route('profile.subscription')
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Koje vrste automobila aplikacija podržava?',
                'answer' => 'Dijagnoza podržava širok spektar marki i modela automobila, uključujući različite tipove motora, vrste goriva i menjače, kako bi osigurala preciznu dijagnostiku za što više korisnika.',
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Želim saradnju',
                'answer' => 'Sve upite vezane za saradnju možete poslati na info@dijagnoza.com i naš tim će Vas kontaktirati u najkraćem mogućem roku.',
            ],
        ];  

        return view('support.form', compact('faqs'));
    }
}
