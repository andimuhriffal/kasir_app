@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <div class="mb-5 flex flex-wrap items-center gap-3">
        <div>
            <h2 class="text-xl font-bold">Pengguna</h2>
            <p class="text-sm text-stone-500">Kelola akun Administrator &amp; Kasir.</p>
        </div>
        <a href="{{ route('users.create') }}" class="ml-auto rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">+ Tambah Pengguna</a>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-stone-200 bg-white">
        <table class="w-full min-w-[620px] text-left text-sm">
            <thead>
                <tr class="border-b border-stone-200 text-xs uppercase tracking-wide text-stone-400">
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Username</th>
                    <th class="px-4 py-3 font-semibold">Role</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($users as $user)
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3 font-medium">{{ $user->nama_lengkap }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $user->username }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->role === 'admin' ? 'bg-amber-50 text-amber-800' : 'bg-stone-100 text-stone-600' }}">
                                {{ $user->isAdmin() ? 'Administrator' : 'Kasir' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->status === 'aktif' ? 'bg-green-50 text-green-700' : 'bg-stone-100 text-stone-500' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('users.edit', $user) }}" class="font-semibold text-amber-700 hover:underline">Ubah</a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 font-semibold text-red-600 hover:underline">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-stone-500">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
@endsection
