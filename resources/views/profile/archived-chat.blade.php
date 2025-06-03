@extends('layouts.app')
@section('content')
<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
    <div class="flex flex-col h-full">
        {{-- ---------------- HEADER ---------------- --}}
        <div class="flex items-center justify-between chat-header p-4">
            <h1 class="page-title title-max-width">Chat #{{ $chat->id }}</h1>
            <a href="{{ route('profile.history') }}"
               class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car small-button">Nazad
            </a>
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
            <div id="chat-input-container" class="left-0">
                <div class="back-button text-right">
                    <a href="{{ route('profile.history') }}" class="btn-orange text-blue-400 inline-block">Nazad</a>
                 </div>
             </div>
            @endsection
        </div>
    </div>
</div>