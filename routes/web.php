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
use App\Http\Controllers\ProfileController;

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
    
    $name = $request->input('name');
    $email = $request->input('email');
    $subject = $request->input('subject');
    $userMessage = $request->input('message');
    
    $ip = $request->ip();
    $date = now()->timezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i');

    $bodyHtml = '
<div style="font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; background-color: #f4f7fa; padding: 40px 20px; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid rgba(0,0,0,0.03);">
        <div style="background-color: #033D60; padding: 30px 20px; text-align: center; color: #ffffff;">
            <h2 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 700; text-align: center;">
                📬 Nova Mensagem de Contato
            </h2>
            <p style="margin: 0; font-size: 14px; color: rgba(255, 255, 255, 0.8);">Recebida pelo formulário da landing page SIBEM</p>
        </div>
        <div style="padding: 30px 25px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">Nome</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 12px 16px; border-radius: 4px; font-size: 14px; color: #222;">
                    ' . e($name) . '
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">E-mail</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 12px 16px; border-radius: 4px; font-size: 14px; color: #222;">
                    ' . e($email) . '
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">Assunto</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 12px 16px; border-radius: 4px; font-size: 14px; color: #222;">
                    ' . e($subject) . '
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">Mensagem</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 16px; border-radius: 4px; font-size: 14px; color: #222; line-height: 1.6;">
                    ' . nl2br(e($userMessage)) . '
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">IP de Origem</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 12px 16px; border-radius: 4px; font-size: 14px; color: #222;">
                    ' . e($ip) . '
                </div>
            </div>
            <div style="margin-bottom: 10px;">
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: 0.5px;">Recebido Em</label>
                <div style="background: #f8f9fa; border-left: 4px solid #033D60; padding: 12px 16px; border-radius: 4px; font-size: 14px; color: #222;">
                    ' . e($date) . '
                </div>
            </div>
        </div>
        <div style="background-color: #fafafa; padding: 25px 20px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #f0f0f0; line-height: 1.6;">
            Este e-mail foi gerado automaticamente pelo <strong>SIBEM</strong>.<br>
            Para responder, basta responder a este e-mail — o destinatário será <strong style="color: #033D60;">' . e($email) . '</strong>.
        </div>
    </div>
</div>';

    try {
        \Illuminate\Support\Facades\Mail::html($bodyHtml, function ($message) use ($subject, $email, $name) {
            $message->to('contato@sibem.top')
                    ->subject("Contato SIBEM: " . $subject)
                    ->replyTo($email, $name);
        });
    } catch (\Exception $e) {
        logger()->error("Erro ao enviar e-mail de contato: " . $e->getMessage());
    }

    return 'OK';
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

    // Perfil do Usuário
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

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
        
        Route::post('tokens/{token}/enviar-email', [TokenController::class, 'sendEmail'])->name('tokens.send-email');
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
