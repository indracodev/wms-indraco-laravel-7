<!-- Overview Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <!-- Total Palet -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <div class="text-slate-400 text-xs font-medium">Total Palet Terdaftar</div>
            <div class="text-2xl font-extrabold text-white mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
            <i class="fa-solid fa-layer-group"></i>
        </div>
    </div>

    <!-- Palet Terisi Stock -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <div class="text-slate-400 text-xs font-medium">Palet Terisi Stok</div>
            <div class="text-2xl font-extrabold text-emerald-400 mt-1">{{ number_format($stats['active_with_stock']) }}</div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
    </div>

    <!-- Palet Kosong / Transit -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <div class="text-slate-400 text-xs font-medium">Palet Kosong / Transit</div>
            <div class="text-2xl font-extrabold text-amber-400 mt-1">{{ number_format($stats['empty_transit']) }}</div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
            <i class="fa-solid fa-box-open"></i>
        </div>
    </div>

    <!-- Palet Non-Aktif / Maint -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
        <div>
            <div class="text-slate-400 text-xs font-medium">Non-Aktif / Maintenance</div>
            <div class="text-2xl font-extrabold text-slate-400 mt-1">{{ number_format($stats['inactive']) }}</div>
        </div>
        <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center text-lg">
            <i class="fa-solid fa-wrench"></i>
        </div>
    </div>
</div>

<!-- Desktop Table View -->
<div class="hidden md:block glass-panel p-6 rounded-2xl shadow-xl overflow-hidden mt-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-800/90 text-slate-300 uppercase tracking-wider font-semibold">
                <tr>
                    <th class="p-3.5">Palet Barcode</th>
                    <th class="p-3.5">Jenis Palet</th>
                    <th class="p-3.5">Lokasi BIN & Gudang</th>
                    <th class="p-3.5">Status Palet</th>
                    <th class="p-3.5">Isi Box & Stok Terkecil</th>
                    <th class="p-3.5 text-center">Aksi / Kontrol</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($pallets as $plt)
                    @php
                        $activeBoxes = $plt->boxes->where('status', 'active');
                        $hasBoxes = $activeBoxes->count() > 0;
                    @endphp
                    <tr class="hover:bg-slate-800/50 transition">
                        <!-- Barcode ID -->
                        <td class="p-3.5 font-mono font-bold text-indigo-400 text-sm">
                            <i class="fa-solid fa-pallet text-xs mr-1 text-slate-400"></i>
                            {{ $plt->id }}
                        </td>

                        <!-- Jenis Palet -->
                        <td class="p-3.5">
                            <div class="font-semibold text-white">{{ $plt->type->name ?? $plt->pallet_type_id }}</div>
                            <div class="text-[10px] text-slate-400">ID: {{ $plt->pallet_type_id }}</div>
                        </td>

                        <!-- Lokasi BIN & Gudang -->
                        <td class="p-3.5">
                            @if($plt->bin_id)
                                <span class="px-2.5 py-1 rounded font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs">
                                    BIN: {{ $plt->bin_id }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    {{ $plt->bin->rack->warehouse->name ?? 'Gudang Utama' }}
                                </div>
                            @else
                                <span class="px-2.5 py-1 rounded font-mono font-bold bg-slate-800 text-slate-400 border border-slate-700 text-xs">
                                    TRANSIT / UNASSIGNED
                                </span>
                            @endif
                        </td>

                        <!-- Status Palet -->
                        <td class="p-3.5 font-semibold">
                            @if(!$plt->is_active)
                                <span class="px-2.5 py-1 rounded font-bold text-[10px] bg-slate-800 text-rose-400 border border-rose-500/30">
                                    NON-AKTIF / MAINTENANCE
                                </span>
                            @elseif($hasBoxes)
                                <span class="px-2.5 py-1 rounded font-bold text-[10px] bg-emerald-950 text-emerald-300 border border-emerald-500/40">
                                    TERISI STOK (AKTIF)
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded font-bold text-[10px] bg-amber-950 text-amber-300 border border-amber-500/40">
                                    KOSONG (READY)
                                </span>
                            @endif
                        </td>

                        <!-- Isi Box & Total Qty Base -->
                        <td class="p-3.5">
                            @if($hasBoxes)
                                <div class="font-bold text-white text-xs">
                                    {{ $activeBoxes->count() }} Box Terdaftar
                                </div>
                                <div class="text-[11px] text-emerald-400 font-semibold mt-0.5">
                                    @php
                                        $firstBox = $activeBoxes->first();
                                    @endphp
                                    {{ $firstBox->product->name ?? $firstBox->product_id }} 
                                    <span class="font-extrabold text-sky-400 font-mono">({{ $firstBox->qty }} {{ $firstBox->uom_base }})</span>
                                    @if($activeBoxes->count() > 1)
                                        <span class="text-[10px] text-slate-400">+{{ $activeBoxes->count() - 1 }} produk lain</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-500 text-xs font-mono">Belum ada box</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="p-3.5 text-center space-x-1">
                            <!-- Tambah Produk Ke Palet Ini -->
                            @canAccessModule('inbound', 'create')
                                <button type="button" 
                                        @click="openAddBoxModal('{{ $plt->id }}')" 
                                        class="p-2 bg-emerald-600/80 hover:bg-emerald-500 text-white rounded-lg transition cursor-pointer" 
                                        title="Tambah Box & Produk ke Palet ini">
                                    <i class="fa-solid fa-square-plus text-xs"></i>
                                </button>
                            @endcanAccessModule

                            <!-- Check Isi -->
                            <button type="button" 
                                    @click="inspectPallet('{{ $plt->id }}')" 
                                    class="p-2 bg-indigo-600/80 hover:bg-indigo-500 text-white rounded-lg transition cursor-pointer" 
                                    title="Check Isi Palet">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>

                            <!-- Pallet Tracking -->
                            <button type="button" 
                                    @click="trackPallet('{{ $plt->id }}')" 
                                    class="p-2 bg-purple-600/80 hover:bg-purple-500 text-white rounded-lg transition cursor-pointer" 
                                    title="Pallet Tracking & History">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            </button>

                            <!-- Print Label Barcode -->
                            <button type="button" 
                                    @click="printLabel('{{ $plt->id }}', '{{ $plt->bin_id ?? 'TRANSIT' }}', '{{ $plt->type->name ?? $plt->pallet_type_id }}')" 
                                    class="p-2 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg transition cursor-pointer" 
                                    title="Cetak Label Barcode Palet">
                                <i class="fa-solid fa-barcode text-xs"></i>
                            </button>

                            <!-- Edit -->
                            @canAccessModule('pallet', 'update')
                                <button type="button" 
                                        @click="editPallet('{{ $plt->id }}', '{{ $plt->pallet_type_id }}', '{{ $plt->bin_id }}', {{ $plt->is_active ? 1 : 0 }})" 
                                        class="p-2 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg transition cursor-pointer" 
                                        title="Edit / Relokasi Palet">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                            @endcanAccessModule

                            <!-- Delete -->
                            @canAccessModule('pallet', 'delete')
                                @if(!$hasBoxes)
                                    <form action="{{ route('pallets.destroy', $plt->id) }}" method="POST" onsubmit="PalletApp.deleteItem(event, this)" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Palet Kosong">
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
                    <div class="text-slate-400">Jenis Palet:</div>
                    <div class="font-bold text-white">{{ $plt->type->name ?? $plt->pallet_type_id }}</div>
                </div>
                <div>
                    @if(!$plt->is_active)
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-slate-800 text-rose-400 border border-rose-500/30">
                            MAINTENANCE
                        </span>
                    @elseif($hasBoxes)
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-emerald-950 text-emerald-300 border border-emerald-500/40">
                            TERISI STOK
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-amber-950 text-amber-300 border border-amber-500/40">
                            KOSONG
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
