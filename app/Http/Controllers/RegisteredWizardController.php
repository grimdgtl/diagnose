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
        // Validacija
        $rules = [
            'issueDescription' => 'required|string',
            'diagnose'         => 'nullable|string',
            'indicatorLight'   => 'nullable|string',
            'carOption'        => 'required|in:existing,new',
        ];

        if ($request->carOption === 'existing') {
            // Ako je već postojeći automobil
            $rules['existing_car_id'] = 'required|exists:car_details,id';
        } else {
            // Ako je novi automobil (obavezna polja)
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
            'user_id' => $user->id,
            'status'  => 'open',
        ]);

        // 2) Odredi koji CarDetail koristimo (novi ili postojeći)
        $carId = null;
        $car   = null;

        if ($request->carOption === 'new') {
            // Kreiramo novi auto
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
            // Uzmi ID postojećeg automobila
            $carId = $request->existing_car_id;
            $car   = CarDetail::find($carId);
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

        // 4) Postavimo OpenAiService
        $openAi = new OpenAiService();

        // Ako imamo $car, pripremamo carForm
        $carForm = [];
        if ($car) {
            $carForm = [
                'brand'           => $car->brand,
                'model'           => $car->model,
                'year'            => $car->year,
                'fuelType'        => $car->fuel_type,
                'engineCapacity'  => $car->engine_capacity,
                'enginePower'     => $car->engine_power,
                'transmission'    => $car->transmission,
            ];
        }

        /**
         * 5) Sada koristimo handleUserQuestion za kontekst:
         *    - $request->diagnose
         *    - $request->indicatorLight
         *    - $request->issueDescription (glavno pitanje)
         *    - $carForm (ako ga imamo)
         *    - System poruku (opcionalno) postavimo
         */
        $chatGptResponse = $openAi->handleUserQuestion(
            $request->diagnose,
            $request->indicatorLight,
            $request->issueDescription, // user question
            $carForm,
            "Ti si AutoMentor – virtualni asistent. Molim te analiziraj problem i daj savet...Odgovaraj u Markdown formatu, koristi listu za nabrajanje, linkove u [tekst](url) formatu i podeli pasuse praznim redovima"
        );

        // 6) Sačuvamo GPT odgovor u bazu
        Response::create([
            'question_id' => $question->id,
            'content'     => $chatGptResponse,
            'created_at'  => now(),
        ]);

        // 7) Smanjimo broj pitanja (ako koristite tu logiku)
        if ($user->num_of_questions_left > 0) {
            $user->num_of_questions_left -= 1;
            $user->save();
        }

        // 8) Preusmeri na dashboard
        return redirect()->route('dashboard')
                         ->with('success','Uspešno kreiran novi chat i zatražen odgovor.');
    }
}
