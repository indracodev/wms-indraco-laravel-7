<!-- ========================================================================= -->
<!-- TAB 4: RACKS (RAK - dilengkapi KODE GUDANG warehouse_id)                  -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar Rak Penyimpanan</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter & Search Bar (Identical to Detail Data & Pallets) -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex flex-col sm:flex-row items-center gap-2.5 text-[13px]">
                <input type="hidden" name="tab" value="racks">
                <div class="search-input-group flex-1 w-full sm:w-auto">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'racks']) }}', 'racks')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_rack" value="{{ request('search_rack') }}" placeholder="Cari Kode Rak / Kode Gudang..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari Rak">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <div class="w-full sm:w-56">
                    <select id="select_wh_filter" name="wh_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Semua Gudang --</option>
                        @foreach($allWarehouses as $w)
                            <option value="{{ $w->id }}" {{ request('wh_filter') == $w->id ? 'selected' : '' }}>Gudang: {{ $w->name }} ({{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Desktop Table View with KODE GUDANG Column -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Barcode Rak</th>
                            <th class="p-3 whitespace-nowrap">Kode Gudang</th>
                            <th class="p-3">Nama Gudang</th>
                            <th class="p-3 whitespace-nowrap">Nama Rak</th>
                            <th class="p-3 whitespace-nowrap text-center">Jumlah Slot BIN</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
                        @forelse($racks as $rk)
                            @php
                                $rkData = [
                                    'id' => $rk->id,
                                    'warehouse_id' => $rk->warehouse_id,
                                    'warehouse' => $rk->warehouse ? ['id' => $rk->warehouse->id, 'name' => $rk->warehouse->name] : null,
                                    'rack_name' => $rk->rack_name,
                                    'created_at' => $rk->created_at ? $rk->created_at->format('d M Y H:i') : '-',
                                    'bins' => $rk->bins ? $rk->bins->map(function($b) {
                                        return [
                                            'id' => $b->id,
                                            'column_no' => $b->column_no,
                                            'level_no' => $b->level_no,
                                            'max_pallet' => $b->max_pallet
                                        ];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">{{ $rk->id }}</td>
                                <td class="p-3 whitespace-nowrap">
                                    <span class="font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 px-2 py-0.5 rounded text-[13px]">
                                        {{ $rk->warehouse_id }}
                                    </span>
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium break-words">{{ $rk->warehouse->name ?? '-' }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white whitespace-nowrap">Rak {{ $rk->rack_name }}</td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-lg border border-emerald-200 dark:border-emerald-800 text-[13px]">
                                        {{ $rk->bins_count }} BIN
                                    </span>
                                </td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-rack="{{ json_encode($rkData) }}" 
                                            onclick="window.openDetailRackModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Show Detail Rak">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-rack="{{ json_encode($rkData) }}" 
                                                onclick="window.openEditRackModal(this)" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit Rak">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.rack.destroy', $rk->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="Hapus Rak">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-slate-500 text-[13px]">Tidak ada data rak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List (No Cutoff, Proper & Proportional) -->
            <div class="space-y-3.5 md:hidden">
                @forelse($racks as $rk)
                    @php
                        $rkData = [
                            'id' => $rk->id,
                            'warehouse_id' => $rk->warehouse_id,
                            'warehouse' => $rk->warehouse ? ['id' => $rk->warehouse->id, 'name' => $rk->warehouse->name] : null,
                            'rack_name' => $rk->rack_name,
                            'created_at' => $rk->created_at ? $rk->created_at->format('d M Y H:i') : '-',
                            'bins' => $rk->bins ? $rk->bins->map(function($b) {
                                return [
                                    'id' => $b->id,
                                    'column_no' => $b->column_no,
                                    'level_no' => $b->level_no,
                                    'max_pallet' => $b->max_pallet
                                ];
                            })->values() : []
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <!-- Header: Badge ID & BIN Count Badge -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-[#004b87] dark:text-sky-400 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $rk->id }}
                            </span>
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $rk->bins_count }} BIN
                            </span>
                        </div>

                        <!-- Rack Name Title -->
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">Rak {{ $rk->rack_name }}</h4>
                        </div>

                        <!-- Warehouse Location Info Box (Full, No Truncate) -->
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/60 rounded-xl space-y-1.5 text-[13px]">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">Gudang Induk:</span>
                                <span class="font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-300 dark:border-amber-700/60 text-[13px] whitespace-nowrap">
                                    {{ $rk->warehouse_id }}
                                </span>
                            </div>
                            <div class="font-bold text-slate-900 dark:text-white break-words">
                                {{ $rk->warehouse->name ?? '-' }}
                            </div>
                        </div>

                        <!-- Card Footer Action Toolbar -->
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-rack="{{ json_encode($rkData) }}" 
                                    onclick="window.openDetailRackModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Detail Rak">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-rack="{{ json_encode($rkData) }}" 
                                    onclick="window.openEditRackModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Edit Rak">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.rack.destroy', $rk->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="Hapus Rak">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-800">Tidak ada data rak.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $racks->links() }}
            </div>
        </div>

        <!-- Form Tambah Rak (Desktop View Only, Hidden on Mobile) -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-indigo-600 dark:text-indigo-400"></i>
                <span>Tambah Rak Baru</span>
            </h4>
            <form action="{{ route('master.rack.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Gudang Induk <span class="text-rose-500">*</span></label>
                    <select id="select_warehouse_id" name="warehouse_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        @foreach($allWarehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Kode: {{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Barcode Rak (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.rack_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" placeholder="misal: RK-D">
                        <button type="button" @click="autoRackCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="rack_name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="D">
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95 text-[13px]">
                    <span class="btn-text">Simpan Rak</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>

@canAccessModule('master', 'create')
    <!-- Ergonomic Thumb Floating Action Button (Mobile Only: Tambah Rak Baru) -->
    <!-- Positioned outside .glass-panel so position: fixed binds directly to viewport -->
    <div class="fixed bottom-[125px] right-4 z-40 lg:hidden">
        <button type="button" 
                @click="openAddRackModal()" 
                onclick="window.openAddRackModal()"
                class="w-12 h-12 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-2xl shadow-indigo-600/50 flex items-center justify-center text-xl active:scale-90 transition-transform cursor-pointer border-2 border-white dark:border-slate-800"
                title="Tambah Rak Baru">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
@endcanAccessModule

<script>
    if (typeof window.swipeableCard !== 'function') {
        window.swipeableCard = function() {
            return {
                startX: 0,
                startY: 0,
                offset: 0,
                isDragging: false,
                isScrolling: false,
                maxLeftSwipe: -144,
                maxRightSwipe: 96,
                touchStart(e) {
                    this.startX = e.touches[0].clientX;
                    this.startY = e.touches[0].clientY;
                    this.isDragging = true;
                    this.isScrolling = false;
                },
                touchMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.touches[0].clientX - this.startX;
                    const diffY = e.touches[0].clientY - this.startY;
                    if (!this.isScrolling && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 8) {
                        this.isScrolling = true;
                        this.isDragging = false;
                        return;
                    }
                    if (this.isScrolling) return;
                    if (Math.abs(diffX) > 10 && e.cancelable) {
                        e.preventDefault();
                    }
                    if (diffX > 0) {
                        this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                    } else {
                        this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                    }
                },
                touchEnd(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                mouseDown(e) {
                    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                    this.startX = e.clientX;
                    this.isDragging = true;
                },
                mouseMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.clientX - this.startX;
                    if (Math.abs(diffX) > 5) {
                        if (diffX > 0) {
                            this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                        } else {
                            this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                        }
                    }
                },
                mouseUp(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                closeTray() {
                    this.offset = 0;
                }
            };
        };
    }
</script>
