@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-4 text-center">
    <h1 class="text-3xl text-orange font-bold mb-4">Hvala na kupovini!</h1>
    <p class="text-white mb-6">Uspesno ste kupili paket. Sada imate 
        @if(auth()->user()->num_of_questions_left > 20) 
           verovatno unlimited
        @else
           20
        @endif
        pitanja!
    </p>
    <a href="{{ route('dashboard') }}" class="btn-orange px-4 py-2">
        Nazad na Dashboard
    </a>
</div>
@endsection
