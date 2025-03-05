{{-- resources/views/auth/verify-notice.blade.php --}}
@extends('layouts.app')  {{-- ako želiš da nasleđuješ glavni layout --}}

@section('content')
    <div class="verify-notice w-1/2 mx-auto text-center translate-y-full mt-10 py-12 px-8 bg-black rounded-md">
        <h2 class="text-2xl text-orange uppercase font-black mb-4">Molimo proverite email</h2>
        <p class="text-white">Poslali smo vam verifikacioni link. Kliknite na njega da verifikujete svoj nalog.</p>
        <br>
        <p class="text-white">U slučaju da Vam mail nije stigao u inbox, proverite spam i junk folder</p>
    </div>
@endsection
