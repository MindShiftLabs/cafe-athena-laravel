<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Cafe Athena')</title>

    <!-- Tailwind and Global JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Role Specific CSS -->
    @stack('styles')
</head>
<body>

    <header>
        @include('partials.navbar')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        @include('partials.footer')
    </footer>

</body>
</html>
