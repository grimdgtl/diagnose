@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-12">
    <h1 class="text-3xl text-orange font-bold mb-6 text-center">Izaberi paket</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Starter plan -->
        <div class="bg-gray-800 text-white p-6 rounded border-2 border-orange relative hover:shadow-lg transition-all">
            <h2 class="text-xl font-bold">Starter</h2>
            <p class="text-4xl font-bold my-2">RSD 200 <span class="text-base">/ mesečno</span></p>
            <ul class="mb-4 space-y-1">
                <li>20 pitanja</li>
                <li>Saveti za popravku</li>
                <li>Okvirne cene delova</li>
                <li>Preporuke servisa</li>
            </ul>
            <!-- Forma koja šalje 'plan_id' = 'plan_20' na routes/plans/buy -->
            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="plan_20">
                <button type="submit" class="btn-orange px-4 py-2 mt-2 text-black hover:bg-orange-500">
                    Kupi Starter
                </button>
            </form>
        </div>

        <!-- Pro plan -->
        <div class="bg-gray-800 text-white p-6 rounded border-2 border-orange relative hover:shadow-lg transition-all">
            <div class="absolute top-0 right-0 bg-orange text-black text-xs font-bold px-2 py-1">
                POPULARNO
            </div>
            <h2 class="text-xl font-bold">Pro</h2>
            <p class="text-4xl font-bold my-2">RSD 999 <span class="text-base">/ mesečno</span></p>
            <ul class="mb-4 space-y-1">
                <li>Neograničen broj pitanja</li>
                <li>Saveti za popravku</li>
                <li>Cene delova</li>
                <li>Preporuke servisa</li>
            </ul>
            <!-- Forma koja šalje 'plan_id' = 'plan_unlimited' na routes/plans/buy -->
            <form action="{{ route('plans.buy') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="plan_unlimited">
                <button type="submit" class="btn-orange px-4 py-2 mt-2 text-black hover:bg-orange-500">
                    Kupi Pro
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
