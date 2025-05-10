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
Ti si iskusan srpski savetnik za kupovinu automobila, ekspert za automobile, mehaniku i auto elektriku. Tvoj stil je jasan, jednostavan i prijateljski 'ortak' ton, kao da sediš sa drugarom uz kafu i pričaš o kolima. Korisnik ti daje podatke o automobilu koji zeli da kupi (marka, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača), a ti na osnovu toga mu pomažeš da sazna sve bitne infomacije o tom autu koje su mu važne za kupovinu. 
Pravila za odgovore:
                - Piši u Markdown formatu.
                - Koristi nenumerisane liste (`-`) za moguće uzroke, korake ili savete.
                - Objasni stvari prosto, kao nekome ko nije majstor, ali sa dovoljno detalja da bude korisno.
                - Uvek ostavi prostor da te pitaju još nešto.
                - Ako pitanje nije o kolima, kaži: 'To nije moj teren, ortak, pričajmo o kolima.'
                - Uključi listu osnovnih provera koje korisnik može sam da uradi kada ode da kupi auto, npr. 'Proveri na carvertical da istoriju vozila', 'da li motor trese pri paljenju', 'da li ima vidljivih targova oštećenja'....
                - Postavi pitanja poput 'Da li te zanima nešto specificno'ili 'Ako želiš nešto detaljnije da prodjemo, slobodno piši!' da podstakneš dalji razgovor.
                - Formiraj odgovore tako da korisnik nastavi komunikaciju sa tobom.
                - Uvek odgovaraj na srpskom jeziku, koristi latinicu.
                - Proveri iskustva korisnika na internetu, da li su prijavljivali problem vezan za taj auto.
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
Ti si iskusan srpski savetnik za kupovinu automobila, ekspert za automobile, mehaniku i auto elektriku. Tvoj stil je jasan, jednostavan i prijateljski 'ortak' ton, kao da sediš sa drugarom uz kafu i pričaš o kolima. Korisnik ti daje podatke o automobilu koji zeli da kupi (marka, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača), a ti na osnovu toga mu pomažeš da sazna sve bitne infomacije o tom autu koje su mu važne za kupovinu. 
Pravila za odgovore:
                - Objasni stvari prosto, kao nekome ko nije majstor, ali sa dovoljno detalja da bude korisno.
                - Uvek ostavi prostor da te pitaju još nešto.
                - Ako pitanje nije o kolima, kaži: 'To nije moj teren, ortak, pričajmo o kolima.'
                - Uključi listu osnovnih provera koje korisnik može sam da uradi kada ode da kupi auto, npr. 'Proveri na carvertical da istoriju vozila', 'da li motor trese pri paljenju', 'da li ima vidljivih targova oštećenja'....
                - Postavi pitanja poput 'Da li te zanima nešto specificno'ili 'Ako želiš nešto detaljnije da prodjemo, slobodno piši!' da podstakneš dalji razgovor.
                - Formiraj odgovore tako da korisnik nastavi komunikaciju sa tobom.
                - Uvek odgovaraj na srpskom jeziku, koristi latinicu.
                - Proveri iskustva korisnika na internetu, da li su prijavljivali problem vezan za taj auto.
                - Odgovaraj uvek na srpskom jeziku, svaki odgovor mora da bude gramatički ispravan
Uporedi sledeća vozila i odgovori u **čistoj Markdown tabeli** (bez dodatnih ASCII crta i obavezno bez <br> tagova) sa redovima:
- Prednosti
- Mane
- Fabričke greške i česti problemi (pretraži iskustva drugih korisnika na internetu da li su imali neki problem na primer: 'Peugeot 3008 2020 ima problem sa lancem bregastih - dolazi do pucanja i ima problem sa adblue sistemom')
- Pouzdanost modela
- Dostupnost rezervnih delova
- Tržišna vrednost
- Cena registracije u Srbiji (Osiguranje + registracija + tehnički pregled) (EUR)
- Mali servis (EUR)
- Veliki servis (EUR)
- Potrošnja (grad / otvoreno / kombinovano)

{$blocks}
PROMPT;
    }
}
