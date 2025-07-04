@extends('layouts.app')

@section('content')
<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
  <div class="flex flex-col h-full">

    {{-- ---------------- HEADER ---------------- --}}
    <div class="flex items-center justify-between chat-header p-4">
        <h1 class="page-title text-left">Istorija</h1>
        <a href="{{ route('home') }}"
            class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car"><i class="fas fa-home"></i>
        </a>
    </div>
    @if($archived->isEmpty())
        <p class="mb-6">Nemate arhiviranih poređenja.</p>
        <a href="{{ route('advisor.wizard') }}" class="btn-orange">Pokreni poređenje</a>
    @else
        <ul class="w-full overflow-x-auto p-2 md:p-8">
            @foreach($archived as $chat)
                <li class="bg-black p-4 lg:m-4 md:m-0 radius-10 rounded justify-between flex border-orange items-center">
                    <span>#{{ $chat->id }} <br> {{ $chat->created_at->format('d.m.Y') }}</span>
                    <a href="{{ route('advisor.history.show',$chat) }}" class="btn-orange second-btn">Pregledaj</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
</div>
@endsection
