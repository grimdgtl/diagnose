@extends('layouts.app')

@section('content')
<div class="archive-chat max-w-3xl support-card  mx-auto p-4 my-12 bg-gray-800 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-orange mb-4">Istorija chat-ova</h1>

    @if($closedChats->count() > 0)
        <ul class="chat-row space-y-2">
        @foreach($closedChats as $c)
            <li class="bg-gray-700 p-3 rounded flex justify-between items-center">
                <div>
                    <p>Chat #{{ $c->id }}</p>
                    <p class="text-sm text-gray-400">Zatvoren: {{ $c->closed_at }}</p>
                </div>
                <a href="{{ route('profile.history.chat', $c->id) }}"
                   class="btn-orange text-black hover:bg-orange-500">
                    Pregled
                </a>
            </li>
        @endforeach
        </ul>
    @else
        <p>Nema zatvorenih chat-ova.</p>
    @endif
</div>
@endsection
