@extends('layouts.app')

@section('content')
    <div class="h-full main-child bg-black border-orange radius-10 p-8 relative">
        <h1 class="page-title mb-6">Servisni Zapisi za {{ $car->brand }} {{ $car->model }}</h1>

        @if (session('success'))
            <div class="bg-orange text-white p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4 auth-form overflow-y-scroll">
            @foreach($serviceRecords as $record)
                <div class="items-center justify-between bg-gray-900 border border-orange rounded-lg p-4 text-white">
                    <div class="flex justify-between">
                        <div class="text-orange font-bold leading-1.2 flex items-center mb-4">
                                    <i class="fas fa-cog mr-4"></i>
                                    {{ $record->description }}
                        </div>
                        <form action="{{ route('service-record.destroy', $record->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-orange second-btn text-white px-4 py-2 rounded" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj zapis?')">Obriši</button>
                        </form>
                    </div>
                    <div class="flex justify-between items-end space-x-4">
                        <div>
                            <div class="text-white flex items-center">
                                <i class="fas fa-calendar-alt mr-4"></i>
                                {{ $record->service_date }}</div>
                            <div class="text-white flex items-center">
                                <i class="fas fa-tachometer-alt mr-4"></i>
                                {{ $record->mileage }} km
                            </div>
                            <div class="text-white flex items-center">
                                <i class="fas fa-edit mr-4"></i>
                                {{ $record->notes ?? 'Nema napomena' }}
                            </div>
                        </div>
                        <div class="text-2xl font-bold">{{ $record->cost ? number_format($record->cost, 0) . ' RSD' : '-' }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-between items-center auth-form">
            <a href="{{ route('service-book.create', $car->id) }}" class="btn-orange text-white px-4 py-2 rounded">Dodaj Novi Zapis</a>
            <a href="{{ route('service-book.export', $car->id) }}" class="btn-orange text-white px-4 py-2 rounded ml-4">Preuzmi PDF</a>
        </div>
    </div>
@endsection