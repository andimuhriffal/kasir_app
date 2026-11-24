@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div>
            <h2 class="font-mono text-xl font-bold">{{ $order->invoice_number }}</h2>
            <p class="text-sm text-stone-500">{{ $order->created_at->format('d F Y, H:i') }} &middot; Kasir: {{ $order->user->nama_lengkap ?? '-' }}@if($order->nama_pemesan) &middot; Pemesan: {{ $order->nama_pemesan }}@endif</p>
        </div>
        <span data-order-id="{{ $order->id }}" data-status="{{ $order->status }}" class="ml-auto rounded-full px-3 py-1.5 text-sm font-semibold
            @if($order->status === 'selesai') bg-green-50 text-green-700
            @elseif($order->status === 'dibatalkan') bg-red-50 text-red-700
            @else bg-amber-50 text-amber-700 @endif">
            {{ $order->statusLabel() }}
        </span>
    </div>

    {{-- Notifikasi saat status berubah dari perangkat lain (mis. kasir membatalkan). --}}
    <div id="status-notice" class="mb-5 hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
        Status pesanan ini berubah menjadi <span id="status-notice-label" class="font-bold"></span>. Halaman sudah diperbarui otomatis.
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-5 xl:col-span-2">
            <h3 class="font-bold">Item Pesanan</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full min-w-[480px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                            <th class="py-2 pr-3 font-semibold">Menu</th>
                            <th class="py-2 pr-3 font-semibold">Harga</th>
                            <th class="py-2 pr-3 font-semibold">Qty</th>
                            <th class="py-2 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($order->details as $detail)
                            <tr>
                                <td class="py-2.5 pr-3">
                                    <span class="font-medium">{{ $detail->product->nama_produk ?? '-' }}</span>
                                    @if($detail->catatan_khusus)
                                        <span class="block text-xs italic text-stone-400">Catatan: {{ $detail->catatan_khusus }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 pr-3">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td class="py-2.5 pr-3">{{ $detail->kuantiti }}</td>
                                <td class="py-2.5 font-semibold">Rp {{ number_format($detail->subtotal_baris, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <dl class="ml-auto mt-5 max-w-xs space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-stone-500">Subtotal</dt>
                    <dd class="font-semibold">Rp {{ number_format($order->total_subtotal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Diskon</dt>
                    <dd class="font-semibold">Rp {{ number_format($order->diskon_nominal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Pajak PB1 (10%)</dt>
                    <dd class="font-semibold">Rp {{ number_format($order->pajak_nominal, 0, ',', '.') }}</dd>
                </div>
                <div class="flex justify-between border-t border-stone-200 pt-2 text-base">
                    <dt class="font-bold">Grand Total</dt>
                    <dd class="font-bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-stone-200 bg-white p-5">
                <h3 class="font-bold">Pembayaran</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-stone-500">Tipe</dt>
                        <dd class="font-medium">{{ $order->typeLabel() }}{{ $order->nomor_meja ? ' — ' . $order->nomor_meja : '' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-stone-500">Metode</dt>
                        <dd class="font-medium">{{ $order->paymentLabel() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-stone-500">Uang Dibayar</dt>
                        <dd class="font-medium">Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-stone-500">Kembalian</dt>
                        <dd class="font-medium">Rp {{ number_format($order->uang_kembali, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            <div id="order-actions" class="rounded-2xl border border-stone-200 bg-white p-5">
                <h3 class="font-bold">Aksi</h3>
                <div class="mt-3 space-y-2.5">
                    <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="block rounded-lg bg-stone-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-stone-700">Cetak Struk</a>
                    @if($order->status === 'diproses')
                        <form action="{{ route('orders.complete', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Tandai Selesai</button>
                        </form>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full rounded-lg border border-red-300 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">Batalkan Pesanan</button>
                        </form>
                    @endif
                    <a href="{{ route('orders.index') }}" class="block text-center text-sm font-medium text-stone-500 hover:underline">&larr; Kembali ke daftar pesanan</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Kalau status pesanan ini berubah dari perangkat lain (mis. kasir
        // membatalkan), sembunyikan tombol aksi dan tampilkan pemberitahuan.
        (function () {
            var badge = document.querySelector('[data-order-id]');
            if (!badge) return;
            var orderId = parseInt(badge.dataset.orderId, 10);
            var actions = document.getElementById('order-actions');
            var notice = document.getElementById('status-notice');
            var noticeLabel = document.getElementById('status-notice-label');
            document.addEventListener('order-status-changed', function (e) {
                if (e.detail.id !== orderId) return;
                if (e.detail.newStatus !== 'diproses') {
                    if (actions) actions.classList.add('hidden');
                    if (notice) {
                        if (noticeLabel) noticeLabel.textContent = e.detail.label;
                        notice.classList.remove('hidden');
                    }
                }
            });
        })();
    </script>
@endsection
