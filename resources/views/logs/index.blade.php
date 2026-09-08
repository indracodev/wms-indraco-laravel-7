@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ $activeTab }}' }">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-400"></i>
                <span>Audit Trail & System Logs</span>
            </h2>
            <p class="text-sm text-slate-400">Pencatatan Jejak Aktivitas User, Mutasi Transaksi In/Out, & Log Impersonasi</p>
        </div>
    </div>

    <!-- Sub Tabs Navigation -->
    <div class="flex space-x-2 border-b border-slate-700/80 pb-2 overflow-x-auto">
        <button @click="tab = 'transaction'" :class="tab === 'transaction' ? 'bg-indigo-600 !text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer">
            <i class="fa-solid fa-right-left text-sm"></i>
            <span>Log Mutasi Transaksi</span>
        </button>
        <button @click="tab = 'audit'" :class="tab === 'audit' ? 'bg-indigo-600 !text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer">
            <i class="fa-solid fa-list-check text-sm"></i>
            <span>Log Audit Aktivitas Eloquent</span>
        </button>
        <button @click="tab = 'impersonation'" :class="tab === 'impersonation' ? 'bg-indigo-600 !text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer">
            <i class="fa-solid fa-user-secret text-sm"></i>
            <span>Log Fitur Login-As (Impersonasi)</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: TRANSACTION LOGS                                                   -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'transaction'" class="glass-panel p-6 rounded-2xl shadow-xl space-y-4">
        <!-- Search & Filter Toolbar -->
        <form method="GET" action="{{ route('logs.index') }}" class="space-y-3 text-xs">
            <input type="hidden" name="tab" value="transaction">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-slate-400 mb-1">Cari Keyword</label>
                    <input type="text" name="tx_search" value="{{ request('tx_search') }}" placeholder="🔍 Produk, User, BIN, Palet..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Filter Produk SKU</label>
                    <select name="tx_product" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Produk SKU --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('tx_product') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Filter Lokasi Gudang</label>
                    <select name="tx_wh" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Gudang --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ request('tx_wh') == $w->id ? 'selected' : '' }}>{{ $w->name }} ({{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Filter Rak Penyimpanan</label>
                    <select name="tx_rack" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Rak --</option>
                        @foreach($racks as $r)
                            <option value="{{ $r->id }}" {{ request('tx_rack') == $r->id ? 'selected' : '' }}>Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-slate-400 mb-1">Filter BIN Slot</label>
                    <select name="tx_bin" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua BIN --</option>
                        @foreach($bins as $b)
                            <option value="{{ $b->id }}" {{ request('tx_bin') == $b->id ? 'selected' : '' }}>BIN: {{ $b->id }} (Rak: {{ $b->rack_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Filter Palet ID</label>
                    <select name="tx_pallet" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Palet --</option>
                        @foreach($pallets as $plt)
                            <option value="{{ $plt->id }}" {{ request('tx_pallet') == $plt->id ? 'selected' : '' }}>Palet: {{ $plt->id }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Status Mutasi</label>
                    <select name="tx_status" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Status --</option>
                        <option value="In" {{ request('tx_status') == 'In' ? 'selected' : '' }}>Mutasi IN (Inbound)</option>
                        <option value="Out" {{ request('tx_status') == 'Out' ? 'selected' : '' }}>Mutasi OUT (Outbound)</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow">Filter Log</button>
                    <a href="{{ route('logs.index', ['tab' => 'transaction']) }}" class="px-3 py-2.5 bg-slate-800 text-slate-300 font-semibold rounded-xl border border-slate-700">Reset</a>
                </div>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800 text-slate-300 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Waktu Transaksi</th>
                        <th class="p-3">PIC User</th>
                        <th class="p-3">Status Mutasi</th>
                        <th class="p-3">Produk SKU</th>
                        <th class="p-3">Qty Mutasi</th>
                        <th class="p-3">BIN & Palet</th>
                        <th class="p-3">Gudang & Rak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-800/40">
                            <td class="p-3 text-slate-400 font-mono">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d H:i:s') }}</td>
                            <td class="p-3 font-semibold text-white">{{ $tx->user->name ?? $tx->pic_user_id }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $tx->status === 'In' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : 'bg-rose-950 text-rose-300 border border-rose-500/40' }}">
                                    MUTASI {{ strtoupper($tx->status) }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-200 font-medium">{{ $tx->product->name ?? $tx->product_id }}</td>
                            <td class="p-3">
                                <div class="font-bold text-emerald-400">
                                    {{ number_format($tx->qty_input ?? $tx->qty) }} {{ $tx->uom_input ?? $tx->unit }}
                                </div>
                                @if($tx->uom_base && $tx->uom_input !== $tx->uom_base)
                                    <div class="text-[10px] text-sky-400 font-mono">
                                        = {{ number_format($tx->qty_base ?? $tx->qty) }} {{ $tx->uom_base }}
                                    </div>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="font-mono text-indigo-300 font-bold">BIN: {{ $tx->bin_id }}</span>
                                <div class="text-[10px] text-slate-400">Palet: {{ $tx->pallet_id }}</div>
                            </td>
                            <td class="p-3">
                                <div class="text-amber-300 font-mono font-bold">{{ $tx->warehouse_id }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">Rak: {{ $tx->bin->rack_id ?? '-' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-slate-500">Tidak ada log transaksi ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Flat Card List -->
        <div class="space-y-2.5 md:hidden">
            @forelse($transactions as $tx)
                <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl space-y-1.5 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $tx->status === 'In' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : 'bg-rose-950 text-rose-300 border border-rose-500/40' }}">
                            MUTASI {{ strtoupper($tx->status) }}
                        </span>
                        <span class="text-slate-400 font-mono text-[10px]">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d M H:i') }}</span>
                    </div>
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-bold text-white text-sm">{{ $tx->product->name ?? $tx->product_id }}</div>
                            <div class="text-slate-400 text-[11px]">PIC: <span class="text-slate-200 font-semibold">{{ $tx->user->name ?? $tx->pic_user_id }}</span></div>
                        </div>
                        <div class="text-right font-extrabold text-emerald-400 text-sm">
                            <div>{{ number_format($tx->qty_input ?? $tx->qty) }} {{ $tx->uom_input ?? $tx->unit }}</div>
                            @if($tx->uom_base && $tx->uom_input !== $tx->uom_base)
                                <div class="text-[10px] text-sky-400 font-mono font-normal">= {{ number_format($tx->qty_base ?? $tx->qty) }} {{ $tx->uom_base }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-800">
                        <span>BIN: <strong class="text-indigo-300 font-mono">{{ $tx->bin_id }}</strong> (Rak: {{ $tx->bin->rack_id ?? '-' }})</span>
                        <span>Palet: <strong class="text-slate-200 font-mono">{{ $tx->pallet_id }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Tidak ada log transaksi ditemukan.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: AUDIT LOGS (ELOQUENT ACTIVITES)                                    -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'audit'" class="glass-panel p-6 rounded-2xl shadow-xl space-y-4">
        <!-- Search & Filter Toolbar -->
        <form method="GET" action="{{ route('logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <input type="hidden" name="tab" value="audit">
            
            <div>
                <label class="block text-slate-400 mb-1">Cari Keyword Aktivitas</label>
                <input type="text" name="audit_search" value="{{ request('audit_search') }}" placeholder="🔍 Modul, Deskripsi, User..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Filter Modul</label>
                <select name="audit_module" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Semua Modul --</option>
                    @foreach($auditModules as $m)
                        <option value="{{ $m }}" {{ request('audit_module') == $m ? 'selected' : '' }}>{{ strtoupper($m) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Filter Aksi</label>
                <select name="audit_action" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Semua Aksi --</option>
                    @foreach($auditActions as $a)
                        <option value="{{ $a }}" {{ request('audit_action') == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Urutan Timestamp</label>
                <select name="dir" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="desc" {{ request('dir', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                    <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow">Filter</button>
                <a href="{{ route('logs.index', ['tab' => 'audit']) }}" class="px-3 py-2.5 bg-slate-800 text-slate-300 font-semibold rounded-xl border border-slate-700">Reset</a>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800 text-slate-300 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Timestamp</th>
                        <th class="p-3">User PIC</th>
                        <th class="p-3">Modul</th>
                        <th class="p-3">Tipe Aksi</th>
                        <th class="p-3">Deskripsi Rincian Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($audits as $aud)
                        <tr class="hover:bg-slate-800/40">
                            <td class="p-3 text-slate-400 font-mono">{{ $aud->timestamp }}</td>
                            <td class="p-3 font-semibold text-indigo-300">{{ $aud->user->name ?? $aud->user_id ?? 'System' }}</td>
                            <td class="p-3 font-mono text-slate-300 uppercase font-bold">{{ $aud->module }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-900 text-amber-300 border border-slate-700">
                                    {{ $aud->action_type }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-200">{{ $aud->description }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada log audit ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Flat Card List -->
        <div class="space-y-2.5 md:hidden">
            @forelse($audits as $aud)
                <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl space-y-1.5 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                        <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-900 text-amber-300 border border-slate-700">
                            {{ $aud->action_type }} • {{ strtoupper($aud->module) }}
                        </span>
                        <span class="text-slate-400 font-mono text-[10px]">{{ $aud->timestamp }}</span>
                    </div>
                    <div class="text-white font-medium">{{ $aud->description }}</div>
                    <div class="text-[11px] text-slate-400 pt-1 border-t border-slate-800">
                        User: <span class="text-indigo-300 font-semibold">{{ $aud->user->name ?? $aud->user_id ?? 'System' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Tidak ada log audit ditemukan.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $audits->links() }}</div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: IMPERSONATION LOGS (LOGIN AS)                                      -->
    <!-- ========================================================================= -->
    <div x-show="tab === 'impersonation'" class="glass-panel p-6 rounded-2xl shadow-xl space-y-4">
        <!-- Search & Filter Toolbar -->
        <form method="GET" action="{{ route('logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <input type="hidden" name="tab" value="impersonation">
            
            <div>
                <label class="block text-slate-400 mb-1">Cari Nama Admin / Target User</label>
                <input type="text" name="imp_search" value="{{ request('imp_search') }}" placeholder="🔍 Nama Admin / Target..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Status Sesi</label>
                <select name="imp_status" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Semua Sesi --</option>
                    <option value="active" {{ request('imp_status') == 'active' ? 'selected' : '' }}>Masih Aktif</option>
                    <option value="ended" {{ request('imp_status') == 'ended' ? 'selected' : '' }}>Selesai (Reverted)</option>
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Urutan Waktu</label>
                <select name="dir" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="desc" {{ request('dir', 'desc') == 'desc' ? 'selected' : '' }}>Terbaru (DESC)</option>
                    <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>Terlama (ASC)</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl shadow">Filter</button>
                <a href="{{ route('logs.index', ['tab' => 'impersonation']) }}" class="px-3 py-2.5 bg-slate-800 text-slate-300 font-semibold rounded-xl border border-slate-700">Reset</a>
            </div>
        </form>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800 text-slate-300 uppercase tracking-wider">
                    <tr>
                        <th class="p-3">Super Admin</th>
                        <th class="p-3">Target User (Login As)</th>
                        <th class="p-3">Waktu Mulai</th>
                        <th class="p-3">Waktu Selesai (Revert)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($impersonations as $imp)
                        <tr class="hover:bg-slate-800/40">
                            <td class="p-3 font-bold text-amber-400">{{ $imp->admin->name ?? $imp->admin_user_id }}</td>
                            <td class="p-3 font-bold text-emerald-400">{{ $imp->target->name ?? $imp->target_user_id }}</td>
                            <td class="p-3 text-slate-300 font-mono">{{ $imp->started_at }}</td>
                            <td class="p-3 font-mono">
                                @if($imp->ended_at)
                                    <span class="text-slate-400">{{ $imp->ended_at }}</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded font-bold text-[10px]">Masih Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-6 text-center text-slate-500">Tidak ada log impersonasi ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Flat Card List -->
        <div class="space-y-2.5 md:hidden">
            @forelse($impersonations as $imp)
                <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl space-y-2 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                        <span class="font-bold text-amber-400">Admin: {{ $imp->admin->name ?? $imp->admin_user_id }}</span>
                        <span class="text-slate-400 font-mono text-[10px]">{{ $imp->started_at }}</span>
                    </div>
                    <div class="text-emerald-400 font-bold text-sm">
                        Login As &rarr; {{ $imp->target->name ?? $imp->target_user_id }}
                    </div>
                    <div class="text-[11px] text-slate-400 pt-1 border-t border-slate-800 flex justify-between">
                        <span>Selesai Revert:</span>
                        <span class="font-mono text-slate-300">{{ $imp->ended_at ?? 'Masih Aktif' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 text-xs">Tidak ada log impersonasi ditemukan.</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $impersonations->links() }}</div>
    </div>
</div>
@endsection
