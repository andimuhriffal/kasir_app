@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Laporan Laba Rugi</h2>
        <p class="text-sm text-stone-500">Pendapatan vs harga pokok (HPP) dari pesanan yang sudah <span class="font-semibold">Selesai</span> saja — pesanan yang masih Diproses belum dihitung sebagai pendapatan. HPP memakai harga pokok produk saat ini.</p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('reports.laba-rugi') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Tampilkan</button>
        <a href="{{ route('reports.laba-rugi') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Reset</a>
    </form>

    {{-- Ringkasan --}}
    <div class="mb-5 grid grid-cols-2 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Penjualan Kotor</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['penjualan_kotor'], 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400">{{ $summary['jumlah_transaksi'] }} transaksi selesai</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Total Diskon</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['total_diskon'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Pendapatan Bersih</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['pendapatan_bersih'], 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400">Penjualan kotor &minus; diskon</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Total HPP</p>
            <p class="mt-1 font-bold">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400">Harga pokok penjualan</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Laba Kotor</p>
            <p class="mt-1 font-bold {{ $summary['laba_kotor'] >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($summary['laba_kotor'], 0, ',', '.') }}</p>
            <p class="text-xs text-stone-400">Pendapatan bersih &minus; HPP</p>
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-4">
            <p class="text-xs font-medium text-stone-500">Margin Laba Kotor</p>
            <p class="mt-1 font-bold {{ $summary['margin'] >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($summary['margin'], 1, ',', '.') }}%</p>
            <p class="text-xs text-stone-400">Pajak PB1 terkumpul: Rp {{ number_format($summary['total_pajak'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Ekspor --}}
    <div class="mb-5 flex flex-wrap gap-3">
        <a href="{{ route('reports.laba-rugi-pdf', request()->query()) }}" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Ekspor PDF</a>
        <a href="{{ route('reports.laba-rugi-csv', request()->query()) }}" class="rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">Ekspor Excel/CSV</a>
    </div>

    {{-- Rincian per produk --}}
    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[720px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">Produk</th>
                    <th class="px-4 py-3 font-semibold">Kategori</th>
                    <th class="px-4 py-3 font-semibold">Qty Terjual</th>
                    <th class="px-4 py-3 font-semibold">Penjualan</th>
                    <th class="px-4 py-3 font-semibold">HPP</th>
                    <th class="px-4 py-3 font-semibold">Laba</th>
                    <th class="px-4 py-3 font-semibold">Margin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($breakdown as $row)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium">{{ $row->nama_produk }}</td>
                        <td class="px-4 py-3">{{ $row->nama_kategori }}</td>
                        <td class="px-4 py-3">{{ $row->qty }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($row->penjualan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($row->hpp, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 font-semibold {{ $row->laba >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($row->laba, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ number_format($row->margin, 1, ',', '.') }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-stone-500">Tidak ada pesanan selesai pada filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($breakdown->isNotEmpty())
                <tfoot>
                    <tr class="border-t border-stone-200 bg-stone-50 font-bold">
                        <td class="px-4 py-3" colspan="2">TOTAL</td>
                        <td class="px-4 py-3">{{ $breakdown->sum('qty') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($summary['penjualan_kotor'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($summary['total_hpp'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 {{ ($summary['laba_kotor'] + $summary['total_diskon']) >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($summary['laba_kotor'] + $summary['total_diskon'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $summary['penjualan_kotor'] > 0 ? number_format((($summary['laba_kotor'] + $summary['total_diskon']) / $summary['penjualan_kotor']) * 100, 1, ',', '.') : '0,0' }}%</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
    <p class="mt-3 text-xs text-stone-400">Catatan: laba per produk dihitung sebelum alokasi diskon (diskon berlaku per pesanan, bukan per item), sehingga total laba di tabel ini = Laba Kotor + Total Diskon. Pajak PB1 adalah pajak yang dipungut dari pelanggan untuk pemerintah, bukan bagian dari laba.</p>
@endsection
