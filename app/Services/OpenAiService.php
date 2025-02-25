<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * OpenAiService
 * Služi za upravljanje kontekstom i slanje upita ChatGPT-u
 */
class OpenAiService
{
    protected $apiUrl;
    protected $apiKey;

    // Session ključevi
    protected $sessionMessagesKey = 'chatgpt_messages';
    protected $systemMessageSentKey = 'system_message_sent';
    protected $carFormSentKey = 'car_form_sent';
    protected $diagnoseSentKey = 'diagnose_sent';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', 'YOUR_FALLBACK_API_KEY');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';

        // Proveravamo da li već postoji niz poruka u session
        if (!Session::has($this->sessionMessagesKey)) {
            Session::put($this->sessionMessagesKey, []);
        }
    }

    /**
     * Vraća podrazumevane sistemske instrukcije za ChatGPT
     */
    private function getDefaultSystemMessage(): string
    {
        return "Ti si iskusan srpski automehaničar, ekspert za automobile, mehaniku i auto elektriku – zovu te AutoMentor. Tvoj stil je jasan, jednostavan i prijateljski 'ortak' ton, kao da sediš sa drugarom uz kafu i pričaš o kolima. Korisnik ti daje podatke o problemu (opis, dijagnostika, lampice) i autu (marka, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača), a ti na osnovu toga pomažeš da otkrije šta nije u redu i šta da radi dalje.
            Pravila za odgovore:
                - Piši u Markdown formatu.
                - Koristi nenumerisane liste (`-`) za moguće uzroke, korake ili savete.
                - Ako imaš linkove, ubaci ih kao `[tekst](url)`.
                - Odvajaj pasuse praznim redovima da bude lako za čitanje.
                - Objasni stvari prosto, kao nekome ko nije majstor, ali sa dovoljno detalja da bude korisno.
                - Fokusiraj se na praktične korake: šta korisnik može sam da proveri (npr. filter, svećice) ili šta da odnese majstoru.
                - Ako nemaš dovoljno podataka, pitaj dodatna pitanja (npr. 'Kako auto vuče na leru?', 'Jel dimi iz auspuha?') i podseti korisnika da ti da više detalja o simptomima ili okolnostima.
                - Ako je problem nejasan, podstakni korisnika da pojasni (npr. 'Daj mi još malo info, pa ćemo skontati šta je.').
                - Vodi razgovor ka rešavanju problema i sledećim koracima, uvek ostavi prostor da te pitaju još nešto.
                - Ako pitanje nije o kolima, kaži: 'To nije moj teren, ortak, pričajmo o kolima.'
                - Ohrabri korisnika da nastavi sa pitanjima ako treba još pojašnjenja (npr. 'Ako šta zapne, pitaj me slobodno!').
                - Ako korisnik pita gde da kupi delove, koristi samo sajtove prodajadelova.rs i autohub.rs za pretragu i predloge. Ne spominji druge sajtove osim ova dva.";
    }

    /**
     * Dodaje system poruku samo jednom po sesiji (ako je niste već dodali).
     */
    public function setSystemMessageOnce(string $message)
    {
        if (!Session::has($this->systemMessageSentKey)) {
            $this->addMessage('system', $message);
            Session::put($this->systemMessageSentKey, true);
            Log::info("System message initialized and added to chat session.");
        }
    }

    /**
     * Dodaje poruku (role: system, assistant, user) u session.
     */
    public function addMessage(string $role, string $content): void
    {
        if (!in_array($role, ['system','assistant','user'])) {
            Log::warning("Invalid role: $role");
            return;
        }

        $messages = Session::get($this->sessionMessagesKey, []);
        $messages[] = [
            'role'    => $role,
            'content' => $content,
        ];
        Session::put($this->sessionMessagesKey, $messages);

        Log::info("Added message to session: [$role] $content");
    }

    public function getMessages(): array
    {
        return Session::get($this->sessionMessagesKey, []);
    }

    /**
     * Resetuje sve poruke i indikatore (system, car, diagnose).
     */
    public function resetMessages(): void
    {
        Session::forget($this->sessionMessagesKey);
        Session::forget($this->systemMessageSentKey);
        Session::forget($this->carFormSentKey);
        Session::forget($this->diagnoseSentKey);

        Log::info("ChatGPT session messages have been reset.");
    }

    /**
     * Dodaje informacije o automobilu kao system poruku
     * SAMO jednom po sesiji (ako je već dodato, preskače).
     */
    public function addCarFormContext(array $carForm)
    {
        if (!Session::has($this->carFormSentKey)) {
            $carDetails = sprintf(
                "Automobil je: %s %s, Godište: %s, Gorivo: %s, Kubikaža: %s, Snaga: %s kW, Menjač: %s.",
                $carForm['brand'] ?? '',
                $carForm['model'] ?? '',
                $carForm['year'] ?? '',
                $carForm['fuelType'] ?? '',
                $carForm['engineCapacity'] ?? '',
                $carForm['enginePower'] ?? '',
                $carForm['transmission'] ?? ''
            );

            $this->addMessage('system', $carDetails);

            Session::put($this->carFormSentKey, true);
            Log::info("Car details appended: $carDetails");
        } else {
            Log::info("Car details were already appended once, skipping...");
        }
    }

    /**
     * Dodaje dijagnostiku i/ili lampicu samo jednom po sesiji.
     */
    public function addDiagnoseContext(?string $diagnose, ?string $indicatorLight)
    {
        if (!Session::has($this->diagnoseSentKey)) {
            if ($diagnose) {
                $this->addMessage('user', "Dijagnostika: $diagnose");
            }
            if ($indicatorLight) {
                $this->addMessage('user', "Lampica upozorenja: $indicatorLight");
            }
            Session::put($this->diagnoseSentKey, true);

            Log::info("Diagnose/lamp appended.");
        }
    }

    /**
     * Formira payload za ChatGPT API
     */
    public function prepareApiRequest(string $model = 'gpt-3.5-turbo'): array
    {
        $messages = $this->getMessages();
        return [
            'model'    => $model,
            'messages' => $messages,
        ];
    }

    /**
     * Šalje upit ChatGPT API i vraća decode-ovan odgovor ili null ako greška
     */
    public function sendApiRequest(array $payload): ?array
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $encodedPayload = json_encode($payload);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Log::error("Curl error: $error");
            curl_close($ch);
            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Log::info("OpenAI API response (HTTP $httpCode): $response");

        $decoded = json_decode($response, true);

        if (!is_array($decoded) || !isset($decoded['choices'])) {
            Log::warning("OpenAI response missing 'choices' key or invalid JSON structure.", $decoded ?? []);
            return null;
        }

        return $decoded;
    }

    /**
     * Jednostavno prepoznavanje fraza "gde mogu da kupim" i slično
     */
    protected function isAskingWhereToBuy(?string $userQuestion): bool
    {
        if (!$userQuestion) {
            return false;
        }

        $patterns = [
            '/gde\s+(mogu\s+)?(da\s+)?(kupim|kupiti|poručim|porucim)/i',
            '/(where\s+to\s+buy)/i',
            '/kupovina\s+dela/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $userQuestion)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Proverava da li je domen relevantan za auto-delove
     */
    private function isRelevantDomain(string $url): bool
    {
        // Lista dozvoljenih domena za auto-delove
        $allowedDomains = [
            'prodajadelova.rs',
            'autohub.rs',
        ];

        // Izvuci domen iz URL-a
        $domain = parse_url($url, PHP_URL_HOST);

        // Proveri da li je domen u listi dozvoljenih
        return in_array($domain, $allowedDomains);
    }

    /**
     * Google Custom Search – dohvat linkova
     */
    protected function searchGoogle(string $query): string
    {
        $apiKey = env('GOOGLE_API_KEY');
        $cx = env('GOOGLE_CX');

        if (!$apiKey || !$cx) {
            Log::warning("Google API key ili CX nije podešen u .env fajlu.");
            return '';
        }

        // Ograničavamo pretragu samo na prodajadelova.rs i autohub.rs sa ključnom reči "auto delovi"
        $enhancedQuery = $query . " auto delovi site:prodajadelova.rs | site:autohub.rs";
        $url = sprintf(
            'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s',
            urlencode($apiKey),
            urlencode($cx),
            urlencode($enhancedQuery)
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Log::error("searchGoogle cURL error: $error");
            curl_close($ch);
            return '';
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Log::warning("searchGoogle API returned HTTP code $httpCode");
            return '';
        }

        $data = json_decode($response, true);
        if (!isset($data['items']) || !is_array($data['items'])) {
            Log::info("searchGoogle: nema 'items' u rezultatu ili je format neispravan.");
            return '';
        }

        // Uzimamo samo relevantne rezultate
        $resultLinks = [];
        foreach (array_slice($data['items'], 0, 3) as $item) {
            $link = $item['link'] ?? '';
            $title = $item['title'] ?? '';
            if ($link && $this->isRelevantDomain($link)) {
                $resultLinks[] = "- $title: $link";
            }
        }

        return !empty($resultLinks) ? implode("\n", $resultLinks) : '';
    }

    /**
     * Jedan mogući "wizard" metod ako hoćete session-based kontekst
     * (diagnose, lampica, carForm).
     */
    public function handleUserQuestion(
        ?string $diagnose,
        ?string $indicatorLight,
        ?string $newQuestion,
        array $carForm = [],
        string $systemMessage = null
    ): ?string {
        // Postavljamo podrazumevane sistemske instrukcije ako nisu prosleđene
        $this->setSystemMessageOnce($systemMessage ?? $this->getDefaultSystemMessage());

        // Dodajemo info o automobilu, ako postoji
        if (!empty($carForm)) {
            $this->addCarFormContext($carForm);
        }

        // Dodajemo dijagnostiku i lampicu, ako postoje
        $this->addDiagnoseContext($diagnose, $indicatorLight);

        // Dodajemo novo pitanje korisnika
        if ($newQuestion) {
            $this->addMessage('user', $newQuestion);
        }

        // Provera za "gde mogu da kupim"
        $userIsAskingToBuy = $this->isAskingWhereToBuy($newQuestion);

        // Šaljemo upit ChatGPT-u
        $payload = $this->prepareApiRequest('gpt-3.5-turbo');
        $response = $this->sendApiRequest($payload);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            $assistantReply = $response['choices'][0]['message']['content'];

            if ($userIsAskingToBuy) {
                $searchQuery = $newQuestion;
                if (!empty($carForm['brand'])) {
                    $searchQuery .= " {$carForm['brand']}";
                }
                if (!empty($carForm['model'])) {
                    $searchQuery .= " {$carForm['model']}";
                }
                if (!empty($carForm['year'])) {
                    $searchQuery .= " {$carForm['year']}";
                }
                if (!empty($carForm['engineCapacity'])) {
                    $searchQuery .= " {$carForm['engineCapacity']}ccm";
                }
                if (!empty($carForm['enginePower'])) {
                    $searchQuery .= " {$carForm['enginePower']}kW";
                }
                if (!empty($carForm['fuelType'])) {
                    $searchQuery .= " {$carForm['fuelType']}";
                }
                if (!empty($carForm['transmission'])) {
                    $searchQuery .= " {$carForm['transmission']}";
                }

                $searchResults = $this->searchGoogle($searchQuery);
                if (!empty($searchResults)) {
                    $assistantReply .= "\n\nEvo nekoliko linkova gde možeš proveriti ponudu:\n" . $searchResults;
                } else {
                    $assistantReply .= "\n\nNisam uspeo da nađem tačan link sad, ali proveri na [ProdajaDelova](https://prodajadelova.rs) ili [AutoHub](https://autohub.rs).";
                }
            }

            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }

        return "Ej, došlo je do greške u komunikaciji sa ChatGPT-om, probaj opet!";
    }

    /**
     * Uprošćeni metod za slanje pitanja GPT-u, uz opcioni carForm.
     */
    public function ask(string $prompt, array $carForm = []): string
    {
        $this->addMessage('system', $this->getDefaultSystemMessage());
        $this->addMessage('user', $prompt);

        $payload  = $this->prepareApiRequest('gpt-3.5-turbo');
        $response = $this->sendApiRequest($payload);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            $assistantReply = $response['choices'][0]['message']['content'];

            // Ako korisnik pita "gde mogu da kupim", ubacujemo i podatke o kolima ako postoje
            if ($this->isAskingWhereToBuy($prompt)) {
                $searchQuery = $prompt;

                if (!empty($carForm['brand'])) {
                    $searchQuery .= " {$carForm['brand']}";
                }
                if (!empty($carForm['model'])) {
                    $searchQuery .= " {$carForm['model']}";
                }
                if (!empty($carForm['year'])) {
                    $searchQuery .= " {$carForm['year']}";
                }
                if (!empty($carForm['engineCapacity'])) {
                    $searchQuery .= " {$carForm['engineCapacity']}ccm";
                }
                if (!empty($carForm['enginePower'])) {
                    $searchQuery .= " {$carForm['enginePower']}kW";
                }
                if (!empty($carForm['fuelType'])) {
                    $searchQuery .= " {$carForm['fuelType']}";
                }
                if (!empty($carForm['transmission'])) {
                    $searchQuery .= " {$carForm['transmission']}";
                }

                $searchResults = $this->searchGoogle($searchQuery);

                if (!empty($searchResults)) {
                    $assistantReply .= "\n\nEvo nekoliko linkova gde možeš proveriti ponudu:\n" . $searchResults;
                } else {
                    $assistantReply .= "\n\nNisam uspeo da nađem tačan link sad, ali proveri na [ProdajaDelova](https://prodajadelova.rs) ili [AutoHub](https://autohub.rs).";
                }
            }

            // Snimamo odgovor i vraćamo
            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }

        return "Došlo je do greške pri komunikaciji sa ChatGPT-om.";
    }
}