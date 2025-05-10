@extends('layouts.app')

@section('content')
<div class="page-format relative">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between chat-header p-4">
            <h1 class="page-title title-max-width">Servisni Zapisi za {{ $car->brand }} {{ $car->model }}</h1>
            <div class="flex w-1/5 gap-4 small-button">
                <a href="{{ route('service-book.create', $car->id) }}"
                    class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car">
                    <i class="fa-solid fa-plus"></i>
                </a>
                <a href="{{ route('service-book.export', $car->id) }}"
                    class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car">
                    <i class="fa-solid fa-download"></i>
                </a>
            </div>
        </div>
            <div class="flex-wrap flex p-4 md:p-8">
                @foreach($serviceRecords as $record)
                    <div class="flex flex-col border-orange radius-10 p-8 relative m-4 service-card">
                        <div class="flex justify-between">
                        <div class="block text-orange font-black flex items-center">
                            {{ $record->description }}
                        </div>
                        <div class="remove-car-icon">    
                            <form action="{{ route('service-record.destroy', $record->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-orange-500/20 hover:bg-orange rounded-full transition-colors group" onclick="return confirm('Da li ste sigurni da želite da obrišete ovaj zapis?')">
                                    <i class="fas fa-trash text-orange-500 group-hover:text-orange-400"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                        <div class="flex justify-between items-end mt-4 mb-4">
                            <div class="text-white flex items-center">
                                <i class="fas fa-calendar-alt mr-4"></i>
                                {{ $record->service_date }}</div>
                            <div class="text-white flex items-center">
                                <i class="fas fa-tachometer-alt mr-4"></i>
                                {{ $record->mileage }} km
                            </div>
                            <div class="text-white flex items-center">
                                <i class="fa-solid fa-wallet mr-4"></i>
                                {{ $record->cost ? number_format($record->cost, 0) . ' RSD' : '-' }}
                            </div>
                        </div>
                        <div class="text-white flex items-center">
                            <i class="fas fa-edit mr-4"></i>
                            {{ $record->notes ?? 'Nema napomena' }}
                        </div>
                    </div>
            @endforeach
        </div>
    </div>
</div>
@endsection