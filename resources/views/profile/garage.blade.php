@extends('layouts.app')

@section('content')
<div class="max-w-7xl garage bg-black mx-8 my-12 px-4 sm:px-6 lg:px-8 py-12">
    <!-- Hero sekcija -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-black bg-gradient-to-r from-orange-400 to-orange-600 bg-clip-text">
            Moja garaža 🚗
        </h1>
        <p class="mt-4 text-lg text-gray-300 max-w-2xl mx-auto">
            Ovo je vaša lična kolekcija automobila. Dodajte, upravljajte i pratite sve detalje o vašim vozilima na jednom mestu.
        </p>
    </div>

    <!-- Grid sa automobilima -->
    @if($cars->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($cars as $car)
                <div class="garage-card relative bg-gray-900 rounded-xl border-2 border-orange-500/20 p-6 hover:border-orange-500/50 transition-all">
                    <!-- Akcije -->
                    <div class="absolute top-4 right-4">
                        <form action="{{ route('profile.garage.delete', $car->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 bg-orange-500/20 hover:bg-orange rounded-full 
                                           transition-colors group"
                                    onclick="return confirm('Da li ste sigurni da želite obrisati ovaj automobil?')">
                                <i class="fas fa-trash text-orange-500 group-hover:text-orange-400"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Glavni podaci -->
                    <div class="space-y-4">
                        <!-- Naslov -->
                        <h2 class="text-2xl font-bold text-orange-500">
                            {{ $car->brand }} {{ $car->model }}
                        </h2>

                        <!-- Godište -->
                        <div class="flex items-center space-x-2 text-gray-300">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ $car->year }}. godište</span>
                        </div>

                        <!-- Snaga i kubikaža -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>{{ $car->engine_power }} KS</span>
                            </div>
                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-gas-pump"></i>
                                <span>{{ $car->engine_capacity }} cm³</span>
                            </div>
                        </div>

                        <!-- Gorivo i menjac -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-gas-pump"></i>
                                <span>{{ $car->fuel_type }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-gray-300">
                                <i class="fas fa-cogs"></i>
                                <span>{{ $car->transmission }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Prazna garaža -->
        <div class="text-center py-20">
            <i class="fas fa-car text-6xl text-orange-500 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-300">Vaša garaža je prazna</h2>
            <p class="text-gray-400 mt-2">
                Dodajte svoj prvi automobil kako biste počeli da koristite aplikaciju.
            </p>
        </div>
    @endif
</div>
@endsection

