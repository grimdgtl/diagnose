@extends('layouts.app')

@section('content')
@auth
    @php
        $tokensLeft = Auth::user()->num_of_questions_left;     // *** NOVO
        $subscriptionUrl = route('profile.subscription');      // *** NOVO
    @endphp
@endauth

<div class="chat relative">
    <!-- ---------------- Header ---------------- -->
    <div class="chat-header flex items-center justify-between">
        <h1 class="page-title">SAVETNIK</h1>

        @auth
            <span id="questions-left" class="bg-orange text-white px-3 py-1 rounded-md shadow-lg">
                <b>Broj preostalih tokena: {{ $tokensLeft }}</b>
            </span>
        @endauth

        <button class="btn-orange" onclick="archiveChat()">Novo poređenje</button>
    </div>

    <!-- ---------------- Poruke ----------------- -->
    <div id="chatBody" class="chat-container">
        @foreach ($purchaseChat->messages as $m)
            <div class="flex {{ $m->role == 'user' ? 'justify-end' : 'justify-start' }}">
                <div class="bubble {{ $m->role == 'user' ? 'user' : 'assistant advisor-table markdown-content' }}"
                     data-content="{{ e($m->content) }}">
                    @if ($m->role == 'user')
                        {{ $m->content }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- ---------------- Input / Poruka ---------------- -->
    <div id="chatInput">
        @auth
            @if ($tokensLeft > 0)    {{-- *** NOVO – prikaz forme samo ako ima tokena --}}
                <form id="msgForm" class="chat-input">
                    @csrf
                    <input type="text" name="message" class="new-message-field" placeholder="Postavi pitanje">
                    <button type="submit" class="btn-orange send-message">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            @else                    {{-- *** NOVO – poruka + dugme kupi paket --}}
                <div class="buy-questions flex items-center justify-between">
                    <p class="text-gray-300">
                        Nemate više besplatnih tokena.
                    </p>
                    <a href="{{ $subscriptionUrl }}" class="btn-orange px-4 py-2">
                        Kupi tokene
                    </a>
                </div>
            @endif
        @endauth
    </div>
</div>

<!-- ---------------- SCRIPTS ----------------- -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
// Renderovanje Markdown‑a
function renderMarkdown() {
    document.querySelectorAll('.markdown-content').forEach(el => {
        if (!el.classList.contains('user') && el.dataset.content) {
            el.innerHTML = marked.parse(el.dataset.content);
        }
    });
}
renderMarkdown();

/* ---------- DOM reference ---------- */
const form        = document.getElementById('msgForm');
const chatBody    = document.getElementById('chatBody');
const tokenField  = document.querySelector('input[name="_token"]');
const questionsEl = document.getElementById('questions-left');
const subscriptionUrl = "{{ $subscriptionUrl ?? '' }}";   // *** NOVO
let   tokensLeft  = parseInt(questionsEl?.textContent.match(/\d+/)?.[0] || 0);

if (form) {   // Forma postoji samo ako ima tokena
    const sendBtn = form.querySelector('.send-message');
    let   sending = false;

    form.addEventListener('submit', e => {
        e.preventDefault();
        if (sending) return;

        const text = form.message.value.trim();
        if (!text) return;

        // UI: dodaj user bubble + loader
        sending      = true;
        sendBtn.disabled = true;
        appendBubble('user', text);
        form.message.value = '';
        const loader = addLoader();

        // AJAX ka serveru
        fetch('{{ route('advisor.chat.message', $purchaseChat) }}', {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN': tokenField.value,
                'Accept'      : 'application/json'
            },
            body: new URLSearchParams({message: text})
        })
        .then(r => r.json())
        .then(d => {
            loader.dataset.content = d.answer;
            renderMarkdown();

            // *** NOVO – osveži broj tokena i eventualno sakrij formu
            if (d.questions_left !== undefined) {
                tokensLeft = d.questions_left;
                questionsEl.innerHTML = `<b>Broj preostalih tokena: ${tokensLeft}</b>`;
                if (tokensLeft === 0) location.reload(); // jednostavno refresuj view
            }

            sending = false;
            sendBtn.disabled = false;
        })
        .catch(err => {
            loader.textContent = err.message || 'Greška.';
            sending = false;
            sendBtn.disabled = false;
        });
    });
}

/* ---------- helper funkcije ---------- */
function appendBubble(role, content) {
    const div = document.createElement('div');
    div.className = 'flex ' + (role === 'user' ? 'justify-end' : 'justify-start');
    div.innerHTML = role === 'user'
        ? `<div class="bubble user">${escapeHtml(content)}</div>`
        : `<div class="bubble assistant markdown-content" data-content="${escapeHtml(content)}"></div>`;
    chatBody.appendChild(div);
    if (role !== 'user') renderMarkdown();
    chatBody.scrollTop = chatBody.scrollHeight;
}

function addLoader() {
    const wrap = document.createElement('div');
    wrap.className = 'flex justify-start';
    wrap.innerHTML = `
        <div class="bubble assistant markdown-content typing" data-content="">
            <div class="flex space-x-2">
                <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-.3s"></div>
                <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-.15s"></div>
                <div class="h-2 w-2 bg-white rounded-full animate-bounce"></div>
            </div>
        </div>`;
    chatBody.appendChild(wrap);
    chatBody.scrollTop = chatBody.scrollHeight;
    return wrap.querySelector('.bubble');
}

function escapeHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function archiveChat() {
    fetch('{{ route('advisor.chat.archive', $purchaseChat) }}', {
        method : 'POST',
        headers: {'X-CSRF-TOKEN': tokenField?.value ?? ''}
    }).then(() => location.href = '{{ route('advisor.wizard') }}');
}
</script>
@endsection
