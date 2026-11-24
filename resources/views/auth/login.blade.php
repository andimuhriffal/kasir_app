@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <h2 class="text-lg font-bold">Login</h2>
    <p class="mt-1 text-sm text-stone-500">Masuk untuk melanjutkan ke kasir.</p>

    <form action="{{ route('login.store') }}" method="POST" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="username" class="mb-1.5 block text-sm font-medium text-stone-700">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100"
                placeholder="contoh: admin">
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-stone-700">Password</label>
            <input type="password" id="password" name="password" required
                class="w-full rounded-lg border border-stone-300 px-3.5 py-2.5 text-sm outline-none focus:border-amber-700 focus:ring-2 focus:ring-amber-100"
                placeholder="••••••••">
        </div>

        <label class="flex items-center gap-2 text-sm text-stone-600">
            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-stone-300 accent-amber-700">
            Ingat saya
        </label>

        <button type="submit" class="w-full rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-800">
            Masuk
        </button>
    </form>
@endsection
