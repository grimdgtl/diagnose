<!-- resources/views/profile/my-data.blade.php -->
@extends('layouts.app')

@section('content')
<div class="my-profile-data max-w-2xl translate-y-1/4 mx-auto p-4  bg-gray-800 rounded-lg shadow-lg">
    <div class="my-profile-data-head grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <h1 class="page-title text-2xl font-bold text-orange mb-4">Moji podaci</h1>
        <!-- Brisanje profila -->
        <form action="{{ route('profile.delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-orange second-btn bg-red-600 hover:bg-red-700">
            Obriši profil
            </button>
        </form>
    </div>

    <!-- Forma za izmene podataka -->
    <form action="{{ route('profile.update') }}" method="POST" class="bg-gray-700 p-3 rounded">
        @csrf

        <!-- 1. red (ime/prezime) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Ime -->
            <div>
                <input type="text" name="first_name" value="{{ $user->first_name }}" 
                       class="input-field bg-gray-600">
            </div>
            <!-- Prezime -->
            <div>
                <input type="text" name="last_name" value="{{ $user->last_name }}" 
                       class="input-field bg-gray-600">
            </div>
        </div>

        <!-- 2. red (email/telefon) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Email -->
            <div>
                <input type="text" name="email" value="{{ $user->email }}" 
                       class="input-field bg-gray-600">
            </div>
            <!-- Telefon (ako postoji polje phone u DB) -->
            <div>
                <input type="text" name="phone" value="{{ $user->phone ?? '' }}" 
                       class="input-field bg-gray-600">
            </div>
        </div>

        <!-- 3. red (grad/drzava) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Grad -->
            <div>
                <input type="text" name="city" value="{{ $user->city }}" 
                       class="input-field bg-gray-600">
            </div>
            <!-- Država -->
            <div>
                <input type="text" name="country" value="{{ $user->country }}" 
                       class="input-field bg-gray-600">
            </div>
        </div>
    
        <!-- Dugme za čuvanje -->
        <button type="submit" class="btn-orange text-black hover:bg-orange-500 mt-2">
            Sačuvaj promene
        </button>
    </form>
</div>
@endsection
