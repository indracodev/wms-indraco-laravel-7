<!-- Overview Statistics Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3.5">
    <!-- Total Palet -->
    <div class="stat-card p-4 rounded-2xl flex items-center justify-between">
        <div>
            <div class="text-slate-500 dark:text-slate-400 text-[13px] font-medium">Total Palet</div>
            <div class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="icon-box" style="background: rgba(0, 75, 135, 0.1); color: #004b87;">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>

    <!-- Palet On Rack -->
    <div class="stat-card p-4 rounded-2xl flex items-center justify-between">
        <div>
            <div class="text-slate-500 dark:text-slate-400 text-[13px] font-medium">Di Rak (On Rack)</div>
            <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['active_with_stock'] - ($stats['in_transit'] ?? 0)) }}</div>
        </div>
        <div class="icon-box" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <!-- Palet In Transit -->
    <div class="stat-card p-4 rounded-2xl flex items-center justify-between">
        <div>
            <div class="text-slate-500 dark:text-slate-400 text-[13px] font-medium">In Transit (Dock)</div>
            <div class="text-2xl font-extrabold text-[#00c0ef] mt-1">{{ number_format($stats['in_transit'] ?? 0) }}</div>
        </div>
        <div class="icon-box" style="background: rgba(0, 192, 239, 0.1); color: #00c0ef;">
            <i class="fa-solid fa-dolly"></i>
        </div>
    </div>

    <!-- Palet Kosong -->
    <div class="stat-card p-4 rounded-2xl flex items-center justify-between">
        <div>
            <div class="text-slate-500 dark:text-slate-400 text-[13px] font-medium">Palet Kosong</div>
            <div class="text-2xl font-extrabold text-[#f39c12] mt-1">{{ number_format($stats['empty_transit']) }}</div>
        </div>
        <div class="icon-box" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
            <i class="fa-solid fa-box-open"></i>
        </div>
    </div>

    <!-- Palet Non-Aktif -->
    <div class="stat-card p-4 rounded-2xl flex items-center justify-between">
        <div>
            <div class="text-slate-500 dark:text-slate-400 text-[13px] font-medium">Non-Aktif</div>
            <div class="text-2xl font-extrabold text-slate-500 dark:text-slate-400 mt-1">{{ number_format($stats['inactive']) }}</div>
        </div>
        <div class="icon-box" style="background: rgba(100, 116, 139, 0.1); color: #64748b;">
            <i class="fa-solid fa-wrench"></i>
        </div>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass-panel p-6 rounded-2xl shadow-xl overflow-hidden mt-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[13px]">
            <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="p-3.5">Palet Barcode</th>
                    <th class="p-3.5">Jenis & Kapasitas</th>
                    <th class="p-3.5">Lokasi BIN & Gudang</th>
                    <th class="p-3.5">Status Palet</th>
                    <th class="p-3.5">Isi Box & Stok Terkecil</th>
                    <th class="p-3.5 text-center">Aksi / Kontrol</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($pallets as $plt)
                    @php
                        $activeBoxes = $plt->boxes->where('status', 'active');
                        $hasBoxes = $activeBoxes->count() > 0;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <!-- Barcode ID -->
                        <td class="p-3.5 font-mono font-bold text-[#004b87] dark:text-indigo-400 text-sm">
                            <i class="fa-solid fa-pallet text-xs mr-1 text-slate-400"></i>
                            {{ $plt->id }}
                        </td>

                        <!-- Jenis Palet & Kapasitas -->
                        <td class="p-3.5">
                            <div class="font-semibold text-slate-900 dark:text-white">{{ $plt->type->name ?? $plt->pallet_type_id }}</div>
                            <div class="text-[13px] text-slate-500 dark:text-slate-400 font-mono">
                                Kap: <span class="font-bold text-[#004b87] dark:text-indigo-300">{{ $plt->max_capacity ?? 72 }}</span> Unit
                            </div>
                        </td>

                        <!-- Lokasi BIN & Gudang -->
                        <td class="p-3.5">
                            @if($plt->bin_id)
                                <span class="px-2.5 py-1 rounded font-mono font-bold bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-500/30 text-[13px]">
                                    BIN: {{ $plt->bin_id }}
                                </span>
                                <div class="text-[13px] text-slate-500 dark:text-slate-400 mt-1">
                                    {{ $plt->bin->rack->warehouse->name ?? 'Gudang Utama' }}
                                </div>
                            @else
                                <span class="px-2.5 py-1 rounded font-mono font-bold bg-sky-500/20 text-[#004b87] dark:text-sky-300 border border-sky-500/30 text-[13px]">
                                    IN TRANSIT (DOCK)
                                </span>
                            @endif
                        </td>

                        <!-- Status Palet -->
                        <td class="p-3.5 font-semibold">
                            @if(!$plt->is_active)
                                <span class="px-2.5 py-1 rounded font-bold text-[13px] bg-slate-100 dark:bg-slate-800 text-rose-600 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30">
                                    NON-AKTIF
                                </span>
                            @elseif($plt->status === 'in_transit' || !$plt->bin_id)
                                <span class="px-2.5 py-1 rounded font-bold text-[13px] bg-sky-50 dark:bg-sky-950 text-[#004b87] dark:text-sky-300 border border-sky-200 dark:border-sky-500/40">
                                    IN TRANSIT
                                </span>
                            @elseif($hasBoxes)
                                <span class="px-2.5 py-1 rounded font-bold text-[13px] bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/40">
                                    ON RACK (AKTIF)
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded font-bold text-[13px] bg-amber-50 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-500/40">
                                    KOSONG (READY)
                                </span>
                            @endif
                        </td>

                        <!-- Isi Box & Total Qty Base -->
                        <td class="p-3.5">
                            @if($hasBoxes)
                                <div class="font-bold text-slate-900 dark:text-white text-[13px]">
                                    {{ $activeBoxes->count() }} Box Terdaftar
                                </div>
                                <div class="text-[13px] text-emerald-600 dark:text-emerald-400 font-semibold mt-0.5">
                                    @php
                                        $firstBox = $activeBoxes->first();
                                    @endphp
                                    {{ $firstBox->product->name ?? $firstBox->product_id }} 
                                    <span class="font-extrabold text-[#004b87] dark:text-sky-400 font-mono">({{ $firstBox->qty }} {{ $firstBox->uom_base }})</span>
                                    @if($activeBoxes->count() > 1)
                                        <span class="text-[13px] text-slate-500 dark:text-slate-400">+{{ $activeBoxes->count() - 1 }} produk lain</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 text-[13px] font-mono">Belum ada box</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="p-3.5 text-center space-x-1 whitespace-nowrap">
                            <!-- Tambah Produk Ke Palet Ini: Green #59c594 -->
                            @canAccessModule('inbound', 'create')
                                <button type="button" 
                                        @click="openAddBoxModal('{{ $plt->id }}')" 
                                        class="btn-green p-2 bg-[#59c594] hover:bg-[#48b282] text-white rounded-lg transition cursor-pointer shadow-xs" 
                                        title="Tambah Box & Produk ke Palet ini">
                                    <i class="fa-solid fa-square-plus text-xs"></i>
                                </button>
                            @endcanAccessModule

                            <!-- Check Isi: Cyan #00c0ef -->
                            <button type="button" 
                                    @click="inspectPallet('{{ $plt->id }}')" 
                                    class="btn-cyan p-2 bg-[#00c0ef] hover:bg-[#00acd6] text-white rounded-lg transition cursor-pointer shadow-xs" 
                                    title="Check Isi Palet">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>

                            <!-- Pallet Tracking: Purple #8e44ad -->
                            <button type="button" 
                                    @click="trackPallet('{{ $plt->id }}')" 
                                    class="p-2 bg-[#8e44ad] hover:bg-[#732d91] text-white rounded-lg transition cursor-pointer shadow-xs" 
                                    title="Pallet Tracking & History">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            </button>

                            <!-- Print Label Barcode: Cyan #00c0ef -->
                            <button type="button" 
                                    @click="printLabel('{{ $plt->id }}', '{{ $plt->bin_id ?? 'TRANSIT' }}', '{{ $plt->type->name ?? $plt->pallet_type_id }}')" 
                                    class="btn-cyan p-2 bg-[#00c0ef] hover:bg-[#00acd6] text-white rounded-lg transition cursor-pointer shadow-xs" 
                                    title="Cetak Label Barcode Palet">
                                <i class="fa-solid fa-barcode text-xs"></i>
                            </button>

                            <!-- Edit: Amber #f39c12 -->
                            @canAccessModule('pallet', 'update')
                                <button type="button" 
                                        @click="editPallet('{{ $plt->id }}', '{{ $plt->pallet_type_id }}', '{{ $plt->bin_id }}', {{ $plt->is_active ? 1 : 0 }})" 
                                        class="btn-amber p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition cursor-pointer shadow-xs" 
                                        title="Edit / Relokasi Palet">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            @endcanAccessModule

                            <!-- Delete: Danger Red #dd4b39 -->
                            @canAccessModule('pallet', 'delete')
                                @if(!$hasBoxes)
                                    <form action="{{ route('pallets.destroy', $plt->id) }}" method="POST" onsubmit="PalletApp.deleteItem(event, this)" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger p-2 bg-[#dd4b39] hover:bg-[#c0392b] text-white rounded-lg transition cursor-pointer shadow-xs" title="Hapus Palet Kosong">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            @endcanAccessModule
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-slate-500 font-medium">
                            Tidak ada data palet yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $pallets->appends(request()->query())->links() }}
    </div>
</div>

<!-- Mobile Card List View -->
<div class="space-y-3 md:hidden mt-6">
    @forelse($pallets as $plt)
        @php
            $activeBoxes = $plt->boxes->where('status', 'active');
            $hasBoxes = $activeBoxes->count() > 0;
        @endphp
        <div class="bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl space-y-3 text-xs">
            <!-- Header: ID + BIN Badge -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                <div class="font-mono font-bold text-indigo-400 text-sm flex items-center">
                    <i class="fa-solid fa-pallet text-xs mr-1.5 text-slate-400"></i>
                    {{ $plt->id }}
                </div>
                <span class="px-2.5 py-0.5 rounded font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs">
                    BIN: {{ $plt->bin_id ?? 'TRANSIT' }}
                </span>
            </div>

            <!-- Info Jenis & Status -->
            <div class="flex items-start justify-between text-xs">
                <div>
                    <div class="text-slate-400">Jenis & Kapasitas:</div>
                    <div class="font-bold text-white">{{ $plt->type->name ?? $plt->pallet_type_id }} ({{ $plt->max_capacity ?? 72 }} Unit)</div>
                </div>
                <div>
                    @if(!$plt->is_active)
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-slate-800 text-rose-400 border border-rose-500/30">
                            NON-AKTIF
                        </span>
                    @elseif($plt->status === 'in_transit' || !$plt->bin_id)
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-sky-950 text-sky-300 border border-sky-500/40">
                            IN TRANSIT
                        </span>
                    @elseif($hasBoxes)
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-950 text-emerald-300 border border-emerald-500/40">
                            ON RACK (AKTIF)
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-950 text-amber-300 border border-amber-500/40">
                            KOSONG (READY)
                        </span>
                    @endif
                </div>
            </div>

            <!-- Isi Box Summary -->
            <div class="p-2.5 bg-slate-900/80 rounded-lg border border-slate-800">
                @if($hasBoxes)
                    <div class="font-bold text-white text-xs">
                        {{ $activeBoxes->count() }} Box Terdaftar
                    </div>
                    <div class="text-[11px] text-emerald-400 mt-0.5">
                        @php $firstBox = $activeBoxes->first(); @endphp
                        {{ $firstBox->product->name ?? $firstBox->product_id }} 
                        <span class="font-bold text-sky-400 font-mono">({{ $firstBox->qty }} {{ $firstBox->uom_base }})</span>
                    </div>
                @else
                    <span class="text-slate-500 text-xs font-mono">Kosong - Belum ada box</span>
                @endif
            </div>

            <!-- Action Toolbar -->
            <div class="pt-2 border-t border-slate-700/40 flex items-center justify-between">
                <div class="flex items-center space-x-1.5">
                    @canAccessModule('inbound', 'create')
                        <button type="button" @click="openAddBoxModal('{{ $plt->id }}')" class="px-2.5 py-1.5 bg-emerald-600 text-white font-semibold rounded-lg text-xs flex items-center space-x-1 cursor-pointer">
                            <i class="fa-solid fa-square-plus"></i>
                            <span>+ Box</span>
                        </button>
                    @endcanAccessModule
                    <button type="button" @click="inspectPallet('{{ $plt->id }}')" class="px-2.5 py-1.5 bg-indigo-600 text-white font-semibold rounded-lg text-xs cursor-pointer">
                        <i class="fa-solid fa-eye"></i> Check
                    </button>
                    <button type="button" @click="trackPallet('{{ $plt->id }}')" class="px-2.5 py-1.5 bg-purple-600 text-white font-semibold rounded-lg text-xs cursor-pointer">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-1">
                    <button type="button" @click="printLabel('{{ $plt->id }}', '{{ $plt->bin_id ?? 'TRANSIT' }}', '{{ $plt->type->name ?? $plt->pallet_type_id }}')" class="p-1.5 bg-sky-600 text-white rounded-lg cursor-pointer">
                        <i class="fa-solid fa-barcode"></i>
                    </button>
                    @canAccessModule('pallet', 'update')
                        <button type="button" @click="editPallet('{{ $plt->id }}', '{{ $plt->pallet_type_id }}', '{{ $plt->bin_id }}', {{ $plt->is_active ? 1 : 0 }})" class="p-1.5 bg-amber-600 text-white rounded-lg cursor-pointer">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                    @endcanAccessModule
                </div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-slate-500 text-xs bg-slate-800/40 rounded-xl">
            Tidak ada data palet yang cocok dengan filter pencarian.
        </div>
    @endforelse

    <div class="mt-4">
        {{ $pallets->appends(request()->query())->links() }}
    </div>
</div>
