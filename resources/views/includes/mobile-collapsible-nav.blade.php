<div class="mobile-nav-collapsible block md:hidden">

    <!-- 1) “Hamburger Hack” checkbox (skriven) -->
    <input type="checkbox" id="mobile-menu-toggle" class="hidden" />

    <!-- 2) Header (logo i hamburger) -->
    <header class="mobile-header bg-black px-4 py-2">
        <div class="flex items-center justify-between">
            <!-- Logo levo -->
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('assets/images/logo-neon.png') }}" alt="Logo" class="h-14" />
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
    <nav class="collapse-nav bg-black orange-shadow overflow-hidden">
        <!-- Isti linkovi kao u sidebaru -->
        @auth
            <div class="collapsible-link">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i> Dijagnoza
                </a>
            </div>

            <div class="collapsible-link">
                <a href="{{ route('chat.new') }}">
                    <i class="fas fa-comments"></i> Novi Chat
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
                    <a href="{{ route('profile.garage') }}">Moja garaža</a>
                    <a href="{{ route('profile.history') }}">Istorija</a>
                    <a href="{{ route('profile.showRateForm') }}">Oceni app</a>
                </div>
            </div>

            <div class="collapsible-link">
                <a href="{{ route('profile.subscription') }}">
                    <i class="fas fa-suitcase"></i> Subscription
                </a>
            </div>
            <div class="collapsible-link">
                <a href="{{ route('support') }}">
                    <i class="fas fa-life-ring"></i> Support
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
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i> Dijagnoza
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
