@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Tambah Menu</h2>
        <p class="text-sm text-stone-500">Lengkapi data menu baru.</p>
    </div>

    <form action="{{ route('products.store') }}" method="POST" class="max-w-2xl rounded-2xl border border-stone-200 bg-white p-5 sm:p-6">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="kode_produk" class="mb-1.5 block text-sm font-medium text-stone-700">Kode Produk <span class="text-red-600">*</span></label>
                <input type="text" id="kode_produk" name="kode_produk" value="{{ old('kode_produk') }}" required placeholder="contoh: K007"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="category_id" class="mb-1.5 block text-sm font-medium text-stone-700">Kategori <span class="text-red-600">*</span></label>
                <select id="category_id" name="category_id" required class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                    <option value="">— Pilih kategori —</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="nama_produk" class="mb-1.5 block text-sm font-medium text-stone-700">Nama Menu <span class="text-red-600">*</span></label>
                <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required placeholder="contoh: Es Kopi Susu"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="harga_jual" class="mb-1.5 block text-sm font-medium text-stone-700">Harga Jual (Rp) <span class="text-red-600">*</span></label>
                <input type="number" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}" required min="0" step="500"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="harga_pokok" class="mb-1.5 block text-sm font-medium text-stone-700">Harga Pokok (Rp) <span class="text-red-600">*</span></label>
                <input type="number" id="harga_pokok" name="harga_pokok" value="{{ old('harga_pokok') }}" required min="0" step="500"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="stok" class="mb-1.5 block text-sm font-medium text-stone-700">Stok <span class="text-red-600">*</span></label>
                <input type="number" id="stok" name="stok" value="{{ old('stok', 0) }}" required min="0"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="unit" class="mb-1.5 block text-sm font-medium text-stone-700">Satuan <span class="text-red-600">*</span></label>
                <input type="text" id="unit" name="unit" value="{{ old('unit', 'pcs') }}" required placeholder="pcs / cup / porsi"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-amber-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Simpan</button>
            <a href="{{ route('products.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50">Batal</a>
        </div>
    </form>
@endsection
