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
            <!-- Filter Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <input type="hidden" name="tab" value="bins">
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Filter Rak</label>
                    <select id="select_rack_filter" name="rack_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Rak --</option>
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}" {{ request('rack_filter') == $r->id ? 'selected' : '' }}>Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Cari Keyword BIN</label>
                    <input type="text" name="search_bin" value="{{ request('search_bin') }}" placeholder="🔍 Cari ID BIN..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Filter</button>
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'bins']) }}', 'bins')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Barcode BIN</th>
                            <th class="p-3">Rak & Gudang</th>
                            <th class="p-3">Posisi (Kolom & Level)</th>
                            <th class="p-3">Kapasitas Palet</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
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
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-amber-400">{{ $bn->id }}</td>
                                <td class="p-3 text-slate-300">Rak {{ $bn->rack_id }} (Gudang: {{ $bn->rack->warehouse_id ?? '-' }})</td>
                                <td class="p-3 text-slate-400 font-mono">Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}</td>
                                <td class="p-3 font-bold">
                                    <span class="px-2 py-0.5 rounded text-[11px] {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                        {{ $activePalletsCount }} / {{ $bn->max_pallet }} Palet {{ $activePalletsCount >= $bn->max_pallet ? '(FULL)' : '' }}
                                    </span>
                                </td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" 
                                            data-bin="{{ json_encode($bnData) }}" 
                                            onclick="window.openDetailBinModal(this)" 
                                            class="p-1.5 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg transition cursor-pointer" 
                                            title="Show Detail BIN">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-bin="{{ json_encode($bnData) }}" 
                                                onclick="window.openEditBinModal(this)" 
                                                class="p-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg transition cursor-pointer" 
                                                title="Edit BIN">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.bin.destroy', $bn->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus BIN">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada data BIN.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Swipeable Interactive Card List -->
            <div class="space-y-3 md:hidden">
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
                    
                    <div x-data="swipeableCard()" 
                         @touchstart="touchStart($event)"
                         @touchmove="touchMove($event)"
                         @touchend="touchEnd($event)"
                         @mousedown="mouseDown($event)"
                         @mousemove="mouseMove($event)"
                         @mouseup="mouseUp($event)"
                         @mouseleave="mouseUp($event)"
                         class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm select-none bg-white dark:bg-slate-800/80">
                        
                        <!-- LEFT ACTION TRAY (Revealed on Swipe Right: DELETE) -->
                        <div class="absolute inset-y-0 left-0 w-24 bg-rose-600 flex items-stretch z-0">
                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.bin.destroy', $bn->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus BIN">
                                    <i class="fa-solid fa-trash-can text-lg mb-1"></i>
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase">Hapus</span>
                                </button>
                            </form>
                            @else
                            <div class="w-full flex flex-col items-center justify-center text-white/60 p-2">
                                <i class="fa-solid fa-lock text-base mb-1"></i>
                                <span class="text-[10px] font-bold">No Akses</span>
                            </div>
                            @endcanAccessModule
                        </div>

                        <!-- RIGHT ACTION TRAY (Revealed on Swipe Left: DETAIL & EDIT) -->
                        <div class="absolute inset-y-0 right-0 w-36 flex items-stretch z-0">
                            <!-- Show / Detail Button -->
                            <button type="button" 
                                    data-bin="{{ json_encode($bnData) }}" 
                                    onclick="window.openDetailBinModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white transition p-2 cursor-pointer"
                                    title="Detail BIN">
                                <i class="fa-solid fa-eye text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Detail</span>
                            </button>

                            <!-- Edit Button -->
                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-bin="{{ json_encode($bnData) }}" 
                                    onclick="window.openEditBinModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                                    title="Edit BIN">
                                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
                            </button>
                            @endcanAccessModule
                        </div>

                        <!-- MAIN SWIPEABLE CARD CONTENT LAYER -->
                        <div :style="'transform: translateX(' + offset + 'px)'" 
                             class="relative z-10 bg-white dark:bg-slate-800 p-4 transition-transform duration-150 ease-out flex items-center justify-between gap-3">
                            
                            <!-- Left: Main Info -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono font-extrabold text-amber-600 dark:text-amber-400 text-xs">{{ $bn->id }}</span>
                                </div>
                                <h4 class="font-bold text-slate-900 dark:text-white text-xs mt-0.5 leading-snug truncate">Rak {{ $bn->rack_id }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                    Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}
                                </p>
                            </div>

                            <!-- Right: Pallet Capacity Badge -->
                            <div class="shrink-0 text-right">
                                <span class="px-2.5 py-1 rounded-xl text-[11px] font-extrabold border shadow-sm inline-flex items-center {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-50 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30' : 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30' }}">
                                    <span>{{ $activePalletsCount }}/{{ $bn->max_pallet }} Palet</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data BIN.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $bins->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah BIN -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-slate-100 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-amber-500"></i>
                <span>Tambah BIN Storage Baru</span>
            </h4>
            <form action="{{ route('master.bin.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Lokasi Rak Induk <span class="text-rose-400">*</span></label>
                    <select id="select_rack_id" name="rack_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white">
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}">Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Kode Barcode BIN (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.bin_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="misal: BIN-1001">
                        <button type="button" @click="autoBinCode()" class="px-3 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Kolom No</label>
                        <input type="number" min="1" name="column_no" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Level No</label>
                        <input type="number" min="1" name="level_no" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Kapasitas Palet</label>
                    <input type="number" min="1" name="max_pallet" value="1" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold">
                </div>
                <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
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
