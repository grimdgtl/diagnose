@extends('layouts.app')

@section('content')
<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
    <div class="flex flex-col h-full">
        {{-- ---------------- HEADER ---------------- --}}
        <div class="flex items-center justify-between chat-header p-4">
            <h1 class="page-title title-max-width">Istorija problema</h1>
            <a href="{{ route('home') }}"
               class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car small-button"><i class="fas fa-home"></i>
            </a>
        </div>

    @if($closedChats->count() > 0)
        <div class="flex-grow flex flex-col justify-center p-8">
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
        </div>
    @else
        <p>Nema zatvorenih chat-ova.</p>
    @endif
</div>
@endsection
</div>