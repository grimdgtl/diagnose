<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Question;
use App\Models\Response;
use App\Models\TempQuestion;
use App\Models\TempResponse;
use App\Models\CarDetail;
use App\Services\OpenAiService;

class ChatController extends Controller
{
    /**
     * Prikazuje chat za registrovane i za goste.
     */
    public function index(Request $request)
    {
        // GOST
        if (!Auth::check()) {
            $tempId = session('temp_id');

            // Ako gost nije poslao inicijalni upit, preusmeri na wizard formu
            if (! $tempId || ! TempQuestion::where('temp_id', $tempId)->exists()) {
                return redirect()->route('guest.wizard-form');
            }

            // Inače, prikazujemo guest-dashboard
            $tempQuestions = TempQuestion::where('temp_id', $tempId)->get();
            $tempResponses = TempResponse::whereIn(
                'question_id',
                $tempQuestions->pluck('id')
            )->get();

            return view('chat.guest-dashboard', compact('tempQuestions', 'tempResponses'));
        }

        // REGISTROVANI KORISNIK
        $user = Auth::user();
        $chat = Chat::where('user_id', $user->id)
                    ->where('status', 'open')
                    ->latest()
                    ->first();

        if (! $chat) {
            return view('chat.dashboard', [
                'chat'      => null,
                'questions' => collect([]),
                'responses' => collect([]),
            ]);
        }

        $questions = Question::where('chat_id', $chat->id)->get();
        $chat->load('questions.responses');
        $responses = Response::whereIn(
            'question_id',
            $questions->pluck('id')
        )->get();

        return view('chat.dashboard', compact('chat', 'questions', 'responses'));
    }

    /**
     * Čuva novo pitanje registrovanog korisnika.
     */
    public function storeQuestion(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'chat_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $chat = Chat::where('id', $request->chat_id)
                    ->where('user_id', $user->id)
                    ->firstOrFail();

        // Kreiramo question
        $question = Question::create([
            'user_id'          => $user->id,
            'issueDescription' => $request->message,
            'chat_id'          => $chat->id,
        ]);

        // Dohvat CarDetail iz baze
        $carDetail = CarDetail::where('user_id', $user->id)
                               ->latest()
                               ->first();

        // Instanciramo OpenAiService
        $openAi = new OpenAiService();

        // Pripremamo kontekst auta ako postoji
        $carForm = [];
        if ($carDetail) {
            $carForm = [
                'brand'           => $carDetail->brand,
                'model'           => $carDetail->model,
                'year'            => $carDetail->year,
                'fuelType'        => $carDetail->fuel_type,
                'engineCapacity'  => $carDetail->engine_capacity,
                'enginePower'     => $carDetail->engine_power,
                'transmission'    => $carDetail->transmission,
            ];
        }

        $chatGptResponse = $openAi->handleUserQuestion(
            null,
            null,
            $request->message,
            $carForm
        );

        // Snimamo odgovor
        $response = Response::create([
            'question_id' => $question->id,
            'content'     => $chatGptResponse,
        ]);

        // Smanjimo broj pitanja
        if ($user->num_of_questions_left > 0) {
            $user->num_of_questions_left--;
            $user->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'question'       => $question->issueDescription,
                'answer'         => $response->content,
                'questions_left' => $user->num_of_questions_left,
            ]);
        }

        return redirect()->route('dashboard')
                         ->with('success', 'Pitanje i odgovor su sačuvani.');
    }

    /**
     * Kreira novi chat za registrovanog korisnika ili goste.
     */
    public function newChat()
    {
        $user = Auth::user();

        // Zatvorimo otvoreni chat ako postoji
        $openChat = Chat::where('user_id', $user->id)
                        ->where('status', 'open')
                        ->first();
        if ($openChat) {
            $openChat->status    = 'closed';
            $openChat->closed_at = now();
            $openChat->save();
        }

        // Resetujemo ChatGPT sesiju
        $openAi = new OpenAiService();
        $openAi->resetMessages();

        // Preusmeravanje na registrovanu formu
        return redirect()->route('registered.wizard-form');
    }
}
