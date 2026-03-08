<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.key');
if (!$apiKey) {
    echo "API Key is missing!\n";
} else {
    echo "API Key is set (length " . strlen($apiKey) . ")\n";
}

$prompt = 'Escolha um versículo bíblico aleatório em Português (NVI ou Almeida) de encorajamento. ' .
    'Analise o versículo e forneça: ' .
    '1. Uma reflexão profunda sobre o significado teológico. ' .
    '2. Uma oração curta e inspiradora. ' .
    '3. Uma aplicação prática para a vida moderna. ' .
    '4. Uma curiosidade bíblica interessante (fato histórico, significado original, contexto). ' .
    'Retorne um JSON com os campos: "verse" (texto do versículo), "reference" (livro capítulo:versículo), ' .
    '"reflection", "prayer", "application", "curiosity". ' .
    'Evite versículos muito comuns como João 3:16. Explore livros menos conhecidos também.';

$baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
$response = Illuminate\Support\Facades\Http::post($baseUrl . 'gemini-1.5-flash-latest:generateContent?key=' . $apiKey, [
    'contents' => [
        ['parts' => [['text' => $prompt]]]
    ],
    'generationConfig' => [
        'temperature' => 1.0,
        'responseMimeType' => 'application/json',
    ]
]);

echo "Status Code: " . $response->status() . "\n";
echo "Response Body: \n";
echo $response->body() . "\n";
