<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseChat;
use App\Models\ComparisonSet;
use App\Models\ComparisonSetItem;
use App\Services\AdvisorAiService;

class AdvisorController extends Controller
{
    public function landing()
    {
        return view('advisor.landing');
    }

    public function showWizard()
    {
        return view('advisor.wizard');
    }

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
        abort_if(count($cars) > 3, 400, 'Maksimalno 3 vozila.');

        $cars[] = $data;
        session(['advisor.cars' => $cars]);

        return response()->json(['count' => count($cars)]);
    }

    public function startChat(AdvisorAiService $ai)
    {
        $cars = session('advisor.cars', []);
        abort_if(empty($cars), 400, 'Nema vozila.');

        // 1) Kreiraj chat
        $chat = PurchaseChat::create([
            'user_id' => Auth::id(),
            'status'  => 'active',
        ]);

        // 2) Prvi GPT odgovor (snimanje radi servis)
        $ai->getInitialAnalysis($chat, $cars);

        // 3) Ako ima više od 1 vozila, napravi comparison set
        if (count($cars) > 1) {
            $set = ComparisonSet::create(['user_id' => Auth::id()]);
            ComparisonSetItem::create([
                'comparison_set_id' => $set->id,
                'purchase_chat_id'  => $chat->id,
            ]);
        }

        // 4) Očisti sesiju i vrati redirect URL
        session()->forget('advisor.cars');

        return response()->json([
            'redirect' => route('advisor.chat', ['purchaseChat' => $chat->id]),
        ]);
    }

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

    public function clearVehicles()
    {
        session()->forget('advisor.cars');
        return response()->json(['count' => 0]);
    }
}
