@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4">
    <h1 class="text-2xl font-bold text-orange mb-4">Pregled arhiviranog chata #{{ $chat->id }}</h1>

    {{-- Prikaz svih pitanja i odgovora --}}
    @foreach($questions as $q)
        <!-- Pitanje (user) -->
        <div class="flex justify-end mb-2">
            <div class="bg-orange text-black p-3 rounded-lg max-w-md">
                {{ $q->issueDescription }}
            </div>
        </div>

        @foreach($q->responses as $resp)
            <!-- Odgovor (assistant) -->
            <div class="flex justify-start mb-2">
                <div class="bg-gray-800 text-white p-3 rounded-lg max-w-md">
                    {{ $resp->content }}
                </div>
            </div>
        @endforeach
    @endforeach

    <a href="{{ route('profile.history') }}" class="mt-4 inline-block text-blue-400 hover:underline">
        Nazad na istoriju
    </a>
</div>
@endsection
