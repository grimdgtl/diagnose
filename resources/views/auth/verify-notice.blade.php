{{-- resources/views/auth/verify-notice.blade.php --}}
@extends('layouts.app')  {{-- ako želiš da nasleđuješ glavni layout --}}

@section('content')
    <div class="border-orange bg-black radius h-full main-child shadow-lg">
        <h2 class="text-2xl page-title font-black text-orange mb-4 text-center">Molimo proverite email</h2>
        <p style="font-weight: 300; text-align: center;" class="text-white">Poslali smo vam verifikacioni link. Kliknite na njega da verifikujete svoj nalog.</p>
        <br>
        <p style="font-weight: 300; text-align: center;" class="text-white">U slučaju da Vam mail nije stigao u inbox, <b>proverite spam i junk</b> folder</p>
    </div>
@endsection
