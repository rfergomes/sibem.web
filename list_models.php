<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey = config('services.gemini.key');
$response = Illuminate\Support\Facades\Http::get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey);
$data = $response->json();
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (str_contains($model['name'], 'flash') || str_contains($model['name'], 'gemini-1.5')) {
            echo $model['name'] . "\n";
        }
    }
}
