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

        if (!Session::has($this->sessionMessagesKey)) {
            Session::put($this->sessionMessagesKey, []);
        }
    }

    public function setSystemMessageOnce(string $message)
    {
        if (!Session::has($this->systemMessageSentKey)) {
            $this->addMessage('system', $message);
            Session::put($this->systemMessageSentKey, true);
            Log::info("System message initialized and added to chat session.");
        }
    }

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

    public function resetMessages(): void
    {
        Session::forget($this->sessionMessagesKey);
        Session::forget($this->systemMessageSentKey);
        Session::forget($this->carFormSentKey);
        Session::forget($this->diagnoseSentKey);

        Log::info("ChatGPT session messages have been reset.");
    }

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
        }
    }

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

    public function prepareApiRequest(string $model = 'gpt-3.5-turbo'): array
    {
        $messages = $this->getMessages();
        return [
            'model'    => $model,
            'messages' => $messages,
        ];
    }

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
     * Glavni “wizard” metod ako hoćeš session-based kontekst
     * (nije obavezan).
     */
    public function handleUserQuestion(
        ?string $diagnose,
        ?string $indicatorLight,
        ?string $newQuestion,
        array $carForm = [],
        string $systemMessage = null
    ): ?string {
        if ($systemMessage) {
            $this->setSystemMessageOnce($systemMessage);
        }
        if (!empty($carForm)) {
            $this->addCarFormContext($carForm);
        }
        $this->addDiagnoseContext($diagnose, $indicatorLight);

        if ($newQuestion) {
            $this->addMessage('user', $newQuestion);
        }

        $payload = $this->prepareApiRequest('gpt-4');
        $response = $this->sendApiRequest($payload);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            $assistantReply = $response['choices'][0]['message']['content'];
            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }
        return null;
    }

    /**
     * Minimalna metoda “ask($prompt)” – 
     * kreira nov niz poruka (system + user) i poziva sendApiRequest().
     */
    public function ask(string $prompt): string
    {
        // Za primer, resetujemo stare poruke:
        $this->resetMessages();

        // Dodajemo system poruku (ako želiš):
        $this->addMessage('system', "Ti si iskusan srpski automehaničar i pišeš u prijateljskom stilu.");
        // Dodajemo user prompt:
        $this->addMessage('user', $prompt);

        // Pripremimo payload:
        $payload = $this->prepareApiRequest('gpt-4');

        // Šaljemo upit:
        $response = $this->sendApiRequest($payload);

        if ($response && isset($response['choices'][0]['message']['content'])) {
            $assistantReply = $response['choices'][0]['message']['content'];
            // Ako želiš, upiši i to u session
            $this->addMessage('assistant', $assistantReply);
            return $assistantReply;
        }

        return "Došlo je do greške pri komunikaciji sa ChatGPT-om.";
    }
}
