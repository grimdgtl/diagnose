@extends('layouts.app')

@section('content')
<div class="h-full main-child bg-black border-orange radius-10">
    <div class="default-width mx-auto">
        <h1 class="page-title">Servisna Knjiga</h1>
        <p class="text-center mb-8"> Odaberite vozilo kojem želite da pogledate servisnu knjigu</p>

        @if (session('success'))
            <div class="bg-green-500 text-white p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="relative">
            @foreach($cars as $car)
                <div class="border-orange radius-10 text-center lg:flex md:block sm:block justify-between items-center p-4 mt-4">
                    <h2 class="text-white lg:text-xl md:text-base font-black">{{ $car->brand }} {{ $car->model }} ({{ $car->year }})</h2>
                    <a href="{{ route('service-book.show', $car->id) }}" class="btn-orange lg:mt-1 mt-4">Prikaži Zapise</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection