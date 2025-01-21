<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-gray-800 rounded-lg">
    <h2 class="text-2xl font-bold text-orange mb-4">Prijava</h2>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Šifra</label>
            <input type="password" name="password" id="password" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="remember" class="form-checkbox text-orange" {{ old('remember') ? 'checked' : '' }}>
                <span class="ml-2">Zapamti me</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <button type="submit" class="w-full bg-orange text-black p-2 rounded hover:bg-orange-500">
                Prijavi se
            </button>
        </div>

        <!-- Forgot Password Link -->
        <div class="text-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-orange underline">
                    Zaboravljena šifra?
                </a>
            @endif
        </div>
    </form>
</div>
@endsection
