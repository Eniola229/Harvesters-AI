<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\ChurchInfoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\ProgramsController;
use App\Http\Controllers\Webhook\TwilioWebhookController;
use Illuminate\Support\Facades\Route;

// ============================================================
// TWILIO WEBHOOK (no CSRF)
// ============================================================
Route::post('/api/whatsapp/webhook', [TwilioWebhookController::class, 'handleIncoming'])
    ->name('webhook.twilio')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// ============================================================
// ADMIN AUTH
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest admin routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    // Authenticated admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Members
        Route::resource('members', MembersController::class);

        // Programs
        Route::resource('programs', ProgramsController::class);

        // Newsletters
        Route::resource('newsletters', NewsletterController::class)->except(['show', 'edit', 'update']);
        Route::post('newsletters/{newsletter}/send', [NewsletterController::class, 'send'])->name('newsletters.send');

        // Campuses
        Route::resource('campuses', CampusController::class);

        // Leaders
        Route::resource('leaders', LeaderController::class);

        // Church Info / Knowledge Base
        Route::resource('church-info', ChurchInfoController::class);
    });
});