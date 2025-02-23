@extends('layouts.app')

@section('content')
<div class="user-chat flex flex-col px-12 pt-12">
    <div class="flex items-center justify-between chat-header">
        <h1 class="page-title mb-0"> DIJAGNOZA</h1>
    </div>
    <div class="chat-container flex-1 overflow-y-auto mb-4">
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
                <div class="bubble assistant markdown-content animate-fadeIn"
                        data-content="{{ e($res->content) }}">
                    </div>
            </div>
        </div>

            <!-- Poziv na registraciju -->
            <div class="flex justify-start mb-8">
                <div class="bg-black w-full text-white p-4 items-center justify-between rounded-lg flex">
                    <p class="uppercase font-black">Registruj se da bi dobio još 2 besplatna pitanja!</p>
                    <a href="{{ route('register') }}"
                       class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Registruj se
                    </a>
                </div>
            </div>
        @endforeach
    @endforeach
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Selektujemo sve elemente koji imaju .markdown-content
    document.querySelectorAll('.markdown-content').forEach(function(el) {
        // 1) Dohvati originalni, HTML-escaped tekst iz data-content
        const originalText = el.dataset.content || '';
        // 2) Marked će da parsira Markdown
        const html = marked.parse(originalText);
        // 3) Ubacimo HTML nazad u element
        el.innerHTML = html;
    });
});
</script>
