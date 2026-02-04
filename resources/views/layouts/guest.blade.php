<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Login' }} - {{ config('app.name') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.scss', 'resources/js/app.js'])

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-8Q80MCTQ0L"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-8Q80MCTQ0L');
        </script>
    </head>
    <body class="bg-light">
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-4">
            <!-- Language Switcher -->
            <div class="position-absolute top-0 end-0 p-3">
                <div class="btn-group" role="group" aria-label="Language selector">
                    <a href="{{ route('language.switch', 'en') }}" 
                       class="btn {{ app()->getLocale() == 'en' ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center gap-1" 
                       title="English">
                        <span class="fi fi-gb" style="width: 1.2em; height: 1.2em; border-radius: 2px;"></span>
                        <span>EN</span>
                    </a>
                    <a href="{{ route('language.switch', 'nl') }}" 
                       class="btn {{ app()->getLocale() == 'nl' ? 'btn-primary' : 'btn-outline-secondary' }} d-flex align-items-center gap-1" 
                       title="Nederlands">
                        <span class="fi fi-nl" style="width: 1.2em; height: 1.2em; border-radius: 2px;"></span>
                        <span>NL</span>
                    </a>
                </div>
            </div>

            <div class="mb-4">
                <a href="/">
                    <x-application-logo class="" style="width: 160px; height: 160px;" />
                </a>
            </div>

            <div class="card" style="width: 100%; max-width: 28rem;">
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
