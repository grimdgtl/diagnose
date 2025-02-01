<!-- resources/views/auth/register.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-black rounded-lg mt-12 shadow-lg">
    <h1 class="text-3xl text-orange font-bold my-8 text-center">Registracija</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-500">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="px-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ime -->
            <div>
                <input type="text" name="first_name" placeholder="Ime" value="{{ old('first_name') }}"
                       class="input-field bg-gray-700" required>
            </div>
            <!-- Prezime -->
            <div>
                <input type="text" name="last_name" placeholder="Prezime" value="{{ old('last_name') }}"
                       class="input-field bg-gray-700" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
            <!-- Email -->
            <div>
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                       class="input-field bg-gray-700" required>
            </div>
            <!-- Telefon -->
            <div>
                <input type="number" name="phone" placeholder="Broj telefona (opciono)" value="{{ old('phone') }}"
                       class="input-field bg-gray-700">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
            <!-- Password -->
            <div>
                <input type="password" name="password" placeholder="Šifra"
                       class="input-field bg-gray-700" required>
            </div>
            <!-- Confirm Password -->
            <div>
                <input type="password" name="password_confirmation" placeholder="Potvrdite šifru"
                       class="input-field bg-gray-700" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
            <!-- Grad -->
            <div>
                <input type="text" name="city" placeholder="Grad" value="{{ old('city') }}"
                       class="input-field bg-gray-700" required>
            </div>
            <!-- Država -->
            <div>
                <input type="text" name="country" placeholder="Država" value="{{ old('country') }}"
                       class="input-field bg-gray-700" required>
            </div>
        </div>

        <!-- Uslovi korišćenja -->
        <div class="mt-2 text-center">
            <label class="inline-flex items-center">
                <input type="checkbox" name="terms" required class="form-checkbox text-orange mr-2">
                <span class="text-sm ">
                    Slažem se sa
                    <a href="{{ route('terms') }}" class="text-orange underline">Uslovima korišćenja</a> i
                    <a href="{{ route('privacy') }}" class="text-orange underline">Politikom privatnosti</a>.
                </span>
            </label>
        </div>
        <div class="text-center">
            <button type="submit" class="btn-orange w-full my-6 mx-auto text-black hover:bg-orange-500">
            Registruj se
            </button>
        </div>
    </form>
</div>
@endsection
