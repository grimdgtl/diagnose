{{-- resources/views/chat/dashboard.blade.php --}}
@extends('layouts.app')

@section('title','Dijagnoza - Dashboard')

@section('content')
<div class="flex flex-col h-full user-chat">
    {{-- Naslov --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl text-orange font-bold">DIJAGNOZA</h1>

        {{-- Broj preostalih pitanja --}}
        @auth
            <span class="bg-orange text-white px-3 py-1 rounded-md">
                <b>Broj preostalih pitanja: {{ Auth::user()->num_of_questions_left }}</b>
            </span>
        @endauth
    </div>

    {{-- Chat prikaz --}}
    <div class="flex-1 overflow-y-auto p-4 rounded-lg">
        {{-- Pretpostavimo da si iz kontrolera poslao $questions i $responses 
             ili sve zajedno, npr. $chatHistory, a ovde loop-uješ 
             Samo primer: --}}
        @foreach($questions as $q)
            {{-- Desni bubble: user question --}}
            <div class="flex justify-end mb-2">
                <div class="bg-orange text-black p-3 rounded-lg max-w-md question-response">
                    {{ $q->issueDescription }}
                </div>
            </div>

            {{-- Nadji response-e povezane sa ovim question-om --}}
            @foreach($q->responses as $resp)
            <div class="flex justify-start mb-2">
                <div class="bg-gray-800 text-white p-3 rounded-lg max-w-md question-response">
                    {{ $resp->content }}
                </div>
            </div>
            @endforeach
        @endforeach
    </div>

    {{-- Forma za novo pitanje (ako user ima preostalih pitanja) --}}
    @auth
      @if(Auth::user()->num_of_questions_left > 0)
        <form action="{{ route('chat.storeQuestion') }}" method="POST" class="mt-4 flex items-center space-x-2">
            @csrf
            <input type="hidden" name="chat_id" value="{{ $chat->id ?? '' }}" />
            <input type="text" name="message" placeholder="Unesi novo pitanje"
                   class="input-field flex-1 new-messsage-input" />
            <button type="submit" class="btn-orange send-message">
                <i class="fa fa-paper-plane"></i>
            </button>
        </form>
      @else
        <p class="text-red-500 mt-2">Nemate više besplatnih pitanja. Kupite paket za više!</p>
      @endif
    @else
      <p class="mt-4 text-gray-400">
        Da biste postavili još pitanja, molimo <a href="{{ route('register.form') }}" class="text-orange hover:underline">registrujte se</a>.
      </p>
    @endauth
</div>
@endsection
