<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestFlowController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;

// ✅ GOST RUTE (Unos problema, registracija)
Route::group([], function () {
    Route::get('/', [GuestFlowController::class, 'showWizardForm'])->name('guest.wizard-form');
    Route::post('/wizard-form', [GuestFlowController::class, 'storeTempData'])->name('guest.store-temp-data');
    Route::post('/submit-temp-data', [GuestFlowController::class, 'storeTempData'])->name('guest.submit-temp-data');

    Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegistrationController::class, 'register'])->name('register');

    Route::get('/verify-notice', [RegistrationController::class, 'verifyNotice'])->name('verify.notice');
    Route::get('/verify', [RegistrationController::class, 'verifyEmail'])->name('verify.email');

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// ✅ KORISNIČKE RUTE (Profil, garaža, istorija, ocene)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');

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

// ✅ PLAĆANJE (Paketi, checkout, webhook)
Route::middleware(['auth'])->group(function () {
    Route::get('/plans', [PaymentController::class, 'showPlans'])->name('plans.show');
    Route::post('/plans/buy', [PaymentController::class, 'buyPlan'])->name('plans.buy');

    Route::post('/create-checkout', [PaymentController::class, 'createCheckout'])->name('payment.create');
    Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment-cancel', function () {
        return redirect()->route('home')->with('error', 'Payment canceled.');
    })->name('payment.cancel');

    Route::get('/plans/thank-you', [PaymentController::class, 'thankYou'])->name('plans.thank-you');
    Route::get('/plans/cancel', function () {
        return view('payment.cancel');
    })->name('plans.cancel');

    Route::get('/payment/redirect-to-checkout', [PaymentController::class, 'redirectToLemonSqueezyCheckout'])->name('payment.redirectToCheckout');
});

// ✅ WEBHOOK za Lemon Squeezy (bez CSRF zaštite)
Route::post('/webhook', [PaymentController::class, 'handleWebhook'])
    ->name('payment.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

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
