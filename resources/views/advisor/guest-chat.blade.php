@extends('layouts.app')

@section('content')
<div class="chat relative">
    <!-- Header -->
    <div class="chat-header flex items-center justify-between">
        <h1 class="page-title">SAVETNIK</h1>
        <!--<a href="{{ route('advisor.guest.wizard') }}" class="btn-orange">Novi upit</a>-->
    </div>

    <!-- Poruke -->
    <div id="chatBody" class="chat-container">
        @foreach($messages as $msg)
            <div class="flex {{ $msg->role=='user' ? 'justify-end' : 'justify-start' }}">
                <div
                    class="bubble {{ $msg->role=='user' ? 'user' : 'assistant' }}
                           advisor-responsive prose prose-sm sm:prose-base
                           max-w-[800px] markdown-content"
                    data-content="{{ e($msg->content) }}">
                    @if($msg->role=='user')
                        {{ $msg->content }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Footer -->
    <div id="chatFooter" class="chat-footer">
        @if($promptRegistration)
        <div id="chatInput">
            <div class="buy-questions flex items-center justify-between">
                <p class="text-gray-300">Registruj se da bi nastavio konverzaciju</p>
                <a href="{{ route('register') }}" class="btn-orange px-4 py-2">Registruj se</a>
            </div>
        </div>
        @else
            <form id="msgForm" class="chat-input">
                @csrf
                <input type="text" name="message" class="new-message-field" placeholder="Postavi pitanje">
                <button type="submit" class="btn-orange send-message">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Marked.js -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
/**
 * 1) Renderuj Markdown
 * 2) Doteraj <table> elemente da lepo izgledaju u Tailwind-u
 * 3) Ispod 640 px prebacuj redove u "stacked" kartice
 */
document.querySelectorAll('.markdown-content').forEach(el => {
    if (!el.dataset.content) return;

    // Zameni <br> &nbsp; hack
    const md = el.dataset.content
        .replace(/&lt;br\s*\/?&gt;/gi, '\n')
        .replace(/<br\s*\/?>(?=\s*)/gi, '\n');

    el.innerHTML = marked.parse(md);

    // Doteraj sve tabele unutar ove poruke
    el.querySelectorAll('table').forEach(table => {
        table.classList.add(
            'w-full', 'text-sm', 'border-collapse',
            'border', 'border-gray-300',
            'rounded-lg', 'overflow-hidden'
        );
        // Thead
        table.querySelectorAll('thead th').forEach(th => {
            th.classList.add(
                'bg-gray-100', 'font-semibold', 'px-3', 'py-2',
                'border', 'border-gray-300', 'text-center'
            );
        });
        // Tbody
        table.querySelectorAll('tbody td').forEach(td => {
            td.classList.add(
                'px-3', 'py-2', 'border', 'border-gray-300',
                'text-center', 'sm:text-left'
            );
        });

        // CARD-VIEW ispod 640 px
        if (window.matchMedia('(max-width: 639px)').matches) {
            const headings = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            table.querySelectorAll('tbody tr').forEach(row => {
                row.querySelectorAll('td').forEach((cell, idx) => {
                    cell.classList.add('flex', 'justify-between', 'gap-2');
                    cell.insertAdjacentHTML('afterbegin',
                        `<span class="font-medium text-gray-500">${headings[idx]}:</span>`);
                });
            });
            // ukloni <thead> da ne duplira informacije
            table.querySelector('thead').remove();
        }
    });
});
</script>

<!-- Dodatni stilovi -->
<style>
/* Bubble – omogući horizontalni skrol samo za sadržaj (ne za ceo balon) */
.advisor-responsive {
    overflow-x: auto;
    scrollbar-width: thin;
}
.advisor-responsive::-webkit-scrollbar {
    height: 6px;
}
.advisor-responsive::-webkit-scrollbar-thumb {
    background: theme('colors.gray.300');
    border-radius: 3px;
}
</style>
@endsection
