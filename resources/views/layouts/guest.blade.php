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
        <div class="fmc-auth-wrap">
            <x-topo-bg />
            <div class="auth-col">
                <div class="auth-head">
                    <h1>Fix My Community</h1>
                    <p>Local issues. Shared progress.</p>
                </div>
                <div class="auth-card">
                    @if (request()->routeIs('login', 'register'))
                        <div class="seg" style="display:flex" role="navigation" aria-label="Account">
                            <a href="{{ route('login') }}" style="flex:1;text-align:center;text-decoration:none" class="segl" @if(request()->routeIs('login')) aria-current="page" @endif>Sign in</a>
                            <a href="{{ route('register') }}" style="flex:1;text-align:center;text-decoration:none" class="segl" @if(request()->routeIs('register')) aria-current="page" @endif>Create account</a>
                        </div>
                    @endif
                    {{ $slot }}
                </div>
                <p class="auth-foot">Your reports can be submitted anonymously to the community.</p>
            </div>
        </div>
    </body>
</html>
