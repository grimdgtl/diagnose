@extends('layouts.app')

@section('content')
<div class="user-chat flex flex-col p-4">
    <div class="flex items-center justify-between mb-4">
    <h1 class="text-3xl text-orange font-bold mb-4 text-center">Pregled arhiviranog chata #{{ $chat->id }}</h1>
</div>
<!-- Chat prikaz -->
    <div class="chat-container flex-1 overflow-y-auto mb-4">
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
</div class="back-button">
    <a href="{{ route('profile.history') }}" class="btn-orange text-blue-400 hover:underline inline-block mt-4">
        Nazad
    </a>
</div>
@endsection
