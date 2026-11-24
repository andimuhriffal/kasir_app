@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Tambah Kategori</h2>
        <p class="text-sm text-stone-500">Slug dibuat otomatis dari nama kategori jika dikosongkan.</p>
    </div>

    <form action="{{ route('categories.store') }}" method="POST" class="max-w-xl rounded-2xl border border-stone-200 bg-white p-5 sm:p-6">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="nama_kategori" class="mb-1.5 block text-sm font-medium text-stone-700">Nama Kategori <span class="text-red-600">*</span></label>
                <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required placeholder="contoh: Coffee"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="slug" class="mb-1.5 block text-sm font-medium text-stone-700">Slug (opsional)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="contoh: coffee"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-stone-700">Status <span class="text-red-600">*</span></label>
                <select id="status" name="status" required class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                    <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-amber-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Simpan</button>
            <a href="{{ route('categories.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50">Batal</a>
        </div>
    </form>
@endsection
