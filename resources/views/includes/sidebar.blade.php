<!-- resources/views/layouts/sidebar.blade.php -->
<div id="nav-bar">
    <input type="checkbox" id="nav-toggle">
    <div id="nav-header">
        <a href="{{ route('dashboard') }}" id="nav-title">
            <img src="{{ Vite::asset('resources/images/logo-neon.png') }}" alt="Logo">
        </a>
        <label for="nav-toggle">
            <span id="nav-toggle-burger"></span>
        </label>
    </div>
    <div id="nav-content">
        @auth
            <div class="nav-button">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i>
                    <span>Dijagnoza</span>
                </a>
            </div>
            <hr>
            <div class="nav-button">
                <a href="{{ route('chat.new') }}">
                    <i class="fas fa-comments"></i>
                    <span>Novi Chat</span>
                </a>
            </div>
            <!-- Moj Profil sa podmenijima -->
            <div class="nav-button profile-submenu-toggle">
                <a href="javascript:void(0);">
                    <i class="fas fa-user"></i>
                    <span>Moj Profil</span>
                </a>
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
                    <span>Subscription</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('support') }}">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Odjavi se</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        @else
            <div class="nav-button">
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-diagnoses"></i>
                    <span>Dijagnoza</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Prijavi se</span>
                </a>
            </div>
        @endauth
    </div>
</div>
