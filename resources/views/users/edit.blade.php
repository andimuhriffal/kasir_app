@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
    <div class="mb-5">
        <h2 class="text-xl font-bold">Ubah Pengguna</h2>
        <p class="text-sm text-stone-500">{{ $user->nama_lengkap }} (@{{ $user->username }})</p>
    </div>

    <form action="{{ route('users.update', $user) }}" method="POST" class="max-w-xl rounded-2xl border border-stone-200 bg-white p-5 sm:p-6">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="nama_lengkap" class="mb-1.5 block text-sm font-medium text-stone-700">Nama Lengkap <span class="text-red-600">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="username" class="mb-1.5 block text-sm font-medium text-stone-700">Username <span class="text-red-600">*</span></label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password baru (kosongkan jika tidak diubah)</label>
                <input type="password" id="password" name="password" minlength="8"
                    class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="role" class="mb-1.5 block text-sm font-medium text-stone-700">Role <span class="text-red-600">*</span></label>
                    <select id="role" name="role" required class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                        <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-sm font-medium text-stone-700">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" required class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100">
                        <option value="aktif" @selected(old('status', $user->status) === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status', $user->status) === 'nonaktif')>Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="mt-6 flex gap-3">
            <button type="submit" class="rounded-lg bg-amber-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">Simpan Perubahan</button>
            <a href="{{ route('users.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-medium text-stone-700 hover:bg-stone-50">Batal</a>
        </div>
    </form>
@endsection
