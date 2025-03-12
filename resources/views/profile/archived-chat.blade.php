@extends('layouts.app')
@section('content')
<div class="h-full main-child mobile-height archived-chat bg-black border-orange radius-10 flex flex-col">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl text-orange font-bold text-center">Chat #{{ $chat->id }}</h1>
    </div>
    <!-- Chat prikaz -->
    <div class="chat-container flex-1 overflow-y-auto">
        @foreach($questions as $q)
        <!-- Pitanje (user) -->
        <div class="flex justify-end mb-2">
            <div class="bubble user">
                {{ $q->issueDescription }}
            </div>
        </div>
        @foreach($q->responses as $resp)
        <!-- Odgovor (assistant) -->
        <div class="flex justify-start mb-2">
            <div class="bubble assistant">
                {{ $resp->content }}
            </div>
        </div>
        @endforeach
        @endforeach
        <div class="back-button">
            <a href="{{ route('profile.history') }}" class="btn-orange text-blue-400 hover:underline inline-block mt-4">Nazad</a>
        </div>
        @endsection
    </div>
</div>