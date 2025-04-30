<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- ============================= -->
    <!-- 1. Primary Meta Tags        -->
    <!-- ============================= -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diagnose App</title>
    <meta name="description"
          content="Vaš virtuelni AI mehaničar i savetnik za kupovinu polovnih automobila: 24/7 dijagnostika kvarova, detaljni saveti i upoređivanje vozila.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- ============================= -->
    <!-- 2. Structured Data (JSON-LD) -->
    <!-- ============================= -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "Dijagnoza",
      "url": "https://dijagnoza.com",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://dijagnoza.com/pretraga?upit={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <!-- ============================= -->
    <!-- 3. Open Graph / Facebook     -->
    <!-- ============================= -->
    <meta property="og:title"        content="Dijagnoza">
    <meta property="og:description"  content="Jedina dijagnoza koja ti je stvarno potrebna">
    <meta property="og:image"        content="{{ asset('assets/images/share-image-min.png') }}">
    <meta property="og:url"          content="https://dijagnoza.com">
    <meta property="og:type"         content="website">
    <meta property="og:site_name"    content="Dijagnoza APP">

    <!-- ============================= -->
    <!-- 4. DNS Prefetch / Preconnect  -->
    <!-- ============================= -->
    <link rel="dns-prefetch" href="//www.googletagmanager.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect"   href="https://fonts.googleapis.com">
    <link rel="preconnect"   href="https://fonts.gstatic.com" crossorigin>

    <!-- ============================= -->
    <!-- 5. Favicons & Touch Icons     -->
    <!-- ============================= -->
    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-96x96.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/favicon.svg') }}">

    <!-- ============================= -->
    <!-- 6. Fonts & Preload Resources  -->
    <!-- ============================= -->
    <link rel="preload" href="/fonts/RedHatDisplay-Variable.woff2" as="font" type="font/woff2" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@300;400;600;700;900&display=swap"
          rel="stylesheet">

    <!-- ============================= -->
    <!-- 7. Styles (Vite & Libraries)  -->
    <!-- ============================= -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">

    <!-- ============================= -->
    <!-- 8. Head Scripts               -->
    <!-- ============================= -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- ============================= -->
    <!-- 9. Analytics & Tag Manager    -->
    <!-- ============================= -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DE9LD0WHPE"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){ dataLayer.push(arguments); }
      gtag('js', new Date());
      gtag('config', 'G-DE9LD0WHPE');
    </script>

    <!-- ============================= -->
    <!-- 10. Custom Inline Scripts     -->
    <!-- ============================= -->
    @lemonJS
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
