@extends('layouts.app')

@section('content')
<div class="chat">
    <div class="flex items-center justify-between chat-header">
        <h1 class="page-title mb-0"> DIJAGNOZA</h1>
    </div>
    <div class="chat-container flex-1 overflow-y-auto mb-4">
    @foreach($tempQuestions as $q)
        <!-- Desni bubble (user) -->
        <div class="flex justify-end mb-2">
            <div class="bubble user animate-fadeIn">
                {{ $q->issueDescription }}
            </div>
        </div>

        @php
            $myResponses = $tempResponses->where('question_id', $q->id);
        @endphp

        @foreach($myResponses as $res)
            <!-- Levi bubble (assistant) -->
            <div class="flex justify-start mb-2 sm:mr-6 md:mb-4 lg:ml-2 xl:m-2">
                <div class="bubble assistant markdown-content animate-fadeIn"
                        data-content="{{ e($res->content) }}">
                </div>
            </div>
        </div>

            <!-- Poziv na registraciju -->
            <div class="flex justify-start">
                <div class="bg-black w-full register-for-more-questions text-white p-2 items-center justify-between flex">
                    <p class="uppercase font-black">Registruj se da bi dobio još 2 besplatna pitanja!</p>
                    <a href="{{ route('register') }}"
                       class="btn-orange text-black px-4 py-2 rounded hover:bg-orange-500">
                        Registruj se
                    </a>
                </div>
            </div>
        @endforeach
    @endforeach

    <!-- Popup za registraciju -->
    <div id="registerPopup" class="register-popup">
        <div class="register-popup-content">
            <button id="closePopup" class="close-btn">X</button>
            <img src="{{ asset('assets/images/logo-small.png') }}" alt="Logo" class="popup-logo">
            <h2>Registruj se sada i otključaj još 2 besplatna pitanja!</h2>
            <p class="timer-text">Ponuda važi još: <span id="timer">04:59</span>!</p>
            <div class="progress-bar">
                <div class="progress" style="width: 50%;"></div>
                <span class="progress-text">1/2</span>
            </div>
            <p class="benefits-title">Šta dobijate registracijom:</p>
            <ul class="benefits-list centered-benefits">
                <li>Dopisivanje sa AI mehaničarem</li>
                <li>Vođenje servisne istorije</li>
                <li>Istorija tvojih pitanja</li>
                <li>"Garaža" za tvoje automobile</li>
                <li>Dodatni popusti i povlastice</li>
            </ul>
            <p class="discount-text">🎁 Dobij kod za 20% popusta na bilo koji paket!</p>
            <a href="{{ route('register') }}" class="btn-orange popup-btn">Registruj se</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Markdown parsiranje za chat odgovore
        document.querySelectorAll('.markdown-content').forEach(function(el) {
            const originalText = el.dataset.content || '';
            const html = marked.parse(originalText);
            el.innerHTML = html;
        });

        // Popup logika
        const popup = document.getElementById('registerPopup');
        const closeBtn = document.getElementById('closePopup');
        const timerElement = document.getElementById('timer');
        let timeLeft = 300; // 5 minuta u sekundama

        // Prikazivanje popupa nakon 3 sekunde
        setTimeout(() => {
            popup.style.display = 'flex';
        }, 3000);

        // Zatvaranje popupa na klik na "X"
        closeBtn.addEventListener('click', () => {
            popup.style.display = 'none';
        });

        // Tajmer odbrojavanje
        const timerInterval = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                popup.style.display = 'none';
                return;
            }

            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    });
</script>
@endsection