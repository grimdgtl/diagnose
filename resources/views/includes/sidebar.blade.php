<!-- resources/views/includes/sidebar.blade.php -->
<nav id="nav-bar">
    <div id="nav-header">
        <!-- Logo sekcija -->
        <div class="sidebar-logo">
            <!-- Logo slika -->
            <a href="{{ route('home') }}" class="logo-img">
                <img src="{{ asset('assets/images/logo-small.png') }}">
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
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i>
                    <span class="link-text">Početna</span>
                </a>
            </div>
            <!-- Dijagnoza sa podmenijem -->
            <div class="nav-button dijagnoza-submenu-section">
                <input type="checkbox" id="dijagnoza-submenu" hidden>
                <label for="dijagnoza-submenu" class="dijagnoza-menu-label">
                    <i class="fas fa-diagnoses"></i>
                    <span class="link-text">Virtuelni mehaničar</span>
                </label>
                <div class="dijagnoza-submenu">
                    <a href="{{ route('dashboard') }}">Aktivni Chat</a>
                    <a href="{{ route('chat.new') }}">Novi Chat</a>
                    <a href="{{ route('profile.history') }}">Istorija</a>
                    <a href="{{ route('profile.garage') }}">Moja Garaža</a>
                </div>
            </div>

            <!-- Savetnik sa podmenijem -->
            <div class="nav-button advisor-submenu-section">
                <input type="checkbox" id="used-cars-submenu" hidden>
                <label for="used-cars-submenu" class="advisor-menu-label">
                    <i class="fas fa-car-side"></i>
                    <span class="link-text">Savetnik za kupovinu</span>
                </label>
                <div class="advisor-submenu">
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

                    {{-- Uvek dostupni ostali linkovi --}}
                    <a href="{{ route('advisor.chatOrWizard') }}">Aktivni Chat</a>
                    <a href="{{ route('advisor.history') }}">Istorija</a>
                </div>
            </div>

            <div class="nav-button">
                <a href="{{ route('service-book.index') }}">
                    <i class="fas fa-book"></i> <!-- FontAwesome ikonica za knjigu -->
                    <span class="link-text">Servisna Knjiga</span>
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
                    <a href="{{ route('profile.my-data') }}">Moji Podaci</a>
                    <a href="{{ route('profile.showRateForm') }}">Oceni Aplikaciju</a>
                </div>
            </div>

            <div class="nav-button">
                <a href="{{ route('profile.subscription') }}">
                    <i class="fas fa-suitcase"></i>
                    <span class="link-text">Kupi tokene</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('support') }}">
                    <i class="fas fa-life-ring"></i>
                    <span class="link-text">Podrška</span>
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
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i>
                    <span class="link-text">Početna</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('guest.wizard-form') }}">
                    <i class="fas fa-diagnoses"></i>
                    <span class="link-text">Virtuelni Mehaničar</span>
                </a>
            </div>
            <div class="nav-button">
                <a href="{{ route('advisor.guest.wizard') }}">
                    <i class="fas fa-car-side"></i>
                    <span class="link-text">Savetnik za Kupovinu</span>
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
