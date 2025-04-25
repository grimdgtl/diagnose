{{-- resources/views/chat/dashboard.blade.php --}}
@extends('layouts.app')

@section('title','Dijagnoza - Dashboard')

@section('content')
<div class="chat relative">
    <div class="flex flex-col h-full">
    
        <!-- Gornja traka: naslov i broj preostalih pitanja -->
        <div class="flex items-center justify-between chat-header">
            <h1 class="page-title">
                DIJAGNOZA
            </h1>
            @auth
                <span id="questions-left" class="bg-orange text-white px-3 py-1 rounded-md shadow-lg">
                    <b>Broj preostalih tokena: {{ Auth::user()->num_of_questions_left }}</b>
                </span>
            @endauth
        </div>

        <!-- Chat prikaz ili poruka kad nema aktivnog chata -->
        <div id="chat-container"
             class="chat-container flex-1 overflow-y-auto mb-4 @unless($chat) flex items-center justify-center @endunless">
            @if($chat)
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
            @else
                <div class="flex flex-col items-center justify-center">
                    <p class="text-gray-400 mb-4">Ne postoji aktivan chat.</p>
                    <a href="{{ route('chat.new') }}" class="btn-orange px-6 py-2">
                        Novi Chat
                    </a>
                </div>
            @endif
        </div>

        <!-- Dinamički deo za formu i poruku -->
        @auth
            @if($chat)
                <div id="chat-input-container"></div>
            @endif
        @else
            <p class="mt-4 text-gray-400">
                Da biste postavili još pitanja, 
                <a href="{{ route('register.form') }}" class="text-orange hover:underline">registrujte se</a>.
            </p>
        @endauth

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parsiranje inicijalnih odgovora
    document.querySelectorAll('.markdown-content').forEach(el => {
        const txt = el.dataset.content || '';
        if (txt) el.innerHTML = marked.parse(txt);
    });

    @auth
    @if($chat)
    const chatContainer      = document.getElementById('chat-container');
    const chatInputContainer = document.getElementById('chat-input-container');
    const questionsLeftSpan  = document.getElementById('questions-left');
    let initialQuestionsLeft = parseInt(questionsLeftSpan.textContent.match(/\d+/)?.[0] || 0);

    const chatFormAction  = '{{ route("chat.storeQuestion") }}';
    const chatId          = '{{ $chat->id }}';
    const subscriptionUrl = '{{ route("profile.subscription") }}';

    function updateChatInput(questionsLeft) {
        chatInputContainer.innerHTML = '';
        if (questionsLeft > 0) {
            chatInputContainer.innerHTML = `
                <form action="${chatFormAction}" method="POST" class="chat-input" id="chat-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="chat_id" value="${chatId}">
                    <input type="text" name="message" class="new-message-field" placeholder="Unesi novo pitanje">
                    <button type="submit" class="btn-orange send-message text-black hover:bg-orange-500">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            `;
            document.getElementById('chat-form')
                    .addEventListener('submit', handleFormSubmit);
        } else {
            chatInputContainer.innerHTML = `
                <div class="buy-questions flex items-center justify-between">
                    <p class="text-white-500">Nemate više besplatnih tokena.</p>
                    <a href="${subscriptionUrl}" class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">Kupite još tokena</a>
                </div>
            `;
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const field = form.querySelector('input[name="message"]');
        const text = field.value.trim();
        if (!text) return;

        // Kreiraj podatke pre brisanja vrednosti
        const payload = new URLSearchParams();
        payload.append('_token', '{{ csrf_token() }}');
        payload.append('chat_id', chatId);
        payload.append('message', text);

        // Clear polje
        field.value = '';

        // Prikaži user bubble
        const userDiv = document.createElement('div');
        userDiv.className = 'flex justify-end animate-fadeIn';
        userDiv.innerHTML = `<div class="bubble user">${escapeHtml(text)}</div>`;
        chatContainer.appendChild(userDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;

        // Prikaži loader
        const loaderDiv = document.createElement('div');
        loaderDiv.className = 'flex justify-start animate-fadeIn mb-2';
        loaderDiv.innerHTML = `
            <div class="bubble assistant typing">
                <div class="flex space-x-2 justify-center items-center">
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.3s"></div>
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.15s"></div>
                    <div class="h-2 w-2 bg-white rounded-full animate-bounce"></div>
                </div>
            </div>`;
        chatContainer.appendChild(loaderDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        const bubble = loaderDiv.querySelector('.bubble');

        try {
            const response = await fetch(chatFormAction, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload
            });
            const data = await response.json();
            if (!response.ok) {
                const msg = data.errors?.message?.[0] || data.message || 'Greška pri slanju poruke.';
                throw new Error(msg);
            }
            const answer = data.answer || '';
            bubble.innerHTML = marked.parse(answer);
            bubble.classList.remove('typing');

            if (data.questions_left !== undefined) {
                questionsLeftSpan.innerHTML = `<b>Broj preostalih tokena: ${data.questions_left}</b>`;
                updateChatInput(data.questions_left);
            }
        } catch (err) {
            bubble.textContent = err.message;
            bubble.classList.remove('typing');
        }
    }

    function escapeHtml(str) {
        return str.replace(/&/g,'&amp;')
                  .replace(/</g,'&lt;')
                  .replace(/>/g,'&gt;')
                  .replace(/"/g,'&quot;');
    }

    // Pokreni inicijalno
    updateChatInput(initialQuestionsLeft);
    @endif
    @endauth
});
</script>
@endsection
