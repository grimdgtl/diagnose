<!-- resources/views/auth/reset-password.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-gray-800 rounded-lg">
    <h2 class="text-2xl font-bold text-orange mb-4">Resetovanje Šifre</h2>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Nova Šifra</label>
            <input type="password" name="password" id="password" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium">Potvrdi Šifru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-orange text-black p-2 rounded hover:bg-orange-500">
                Resetuj Šifru
            </button>
        </div>
    </form>
</div>
@endsection
