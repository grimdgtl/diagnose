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
    protected $sonarApiKey;
    protected $sonarApiUrl = 'https://api.perplexity.ai/chat/completions';

    // Session ključevi
    protected $sessionMessagesKey = 'chatgpt_messages';
    protected $systemMessageSentKey = 'system_message_sent';
    protected $carFormSentKey = 'car_form_sent';
    protected $diagnoseSentKey = 'diagnose_sent';

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', 'YOUR_FALLBACK_API_KEY');
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
        $this->sonarApiKey = env('PERPLEXITY_API_KEY', 'YOUR_FALLBACK_PERPLEXITY_KEY');

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
        return "Ti si iskusan srpski automehaničar, ekspert za automobile, mehaniku i auto elektriku. Tvoj stil je jasan, jednostavan i prijateljski 'ortak' ton, kao da sediš sa drugarom uz kafu i pričaš o kolima. Korisnik ti daje podatke o problemu (opis, dijagnostika, lampice) i autu (marka, model, godište, zapremina motora, snaga motora, vrsta goriva, vrsta menjača), a ti na osnovu toga pomažeš da otkrije šta nije u redu i šta da radi dalje.
            Pravila za odgovore:
                - Piši u Markdown formatu.
                - Koristi nenumerisane liste (`-`) za moguće uzroke, korake ili savete.
                - Ako imaš linkove, ubaci ih kao `[tekst](url)`.
                - Objasni stvari prosto, kao nekome ko nije majstor, ali sa dovoljno detalja da bude korisno.
                - Fokusiraj se na praktične korake: šta korisnik može sam da proveri (npr. filter, svećice) ili šta da odnese majstoru.
                - Ako nemaš dovoljno podataka, pitaj dodatna pitanja (npr. 'Kako auto vuče na leru?', 'Jel dimi iz auspuha?') i podseti korisnika da ti da više detalja o simptomima ili okolnostima. 
                - Ako je problem nejasan, podstakni korisnika da pojasni (npr. 'Daj mi još malo info, pa ćemo skontati šta je.').
                - Vodi razgovor ka rešavanju problema i sledećim koracima, uvek ostavi prostor da te pitaju još nešto.
                - Ako pitanje nije o kolima, kaži: 'To nije moj teren, ortak, pričajmo o kolima.'
                - Uključi listu osnovnih provera koje korisnik može sam da uradi, npr. 'Pogledaj nivo ulja', 'proveri akumulator'....
                - Postavi pitanja poput 'Jel imaš još nešto da dodaš o problemu?' ili 'Šta si već pokušao da središ?' ili 'Ako želiš nešto detaljnije da prodjemo, slobodno piši!' da podstakneš dalji razgovor.
                - Formiraj odgovore tako da korisnik nastavi komunikaciju sa tobom.";
    }

    /**
     * Dodaje system poruku samo jednom po sesiji (ako nije već dodata).
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
        if (!in_array($role, ['system', 'assistant', 'user'])) {
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
    public function prepareApiRequest(string $model = 'gpt-4o-mini'): array
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
            '.rs', // Ograničavamo na sve .rs domene
        ];

        // Izvuci domen iz URL-a
        $domain = parse_url($url, PHP_URL_HOST);

        // Proveri da li se domen završava na .rs
        foreach ($allowedDomains as $allowed) {
            if (str_ends_with($domain, $allowed)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pretraga delova putem Sonar API-ja (Perplexity) sa podacima o autu iz sesije
     */
    protected function searchSonar(string $query): string
    {
        if (!$this->sonarApiKey) {
            Log::warning("Sonar API key nije podešen u .env fajlu.");
            return '';
        }

        // Dohvatamo samo marka, model, godište
        $messages = $this->getMessages();
        $carDetails = '';
        foreach ($messages as $message) {
            if ($message['role'] === 'system' && strpos($message['content'], 'Automobil je:') === 0) {
                preg_match('/Automobil je: ([^,]+) ([^,]+), Godište: (\d{4})/', $message['content'], $matches);
                if (count($matches) >= 4) {
                    $carDetails = "{$matches[1]} {$matches[2]} {$matches[3]}"; // Npr. "Renault Megane 2016"
                }
                break;
            }
        }

        // Skraćeni upit
        $enhancedQuery = $carDetails ? "$carDetails $query" : $query;

        $payload = [
            'model' => 'sonar-pro',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Pretraži isključivo sajtove iz Srbije (.rs domeni) i vrati dostupne auto-delove kao listu linkova u formatu - [opis](url). Ne piši nikakav tekst, komentare ili objašnjenja. Ako nema linkova, vrati prazan string ("").'
                ],
                [
                    'role' => 'user',
                    'content' => $enhancedQuery
                ]
            ]
        ];

        $ch = curl_init($this->sonarApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->sonarApiKey
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            Log::error("Sonar API cURL error: $error");
            curl_close($ch);
            return '';
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        Log::info("Sonar API response (HTTP $httpCode): $response");

        $data = json_decode($response, true);
        if (!isset($data['choices'][0]['message']['content'])) {
            Log::warning("Sonar API response missing 'choices' or invalid format.");
            return '';
        }

        $content = $data['choices'][0]['message']['content'];
        // Formatiraj odgovor kao listu linkova
        $lines = explode("\n", $content);
        $resultLinks = [];
        foreach ($lines as $line) {
            if (preg_match('/\[(.*?)\]\((https?:\/\/[^\s]+)\)/', $line, $matches)) {
                $title = $matches[1];
                $url = $matches[2];
                if ($this->isRelevantDomain($url)) {
                    $resultLinks[] = "- $title: $url";
                }
            }
        }

        return !empty($resultLinks) ? implode("\n", $resultLinks) : '';
    }

    /**
 * Jedan mogući "wizard" metod ako hoćete session-based kontekst (diagnose, lampica, carForm).
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

    // Pripremamo upit za ChatGPT
    $payload = $this->prepareApiRequest('gpt-4o-mini');
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
            if (!empty($carForm['fuelType'])) {
                $searchQuery .= " {$carForm['fuelType']}";
            }

            $searchResults = $this->searchSonar($searchQuery);
            if (!empty($searchResults)) {
                $assistantReply = "Evo gde možeš da kupiš filter ulja za tvoj Renault Megane:\n" . $searchResults . "\n\nAko ti treba još pomoći, pitaj me slobodno!";
            } else {
                $assistantReply .= "\n\nNisam našao tačne linkove sad, ali proveri sajtove za auto-delove u Srbiji.";
            }
        }

        $this->addMessage('assistant', $assistantReply);
        return $assistantReply;
    }

    return "Ej, došlo je do greške u komunikaciji sa ChatGPT-om, probaj opet!";
}
}