@extends('layouts.app')

@section('content')
<div class="chat relative">
    <!-- Header -->
    <div class="chat-header flex items-center justify-between px-4 py-3 bg-white shadow-sm">
        <h1 class="page-title text-lg font-semibold">CHAT</h1>
        <a href="{{ route('advisor.history') }}" class="btn-orange add-car">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

    <!-- Poruke -->
    <div id="chatBody" class="chat-container pb-1">
        @foreach ($purchaseChat->messages as $m)
            {{-- Ako je to prva poruka i uopšte nije odgovor asistenta, preskoči je --}}
            @if($loop->first && $m->role === 'user')
                @continue
            @endif

            <div class="flex {{ $m->role == 'user' ? 'justify-end' : 'justify-start' }}">
                <div
                    class="bubble {{ $m->role == 'user' ? 'user' : 'assistant advisor-table markdown-content' }}"
                    data-content="{{ e($m->content) }}"
                >
                    @if ($m->role == 'user')
                        {{ $m->content }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div id="chat-input-container" class="left-0 mobile-hidden">
        <div class="back-button text-right">
            <a href="{{ route('profile.history') }}" class="btn-orange text-blue-400 inline-block">Nazad</a>
        </div>
    </div>
</div>


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

function escapeHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endsection
