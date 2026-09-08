@extends('layouts.app')

@section('title', 'Matriks RBAC')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Matriks Hak Akses Modul (RBAC)</h2>
            <p class="text-sm text-slate-400">Pengaturan izin Granular per Modul (Read, Create, Update, Delete) untuk setiap Role</p>
        </div>
    </div>

    <form action="{{ route('rbac.update') }}" method="POST">
        @csrf
        <div class="glass-panel p-6 rounded-2xl shadow-xl space-y-6">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-800 text-slate-300 uppercase">
                        <tr>
                            <th class="p-4 border-b border-slate-700">Modul System</th>
                            @foreach($roles as $role)
                                <th class="p-4 border-b border-slate-700 text-center">
                                    <div class="font-bold text-sm text-white">{{ $role->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">({{ $role->id }})</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($modules as $mod)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-4 font-bold text-white uppercase tracking-wider font-mono text-xs">
                                    <i class="fa-solid fa-cube text-indigo-400 mr-2"></i>
                                    {{ $mod }}
                                </td>
                                @foreach($roles as $role)
                                    @php
                                        $perm = $role->permissions->firstWhere('module', $mod);
                                        $isSuperAdmin = $role->id === 'super_admin';
                                    @endphp
                                    <td class="p-4 text-center bg-slate-900/30">
                                        @if($isSuperAdmin)
                                            <span class="px-2 py-1 bg-emerald-950/80 text-emerald-300 border border-emerald-500/40 rounded font-bold text-[10px]">
                                                ALL ACCESS (SUPER ADMIN)
                                            </span>
                                        @else
                                            <div class="flex items-center justify-center space-x-3 text-[11px]">
                                                <label class="flex items-center space-x-1 cursor-pointer" title="Read">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_read]" value="1" {{ ($perm && $perm->can_read) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                                                    <span class="text-slate-300">R</span>
                                                </label>
                                                <label class="flex items-center space-x-1 cursor-pointer" title="Create">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_create]" value="1" {{ ($perm && $perm->can_create) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                                                    <span class="text-slate-300">C</span>
                                                </label>
                                                <label class="flex items-center space-x-1 cursor-pointer" title="Update">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_update]" value="1" {{ ($perm && $perm->can_update) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                                                    <span class="text-slate-300">U</span>
                                                </label>
                                                <label class="flex items-center space-x-1 cursor-pointer" title="Delete">
                                                    <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_delete]" value="1" {{ ($perm && $perm->can_delete) ? 'checked' : '' }} class="rounded bg-slate-800 border-slate-700 text-indigo-600">
                                                    <span class="text-slate-300">D</span>
                                                </label>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List View (No Card inside Card) -->
            <div class="space-y-4 md:hidden">
                @foreach($roles as $role)
                    @if($role->id !== 'super_admin')
                        <div class="bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl space-y-3 text-xs">
                            <div class="font-bold text-white text-sm border-b border-slate-700 pb-2 flex items-center justify-between">
                                <span>Role: {{ $role->name }}</span>
                                <span class="font-mono text-[10px] text-slate-400">({{ $role->id }})</span>
                            </div>
                            <div class="space-y-3">
                                @foreach($modules as $mod)
                                    @php $perm = $role->permissions->firstWhere('module', $mod); @endphp
                                    <div class="flex items-center justify-between pt-1 border-b border-slate-800 pb-2">
                                        <div class="font-mono font-bold text-indigo-300 uppercase text-[11px]">{{ $mod }}</div>
                                        <div class="flex items-center space-x-2 text-[11px]">
                                            <label class="flex items-center space-x-1 bg-slate-900 px-2 py-1 rounded border border-slate-700 cursor-pointer">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_read]" value="1" {{ ($perm && $perm->can_read) ? 'checked' : '' }} class="rounded text-indigo-600">
                                                <span class="text-slate-300">Read</span>
                                            </label>
                                            <label class="flex items-center space-x-1 bg-slate-900 px-2 py-1 rounded border border-slate-700 cursor-pointer">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_create]" value="1" {{ ($perm && $perm->can_create) ? 'checked' : '' }} class="rounded text-indigo-600">
                                                <span class="text-slate-300">Create</span>
                                            </label>
                                            <label class="flex items-center space-x-1 bg-slate-900 px-2 py-1 rounded border border-slate-700 cursor-pointer">
                                                <input type="checkbox" name="permissions[{{ $role->id }}][{{ $mod }}][can_update]" value="1" {{ ($perm && $perm->can_update) ? 'checked' : '' }} class="rounded text-indigo-600">
                                                <span class="text-slate-300">Update</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            @canAccessModule('rbac', 'update')
                <div class="flex justify-end pt-2">
                    <button type="submit" class="w-full md:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-save"></i>
                        <span>Simpan Matriks Permission</span>
                    </button>
                </div>
            @endcanAccessModule
        </div>
    </form>
</div>
@endsection
