<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestFlowController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;
use LemonSqueezy\Laravel\Http\Controllers\WebhookController;
use App\Http\Controllers\ServiceBookController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\AdvisorChatController;
use App\Http\Controllers\AdvisorHistoryController;
use App\Http\Controllers\GuestAdvisorController;  // NOVO: Kontroler za guest savetnika


// Početna ruta za sve – vraća home.blade.php
Route::get('/', [HomeController::class, 'index'])->name('home');

// ✅ GOST RUTE (Unos problema, registracija)
Route::group([], function () {
    Route::get('dijagnoza-form', [GuestFlowController::class, 'showWizardForm'])->name('guest.wizard-form');
    Route::post('/wizard-form', [GuestFlowController::class, 'storeTempData'])->name('guest.store-temp-data');
    Route::post('/submit-temp-data', [GuestFlowController::class, 'storeTempData'])->name('guest.submit-temp-data');

    Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegistrationController::class, 'register'])->name('register.submit');

    Route::get('/verify-notice', [RegistrationController::class, 'verifyNotice'])->name('verify.notice');
    Route::get('/verify', [RegistrationController::class, 'verifyEmail'])->name('verify.email');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');

// ✅ KORISNIČKE RUTE (Profil, garaža, istorija, ocene)
Route::middleware(['auth'])->group(function () {

    Route::post('/store-question', [ChatController::class, 'storeQuestion'])->name('chat.storeQuestion');
    Route::get('/new-chat', [ChatController::class, 'newChat'])->name('chat.new');

    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/my-data', [ProfileController::class, 'showProfile'])->name('profile.my-data');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'deleteProfile'])->name('profile.delete');

    // ✅ Moja garaža
    Route::get('/profile/garage', [ProfileController::class, 'showGarage'])->name('profile.garage');
    Route::get('/profile/garage/{car}/edit', [ProfileController::class, 'editCar'])->name('profile.garage.edit');
    Route::put('/profile/garage/{car}', [ProfileController::class, 'updateCar'])->name('profile.garage.update');
    Route::delete('/profile/garage/{car}', [ProfileController::class, 'deleteCar'])->name('profile.garage.delete');

    // ✅ Istorija chatova
    Route::get('/profile/history', [ProfileController::class, 'showHistory'])->name('profile.history');
    Route::get('/profile/history/{chat}', [ProfileController::class, 'showArchivedChat'])->name('profile.history.chat');

    // ✅ Oceni aplikaciju
    Route::get('/profile/rate', [ProfileController::class, 'showRateForm'])->name('profile.showRateForm');
    Route::post('/profile/rate', [ProfileController::class, 'rateApp'])->name('profile.rate');

    // ✅ Pretplata
    Route::get('/subscription', [ProfileController::class, 'subscription'])->name('profile.subscription');
});

//PAYMENT
// Webhook ruta za Lemon Squeezy
Route::middleware('auth')->get('/buy', function (Request $request) {
    $checkoutBasic = $request->user()->checkout('714137');
    $checkoutPro   = $request->user()->checkout('714199');

    return view('billing', compact('checkoutBasic', 'checkoutPro'));
})->name('buy');

// ✅ SUPPORT & FAQ
Route::middleware(['auth'])->group(function () {
    Route::get('/support', [SupportController::class, 'showSupportForm'])->name('support');
    Route::post('/support', [SupportController::class, 'submitSupportForm'])->name('support.submit');
});
Route::get('/faq', [SupportController::class, 'showFAQ'])->name('faq');

// ✅ PRAVILA & POLITIKA PRIVATNOSTI
Route::get('/terms', function () {
    return view('auth.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('auth.privacy');
})->name('privacy');

// ✅ SPECIJALNE RUTE ZA REGISTROVANE KORISNIKE
Route::middleware(['auth'])->group(function () {
    Route::get('/wizard-form-registered', [\App\Http\Controllers\RegisteredWizardController::class, 'showForm'])
        ->name('registered.wizard-form');

    Route::post('/wizard-form-registered', [\App\Http\Controllers\RegisteredWizardController::class, 'storeData'])
        ->name('registered.store-data');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/service-book', [ServiceBookController::class, 'index'])->name('service-book.index');
    Route::get('/service-book/{car_id}/create', [ServiceBookController::class, 'create'])->name('service-book.create');
    Route::post('/service-book/store', [ServiceBookController::class, 'store'])->name('service-book.store');
    Route::get('/service-book/{car_id}/export', [ServiceBookController::class, 'exportPdf'])->name('service-book.export');
    Route::delete('/service-record/{id}', [ServiceBookController::class, 'destroy'])->name('service-record.destroy');
    Route::get('/service-book/{car_id}/records', [ServiceBookController::class, 'show'])->name('service-book.show');
});


/* --------------------------------------------------------------
 |  POLOVNJACI (advisor) – sve unutar prefixa /advisor
 |-------------------------------------------------------------- */
Route::prefix('advisor')->name('advisor.')->group(function () {

    /* javni landing */
    Route::get('/', [AdvisorController::class, 'landing'])->name('landing');

    /* sve ispod zahtijeva login */
    Route::middleware('auth')->group(function () {

        /* wizard */
        Route::get ('/wizard', [AdvisorController::class, 'showWizard'])->name('wizard');
        Route::post('/wizard', [AdvisorController::class, 'storeVehicle'])->name('wizard.store');
        Route::post('/wizard/start', [AdvisorController::class, 'startChat'])->name('wizard.start');
        Route::post('/wizard/clear', [AdvisorController::class, 'clearVehicles'])->name('wizard.clear');

        /* aktivni chat */
        Route::get ('/chat/{purchaseChat}', [AdvisorChatController::class, 'show'])->name('chat');
        Route::post('/chat/{purchaseChat}/message', [AdvisorChatController::class, 'store'])->name('chat.message');
        Route::post('/chat/{purchaseChat}/archive', [AdvisorChatController::class, 'archive'])->name('chat.archive');
        Route::get ('/chat/{purchaseChat}/export', [AdvisorChatController::class, 'export'])->name('chat.export');

        /* prečica iz side‑bara */
        Route::get('/chat-or-wizard', [AdvisorController::class, 'chatOrWizard'])->name('chatOrWizard');

        /* istorija (arhiva) */
        Route::get('/history', [AdvisorHistoryController::class, 'index'])->name('history');
        Route::get('/history/{purchaseChat}', [AdvisorHistoryController::class, 'show'])->name('history.show');
    });
});


/* --------------------------------------------------------------
 |  GOST SAJETNIK (guest advisor) – unutar prefiksa /advisor, bez auth middleware
 |-------------------------------------------------------------- */
Route::prefix('advisor')->name('advisor.guest.')->group(function () {
    Route::get('/guest-wizard', [GuestAdvisorController::class, 'showWizard'])->name('wizard');
    Route::post('/guest-wizard', [GuestAdvisorController::class, 'storeVehicle'])->name('wizard.store');
    Route::post('/guest-wizard/clear', [GuestAdvisorController::class, 'clearVehicles'])->name('wizard.clear');
    Route::post('/guest-wizard/start', [GuestAdvisorController::class, 'startChat'])->name('wizard.start');
    Route::get('/guest/chat/{chatId}', [GuestAdvisorController::class, 'showChat'])->name('chat');
});
