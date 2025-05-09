<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TempAdvisorUser;
use App\Models\TempAdvisorVehicle;
use App\Models\TempAdvisorChat;
use App\Models\TempAdvisorMessage;
use App\Services\AdvisorAiService;

class GuestAdvisorController extends Controller
{
    public function showWizard()
    {
        return view('advisor.guest-wizard');
    }

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

        $tempId = session('advisor_temp_id');
        if (! $tempId || ! TempAdvisorUser::where('temp_id', $tempId)->exists()) {
            $tempId = (string) Str::uuid();
            TempAdvisorUser::create([
                'temp_id'    => $tempId,
                'created_at' => now(),
            ]);
            session(['advisor_temp_id' => $tempId]);
        }

        $count = TempAdvisorVehicle::where('temp_id', $tempId)->count();
        if ($count > 3) {
            return response()->json(['error' => 'Možete uneti maksimalno 3 vozila.'], 400);
        }

        TempAdvisorVehicle::create(array_merge($data, [
            'temp_id'    => $tempId,
            'created_at' => now(),
        ]));

        return response()->json(['count' => $count + 1]);
    }

    public function clearVehicles()
    {
        if ($tempId = session('advisor_temp_id')) {
            TempAdvisorVehicle::where('temp_id', $tempId)->delete();
        }
        return response()->json(['count' => 0]);
    }

    public function startChat(AdvisorAiService $ai)
    {
        $tempId = session('advisor_temp_id');
        if (! $tempId) {
            return response()->json(['error' => 'Nema podataka o vozilima.'], 400);
        }

        $vehiclesRaw = TempAdvisorVehicle::where('temp_id', $tempId)->get()->toArray();
        if (empty($vehiclesRaw)) {
            return response()->json(['error' => 'Nema vozila.'], 400);
        }

        $vehicles = collect($vehiclesRaw)
            ->unique(fn($v) => implode('|', [
                $v['brand'], $v['model'], $v['year'], $v['mileage'],
                $v['engine_capacity'], $v['engine_power'],
                $v['fuel_type'], $v['transmission'],
            ]))
            ->values()
            ->toArray();

        $tempChat = TempAdvisorChat::create([
            'temp_id'    => $tempId,
            'status'     => 'open',
            'created_at' => now(),
        ]);

        $ai->getInitialAnalysisForGuest($tempChat, $vehicles);

        TempAdvisorVehicle::where('temp_id', $tempId)->delete();

        return response()->json([
            'redirectUrl' => route('advisor.guest.chat', $tempChat->id),
        ]);
    }

    public function showChat($chatId)
    {
        $tempChat = TempAdvisorChat::findOrFail($chatId);
        $messages = TempAdvisorMessage::where('chat_id', $chatId)
                                      ->orderBy('id')
                                      ->get();

        $userCount        = $messages->where('role', 'user')->count();
        $promptRegistration = $userCount >= 2;

        return view('advisor.guest-chat', compact(
            'tempChat', 'messages', 'promptRegistration'
        ));
    }

    public function storeGuestFollowup(
        Request $request,
        $chatId,
        AdvisorAiService $ai
    ) {
        $data = $request->validate(['message' => 'required|string']);
        $chat = TempAdvisorChat::findOrFail($chatId);

        // 1) Snimi samo user poruku
        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'user',
            'content'    => $data['message'],
            'created_at' => now(),
        ]);

        // 2) Pozovi servis koji **sam** snimi assistant odgovor
        $reply = $ai->followUpGuest($chat, $data['message']);

        // 3) Brojač user poruka
        $userCount = TempAdvisorMessage::where('chat_id', $chat->id)
                                       ->where('role', 'user')
                                       ->count();

        // 4) Priprema HTML
        $q = e($data['message']);
        $r = e($reply);

        $questionHtml = <<<HTML
<div class="flex justify-end mb-2">
  <div class="bubble user animate-fadeIn">{$q}</div>
</div>
HTML;

        $responseHtml = <<<HTML
<div class="flex justify-start mb-2">
  <div class="bubble assistant animate-fadeIn markdown-content" data-content="{$r}"></div>
</div>
HTML;

        return response()->json([
            'questionHtml' => $questionHtml,
            'responseHtml' => $responseHtml,
            'userCount'    => $userCount,
        ]);
    }
}
