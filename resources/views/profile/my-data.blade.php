<!-- resources/views/profile/my-data.blade.php -->
@extends('layouts.app')

@section('content')
<div class="h-full main-child bg-black radius-10 border-orange mobile-height">
    <div class="justify-between flex auth-form mb-4">
            <h1 class="font-black text-orange text-uppercase text-left second-title mb-4">Moji podaci</h1>
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
    <form action="{{ route('profile.update') }}" method="POST" class="bg-gray-700 auth-form">
        @csrf
        <div class="mb-4">
            <p> Ako želite da promenite podatke, potrebno je da unesete nove podatke u polja forme i kliknete na dugme "Sačuvaj promene"</p>
        </div>
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
