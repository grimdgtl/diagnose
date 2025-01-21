@extends('layouts.app')

@section('title', 'Unos problema i podataka o automobilu - Dva koraka')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js" defer></script> 
<style>
/* Uklanjanje strelica iz `number` polja */
input[type="number"] {
    -moz-appearance: textfield; /* Firefox */
    -webkit-appearance: none; /* Safari i Chrome */
    appearance: none; /* Standardni CSS */
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0; /* Uklanjanje margina za spin dugmad */
}

/* Dodavanje strelice za dropdown */
select {
    appearance: none;
    -moz-appearance: none;
    -webkit-appearance: none;
    background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="black" d="M2 0L0 2h4z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 8px 8px;
    padding-right: 2rem; /* Prostor za strelicu */
    border: 1px solid #ccc; /* Prilagodite okvir */
    border-radius: 4px; /* Zaobljeni uglovi */
}
</style>
@endsection

@section('content')
<div class="max-w-3xl mx-auto mt-10" x-data="{ step: 1 }">
    <h1 class="text-3xl text-orange page-title font-bold mb-4">
        Dobro Došao na Dijagnozu
    </h1>
    <p class="mb-6 text-gray-400 text-center">
        Ovo je “dvostepni” formular u jednoj stranici. Popuni korak 1, zatim pređi na korak 2.
    </p>

    @if ($errors->any())
        <div class="bg-red-500 text-white p-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guest.store-temp-data') }}" method="POST" class="space-y-6">
        @csrf

        {{-- STEP 1: Problem --}}
        <div x-show="step === 1" x-cloak class="transition-all duration-300">
            <h2 class="text-xl step font-semibold text-orange text-center mb-2">Korak 1: Opis problema</h2>
            
            <div class="mb-4">
                <textarea id="issueDescription" name="issueDescription" rows="3" class="input-field" 
                placeholder="Detaljno opišite problem koji imate sa automobilom">{{ old('issueDescription') }}</textarea>
            </div>

            <div class="mb-4">
                <input type="text" id="diagnose" name="diagnose" class="input-field"
                       placeholder="Ako ste kačili auto na dijagnostiku upišite kod greške" 
                       value="{{ old('diagnose') }}" />
            </div>

            <div class="mb-4">
                <input type="text" id="indicatorLight" name="indicatorLight" class="input-field"
                       placeholder="Da li Vam sija neka od lampica?"
                       value="{{ old('indicatorLight') }}" />
            </div>

            <button type="button" class="btn-orange"
                    @click="step = 2">
                Dalje
            </button>
        </div>

        {{-- STEP 2: Podaci o autu --}}
        <div x-show="step === 2" x-cloak class="transition-all duration-300">
            <h2 class="text-xl step font-semibold text-center text-orange mb-2">Korak 2: Podaci o automobilu</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input type="text" id="brand" name="brand" class="input-field" placeholder="Proizvođač (brend)"
                           value="{{ old('brand') }}" />
                </div>
                <div>
                    <input type="text" id="model" name="model" class="input-field" placeholder="Model"
                           value="{{ old('model') }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <input type="number" id="year" name="year" class="input-field" placeholder="Godište"
                           value="{{ old('year') }}" />
                </div>
                <div>
                    <input type="text" id="engine_capacity" name="engine_capacity" class="input-field" placeholder="Kubikaža"
                           value="{{ old('engine_capacity') }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="select-arrow">
                    <select id="engine_power" name="engine_power" class="input-field">
                        <option value="">Snaga motora</option>
                        @foreach ([
                            "25kW (34KS)", "35kW (48KS)", "44kW (60KS)", "55kW (75KS)", "66kW (90KS)",
                            "74kW (101KS)", "80kW (109KS)", "85kW (116KS)", "96kW (131KS)", "110kW (150KS)",
                            "125kW (170KS)", "147kW (200KS)", "184kW (250KS)", "222kW (302KS)", 
                            "262kW (356KS)", "294kW (402KS)", "333kW (453KS)"
                        ] as $option)
                            <option value="{{ $option }}" {{ old('engine_power') == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="select-arrow">
                    <select id="fuel_type" name="fuel_type" class="input-field"> 
                        <option value="">Vrsta goriva</option>
                        @foreach ([
                            "Benzin", "Dizel", "Benzin + Gas (TNG)", "Benzin + Metan (CNG)", 
                            "Električni pogon", "Hibridni pogon"
                        ] as $option)
                            <option value="{{ $option }}" {{ old('fuel_type') == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="select-arrow">
                    <select id="transmission" name="transmission" class="input-field">
                        <option value="">Tip menjača</option> 
                        @foreach ([
                            "Manuelni 4 brzine", "Manuelni 5 brzina", 
                            "Manuelni 6 brzina", "Automatski / poluautomatski"
                        ] as $option)
                            <option value="{{ $option }}" {{ old('transmission') == $option ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-4">
                <button type="button" class="btn-orange"
                        @click="step = 1">
                    Nazad
                </button>
                <button type="submit" class="btn-orange">
                    Pošalji
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
