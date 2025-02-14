{{-- resources/views/chat/dashboard.blade.php --}}
@extends('layouts.app')

@section('title','Dijagnoza - Dashboard')

@section('content')
<div class="user-chat flex flex-col px-12 pt-12">

    <!-- Gornja traka: naslov i broj preostalih pitanja -->
    <div class="flex items-center justify-between chat-header">
        <h1 class="page-title">
            DIJAGNOZA
        </h1>
        @auth
            <span id="questions-left" class="bg-orange text-white px-3 py-1 rounded-md shadow-lg">
                <b>Broj preostalih pitanja: {{ Auth::user()->num_of_questions_left }}</b>
            </span>
        @endauth
    </div>

    <!-- Chat prikaz -->
    <div class="chat-container flex-1 overflow-y-auto mb-4" id="chat-container">
        @foreach($questions as $q)
            <!-- Desna poruka (user) -->
            <div class="flex justify-end animate-fadeIn">
                <div class="bubble user">
                    {{ $q->issueDescription }}
                </div>
            </div>
            <!-- Odgovori (assistant) -->
            @foreach($q->responses as $resp)
                <div class="flex justify-start animate-fadeIn mb-2">
                    <div class="bubble assistant markdown-content"
                        data-content="{{ e($resp->content) }}">
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>

    <!-- Forma za novo pitanje -->
    @auth
        @if(Auth::user()->num_of_questions_left > 0)
            <form action="{{ route('chat.storeQuestion') }}" method="POST" class="chat-input" id="chat-form">
                @csrf
                <input type="hidden" name="chat_id" value="{{ $chat->id ?? '' }}" />
                <input type="text" name="message" class="new-message-field" placeholder="Unesi novo pitanje"
                       class="input-field flex-1 bg-black" />
                <button type="submit" class="btn-orange send-message text-black hover:bg-orange-500">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        @else
            <div class="flex items-center justify-between mb-4">
                <p class="text-red-500 mt-2">
                Nemate više besplatnih pitanja. Kupite paket za više!
                </p>
                <a href="{{ route('profile.subscription') }}"
                    class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Kupite još pitanja
                </a>
            </div>
        @endif
    @else
        <p class="mt-4 text-gray-400">
            Da biste postavili još pitanja, 
            <a href="{{ route('register.form') }}" class="text-orange hover:underline">registrujte se</a>.
        </p>
    @endauth

</div>
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

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

document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const chatContainer = document.getElementById('chat-container');

    chatForm.addEventListener('submit', function(e) {
    e.preventDefault(); // Sprečava standardni submit

    // Preuzmi referencu na input polje i njegovu vrednost
    const messageField = chatForm.querySelector('input[name="message"]');
    const userMessage = messageField.value;
    
    // Kreiraj FormData pre nego što se input očisti
    const formData = new FormData(chatForm);
    
    // Sada očisti input polje
    messageField.value = ''; 

    // 1) Kreiraj korisničku poruku u DOM-u
    addUserBubble(userMessage);

    // 2) Kreiraj "assistant" bubble sa loader animacijom
    const loaderBubble = addAssistantLoader();

    // 3) Pošalji AJAX zahtev
    fetch(chatForm.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async (response) => {
        if (!response.ok) {
            throw await response.json();
        }
        return response.json();
    })
    .then(data => {
        // 4) Zamenimo loaderBubble sadržajem formiranim odgovorom
        if (data.success) {
            // Koristi marked.js da parsira Markdown u HTML
            loaderBubble.innerHTML = marked.parse(data.answer);
            loaderBubble.classList.remove('typing');

            // Ažuriraj broj preostalih pitanja, ako postoji element za to
            const questionsLeftSpan = document.getElementById('questions-left');
            if (questionsLeftSpan && data.questions_left !== undefined) {
                questionsLeftSpan.textContent = data.questions_left;
            }
        }
    })
    .catch(err => {
        console.error("Greška:", err);
        loaderBubble.textContent = "Dogodila se greška prilikom slanja poruke.";
    });
});


    // Pomoćne funkcije:

    function addUserBubble(message) {
        const userDiv = document.createElement('div');
        userDiv.classList.add('flex', 'justify-end', 'animate-fadeIn');
        userDiv.innerHTML = `
            <div class="bubble user">
                ${escapeHtml(message)}
            </div>
        `;
        chatContainer.appendChild(userDiv);
        // Scroll na dno
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function addAssistantLoader() {
        const assistantDiv = document.createElement('div');
        assistantDiv.classList.add('flex', 'justify-start', 'animate-fadeIn');
        assistantDiv.innerHTML = `
            <div class="bubble assistant typing">
                <div class='flex space-x-2 justify-center items-center'>
                    <span class='sr-only'>Loading...</span>
                    <div class='h-2 w-2 bg-white rounded-full animate-bounce [animation-delay:-0.3s]'></div>
                    <div class='h-2 w-2 bg-white rounded-full animate-bounce [animation-delay:-0.15s]'></div>
                    <div class='h-2 w-2 bg-white rounded-full animate-bounce'></div>
                </div>
            </div>
        `;
        chatContainer.appendChild(assistantDiv);
        // Dohvati sam element "assistant bubble" koji ćemo kasnije zameniti sadržajem
        const bubble = assistantDiv.querySelector('.assistant');
        chatContainer.scrollTop = chatContainer.scrollHeight;
        return bubble;
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }
});
</script>

@endsection


