<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$gemini = app(\App\Services\GeminiService::class);
Cache::forget('daily_word_data_' . now()->format('Y-m-d'));
$data = $gemini->getDailyData();
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
