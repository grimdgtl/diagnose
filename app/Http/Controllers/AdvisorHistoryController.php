<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\PurchaseChat;

class AdvisorHistoryController extends Controller
{
    use AuthorizesRequests;

    /* --------------------------------------------------------------
     |  GET  /advisor/history  – lista arhiviranih chat‑ova
     |-------------------------------------------------------------- */
    public function index()
    {
        $archived = PurchaseChat::withCount('messages')          // za brži prikaz broja poruka
            ->where('user_id', Auth::id())
            ->where('status', 'archived')
            ->latest('updated_at')                               // najskoriji gore
            ->get();

        return view('advisor.history.index', compact('archived'));
    }

    /* --------------------------------------------------------------
     |  GET  /advisor/history/{purchaseChat}  – pregled jednog chat‑a
     |-------------------------------------------------------------- */
    public function show(PurchaseChat $purchaseChat)
    {
        $this->authorize('view', $purchaseChat);

        abort_unless($purchaseChat->status === 'archived', 404);

        $purchaseChat->load([
            'messages' => fn ($q) => $q->orderBy('created_at'),   // hronološki
        ]);

        return view('advisor.history.show', compact('purchaseChat'));
    }

    /* --------------------------------------------------------------
     |  POST /advisor/history/{purchaseChat}/restore  – (OPTIONAL)
     |  vraća arhivirani chat nazad u “active” i preusmerava ga u chat
     |-------------------------------------------------------------- */
    public function restore(PurchaseChat $purchaseChat)
    {
        $this->authorize('view', $purchaseChat);

        abort_unless($purchaseChat->status === 'archived', 404);

        $purchaseChat->update(['status' => 'active']);

        return redirect()->route('advisor.chat', $purchaseChat);
    }
}
