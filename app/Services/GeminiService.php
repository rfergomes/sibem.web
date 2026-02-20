<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
    }

    /**
     * Get daily Bible data (verse + devotional).
     * Caches the result for 24 hours.
     *
     * @return array
     */
    public function getDailyData(): array
    {
        // Cache key based on current date (Y-m-d) so it changes at midnight
        $today = now()->format('Y-m-d');
        $cacheKey = "daily_word_data_{$today}";

        return Cache::remember($cacheKey, now()->endOfDay(), function () {
            return $this->fetchDailyDataFromApi();
        });
    }

    /**
     * Fetch the verse and devotional from Gemini API.
     *
     * @return array
     */
    protected function fetchDailyDataFromApi(): array
    {
        $default = [
            'verse' => "Lâmpada para os meus pés é tua palavra, e luz para o meu caminho.",
            'reference' => "Salmos 119:105", // React app splits this
            'reflection' => "A Palavra de Deus é o guia seguro em meio às trevas deste mundo, iluminando cada passo de nossa jornada.",
            'prayer' => "Senhor, guia meus passos conforme a Tua vontade e que a Tua Palavra seja sempre a luz que orienta minhas decisões. Amém.",
            'application' => "Dedique um tempo hoje para meditar em uma promessa bíblica e permita que ela direcione suas atitudes.",
            'curiosity' => "Este é um dos versículos mais conhecidos do Salmo 119, o capítulo mais longo da Bíblia."
        ];

        if (empty($this->apiKey)) {
            Log::warning('GEMINI_API_KEY not set.');
            return $default;
        }

        try {
            // Prompt adapted from React App (Palavra_do_Dia)
            $prompt = 'Escolha um versículo bíblico aleatório em Português (NVI ou Almeida) de encorajamento. ' .
                'Analise o versículo e forneça: ' .
                '1. Uma reflexão profunda sobre o significado teológico. ' .
                '2. Uma oração curta e inspiradora. ' .
                '3. Uma aplicação prática para a vida moderna. ' .
                '4. Uma curiosidade bíblica interessante (fato histórico, significado original, contexto). ' .
                'Retorne um JSON com os campos: "verse" (texto do versículo), "reference" (livro capítulo:versículo), ' .
                '"reflection", "prayer", "application", "curiosity". ' .
                'Evite versículos muito comuns como João 3:16. Explore livros menos conhecidos também.';

            $response = Http::post($this->baseUrl . 'gemini-1.5-flash:generateContent?key=' . $this->apiKey, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 1.0,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $jsonText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($jsonText) {
                    $decoded = json_decode($jsonText, true);
                    // Ensure all keys exist
                    if (isset($decoded['verse']) && isset($decoded['reflection'])) {
                        // Fix potential key mismatch from React app (texto -> verse, referencia -> reference)
                        return [
                            'verse' => $decoded['verse'] ?? $decoded['texto'] ?? $default['verse'],
                            'reference' => $decoded['reference'] ?? $decoded['referencia'] ?? $default['reference'],
                            'reflection' => $decoded['reflection'] ?? $default['reflection'],
                            'prayer' => $decoded['prayer'] ?? $default['prayer'],
                            'application' => $decoded['application'] ?? $default['application'],
                            'curiosity' => $decoded['curiosity'] ?? $default['curiosity'],
                        ];
                    }
                }
            }

            Log::error('Gemini API Error: ' . $response->status() . ' - ' . $response->body());
            return $default;

        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Legacy method for backward compatibility
     */
    public function getDailyVerse(): string
    {
        $data = $this->getDailyData();
        return $data['verse'];
    }
}
