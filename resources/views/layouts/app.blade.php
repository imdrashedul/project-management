<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-purple-025">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-purple-05 shadow">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">{{ $header }}</div>
                        @isset($headerButton)
                            <div class="flex gap-2">{{ $headerButton }}</div>
                        @endisset
                    </div>
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            <x-push-notification />
            {{ $slot }}
        </main>
    </div>
</body>

</html>
