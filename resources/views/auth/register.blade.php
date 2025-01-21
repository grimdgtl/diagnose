<!-- resources/views/auth/register.blade.php -->
@extends('layouts.app')

@section('content')
<div class="mx-auto p-6 bg-gray-800 rounded-lg">
    <h1 class="text-3xl text-orange page-title font-bold mb-4">Registracija</h1>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="auth-form">
        @csrf
        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mt-6">
            <!-- First Name -->
            <div class="mb-4">
                <input type="text" name="first_name" id="first_name" class="input-field" placeholder="Ime" value="{{ old('first_name') }}" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
            <!-- Last Name -->
            <div>
                <input type="text" name="last_name" id="last_name" class="input-field" placeholder="Prezime" value="{{ old('last_name') }}" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mt-6">
            <!-- Email -->
            <div>
                <input type="email" name="email" id="email" class="input-field" placeholder="Email" value="{{ old('email') }}" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
            <div>
                <input type="number" name="phone" id="phone" class="input-field" placeholder="Broj telefona (Opciono)" value="{{ old('phone') }}"
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mt-6">
            <!-- Password -->
            <div>
                <input type="password" name="password" id="password" class="input-field" placeholder="Šifra" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
            <!-- Confirm Password -->
            <div>
                <input type="password" name="password_confirmation" id="password_confirmation" class="input-field" placeholder="Potvrdite šifru" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mt-6">
            <!-- City -->
            <div>
                <input type="text" name="city" id="city" class="input-field" placeholder="Grad" value="{{ old('city') }}" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
            <!-- Country -->
            <div>
                <input type="text" name="country" id="country" class="input-field" placeholder="Država" value="{{ old('country') }}" required
                       class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="terms" required class="form-checkbox text-orange">
                <span class="ml-2">Slažem se sa <a href="{{ route('terms') }}" class="text-orange underline">Uslovima korišćenja</a> i <a href="{{ route('privacy') }}" class="text-orange underline">Politikom privatnosti</a>.</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full btn-orange text-black p-2 rounded hover:bg-orange-500">
                Registruj se
            </button>
        </div>
    </form>
</div>
@endsection
