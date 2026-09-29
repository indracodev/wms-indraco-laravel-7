<!-- ========================================================================= -->
<!-- TAB 4: RACKS (RAK - dilengkapi KODE GUDANG warehouse_id)                  -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-bars-staggered text-indigo-600 dark:text-indigo-400"></i>
            <span>Daftar Rak Penyimpanan</span>
        </h3>
        @canAccessModule('master', 'create')
        <button type="button" 
                @click="openAddRackModal()" 
                class="md:hidden px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5 active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah</span>
        </button>
        @endcanAccessModule
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <input type="hidden" name="tab" value="racks">
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Filter Gudang</label>
                    <select id="select_wh_filter" name="wh_filter" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        <option value="">-- Semua Gudang --</option>
                        @foreach($allWarehouses as $w)
                            <option value="{{ $w->id }}" {{ request('wh_filter') == $w->id ? 'selected' : '' }}>Gudang: {{ $w->name }} ({{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Cari Keyword Rak</label>
                    <input type="text" name="search_rack" value="{{ request('search_rack') }}" placeholder="🔍 Cari Kode Rak / Kode Gudang..." class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">Filter</button>
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'racks']) }}', 'racks')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer">Reset</button>
                </div>
            </form>

            <!-- Desktop Table View with KODE GUDANG Column -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3">Kode Barcode Rak</th>
                            <th class="p-3">Kode Gudang</th>
                            <th class="p-3">Nama Gudang</th>
                            <th class="p-3">Nama Rak</th>
                            <th class="p-3">Jumlah Slot BIN</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40">
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
                                <td class="p-3 font-mono font-extrabold text-indigo-600 dark:text-indigo-400">{{ $rk->id }}</td>
                                <td class="p-3 font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded">{{ $rk->warehouse_id }}</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">{{ $rk->warehouse->name ?? '-' }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white">Rak {{ $rk->rack_name }}</td>
                                <td class="p-3 font-extrabold text-emerald-700 dark:text-emerald-400">{{ $rk->bins_count }} BIN</td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" 
                                            data-rack="{{ json_encode($rkData) }}" 
                                            onclick="window.openDetailRackModal(this)" 
                                            class="p-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                            title="Show Detail Rak">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-rack="{{ json_encode($rkData) }}" 
                                                onclick="window.openEditRackModal(this)" 
                                                class="p-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                                title="Edit Rak">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.rack.destroy', $rk->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition shadow-sm cursor-pointer" title="Hapus Rak">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-slate-500">Tidak ada data rak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Swipeable Interactive Card List -->
            <div class="space-y-3 md:hidden">
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
                    
                    <div x-data="swipeableCard()" 
                         @touchstart="touchStart($event)"
                         @touchmove="touchMove($event)"
                         @touchend="touchEnd($event)"
                         @mousedown="mouseDown($event)"
                         @mousemove="mouseMove($event)"
                         @mouseup="mouseUp($event)"
                         @mouseleave="mouseUp($event)"
                         class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm select-none">
                        
                        <!-- LEFT ACTION TRAY (Revealed on Swipe Right: DELETE) -->
                        <div class="absolute inset-y-0 left-0 w-24 bg-rose-600 flex items-stretch z-0">
                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.rack.destroy', $rk->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus Rak">
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
                                    data-rack="{{ json_encode($rkData) }}" 
                                    onclick="window.openDetailRackModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white transition p-2 cursor-pointer"
                                    title="Detail Rak">
                                <i class="fa-solid fa-eye text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Detail</span>
                            </button>

                            <!-- Edit Button -->
                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-rack="{{ json_encode($rkData) }}" 
                                    onclick="window.openEditRackModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                                    title="Edit Rak">
                                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
                            </button>
                            @endcanAccessModule
                        </div>

                        <!-- FOREGROUND CARD (Slides with swipe gesture) -->
                        <div class="relative z-10 bg-white dark:bg-slate-800/95 p-4 transition-transform ease-out cursor-grab active:cursor-grabbing space-y-2.5 text-xs"
                             :class="{ 'duration-200': !isDragging }"
                             :style="'transform: translateX(' + offset + 'px)'"
                             @click="if (offset !== 0) { closeTray(); $event.stopPropagation(); }">
                            
                            <!-- Header: Badge ID & BIN Count Badge -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-indigo-700 dark:text-indigo-400 font-mono font-extrabold rounded-lg text-xs flex items-center gap-1.5 shadow-xs">
                                    <i class="fa-solid fa-barcode text-[11px]"></i>
                                    <span>{{ $rk->id }}</span>
                                </span>
                                <span class="shrink-0 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-extrabold rounded-lg text-xs whitespace-nowrap shadow-xs flex items-center gap-1.5">
                                    <i class="fa-solid fa-qrcode text-[11px] text-emerald-600 dark:text-emerald-400"></i>
                                    <span>{{ $rk->bins_count }} BIN</span>
                                </span>
                            </div>

                            <!-- Rack Name Title -->
                            <div>
                                <h4 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight">Rak {{ $rk->rack_name }}</h4>
                            </div>

                            <!-- Warehouse Location Info Box -->
                            <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/60 rounded-xl space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-500 dark:text-slate-400 font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-warehouse text-indigo-600 dark:text-indigo-400"></i>
                                        <span>Gudang Induk:</span>
                                    </span>
                                    <span class="font-mono font-bold text-amber-800 dark:text-amber-300 bg-amber-100 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-300 dark:border-amber-700/60">
                                        {{ $rk->warehouse_id }}
                                    </span>
                                </div>
                                <div class="text-xs font-bold text-slate-900 dark:text-white pl-5 truncate">
                                    {{ $rk->warehouse->name ?? '-' }}
                                </div>
                            </div>

                            <!-- Gesture Hints (Swipe guide) -->
                            <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-700/60">
                                <span class="flex items-center gap-1 font-semibold text-rose-600/80 dark:text-rose-400/80">
                                    <i class="fa-solid fa-angles-right text-[9px]"></i> Geser kanan: Hapus
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-sky-600/80 dark:text-sky-400/80">
                                    Geser kiri: Detail & Edit <i class="fa-solid fa-angles-left text-[9px]"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-800">Tidak ada data rak.</div>
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
                        <button type="button" @click="autoRackCode()" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-600 transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Nama Rak <span class="text-rose-500">*</span></label>
                    <input type="text" name="rack_name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" placeholder="D">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95">
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
    <div class="fixed bottom-[114px] right-4 z-40 lg:hidden">
        <button type="button" 
                @click="openAddRackModal()" 
                onclick="window.openAddRackModal()"
                class="w-13 h-13 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-2xl shadow-indigo-600/50 flex items-center justify-center text-2xl active:scale-90 transition-transform cursor-pointer border-2 border-white dark:border-slate-800"
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
