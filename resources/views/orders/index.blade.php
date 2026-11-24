@extends('layouts.app')

@section('title', 'Pesanan')

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div>
            <h2 class="text-xl font-bold">Pesanan</h2>
            <p class="text-sm text-stone-500">{{ auth()->user()->isAdmin() ? 'Semua pesanan.' : 'Pesanan yang kamu buat.' }}</p>
        </div>
        <span class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700" title="Status pesanan diperbarui otomatis setiap 5 detik">
            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span> Status live
        </span>
        <a href="{{ route('orders.create') }}" class="rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">+ Pesanan Baru</a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('orders.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Cari Faktur</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="INV-POS-..." class="w-48 rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Status</label>
            <select name="status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
                <option value="">Semua</option>
                @foreach(\App\Models\Order::STATUSES as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Filter</button>
        <a href="{{ route('orders.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Reset</a>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">Faktur</th>
                    <th class="px-4 py-3 font-semibold">Kasir</th>
                    <th class="px-4 py-3 font-semibold">Tipe</th>
                    <th class="px-4 py-3 font-semibold">Item</th>
                    <th class="px-4 py-3 font-semibold">Total</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="font-mono text-xs font-semibold text-amber-700 hover:underline">{{ $order->invoice_number }}</a>
                            <span class="block text-xs text-stone-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $order->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{ $order->typeLabel() }}
                            @if($order->nomor_meja)
                                <span class="block text-xs text-stone-400">{{ $order->nomor_meja }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $order->details->sum('kuantiti') }} item</td>
                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span data-order-id="{{ $order->id }}" data-status="{{ $order->status }}" class="rounded-full px-2.5 py-1 text-xs font-semibold
                                @if($order->status === 'selesai') bg-green-50 text-green-700
                                @elseif($order->status === 'dibatalkan') bg-red-50 text-red-700
                                @else bg-amber-50 text-amber-700 @endif">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('orders.show', $order) }}" class="font-semibold text-amber-700 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-stone-500">Belum ada pesanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
