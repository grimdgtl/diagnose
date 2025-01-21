<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diagnose App</title>
    <!-- Uključivanje CSS i JS preko Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome za ikone (ako koristiš CDN) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</head>
<body class="bg-gray-900 text-white">
    <div class="flex">
        @include('includes.sidebar')
        <div id="app" class="main-content flex-1">
            @yield('content')
        </div>
    </div>
    <!-- Uključivanje JS preko Vite -->
    @vite(['resources/js/app.js'])
</body>
</html>
