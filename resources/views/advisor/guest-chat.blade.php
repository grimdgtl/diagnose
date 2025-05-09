{{-- resources/views/advisor/guest-chat.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="chat relative h-full">
  <!-- Header -->
  <div class="chat-header flex items-center justify-between">
    <h1 class="page-title">SAVETNIK</h1>
  </div>

  <!-- Poruke -->
  <div id="chatBody" class="chat-container space-y-6 mb-4 overflow-y-auto">
    @foreach($messages as $msg)
      {{-- Preskoči inicijalni “user” prompt --}}
      @if($loop->first && $msg->role === 'user')
        @continue
      @endif

      <div class="flex {{ $msg->role==='user' ? 'justify-end' : 'justify-start' }}">
        <div
          class="bubble {{ $msg->role==='user' ? 'user' : 'assistant advisor-table markdown-content' }}
                 max-w-prose bg-white text-gray-900 p-6 rounded-2xl shadow-lg
                 prose prose-lg dark:prose-invert"
          data-content="{{ e($msg->content) }}"
        >
          @if($msg->role==='user')
            {{ $msg->content }}
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <!-- Footer -->
  <div id="chatFooter" class="chat-footer">
    @if($promptRegistration)
      <div class="buy-questions flex items-center justify-between">
        <p class="text-gray-300">Registruj se da bi nastavio konverzaciju</p>
        <a href="{{ route('register') }}" class="btn-orange px-4 py-2">Registruj se</a>
      </div>
    @else
      <form id="guest-followup-form" class="chat-input flex items-center space-x-2">
        @csrf
        <input type="text" name="message" class="flex-1 new-message-field" placeholder="Postavi dodatno pitanje">
        <button type="submit" class="btn-orange send-message px-4 py-2">
          <i class="fa fa-paper-plane"></i>
        </button>
      </form>
    @endif
  </div>
</div>

{{-- Marked.js --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
  function escapeHtml(str) {
    return str.replace(/&/g,'&amp;')
              .replace(/</g,'&lt;')
              .replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;');
  }

  // Markdown + table styling
  function renderMarkdown() {
    document.querySelectorAll('.markdown-content').forEach(el => {
      if (el.dataset.content) {
        el.innerHTML = marked.parse(el.dataset.content);
      }
    });
  }
  function styleTables() {
    document.querySelectorAll('.advisor-table table').forEach(table => {
      table.classList.add(
        'w-full','text-sm','border-collapse','border','border-gray-300','rounded-lg','overflow-hidden'
      );
      table.querySelectorAll('thead th').forEach(th => {
        th.classList.add('bg-gray-100','font-semibold','px-4','py-2','border','border-gray-300','text-left');
      });
      table.querySelectorAll('tbody td').forEach(td => {
        td.classList.add('px-4','py-2','border','border-gray-300');
      });
      if (window.matchMedia('(max-width:639px)').matches) {
        const headers = Array.from(table.querySelectorAll('thead th')).map(th=>th.textContent.trim());
        table.querySelectorAll('tbody tr').forEach(row => {
          row.querySelectorAll('td').forEach((td,i) => {
            td.classList.add('flex','justify-between','gap-2');
            td.insertAdjacentHTML('afterbegin',
              `<span class="font-medium text-gray-500">${headers[i]}:</span>`
            );
          });
        });
        table.querySelector('thead')?.remove();
      }
    });
  }

  // Ubacuje bubli i vraća .bubble element
  function appendBubble(role, content = '') {
    const chatBody = document.getElementById('chatBody');
    const wrapper = document.createElement('div');
    wrapper.className = 'flex ' + (role==='user' ? 'justify-end mb-2' : 'justify-start mb-2');
    if (role === 'user') {
      wrapper.innerHTML = `<div class="bubble user max-w-prose bg-white text-gray-900 p-6 rounded-2xl shadow-lg">${escapeHtml(content)}</div>`;
    } else {
      wrapper.innerHTML = `
        <div class="bubble assistant markdown-content typing" data-content="">
          <div class="flex space-x-2 justify-center items-center">
            <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.3s"></div>
            <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.15s"></div>
            <div class="h-2 w-2 bg-white rounded-full animate-bounce"></div>
          </div>
        </div>`;
    }
    chatBody.appendChild(wrapper);
    chatBody.scrollTop = chatBody.scrollHeight;
    return wrapper.querySelector('.bubble');
  }

  document.getElementById('guest-followup-form')?.addEventListener('submit', async e => {
    e.preventDefault();
    const inp  = e.target.querySelector('input[name="message"]');
    const text = inp.value.trim();
    if (!text) return;
    inp.value = '';

    // 1) ubaci user bubble
    appendBubble('user', text);

    // 2) ubaci loader bubble
    const loaderBubble = appendBubble('assistant');

    // 3) pošalji AJAX
    const res = await fetch(
      '{{ route("advisor.guest.chat.message", $tempChat->id) }}',
      {
        method : 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: text })
      }
    );
    const data = await res.json();

    // 4) ukloni loader
    loaderBubble.closest('.flex').remove();

    // 5) ubaci odgovor
    document.getElementById('chatBody')
            .insertAdjacentHTML('beforeend', data.responseHtml);

    // 6) rerender + tables + scroll
    renderMarkdown();
    styleTables();
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;

    // 7) ako je drugi upit, zameni footer sa CTA
    if (data.userCount >= 2) {
      document.getElementById('chatFooter').innerHTML = `
        <div class="buy-questions flex items-center justify-between">
          <p class="text-gray-300">Registruj se da bi nastavio konverzaciju</p>
          <a href="{{ route('register') }}" class="btn-orange px-4 py-2">Registruj se</a>
        </div>
      `;
    }
  });

  // inicijalna obrada postojećih poruka
  renderMarkdown();
  styleTables();
</script>

<style>
.advisor-responsive { overflow-x:auto; scrollbar-width:thin; }
.advisor-responsive::-webkit-scrollbar { height:6px }
.advisor-responsive::-webkit-scrollbar-thumb { background:theme('colors.gray.300'); border-radius:3px }
.prose ul li + li { margin-top:0.5rem }
.prose table { margin-top:1.5rem }
</style>
@endsection
