<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<div class="border-orange bg-black radius h-full main-child shadow-lg">
    <h2 class="text-2xl font-black text-orange my-8 page-title text-center">Zaboravljena Šifra</h2>
    <p>Unesite email adresu preko koje ste se registrovali da bismo vam poslali link za zamenu šifre</p>
    
    @if (session('status'))
        <div class="mb-4 text-orange text-center animate-fadeIn font-black uppercase mt-4">
            {{ __('passwords.sent') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="auth-form-min">
        @csrf
        <div class="my-8">
            <input type="email" name="email" placeholder="Unesite Vašu Email adresu" id="email" value="{{ old('email') }}" required
                   class="input-field bg-gray-700">
        </div>
        <div class="text-center">
            <button type="submit" class="btn-orange mb-8 w-full text-black hover:bg-orange-500 mt-2">
                Pošalji Reset Link
            </button>
        </div>
    </form>
</div>
@endsection