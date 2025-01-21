@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-3xl text-orange mb-4">Guest Chat Dashboard</h1>

    @foreach($tempQuestions as $q)
        <!-- Desni bubble (user) -->
        <div class="flex justify-end mb-2">
            <div class="text-white p-3 rounded-lg assistant-response">
                {{ $q->issueDescription }}
            </div>
        </div>

        <!-- Pronadji pripadajuće tempResponse-e -->
        @php
            $myResponses = $tempResponses->where('question_id', $q->id);
        @endphp
        @foreach($myResponses as $res)
            <!-- Levi bubble (assistant) -->
            <div class="flex justify-start mb-2">
                <div class="bg-gray-800 text-white p-3 rounded-lg assistant-response">
                    {{ $res->content }}
                </div>
            </div>

            <!-- Poziv na registraciju ispod asistentovog odgovora (samo jednom?)
                 Ako želiš posle svakog asistentovog odgovora, ostavi ovako.
                 Ako želiš samo posle *poslednjeg*, stavi uslov izvan petlje. -->
            <div class="flex justify-start mb-8">
                <div class="bg-gray-700 text-white p-3 rounded-lg assistant-response">
                    <p class="mb-2">Registruj se da bi dobio još 2 besplatna pitanja!</p>
                    <a href="{{ route('register') }}"
                       class="inline-block bg-orange btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Registruj se
                    </a>
                </div>
            </div>

        @endforeach
    @endforeach
</div>
@endsection
