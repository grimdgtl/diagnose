@extends('layouts.app')

@section('title', 'Dobro došao na dijagnozu!')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <h1 class="text-3xl text-orange font-bold mb-4">DOBRO DOŠAO NA DIJAGNOZU!</h1>
    <p class="mb-6 text-gray-400">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor...
    </p>

    <form action="{{ route('guest.store-temp-data') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="issueDescription" class="block mb-2">Opis problema</label>
            <textarea name="issueDescription" id="issueDescription" rows="3"
                      class="input-field"></textarea>
        </div>

        <div class="mb-4">
            <label for="diagnose" class="block mb-2">Da li je kacio na dijagnostiku?</label>
            <input type="text" name="diagnose" id="diagnose"
                   class="input-field"/>
        </div>

        <div class="mb-4">
            <label for="indicatorLight" class="block mb-2">Da li sija neka lampica?</label>
            <input type="text" name="indicatorLight" id="indicatorLight"
                   class="input-field"/>
        </div>

        <button type="submit" class="btn-orange">
            Pošalji
        </button>
    </form>
</div>
@endsection
