<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Webhook para Automação do GitHub Deploy
Route::any('/deploy_webhook_secreto123', function (Request $request) {
    // Validação extra de segurança garantindo que vem do GitHub (comentado para testes via navegador)
    /*
    if (!$request->hasHeader('X-GitHub-Event')) {
        return response()->json(['error' => 'Acesso negado'], 403);
    }
    */

    $repo_dir = base_path();
    chdir($repo_dir);

    // Executa o git pull no lado do servidor
    $output = shell_exec('git pull origin master 2>&1');

    return response()->json([
        'success' => true,
        'message' => 'Deploy acionado com sucesso',
        'output' => $output
    ]);
});
