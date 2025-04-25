<?php

namespace App\Services;

use App\Models\PurchaseChat;
use App\Models\PurchaseMessage;
use App\Models\TempAdvisorMessage;
use Illuminate\Support\Facades\Session;  // Dodato za rad sa sesijom

class AdvisorAiService extends OpenAiService   // koristi isti API‑key
{
    /* ==============================================================
     |  JAVNI API
     |============================================================== */

    /** Prvi poziv – analiza do 3 vozila (registrovani korisnici) */
    public function getInitialAnalysis(PurchaseChat $chat, array $cars): string
    {
        $prompt = $this->buildUserPrompt($cars);

        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'user',
            'content'          => $prompt,
        ]);

        $this->bootstrapFromDb($chat);
        $answer = $this->askGpt();

        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'assistant',
            'content'          => $answer,
        ]);

        return $answer;
    }

    /** Sledeći upiti u istom chatu (registrovani korisnici) */
    public function followUp(PurchaseChat $chat, string $question): string
    {
        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'user',
            'content'          => $question,
        ]);

        $this->bootstrapFromDb($chat);
        $answer = $this->askGpt();

        PurchaseMessage::create([
            'purchase_chat_id' => $chat->id,
            'role'             => 'assistant',
            'content'          => $answer,
        ]);

        return $answer;
    }

    /**
     * Prvi poziv – analiza za gost korisnike.
     *
     * @param mixed $chat  (Očekuje se instanca temp chat modela)
     * @param array $vehicles  Podaci o vozilima uneseni od strane gosta
     * @return string
     */
    public function getInitialAnalysisForGuest($chat, array $vehicles): string
    {
        $prompt = $this->buildGuestUserPrompt($vehicles);

        // Snimi korisničku poruku u temp tabelu
        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'user',
            'content'    => $prompt,
            'created_at' => now(),
        ]);

        // Pripremi kontekst poruka i ažuriraj session – ovo osigurava da se u API payload-u ima barem 2 poruke
        $this->prepareContext($prompt, true);

        $answer = $this->askGpt();

        // Snimi odgovor asistenta u temp tabelu
        TempAdvisorMessage::create([
            'chat_id'    => $chat->id,
            'role'       => 'assistant',
            'content'    => $answer,
            'created_at' => now(),
        ]);

        return $answer;
    }

    /* ==============================================================
     |  PRIVATE HELPERS
     |============================================================== */

    /**
     * Priprema kontekst poruka za API.
     *
     * Resetuje prethodne poruke i postavlja sistemsku i korisničku poruku.
     * Ova metoda ažurira i internu varijablu poruka, i session tako da je payload
     * uvek popunjen.
     *
     * @param string $prompt  Korisnički prompt
     * @param bool   $isGuest Ako je true, koristi gost sistemsku poruku.
     * @return void
     */
    private function prepareContext(string $prompt, bool $isGuest = false): void
    {
        // Resetuj prethodne poruke (metoda iz roditelj klase)
        $this->resetMessages();

        // Odredi sistemsku poruku
        $systemMsg = $isGuest ? $this->buildGuestSystemMsg() : $this->buildSystemMsg();

        // Kreiraj niz poruka (sistem + korisnik)
        $messages = [
            ['role' => 'system', 'content' => $systemMsg],
            ['role' => 'user',   'content' => $prompt],
        ];

        // Postavi internu varijablu poruka, ako se ona koristi u roditeljskoj logici
        $this->messages = $messages;

        // Takođe, ažuriraj session da bi prepareApiRequest() mogao da pročita niz poruka
        Session::put($this->sessionMessagesKey, $messages);
    }

    /**
     * Popunjava poruke iz baze za registrovane korisnike.
     */
    private function bootstrapFromDb(PurchaseChat $chat): void
    {
        $this->resetMessages();
        $this->addMessage('system', $this->buildSystemMsg());

        foreach ($chat->messages()->orderBy('id')->get() as $msg) {
            $this->addMessage($msg->role, $msg->content);
        }
    }

    /**
     * Poziva GPT API i vraća sadržaj odgovora.
     */
    private function askGpt(): string
    {
        $payload  = $this->prepareApiRequest('gpt-4.1-mini');
        $response = $this->sendApiRequest($payload);

        return $response['choices'][0]['message']['content'] ?? 'Greška…';
    }

    /* ------------------ FIKSNI STRINGOVI ------------------ */

    private function buildSystemMsg(): string
    {
        return <<<SYS
You are an experienced automotive consultant who speaks Serbian.
Your role is to provide detailed and accurate information about used cars,
including their advantages, disadvantages, common issues, service costs,
registration costs in Serbia, fuel consumption, reliability, availability
of spare parts, market value in Serbia, insurance costs, ecological aspects,
and more. Always respond in Serbian and use a professional tone.
SYS;
    }

    private function buildUserPrompt(array $cars): string
    {
        $blocks = collect($cars)->map(fn($c, $i) => sprintf(
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
        ))->implode("\n\n");

        return <<<PROMPT
Uporedi sledeća vozila i odgovori u **čistoj Markdown tabeli** (bez dodatnih ASCII crta) sa redovima:
- Prednosti
- Mane
- Fabričke greške i česti problemi (uključujući informacije iz diskusija na forumima entuzijasta)
- Pouzdanost modela
- Dostupnost rezervnih delova (lakoća nabavke i cena)
- Tržišna vrednost (procena deprecijacije)
- Osiguranje (okvirna cena)
- Ekološki aspekt (emisija CO2, ekološka kategorija)
- Mali servis (EUR)
- Veliki servis (EUR)
- Registracija (EUR)
- Potrošnja (grad / otvoreno / kombinovano)

{$blocks}
PROMPT;
    }

    protected function buildGuestSystemMsg(): string
    {
        return <<<SYS
You are an experienced automotive consultant who speaks Serbian.
Please provide detailed and professional advice about used cars for guest users.
Always respond in Serbian in a friendly and informative manner.
SYS;
    }

    protected function buildGuestUserPrompt(array $vehicles): string
    {
        $blocks = "";
        foreach ($vehicles as $i => $vehicle) {
            $idx = $i + 1;
            $blocks .= "VOZILO #$idx:\n";
            $blocks .= "Marka: {$vehicle['brand']}\n";
            $blocks .= "Model: {$vehicle['model']}\n";
            $blocks .= "Godište: {$vehicle['year']}\n";
            $blocks .= "Kilometraža: {$vehicle['mileage']} km\n";
            $blocks .= "Motor: {$vehicle['engine_capacity']} ccm, {$vehicle['engine_power']} kW\n";
            $blocks .= "Gorivo: {$vehicle['fuel_type']}\n";
            $blocks .= "Menjač: {$vehicle['transmission']}\n\n";
        }

        return <<<PROMPT
Uporedi sledeća vozila i odgovori u čistom Markdown formatu (bez dodatnih ASCII crta) sa redovima:
- Prednosti
- Mane
- Fabričke greške i česti problemi
- Pouzdanost modela
- Dostupnost rezervnih delova
- Tržišna vrednost
- Osiguranje
- Ekološki aspekt
- Cena malog servisa (EUR)
- Cena velikog servisa (EUR)
- Registracija (EUR)
- Potrošnja (grad/otvoreno/kompaktno)

{$blocks}
PROMPT;
    }
}
