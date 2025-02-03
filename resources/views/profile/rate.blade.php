@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 my-12">
    <!-- Glavni kontejner sa gradient pozadinom -->
    <div class="bg-black rate-form from-gray-900 via-black to-gray-900 rounded-2xl p-6 relative overflow-hidden">
        <!-- Dekorativni elementi -->
        <div class="absolute -top-20 -right-20 w-48 h-48 bg-orange-500/10 rounded-full blur-xl"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-orange-500/10 rounded-full blur-xl"></div>

        <!-- Naslov sa animiranom ikonom -->
        <div class="text-center mb-8">
            <div class="inline-block bg-orange-500/20 p-4 rounded-2xl mb-4">
                <i class="fas fa-hand-holding-star text-4xl text-orange-500"></i>
            </div>
            <h1 class="text-3xl font-black bg-clip-text">
                Vaše mišljenje nas pokreće!
            </h1>
        </div>

        <!-- Objašnjenje -->
        <div class="text-center mb-8 space-y-4">
            <p class="text-gray-300 leading-relaxed">
                Svaka vaša zvezdica je kao gorivo za naš razvoj! 🔥<br>
                Ocenite koliko ste zadovoljni iskustvom i pomozite nam da budemo još bolji.
            </p>
        </div>

        <!-- Forma -->
        <form action="{{ route('profile.rate') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Zvezdice sa hover efektom -->
            <div class="group">
                <div class="flex justify-center space-x-4" id="ratingContainer">
                    @for($i = 1; $i <= 5; $i++)
                        <div class="relative star-wrapper" 
                             data-rating="{{ $i }}"
                             onmouseenter="hoverRating({{ $i }})"
                             onmouseleave="resetRating()"
                             ontouchstart="startTouchRating({{ $i }})"
                             ontouchend="resetRating()">
                            <button type="button"
                                    onclick="setRating({{ $i }})"
                                    class="star-button text-5xl transition-all duration-300 transform 
                                           text-gray-600 hover:text-orange-400 hover:scale-125 
                                           hover:rotate-12 hover:drop-shadow-[0_0_15px_rgba(255,92,0,0.4)]">
                                &#9733;
                            </button>
                            <span class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-orange-500 
                                      font-bold opacity-0 transition-opacity duration-200 text-sm">
                                {{ $i }}.0
                            </span>
                        </div>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="selectedRating" value="0" required>
            </div>

            <!-- Komentar sa karakter count-om -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <label class="text-orange-300 font-medium">Želite li dodati komentar?</label>
                    <span id="charCount" class="text-gray-500 text-sm">0/250</span>
                </div>
                <textarea name="feedback" id="feedback" rows="4"
                          class="w-full bg-black/40 border-2 border-orange-500/30 rounded-xl p-4 
                                 text-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/50 
                                 transition-all scrollbar-thin scrollbar-thumb-orange-500/30 scrollbar-track-black/20"
                          placeholder="Šta vam se posebno dopada? Šta bismo mogli poboljšati?"
                          maxlength="250"
                          oninput="updateCharCount(this)"></textarea>
            </div>

            <!-- Submit dugme -->
            <button type="submit" 
                    class="w-full btn-orange py-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl 
                           font-bold text-black uppercase tracking-wide transition-all 
                           hover:from-orange-400 hover:to-orange-500 hover:shadow-lg hover:shadow-orange-500/30
                           active:scale-95 transform">
                Pošalji ocenu 🚀
            </button>
        </form>
    </div>

    <!-- Poruka posle submita (hidden po defaultu) -->
    <div id="successMessage" class="hidden text-center p-6 bg-green-500/10 rounded-xl border-2 border-green-500/30">
        <i class="fas fa-check-circle text-3xl text-green-500 mb-4"></i>
        <p class="text-xl font-bold text-green-400">Hvala što delite svoje iskustvo! 💖</p>
        <p class="text-green-300 mt-2">Vaša ocena je uspešno zabeležena.</p>
    </div>
</div>

<script>
    let currentRating = 0;
    const stars = document.querySelectorAll('.star-wrapper');
    const hintElement = document.getElementById('ratingHint');

    // Hover efekat za desktop
    function hoverRating(rating) {
        if(currentRating === 0) {
            stars.forEach((star, index) => {
                const starButton = star.querySelector('.star-button');
                const tooltip = star.querySelector('span');
                if(index < rating) {
                    starButton.classList.add('text-orange-500', 'scale-110');
                    tooltip.classList.add('opacity-100');
                }
            });
        }
    }

    // Resetovanje stanja
    function resetRating() {
        stars.forEach((star, index) => {
            const starButton = star.querySelector('.star-button');
            const tooltip = star.querySelector('span');
            if(index < currentRating) {
                starButton.classList.add('text-orange-500');
                tooltip.classList.remove('opacity-100');
            } else {
                starButton.classList.remove('text-orange-500', 'scale-110');
                tooltip.classList.remove('opacity-100');
            }
        });
    }

    // Postavljanje ocene
    function setRating(rating) {
        currentRating = rating;
        document.getElementById('selectedRating').value = rating;
        stars.forEach((star, index) => {
            const starButton = star.querySelector('.star-button');
            starButton.classList.toggle('text-orange-500', index < rating);
        });
        hintElement.textContent = `Odabrali ste ${rating} zvezdice! 🌟`;
    }

    // Touch interakcija za mobilne uređaje
    let touchTimer;
    function startTouchRating(rating) {
        touchTimer = setTimeout(() => {
            hoverRating(rating);
        }, 300);
    }

    // Brojač karaktera
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        document.getElementById('charCount').textContent = `${count}/250`;
    }

    // Prikaz poruke posle submita (dodati u form submit handler)
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        this.classList.add('hidden');
        document.getElementById('successMessage').classList.remove('hidden');
        // Ovdje dodati stvarni AJAX poziv ili form submit
    });
</script>

<style>
    /* Custom animacija za zvezdice */
    @keyframes star-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .star-button:hover {
        animation: star-pulse 2s ease-in-out infinite;
    }

    /* Custom scrollbar za textarea */
    .scrollbar-thumb-orange-500/30::-webkit-scrollbar-thumb {
        background-color: rgba(255, 92, 0, 0.3);
        border-radius: 8px;
    }

    .scrollbar-track-black/20::-webkit-scrollbar-track {
        background-color: rgba(0, 0, 0, 0.2);
    }
</style>
@endsection