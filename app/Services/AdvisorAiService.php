<?php

namespace App\Services;

use App\Models\PurchaseChat;
use App\Models\PurchaseMessage;
use App\Models\TempAdvisorMessage;
use Illuminate\Support\Facades\Session;

class AdvisorAiService extends OpenAiService
{
    /**
     * Prvi poziv – registrovani korisnici.
     */
    public function getInitialAnalysis(PurchaseChat $chat, array $cars): string
    {
        // Generišemo blok sa do 3 vozila
        $prompt = $this->buildUserPrompt($cars);

        // Snimamo initial user prompt
        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'user',
            'content'          => $prompt,
        ]);

        // Za prvi poziv vraćamo kompletan kontekst (system + prompt)
        $this->prepareContext($prompt);

        $answer = $this->askGpt();

        // Snimamo odgovor asistenta
        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'assistant',
            'content'          => $answer,
        ]);

        return $answer;
    }

    /**
     * Follow-up za registrovane korisnike:
     * šaljemo samo system + inicijalni prompt + novo pitanje.
     */
    public function followUp(PurchaseChat $chat, string $question): string
    {
        // 1) Snimimo novo pitanje korisnika
        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'user',
            'content'          => $question,
        ]);

        // 2) Dohvatimo prvi saved user prompt (blok vozila)
        $initial = PurchaseMessage::where('purchase_chat_id', $chat->id)
                    ->where('role', 'user')
                    ->orderBy('id')
                    ->first();

        $prompt = $initial ? $initial->content : '';

        // 3) Pripremimo kontekst: system + initial prompt
        $this->prepareContext($prompt);

        // 4) Dodamo samo novo pitanje
        $this->addMessage('user', $question);

        // 5) Pozovemo GPT
        $answer = $this->askGpt();

        // 6) Snimimo odgovor asistenta
        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'assistant',
            'content'          => $answer,
        ]);

        return $answer;
    }

    /**
     * Prvi poziv – guest korisnici.
     */
    public function getInitialAnalysisForGuest($chat, array $vehicles): string
    {
        $prompt = $this->buildUserPrompt($vehicles);

        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'user',
            'content'    => $prompt,
            'created_at' => now(),
        ]);

        $this->prepareContext($prompt);
        $answer = $this->askGpt();

        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'assistant',
            'content'    => $answer,
            'created_at' => now(),
        ]);

        return $answer;
    }

    /**
     * Follow-up za goste.
     */
    public function followUpGuest($chat, string $question): string
    {
        // 1) Dohvatimo inicijalni user prompt iz temp tabele
        $initial = TempAdvisorMessage::where('chat_id', $chat->id)
                       ->where('role', 'user')
                       ->orderBy('id')
                       ->first();

        $prompt = $initial ? $initial->content : '';

        // 2) Pripremimo context: system + inicijalni prompt
        $this->prepareContext($prompt);

        // 3) Dodamo samo novo pitanje
        $this->addMessage('user', $question);

        // 4) Pozovemo GPT
        $answer = $this->askGpt();

        // 5) Snimimo asistentski odgovor
        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'assistant',
            'content'    => $answer,
            'created_at' => now(),
        ]);

        return $answer;
    }

    /* ────────────────────────────────────────────────────────────── */

    /**
     * Postavlja u session i interne poruke:
     *  - jedna system poruka
     *  - jedna user poruka (blok sa vozilima)
     */
    private function prepareContext(string $prompt): void
    {
        $this->resetMessages();

        $sys = $this->buildSystemMsg();
        $this->messages = [
            ['role'    => 'system', 'content' => $sys],
            ['role'    => 'user',   'content' => $prompt],
        ];
        Session::put($this->sessionMessagesKey, $this->messages);
    }

    /**
     * (Ovaj metod više ne koristimo za followUp)
     */
    private function bootstrapFromDb(PurchaseChat $chat): void
    {
        // ostaje ako ga koristimo negde drugde
        $this->resetMessages();
        $this->addMessage('system', $this->buildSystemMsg());
        foreach ($chat->messages()->orderBy('id')->get() as $msg) {
            $this->addMessage($msg->role, $msg->content);
        }
    }

    private function askGpt(): string
    {
        $payload  = $this->prepareApiRequest('gpt-4.1-mini');
        $response = $this->sendApiRequest($payload);
        return $response['choices'][0]['message']['content'] ?? 'Greška…';
    }

    private function buildSystemMsg(): string
    {
        return <<<SYS
You are an experienced automotive consultant who speaks Serbian.
Your role is to provide detailed and accurate information about used cars,
including their advantages, disadvantages, common issues, service costs,
registration costs in Serbia, fuel consumption, reliability, availability
of spare parts, market value in Serbia, insurance costs, ecological aspects,
and more. Always respond in Serbian with a professional tone.
SYS;
    }

    private function buildUserPrompt(array $cars): string
    {
        $blocks = collect($cars)
            ->map(fn($c, $i) => sprintf(
                "VOZILO #%d:\nMarka: %s\nModel: %s\nGodište: %s\nKilometraža: %s km\nMotor: %scc %skW\nGorivo: %s\nMenjač: %s",
                $i + 1,
                $c['brand'],
                $c['model'],
                $c['year'],
                number_format($c['mileage'] ?? 0, 0, ',', '.'),
                $c['engine_capacity'],
                $c['engine_power'],
                $c['fuel_type'],
                $c['transmission']
            ))
            ->implode("\n\n");

        return <<<PROMPT
Uporedi sledeća vozila i odgovori u **čistoj Markdown tabeli** (bez dodatnih ASCII crta) sa redovima:
- Prednosti
- Mane
- Fabričke greške i česti problemi
- Pouzdanost modela
- Dostupnost rezervnih delova
- Tržišna vrednost
- Osiguranje
- Ekološki aspekt
- Mali servis (EUR)
- Veliki servis (EUR)
- Registracija (EUR)
- Potrošnja (grad / otvoreno / kombinovano)

{$blocks}
PROMPT;
    }
}
