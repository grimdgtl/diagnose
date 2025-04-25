@extends('layouts.app')

@section('content')
<div class="main-child text-center">
    <h1 class="page-title">SAVETNIK ZA KUPOVINU</h1>
    <p class="mb-8">Uporedi do tri polovnjaka i odmah vidi njihove mane, troškove servisa i potrošnju.</p>

    @auth
        <a href="{{ route('advisor.wizard') }}" class="btn-orange mx-auto">Pokreni upoređivanje</a>
    @else
        <a href="{{ route('login') }}" class="btn-orange mx-auto">Prijavi se da započneš</a>
    @endauth
</div>
@endsection
