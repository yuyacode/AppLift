<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>AppLift</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <?php
            $manifest = json_decode(file_get_contents(public_path('company/build/manifest.json')), true);
            $appCssPath = $manifest['resources/css/app.css']['file'];
            $appJsPath = $manifest['resources/js/app.js']['file'];
        ?>
        <link rel="stylesheet" href="{{ asset('company/build/'.$appCssPath) }}">
        <link rel="stylesheet" href="{{ asset('company/build/assets/custom.css') }}">
        <script src="{{ asset('company/build/'.$appJsPath) }}" defer></script>
        @if (Request::is('company/message*'))
            <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script> -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-latest.min.js" defer></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout.mapping/2.4.1/knockout.mapping.min.js" defer></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.13/dayjs.min.js" defer></script>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
            <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
