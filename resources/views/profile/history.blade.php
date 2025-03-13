@extends('layouts.app')

@section('content')
<div class="border-orange bg-black radius h-full main-child shadow-lg">
    <h1 class="page-title mb-4">Istorija problema</h1>

    @if($closedChats->count() > 0)
        <ul class="w-full">
        @foreach($closedChats as $c)
            <li class="bg-black p-4 lg:m-4 md:m-0 radius-10 rounded justify-between lg:flex md:block border-orange items-center">
                <div>
                    <p>Chat #{{ $c->id }}</p>
                    <p class="text-sm text-gray-400">Zatvoren: {{ $c->closed_at }}</p>
                </div>
                <a href="{{ route('profile.history.chat', $c->id) }}"
                   class="btn-orange text-black hover:bg-orange-500 mt-4">
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
