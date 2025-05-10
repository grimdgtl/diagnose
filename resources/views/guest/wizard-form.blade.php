@extends('layouts.app')

@section('title', 'Unos problema i podataka o automobilu - Dva koraka')

@section('content')
<div class="bg-black border-orange radius h-full main-child mobile-height" x-data="{ step: 1 }">
    <h1 class="text-3xl text-orange font-bold mb-4 text-center page-title">
        Dobrodošao na Dijagnozu
    </h1>
    <p class="mb-6 text-gray-400 text-center max-w-2xl">
        Dijagnoza je tvoj novi, lični, virtuelni auto-mehaničar koji može da ti pomogne da rešiš svaki problem sa tvojim autom. Jesi li spreman da uštediš vreme i novac, a da pritom dobiješ savete od pravog stručnjaka?
    </p>

    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 hidden">
        <div class="text-center">
            <!-- Može slika, animirani GIF, ili CSS spinner -->
            <img class="pulse" src="{{ asset('assets/images/logo-neon.png') }}">
            <p class="text-white uppercase font-black">Treba mi 10s da razmislim, molim sačekajte...</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-500 text-white p-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="guest-form" action="{{ route('guest.store-temp-data') }}" method="POST" class="auth-form">
        @csrf

        <!-- STEP 1 -->
        <div x-show="step === 1" x-cloak>
            <textarea id="issueDescription" name="issueDescription" rows="3" required=""
                      class="input-field bg-gray-700 mb-4"
                      placeholder="Detaljno opišite problem koji imate sa automobilom">{{ old('issueDescription') }}</textarea>

            <input type="text" id="diagnose" name="diagnose" class="input-field bg-gray-700 mb-4"
                   placeholder="Ako ste kačili auto na dijagnostiku, upišite kod greške"
                   value="{{ old('diagnose') }}">

            <input type="text" id="indicatorLight" name="indicatorLight" class="input-field bg-gray-700 mb-4"
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form   = document.getElementById('guest-form');
    const loader = document.getElementById('loader');

    form.addEventListener('submit', async e => {
        e.preventDefault();
        loader.classList.remove('hidden');

        try {
            const res = await fetch(form.action, {
                method : 'POST',
                body   : new FormData(form),
                headers: { 'X-Requested-With' : 'XMLHttpRequest' }
            });

            if (res.status === 403) {
                const j = await res.json();
                window.location = j.redirectUrl;
                return;
            }

            if (!res.ok) throw await res.json();
            const j = await res.json();
            window.location = j.redirectUrl;

        } catch (err) {
            alert('Greška pri slanju forme.');
        } finally {
            loader.classList.add('hidden');
        }
    });
});
</script>

@endsection
