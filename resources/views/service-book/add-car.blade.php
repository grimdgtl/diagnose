@extends('layouts.app')

@section('content')
<div class="h-full main-child bg-black border-orange radius-10">
  <div class="default-width mx-auto">
    <h1 class="page-title">Dodaj vozilo u garazu</h1>
    <p class="text-center mb-8">Nemate nijedno vozilo. Unesite podatke o svom automobilu:</p>

    <form action="{{ route('garage.store') }}" method="POST" class="max-w-xl mx-auto space-y-4 p-8 md:p-0">
      @csrf
      <input name="brand"           class="input-field" placeholder="Proizvođač" value="{{ old('brand') }}">
      <input name="model"           class="input-field" placeholder="Model"     value="{{ old('model') }}">
      <input name="year"  type="number" class="input-field" placeholder="Godište" value="{{ old('year') }}">

      <div class="relative">
        <input name="mileage" type="number" class="input-field" placeholder="Pređeni kilometri" value="{{ old('mileage') }}">
        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">KM</span>
      </div>

      <div class="relative">
        <input name="engine_capacity" class="input-field" placeholder="Kubikaža (1499)" value="{{ old('engine_capacity') }}">
        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">ccm</span>
      </div>

      <div class="relative">
        <input name="engine_power" type="number" class="input-field pr-12" placeholder="Snaga" value="{{ old('engine_power') }}">
        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm">kW</span>
      </div>

      <select name="fuel_type" class="input-field bg-gray-700">
        <option value="">Vrsta goriva</option>
        @foreach (['Benzin','Dizel','TNG','CNG','Električni','Hibridni'] as $opt)
          <option value="{{ $opt }}" {{ old('fuel_type')===$opt?'selected':'' }}>{{ $opt }}</option>
        @endforeach
      </select>

      <select name="transmission" class="input-field bg-gray-700">
        <option value="">Tip menjača</option>
        @foreach (['Manuelni 4','Manuelni 5','Manuelni 6','Automatski'] as $opt)
          <option value="{{ $opt }}" {{ old('transmission')===$opt?'selected':'' }}>{{ $opt }}</option>
        @endforeach
      </select>

      <button type="submit" class="btn-orange w-full">Sačuvaj vozilo</button>
    </form>
  </div>
</div>
@endsection
