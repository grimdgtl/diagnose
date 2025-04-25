<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;                       // *** NOVO
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PurchaseChat;
use App\Models\PurchaseMessage;
use App\Services\AdvisorAiService;

class AdvisorChatController extends Controller
{
    use AuthorizesRequests;

    /* --------------------------------------------------------------
     |  GET /advisor/chat/{purchaseChat}
     |-------------------------------------------------------------- */
    public function show(PurchaseChat $purchaseChat)
    {
        $this->authorize('view', $purchaseChat);
        $purchaseChat->load('messages');

        return view('advisor.chat', compact('purchaseChat'));
    }

    /* --------------------------------------------------------------
     |  POST /advisor/chat/{purchaseChat}/message
     |-------------------------------------------------------------- */
    public function store(
        Request          $request,
        PurchaseChat     $purchaseChat,
        AdvisorAiService $ai
    ) {
        $this->authorize('view', $purchaseChat);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // *** NOVO – provera i decrement tokena
        $user = Auth::user();
        abort_if($user->num_of_questions_left <= 0, 403, 'Nemate tokena.');

        // Popunjava AI odgovor i snima ga kroz servis
        $answer = $ai->followUp($purchaseChat, $validated['message']);

        // Smanji broj tokena i snimi
        $user->num_of_questions_left -= 1;
        $user->save();

        return response()->json([
            'success'        => true,
            'answer'         => $answer,
            'questions_left' => $user->num_of_questions_left,   // *** NOVO
        ]);
    }

    /* --------------------------------------------------------------
     |  GET /advisor/chat/{purchaseChat}/export
     |-------------------------------------------------------------- */
    public function export(PurchaseChat $purchaseChat)
    {
        $this->authorize('view', $purchaseChat);

        $assistant = $purchaseChat->messages()
                                  ->where('role', 'assistant')
                                  ->firstOrFail();

        $pdf = Pdf::loadHTML(Str::markdown($assistant->content))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('advisor-' . $purchaseChat->id . '.pdf');
    }

    /* --------------------------------------------------------------
     |  POST /advisor/chat/{purchaseChat}/archive
     |-------------------------------------------------------------- */
    public function archive(PurchaseChat $purchaseChat)
    {
        $this->authorize('view', $purchaseChat);

        $purchaseChat->update(['status' => 'archived']);

        return redirect()->route('advisor.wizard');
    }
}
