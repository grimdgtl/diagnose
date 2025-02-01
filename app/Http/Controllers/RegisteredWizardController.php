<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Models\CarDetail;
use App\Models\Chat;
use App\Models\Question;
use App\Models\Response;
use App\Services\OpenAiService;

class RegisteredWizardController extends Controller
{
    public function showForm()
    {
        $user = Auth::user();
        
        // Dohvati postojeće automobile iz garaže
        $userCars = $user->carDetails()->orderBy('created_at', 'desc')->get();

        return view('chat.wizard-form-registered', compact('userCars'));
    }

    public function storeData(Request $request)
    {
        // Validacija: Korak 1 i 2
        // Pogledaj da li je user odabrao "existing" ili "new"
        // pa validiraj u skladu s tim
        $rules = [
            'issueDescription' => 'required|string',
            'diagnose'         => 'nullable|string',
            'indicatorLight'   => 'nullable|string',
            'carOption'        => 'required|in:existing,new',
        ];

        if ($request->carOption === 'existing') {
            // Zahtevamo "existing_car_id"
            $rules['existing_car_id'] = 'required|exists:car_details,id';
        } else {
            // Zahtevamo sva polja za novi auto
            $rules['brand']           = 'required|string';
            $rules['model']           = 'required|string';
            $rules['year']            = 'required|integer';
            $rules['engine_capacity'] = 'required|string';
            $rules['engine_power']    = 'required|string';
            $rules['fuel_type']       = 'required|string';
            $rules['transmission']    = 'required|string';
        }

        $data = $request->validate($rules);

        $user = Auth::user();

        // 1) Napravi novi Chat (status = 'open')
        $chat = Chat::create([
            'user_id'   => $user->id,
            'status'    => 'open',
        ]);

        // 2) Ako je carOption = new, snimi nov car
        $carId = null;
        if ($request->carOption === 'new') {
            $car = CarDetail::create([
                'user_id'         => $user->id,
                'brand'           => $request->brand,
                'model'           => $request->model,
                'year'            => $request->year,
                'fuel_type'       => $request->fuel_type,
                'engine_capacity' => $request->engine_capacity,
                'engine_power'    => $request->engine_power,
                'transmission'    => $request->transmission,
                'created_at'      => now(),
            ]);
            $carId = $car->id;
        } else {
            // Uzeo je postojeći auto
            $carId = $request->existing_car_id;
        }

        // 3) Kreiraj Question (opis problema)
        $question = Question::create([
            'user_id'          => $user->id,
            'issueDescription' => $request->issueDescription,
            'diagnose'         => $request->diagnose,
            'indicatorLight'   => $request->indicatorLight,
            'chat_id'          => $chat->id,
            'created_at'       => now(),
        ]);

        // 4) Pozovi ChatGPT
        $fullPrompt = "Opis problema: ".$request->issueDescription."\n".
                      "Dijagnostika: ".$request->diagnose."\n".
                      "Lampica: ".$request->indicatorLight."\n".
                      "CarDetail ID: " . $carId . " (za interni ID)";

        $chatGptResponse = (new OpenAiService())->ask($fullPrompt);

        // 5) Sačuvaj odgovor
        Response::create([
            'question_id' => $question->id,
            'content'     => $chatGptResponse,
            'created_at'  => now(),
        ]);

        // 6) (Opcionalno) Smanji broj preostalih pitanja user-u, ako to želiš
        if ($user->num_of_questions_left > 0) {
            $user->num_of_questions_left -= 1;
            $user->save();
        }

        // Preusmeri na dashboard
        return redirect()->route('dashboard')
                         ->with('success','Uspešno kreiran novi chat i zatražen odgovor.');
    }
}
