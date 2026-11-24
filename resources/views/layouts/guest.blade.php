<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 font-sans text-stone-900 antialiased">
<div class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Titik Temu Kopi" class="mx-auto h-18 w-18 rounded-full border border-stone-200 object-cover shadow-sm">
            <h1 class="mt-3 text-2xl font-bold">Titik Temu Kopi</h1>
            <p class="text-sm text-stone-500">Sistem Kasir &amp; Pemesanan</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
            @include('partials.alert')
            @yield('content')
        </div>
        <p class="mt-4 text-center text-xs text-stone-400">Masuk dengan akun Administrator atau Kasir.</p>
    </div>
</div>
</body>
</html>
