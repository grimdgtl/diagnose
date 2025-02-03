@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 mt-12 text-center">
    <h1 class="text-3xl text-orange font-bold mb-6">Otkazivanje Procesa Kupovine</h1>
    <p>Odlučili ste da odustanete od kupovine. Ukoliko imate pitanja ili vam je potrebna pomoć, slobodno nas kontaktirajte.</p>
    <a href="{{ route('plans.show') }}" class="btn-orange px-4 py-2 mt-4 text-black hover:bg-orange-500">Povratak na Planove</a>
</div>
@endsection
