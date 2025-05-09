{{-- resources/views/chat/guest-dashboard.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="chat relative">
  <div class="flex items-center justify-between chat-header">
    <h1 class="page-title mb-0">DIJAGNOZA</h1>
  </div>

  {{-- glavni kontejner za bubli --}}
  <div id="guest-chat-container" class="chat-container flex-1 overflow-y-auto mb-4">
    @foreach($tempQuestions as $q)
      {{-- korisnikovo pitanje --}}
      <div class="flex justify-end mb-2">
        <div class="bubble user animate-fadeIn">
          {{ $q->issueDescription }}
        </div>
      </div>

      @php
        $myResponses = $tempResponses->where('question_id', $q->id);
      @endphp

      @foreach($myResponses as $res)
        {{-- ChatGPT odgovor --}}
        <div class="flex justify-start mb-2">
          <div class="bubble assistant markdown-content animate-fadeIn"
               data-content="{{ e($res->content) }}">
          </div>
        </div>
      @endforeach
    @endforeach
  </div>

  {{-- ovde ćemo ubaciti input za 2. pitanje --}}
  <div id="guest-chat-input-area" class="mb-4"></div>

  {{-- CTA za registraciju, sakriven dok ne dođe 2 pitanja --}}
  <div id="guest-register-cta" style="display: none;">
    <div class="bg-black w-full register-for-more-questions text-white p-2 flex items-center justify-between">
      <p class="uppercase font-black">Registruj se da bi dobio još 2 besplatna pitanja!</p>
      <a href="{{ route('register') }}"
         class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
        Registruj se
      </a>
    </div>
    </div>
      <div id="registerPopup" class="register-popup">
        <div class="register-popup-content">
            <button id="closePopup" class="close-btn">X</button>
            <img src="{{ asset('assets/images/logo-small.png') }}" alt="Logo" class="popup-logo">
            <h2>Registruj se sada i dobijas 2 besplatna tokena!</h2>
            <p class="timer-text">Ponuda važi još: <span id="timer">04:59</span>!</p>
            <div class="progress-bar">
                <div class="progress" style="width: 50%;"></div>
                <span class="progress-text">1/2</span>
            </div>
            <p class="benefits-title">Šta dobijate registracijom:</p>
            <ul class="benefits-list centered-benefits">
                <li><p>Ličnog virtuelnog mehaničara</p></li>
                <li><p>Savetnik za kupovinu automobila</p></li>
                <li><p>Vođenje servisne istorije</p></li>
                <li><p>Istorija tvojih pitanja</p></li>
                <li><p>"Garaža" za tvoje automobile</p></li>
                <li><p>Dodatni popusti i povlastice</p></li>
            </ul>
            <p class="discount-text">🎁 Osvoji kod za 20% popusta na bilo koji paket!</p>
            <a href="{{ route('register') }}" class="btn-orange popup-btn">Registruj se</a>
        </div>
    </div>
</div>

{{-- marked.js za parsiranje Markdowna --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Parsiraj postojeće odgovore
  document.querySelectorAll('.markdown-content').forEach(el => {
    const txt = el.dataset.content || '';
    if (txt) el.innerHTML = marked.parse(txt);
  });

  const chatContainer = document.getElementById('guest-chat-container');
  const inputArea     = document.getElementById('guest-chat-input-area');
  const registerCta   = document.getElementById('guest-register-cta');

  let questionsCount = {{ $tempQuestions->count() }};
  const csrfToken     = '{{ csrf_token() }}';
  const extraUrl      = '{{ route("guest.store-extra-question") }}';

  function escapeHtml(str) {
    return str.replace(/&/g,'&amp;')
              .replace(/</g,'&lt;')
              .replace(/>/g,'&gt;')
              .replace(/"/g,'&quot;');
  }

    // popup elementi
    const popup        = document.getElementById('registerPopup');
    const closeBtn     = document.getElementById('closePopup');
    const timerElement = document.getElementById('timer');
    
    // Zatvaranje popupa na klik na "X"
    closeBtn.addEventListener('click', () => {
      popup.style.display = 'none';
    });
    
    // funkcija koja prikazuje popup i startuje odbrojavanje
    function showRegisterPopup() {
      let timeLeft = 300; // 5 minuta
      popup.style.display = 'flex';
    
    // odmah postavi inicijalni timer
    timerElement.textContent = '05:00';
    
    const timerInterval = setInterval(() => {
      if (timeLeft <= 0) {
        clearInterval(timerInterval);
        popup.style.display = 'none';
        return;
      }
      timeLeft--;
      const minutes = Math.floor(timeLeft / 60);
      const seconds = timeLeft % 60;
      timerElement.textContent =
        `${minutes.toString().padStart(2,'0')}:${seconds.toString().padStart(2,'0')}`;
        }, 1000);
    }


    function updateView() {
        inputArea.innerHTML = '';
        registerCta.style.display = 'none';

        if (questionsCount < 2) {
            inputArea.innerHTML = `
            <form id="extra-question-form" class="chat-input flex items-center">
              <input type="text" name="issueDescription" class="new-message-field flex-1" placeholder="Unesi dodatno pitanje" />
              <button type="submit" class="btn-orange send-message text-black hover:bg-orange-500 ml-2">
                <i class="fa fa-paper-plane"></i>
              </button>
            </form>`;
          document.getElementById('extra-question-form')
                  .addEventListener('submit', handleSubmit);
        } else {
            registerCta.style.display = 'block';
            // prikaži popup 10s nakon drugog pitanja
            setTimeout(showRegisterPopup, 5000);
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const form  = e.target;
        const field = form.querySelector('input[name="issueDescription"]');
        const text  = field.value.trim();
        if (!text) return;

        field.value = '';

        // Prikaži user bubble
        chatContainer.insertAdjacentHTML('beforeend',
          `<div class="flex justify-end animate-fadeIn">
             <div class="bubble user">${escapeHtml(text)}</div>
           </div>`);

        // Prikaži loader
        chatContainer.insertAdjacentHTML('beforeend',
          `<div class="flex justify-start animate-fadeIn mb-2">
             <div class="bubble assistant typing">
               <div class="flex space-x-2 justify-center items-center">
                 <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.3s"></div>
                 <div class="h-2 w-2 bg-white rounded-full animate-bounce" style="animation-delay:-0.15s"></div>
                 <div class="h-2 w-2 bg-white rounded-full animate-bounce"></div>
               </div>
             </div>
           </div>`);

        chatContainer.scrollTop = chatContainer.scrollHeight;

        try {
          const payload = new URLSearchParams();
          payload.append('_token', csrfToken);
          payload.append('issueDescription', text);

          const response = await fetch(extraUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: payload
          });

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Greška pri slanju pitanja.');

        // Ukloni loader
        const typing = chatContainer.querySelector('.bubble.assistant.typing');
        if (typing) typing.closest('.flex').remove();

        // Dodaj stvarne bubli
        chatContainer.insertAdjacentHTML('beforeend',
          data.responseHtml);

        // Parsiraj Markdown novog odgovora
        document.querySelectorAll('.bubble.assistant.markdown-content').forEach(el => {
          const txt = el.dataset.content || '';
          if (txt) el.innerHTML = marked.parse(txt);
        });

        questionsCount = data.questionsCount;
        updateView();
        chatContainer.scrollTop = chatContainer.scrollHeight;

        } catch (err) {
          // Prikaži grešku u loader bublu
          const errorBubble = chatContainer.querySelector('.bubble.assistant.typing');
          if (errorBubble) {
            errorBubble.textContent = err.message;
            errorBubble.classList.remove('typing');
          }
        }
    }

  updateView();
});
</script>
@endsection
