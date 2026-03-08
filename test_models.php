<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prompt = 'hi';
$baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';
$apiKey = config('services.gemini.key');

$models = ['gemini-1.5-flash', 'gemini-1.5-flash-latest', 'gemini-2.0-flash', 'gemini-2.5-flash'];

foreach ($models as $model) {
    try {
        $response = Illuminate\Support\Facades\Http::post($baseUrl . $model . ':generateContent?key=' . $apiKey, [
            'contents' => [['parts' => [['text' => $prompt]]]]
        ]);
        echo $model . " => " . $response->status() . "\n";
    } catch (\Exception $e) {
        echo $model . " => ERROR\n";
    }
}
