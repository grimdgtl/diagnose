<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
<div class="border-orange bg-black radius h-full main-child shadow-lg">
    <h2 class="text-2xl page-title font-black text-orange mb-4 text-center">Prijavi se</h2>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="auth-form-min">
        @csrf
        <div class="my-8">
            <input type="email" name="email" placeholder="Email" id="email" value="{{ old('email') }}" required
                   class="input-field bg-gray-700">
        </div>

        <div class="mt-8 mb-2 relative">
            <input type="password" name="password" placeholder="Šifra" id="password" required class="input-field bg-gray-700">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer toggle-password">
                <svg class="w-5 h-5 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </span>
        </div>


        <div class="mb-4 flex items-center justify-center">
            <input type="checkbox" name="remember" class="form-checkbox text-orange mr-2"
                   {{ old('remember') ? 'checked' : '' }}>
            <span class="text-sm">Zapamti me</span>
        </div>
        <div class="mb-4 mt-8 flex items-center justify-center">
            <button type="submit" class="btn-orange w-full text-black hover:bg-orange-500">
            Prijavi se
            </button>
        </div>

        <div class="text-center mt-4">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-orange underline text-sm">
                    Zaboravljena šifra?
                </a>
            @endif
        </div>
    </form>
</div>


@endsection
