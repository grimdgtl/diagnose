@extends('layouts.app')

@section('content')
<div class="subscription-wrapper m-12">

    <!-- Informacije o trenutnom paketu i preostalim pitanjima -->
    <div class="mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 plan-grid">
        
        <!-- Blok s info o trenutnoj pretplati -->
        <div class="current-sub-info support-card bg-gray-800 p-6 rounded-md shadow-lg">
            <div>
                <h1 class="plan-page-title text-center mb-8">
                    Dijagnoza paketi
                </h1>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-orange mb-2">Trenutno stanje</h2>
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
            </div>
        </div>
        
        <!-- Starter plan (20 pitanja) -->
        <div class="plan-box relative support-card">
            <h2 class="plan-name">Starter</h2>
            
            <div class="plan-price">
                <span class="plan-amount">200</span>
                <span class="plan-currency">RSD</span> /
                <span class="plan-interval">Mesečno</span>
            </div>

            <ul class="plan-features">
                <li>20 PITANJA</li>
                <li>SAVETI ZA POPRAVKU</li>
                <li>OKVIRNE CENE DELOVA</li>
                <li>PREPORUKE SERVISA</li>
            </ul>

            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <!-- plan_type = "20" -->
                <input type="hidden" name="plan_type" value="20">
                <button type="submit" class="btn-orange plan-button text-black hover:bg-orange-500">
                    Kupi Starter
                </button>
            </form>
        </div>

        <!-- Pro plan (unlimited pitanja) -->
        <div class="plan-box relative support-card">
            <!-- Traka “POPULARNO” -->
            <div class="plan-badge">POPULARNO</div>
            
            <h2 class="plan-name">Pro</h2>
            
            <div class="plan-price">
                <span class="plan-amount">999</span>
                <span class="plan-currency">RSD</span> /
                <span class="plan-interval">Mesečno</span>
            </div>

            <ul class="plan-features">
                <li>NEOGRANIČEN BROJ PITANJA</li>
                <li>SAVETI ZA POPRAVKU</li>
                <li>CENE DELOVA</li>
                <li>PREPORUKE SERVISA</li>
            </ul>

            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <!-- plan_type = "unlimited" -->
                <input type="hidden" name="plan_type" value="unlimited">
                <button type="submit" class="btn-orange plan-button text-black hover:bg-orange-500">
                    Kupi Pro
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
