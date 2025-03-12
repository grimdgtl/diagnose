@extends('layouts.app')
@section('content')
<div class="h-full mobile-height main-child bg-black border-orange radius-10">
    <form method="POST" action="{{ route('service-book.store') }}" class="w-full">
        @csrf
        <h1 class="page-title md:text-xl mb-6">Dodaj Novi Servisni Zapis</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-6 md:gap-2">
            <div class="mb-4">
                <select name="car_detail_id" id="car_detail_id" class="input-field">
                    @foreach($cars as $car)
                    <option value="{{ $car->id }}">{{ $car->brand }} {{ $car->model }} ({{ $car->year }})</option>
                    @endforeach
                </select>
                @error('car_detail_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <input type="text" name="service_date" id="service_date" class="input-field flatpickr-input" required placeholder="Izaberite datum">
                @error('service_date')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mb-4">
            <textarea name="description" id="description" class="input-field" rows="4" placeholder="Opišite šta je radjeno" required></textarea>
            @error('description')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-6 md:gap-2">
            <div class="mb-4">
                
                <input type="number" name="mileage" id="mileage" placeholder="Kilometraža" class="input-field" required>
                @error('mileage')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <input type="number" step="0.01" name="cost" id="cost" placeholder="Cena (RSD)" class="input-field">
                @error('cost')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="mb-4">
            <textarea name="notes" id="notes" class="input-field" placeholder="Napomena" rows="3"></textarea>
            @error('notes')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn-orange">Sačuvaj</button>
    </form>
</div>
@endsection