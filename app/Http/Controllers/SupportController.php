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
                'question' => 'Kako da zakažem servis?',
                'answer' => 'Posjetite sekciju "Moja Garaža" i izaberite željeni termin...',
                'link' => route('profile.garage')
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Kako da zakažem servis?',
                'answer' => 'Posjetite sekciju "Moja Garaža" i izaberite željeni termin...',
                'link' => route('profile.garage')
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Kako da zakažem servis?',
                'answer' => 'Posjetite sekciju "Moja Garaža" i izaberite željeni termin...',
                'link' => route('profile.garage')
            ],
            // Dodajte ostala pitanja
            [
                'question' => 'Kako da zakažem servis?',
                'answer' => 'Posjetite sekciju "Moja Garaža" i izaberite željeni termin...',
                'link' => route('profile.garage')
            ],
            // Dodajte ostala pitanja
        ];  

        return view('support.form', compact('faqs'));
    }
}
