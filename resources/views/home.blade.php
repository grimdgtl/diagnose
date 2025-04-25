@extends('layouts.app')
@section('content')
<div class="h-full mobile-height main-child bg-black border-orange radius-10">
    <!-- Informacije o trenutnom paketu i preostalim pitanjima -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:p-8 md:p-0 homepage">
        
        <!-- Blok s info o trenutnoj pretplati -->
        <div class="bg-orange p-6 rounded-md shadow-lg flex homepage-card justify-center">
            <div>
                <h1 class="text-white text-left mb-8">Dobrodošli na Dijagnozu</h1>
            </div>
            <div>
                <p>Vaš lični virtuelni mehaničar i savetnik za kupovinu polovnih automobila!</p><br>
                <p>Naša platforma koristi napredne AI tehnologije za brzu i tačnu dijagnostiku automobilskih problema i detaljne savete pri kupovini polovnih vozila.<br><br>Dobijte profesionalne informacije i preporuke prilagođene vašim potrebama, bilo da rešavate kvar ili tražite idealno vozilo po pristupačnoj ceni.</p>
            </div>
        </div>
        
        <!-- Starter plan (20 pitanja) -->
        <div class="plan-box relative support-card flex homepage-card items-center justify-between">
            <h2 class="plan-name text-orange-400">Virtuelni Mehaničar</h2>
            <p>Samo unesite opis problema i detalje vozila, a naša usluga koristi napredne algoritme i AI za identifikaciju mogućih uzroka kvara.<br><br>Dizajniran je da bude dostupan 24/7, uvek spreman da razjasni sve nedoumice i odgovori na pitanja na koja majstori često nemaju vremena da odgovore.</p>
            
            {{-- Link za neulogovane --}}
            @guest
                <a href="{{ route('guest.wizard-form') }}" class="btn-orange mt-8">
                    Virtuelni mehaničar
                </a>
            @endguest

            {{-- Link za ulogovane --}}
            @auth
                <a href="{{ route('dashboard') }}" class="btn-orange mt-8">
                    Virtuelni mehaničar
                </a>
            @endauth
        </div>

        <!-- Pro plan (unlimited pitanja) -->
        <div class="plan-box relative support-card flex homepage-card items-center justify-between">
            <h2 class="plan-name text-orange-400">Savetnik za kupovinu</h2>
            <p>Unesite podatke o vozilu, a AI će analizirati unete informacije i pružiti detalje o prednostima, manama, fabričkim greškama i ostale bitne informacije o vozilu. Takođe možete uporediti do tri vozila, što vam olakšava izbor idealnog vozila prema vašim potrebama.<br><br>Savetnik za kupovinu automobila dostupan je 24/7, omogućavajući vam da chatujete i dobijete odgovore na sva pitanja vezana za kupovinu vozila.</p>
            
            {{-- Link za neulogovane --}}
            @guest
                <a href="{{ route('advisor.guest.wizard') }}" class="btn-orange mt-8">
                    Savetnik za kupovinu
                </a>
            @endguest

            {{-- Link za ulogovane --}}
            @auth
                <a href="{{ route('advisor.chatOrWizard') }}" class="btn-orange mt-8">
                    Savetnik za kupovinu
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
