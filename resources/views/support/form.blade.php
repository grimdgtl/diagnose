<!-- resources/views/support/form.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto p-6 bg-gray-800 rounded-lg">
    <h2 class="text-2xl font-bold text-orange mb-4">Podrška</h2>

    @if(session('success'))
        <div class="mb-4 text-green-500">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 text-red-500">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('support.submit') }}" method="POST">
        @csrf

        <!-- Subject -->
        <div class="mb-4">
            <label for="subject" class="block text-sm font-medium">Predmet</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Message -->
        <div class="mb-4">
            <label for="message" class="block text-sm font-medium">Poruka</label>
            <textarea name="message" id="message" rows="5" required
                      class="w-full p-2 mt-1 bg-gray-700 text-white rounded">{{ old('message') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-orange text-black p-2 rounded hover:bg-orange-500">
                Pošalji poruku
            </button>
        </div>
    </form>
</div>
@endsection
