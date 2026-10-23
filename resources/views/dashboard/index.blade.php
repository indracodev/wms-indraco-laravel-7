@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-indigo-900/50 via-slate-800 to-slate-900 p-6 rounded-2xl border border-indigo-500/20 shadow-xl">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Selamat Datang, {{ $user->name }}!</h2>
            <p class="text-sm text-slate-300 mt-1">
                Anda masuk sebagai <span class="text-indigo-400 font-semibold uppercase tracking-wider">{{ $user->role->name }}</span> (Shift {{ $user->shift }}). Berikut adalah ringkasan status operasional gudang hari ini.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('scanner.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2">
                <i class="fa-solid fa-barcode"></i>
                <span>Scanner Simulator</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Okupansi BIN -->
        <div class="glass-panel p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Okupansi BIN</div>
            <div class="text-3xl font-extrabold text-white mt-1">{{ $occupancyRate }}%</div>
            <div class="text-xs text-slate-400 mt-1">{{ $occupiedBins }} dari {{ $totalBins }} BIN Terisi</div>
            <div class="w-full bg-slate-700/50 h-2 rounded-full mt-3 overflow-hidden">
                <div class="bg-indigo-500 h-full rounded-full" style="width: {{ min(100, $occupancyRate) }}%"></div>
            </div>
        </div>

        <!-- KPI 2: Total Palet Aktif -->
        <div class="glass-panel p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Palet Aktif</div>
            <div class="text-3xl font-extrabold text-white mt-1">{{ $totalPallets }}</div>
            <div class="text-xs text-emerald-500 font-medium mt-1">Terdaftar di Rak</div>
        </div>

        <!-- KPI 3: Total Qty Stok Box -->
        <div class="glass-panel p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Stok Box</div>
            <div class="text-3xl font-extrabold text-white mt-1">{{ number_format($totalStockQty) }}</div>
            <div class="text-xs text-slate-400 mt-1">Karton / Unit</div>
        </div>

        <!-- KPI 4: Critical Full Bins Alert -->
        <div class="glass-panel p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="text-xs font-semibold text-amber-500 uppercase tracking-wider">BIN Kapasitas Penuh (2 Palet)</div>
            <div class="text-3xl font-extrabold text-white mt-1">{{ $fullBins->count() }}</div>
            <div class="text-xs text-slate-400 mt-1">Mencapai Batas Maksimal</div>
        </div>
    </div>

    <!-- Critical Bin Capacity Alert Warning Box -->
    @if($fullBins->count() > 0)
    <div class="bg-amber-950/60 border border-amber-500/40 rounded-2xl p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-base font-bold text-amber-300">
                Peringatan Kapasitas BIN Maksimal (2 Palet per BIN Rule)
            </h3>
            <span class="text-xs bg-amber-900/80 text-amber-200 px-3 py-1 rounded-full font-semibold">
                {{ $fullBins->count() }} BIN Penuh
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($fullBins as $fbin)
                <div class="bg-slate-900/80 border border-slate-700/80 p-3 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <div class="font-bold text-white text-sm">BIN: {{ $fbin->id }}</div>
                        <div class="text-slate-400">{{ $fbin->rack->warehouse->name ?? '' }}</div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 bg-amber-500/20 border border-amber-500/30 text-amber-300 font-bold rounded">
                            {{ $fbin->pallets->count() }} / {{ $fbin->max_pallet }} Palet (FULL)
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Content Grid: Stock Expiring & Recent Mutasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expiring Stock Alert -->
        <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl">
            <h3 class="text-base sm:text-lg font-bold text-white mb-4">
                Produk Mendekati Expired (&lt; 90 Hari)
            </h3>
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/80 text-slate-300 uppercase tracking-wider">
                        <tr>
                            <th class="p-3">Box ID / Palet</th>
                            <th class="p-3">Produk</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">BIN</th>
                            <th class="p-3">Expired Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($expiringBoxes as $box)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="p-3 font-semibold text-indigo-300">
                                    {{ $box->id }}
                                    <div class="text-[10px] text-slate-500">{{ $box->pallet_id }}</div>
                                </td>
                                <td class="p-3 text-white font-medium">{{ $box->product->name ?? $box->product_id }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ $box->qty }}</td>
                                <td class="p-3 text-amber-300 font-mono">{{ $box->pallet->bin_id ?? 'Transit' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded font-semibold bg-amber-950 text-amber-300 border border-amber-500/30">
                                        {{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">Tidak ada produk mendekati masa kedaluwarsa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List View (No Card inside Card) -->
            <div class="space-y-2.5 md:hidden">
                @forelse($expiringBoxes as $box)
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl flex items-center justify-between text-xs">
                        <div class="space-y-1">
                            <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $box->product->name ?? $box->product_id }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                                Box: <span class="text-indigo-600 dark:text-indigo-300 font-bold">{{ $box->id }}</span> • Palet: {{ $box->pallet_id }}
                            </div>
                            <div class="flex items-center space-x-2 text-[11px]">
                                <span class="text-slate-500 dark:text-slate-400">Lokasi:</span>
                                <span class="px-2 py-0.5 rounded font-mono font-bold bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                    {{ $box->pallet->bin_id ?? 'Transit' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">{{ $box->qty }} <span class="text-[10px] text-slate-500 dark:text-slate-400">Karton</span></div>
                            <div class="px-2 py-0.5 rounded font-semibold bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 text-[10px]">
                                {{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada produk mendekati masa kedaluwarsa.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions Log -->
        <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl">
            <h3 class="text-base sm:text-lg font-bold text-white mb-4">
                Riwayat Transaksi Terakhir
            </h3>
            <div class="space-y-3">
                @forelse($recentTransactions as $tx)
                    <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white {{ $tx->status === 'In' ? 'bg-emerald-600' : 'bg-rose-600' }}">
                                {{ $tx->status }}
                            </span>
                            <div>
                                <div class="font-bold text-white">{{ $tx->product->name ?? $tx->product_id }}</div>
                                <div class="text-slate-400">
                                    {{ $tx->qty }} {{ $tx->unit }} • BIN: <span class="text-indigo-300 font-mono">{{ $tx->bin_id }}</span> • Palet: {{ $tx->pallet_id }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-slate-300 font-medium">{{ $tx->user->name ?? 'System' }}</div>
                            <div class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($tx->transaction_date)->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-4">Belum ada data transaksi recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
