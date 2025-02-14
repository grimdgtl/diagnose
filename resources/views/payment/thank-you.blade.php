@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h1>Hvala na kupovini!</h1>
    <p>Vaša uplata je uspešno obrađena.</p>
    <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3">Vrati se na početnu</a>
</div>
@endsection
