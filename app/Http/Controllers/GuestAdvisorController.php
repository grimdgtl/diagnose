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
        if ($id = session('advisor_temp_id')) {
            // koristiš tabelu temp_advisor_chats_guest preko modela
            if ($chat = TempAdvisorChat::where('temp_id', $id)->first()) {
                return redirect()->route('advisor.guest.chat', $chat->id);
            }
        }
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

        // kreiranje privremenog usera i session('advisor_temp_id') isti kao pre…
        $tempId = session('advisor_temp_id');
        if (! $tempId || ! TempAdvisorUser::where('temp_id', $tempId)->exists()) {
            $tempId = (string) Str::uuid();
            TempAdvisorUser::create(['temp_id' => $tempId, 'created_at' => now()]);
            session(['advisor_temp_id' => $tempId]);
        }

        // **OVDE** umjesto where('temp_id',$tempId) na porukama:
        $chat = TempAdvisorChat::where('temp_id', $tempId)->first();
        if ($chat && $chat->messages()->where('role', 'user')->count() >= 2) {
            return response()->json(['redirectUrl' => route('register')], 403);
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

    public function startChat(AdvisorAiService $ai)
    {
        $tempId = session('advisor_temp_id');
        if (! $tempId) {
            return response()->json(['error' => 'Nema podataka o vozilima.'], 400);
        }

        // i ovdje provjera istih poruka po chat_id
        $chat = TempAdvisorChat::where('temp_id', $tempId)->first();
        if ($chat && $chat->messages()->where('role', 'user')->count() >= 2) {
            return response()->json(['redirectUrl' => route('register')], 403);
        }

        $raw = TempAdvisorVehicle::where('temp_id', $tempId)->get();
        if ($raw->isEmpty()) {
            return response()->json(['error' => 'Nema vozila.'], 400);
        }

        // kreiraš chat
        $chat = TempAdvisorChat::create([
            'temp_id'    => $tempId,
            'status'     => 'open',
            'created_at' => now(),
        ]);

        $vehicles = $raw->unique(function ($v) {
            return implode('|', [
                $v->brand, $v->model, $v->year, $v->mileage,
                $v->engine_capacity, $v->engine_power, $v->fuel_type, $v->transmission,
            ]);
        })->values()->toArray();

        $ai->getInitialAnalysisForGuest($chat, $vehicles);

        TempAdvisorVehicle::where('temp_id', $tempId)->delete();

        return response()->json(['redirectUrl' => route('advisor.guest.chat', $chat->id)]);
    }

    public function showChat($chatId)
    {
        $chat      = TempAdvisorChat::findOrFail($chatId);
        $messages  = TempAdvisorMessage::where('chat_id', $chatId)->orderBy('id')->get();
        $needReg   = $messages->where('role', 'user')->count() >= 2;

        return view('advisor.guest-chat', [
            'tempChat'           => $chat,
            'messages'           => $messages,
            'promptRegistration' => $needReg,
        ]);
    }

    public function storeGuestFollowup(Request $request, $chatId, AdvisorAiService $ai)
    {
        $data = $request->validate(['message' => 'required|string']);
        $chat = TempAdvisorChat::findOrFail($chatId);

        if (TempAdvisorMessage::where('chat_id', $chatId)->where('role', 'user')->count() >= 2) {
            return response()->json(['redirectUrl' => route('register')], 403);
        }

        TempAdvisorMessage::create([
            'chat_id' => $chat->id,
            'role'    => 'user',
            'content' => $data['message'],
            'created_at' => now(),
        ]);

        $reply = $ai->followUpGuest($chat, $data['message']);

        $count = TempAdvisorMessage::where('chat_id', $chat->id)->where('role', 'user')->count();

        $qHtml = '<div class="flex justify-end mb-2"><div class="bubble user animate-fadeIn">'
               . e($data['message']) . '</div></div>';

        $rHtml = '<div class="flex justify-start mb-2"><div class="bubble assistant animate-fadeIn markdown-content" data-content="'
               . e($reply) . '"></div></div>';

        return response()->json([
            'questionHtml' => $qHtml,
            'responseHtml' => $rHtml,
            'userCount'    => $count,
        ]);
    }
}
