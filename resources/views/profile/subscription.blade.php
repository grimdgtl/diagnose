<!-- resources/views/profile/subscription.blade.php -->
@extends('layouts.app')

@section('content')
<div class="subscription-wrapper my-8">

    <!-- Sada prikaz trenutnog paketa i preostalih pitanja -->
    

    <!-- Kartice (Starter i Pro) -->
    <div class="mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 plan-grid">
        <div class="current-sub-info bg-gray-800 p-6 rounded-md shadow-lg">
        <div>
            <h1 class="plan-page-title text-center mb-8">
        ODABERI PAKET
    </h1>
        </div>
        <div>
        <h2 class="text-xl font-semibold text-orange mb-2">Trenutno stanje</h2>
        <p><strong>Paket: </strong>{{ ucfirst($user->subscription_type ?? 'Nema aktivnog paketa') }}</p>
        <p><strong>Preostala pitanja: </strong>
            @if($user->subscription_type === 'unlimited')
                Unlimited
            @else
                {{ $user->num_of_questions_left }}
            @endif
        </p>
        </div>
    </div>
        <!-- Starter plan -->
        <div class="plan-box relative">
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
                <input type="hidden" name="plan_type" value="20">
                <button type="submit" class="btn-orange plan-button text-black hover:bg-orange-500">
                    Kupi Starter
                </button>
            </form>
        </div>

        <!-- Pro plan -->
        <div class="plan-box relative">
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
                <input type="hidden" name="plan_type" value="unlimited">
                <button type="submit" class="btn-orange plan-button text-black hover:bg-orange-500">
                    Kupi Pro
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
