<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestFlowController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SupportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// -------------------------------------------------
//  1) GOST FLOW (Unos problema i auta u temp_ tabele)
// -------------------------------------------------

// 1. GET ruta - prikazuje objedinjenu (dvostepnu) formu:
Route::get('/wizard-form', [GuestFlowController::class, 'showWizardForm'])
     ->name('guest.wizard-form');

// 2. POST ruta - prima submit i čuva sve podatke u temp_* tabelama:
Route::post('/wizard-form', [GuestFlowController::class, 'storeTempData'])
     ->name('guest.store-temp-data');

// Forma 1: Opis problema
Route::get('/', [GuestFlowController::class, 'showProblemForm'])
    ->name('guest.problem-form');

// Forma 2: Podaci o autu
Route::get('/car-form', [GuestFlowController::class, 'showCarForm'])
    ->name('guest.car-form');

// Submit podataka i poziv ChatGPT-a, čuvanje u temp_*
Route::post('/submit-temp-data', [GuestFlowController::class, 'storeTempData'])
    ->name('guest.store-temp-data');

// -------------------------------------------------
//  2) REGISTRACIJA / VERIFIKACIJA EMAIL-A
// -------------------------------------------------

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])
    ->name('register')->middleware('web');
Route::post('/register', [RegistrationController::class, 'register'])
    ->name('register');

// Stranica koja obaveštava korisnika da proveri email
Route::get('/verify-notice', [RegistrationController::class, 'verifyNotice'])
    ->name('verify.notice')->middleware('web');   

// Link iz email-a za verifikaciju
Route::get('/verify', [RegistrationController::class, 'verifyEmail'])->name('verify.email')->middleware('web');


// -------------------------------------------------
//  3) LOGIN / LOGOUT
// -------------------------------------------------

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

// (Opcionalno) Zaboravljena lozinka, reset itd.
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
    ->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
    ->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

// -------------------------------------------------
//  4) DASHBOARD (CHAT) 
// -------------------------------------------------

// Ovde prikaži chat: ako je gost -> učitaj temp_
    // ako je registrovan -> učitaj prave tabele
Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard')->middleware('web');


// Novo pitanje u aktuelnom chatu (registrovan korisnik)
Route::post('/store-question', [ChatController::class, 'storeQuestion'])
    ->name('chat.storeQuestion')
    ->middleware('auth');

// “Novi chat” -> zatvaranje starog i prebacivanje na formu za novi problem
Route::get('/new-chat', [ChatController::class, 'newChat'])
    ->name('chat.new')
    ->middleware('auth');

// -------------------------------------------------
//  5) PROFIL, GARAŽA, ISTORIJA, OCENA
// -------------------------------------------------

// Moj profil (prikaz podataka)
Route::get('/profile', [ProfileController::class, 'showProfile'])
    ->name('profile.show')
    ->middleware('auth');  

Route::get('/profile/my-data', [ProfileController::class, 'showProfile'])
    ->name('profile.my-data')
    ->middleware('auth');    

// Update profila (POST ili PUT, kako više voliš)
Route::post('/profile', [ProfileController::class, 'updateProfile'])
    ->name('profile.update')
    ->middleware('auth');

// Brisanje profila (i svih podataka)
Route::delete('/profile', [ProfileController::class, 'deleteProfile'])
    ->name('profile.delete')
    ->middleware('auth');

// -----------------
// Moja garaža
// -----------------
Route::get('/profile/garage', [ProfileController::class, 'showGarage'])
    ->name('profile.garage')
    ->middleware('auth');

Route::get('/profile/garage/{car}/edit', [ProfileController::class, 'editCar'])
    ->name('profile.garage.edit')
    ->middleware('auth');

Route::put('/profile/garage/{car}', [ProfileController::class, 'updateCar'])
    ->name('profile.garage.update')
    ->middleware('auth');

Route::delete('/profile/garage/{car}', [ProfileController::class, 'deleteCar'])
    ->name('profile.garage.delete')
    ->middleware('auth');

// -----------------
// Istorija (arhivirani chatovi)
// -----------------
Route::get('/profile/history', [ProfileController::class, 'showHistory'])
    ->name('profile.history')
    ->middleware('auth');

Route::get('/profile/history/{chat}', [ProfileController::class, 'showArchivedChat'])
    ->name('profile.history.chat')
    ->middleware('auth');

// -----------------
// Oceni aplikaciju
// -----------------
Route::get('/profile/rate', [ProfileController::class, 'showRateForm'])
    ->name('profile.showRateForm')
    ->middleware('auth');
Route::post('/profile/rate', [ProfileController::class, 'rateApp'])
    ->name('profile.rate')
    ->middleware('auth');

// -------------------------------------------------
//  6) PLAĆANJE / KUPOVINA PAKETA
// -------------------------------------------------

Route::get('/plans', [PaymentController::class, 'showPlans'])
    ->name('plans.show')
    ->middleware('auth');

Route::post('/plans/buy', [PaymentController::class, 'buyPlan'])
    ->name('plans.buy')
    ->middleware('auth');

// Webhook (obično public endpoint, bez auth middlewara),
    // ali zahteva verifikaciju potpisa od strane LemonSqueezy / Stripe
Route::post('/webhook', [PaymentController::class, 'webhook'])
    ->name('payment.webhook');

Route::get('/plans/thank-you', [PaymentController::class, 'thankYou'])
    ->name('plans.thank-you')
    ->middleware('auth');

Route::get('/terms', function () {
    return view('auth.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('auth.privacy');
})->name('privacy');

// Subscription Routes
Route::get('/subscription', [ProfileController::class, 'subscription'])
    ->name('profile.subscription')
    ->middleware('auth');

// Support Routes
Route::get('/support', [SupportController::class, 'showSupportForm'])
    ->name('support')
    ->middleware('auth');
Route::post('/support', [SupportController::class, 'submitSupportForm'])
    ->name('support.submit')
    ->middleware('auth');

// FAQ Route
Route::get('/faq', [SupportController::class, 'showFAQ'])
    ->name('faq');

Route::get('/test-email', function() {
    try {
        \Illuminate\Support\Facades\Mail::raw('Ovo je test poruka', function($msg) {
            $msg->to('nikola.sandbox@gmail.com');
            $msg->subject('Test email');
        });
        return 'Poslato!';
    } catch (\Exception $e) {
        return 'Greška: '.$e->getMessage();
    }
});
