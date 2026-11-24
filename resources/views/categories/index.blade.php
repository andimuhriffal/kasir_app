@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div>
            <h2 class="text-xl font-bold">Kategori</h2>
            <p class="text-sm text-stone-500">Kelola kategori menu (Coffee, Non-Coffee, Food, Pastry).</p>
        </div>
        <a href="{{ route('categories.create') }}" class="ml-auto rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">+ Tambah Kategori</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">Kategori</th>
                    <th class="px-4 py-3 font-semibold">Slug</th>
                    <th class="px-4 py-3 font-semibold">Jumlah Menu</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium">{{ $category->nama_kategori }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-stone-500">{{ $category->slug }}</td>
                        <td class="px-4 py-3">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->status === 'aktif' ? 'bg-green-50 text-green-700' : 'bg-stone-100 text-stone-500' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('categories.edit', $category) }}" class="font-semibold text-amber-700 hover:underline">Ubah</a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 font-semibold text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-stone-500">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
