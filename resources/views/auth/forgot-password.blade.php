<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-gray-800 rounded-lg">
    <h2 class="text-2xl font-bold text-orange mb-4">Zaboravljena Šifra</h2>

    @if (session('status'))
        <div class="mb-4 text-green-500">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="w-full p-2 mt-1 bg-gray-700 text-white rounded">
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="w-full bg-orange text-black p-2 rounded hover:bg-orange-500">
                Pošalji Reset Link
            </button>
        </div>
    </form>
</div>
@endsection
