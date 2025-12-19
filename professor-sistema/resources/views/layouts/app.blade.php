<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Sistema Professor')</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js x-cloak -->
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-gray-100">
        @include('layouts.navigation')
        
        @yield('content')

        <!-- IMask Library -->
        <script src="https://unpkg.com/imask"></script>
        @stack('scripts')
    </body>
</html>
