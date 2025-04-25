@extends('layouts.app')

@section('content')
<div class="chat relative">
    <div class="chat-header flex items-center justify-between">
        <h1 class="page-title">ARHIVIRANI CHAT</h1>
        <a href="{{ route('advisor.history') }}" class="btn-orange">Nazad</a>
    </div>

    <div class="chat-container">
        @foreach($purchaseChat->messages as $m)
            <div class="flex {{ $m->role=='user'?'justify-end':'justify-start' }}">
                <div class="bubble {{ $m->role=='user'?'user':'assistant markdown-content' }}"
                     data-content="{{ e($m->content) }}"></div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.querySelectorAll('.markdown-content').forEach(el=>{
    el.innerHTML = marked.parse(el.dataset.content);
});
</script>
@endsection
