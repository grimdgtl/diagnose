@extends('layouts.app')

@section('content')
<div class="subscription-wrapper m-12">
    <!-- Informacije o trenutnom paketu i preostalim pitanjima -->
    <div class="mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 plan-grid">
        
        <!-- Blok s info o trenutnoj pretplati -->
        <div class="current-sub-info support-card bg-gray-800 p-6 rounded-md shadow-lg">
            <div>
                <h1 class="plan-page-title text-center mb-8">Dijagnoza paketi</h1>
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

            <form id="buy-basic-form">
                @csrf
                <input type="hidden" name="product" value="basic">
                <button type="button" 
                        class="btn-orange plan-button text-black hover:bg-orange-500"
                        id="buy-basic">
                    Kupi Basic
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

            <form id="buy-pro-form">
                @csrf
                <input type="hidden" name="product" value="pro">
                <button type="button" 
                        class="btn-orange plan-button text-black hover:bg-orange-500"
                        id="buy-pro">
                    Kupi Pro
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buyBasicBtn = document.getElementById('buy-basic');
    const buyProBtn   = document.getElementById('buy-pro');
    
    // Event listeneri za klik na dugmad
    buyBasicBtn.addEventListener('click', () => {
        buyPlan('basic');
    });

    buyProBtn.addEventListener('click', () => {
        buyPlan('pro');
    });

    function buyPlan(planType) {
        fetch("{{ route('payment.create') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').getAttribute("content")
            },
            body: JSON.stringify({ product: planType })
        })
        .then(response => response.json())
        .then(data => {
            if (data.checkout_url) {
                window.location.href = data.checkout_url;
            } else {
                alert("Error: Could not load checkout.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Došlo je do greške prilikom kupovine.");
        });
    }
});
</script>
@endsection
