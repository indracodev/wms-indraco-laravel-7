@extends('layouts.app')

@section('title', 'Profil Akun')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Profil Pengguna & Keamanan</h2>
            <p class="text-sm text-slate-400">Informasi pribadi, shift kerja, dan pembaruan kata sandi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- User Info Card -->
        <div class="glass-panel p-6 rounded-2xl text-center space-y-4">
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center font-bold text-2xl text-white mx-auto shadow-xl ring-4 ring-indigo-500/20">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                <div class="text-xs text-indigo-400 font-mono">@ {{ $user->username }}</div>
            </div>
            <div class="pt-2 border-t border-slate-700/60 space-y-2 text-xs text-slate-300">
                <div class="flex justify-between">
                    <span class="text-slate-500">Role System:</span>
                    <span class="font-bold uppercase text-amber-400">{{ $user->role->name ?? $user->role_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Shift Kerja:</span>
                    <span class="font-semibold text-white">Shift {{ $user->shift }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Akun:</span>
                    <span class="font-bold text-emerald-400">AKTIF</span>
                </div>
            </div>
        </div>

        <!-- Change Password Form -->
        <div class="md:col-span-2 glass-panel p-6 rounded-2xl space-y-4">
            <h3 class="font-bold text-white text-base mb-4">Ubah Password Akun</h3>
            <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Password Baru</label>
                    <input type="password" name="new_password" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow transition">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
