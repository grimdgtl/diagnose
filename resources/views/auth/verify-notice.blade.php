{{-- resources/views/auth/verify-notice.blade.php --}}
@extends('layouts.app')  {{-- ako želiš da nasleđuješ glavni layout --}}

@section('content')
    <div class="max-w-md mx-auto mt-10 p-4 bg-gray-800 rounded-md">
        <h2 class="text-2xl text-orange font-bold mb-4">Molimo proverite email</h2>
        <p class="text-white">Poslali smo vam verifikacioni link. Kliknite na njega da verifikujete svoj nalog.</p>
    </div>
@endsection
