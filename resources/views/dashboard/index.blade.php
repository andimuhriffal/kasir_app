@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6 flex flex-wrap items-start gap-3">
        <div>
            <h2 class="text-xl font-bold">Selamat datang, {{ auth()->user()->nama_lengkap }}</h2>
            <p class="text-sm text-stone-500">{{ now()->isoFormat('dddd, D MMMM YYYY') }} &middot; Ringkasan kinerja kafe hari ini.</p>
        </div>
        <span class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700" title="Seluruh dashboard (KPI, grafik, stok, transaksi) diperbarui otomatis setiap 5 detik">
    </div>

    {{-- 4 KPI --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Total Omset Hari Ini</p>
            <p class="mt-1.5 text-2xl font-bold" data-kpi="omset">Rp {{ number_format($omsetHariIni, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-stone-400">Pesanan Selesai hari ini</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Jumlah Pesanan Hari Ini</p>
            <p class="mt-1.5 text-2xl font-bold"><span data-kpi="jumlah-pesanan">{{ $jumlahPesanan }}</span> <span class="text-base font-medium text-stone-400">pesanan</span></p>
            <p class="mt-1 text-xs text-stone-400">Berstatus Selesai</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Menu Terlaris</p>
            <p class="mt-1.5 truncate text-2xl font-bold {{ ($menuTerlaris && $menuTerlaris->product) ? '' : 'text-stone-300' }}" data-kpi="menu-terlaris-nama">{{ ($menuTerlaris && $menuTerlaris->product) ? $menuTerlaris->product->nama_produk : '—' }}</p>
            <p class="mt-1 text-xs text-stone-400" data-kpi="menu-terlaris-jumlah">{{ $menuTerlaris ? number_format($menuTerlaris->total_terjual) . ' terjual' : 'Belum ada penjualan' }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <p class="text-sm font-medium text-stone-500">Peringatan Stok Rendah</p>
            <p class="mt-1.5 text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : '' }}"><span data-kpi="stok-rendah">{{ $lowStockCount }}</span> <span class="text-base font-medium text-stone-400">menu</span></p>
            <p class="mt-1 text-xs text-stone-400">Stok &le; 10 &middot; perlu restock</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Grafik tren 7 hari (SVG murni, tanpa dependensi eksternal) --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5 xl:col-span-2">
            <div class="flex items-baseline justify-between">
                <h3 class="font-bold">Tren Omset 7 Hari Terakhir</h3>
                <span class="text-xs text-stone-400">Rp per hari</span>
            </div>
            @php
                $max = max(collect($chartData)->max('total'), 1);
            @endphp
            <div id="chart-7-hari" class="trend-chart mt-5 flex items-end gap-2 sm:gap-3">
                @foreach($chartData as $day)
                    @php $height = max(4, round($day['total'] / $max * 100)); @endphp
                    <div class="group flex h-full flex-1 cursor-pointer flex-col items-center justify-end gap-2">
                        <span class="text-[11px] font-semibold text-stone-600 transition-colors duration-200 group-hover:text-amber-800"><span class="group-hover:hidden">Rp {{ number_format($day['total'] / 1000, 0) }}rb</span><span class="hidden whitespace-nowrap group-hover:inline">Rp {{ number_format($day['total'], 0, ',', '.') }}</span></span>
                        <div class="trend-chart-bar w-full max-w-10 origin-bottom rounded-t-lg bg-amber-700/90 transition-all duration-300 ease-out group-hover:scale-y-105 group-hover:bg-amber-600 group-hover:shadow-md" style="--bar-height: {{ $height }}%;"></div>
                        <span class="text-center text-[11px] leading-tight text-stone-500 transition-colors duration-200 group-hover:font-semibold group-hover:text-stone-800">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Stok rendah --}}
        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <h3 class="font-bold">Stok Menipis</h3>
            <div id="low-stock-list">
                @if($lowStockProducts->isEmpty())
                    <p class="mt-3 text-sm text-stone-500">Semua stok aman.</p>
                @else
                    <ul class="mt-3 divide-y divide-stone-100">
                        @foreach($lowStockProducts as $product)
                            <li class="flex items-center justify-between py-2.5 text-sm">
                                <span>
                                    <span class="block font-medium">{{ $product->nama_produk }}</span>
                                    <span class="text-xs text-stone-400">{{ $product->category->nama_kategori ?? '-' }}</span>
                                </span>
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Sisa {{ $product->stok }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sm font-semibold text-amber-700 hover:underline">Kelola menu &rarr;</a>
            @endif
        </div>
    </div>

    {{-- Transaksi terbaru + aksi cepat --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 xl:col-span-2">
            <div class="flex items-baseline justify-between">
                <h3 class="font-bold">Transaksi Terbaru</h3>
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-amber-700 hover:underline">Lihat semua &rarr;</a>
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[520px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                            <th class="py-2 pr-3 font-semibold">Faktur</th>
                            <th class="py-2 pr-3 font-semibold">Kasir</th>
                            <th class="py-2 pr-3 font-semibold">Total</th>
                            <th class="py-2 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody id="recent-orders-body" class="divide-y divide-stone-100">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="py-2.5 pr-3">
                                    <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-semibold text-amber-700 hover:underline">{{ $order->invoice_number }}</a>
                                    <span class="block text-xs text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-2.5 pr-3">{{ $order->user->nama_lengkap ?? '-' }}</td>
                                <td class="py-2.5 pr-3 font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                <td class="py-2.5">
                                    <span data-order-id="{{ $order->id }}" data-status="{{ $order->status }}" class="rounded-full px-2.5 py-1 text-xs font-semibold
                                        @if($order->status === 'selesai') bg-green-50 text-green-700
                                        @elseif($order->status === 'dibatalkan') bg-red-50 text-red-700
                                        @else bg-amber-50 text-amber-700 @endif">
                                        {{ $order->statusLabel() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-2.5 text-sm text-stone-500">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-5">
            <h3 class="font-bold">Aksi Cepat</h3>
            <div class="mt-4 space-y-2.5">
                <a href="{{ route('orders.create') }}" class="block rounded-xl bg-amber-700 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-amber-800">Buat Pesanan Baru</a>
                <a href="{{ route('orders.index') }}" class="block rounded-xl border border-stone-300 px-4 py-3 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50">Riwayat Pesanan</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('reports.index') }}" class="block rounded-xl border border-stone-300 px-4 py-3 text-center text-sm font-semibold text-stone-700 hover:bg-stone-50">Laporan Penjualan</a>
                @endif
            </div>
        </div>
    </div>
@endsection
