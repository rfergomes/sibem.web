<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesktopApiController;

// Endpoint for the Desktop application to submit access token requests
Route::post('/desktop/solicitar-token', [DesktopApiController::class, 'storeSolicitacao']);
