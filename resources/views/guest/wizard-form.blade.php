@extends('layouts.app')

@section('title', 'Unos problema i podataka o automobilu - Dva koraka')

@section('content')
<div class="mx-auto box-height p-12 bg-black rounded-lg shadow-lg" x-data="{ step: 1 }">
    <h1 class="text-3xl text-orange font-bold mb-4 text-center page-title">
        Dobrodošao na Dijagnozu
    </h1>
    <p class="mb-6 text-gray-400 text-center">
        Ovo je dvostepni formular. Popuni korak 1, zatim pređi na korak 2.
    </p>

    @if ($errors->any())
        <div class="bg-red-500 text-white p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('guest.store-temp-data') }}" method="POST" class="space-y-6 default-width">
        @csrf

        <!-- STEP 1 -->
        <div x-show="step === 1" x-cloak>
            <h2 class="text-xl font-semibold text-orange mb-2 text-center">Korak 1: Opis problema</h2>
            <textarea id="issueDescription" name="issueDescription" rows="3"
                      class="input-field bg-gray-700"
                      placeholder="Detaljno opišite problem koji imate sa automobilom">{{ old('issueDescription') }}</textarea>

            <input type="text" id="diagnose" name="diagnose" class="input-field bg-gray-700"
                   placeholder="Ako ste kačili auto na dijagnostiku, upišite kod greške"
                   value="{{ old('diagnose') }}">

            <input type="text" id="indicatorLight" name="indicatorLight" class="input-field bg-gray-700"
                   placeholder="Da li Vam sija neka od lampica?"
                   value="{{ old('indicatorLight') }}">

            <div class="text-center">
                <button type="button" class="btn-orange px-6 py-2 text-black hover:bg-orange-500"
                        @click="step = 2">
                    Dalje
                </button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div x-show="step === 2" x-cloak>
            <h2 class="text-xl font-semibold text-orange mb-2 text-center">Korak 2: Podaci o automobilu</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" id="brand" name="brand" class="input-field bg-gray-700"
                       placeholder="Proizvođač (brend)" value="{{ old('brand') }}">

                <input type="text" id="model" name="model" class="input-field bg-gray-700"
                       placeholder="Model" value="{{ old('model') }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <input type="number" id="year" name="year" class="input-field bg-gray-700"
                       placeholder="Godište" value="{{ old('year') }}">
                <input type="text" id="engine_capacity" name="engine_capacity" class="input-field bg-gray-700"
                       placeholder="Kubikaža" value="{{ old('engine_capacity') }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <!-- Snaga motora -->
                <select id="engine_power" name="engine_power" class="input-field bg-gray-700">
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

                <!-- Vrsta goriva -->
                <select id="fuel_type" name="fuel_type" class="input-field bg-gray-700">
                    <option value="">Vrsta goriva</option>
                    @foreach (["Benzin", "Dizel", "Benzin + Gas (TNG)", "Benzin + Metan (CNG)", "Električni pogon", "Hibridni pogon"] as $option)
                        <option value="{{ $option }}" {{ old('fuel_type') == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>

                <!-- Tip menjača -->
                <select id="transmission" name="transmission" class="input-field bg-gray-700">
                    <option value="">Tip menjača</option>
                    @foreach (["Manuelni 4 brzine", "Manuelni 5 brzina", "Manuelni 6 brzina", "Automatski / poluautomatski"] as $option)
                        <option value="{{ $option }}" {{ old('transmission') == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-6 flex items-center justify-center space-x-4">
                <button type="button" class="btn-orange second-btn px-4 py-2 text-black hover:bg-orange-500"
                        @click="step = 1">
                    Nazad
                </button>
                <button type="submit" class="btn-orange px-4 py-2 text-black hover:bg-orange-500">
                    Pošalji
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
