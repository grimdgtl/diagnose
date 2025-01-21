<!-- resources/views/profile/subscription.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-gray-800 rounded-lg">
    <h1 class="text-2xl font-bold text-orange mb-4">Subscription</h1>

    <div class="mb-4">
        <p><strong>Trenutni Paket:</strong> {{ ucfirst($user->subscription_type ?? 'Nema aktivnog paketa') }}</p>
        <p><strong>Preostala Pitanja:</strong> 
            @if($user->subscription_type === 'unlimited')
                Unlimited
            @else
                {{ $user->num_of_questions_left }}
            @endif
        </p>
    </div>

    <h2 class="text-xl font-semibold text-orange mb-2">Upgrade Paket</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Starter plan -->
        <div class="bg-gray-700 p-4 rounded">
            <h3 class="text-lg font-bold">Starter</h3>
            <p>20 pitanja</p>
            <p>Cena: RSD 200 mesečno</p>
            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_type" value="20">
                <button type="submit" class="btn-orange mt-2 w-full">Kupi Starter</button>
            </form>
        </div>

        <!-- Pro plan -->
        <div class="bg-gray-700 p-4 rounded">
            <h3 class="text-lg font-bold">Pro</h3>
            <p>Neograničen broj pitanja</p>
            <p>Cena: RSD 999 mesečno</p>
            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_type" value="unlimited">
                <button type="submit" class="btn-orange mt-2 w-full">Kupi Pro</button>
            </form>
        </div>
    </div>
</div>
@endsection
