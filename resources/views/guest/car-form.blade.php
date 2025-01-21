{{-- resources/views/guest/car-form.blade.php --}}
@extends('layouts.app')

@section('title', 'Unesi podatke o automobilu')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl text-orange font-bold mb-4">DOBRO DOŠAO NA DIJAGNOZU!</h1>
    <p class="mb-6 text-gray-400">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod...
    </p>

    {{-- Druga forma (ako hoćeš da je odvojeno) --}}
    <form action="{{ route('guest.store-temp-data') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="mb-4">
                <label for="brand" class="block mb-1">Proizvođač</label>
                <input type="text" name="brand" id="brand" class="input-field" />
            </div>
            <div class="mb-4">
                <label for="model" class="block mb-1">Model</label>
                <input type="text" name="model" id="model" class="input-field" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="mb-4">
                <label for="year" class="block mb-1">Godište</label>
                <input type="number" name="year" id="year" class="input-field" />
            </div>
            <div class="mb-4">
                <label for="engine_capacity" class="block mb-1">Kubikaža</label>
                <input type="text" name="engine_capacity" id="engine_capacity"
                       class="input-field" />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4">
            <div class="mb-4">
                <label for="engine_power" class="block mb-1">Snaga motora</label>
                <input type="text" name="engine_power" id="engine_power" class="input-field" />
            </div>
            <div class="mb-4">
                <label for="fuel_type" class="block mb-1">Vrsta goriva</label>
                <select name="fuel_type" id="fuel_type" class="input-field">
                    <option value="Benzin">Benzin</option>
                    <option value="Dizel">Dizel</option>
                    <option value="Plin">Plin</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label for="transmission" class="block mb-1">Vrsta menjača</label>
            <select name="transmission" id="transmission" class="input-field">
                <option value="Manuelni 5 brzina">Manuelni 5 brzina</option>
                <option value="Manuelni 6 brzina">Manuelni 6 brzina</option>
                <option value="Automatik">Automatik</option>
            </select>
        </div>

        <button type="submit" class="btn-orange">
            Dalje
        </button>
    </form>
</div>
@endsection
