@extends('layouts.app')

@section('content')
<div class="page-format relative">
  {{-- ceo ekran kao kolona – header + ostatak --}}
  <div class="flex flex-col h-full">

    {{-- ---------------- HEADER ---------------- --}}
    <div class="flex items-center justify-between chat-header p-4">
      <h1 class="page-title title-max-width">Moja garaža</h1>

      @if($cars->count())
        <a href="{{ route('profile.garage', ['add' => 1]) }}"
           class="btn-orange px-6 py-2 text-black hover:bg-orange-500 add-car small-button">
          + <i class="fas fa-car"></i>
        </a>
      @endif
    </div>

    {{-- =========================================================
         A)  Forma za unos (prazna garaža ili ?add=1)
    ========================================================= --}}
    @if($cars->isEmpty() || request()->boolean('add'))
      {{-- *** forma ostaje ista kao do sada *** --}}
      {{-- -------------- naslov + validacija + form -------------- --}}
      <h2 class="text-center mt-4 md:mt-8 mb-0  text-orange text-xl font-semibold">
        Dodaj vozilo u garažu
      </h2>

      @if ($errors->any())
        <div class="bg-red-600/40 text-red-200 p-4 mb-6 rounded">
          <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('service-book.add-car') }}"
            method="POST"
            class="space-y-6 default-width mx-auto pt-0 pb:0 md:pb-0 px-8 md:p-0">
        @csrf
        {{-- … svi inputi ostaju nepromenjeni … --}}
        {{-- RED 1 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input name="brand"  class="input-field" placeholder="Proizvođač (brend)" value="{{ old('brand') }}">
          <input name="model"  class="input-field" placeholder="Model"               value="{{ old('model') }}">
        </div>

        {{-- RED 2 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input name="year"    type="number" class="input-field" placeholder="Godište"     value="{{ old('year') }}">
          <input name="mileage" type="number" class="input-field" placeholder="Kilometraža" value="{{ old('mileage') }}">
        </div>

        {{-- RED 3 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input name="engine_capacity" class="input-field" placeholder="Kubikaža (npr. 1499)" value="{{ old('engine_capacity') }}">
          <input name="engine_power"    type="number" class="input-field" placeholder="Snaga (kW)" value="{{ old('engine_power') }}">
        </div>

        {{-- RED 4 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <select name="fuel_type" class="input-field">
            <option value="">Vrsta goriva</option>
            @foreach(['Benzin','Dizel','TNG','CNG','Električni','Hibridni'] as $f)
              <option value="{{ $f }}" @selected(old('fuel_type')===$f)>{{ $f }}</option>
            @endforeach
          </select>

          <select name="transmission" class="input-field">
            <option value="">Tip menjača</option>
            @foreach(['Manuelni 4 brzine','Manuelni 5 brzina','Manuelni 6 brzina','Automatski / poluautomatski'] as $t)
              <option value="{{ $t }}" @selected(old('transmission')===$t)>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        <div class="text-center pt-4">
          <button type="submit"
                  class="btn-orange px-6 py-2 text-black hover:bg-orange-500">
            Sačuvaj vozilo
          </button>
        </div>
      </form>

    {{-- =========================================================
         B)  Lista vozila – kartice centrirane po visini & širini
    ========================================================= --}}
    @else
      {{-- flex-grow za preostali prostor, centriranje i po osi Y --}}
      <div class="flex-1 items-center justify-center overflow-y-scroll garage-card-list">
        {{-- same kartice – wrap i horizontalno centriranje --}}
        <div class="flex flex-wrap justify-center items-start gap-2 p-4 w-full">
          @foreach($cars as $car)
            <div
              class="garage-card relative bg-gray-900 rounded-xl border-2 border-orange-500/20 p-6 hover:border-orange-500/50 transition-all
                     w-full flex flex-col">

              {{-- Akcija brisanja --}}
              <div class="absolute top-4 right-4 remove-car-icon">
                <form action="{{ route('profile.garage.delete', $car->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          onclick="return confirm('Da li ste sigurni da želite obrisati ovaj automobil?')"
                          class="p-2 bg-orange-500/20 hover:bg-orange rounded-full transition-colors group">
                    <i class="fas fa-trash text-orange-500 group-hover:text-orange-400"></i>
                  </button>
                </form>
              </div>

              {{-- Podaci o automobilu --}}
              <div class="space-y-4 flex-grow">
                <h2 class="text-2xl font-bold text-orange-500">
                  {{ $car->brand }} {{ $car->model }}
                </h2>

                <div class="flex items-center space-x-2 text-gray-300">
                  <i class="fas fa-calendar-alt"></i>
                  <span>{{ $car->year }}. godište</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div class="flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>{{ $car->engine_power }}</span>
                  </div>
                  <div class="flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-cogs"></i>
                    <span>{{ $car->engine_capacity }} cc</span>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div class="flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-gas-pump"></i>
                    <span>{{ $car->fuel_type }}</span>
                  </div>
                  <div class="flex items-center space-x-2 text-gray-300">
                    <i class="fas fa-cogs"></i>
                    <span>{{ $car->transmission }}</span>
                  </div>
                </div>
              </div>

              {{-- Dugme za servisnu knjigu --}}
              <a href="{{ route('service-book.show', $car->id) }}"
                 class="btn-orange second-btn mt-6 text-black hover:bg-orange-500 text-center">
                Servisna knjiga
              </a>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
