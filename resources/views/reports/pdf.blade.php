<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan — Titik Temu Kopi</title>
    {{--
        Catatan: style laporan PDF sengaja tetap di sini (blok <style>),
        bukan file CSS terpisah. dompdf (library pembuat PDF) tidak bisa
        memuat CSS eksternal secara andal, jadi style harus menempel di
        template ini supaya ekspor PDF tetap jalan.
    --}}
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin: 0 0 2px; }
        .subtitle { color: #666; margin-bottom: 12px; }
        .filters { color: #555; margin-bottom: 12px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .summary td { border: 1px solid #ccc; padding: 6px 8px; }
        .summary td.label { background: #f5f5f4; font-weight: bold; width: 22%; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 5px 6px; text-align: left; }
        table.data th { background: #f5f5f4; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 14px; color: #888; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan</h1>
    <div class="subtitle">Titik Temu Kopi &middot; Sistem Kasir &amp; Pemesanan</div>

    <div class="filters">
        @if(!empty($filters['dari']) || !empty($filters['sampai']))
            Periode: {{ $filters['dari'] ?? '...' }} s.d. {{ $filters['sampai'] ?? '...' }} &nbsp;|&nbsp;
        @endif
        @if(!empty($filters['category_id']))
            Kategori: {{ \App\Models\Category::find($filters['category_id'])?->nama_kategori ?? '-' }} &nbsp;|&nbsp;
        @endif
        @if(!empty($filters['metode_bayar']))
            Metode: {{ \App\Models\Order::PAYMENT_METHODS[$filters['metode_bayar']] ?? $filters['metode_bayar'] }} &nbsp;|&nbsp;
        @endif
        Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table class="summary">
        <tr>
            <td class="label">Omset Bruto</td>
            <td>Rp {{ number_format($summary['omset_bruto'], 0, ',', '.') }}</td>
            <td class="label">Total Diskon</td>
            <td>Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Total Pajak PB1</td>
            <td>Rp {{ number_format($summary['total_pajak'], 0, ',', '.') }}</td>
            <td class="label">Omset Bersih</td>
            <td>Rp {{ number_format($summary['omset_bersih'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Transaksi</td>
            <td>{{ $summary['jumlah_transaksi'] }}</td>
            <td class="label">Rata-rata / Transaksi</td>
            <td>Rp {{ number_format($summary['rata_rata'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No. Faktur</th>
                <th>Tanggal &amp; Waktu</th>
                <th>Kasir</th>
                <th>Tipe</th>
                <th class="right">Item</th>
                <th class="right">Subtotal</th>
                <th class="right">Pajak</th>
                <th class="right">Grand Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->user->nama_lengkap ?? '-' }}</td>
                    <td>{{ $order->typeLabel() }}</td>
                    <td class="right">{{ $order->details->sum('kuantiti') }}</td>
                    <td class="right">Rp {{ number_format($order->total_subtotal, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($order->pajak_nominal, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                    <td>{{ $order->statusLabel() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Tidak ada transaksi pada filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Laporan ini dibuat otomatis oleh Sistem Kasir Titik Temu Kopi. Pesanan yang dibatalkan tidak termasuk.</div>
</body>
</html>
