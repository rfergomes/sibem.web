<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenantSelectionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\Admin\TokenRequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\Admin\RegionalController;
use App\Http\Controllers\Admin\LocalController;
use App\Http\Controllers\Admin\SetorController;
use App\Http\Controllers\Admin\DependenciaController;
use App\Http\Controllers\Admin\IgrejaController;
use App\Http\Controllers\Admin\TipoImovelController;

// Public Landing Page
Route::get('/', function () {
    try {
        $users = \App\Models\User::count();
        $regionais = \App\Models\Regional::count();
        $locais = \App\Models\Local::count();
        $igrejas = \App\Models\Igreja::count();
    } catch (\Exception $e) {
        $users = 0;
        $regionais = 0;
        $locais = 0;
        $igrejas = 0;
    }

    return view('landing', compact('users', 'regionais', 'locais', 'igrejas'));
})->name('landing');

// Public Contact Form Submission
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    logger()->info("Contato recebido: " . json_encode($request->all()));
    return response()->json(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
})->name('contact.store');

// Public Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dynamic Tenant Chaveamento (Selection)
    Route::post('/tenant/select', [TenantSelectionController::class, 'select'])->name('tenant.select');

    // Inventórios Realizados (View of Tenant inventories)
    Route::get('/inventarios/concluidos', [InventarioController::class, 'concluidos'])->name('inventarios.concluidos');

    // Admin Group: User Management and Base cadastros
    Route::prefix('admin')->name('admin.')->group(function () {
        // Token Requests
        Route::get('/solicitacoes', [TokenRequestController::class, 'index'])->name('token-requests.index');
        Route::post('/solicitacoes/{id}/approve', [TokenRequestController::class, 'approve'])->name('token-requests.approve');
        Route::post('/solicitacoes/{id}/reject', [TokenRequestController::class, 'reject'])->name('token-requests.reject');

        // CRUDs
        Route::resource('usuarios', UserController::class)->names('usuarios');
        Route::post('usuarios/{usuario}/tokens/gerar', [UserController::class, 'generateToken'])->name('usuarios.tokens.gerar');
        
        Route::resource('tokens', TokenController::class)->names('tokens');
        Route::resource('regionais', RegionalController::class)->names('regionais');
        Route::resource('locais', LocalController::class)->names('locais');
        Route::resource('setores', SetorController::class)->names('setores');
        Route::resource('dependencias', DependenciaController::class)->names('dependencias');
        Route::resource('igrejas', IgrejaController::class)->names('igrejas');
        Route::resource('tipos-imovel', TipoImovelController::class)->names('tipos-imovel');
    });
});

// Public route for distributing the desktop installer (ClickOnce / VS 2022)
Route::get('/app/{path}', function ($path) {
    $filePath = public_path('app/' . $path);
    
    if (!file_exists($filePath) || is_dir($filePath)) {
        abort(404);
    }
    
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeType = match ($extension) {
        'application' => 'application/x-ms-application',
        'manifest'    => 'application/x-ms-manifest',
        'deploy'      => 'application/octet-stream',
        'msi'         => 'application/octet-stream',
        'msp'         => 'application/octet-stream',
        'exe'         => 'application/octet-stream',
        default       => (function_exists('mime_content_type') ? @mime_content_type($filePath) : null) ?: 'application/octet-stream'
    };
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->where('path', '.*')->name('desktop.update.files');
