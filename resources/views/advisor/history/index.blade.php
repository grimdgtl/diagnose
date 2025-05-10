@extends('layouts.app')

@section('content')
<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
  <div class="flex flex-col h-full">

    {{-- ---------------- HEADER ---------------- --}}
    <div class="flex items-center justify-between chat-header p-4">
      <h1 class="page-title title-max-width">Istorija poredjenja</h1>
    </div>
    @if($archived->isEmpty())
        <p class="mb-6">Nemate arhiviranih poređenja.</p>
        <a href="{{ route('advisor.wizard') }}" class="btn-orange">Pokreni poređenje</a>
    @else
        <ul class="w-full overflow-x-auto px-8">
            @foreach($archived as $chat)
                <li class="bg-black p-4 lg:m-4 md:m-0 radius-10 rounded justify-between flex border-orange items-center">
                    <span>#{{ $chat->id }} • {{ $chat->created_at->format('d.m.Y H:i') }}</span>
                    <a href="{{ route('advisor.history.show',$chat) }}" class="btn-orange second-btn">Pregledaj</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
</div>
@endsection
