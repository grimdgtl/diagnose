{{-- resources/views/chat/dashboard.blade.php --}}
@extends('layouts.app')

@section('title','Dijagnoza - Dashboard')

@section('content')
<div class="user-chat flex flex-col px-12 pt-12">

    <!-- Gornja traka: naslov i broj preostalih pitanja -->
    <div class="flex items-center justify-between chat-header">
        <h1 class="page-title">
            DIJAGNOZA
        </h1>
        @auth
            <span class="bg-orange text-white px-3 py-1 rounded-md shadow-lg">
                <b>Broj preostalih pitanja: {{ Auth::user()->num_of_questions_left }}</b>
            </span>
        @endauth
    </div>

    <!-- Chat prikaz -->
    <div class="chat-container flex-1 overflow-y-auto mb-4">
        @foreach($questions as $q)
            <!-- Desna poruka (user) -->
            <div class="flex justify-end animate-fadeIn">
                <div class="bubble user">
                    {{ $q->issueDescription }}
                </div>
            </div>
            <!-- Odgovori (assistant) -->
            @foreach($q->responses as $resp)
                <div class="flex justify-start animate-fadeIn">
                    <div class="bubble assistant">
                        {{ $resp->content }}
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    <!-- Forma za novo pitanje -->
    @auth
        @if(Auth::user()->num_of_questions_left > 0)
            <form action="{{ route('chat.storeQuestion') }}" method="POST" class="chat-input">
                @csrf
                <input type="hidden" name="chat_id" value="{{ $chat->id ?? '' }}" />
                <input type="text" name="message" class="new-message-field" placeholder="Unesi novo pitanje"
                       class="input-field flex-1 bg-black" />
                <button type="submit" class="btn-orange send-message text-black hover:bg-orange-500">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        @else
            <div class="flex items-center justify-between mb-4">
                <p class="text-red-500 mt-2">
                Nemate više besplatnih pitanja. Kupite paket za više!
                </p>
                <a href="{{ route('profile.subscription') }}"
                    class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Kupite još pitanja
                </a>
            </div>
        @endif
    @else
        <p class="mt-4 text-gray-400">
            Da biste postavili još pitanja, 
            <a href="{{ route('register.form') }}" class="text-orange hover:underline">registrujte se</a>.
        </p>
    @endauth

</div>
@endsection
