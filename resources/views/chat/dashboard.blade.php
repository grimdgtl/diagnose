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

    <!-- Dinamički deo za formu i poruku -->
    @auth
        <div id="chat-input-container">
            <!-- Ovde će JavaScript ubaciti formu ili poruku -->
        </div>
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
    // Obrada Markdown sadržaja
    document.querySelectorAll('.markdown-content').forEach(function(el) {
        const originalText = el.dataset.content || '';
        const html = marked.parse(originalText);
        el.innerHTML = html;
    });

    const chatContainer = document.getElementById('chat-container');
    const chatInputContainer = document.getElementById('chat-input-container');
    const questionsLeftSpan = document.getElementById('questions-left');
    let initialQuestionsLeft = parseInt(questionsLeftSpan?.textContent.match(/\d+/)?.[0] || 0);

    // Funkcija za postavljanje forme ili poruke
    function updateChatInput(questionsLeft) {
        chatInputContainer.innerHTML = ''; // Očisti postojeći sadržaj

        if (questionsLeft > 0) {
            chatInputContainer.innerHTML = `
                <form action="${chatFormAction}" method="POST" class="chat-input" id="chat-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="chat_id" value="${chatId}" />
                    <input type="text" name="message" class="new-message-field" placeholder="Unesi novo pitanje" />
                    <button type="submit" class="btn-orange send-message text-black hover:bg-orange-500">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            `;
            const chatForm = document.getElementById('chat-form');
            chatForm.addEventListener('submit', handleFormSubmit);
        } else {
            chatInputContainer.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <p class="text-red-500 mt-2">
                        Nemate više besplatnih pitanja. Kupite paket za više!
                    </p>
                    <a href="${subscriptionUrl}" class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Kupite još pitanja
                    </a>
                </div>
            `;
        }
    }

    // Funkcija za obradu submit-a forme
    function handleFormSubmit(e) {
        e.preventDefault();
        const messageField = e.target.querySelector('input[name="message"]');
        const userMessage = messageField.value;
        const formData = new FormData(e.target);
        messageField.value = '';

        addUserBubble(userMessage);
        const loaderBubble = addAssistantLoader();

        fetch(e.target.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async (response) => {
            if (!response.ok) {
                throw await response.json();
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                loaderBubble.innerHTML = marked.parse(data.answer);
                loaderBubble.classList.remove('typing');

                if (questionsLeftSpan && data.questions_left !== undefined) {
                    questionsLeftSpan.innerHTML = `<b>Broj preostalih pitanja: ${data.questions_left}</b>`;
                    updateChatInput(data.questions_left); // Ažuriraj formu ili poruku
                }
            }
        })
        .catch(err => {
            console.error("Greška:", err);
            loaderBubble.textContent = err.message || "Dogodila se greška prilikom slanja poruke.";
            loaderBubble.classList.remove('typing');
        });
    }

    // Pomoćne funkcije
    function addUserBubble(message) {
        const userDiv = document.createElement('div');
        userDiv.classList.add('flex', 'justify-end', 'animate-fadeIn');
        userDiv.innerHTML = `
            <div class="bubble user">
                ${escapeHtml(message)}
            </div>
        `;
        chatContainer.appendChild(userDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function addAssistantLoader() {
        const assistantDiv = document.createElement('div');
        assistantDiv.classList.add('flex', 'justify-start', 'animate-fadeIn');
        assistantDiv.innerHTML = `
            <div class="bubble assistant typing">
                <div class="flex space-x-2 justify-center items-center">
                    <span class="sr-only">Loading...</span>
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce"></div>
                </div>
            </div>
        `;
        chatContainer.appendChild(assistantDiv);
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

    // Inicijalizacija
    const chatFormAction = '{{ route("chat.storeQuestion") }}';
    const chatId = '{{ $chat->id ?? "" }}';
    const subscriptionUrl = '{{ route("profile.subscription") }}';
    updateChatInput(initialQuestionsLeft);
});
</script>

@endsection