@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-3xl text-orange font-bold mb-4 text-center">Guest Chat Dashboard</h1>

    @foreach($tempQuestions as $q)
        <!-- Desni bubble (user) -->
        <div class="flex justify-end mb-2">
            <div class="bubble user animate-fadeIn">
                {{ $q->issueDescription }}
            </div>
        </div>

        @php
            $myResponses = $tempResponses->where('question_id', $q->id);
        @endphp

        @foreach($myResponses as $res)
            <!-- Levi bubble (assistant) -->
            <div class="flex justify-start mb-2">
                <div class="bubble assistant animate-fadeIn">
                    {{ $res->content }}
                </div>
            </div>

            <!-- Poziv na registraciju -->
            <div class="flex justify-start mb-8">
                <div class="bg-gray-700 text-white p-4 rounded-lg assistant-response">
                    <p class="mb-2">Registruj se da bi dobio još 2 besplatna pitanja!</p>
                    <a href="{{ route('profile.subscription') }}"
                       class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Registruj se
                    </a>
                </div>
            </div>
        @endforeach
    @endforeach
</div>
@endsection
