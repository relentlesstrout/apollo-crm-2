<!DOCTYPE html>
<html class="h-full" lang="en-GB">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Apollo Window Cleaners</title>
</head>
<body class="pb-8">
    <x-navbar/>
    <main class="h-full mx-30">
        @include('partials.flash')
        {{ $slot }}
    </main>
</body>
</html>
