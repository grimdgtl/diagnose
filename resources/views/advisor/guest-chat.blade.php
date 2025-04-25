@extends('layouts.app')

@section('content')
<div class="chat relative">
    <div class="chat-header flex items-center justify-between">
        <h1 class="page-title">SAVETNIK</h1>
        <a href="{{ route('advisor.guest.wizard') }}" class="btn-orange">Novi upit</a>
    </div>

    <div id="chatBody" class="chat-container">
        @foreach($messages as $msg)
            <div class="flex {{ $msg->role=='user' ? 'justify-end' : 'justify-start' }}">
                <div class="bubble {{ $msg->role=='user' ? 'user' : 'assistant advisor-table markdown-content' }}"
                     data-content="{{ e($msg->content) }}">
                    @if($msg->role=='user')
                        {{ $msg->content }}
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div id="chatFooter" class="chat-footer">
        @if($promptRegistration)
            <div class="text-center p-4">
                <a href="{{ route('register') }}" class="btn-orange">
                    Registruj se da bi dobio još 2 besplatna upita
                </a>
            </div>
        @else
            <div id="chatInput">
                <form id="msgForm" class="chat-input">
                    @csrf
                    <input type="text" name="message" class="new-message-field" placeholder="Postavi pitanje">
                    <button type="submit" class="btn-orange send-message">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Dodaj osnovne stilove za HTML tabele -->
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1em;
    }
    table, th, td {
        border: 1px solid #ddd;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
</style>

<!-- Učitaj Marked.js i renderuj Markdown sadržaj -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    // Render Markdown za sve elemente sa klasom "markdown-content"
    document.querySelectorAll('.markdown-content').forEach(el => {
        if (el.dataset.content) {
            el.innerHTML = marked.parse(el.dataset.content);
        }
    });
</script>
@endsection
