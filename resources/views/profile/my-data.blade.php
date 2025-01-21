@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-4">
    <h1 class="text-2xl font-bold text-orange mb-4">Moji podaci</h1>

    {{-- Ispis informacija o user-u --}}
    <div class="mb-4">
        <p><strong>Ime:</strong> {{ $user->first_name }}</p>
        <p><strong>Prezime:</strong> {{ $user->last_name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Grad:</strong> {{ $user->city }}</p>
        <p><strong>Država:</strong> {{ $user->country }}</p>
    </div>

    {{-- Forma za izmene (ako želiš) --}}
    <form action="{{ route('profile.update') }}" method="POST" class="bg-gray-800 p-3 rounded">
        @csrf
        <!-- Polja za izmene -->
        <div class="mb-2">
            <label>Ime</label>
            <input type="text" name="first_name" value="{{ $user->first_name }}" class="input-field">
        </div>
        <div class="mb-2">
            <label>Prezime</label>
            <input type="text" name="last_name" value="{{ $user->last_name }}" class="input-field">
        </div>
        <div class="mb-2">
            <label>Grad</label>
            <input type="text" name="city" value="{{ $user->city }}" class="input-field">
        </div>
        <div class="mb-2">
            <label>Država</label>
            <input type="text" name="country" value="{{ $user->country }}" class="input-field">
        </div>
        <button type="submit" class="btn-orange">Sačuvaj promene</button>
    </form>

    {{-- Dugme za brisanje profila --}}
    <form action="{{ route('profile.delete') }}" method="POST" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-orange bg-red-600 hover:bg-red-700">
            Obriši profil
        </button>
    </form>
</div>
@endsection
