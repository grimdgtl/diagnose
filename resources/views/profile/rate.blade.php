@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-4">
    <h1 class="text-2xl font-bold text-orange mb-4">Oceni aplikaciju</h1>

    <form action="{{ route('profile.rate') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="rating" class="block mb-1">Ocena (1-5)</label>
            <select name="rating" id="rating" class="input-field">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="feedback" class="block mb-1">Komentar (opciono)</label>
            <textarea name="feedback" id="feedback" rows="3" class="input-field"></textarea>
        </div>
        <button type="submit" class="btn-orange">Pošalji ocenu</button>
    </form>
</div>
@endsection
