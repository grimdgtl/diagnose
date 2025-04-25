<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseChat;
use App\Models\PurchaseMessage;
use App\Models\ComparisonSet;
use App\Models\ComparisonSetItem;
use App\Services\AdvisorAiService;

class AdvisorController extends Controller
{
    /* --------------------------------------------------------------
     |  GET  /advisor  – javni landing
     |-------------------------------------------------------------- */
    public function landing()
    {
        return view('advisor.landing');
    }

    /* --------------------------------------------------------------
     |  GET  /advisor/wizard  – forma za unos vozila
     |-------------------------------------------------------------- */
    public function showWizard()
    {
        return view('advisor.wizard');
    }

    /* --------------------------------------------------------------
     |  POST /advisor/wizard  – dodavanje vozila u session
     |-------------------------------------------------------------- */
    public function storeVehicle(Request $request)
    {
        $data = $request->validate([
            'brand'           => 'required',
            'model'           => 'required',
            'year'            => 'required|integer',
            'mileage'         => 'required|integer|min:0',
            'engine_capacity' => 'required',
            'engine_power'    => 'required|numeric',
            'fuel_type'       => 'required',
            'transmission'    => 'required',
        ]);

        $cars = session('advisor.cars', []);
        abort_if(count($cars) >= 3, 400, 'Maksimalno 3 vozila.');

        $cars[] = $data;
        session(['advisor.cars' => $cars]);

        return response()->json(['count' => count($cars)]);
    }

    /* --------------------------------------------------------------
     |  POST /advisor/wizard/start  – kreiranje chata
     |-------------------------------------------------------------- */
    public function startChat(AdvisorAiService $ai)
    {
        $cars = session('advisor.cars', []);
        abort_if(empty($cars), 400, 'Nema vozila.');

        /* ---------- kreiraj chat ---------- */
        $chat = PurchaseChat::create([
            'user_id' => Auth::id(),
            'status'  => 'active',
        ]);

        /* ---------- prvi GPT odgovor ---------- */
        $reply = $ai->getInitialAnalysis($chat, $cars);

        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'assistant',
            'content'          => $reply,
        ]);

        /* ---------- ako je upoređivanje (više od 1 auta) ---------- */
        if (count($cars) > 1) {
            $set = ComparisonSet::create(['user_id' => Auth::id()]);

            ComparisonSetItem::create([
                'comparison_set_id' => $set->id,
                'purchase_chat_id'  => $chat->id,
            ]);
        }

        session()->forget('advisor.cars');

        return response()->json([
            'redirect' => route('advisor.chat', $chat),
        ]);
    }

    /* --------------------------------------------------------------
     |  GET /advisor/chat-or-wizard  – logika za sidebar
     |-------------------------------------------------------------- */
    public function chatOrWizard()
    {
        $openChat = PurchaseChat::where('user_id', Auth::id())
                                ->where('status', 'active')
                                ->latest()
                                ->first();

        return $openChat
            ? redirect()->route('advisor.chat', $openChat)
            : redirect()->route('advisor.wizard');
    }

    /* --------------------------------------------------------------
     |  POST /advisor/wizard/clear  – briše sva vozila iz sesije
     |-------------------------------------------------------------- */
    public function clearVehicles()
    {
        session()->forget('advisor.cars');
    
        return response()->json(['count' => 0]);
    }
}
