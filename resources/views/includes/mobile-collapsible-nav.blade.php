<div class="mobile-nav-collapsible block md:hidden">

    <!-- 1) “Hamburger Hack” checkbox (skriven) -->
    <input type="checkbox" id="mobile-menu-toggle" class="hidden" />

    <!-- 2) Header (logo i hamburger) -->
    <header class="mobile-header bg-black pr-2 py-2">
        <div class="flex items-center justify-between">
            <!-- Logo levo -->
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('assets/images/logo-small.png') }}" alt="Logo" class="h-14" />
                <!-- Po želji text: <span class="text-orange font-bold ml-2">Dijagnoza</span> -->
            </a>

            <!-- Hamburger desno (label za checkbox) -->
            <label for="mobile-menu-toggle" class="hamburger cursor-pointer flex flex-col justify-center">
                <!-- Možeš koristiti 2 ili 3 linije. Ovde 3 za “klasični hamburger”. -->
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </label>
        </div>
    </header>

    <!-- 3) Navigacija koja se “collapsuje” unutar headera -->
    <!-- Kad je #mobile-menu-toggle:checked, menja se .collapse-nav (max-height) -->
    <nav class="collapse-nav bg-black border-orange overflow-hidden">
        <!-- Isti linkovi kao u sidebaru -->
        @auth
            <div class="collapsible-link">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Početna
                </a>
            </div>
            
            <!-- Dijagnoza sa podmenijem: moze i ovde “checkbox hack” 2. nivo, ali da pojednostavimo: 
                 samo linkovi ispod. Ili isprobaj “accordions”. -->
            <div class="collapsible-link submenu-section">
                <input type="checkbox" id="mobile-dijagnoza-submenu" class="hidden" />
                <label for="mobile-dijagnoza-submenu" class="dijagnoza-submenu-label">
                    <i class="fa-solid fa-screwdriver-wrench"></i> Virtuelni mehaničar
                </label>
                <div class="dijagnoza-submenu-items">
                    <a href="{{ route('dashboard') }}">Aktivni Chat</a>
                    <a href="{{ route('chat.new') }}">Novi Chat</a>
                    <a href="{{ route('profile.history') }}">Istorija</a>
                    <a href="{{ route('profile.garage') }}">Moja Garaža</a>
                </div>
            </div>

            <!-- Advisor sa podmenijem: moze i ovde “checkbox hack” 2. nivo, ali da pojednostavimo: 
                 samo linkovi ispod. Ili isprobaj “accordions”. -->
            <div class="collapsible-link submenu-section">
                <input type="checkbox" id="mobile-advisor-submenu" class="hidden" />
                <label for="mobile-advisor-submenu" class="advisor-submenu-label">
                    <i class="fas fa-car-side"></i> Savetnik za kupovinu
                </label>
                <div class="advisor-submenu-items">
                    @php
                        // pokušaj da preuzmeš model/id iz URI-ja
                        $currentChat = request()->route('purchaseChat');
                    @endphp

                    {{-- Prikaži link “Novo poređenje” samo ako je chat otvoren --}}
                    @if($currentChat)
                        <form id="archive-form"
                            action="{{ route('advisor.chat.archive', $currentChat) }}"
                            method="POST"
                            style="display:none">
                            @csrf
                        </form>
                        <a href="#"
                            onclick="event.preventDefault(); document.getElementById('archive-form').submit();"
                            class="submenu-link">
                            Novo poređenje
                        </a>
                    @endif
                    <a href="{{ route('advisor.chatOrWizard') }}">Aktivni Chat</a>
                    <a href="{{ route('advisor.history') }}">Istorija</a>
                </div>
            </div>

            <div class="collapsible-link">
                <a href="{{ route('service-book.index') }}">
                    <i class="fa-solid fa-book"></i> Servisna Knjiga
                </a>
            </div>

             <div class="collapsible-link">
                <a href="{{ route('profile.garage') }}">
                    <i class="fa-solid fa-warehouse"></i> Moja Garaža
                </a>
            </div>

            <!-- Moj Profil sa podmenijem: moze i ovde “checkbox hack” 2. nivo, ali da pojednostavimo: 
                 samo linkovi ispod. Ili isprobaj “accordions”. -->
            <div class="collapsible-link submenu-section">
                <input type="checkbox" id="mobile-profile-submenu" class="hidden" />
                <label for="mobile-profile-submenu" class="submenu-label">
                    <i class="fas fa-user"></i> Moj Profil
                </label>
                <div class="submenu-items">
                    <a href="{{ route('profile.my-data') }}">Moji podaci</a>
                    <a href="{{ route('profile.showRateForm') }}">Oceni app</a>
                </div>
            </div>

            <div class="collapsible-link">
                <a href="{{ route('profile.subscription') }}">
                    <i class="fa-solid fa-cart-shopping"></i> Kupi tokene
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('support') }}">
                    <i class="fa-solid fa-headset"></i> Pordrška
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fas fa-sign-out-alt"></i> Odjavi se
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        @else
            <div class="collapsible-link">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Početna
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('guest.wizard-form') }}">
                    <i class="fa-solid fa-screwdriver-wrench"></i> Virtuelni Mehaničar
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('advisor.guest.wizard') }}">
                    <i class="fas fa-car-side"></i> Savetnik za Kupovinu
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i> Prijavi se
                </a>
            </div>
        @endauth
    </nav>

</div>
