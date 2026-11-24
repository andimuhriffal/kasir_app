@extends('layouts.app')

@section('title', 'Menu')

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div>
            <h2 class="text-xl font-bold">Menu</h2>
            <p class="text-sm text-stone-500">Kelola menu makanan &amp; minuman kafe.</p>
        </div>
        <a href="{{ route('products.create') }}" class="ml-auto rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">+ Tambah Menu</a>
    </div>

    <form method="GET" action="{{ route('products.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-2xl border border-stone-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Cari</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / kode menu" class="w-52 rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-stone-500">Kategori</label>
            <select name="category_id" class="rounded-lg border border-stone-300 px-3 py-2 text-sm outline-none focus:border-amber-700">
                <option value="">Semua</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-stone-900 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Filter</button>
        <a href="{{ route('products.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50">Reset</a>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">Menu</th>
                    <th class="px-4 py-3 font-semibold">Kategori</th>
                    <th class="px-4 py-3 font-semibold">Harga Jual</th>
                    <th class="px-4 py-3 font-semibold">Harga Pokok</th>
                    <th class="px-4 py-3 font-semibold">Stok</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($products as $product)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $product->nama_produk }}</span>
                            <span class="block font-mono text-xs text-stone-400">{{ $product->kode_produk }} &middot; {{ $product->unit }}</span>
                        </td>
                        <td class="px-4 py-3">{{ $product->category->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($product->harga_pokok, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->stok <= 10 ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                {{ $product->stok }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.edit', $product) }}" class="font-semibold text-amber-700 hover:underline">Ubah</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 font-semibold text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-stone-500">Belum ada menu.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection
