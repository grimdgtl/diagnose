<!-- mobile-nav.blade.php -->
<div class="mobile-nav-wrapper block md:hidden">
    <!-- Checkbox za "hamburger hack" -->
    <input type="checkbox" id="mobile-nav-toggle" class="hidden" />

    <!-- Gornja traka (logo levo, hamburger desno) -->
    <header class="mobile-nav flex items-center justify-between bg-black px-4 py-2 border-b-2 border-orange">
        <!-- Logo (levo) -->
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/images/logo-neon.png') }}" alt="Logo" class="h-12" />
            </a>
        </div>

        <!-- Hamburger (desno) - dve linije -->
        <label for="mobile-nav-toggle" 
               class="mobile-hamburger cursor-pointer flex flex-col items-center justify-center">
            <span class="mobile-hamburger-line"></span>
            <span class="mobile-hamburger-line"></span>
        </label>
    </header>
    <!-- "Slide-in" meni: sadrži iste linkove kao tvoj sidebar -->
    <nav class="mobile-menu fixed top-0 left-0 h-screen w-3/4 max-w-xs bg-black z-50 transform -translate-x-full transition-transform duration-300 overflow-y-auto border-r-2 border-orange" 
         style="padding-top: 4rem;">
        <!-- Opcioni “zatvori” button (ako želiš), ili klikni na hamburger ponovo da se zatvori -->
        <label for="mobile-nav-toggle" class="absolute top-4 right-4 text-white text-2xl font-bold cursor-pointer">×</label>

        <!-- Linkovi (isti kao u sidebar) -->
        @auth
            <div class="mobile-nav-button">
                <a href="{{ route('dashboard') }}" class="mobile-link">
                    <i class="fas fa-diagnoses"></i> Dijagnoza
                </a>
            </div>

            <div class="mobile-nav-button">
                <a href="{{ route('chat.new') }}" class="mobile-link">
                    <i class="fas fa-comments"></i> Novi Chat
                </a>
            </div>

            <!-- Moj Profil s podmenijem (checkbox hack) -->
            <div class="mobile-nav-button relative">
                <input type="checkbox" id="mobile-profile-submenu" class="hidden" />
                <label for="mobile-profile-submenu" class="mobile-link block cursor-pointer">
                    <i class="fas fa-user"></i> Moj Profil
                </label>
                <div class="mobile-submenu ml-6 border-l border-orange hidden">
                    <a href="{{ route('profile.my-data') }}">Moji podaci</a>
                    <a href="{{ route('profile.garage') }}">Moja garaža</a>
                    <a href="{{ route('profile.history') }}">Istorija</a>
                    <a href="{{ route('profile.showRateForm') }}">Oceni app</a>
                </div>
            </div>

            <div class="mobile-nav-button">
                <a href="{{ route('profile.subscription') }}" class="mobile-link">
                    <i class="fas fa-suitcase"></i> Subscription
                </a>
            </div>

            <div class="mobile-nav-button">
                <a href="{{ route('support') }}" class="mobile-link">
                    <i class="fas fa-life-ring"></i> Support
                </a>
            </div>

            <!-- Logout -->
            <div class="mobile-nav-button">
                <a href="{{ route('logout') }}" class="mobile-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fas fa-sign-out-alt"></i> Odjavi se
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        @else
            <div class="mobile-nav-button">
                <a href="{{ route('dashboard') }}" class="mobile-link">
                    <i class="fas fa-diagnoses"></i> Dijagnoza
                </a>
            </div>
            <div class="mobile-nav-button">
                <a href="{{ route('login') }}" class="mobile-link">
                    <i class="fas fa-sign-in-alt"></i> Prijavi se
                </a>
            </div>
        @endauth
    </nav>
</div>
