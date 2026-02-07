<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.update');

// Access Request Routes (Guest)
Route::get('/solicitar-acesso', [App\Http\Controllers\AccessRequestController::class, 'create'])->name('access-request.create');
Route::post('/solicitar-acesso', [App\Http\Controllers\AccessRequestController::class, 'store'])->name('access-request.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        // Retrieve stats for the view if needed, or move to Controller
        // For now, view is static-ish
        return view('dashboard');
    })->name('dashboard');

    // Switch Administration Routes
    Route::get('/api/switch-options', [App\Http\Controllers\SwitchAdmController::class, 'list'])->name('adm.list');
    Route::post('/api/switch', [App\Http\Controllers\SwitchAdmController::class, 'switch'])->name('adm.switch');

    // Tenant Routes (Protected by TenancyMiddleware implicit in 'web' group if session exists, but good to ensure validity)
    Route::resource('inventarios', App\Http\Controllers\InventoryController::class);
    Route::post('/inventarios/{id}/finalize', [App\Http\Controllers\InventoryController::class, 'finalize'])->name('inventarios.finalize');
    Route::get('/inventarios/{id}/print', [App\Http\Controllers\InventoryController::class, 'printReport'])->name('inventarios.print');
    Route::get('/inventarios/{id}/custom-report', [App\Http\Controllers\InventoryController::class, 'customReport'])->name('inventarios.custom_report');

    // Scanning Routes
    Route::get('/inventarios/{id}/conferencia', [App\Http\Controllers\ScanningController::class, 'show'])->name('scan.show');
    Route::post('/inventarios/{id}/scan', [App\Http\Controllers\ScanningController::class, 'process'])->name('scan.process');
    Route::post('/inventarios/{id}/tratativa', [App\Http\Controllers\ScanningController::class, 'saveTratativa'])->name('scan.tratativa');
    Route::get('/inventarios/{id}/search-description', [App\Http\Controllers\ScanningController::class, 'searchByDescription'])->name('scan.search_description');

    // Bens & Import Routes
    Route::resource('bens', App\Http\Controllers\BemController::class);
    Route::get('/bens-import', [App\Http\Controllers\BemController::class, 'showImportForm'])->name('bens.import');
    Route::post('/bens-import', [App\Http\Controllers\BemController::class, 'import'])->name('bens.import.post');

    // SIGA Reports Routes (Section 14)
    Route::get('/report/14-3/{detalheId}', [App\Http\Controllers\ReportController::class, 'generate143'])->name('report.14-3');
    Route::get('/report/14-4/{detalheId}', [App\Http\Controllers\ReportController::class, 'generate144'])->name('report.14-4');
    Route::get('/report/14-5/{inventarioId}', [App\Http\Controllers\ReportController::class, 'generate145'])->name('report.14-5');
    Route::get('/report/14-6/{detalheId}', [App\Http\Controllers\ReportController::class, 'generate146'])->name('report.14-6');
    Route::get('/report/14-7/{detalheId}', [App\Http\Controllers\ReportController::class, 'generate147'])->name('report.14-7');
    Route::get('/report/14-8/{inventarioId}', [App\Http\Controllers\ReportController::class, 'generate148'])->name('report.14-8');

    // Profile Routes (Access for all authenticated users)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Force Password Change Routes
    Route::get('/password/change', [App\Http\Controllers\Auth\ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])->name('password.update_changed');

    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        // Access Requests Admin Management
        Route::get('/admin/solicitacoes', [App\Http\Controllers\AccessRequestController::class, 'index'])->name('admin.access-requests.index');
        Route::post('/admin/solicitacoes/{id}/approve', [App\Http\Controllers\AccessRequestController::class, 'approve'])->name('admin.access-requests.approve');
        Route::post('/admin/solicitacoes/{id}/reject', [App\Http\Controllers\AccessRequestController::class, 'reject'])->name('admin.access-requests.reject');

        // User Management Routes
        Route::resource('users', App\Http\Controllers\UserController::class);

        // Administration Management Routes
        Route::resource('admin/locais', App\Http\Controllers\LocalController::class)
            ->parameters(['locais' => 'local'])
            ->names('locais');
        Route::post('/admin/locais/{local}/provision', [App\Http\Controllers\LocalController::class, 'provision'])->name('locais.provision');

        // Administration Switching
        Route::post('/admin/switch-local', [App\Http\Controllers\LocalSwitchController::class, 'switch'])->name('admin.switch-local');
    });
});
