<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk — {{ $order->invoice_number }}</title>
    @vite(['resources/css/documents/receipt.css'])
</head>
<body>
    <div class="toolbar">
        <button type="button" class="primary" onclick="window.print()">Cetak</button>
        <a href="{{ route('orders.show', $order) }}">Kembali</a>
    </div>
    <div class="receipt">
        <div class="center">
            <div class="bold receipt-title">TITIK TEMU KOPI</div>
            <div>Sistem Kasir &amp; Pemesanan</div>
        </div>
        <div class="divider"></div>
        <div class="row"><span>No. Faktur</span><span class="bold">{{ $order->invoice_number }}</span></div>
        <div class="row"><span>Tanggal</span><span>{{ $order->created_at->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span>Kasir</span><span>{{ $order->user->nama_lengkap ?? '-' }}</span></div>
        @if($order->nama_pemesan)
            <div class="row"><span>Pemesan</span><span>{{ $order->nama_pemesan }}</span></div>
        @endif
        <div class="row"><span>Tipe</span><span>{{ $order->typeLabel() }}{{ $order->nomor_meja ? ' — ' . $order->nomor_meja : '' }}</span></div>
        <div class="divider"></div>
        @foreach($order->details as $detail)
            <div class="item">
                <div class="row"><span>{{ $detail->product->nama_produk ?? '-' }}</span><span>Rp {{ number_format($detail->subtotal_baris, 0, ',', '.') }}</span></div>
                <div>{{ $detail->kuantiti }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</div>
                @if($detail->catatan_khusus)
                    <div class="note">* {{ $detail->catatan_khusus }}</div>
                @endif
            </div>
        @endforeach
        <div class="divider"></div>
        <div class="row"><span>Subtotal</span><span>Rp {{ number_format($order->total_subtotal, 0, ',', '.') }}</span></div>
        <div class="row"><span>Diskon</span><span>Rp {{ number_format($order->diskon_nominal, 0, ',', '.') }}</span></div>
        <div class="row"><span>Pajak PB1 (10%)</span><span>Rp {{ number_format($order->pajak_nominal, 0, ',', '.') }}</span></div>
        <div class="row total"><span class="bold">TOTAL</span><span class="bold">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span></div>
        <div class="divider"></div>
        <div class="row"><span>{{ $order->paymentLabel() }}</span><span>Rp {{ number_format($order->uang_dibayar, 0, ',', '.') }}</span></div>
        <div class="row"><span>Kembalian</span><span>Rp {{ number_format($order->uang_kembali, 0, ',', '.') }}</span></div>
        <div class="row"><span>Status</span><span>{{ $order->statusLabel() }}</span></div>
        <div class="divider"></div>
        <div class="center">Terima kasih atas kunjungan Anda.<br>Selamat menikmati!</div>
    </div>
</body>
</html>
