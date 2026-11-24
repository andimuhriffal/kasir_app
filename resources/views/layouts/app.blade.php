<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/realtime.js'])
</head>
<body class="bg-stone-100 font-sans text-stone-900 antialiased" data-status-url="{{ route('orders.statuses') }}" data-kpi-url="{{ route('dashboard.kpis') }}">
<div class="min-h-screen lg:flex">
    {{-- Sidebar --}}
    <aside id="sidebar" class="no-print fixed inset-y-0 left-0 z-40 flex w-64 shrink-0 -translate-x-full flex-col border-r border-stone-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:bottom-auto lg:h-screen lg:self-start lg:translate-x-0">
        <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Titik Temu Kopi" class="h-9 w-9 shrink-0 rounded-full border border-stone-200 object-cover">
                <span class="leading-tight">
                    <span class="block text-[15px] font-bold">Titik Temu Kopi</span>
                    <span class="block text-xs text-stone-500">Sistem Kasir &amp; Pemesanan</span>
                </span>
            </a>
            <button type="button" id="sidebar-close" class="rounded-lg p-1.5 text-stone-500 hover:bg-stone-100 lg:hidden" aria-label="Tutup menu">&times;</button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 text-sm">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('dashboard') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>Dashboard</span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('orders.*') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                <span>Pesanan</span>
            </a>
            @if(auth()->user()?->isAdmin())
                <p class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-stone-400">Master Data</p>
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('products.*') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    <span>Menu</span>
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('categories.*') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    <span>Kategori</span>
                </a>
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('users.*') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    <span>Pengguna</span>
                </a>
                <p class="px-3 pb-1 pt-4 text-[11px] font-semibold uppercase tracking-wider text-stone-400">Laporan</p>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('reports.index') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    <span>Laporan Penjualan</span>
                </a>
                <a href="{{ route('reports.laba-rugi') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 font-medium {{ request()->routeIs('reports.laba-rugi*') ? 'bg-amber-50 text-amber-800' : 'text-stone-600 hover:bg-stone-100' }}">
                    <span>Laporan Laba Rugi</span>
                </a>
            @endif
        </nav>

        <div class="border-t border-stone-200 px-4 py-3">
            @php
                // Inisial avatar dari nama lengkap (2 huruf): "Kasir Utama" -> KU,
                // "Administrator" -> AD. Berubah mengikuti user yang login.
                $namaUser = auth()->user()->nama_lengkap ?? '';
                $kataNama = preg_split('/\s+/', trim($namaUser));
                $inisialUser = strtoupper(
                    mb_substr($kataNama[0] ?? '', 0, 1)
                    . (isset($kataNama[1]) ? mb_substr($kataNama[1], 0, 1) : mb_substr($kataNama[0] ?? '', 1, 1))
                );
            @endphp
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white {{ auth()->user()->isAdmin() ? 'bg-amber-700' : 'bg-stone-700' }}" title="{{ $namaUser }}">
                    {{ $inisialUser }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-semibold">{{ $namaUser }}</span>
                    <span class="block text-xs text-stone-500">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kasir' }}</span>
                </span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Logout</button>
            </form>
        </div>
    </aside>
    <div id="sidebar-overlay" class="no-print fixed inset-0 z-30 hidden bg-black/40 lg:hidden"></div>

    {{-- Konten utama --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="no-print sticky top-0 z-20 flex items-center gap-3 border-b border-stone-200 bg-white px-4 py-3 sm:px-6">
            <button type="button" id="sidebar-toggle" class="rounded-lg p-2 text-stone-600 hover:bg-stone-100 lg:hidden" aria-label="Buka menu">
                <span class="block h-0.5 w-5 bg-current"></span>
                <span class="mt-1 block h-0.5 w-5 bg-current"></span>
                <span class="mt-1 block h-0.5 w-5 bg-current"></span>
            </button>
            <h1 class="text-lg font-bold">@yield('title', 'Dashboard')</h1>
        </header>

        <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:px-6">
            @include('partials.alert')
            @yield('content')
        </main>

        <footer class="no-print border-t border-stone-200 px-6 py-4 text-center text-xs text-stone-400">
            Titik Temu Kopi &middot; Sistem Kasir &amp; Pemesanan &middot; {{ now()->year }}
        </footer>
    </div>
</div>
</body>
</html>
