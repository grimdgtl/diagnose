@extends('layouts.app')
@section('content')
<div class="h-full mobile-height main-child bg-black border-orange radius-10">
    <!-- Informacije o trenutnom paketu i preostalim pitanjima -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:p-8 md:p-0">
        
        <!-- Blok s info o trenutnoj pretplati -->
        <div class="bg-orange p-6 rounded-md shadow-lg flex justify-center flex-col">
            <div>
                <h1 class="text-white text-left mb-4 leading-8">Dijagnoza paketi</h1>
            </div>
            <div>
                 
                <div class="bg-black p-4 radius-10 mb-4">  
                    <div>
                        <h2 class="text-xl font-semibold text-white mb-2">Trenutno stanje</h2>
                            <p>
                                <strong>Paket: </strong>
                                {{ ucfirst($user->subscription_type ?? 'Nema aktivnog paketa') }}
                            </p>
                            <p>
                                <strong>Preostalo tokena: </strong>
                                @if($user->subscription_type === 'unlimited')
                                Unlimited
                                @else
                                {{ $user->num_of_questions_left }}
                                @endif
                            </p>
                            <p class="mt-4">
                                <strong>*1 upit/poruka = 1 token </strong>
                            </p>
                        </div>
                        @if($user->subscription_type && $user->lemonSqueezyCustomer?->trial_ends_at)
                        <p>
                            <strong>Vazi do: </strong>
                            {{ $user->lemonSqueezyCustomer->trial_ends_at->format('d.m.Y.') }}
                        </p>
                        @endif
                    </div>
                    <div class="bg-black p-4 radius-10">
                    <div>
                        <p>*Tokeni se koriste za komunikaciju sa AI savetnicima u aplikaciji.<br><br>Svaka poruka ili upit koji pošalješ troši jedan token.<br><br>Kupljeni paketi tokena važe 31 dan od dana kupovine. Plaćanje se obavlja brzo i bezbedno platnom karticom.</p>
                    </div>
                </div> 
            </div>
        </div>
        
        <!-- Starter plan (20 pitanja) -->
        <div class="plan-box relative support-card">
            <h2 class="plan-name">Starter</h2>
            
            <div class="plan-price">
                <span class="plan-amount">300</span>
                <span class="plan-currency">RSD</span>
                <p class="plan-interval">*paket važi 1 mesec</p>
            </div>
            <ul class="plan-features">
                <li>20 tokena</li>
                <li>Virtuelni automehaničar</li>
                <li>Savetnik za kupovonu automobila</li>
                <li>Servisna knjiga</li>
                <li>Okvirne cene delova</li>
                <li>Preporuke servisa</li>
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
                <span class="plan-currency">RSD</span>
                <p class="plan-interval">*paket važi 1 mesec</p>
            </div>
            <ul class="plan-features">
                <li>500 tokena</li>
                <li>Virtuelni automehaničar</li>
                <li>Savetnik za kupovonu automobila</li>
                <li>Servisna knjiga</li>
                <li>Okvirne cene delova</li>
                <li>Preporuke servisa</li>
            </ul>
            <!-- Direktan checkout link generisan u Lemon Squeezy dashboard-u -->
            <!-- Pro plan -->
            <x-lemon-button :href="$checkoutPro" class="btn-orange">
            Kupi Pro
            </x-lemon-button>
        </div>
        <!-- Pro plan subskripcija (unlimited pitanja) 
        <div class="plan-box relative support-card">
            <div class="plan-badge">POPULARNO</div>
            
            <h2 class="plan-name">Expert</h2>
            
            <div class="plan-price">
                <span class="plan-amount">9000</span>
                <span class="plan-currency">RSD</span>
                <span class="plan-interval">Tokeni važe 1 godinu</span>
            </div>
            <ul class="plan-features">
                <li>500 tokena svaki mesec</li>
                <li>Virtuelni automehaničar</li>
                <li>Savetnik za kupovonu automobila</li>
                <li>Servisna knjiga</li>
                <li>CarVertical kod za popust</li>
                <li>Okvirne cene delova</li>
                <li>Preporuke servisa</li>
            </ul>
            <x-lemon-button :href="$checkoutPro" class="btn-orange">
            Kupi Expret
            </x-lemon-button>
        </div>-->
    </div>
</div>
@endsection