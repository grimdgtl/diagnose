@extends('layouts.app')
@section('content')
<div class="h-full mobile-height main-child bg-black border-orange radius-10">
    <!-- Informacije o trenutnom paketu i preostalim pitanjima -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:p-8 md:p-0">
        
        <!-- Blok s info o trenutnoj pretplati -->
        <div class="bg-orange p-6 rounded-md shadow-lg">
            <div>
                <h1 class="text-white text-left mb-8">Dijagnoza paketi</h1>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-white mb-2">Trenutno stanje</h2>
                <p>
                    <strong>Paket: </strong>
                    {{ ucfirst($user->subscription_type ?? 'Nema aktivnog paketa') }}
                </p>
                <p>
                    <strong>Preostala pitanja: </strong>
                    @if($user->subscription_type === 'unlimited')
                    Unlimited
                    @else
                    {{ $user->num_of_questions_left }}
                    @endif
                </p>
                <!-- Dodavanje datuma isteka -->
                @if($user->subscription_type && $user->lemonSqueezyCustomer?->trial_ends_at)
                <p>
                    <strong>Vazi do: </strong>
                    {{ $user->lemonSqueezyCustomer->trial_ends_at->format('d.m.Y.') }}
                </p>
                @endif
            </div>
        </div>
        
        <!-- Starter plan (20 pitanja) -->
        <div class="plan-box relative support-card">
            <h2 class="plan-name">Starter</h2>
            
            <div class="plan-price">
                <span class="plan-amount">300</span>
                <span class="plan-currency">RSD</span> /
                <span class="plan-interval">Mesečno</span>
            </div>
            <ul class="plan-features">
                <li>20 PITANJA</li>
                <li>SAVETI ZA POPRAVKU</li>
                <li>OKVIRNE CENE DELOVA</li>
                <li>PREPORUKE SERVISA</li>
                <li>Servisna knjiga</li>
            </ul>
            <!-- Direktan checkout link generisan u Lemon Squeezy dashboard-u -->
            <!-- Starter plan -->
            <x-lemon-button :href="$checkoutBasic" class="btn-orange">
            Kupi Basic
            </x-lemon-button>
        </div>
        <!-- Pro plan (unlimited pitanja) -->
        <div class="plan-box relative support-card">
            <!-- Traka “POPULARNO” -->
            <div class="plan-badge">POPULARNO</div>
            
            <h2 class="plan-name">Pro</h2>
            
            <div class="plan-price">
                <span class="plan-amount">990</span>
                <span class="plan-currency">RSD</span> /
                <span class="plan-interval">Mesečno</span>
            </div>
            <ul class="plan-features">
                <li>NEOGRANIČEN BROJ PITANJA</li>
                <li>SAVETI ZA POPRAVKU</li>
                <li>CENE DELOVA</li>
                <li>PREPORUKE SERVISA</li>
                <li>Servisna knjiga</li>
            </ul>
            <!-- Direktan checkout link generisan u Lemon Squeezy dashboard-u -->
            <!-- Pro plan -->
            <x-lemon-button :href="$checkoutPro" class="btn-orange">
            Kupi Pro
            </x-lemon-button>
        </div>
    </div>
</div>
@endsection