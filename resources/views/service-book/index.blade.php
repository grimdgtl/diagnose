@extends('layouts.app')

@section('content')
<div class="chat relative">
  <div class="flex flex-col h-full">

    {{-- ---------------- HEADER ---------------- --}}
    <div class="flex items-center justify-between chat-header">
      <h1 class="page-title">Servisna knjiga</h1>

      @if($cars->count())
        <a href="{{ route('home') }}"
          class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car">
          <i class="fas fa-home"></i>
        </a>
      @else
        <a href="{{ route('profile.garage', ['add' => 1]) }}"
           class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car">
          + <i class="fas fa-car"></i>
        </a>
      @endif
    </div>

    {{-- Flash poruka --}}
    @if(session('success'))
      <div class="bg-green-500 text-white p-4 mb-6 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- ************************************************************
         A) Nema vozila – obaveštenje + link ka “Moja garaža”
    ************************************************************ --}}
    @if($cars->isEmpty())
      <div class="text-center py-20 flex-grow flex flex-col justify-center">
        <h2 class="text-2xl font-bold text-gray-300">
          Nemate nijedno vozilo u garaži
        </h2>
        <p class="text-gray-400 mt-2">
          Da biste mogli da vodite servisnu knjigu, prvo unesite vozilo.
        </p>

        <a href="{{ route('profile.garage', ['add' => 1]) }}"
           class="btn-orange mt-8 ml-auto mr-auto">
          + Dodaj vozilo
        </a>
      </div>

    {{-- ************************************************************
         B) Lista postojećih vozila – VERTIKALNO CENTRIRANO
    ************************************************************ --}}
    @else
      <div class="flex-grow flex flex-col overflow-y-scroll justify-top p-2 md:p-8">

        <p class="text-center mb-8">
          Odaberite vozilo kojem želite da pregledate servisnu knjigu
        </p>

        {{-- lista automobila --}}
        @foreach($cars as $car)
          <div class="border-orange radius-10 text-center lg:flex md:block
                      justify-between items-center p-4 mt-4">
            <h2 class="text-white lg:text-xl md:text-base font-black">
              {{ $car->brand }} {{ $car->model }} ({{ $car->year }})
            </h2>

            <a href="{{ route('service-book.show', $car->id) }}"
               class="btn-orange lg:mt-1 mt-4">
              Prikaži zapise
            </a>
          </div>
        @endforeach

        {{-- dugme: dodaj novo vozilo --}}
        <div class="text-center mt-10">
          <a href="{{ route('profile.garage', ['add' => 1]) }}"
             class="btn-orange second-btn">
            + Dodaj novo vozilo
          </a>
        </div>

      </div>
    @endif
  </div>
</div>
@endsection
