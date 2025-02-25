<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\TempUser;
use App\Models\TempQuestion;
use App\Models\TempCarDetail;
use App\Models\TempResponse;
use App\Models\TempChat;
use App\Models\Question;
use App\Models\CarDetail;
use App\Models\Response as RealResponse;
use App\Models\Chat;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        Log::info("Ulazim u register()...");

        // 1. Validacija
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|string|max:255',
            'password'   => [
                'required',
                'confirmed', // Potvrda šifre
                'min:8',
                'regex:/[a-z]/',      // Minimum jedno malo slovo
                'regex:/[A-Z]/',      // Minimum jedno veliko slovo
                'regex:/[0-9]/',      // Minimum jedan broj
            ],
            'city'       => 'required|string|max:255',
            'country'    => 'required|in:RS,BA,HR,ME', // Validacija za ISO kodove
            'terms'      => 'accepted', // Potvrda uslova korišćenja
        ], [
            'password.regex' => 'Šifra mora sadržati najmanje jedno veliko slovo, jedno malo slovo i jedan broj.',
            'terms.accepted' => 'Morate se složiti sa Uslovima korišćenja i Politikom privatnosti.',
            'country.in' => 'Morate izabrati validnu državu iz ponuđenih opcija.',
        ]);

        Log::info("Validacija prošla, kreiram novog user-a...");

        // 2. Kreiraj user.id (UUID ili generisan string, pošto je varchar)
        $userId = (string) Str::uuid();

        // 3. Kreiraj user-a
        $user = User::create([
            'id'                => $userId,
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'city'              => $request->city,
            'country'           => $request->country, // ISO kod se upisuje direktno (RS, BA, HR, ME)
            'verified'          => false,
            'verification_token'=> Str::random(32),
            'num_of_questions_left' => 2, // Dajemo 2 besplatna pitanja
        ]);

        Log::info("User kreiran, ID = {$user->id}, email = {$user->email}");

        // 4. Pošalji email za verifikaciju
        $verificationLink = route('verify.email', [
            'token' => $user->verification_token,
            'temp_id' => session('temp_id'), // Dodaj temp_id u link
        ]);

        try {
            Mail::to($user->email)->send(new \App\Mail\VerificationMail($user, $verificationLink));
            Log::info("Verifikacioni mail uspešno poslat na {$user->email}");
        } catch (\Exception $ex) {
            Log::error("Greška prilikom slanja emaila korisniku {$user->email}: " . $ex->getMessage());
        }

        // 5. Preusmeri na "molimo proverite mail"
        return redirect()->route('verify.notice');
    }

    /**
     * Stranica "Molimo proverite email za verifikaciju".
     */
    public function verifyNotice()
    {
        return view('auth.verify-notice');
    }

    /**
     * Ruta za verifikaciju email-a (npr. /verify?token=xxx).
     * Ruta: GET /verify?token=xxx
     */
    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');
        $tempId = $request->query('temp_id'); // Uzmi temp_id iz query parametra

        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            Log::error("Nevažeći ili iskorišćeni token za verifikaciju.");
            return redirect('/')->with('error', 'Nevažeći ili iskorišćeni token.');
        }

        $user->verified = true;
        $user->verification_token = null;
        $user->save();

        Log::info("Email je verifikovan, user_id: {$user->id}");

        // Ako temp_id nije u sesiji, ali je prosleđen kroz query parametar, sačuvaj ga u sesiji
        if (!$request->session()->has('temp_id') && $tempId) {
            session(['temp_id' => $tempId]);
        }

        if (session()->has('temp_id')) {
            $tempId = session('temp_id');
            Log::info("Temp ID found in session: {$tempId}");
            $this->migrateTempDataToReal($tempId, $user->id); // Migriraj podatke
        } else {
            Log::warning("No temp_id in session after email verification.");
        }

        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Email verifikovan, dobrodošli!');
    }

    /**
     * Logika prebacivanja slogova iz temp_* u prave tabele.
     */
    public function migrateTempDataToReal($tempId, $userId)
    {
        Log::info("Starting migration for temp_id: {$tempId}, user_id: {$userId}");

        // 1. Dohvati sve temp slogove
        $tempUser = TempUser::find($tempId);
        if (!$tempUser) {
            Log::warning("Nema tempUser za temp_id={$tempId}. Nema šta da migrira");
            return;
        }

        $tempQuestions = TempQuestion::where('temp_id', $tempId)->get();
        $tempCarDetail = TempCarDetail::find($tempId);
        $tempChat = TempChat::where('temp_id', $tempId)->first();
        $tempResponses = TempResponse::whereIn('question_id', $tempQuestions->pluck('id'))->get();

        // 2. Kreiraj chat (status = open ili closed)
        $chat = Chat::create([
            'user_id'   => $userId,
            'status'    => 'open',
            'session_id'=> $tempChat ? $tempChat->id : null,
        ]);

        Log::info("Chat created: {$chat->id}");

        // 3. Kreiraj CarDetail (ako postoji)
        if ($tempCarDetail) {
            CarDetail::create([
                'user_id'         => $userId,
                'brand'           => $tempCarDetail->brand,
                'model'           => $tempCarDetail->model,
                'year'            => $tempCarDetail->year,
                'fuel_type'       => $tempCarDetail->fuel_type,
                'engine_capacity' => $tempCarDetail->engine_capacity,
                'engine_power'    => $tempCarDetail->engine_power,
                'transmission'    => $tempCarDetail->transmission,
            ]);
            Log::info("CarDetail created for user_id: {$userId}");
        }

        // 4. Prebaci questions i responses
        foreach ($tempQuestions as $tq) {
            $question = Question::create([
                'user_id'          => $userId,
                'issueDescription' => $tq->issueDescription,
                'diagnose'         => $tq->diagnose,
                'indicatorLight'   => $tq->indicatorLight,
                'chat_id'          => $chat->id,
            ]);
            Log::info("Question created: {$question->id}");

            // Pronađi tempResponse-e za ovo question
            $trs = $tempResponses->where('question_id', $tq->id);
            foreach ($trs as $tempRes) {
                RealResponse::create([
                    'question_id' => $question->id,
                    'content'     => $tempRes->content
                ]);
                Log::info("Response created for question_id: {$question->id}");
            }
        }

        // 5. Očisti temp tabele
        TempResponse::whereIn('question_id', $tempQuestions->pluck('id'))->delete();
        TempQuestion::where('temp_id', $tempId)->delete();
        if ($tempCarDetail) {
            $tempCarDetail->delete();
        }
        if ($tempChat) {
            $tempChat->delete();
        }
        $tempUser->delete();

        Log::info("Migration completed for temp_id: {$tempId}, user_id: {$userId}");
    }
}