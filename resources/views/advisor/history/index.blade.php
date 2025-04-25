@extends('layouts.app')

@section('content')
<div class="border-orange bg-black radius h-full main-child shadow-lg">
    <h1 class="page-title mb-4">Istorija poređenja</h1>

    @if($archived->isEmpty())
        <p class="mb-6">Nemate arhiviranih poređenja.</p>
        <a href="{{ route('advisor.wizard') }}" class="btn-orange">Pokreni poređenje</a>
    @else
        <ul class="w-full overflow-x-auto">
            @foreach($archived as $chat)
                <li class="bg-black p-4 lg:m-4 md:m-0 radius-10 rounded justify-between lg:flex md:block border-orange items-center">
                    <span>#{{ $chat->id }} • {{ $chat->created_at->format('d.m.Y H:i') }}</span>
                    <a href="{{ route('advisor.history.show',$chat) }}" class="btn-orange second-btn">Pregledaj</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
