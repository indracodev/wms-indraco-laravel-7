<!-- ========================================================================= -->
<!-- TAB 5: BINS (BIN SLOT)                                                    -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar BIN Storage Slot</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter & Search Bar (Identical to Detail Data & Pallets) -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex flex-col sm:flex-row items-center gap-2.5 text-[13px]">
                <input type="hidden" name="tab" value="bins">
                <div class="search-input-group flex-1 w-full sm:w-auto">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'bins']) }}', 'bins')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_bin" value="{{ request('search_bin') }}" placeholder="Cari ID BIN..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari BIN">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <div class="w-full sm:w-56">
                    <select id="select_rack_filter" name="rack_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Semua Rak --</option>
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}" {{ request('rack_filter') == $r->id ? 'selected' : '' }}>Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Barcode BIN</th>
                            <th class="p-3">Rak & Gudang</th>
                            <th class="p-3 whitespace-nowrap">Posisi (Kolom & Level)</th>
                            <th class="p-3 whitespace-nowrap text-center">Kapasitas Palet</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
                        @forelse($bins as $bn)
                            @php
                                $activePalletsCount = $bn->active_pallets_count ?? 0;
                                $bnData = [
                                    'id' => $bn->id,
                                    'rack_id' => $bn->rack_id,
                                    'rack' => $bn->rack ? [
                                        'id' => $bn->rack->id,
                                        'warehouse_id' => $bn->rack->warehouse_id,
                                        'warehouse' => $bn->rack->warehouse ? ['id' => $bn->rack->warehouse->id, 'name' => $bn->rack->warehouse->name] : null
                                    ] : null,
                                    'column_no' => $bn->column_no,
                                    'level_no' => $bn->level_no,
                                    'max_pallet' => $bn->max_pallet,
                                    'active_pallets_count' => $activePalletsCount,
                                    'created_at' => $bn->created_at ? $bn->created_at->format('d M Y H:i') : '-',
                                    'pallets' => $bn->pallets ? $bn->pallets->map(function($p) {
                                        return [
                                            'id' => $p->id,
                                            'pallet_type_id' => $p->pallet_type_id,
                                            'max_box' => $p->max_box
                                        ];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">{{ $bn->id }}</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium break-words">
                                    <span class="font-bold text-slate-900 dark:text-white">Rak {{ $bn->rack_id }}</span>
                                    <span class="text-slate-500 dark:text-slate-400 text-[13px]">({{ $bn->rack->warehouse->name ?? $bn->rack->warehouse_id ?? '-' }})</span>
                                </td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 font-mono whitespace-nowrap">Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}</td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[13px] font-bold border whitespace-nowrap {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/50' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50' }}">
                                        {{ $activePalletsCount }} / {{ $bn->max_pallet }} Palet {{ $activePalletsCount >= $bn->max_pallet ? '(FULL)' : '' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-bin="{{ json_encode($bnData) }}" 
                                            onclick="window.openDetailBinModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Show Detail BIN">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-bin="{{ json_encode($bnData) }}" 
                                                onclick="window.openEditBinModal(this)" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit BIN">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.bin.destroy', $bn->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="Hapus BIN">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500 text-[13px]">Tidak ada data BIN.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List (No Cutoff, Proper & Proportional) -->
            <div class="space-y-3.5 md:hidden">
                @forelse($bins as $bn)
                    @php
                        $activePalletsCount = $bn->active_pallets_count ?? 0;
                        $bnData = [
                            'id' => $bn->id,
                            'rack_id' => $bn->rack_id,
                            'rack' => $bn->rack ? [
                                'id' => $bn->rack->id,
                                'warehouse_id' => $bn->rack->warehouse_id,
                                'warehouse' => $bn->rack->warehouse ? ['id' => $bn->rack->warehouse->id, 'name' => $bn->rack->warehouse->name] : null
                            ] : null,
                            'column_no' => $bn->column_no,
                            'level_no' => $bn->level_no,
                            'max_pallet' => $bn->max_pallet,
                            'active_pallets_count' => $activePalletsCount,
                            'created_at' => $bn->created_at ? $bn->created_at->format('d M Y H:i') : '-',
                            'pallets' => $bn->pallets ? $bn->pallets->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'pallet_type_id' => $p->pallet_type_id,
                                    'max_box' => $p->max_box
                                ];
                            })->values() : []
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <!-- Header: Badge ID & Pallet Capacity Badge -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/70 border border-amber-200 dark:border-amber-800/70 text-amber-800 dark:text-amber-400 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $bn->id }}
                            </span>
                            <span class="px-2.5 py-1 rounded-xl text-[13px] font-bold border shadow-xs whitespace-nowrap {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-50 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30' : 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30' }}">
                                {{ $activePalletsCount }}/{{ $bn->max_pallet }} Palet {{ $activePalletsCount >= $bn->max_pallet ? '(FULL)' : '' }}
                            </span>
                        </div>

                        <!-- Rak & Position Info -->
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">
                                Rak {{ $bn->rack_id }} 
                                <span class="font-normal text-slate-500 dark:text-slate-400 text-[13px]">({{ $bn->rack->warehouse->name ?? $bn->rack->warehouse_id ?? '-' }})</span>
                            </h4>
                            <div class="text-[13px] text-slate-600 dark:text-slate-300 font-mono pt-0.5">
                                Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}
                            </div>
                        </div>

                        <!-- Card Footer Action Toolbar -->
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-bin="{{ json_encode($bnData) }}" 
                                    onclick="window.openDetailBinModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Detail BIN">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-bin="{{ json_encode($bnData) }}" 
                                    onclick="window.openEditBinModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Edit BIN">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.bin.destroy', $bn->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="Hapus BIN">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data BIN.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $bins->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah BIN -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-amber-500"></i>
                <span>Tambah BIN Storage Baru</span>
            </h4>
            <form action="{{ route('master.bin.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Lokasi Rak Induk <span class="text-rose-500">*</span></label>
                    <select id="select_rack_id" name="rack_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}">Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Barcode BIN (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.bin_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-amber-500 focus:outline-none shadow-sm" placeholder="misal: BIN-1001">
                        <button type="button" @click="autoBinCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Kolom No</label>
                        <input type="number" min="1" name="column_no" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold shadow-sm text-[13px]">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Level No</label>
                        <input type="number" min="1" name="level_no" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold shadow-sm text-[13px]">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Kapasitas Palet</label>
                    <input type="number" min="1" name="max_pallet" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold shadow-sm text-[13px]">
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 text-[13px]">
                    <span class="btn-text">Simpan BIN</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>

</div>

<!-- FLOATING ACTION BUTTON (FAB) FOR MOBILE -->
@canAccessModule('master', 'create')
<div class="fixed bottom-[125px] right-4 z-40 md:hidden">
    <button type="button" 
            @click="openAddBinModal()" 
            class="w-12 h-12 rounded-full bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-white font-bold shadow-xl shadow-amber-600/40 border-2 border-white dark:border-slate-800 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
            title="Tambah BIN Baru">
        <i class="fa-solid fa-plus text-lg"></i>
    </button>
</div>
@endcanAccessModule
