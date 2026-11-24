<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi — Titik Temu Kopi</title>
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
        table.data tfoot td { font-weight: bold; background: #f5f5f4; }
        .right { text-align: right; }
        .center { text-align: center; }
        .footer { margin-top: 14px; color: #888; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Laba Rugi</h1>
    <div class="subtitle">Titik Temu Kopi &middot; Sistem Kasir &amp; Pemesanan</div>

    <div class="filters">
        @if(!empty($filters['dari']) || !empty($filters['sampai']))
            Periode: {{ $filters['dari'] ?? '...' }} s.d. {{ $filters['sampai'] ?? '...' }} &nbsp;|&nbsp;
        @endif
        Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table class="summary">
        <tr>
            <td class="label">Penjualan Kotor</td>
            <td>Rp {{ number_format($summary['penjualan_kotor'], 0, ',', '.') }}</td>
            <td class="label">Total Diskon</td>
            <td>Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Pendapatan Bersih</td>
            <td>Rp {{ number_format($summary['pendapatan_bersih'], 0, ',', '.') }}</td>
            <td class="label">Total HPP</td>
            <td>Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Laba Kotor</td>
            <td>Rp {{ number_format($summary['laba_kotor'], 0, ',', '.') }}</td>
            <td class="label">Margin Laba Kotor</td>
            <td>{{ number_format($summary['margin'], 1, ',', '.') }}%</td>
        </tr>
        <tr>
            <td class="label">Jumlah Transaksi</td>
            <td>{{ $summary['jumlah_transaksi'] }} transaksi selesai</td>
            <td class="label">Pajak PB1 Terkumpul</td>
            <td>Rp {{ number_format($summary['total_pajak'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th class="right">Qty Terjual</th>
                <th class="right">Penjualan</th>
                <th class="right">HPP</th>
                <th class="right">Laba</th>
                <th class="right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($breakdown as $row)
                <tr>
                    <td>{{ $row->nama_produk }}</td>
                    <td>{{ $row->nama_kategori }}</td>
                    <td class="right">{{ $row->qty }}</td>
                    <td class="right">Rp {{ number_format($row->penjualan, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row->hpp, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($row->laba, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($row->margin, 1, ',', '.') }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Tidak ada pesanan selesai pada filter ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if($breakdown->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="2">TOTAL</td>
                    <td class="right">{{ $breakdown->sum('qty') }}</td>
                    <td class="right">Rp {{ number_format($summary['penjualan_kotor'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($summary['laba_kotor'] + $summary['total_diskon'], 0, ',', '.') }}</td>
                    <td class="right">{{ $summary['penjualan_kotor'] > 0 ? number_format((($summary['laba_kotor'] + $summary['total_diskon']) / $summary['penjualan_kotor']) * 100, 1, ',', '.') : '0,0' }}%</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">Laporan ini dibuat otomatis oleh Sistem Kasir Titik Temu Kopi. Hanya pesanan berstatus Selesai yang dihitung. HPP memakai harga pokok produk saat ini. Laba per produk dihitung sebelum alokasi diskon; total laba di tabel = Laba Kotor + Total Diskon.</div>
</body>
</html>
