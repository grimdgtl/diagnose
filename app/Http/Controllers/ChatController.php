<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\OpenAiService;
use App\Models\CarDetail;

class ChatController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            // GOST
            $tempId = session('temp_id');
            if ($tempId) {
                $tempQuestions = \App\Models\TempQuestion::where('temp_id', $tempId)->get();
                $tempResponses = \App\Models\TempResponse::whereIn('question_id', $tempQuestions->pluck('id'))->get();

                return view('chat.guest-dashboard', compact('tempQuestions','tempResponses'));
            } else {
                return redirect('/')->with('info','Niste uneli problem.');
            }
        } else {
            // REG KORISNIK
            $user = Auth::user();
            $chat = Chat::where('user_id', $user->id)->where('status','open')->latest()->first();

            if (!$chat) {
                return view('chat.dashboard', [
                    'questions' => collect([]),
                    'responses' => collect([]),
                    'chat'      => null
                ]);
            }

            $questions = Question::where('chat_id', $chat->id)->get();
            $chat->load('questions.responses');
            $responses = Response::whereIn('question_id', $questions->pluck('id'))->get();

            return view('chat.dashboard', compact('chat','questions','responses'));
        }
    }

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
        $carDetail = CarDetail::where('user_id', $user->id)->latest()->first();

        // Instanciramo OpenAiService
        $openAi = new OpenAiService();

        // Ako imamo CarDetail, formiramo kontekst
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

        /**
         * Sada koristimo handleUserQuestion:
         *  - diagnose i indicatorLight ovde nisu definisani (pošto nema polja u formi),
         *    prosledićemo null
         *  - newQuestion je $request->message
         *  - $carForm ako postoji
         *  - systemMessage možemo proslediti ukoliko želimo (biće setovano jednom po sesiji)
         */
        $chatGptResponse = $openAi->handleUserQuestion(
            null,               // diagnose
            null,               // indicatorLight
            $request->message,  // newQuestion
            $carForm,           // carForm array
            "Ti si AutoMentor – virtualni asistent. Pomažeš vozačima da reše probleme na automobilu. Odgovaraj u Markdown formatu, koristi listu za nabrajanje, linkove u [tekst](url) formatu i podeli pasuse praznim redovima" 
        );

        // Snimamo odgovor (što nam je vratila handleUserQuestion)
        $response = Response::create([
            'question_id' => $question->id,
            'content'     => $chatGptResponse
        ]);

        // Smanjimo broj pitanja
        if ($user->num_of_questions_left > 0) {
            $user->num_of_questions_left -= 1;
            $user->save();
        }

        // AJAX ili klasičan redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'        => true,
                'question'       => $question->issueDescription,
                'answer'         => $response->content,
                'questions_left' => $user->num_of_questions_left,
            ]);
        }

        return redirect()->route('dashboard')
                         ->with('success','Pitanje i odgovor su sačuvani.');
    }

    public function newChat()
{
    $user = Auth::user();

    // Zatvaramo stari chat ako postoji
    $openChat = Chat::where('user_id', $user->id)->where('status','open')->first();
    if ($openChat) {
        $openChat->status = 'closed';
        $openChat->closed_at = now();
        $openChat->save();
    }

    // Resetujemo ChatGPT sesiju (kontekst)
    $openAi = new OpenAiService();
    $openAi->resetMessages();

    // Nastavljamo redirekciju na odgovarajuću formu
    if (!$user) {
        return redirect()->route('guest.wizard-form');
    }

    return redirect()->route('registered.wizard-form');
}

}
