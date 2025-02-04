<!-- resources/views/includes/sidebar.blade.php -->
<nav id="nav-bar">
    <div id="nav-header">
        <!-- Logo sekcija -->
        <div class="sidebar-logo">
            <!-- Logo slika -->
            <a href="{{ route('dashboard') }}" class="logo-img">
                <img src="{{ asset('images/logo-neon.png') }}">
            </a>
        </div>

        <!-- NOVI Hamburger dugme (3 linije umesto .fa-bars) -->
        <label for="nav-toggle" class="hamburger-label">
            <span class="hamburger-line"></span>
        </label>
    </div>

    <!-- Linkovi -->
    <div id="nav-content">
        @auth
            <div class="nav-button">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i>
                    <span class="link-text">Dijagnoza</span>
                </a>
            </div>

            <div class="nav-button">
                <a href="{{ route('chat.new') }}">
                    <i class="fas fa-comments"></i>
                    <span class="link-text">Novi Chat</span>
                </a>
            </div>

            <!-- Moj Profil sa podmenijem -->
            <div class="nav-button profile-submenu-section">
                <input type="checkbox" id="profile-submenu-checkbox" hidden>
                <label for="profile-submenu-checkbox" class="profile-menu-label">
                    <i class="fas fa-user"></i>
                    <span class="link-text">Moj Profil</span>
                </label>
                <div class="profile-submenu">
                    <a href="{{ route('profile.my-data') }}">Moji podaci</a>
                    <a href="{{ route('profile.garage') }}">Moja garaža</a>
                    <a href="{{ route('profile.history') }}">Istorija</a>
                    <a href="{{ route('profile.showRateForm') }}">Oceni app</a>
                </div>
            </div>

            <div class="nav-button">
                <a href="{{ route('profile.subscription') }}">
                    <i class="fas fa-suitcase"></i>
                    <span class="link-text">Subscription</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('support') }}">
                    <i class="fas fa-life-ring"></i>
                    <span class="link-text">Support</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="link-text">Odjavi se</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        @else
            <div class="nav-button">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i>
                    <span class="link-text">Dijagnoza</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="link-text">Prijavi se</span>
                </a>
            </div>
        @endauth
    </div>
</nav>
