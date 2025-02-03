<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-10 bg-black translate-y-1/4 rounded-lg shadow-lg">
    <h2 class="text-2xl page-title font-black text-orange mb-4 text-center">Prijava</h2>

    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="my-8">
            <input type="email" name="email" placeholder="Email" id="email" value="{{ old('email') }}" required
                   class="input-field bg-gray-700">
        </div>

        <div class="mt-8 mb-2">
            <input type="password" name="password" placeholder="Šifra" id="password" required
                   class="input-field bg-gray-700">
        </div>

        <div class="mb-4 flex items-center justify-center">
            <input type="checkbox" name="remember" class="form-checkbox text-orange mr-2"
                   {{ old('remember') ? 'checked' : '' }}>
            <span class="text-sm">Zapamti me</span>
        </div>
        <div class="mb-4 mt-8 flex items-center justify-center">
            <button type="submit" class="btn-orange w-full text-black hover:bg-orange-500">
            Prijavi se
            </button>
        </div>

        <div class="text-center mt-4">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-orange underline text-sm">
                    Zaboravljena šifra?
                </a>
            @endif
        </div>
    </form>
</div>
@endsection
