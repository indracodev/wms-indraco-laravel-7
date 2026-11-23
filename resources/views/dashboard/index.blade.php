@extends('layouts.app')

@section('title', 'Dashboard - WMS Indraco')

@section('content')
<div class="space-y-6">
    <!-- Clean Page Header Title matching gifanya-adhiyaksa-jaya -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white tracking-tight">Dashboard Superadmin</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau aktivitas dan status operasional gudang seluruh platform.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('scanner.index') }}" class="btn-cyan px-4 py-2 bg-[#00c0ef] hover:bg-[#00acd6] text-white font-semibold text-[13px] rounded-xl shadow-md transition flex items-center gap-2">
                <span>Scanner Simulator</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Stat Cards (Clean Minimalist Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Okupansi BIN -->
        <div class="stat-card">
            <div>
                <p class="text-slate-400 mb-1 font-semibold text-[13px] uppercase tracking-wider">Okupansi BIN</p>
                <h4 class="font-extrabold text-slate-900 dark:text-white text-3xl mb-1">{{ $occupancyRate }}%</h4>
                <p class="text-[13px] text-slate-500 font-medium">{{ $occupiedBins }} dari {{ $totalBins }} BIN Terisi</p>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700/50 h-2 rounded-full mt-3.5 overflow-hidden">
                <div class="bg-[#004b87] h-full rounded-full transition-all duration-500" style="width: {{ min(100, $occupancyRate) }}%"></div>
            </div>
        </div>

        <!-- KPI 2: Total Palet Aktif -->
        <div class="stat-card">
            <div>
                <p class="text-slate-400 mb-1 font-semibold text-[13px] uppercase tracking-wider">Palet Aktif</p>
                <h4 class="font-extrabold text-slate-900 dark:text-white text-3xl mb-1">{{ $totalPallets }}</h4>
                <p class="text-[13px] text-emerald-600 font-semibold">Terdaftar di Rak</p>
            </div>
        </div>

        <!-- KPI 3: Total Qty Stok Box -->
        <div class="stat-card">
            <div>
                <p class="text-slate-400 mb-1 font-semibold text-[13px] uppercase tracking-wider">Total Stok Box</p>
                <h4 class="font-extrabold text-slate-900 dark:text-white text-3xl mb-1">{{ number_format($totalStockQty) }}</h4>
                <p class="text-[13px] text-slate-500 font-medium">Karton / Unit</p>
            </div>
        </div>

        <!-- KPI 4: Critical Full Bins Alert -->
        <div class="stat-card">
            <div>
                <p class="text-amber-500 mb-1 font-semibold text-[13px] uppercase tracking-wider">BIN Kapasitas Penuh (2 Palet)</p>
                <h4 class="font-extrabold text-slate-900 dark:text-white text-3xl mb-1">{{ $fullBins->count() }}</h4>
                <p class="text-[13px] text-slate-500 font-medium">Mencapai Batas Maksimal</p>
            </div>
        </div>
    </div>

    <!-- Critical Bin Capacity Alert Warning Box -->
    @if($fullBins->count() > 0)
    <div class="bg-[#fffbeb] dark:bg-amber-950/60 border border-[#fde68a] dark:border-amber-500/40 rounded-2xl p-4 sm:p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-3.5">
            <div class="text-[#78350f] dark:text-amber-200 font-bold text-sm">
                <span class="leading-snug">Peringatan Kapasitas BIN Maksimal (2 Palet per BIN Rule)</span>
            </div>
            <span class="text-xs sm:text-[13px] bg-[#d97706] text-white px-3 py-1 rounded-full font-bold shadow-xs whitespace-nowrap self-start sm:self-auto">
                {{ $fullBins->count() }} BIN Penuh
            </span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($fullBins as $fbin)
                <div class="bg-white dark:bg-slate-900 border border-[#fcd34d] dark:border-slate-700/80 p-3 sm:p-3.5 rounded-xl flex items-center justify-between text-[13px] shadow-xs gap-3">
                    <div class="min-w-0">
                        <div class="font-extrabold text-slate-900 dark:text-white text-[13px] truncate">
                            BIN: <span class="font-mono text-[#004b87] dark:text-sky-300">{{ $fbin->id }}</span>
                        </div>
                        <div class="text-slate-500 dark:text-slate-400 text-xs truncate mt-0.5 font-medium">
                            {{ $fbin->rack->warehouse->name ?? 'Gudang' }} • Rak: {{ $fbin->rack->id ?? '-' }}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-bold text-rose-600 dark:text-rose-400 text-xs whitespace-nowrap">
                            {{ $fbin->pallets->count() }} / {{ $fbin->max_pallet }} Palet
                        </div>
                        <div class="mt-0.5">
                            <span class="inline-block px-2 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-300 font-extrabold text-[10px] rounded-md border border-rose-200 dark:border-rose-800 whitespace-nowrap uppercase tracking-wider">
                                (FULL)
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Content Grid: Stock Expiring & Recent Mutasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expiring Stock Alert -->
        <div class="stat-card space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">
                    <span>Produk Mendekati Expired (&lt; 90 Hari)</span>
                </h3>
            </div>
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-[#004b87] dark:text-sky-300 uppercase tracking-wider font-bold border-b-2 border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3">Box ID / Palet</th>
                            <th class="p-3">Produk</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">BIN</th>
                            <th class="p-3">Expired Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($expiringBoxes as $box)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3 font-semibold text-[#004b87] dark:text-sky-300">
                                    {{ $box->id }}
                                    <div class="text-[13px] text-slate-400 font-normal">{{ $box->pallet_id }}</div>
                                </td>
                                <td class="p-3 text-slate-900 dark:text-white font-medium">{{ $box->product->name ?? $box->product_id }}</td>
                                <td class="p-3 font-bold text-emerald-600 dark:text-emerald-400">{{ $box->qty }}</td>
                                <td class="p-3 text-amber-700 dark:text-amber-300 font-mono font-semibold">{{ $box->pallet->bin_id ?? 'Transit' }}</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 rounded-full font-semibold text-[13px] bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500 text-[13px]">Tidak ada produk mendekati masa kedaluwarsa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View: Identik Riwayat Transaksi (Gambar 4 Style) -->
            <div class="space-y-3 md:hidden">
                @forelse($expiringBoxes as $box)
                    <div class="p-3 bg-white dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60 flex items-center justify-between text-[13px] gap-3 shadow-xs">
                        <div class="flex items-center space-x-3 min-w-0">
                            <!-- Left Squircle Icon (Amber Clock / Expired Alert) -->
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white bg-amber-500 shadow-xs flex-shrink-0">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            </span>
                            <!-- Middle Product Name & Box/Bin Info -->
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate text-[13px]">
                                    {{ $box->product->name ?? $box->product_id }}
                                </div>
                                <div class="text-slate-500 dark:text-slate-400 text-xs truncate mt-0.5">
                                    Box: <span class="font-mono text-slate-700 dark:text-slate-300 font-semibold">{{ $box->id }}</span> • Palet: {{ $box->pallet_id }} • BIN: <span class="font-mono font-bold text-[#004b87] dark:text-sky-300">{{ $box->pallet->bin_id ?? 'Transit' }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Right Side: Qty Karton & Expired Date Pill (Rapi, Tidak Terpotong, Format 1 Baris) -->
                        <div class="text-right flex-shrink-0">
                            <div class="font-bold text-slate-900 dark:text-white text-xs whitespace-nowrap">
                                {{ $box->qty }} Karton
                            </div>
                            <div class="mt-0.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 font-bold text-[11px] rounded-full border border-amber-300 dark:border-amber-700 whitespace-nowrap shadow-2xs">
                                    <i class="fa-regular fa-calendar text-[10px]"></i> {{ \Carbon\Carbon::parse($box->expired_date)->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-4 text-[13px]">Tidak ada produk mendekati masa kedaluwarsa.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions Log -->
        <div class="stat-card space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">
                    <span>Riwayat Transaksi Terakhir</span>
                </h3>
            </div>
            <div class="space-y-3">
                @forelse($recentTransactions as $tx)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-200 dark:border-slate-700/60 flex items-center justify-between text-[13px] gap-2.5">
                        <div class="flex items-center space-x-3 min-w-0">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs text-white shadow-xs {{ $tx->status === 'In' ? 'bg-[#004b87]' : 'bg-[#eab308]' }} flex-shrink-0">
                                {{ $tx->status }}
                            </span>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 dark:text-white truncate text-sm">{{ $tx->product->name ?? $tx->product_id }}</div>
                                <div class="text-slate-500 dark:text-slate-400 text-xs truncate">
                                    {{ $tx->qty }} {{ $tx->unit }} • BIN: <span class="text-[#004b87] dark:text-sky-300 font-mono font-semibold">{{ $tx->bin_id }}</span> • Palet: {{ $tx->pallet_id }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-slate-700 dark:text-slate-300 font-medium text-xs whitespace-nowrap">{{ $tx->user->name ?? 'System' }}</div>
                            <div class="text-[11px] text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->transaction_date)->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-4 text-[13px]">Belum ada data transaksi recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
