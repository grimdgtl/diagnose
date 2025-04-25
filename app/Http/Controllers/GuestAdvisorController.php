<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\TempAdvisorUser;
use App\Models\TempAdvisorVehicle;
use App\Models\TempAdvisorChat;
use App\Models\TempAdvisorMessage;
use App\Services\AdvisorAiService;

class GuestAdvisorController extends Controller
{
    /**
     * Prikazuje guest verziju wizard-a za savetnika.
     */
    public function showWizard()
    {
        return view('advisor.guest-wizard');
    }

    /**
     * Čuva podatke o vozilu u temp_advisor_vehicles.
     */
    public function storeVehicle(Request $request)
    {
        $data = $request->validate([
            'brand'           => 'required|string',
            'model'           => 'required|string',
            'year'            => 'required|integer',
            'mileage'         => 'required|integer|min:0',
            'engine_capacity' => 'required|string',
            'engine_power'    => 'required|numeric',
            'fuel_type'       => 'required|string',
            'transmission'    => 'required|string',
        ]);

        // Dohvati ili kreiraj temp_id u sesiji (koristimo ključ "advisor_temp_id" da bude izolovano od dijagnoze)
        $tempId = session('advisor_temp_id');
        if (!$tempId) {
            $tempId = (string) Str::uuid();
            TempAdvisorUser::create(['temp_id' => $tempId]);
            session(['advisor_temp_id' => $tempId]);
        }

        // Proveri broj postojećih vozila (maks 3)
        $vehicleCount = TempAdvisorVehicle::where('temp_id', $tempId)->count();
        if ($vehicleCount >= 3) {
            return response()->json(['error' => 'Maksimalno 3 vozila.'], 400);
        }

        // Čuvanje vozila
        TempAdvisorVehicle::create(array_merge($data, [
            'temp_id'    => $tempId,
            'created_at' => now(),
        ]));

        $vehicleCount++;

        return response()->json(['count' => $vehicleCount]);
    }

    /**
     * Briše sva vozila za trenutnog temp korisnika.
     */
    public function clearVehicles()
    {
        $tempId = session('advisor_temp_id');
        if ($tempId) {
            TempAdvisorVehicle::where('temp_id', $tempId)->delete();
        }
        return response()->json(['count' => 0]);
    }

    /**
     * Kreira temp chat, poziva AdvisorAiService za početnu analizu i vraća URL ka guest chatu.
     */
    public function startChat(AdvisorAiService $ai)
    {
        $tempId = session('advisor_temp_id');
        if (!$tempId) {
            return response()->json(['error' => 'Nema podataka o vozilima.'], 400);
        }

        // Dohvati vozila uneta za ovog temp korisnika
        $vehicles = TempAdvisorVehicle::where('temp_id', $tempId)->get()->toArray();
        if (empty($vehicles)) {
            return response()->json(['error' => 'Nema vozila.'], 400);
        }

        // Kreiraj temp chat
        $tempChat = TempAdvisorChat::create([
            'temp_id'    => $tempId,
            'status'     => 'open',
            'created_at' => now(),
        ]);

        // Pozovi AI servis za početnu analizu – metoda getInitialAnalysisForGuest() već
        // snima korisničku poruku i odgovor asistenta u bazu
        $reply = $ai->getInitialAnalysisForGuest($tempChat, $vehicles);

        // Ukloni dupliranje: Nema potrebe ponovo snimati odgovor asistenta ovde,
        // jer je on već unesen u getInitialAnalysisForGuest()

        // Opcionalno: obriši vozila iz temp tabele nakon pokretanja chata
        TempAdvisorVehicle::where('temp_id', $tempId)->delete();

        return response()->json([
            'redirectUrl' => route('advisor.guest.chat', $tempChat->id)
        ]);
    }

    /**
     * Prikazuje guest chat (privremeni chat) sa svim porukama.
     */
    public function showChat($chatId)
    {
        $tempChat = TempAdvisorChat::findOrFail($chatId);
        $messages = TempAdvisorMessage::where('chat_id', $tempChat->id)
                        ->orderBy('id')->get();

        // Pošto gost ima samo jedan besplatan upit, prikazaćemo dugme za registraciju ispod odgovora
        $promptRegistration = true;

        return view('advisor.guest-chat', compact('tempChat', 'messages', 'promptRegistration'));
    }
}
