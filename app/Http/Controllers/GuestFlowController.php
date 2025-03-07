<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 
use App\Models\TempUser;
use App\Models\TempQuestion;
use App\Models\TempCarDetail;
use App\Models\TempChat;
use App\Models\TempResponse;
use App\Mail\VerificationMail;
use App\Services\OpenAiService;

class GuestFlowController extends Controller
{
    /**
     * Prikazuje prvu formu (opis problema).
     */
    public function showWizardForm()
    {
        return view('guest.wizard-form'); 
    }

    /**
     * Nakon što korisnik popuni forme, ovde čuvamo temp_ podatke i zovemo ChatGPT.
     * Sada vraćamo JSON ako je zahtev AJAX, ili radimo redirect ako nije.
     */
    public function storeTempData(Request $request)
{
    // 1) Validacija
    $validatedData = $request->validate([
        'issueDescription' => 'required|string',
        'indicatorLight'   => 'nullable|string',
        'diagnose'         => 'nullable|string',
        'brand'            => 'required|string',
        'model'            => 'required|string',
        'year'            => 'required|integer',
        'engine_capacity'  => 'required|string',
        'engine_power'     => 'required|string',
        'fuel_type'        => 'required|string',
        'transmission'     => 'required|string',
    ]);

    // 2) Kreiranje tempUser, question, carDetail, itd...
    $tempId = (string) Str::uuid();
    $tempUser = TempUser::create(['temp_id' => $tempId]);

    $tempQuestion = TempQuestion::create([
        'temp_id'         => $tempId,
        'issueDescription'=> $validatedData['issueDescription'],
        'diagnose'        => $validatedData['diagnose']        ?? null,
        'indicatorLight'  => $validatedData['indicatorLight']  ?? null,
    ]);

    TempCarDetail::create([
        'temp_id'         => $tempId,
        'brand'           => $validatedData['brand'],
        'model'           => $validatedData['model'],
        'year'            => $validatedData['year'],
        'fuel_type'       => $validatedData['fuel_type'],
        'engine_capacity' => $validatedData['engine_capacity'],
        'engine_power'    => $validatedData['engine_power'],
        'transmission'    => $validatedData['transmission'],
    ]);

    $tempChat = TempChat::create([
        'temp_id' => $tempId,
        'status'  => 'open',
    ]);

    // 3) ChatGPT upit
    $openAiService = new OpenAiService();
    $chatGptResponse = $openAiService->handleUserQuestion(
        $validatedData['diagnose'] ?? null,
        $validatedData['indicatorLight'] ?? null,
        $validatedData['issueDescription'],
        [
            'brand' => $validatedData['brand'],
            'model' => $validatedData['model'],
            'year' => $validatedData['year'],
            'fuelType' => $validatedData['fuel_type'],
            'engineCapacity' => $validatedData['engine_capacity'],
            'enginePower' => $validatedData['engine_power'],
            'transmission' => $validatedData['transmission'],
        ]
    );

    // 4) Snimamo odgovor u bazu, ne prikazujemo ga korisniku
    TempResponse::create([
        'question_id' => $tempQuestion->id,
        'content'     => $chatGptResponse,
    ]);

    // 5) Snimamo temp_id u session, radi gost daljeg pregleda
    session(['temp_id' => $tempId]);
    Log::info("Temp ID in session: {$tempId}");

    // 6) Bez prikaza poruka – samo redirect
    if ($request->ajax() || $request->wantsJson()) {
        // 6a) Ako je AJAX => vratimo samo URL; front da uradi window.location
        return response()->json([
            'redirectUrl' => route('dashboard'),
        ]);
    } else {
        // 6b) Klasičan submit => direktan redirect
        return redirect()->route('dashboard');
    }
}
}
