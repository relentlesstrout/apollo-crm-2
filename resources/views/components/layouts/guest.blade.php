<!DOCTYPE html>
<html class="h-full" lang="en-GB">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
    <script src="https://js.stripe.com/v3/"></script>
    <title>Apollo Window Cleaners — Checkout</title>
</head>
<body class="bg-slate-50 min-h-full flex items-center justify-center py-12 px-4">
    {{ $slot }}
</body>
</html>
