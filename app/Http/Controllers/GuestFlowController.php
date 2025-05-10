<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\TempUser;
use App\Models\TempQuestion;
use App\Models\TempCarDetail;
use App\Models\TempChat;
use App\Models\TempResponse;
use App\Services\OpenAiService;

class GuestFlowController extends Controller
{
    public function showWizardForm()
    {
        return view('guest.wizard-form');
    }

    /* --------------------- PRVO PITANJE --------------------- */
    public function storeTempData(Request $request)
    {
        $validated = $request->validate([
            'issueDescription' => 'required|string',
            'indicatorLight'   => 'nullable|string',
            'diagnose'         => 'nullable|string',
            'brand'            => 'required|string',
            'model'            => 'required|string',
            'year'             => 'required|integer',
            'engine_capacity'  => 'required|string',
            'engine_power'     => 'required|string',
            'fuel_type'        => 'required|string',
            'transmission'     => 'required|string',
        ]);

        /* temp_id */
        $tempId = $request->session()->get('temp_id');
        if (!$tempId) {
            $tempId = (string) Str::uuid();
            TempUser::create(['temp_id' => $tempId]);
            $request->session()->put('temp_id', $tempId);
        }

        /* limit */
        if (TempQuestion::where('temp_id', $tempId)->count() >= 2) {
            return $this->limitReached($request);
        }

        /* upis */
        $question = TempQuestion::create([
            'temp_id'         => $tempId,
            'issueDescription'=> $validated['issueDescription'],
            'diagnose'        => $validated['diagnose'],
            'indicatorLight'  => $validated['indicatorLight'],
        ]);

        TempCarDetail::updateOrCreate(
            ['temp_id' => $tempId],
            [
                'brand'           => $validated['brand'],
                'model'           => $validated['model'],
                'year'            => $validated['year'],
                'fuel_type'       => $validated['fuel_type'],
                'engine_capacity' => $validated['engine_capacity'],
                'engine_power'    => $validated['engine_power'],
                'transmission'    => $validated['transmission'],
            ]
        );

        $chat = TempChat::firstOrCreate(['temp_id' => $tempId], ['status' => 'open']);

        $ai = new OpenAiService();
        $answer = $ai->handleUserQuestion(
            $validated['diagnose'],
            $validated['indicatorLight'],
            $validated['issueDescription'],
            [
                'brand'          => $validated['brand'],
                'model'          => $validated['model'],
                'year'           => $validated['year'],
                'fuelType'       => $validated['fuel_type'],
                'engineCapacity' => $validated['engine_capacity'],
                'enginePower'    => $validated['engine_power'],
                'transmission'   => $validated['transmission'],
            ]
        );

        TempResponse::create([
            'question_id' => $question->id,
            'content'     => $answer,
        ]);

        $redirect = route('dashboard');

        return $request->expectsJson()
            ? response()->json(['redirectUrl' => $redirect])
            : redirect($redirect);
    }

    /* --------------------- DRUGO PITANJE -------------------- */
    public function storeAdditionalQuestion(Request $request)
    {
        $request->validate(['issueDescription' => 'required|string']);

        $tempId = $request->session()->get('temp_id');
        if (!$tempId) {
            return response()->json(['message' => 'Sesija je istekla.'], 400);
        }

        if (TempQuestion::where('temp_id', $tempId)->count() >= 2) {
            return $this->limitReached($request, true);
        }

        $issue = $request->input('issueDescription');

        $question = TempQuestion::create([
            'temp_id'          => $tempId,
            'issueDescription' => $issue,
        ]);

        $ai = new OpenAiService();
        $answer = $ai->handleUserQuestion(null, null, $issue, []);

        TempResponse::create([
            'question_id' => $question->id,
            'content'     => $answer,
        ]);

        $total = TempQuestion::where('temp_id', $tempId)->count();

        $qHtml = '<div class="flex justify-end animate-fadeIn"><div class="bubble user">'
               . e($issue) . '</div></div>';

        $rHtml = '<div class="flex justify-start animate-fadeIn mb-2">'
               . '<div class="bubble assistant markdown-content" data-content="'
               . e($answer) . '"></div></div>';

        return response()->json([
            'questionHtml'   => $qHtml,
            'responseHtml'   => $rHtml,
            'questionsCount' => $total,
        ]);
    }

    /* --------------------- pomoćna --------------------- */
    protected function limitReached(Request $request, bool $json = false)
    {
        $url = route('register');

        return $request->expectsJson() || $json
            ? response()->json(['redirectUrl' => $url], 403)
            : redirect($url);
    }
}
