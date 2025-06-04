{{-- resources/views/chat/wizard-form-registered.blade.php --}}
@extends('layouts.app')

@section('title', 'Unos problema i podataka o automobilu (registrovani)')

@section('content')
<div class="page-format relative"
     x-data="{
         step: 1,
         carOption: 'existing',
         isSubmitting: false
     }"
>
    <div class="flex flex-col h-full">
    <div class="flex items-center justify-between chat-header p-4">
        <h1 class="page-title">
            Novi Chat
        </h1>
        <span id="questions-left" class="bg-orange text-white text-center px-3 py-1 rounded-md">
            <b>Broj preostalih tokena: {{ Auth::user()->num_of_questions_left }}</b>
        </span>
    </div>

    {{-- PRIKAZ VALIDACIONIH GREŠAKA --}}
    @if ($errors->any())
        <div class="bg-red-500 text-white p-3 rounded mb-4 default-width">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- LOADER --}}
    <div id="loader" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50 hidden">
        <div class="text-center">
            <img class="pulse" src="{{ asset('assets/images/logo-neon.png') }}" alt="Loader">
            <p class="text-white uppercase font-black">Treba mi malo vremena da razmislim, molim sačekajte...</p>
        </div>
    </div>

    @if(Auth::user()->num_of_questions_left > 0)
    <div class="flex-grow flex flex-col justify-center p-2 md:p-8">
        <p class="text-gray-400 text-center">
            Dvostepni formular. Na drugom koraku možeš da izabereš postojeći auto iz garaže ili da uneseš potpuno novi.
        </p>
        <form id="wizardForm" action="{{ route('registered.store-data') }}" method="POST"
              @submit="document.getElementById('loader').classList.remove('hidden')"
              class="space-y-6 default-width"
        >
            @csrf

            {{-- STEP 1 --}}
            <div x-show="step === 1" x-transition x-cloak>            
                <textarea id="issueDescription" required name="issueDescription" rows="3"
                          class="input-field mb-4"
                          placeholder="Detaljno opišite problem koji imate sa automobilom">{{ old('issueDescription') }}</textarea>

                <input type="text" id="diagnose" name="diagnose" class="input-field mb-4"
                       placeholder="Ako ste kačili auto na dijagnostiku, upišite kod greške"
                       value="{{ old('diagnose') }}">

                <input type="text" id="indicatorLight" name="indicatorLight" class="input-field mb-4"
                       placeholder="Da li Vam sija neka od lampica?"
                       value="{{ old('indicatorLight') }}">

                <div class="text-center">
                    <button type="button"
                            class="btn-orange px-6 py-2 text-black hover:bg-orange-500"
                            @click="step = 2">
                        Dalje
                    </button>
                </div>
            </div>

            {{-- STEP 2 --}}
            <div x-show="step === 2" x-transition x-cloak>
                <div class="flex items-center justify-center space-x-6 mb-8">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="carOption" value="existing" 
                               class="hidden radio-custom"
                               x-model="carOption" />
                        <span class="radio-custom-display"></span>
                        <span class="text-gray-300 hover:text-orange transition-colors">Izaberi iz garaže</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="carOption" value="new" 
                               class="hidden radio-custom"
                               x-model="carOption" />
                        <span class="radio-custom-display"></span>
                        <span class="text-gray-300 hover:text-orange transition-colors">Unesi novi auto</span>
                    </label>
                </div>

                {{-- Postojeći auto --}}
                <div x-show="carOption === 'existing'" x-transition>
                    <select id="existing_car_id" name="existing_car_id" class="input-field w-full">
                        <option value="">-- Odaberi auto --</option>
                        @foreach($userCars as $car)
                            <option value="{{ $car->id }}" {{ old('existing_car_id') == $car->id ? 'selected' : '' }}>
                                {{ $car->brand }} - {{ $car->model }} ({{ $car->year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Novi auto --}}
                <div x-show="carOption === 'new'" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <input type="text" id="brand" name="brand" class="input-field"
                               placeholder="Proizvođač (brend)" value="{{ old('brand') }}">
                        <input type="text" id="model" name="model" class="input-field"
                               placeholder="Model" value="{{ old('model') }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <input type="number" id="year" name="year" class="input-field"
                               placeholder="Godište" value="{{ old('year') }}">
                        <input type="text" id="engine_capacity" name="engine_capacity" class="input-field"
                               placeholder="Kubikaža" value="{{ old('engine_capacity') }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <select id="engine_power" name="engine_power" class="input-field">
                            <option value="">Snaga motora</option>
                            @foreach ([
                                "29kW (40KS)", "35kW (48KS)", "37kW (50KS)", "44kW (60KS)", "50kW (68KS)", "55kW (75KS)", "59kW (80KS)", "66kW (90KS)", "74kW (100KS)", "80kW (109KS)", "82kW (112KS)", "85kW (116KS)", "90kW (122KS)", "96kW (130KS)", "100kW (136KS)", "107kW (145KS)", "110kW (150KS)", "118kW (160KS)", "125kW (170KS)", "132kW (180KS)", "143kW (195KS)", "147kW (200KS)", "150kW (204KS)", "162kW (220KS)", "184kW (250KS)", "200kW (272KS)", "220kW (300KS)", "265kW (360KS)", "294kW (400KS)", "331kW (450KS)", "373kW (500KS)"
                            ] as $option)
                                <option value="{{ $option }}" {{ old('engine_power') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>

                        <select id="fuel_type" name="fuel_type" class="input-field">
                            <option value="">Vrsta goriva</option>
                            @foreach (["Benzin", "Dizel", "Benzin + Gas (TNG)", "Benzin + Metan (CNG)", "Električni pogon", "Hibridni pogon"] as $option)
                                <option value="{{ $option }}" {{ old('fuel_type') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>

                        <select id="transmission" name="transmission" class="input-field">
                            <option value="">Tip menjača</option>
                            @foreach (["Manuelni 4 brzine", "Manuelni 5 brzina", "Manuelni 6 brzina", "Automatski / poluautomatski"] as $option)
                                <option value="{{ $option }}" {{ old('transmission') == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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
    @else
        <div class="flex flex-col items-center justify-center h-full default-width">
            <p class="text-gray-400 mb-4 text-center">Nemate više tokena.</p>
            <a href="{{ route('profile.subscription') }}" class="btn-orange px-6 py-2 text-black hover:bg-orange-500">
                Kupi tokene
            </a>
        </div>
    @endif
</div>

<style>
    .pulse {
        animation: pulse-animation 2s infinite;
    }
    @keyframes pulse-animation {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
@endsection
