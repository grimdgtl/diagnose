@extends('layouts.app')
@section('content')
<div class="page-format relative">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between chat-header p-4">
            <h1 class="page-title title-max-width">Dodaj Novi Servisni Zapis</h1>
        </div>
    <form method="POST" action="{{ route('service-book.store') }}" class="space-y-4 default-width mx-auto p-8 md:p-0">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-4 md:gap-2">
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-4 md:gap-2">
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
</div>
@endsection