<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Prikaz login forme.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Obrada login zahteva.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Netačan email ili šifra.',
        ]);
    }

    /**
     * Obrada logout zahteva.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Prikaz Forgot Password forme.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Slanje reset linka na email.
     */
    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => 'Poslali smo vam link za resetovanje lozinke na email.'])
                : back()->withErrors(['email' => __($status)]);
}

    /**
     * Prikaz Reset Password forme.
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Resetovanje lozinke.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',      // Minimum jedno malo slovo
                'regex:/[A-Z]/',      // Minimum jedno veliko slovo
                'regex:/[0-9]/',      // Minimum jedan broj
            ],
        ], [
            'password.regex' => 'Šifra mora sadržati najmanje jedno veliko slovo, jedno malo slovo i jedan broj.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('dashboard')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
