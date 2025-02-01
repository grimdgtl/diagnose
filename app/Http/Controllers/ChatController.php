<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Question;
use App\Models\Response;
use App\Models\TempChat;
use App\Models\TempQuestion;
use App\Models\TempResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\OpenAiService;

class ChatController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            // GOST -> temp_ tabele
            $tempId = session('temp_id');
            if ($tempId) {
                $tempQuestions = TempQuestion::where('temp_id', $tempId)->get();
                $tempResponses = TempResponse::whereIn('question_id', $tempQuestions->pluck('id'))->get();

                return view('chat.guest-dashboard', compact('tempQuestions','tempResponses'));
            } else {
                return redirect('/')->with('info','Niste uneli problem.');
            }
        } else {
            // Registrovan korisnik
            $user = Auth::user();
            // Pronađi poslednji open chat
            $chat = Chat::where('user_id', $user->id)->where('status','open')->latest()->first();

            if (!$chat) {
                return view('chat.dashboard', [
                    'questions' => collect([]),
                    'responses' => collect([]),
                    'chat'      => null
                ]);
            }

            $questions = Question::where('chat_id', $chat->id)->get();
            // Eager load
            $chat->load('questions.responses'); // ← dodali smo ';'

            // Možeš ili ovako:
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
        $chat = Chat::where('id',$request->chat_id)->where('user_id',$user->id)->firstOrFail();

        // Kreiramo question
        $question = Question::create([
            'user_id'          => $user->id,
            'issueDescription' => $request->message,
            'chat_id'          => $chat->id,
        ]);

        // Ovde kasnije možeš da ubaciš pravi ChatGPT upit:
        //chatGptResponse = "Odgovor ChatGPT-a na: " . $request->message;
        $chatGptResponse = (new OpenAiService)->ask($request->message);

        // Snimamo response
        $response = Response::create([
            'question_id' => $question->id,
            'content'     => $chatGptResponse
        ]);

        // Smanjimo broj pitanja
        if ($user->num_of_questions_left > 0) {
            $user->num_of_questions_left -= 1;
            $user->save();
        }

        return redirect()->route('dashboard')->with('success','Pitanje i odgovor su sačuvani.');
    }

    // ChatController.php
    public function newChat()
    {
        $user = Auth::user();

        // Zatvori stari chat, ako postoji
        $openChat = Chat::where('user_id', $user->id)->where('status','open')->first();
        if ($openChat) {
            $openChat->status = 'closed';
            $openChat->closed_at = now();
            $openChat->save();
        }

        // Ako je gost (teoretski, ovo se zove iz linka koji je dostupan samo registrovanima
        // ali za svaki slučaj):
        if (!$user) {
            return redirect()->route('guest.wizard-form');
        }

        // Ako je registrovan
        return redirect()->route('registered.wizard-form');
    }  

}
