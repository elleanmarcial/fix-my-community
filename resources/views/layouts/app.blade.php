<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Fix My Community') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/fmc.css') }}">
    </head>
    <body class="fmc">
        @include('layouts.navigation')

        <main class="wrap">
            @isset($header)
                <div class="fmc-title">{{ $header }}</div>
            @endisset

            {{ $slot }}
        </main>

        <div class="sr" role="status" aria-live="polite" id="live">{{ session('success') }}</div>
        <script src="{{ asset('js/fmc.js') }}" defer></script>
        @stack('scripts')
    </body>
</html>
