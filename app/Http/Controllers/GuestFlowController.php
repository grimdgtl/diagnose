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

    public function showProblemForm()
    {
        return view('guest.problem-form');
    }

    /**
     * Prikazuje drugu formu (podaci o automobilu).
     */
    public function showCarForm(Request $request)
    {
        return view('guest.car-form');
    }

    /**
     * Nakon što korisnik popuni forme, ovde čuvamo temp_ podatke i zovemo ChatGPT.
     */
    public function storeTempData(Request $request)
    {
        // 1) Validacija
        $request->validate([
            'issueDescription' => 'required|string',
            'indicatorLight'   => 'nullable|string',
            'diagnose'         => 'nullable|string',
            'brand'            => 'required|string',
            'model'            => 'required|string',
            'year'             => 'required|integer',
            'engine_capacity'  => 'required|string',
            'engine_power'     => 'required|string',
            'fuel_type'        => 'required|string',
            'transmission'     => 'required|string',
        ]);

        // 2) Kreiramo tempUser
        $tempId = (string) Str::uuid();
        $tempUser = TempUser::create([
            'temp_id' => $tempId
        ]);

        // 3) Kreiramo tempQuestion
        $tempQuestion = TempQuestion::create([
            'temp_id'         => $tempId,
            'issueDescription'=> $request->issueDescription,
            'diagnose'        => $request->diagnose,
            'indicatorLight'  => $request->indicatorLight,
        ]);

        // 4) Kreiramo tempCarDetail
        TempCarDetail::create([
            'temp_id'          => $tempId,
            'brand'            => $request->brand,
            'model'            => $request->model,
            'year'             => $request->year,
            'fuel_type'        => $request->fuel_type,
            'engine_capacity'  => $request->engine_capacity,
            'engine_power'     => $request->engine_power,
            'transmission'     => $request->transmission,
        ]);

        // 5) Kreiramo tempChat
        $tempChat = TempChat::create([
            'temp_id' => $tempId,
            'status'  => 'open',
        ]);

        // 6) Formiramo prompt
        $fullPrompt = "Opis problema: ".$request->issueDescription."\n".
                      "Dijagnostika: ".$request->diagnose."\n".
                      "Lampica: ".$request->indicatorLight."\n".
                      "Auto (brand/model/year...): ".$request->brand." ".$request->model." ".$request->year." ...";

        // 7) Pozivamo ChatGPT preko OpenAiService
        $chatGptResponse = (new OpenAiService)->ask($fullPrompt);

        // 8) Snimamo odgovor u temp_responses
        $tempResponse = TempResponse::create([
            'question_id' => $tempQuestion->id,
            'content'     => $chatGptResponse,
        ]);

        // 9) Čuvamo temp_id u session
        session(['temp_id' => $tempId]);
        Log::info("Temp ID saved in session: {$tempId}");
        Log::info("Session ID: " . session()->getId());

        // 10) Preusmeravamo na /dashboard
        return redirect()->route('dashboard')->with('info','Podaci su poslati, ChatGPT je odgovorio.');
    }
}
