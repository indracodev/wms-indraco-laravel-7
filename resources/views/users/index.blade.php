@extends('layouts.app')

@section('title', 'Manajemen User & Impersonasi')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Manajemen Akun User & Admin</h2>
            <p class="text-sm text-slate-400">Pengelolaan hak akses pengguna, penetapan shift, dan fitur Impersonasi (Login As)</p>
        </div>
        @canAccessModule('users', 'create')
            <button @click="showCreateModal = true" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <i class="fa-solid fa-user-plus"></i>
                <span>Tambah User Baru</span>
            </button>
        @endcanAccessModule
    </div>

    <!-- Desktop Datatable View -->
    <div class="hidden md:block glass-panel p-6 rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800 text-slate-300 uppercase tracking-wider">
                    <tr>
                        <th class="p-3.5">User ID</th>
                        <th class="p-3.5">Nama & Username</th>
                        <th class="p-3.5">Role</th>
                        <th class="p-3.5">Shift</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi & Impersonasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $u)
                        <tr class="hover:bg-slate-800/50 transition">
                            <td class="p-3.5 font-mono font-bold text-indigo-400">{{ $u->id }}</td>
                            <td class="p-3.5">
                                <div class="font-bold text-white text-sm">{{ $u->name }}</div>
                                <div class="text-slate-400">@ {{ $u->username }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider
                                    {{ $u->role_id === 'super_admin' ? 'bg-purple-950 text-purple-300 border border-purple-500/40' : '' }}
                                    {{ $u->role_id === 'supervisor' ? 'bg-amber-950 text-amber-300 border border-amber-500/40' : '' }}
                                    {{ $u->role_id === 'operator' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : '' }}
                                ">
                                    {{ $u->role->name ?? $u->role_id }}
                                </span>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-300">
                                Shift {{ $u->shift }}
                            </td>
                            <td class="p-3.5">
                                @if($u->is_active)
                                    <span class="text-emerald-400 font-bold flex items-center"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Aktif</span>
                                @else
                                    <span class="text-rose-400 font-bold flex items-center"><i class="fa-solid fa-circle text-[8px] mr-1"></i> Non-Aktif</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-2">
                                <!-- Impersonate / Login As Button (Only for Super Admin) -->
                                @if(auth()->user()->role_id === 'super_admin' && auth()->id() !== $u->id)
                                    <form action="{{ route('impersonate.start', $u->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-lg shadow transition flex items-center inline-flex space-x-1" title="Login Sebagai User Ini">
                                            <i class="fa-solid fa-user-secret"></i>
                                            <span>Login As</span>
                                        </button>
                                    </form>
                                @endif

                                @canAccessModule('users', 'delete')
                                    @if(auth()->id() !== $u->id)
                                        <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-400 hover:bg-rose-950/60 rounded-lg">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcanAccessModule
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Flat Card List View (No Card inside Card) -->
    <div class="space-y-3 md:hidden">
        @foreach($users as $u)
            <div class="bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl space-y-3 text-xs">
                <!-- User Header: ID & Status -->
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                    <div class="font-mono font-bold text-indigo-400 text-sm">{{ $u->id }}</div>
                    <div>
                        @if($u->is_active)
                            <span class="px-2 py-0.5 rounded font-bold bg-emerald-950 text-emerald-300 border border-emerald-500/40 text-[10px]">Aktif</span>
                        @else
                            <span class="px-2 py-0.5 rounded font-bold bg-rose-950 text-rose-300 border border-rose-500/40 text-[10px]">Non-Aktif</span>
                        @endif
                    </div>
                </div>

                <!-- Info: Name, Username, Role, Shift -->
                <div class="flex items-start justify-between">
                    <div>
                        <div class="font-bold text-white text-base">{{ $u->name }}</div>
                        <div class="text-slate-400 text-xs">@ {{ $u->username }}</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider block
                            {{ $u->role_id === 'super_admin' ? 'bg-purple-950 text-purple-300 border border-purple-500/40' : '' }}
                            {{ $u->role_id === 'supervisor' ? 'bg-amber-950 text-amber-300 border border-amber-500/40' : '' }}
                            {{ $u->role_id === 'operator' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : '' }}
                        ">
                            {{ $u->role->name ?? $u->role_id }}
                        </span>
                        <span class="text-[11px] text-slate-400 block mt-1">Shift {{ $u->shift }}</span>
                    </div>
                </div>

                <!-- Touch-friendly Actions (Min 44px height) -->
                <div class="pt-2 border-t border-slate-700/50 flex items-center justify-end space-x-2">
                    @if(auth()->user()->role_id === 'super_admin' && auth()->id() !== $u->id)
                        <form action="{{ route('impersonate.start', $u->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs rounded-xl shadow transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-user-secret text-sm"></i>
                                <span>Login As (Impersonate)</span>
                            </button>
                        </form>
                    @endif

                    @canAccessModule('users', 'delete')
                        @if(auth()->id() !== $u->id)
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-300 rounded-xl">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        @endif
                    @endcanAccessModule
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create User Modal -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                <h3 class="font-bold text-white text-base">Tambah User Baru</h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">User ID</label>
                    <input type="text" name="id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono" placeholder="misal: USR-005">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Username Login</label>
                    <input type="text" name="username" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Password</label>
                    <input type="password" name="password" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Role Otorisasi</label>
                        <select name="role_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Shift</label>
                        <select name="shift" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                            <option value="3">Shift 3</option>
                        </select>
                    </div>
                </div>
                <div class="pt-2 flex justify-end space-x-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 rounded-xl font-semibold">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold shadow">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Alpine.js script CDN -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
