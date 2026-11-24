@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Laporan Penjualan</h2>
        <p class="text-sm text-stone-500">Rekap transaksi dengan filter periode, kategori, dan metode bayar. Pesanan yang dibatalkan tidak masuk laporan.</p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('reports.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Kategori Menu</label>
            <select name="category_id" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
                <option value="">Semua</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Metode Bayar</label>
            <select name="metode_bayar" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
                <option value="">Semua</option>
                @foreach(\App\Models\Order::PAYMENT_METHODS as $key => $label)
                    <option value="{{ $key }}" @selected(request('metode_bayar') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Tampilkan</button>
        <a href="{{ route('reports.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Reset</a>
    </form>

    {{-- Ringkasan --}}
    <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-5">
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Omset Bruto</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['omset_bruto'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Total Diskon</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Total Pajak PB1</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['total_pajak'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Omset Bersih</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['omset_bersih'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Rata-rata / Transaksi</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400">{{ $summary['jumlah_transaksi'] }} transaksi</p>
        </div>
    </div>

    {{-- Ekspor --}}
    <div class="mb-5 flex flex-wrap gap-3">
        <a href="{{ route('reports.export-pdf', request()->query()) }}" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ekspor PDF</a>
        <a href="{{ route('reports.export-csv', request()->query()) }}" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Ekspor Excel/CSV</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[860px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">No. Faktur</th>
                    <th class="px-4 py-3 font-semibold">Tanggal &amp; Waktu</th>
                    <th class="px-4 py-3 font-semibold">Kasir</th>
                    <th class="px-4 py-3 font-semibold">Tipe</th>
                    <th class="px-4 py-3 font-semibold">Item</th>
                    <th class="px-4 py-3 font-semibold">Subtotal</th>
                    <th class="px-4 py-3 font-semibold">Pajak</th>
                    <th class="px-4 py-3 font-semibold">Grand Total</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-semibold text-amber-700 hover:underline">{{ $order->invoice_number }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $order->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $order->typeLabel() }}</td>
                        <td class="px-4 py-3">{{ $order->details->sum('kuantiti') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($order->total_subtotal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($order->pajak_nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                @if($order->status === 'selesai') bg-green-50 text-green-700
                                @elseif($order->status === 'dibatalkan') bg-red-50 text-red-700
                                @else bg-amber-50 text-amber-700 @endif">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-stone-500">Tidak ada transaksi pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
