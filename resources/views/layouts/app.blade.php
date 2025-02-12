<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnose App</title>
    <!-- Uključivanje CSS i JS preko Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts (Red Hat Display) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Font Awesome (ikonice) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

    <!-- Alpine.js (po potrebi) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="layout-container">
        <!-- Checkbox koji kontroliše sidebar -->
        <input type="checkbox" id="nav-toggle" class="nav-toggle-checkbox" />

        <!-- Desktop Sidebar -->
            @include('includes.sidebar')

        <!-- Mobile Navigation -->
        @include('includes.mobile-collapsible-nav') 

        <!-- Glavni sadržaj -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>
    @vite(['resources/js/app.js'])
</body>
</html>
