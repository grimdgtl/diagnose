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

        $url = sprintf(
            'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s',
            urlencode($apiKey),
            urlencode($cx),
            urlencode($query)
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

        // Uzimamo samo prva 3 rezultata
        $items = array_slice($data['items'], 0, 3);
        $resultLinks = [];
        foreach ($items as $item) {
            $link = $item['link'] ?? '';
            $title = $item['title'] ?? '';
            if ($link) {
                $resultLinks[] = "- $title: $link";
            }
        }

        return implode("\n", $resultLinks);
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
        // 1) Ako hoćete da dodate system poruku
        if ($systemMessage) {
            $this->setSystemMessageOnce($systemMessage);
        }

        // 2) Ako hoćete da dodate info o automobilu
        if (!empty($carForm)) {
            $this->addCarFormContext($carForm);
        }

        // 3) Ako hoćete da dodate dijagnostiku/lampicu, jednom po sesiji
        $this->addDiagnoseContext($diagnose, $indicatorLight);

        // 4) Korisnikovo pitanje
        if ($newQuestion) {
            $this->addMessage('user', $newQuestion);
        }

        // 5) Provera za "gde mogu da kupim..."
        $userIsAskingToBuy = $this->isAskingWhereToBuy($newQuestion);

        // 6) Pošaljemo GPT-u
        $payload = $this->prepareApiRequest('gpt-3.5-turbo');
        $response = $this->sendApiRequest($payload);

        // 7) Ako dobijemo odgovor, dopunimo ako se radi o kupovini
        if ($response && isset($response['choices'][0]['message']['content'])) {
            $assistantReply = $response['choices'][0]['message']['content'];

            if ($userIsAskingToBuy) {
                // Napravimo query za Google, dopunjen podacima o automobilu ako postoje
                $searchQuery = $newQuestion;

                // Ako imamo sve ključeve, možemo ih dodati
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
                    $assistantReply .= "\n\nTrenutno nisam uspeo da pronađem konkretne linkove. Probaj da preciziraš deo ili brend.";
                }
            }

            // 8) Dodamo asistentov odgovor u session
            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }

        // Ako nešto nije u redu
        return null;
    }

    /**
     * Uprošćeni metod za slanje pitanja GPT-u, uz opcioni carForm.
     */
    public function ask(string $prompt, array $carForm = []): string
    {
        $this->addMessage('system', 'Ti si AutoMentor – virtualni asistent .Odgovaraj u Markdown formatu, koristi listu za nabrajanje, linkove u [tekst](url) formatu i podeli pasuse praznim redovima...');
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
                    $assistantReply .= "\n\nTrenutno nisam uspeo da pronađem konkretne linkove. Probaj da preciziraš deo ili brend.";
                }
            }

            // Snimamo odgovor i vraćamo
            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }

        return "Došlo je do greške pri komunikaciji sa ChatGPT-om.";
    }
}
