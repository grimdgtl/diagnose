<!-- resources/views/auth/forgot-password.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 translate-y-1/3 bg-black rounded-lg shadow-lg">
    <h2 class="text-2xl font-black text-orange my-8 page-title text-center">Zaboravljena Šifra</h2>

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
        <div class="my-12">
            <input type="email" name="email" placeholder="Unesite Vašu Email adresu" id="email" value="{{ old('email') }}" required
                   class="input-field bg-gray-700">
        </div>
        <div class="text-center">
            <button type="submit" class="btn-orange mb-8 w-full text-black hover:bg-orange-500 mt-2">
            Pošalji Reset Link
            </button>
        </div>
    </form>
</div>
@endsection
