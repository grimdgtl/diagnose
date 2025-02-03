@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-12 px-4 sm:px-6 lg:px-8">

    <!-- FAQ sekcija -->
    <div class="grid lg:grid-cols-1 gap-8 mb-8">
        <div class="bg-black p-8 rounded-2xl border-orange-500/30">
            <div class="flex items-center gap-4">
                    <div class="pl-4 pr-4 bg-orange-500/10 rounded-xl">
                        <i class="fas fa-lightbulb text-3xl text-orange-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-orange-500">Često postavljana pitanja:</h2>
                </div>
            <div class="grid md:grid-cols-2 gap-4 pl-16">
                @foreach($faqs as $faq)
                    <div class="group relative bg-gray-800 rounded-xl p-2 hover:bg-gray-700 transition-colors cursor-pointer"
                         x-data="{ open: false }"
                         @click="open = !open">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-white">{{ $faq['question'] }}</h3>
                            <i class="fas fa-chevron-down text-white-500 transition-transform" 
                               :class="{ 'rotate-180': open }"></i>
                        </div>
                        <div class="overflow-hidden transition-all duration-300 ease-out"
                             :style="open ? 'max-height: ' + $refs.content.scrollHeight + 'px' : 'max-height: 0'">
                            <div class="pt-4 text-gray-300" x-ref="content">
                                {{ $faq['answer'] }}
                                @isset($faq['link'])
                                    <a href="{{ $faq['link'] }}" class="text-orange-400 hover:underline block mt-2">
                                        Saznajte više ➔
                                    </a>
                                @endisset
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    <!-- Glavni sadržaj -->
        <!-- Kontakt sekcija -->
        <div class="bg-black from-gray-900 to-black p-8 rounded-2xl border-orange-500/30 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-orange-500/10 rounded-full blur-xl"></div>
            
            <div class="relative space-y-6">
                <div class="flex justify-between items-end">
                    <div class="flex items-center gap-4">
                        <div class="pl-4 pr-4 bg-orange-500/10 rounded-xl">
                            <i class="fas fa-headset text-3xl text-orange-500"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-orange-500">Direktni Kontakt</h2>
                    </div>
                    <!-- Status podrške -->
                    <div class="bg-gray-800 rounded-xl">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="flex-shrink-0">
                                <div class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-400">Trenutni odziv: 2.3h ⚡</p>
                                <p class="text-xs text-gray-400">Prosečno vreme odgovora</p>
                            </div>
                        </div>
                    </div>
                </div>    

                <!-- Email sekcija -->
                <div class="group relative pl-20 bg-gray-800 rounded-xl hover:bg-gray-700 transition-colors cursor-pointer" 
                     onclick="copyEmail()"
                     data-tippy-content="Klikni za kopiranje">
                    <div class="flex items-center gap-4">
                        <div>
                            <p class="text-sm text-gray-400">E-mail za podršku:</p>
                            <p id="email" class="text-lg font-medium text-orange-400 transition-colors">
                                support@dijagnoza.com
                            </p>
                        </div>
                    </div>
                    <span class="absolute top-2 right-2 text-xs text-orange-500/50 hidden group-hover:block">
                        Kopiraj
                    </span>
                </div>
            </div>
        </div>

        <!-- Forma za podršku -->
        <div class="support-form bg-black p-8 rounded-2xl border-orange-500/30 relative">
            <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-orange-500/10 rounded-full blur-xl"></div>
                <div class="flex items-center gap-4">
                    <div class="pl-4 pr-4 bg-orange-500/10 rounded-xl">
                        <i class="fas fa-envelope-open-text text-3xl text-orange-500"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-orange-500">Prijavi problem</h2>
                </div>
            <form action="{{ route('support.submit') }}" method="POST" class="space-y-6 pl-20" id="supportForm">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <input type="text" name="name" required 
                               class="input-field bg-gray-800 hover:bg-gray-700 focus:border-orange-500"
                               placeholder="Vaše ime i prezime"
                               x-data="{}"
                               x-on:input="document.getElementById('nameError').classList.add('hidden')">
                        <p id="nameError" class="text-red-400 text-sm mt-1 hidden">Morate uneti ime</p>
                    </div>
                    
                    <div>
                        <input type="email" name="email" required 
                               class="input-field bg-gray-800 hover:bg-gray-700 focus:border-orange-500"
                               placeholder="Vaš Email"
                               x-data="{}"
                               x-on:input="validateEmail(this.value)">
                        <p id="emailError" class="text-red-400 text-sm mt-1 hidden">Unesite validan email</p>
                    </div>
                </div>

                <!-- Poruka sa brojačem karaktera -->
                <div>
                    <textarea name="message" rows="6" required maxlength="500"
                              class="input-field bg-gray-800 hover:bg-gray-700 focus:border-orange-500"
                              placeholder="Opišite problem sa što više detalja..."
                              id="messageInput"
                              x-data="{}"
                              x-on:input="updateCharCount(this)"></textarea>
                    <div class="flex justify-between mt-1">
                        <span class="text-sm text-gray-500">Preporučena minimalna dužina: 50 karaktera</span>
                        <span id="charCount" class="text-sm text-gray-500">0/500</span>
                    </div>
                </div>

                <!-- Dugme za submit -->
                <button type="submit" 
                        class="w-full btn-orange py-4 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl 
                               font-bold text-black uppercase tracking-wide transition-all 
                               hover:from-orange-400 hover:to-orange-500 hover:shadow-lg hover:shadow-orange-500/30
                               active:scale-95 transform relative">
                    <span class="relative z-10">Pošalji Zahtev</span>
                    <div class="absolute inset-0 bg-orange-500/0 hover:bg-orange-500/10 transition"></div>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // Kopiranje emaila
    function copyEmail() {
        const email = document.getElementById('email');
        navigator.clipboard.writeText(email.innerText).then(() => {
            const originalText = email.innerText;
            email.innerText = "✓ Email kopiran!";
            email.classList.add('text-green-400');
            setTimeout(() => {
                email.innerText = originalText;
                email.classList.remove('text-green-400');
            }, 2000);
        });
    }

    // Validacija emaila
    function validateEmail(email) {
        const emailError = document.getElementById('emailError');
        const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        emailError.classList.toggle('hidden', isValid);
        return isValid;
    }

    // Brojač karaktera
    function updateCharCount(textarea) {
        const count = textarea.value.length;
        document.getElementById('charCount').textContent = `${count}/500`;
        const charCount = document.getElementById('charCount');
        charCount.classList.toggle('text-orange-400', count > 50);
        charCount.classList.toggle('text-gray-500', count <= 50);
    }

    // Forma validacija
    document.getElementById('supportForm').addEventListener('submit', function(e) {
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');
        
        if(nameInput.value.trim() === '') {
            document.getElementById('nameError').classList.remove('hidden');
            e.preventDefault();
        }
        
        if(!validateEmail(emailInput.value)) {
            e.preventDefault();
        }
    });
</script>

<style>
    /* Animacije */
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fade-in 0.5s ease-out;
    }

    /* Custom input efekti */
    .input-field {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input-field:focus {
        box-shadow: 0 0 0 3px rgba(255, 92, 0, 0.3);
    }
</style>
@endsection