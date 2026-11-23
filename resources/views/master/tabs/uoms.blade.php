<!-- ========================================================================= -->
<!-- TAB 6: UOMS (MASTER UOM INVENTORY - BASE UNIT)                            -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar Master Satuan UOM Inventory (Base Unit)</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar (Gambar 2 Style) -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="w-full">
                <input type="hidden" name="tab" value="uoms">
                <div class="search-input-group">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'uoms']) }}', 'uoms')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_uom" value="{{ request('search_uom') }}" placeholder="Cari Kode Satuan / Nama / Deskripsi UOM..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari UOM">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Satuan (ID)</th>
                            <th class="p-3 whitespace-nowrap">Nama Satuan UOM</th>
                            <th class="p-3">Keterangan / Fungsi</th>
                            <th class="p-3 text-center whitespace-nowrap">Penggunaan SKU</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
                        @forelse($uoms as $u)
                            @php 
                                $inUseCount = $u->products_count ?? 0;
                                $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
                                $uomData = [
                                    'id' => $u->id,
                                    'name' => $u->name,
                                    'description' => $u->description ?? '',
                                    'products_count' => $inUseCount,
                                    'products' => $u->products ? $u->products->map(function($p) {
                                        return [
                                            'id' => $p->id,
                                            'name' => $p->name,
                                            'brand' => $p->brand,
                                            'item_group' => $p->item_group,
                                            'uom_group' => $p->uom_group,
                                            'is_active' => (bool)$p->is_active,
                                        ];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">{{ $u->id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white whitespace-nowrap">{{ $u->name }}</td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 leading-relaxed break-words">{{ $u->description ?? '-' }}</td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if($inUseCount > 0)
                                        <button type="button" 
                                                @click="openUomProductsModal('{{ $u->id }}', '{{ addslashes($u->name) }}', {{ $u->products->toJson() }})"
                                                class="px-2.5 py-1 rounded-lg text-[13px] font-bold bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 transition cursor-pointer inline-flex items-center justify-center gap-1.5 group whitespace-nowrap"
                                                title="Klik untuk lihat {{ $inUseCount }} produk SKU dengan satuan {{ $u->name }}">
                                            <i class="fa-solid fa-boxes-stacked text-xs group-hover:scale-110 transition-transform"></i>
                                            <span>{{ $inUseCount }} Produk</span>
                                        </button>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[13px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 whitespace-nowrap inline-flex items-center">
                                            0 Produk
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-uom="{{ json_encode($uomData) }}" 
                                            onclick="window.openDetailUomModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Show Detail Satuan UOM">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>

                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}')" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit Satuan UOM">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule

                                    @canAccessModule('master', 'delete')
                                        @if($isSuperAdmin || $inUseCount === 0)
                                            <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="{{ $inUseCount > 0 ? 'Hapus UOM (Otorisasi Super Admin - Terkait ' . $inUseCount . ' produk)' : 'Hapus UOM' }}">
                                                    <i class="fa-solid fa-trash text-[13px]"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[13px] text-slate-400 inline-flex items-center gap-1 font-medium cursor-not-allowed" title="Satuan terkunci karena masih dipakai di Katalog Produk">
                                                <i class="fa-solid fa-lock text-slate-400"></i> Terkunci
                                            </span>
                                        @endif
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500 text-[13px]">Tidak ada data satuan UOM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List (No Cutoff, Proper & Proportional) -->
            <div class="space-y-3.5 md:hidden">
                @forelse($uoms as $u)
                    @php 
                        $inUseCount = $u->products_count ?? 0;
                        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
                        $uomData = [
                            'id' => $u->id,
                            'name' => $u->name,
                            'description' => $u->description ?? '',
                            'products_count' => $inUseCount,
                            'products' => $u->products ? $u->products->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'name' => $p->name,
                                    'brand' => $p->brand,
                                    'item_group' => $p->item_group,
                                    'uom_group' => $p->uom_group,
                                    'is_active' => (bool)$p->is_active,
                                ];
                            })->values() : []
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <!-- Header: Badge ID & SKU Count Badge -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-[#004b87] dark:text-sky-400 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $u->id }}
                            </span>
                            <div>
                                @if($inUseCount > 0)
                                    <button type="button" 
                                             @click="openUomProductsModal('{{ $u->id }}', '{{ addslashes($u->name) }}', {{ $u->products->toJson() }})"
                                             class="px-2.5 py-1 rounded-xl text-[13px] font-bold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 transition cursor-pointer inline-flex items-center active:scale-95 shadow-xs whitespace-nowrap"
                                             title="Klik untuk lihat {{ $inUseCount }} produk SKU">
                                        <span>{{ $inUseCount }} Produk</span>
                                    </button>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[13px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 inline-flex items-center whitespace-nowrap">
                                        0 Produk
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- UOM Name & Description -->
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">{{ $u->name }}</h4>
                            <p class="text-[13px] text-slate-600 dark:text-slate-300 leading-relaxed break-words">
                                {{ $u->description ?: 'Tidak ada keterangan' }}
                            </p>
                        </div>

                        <!-- Card Footer Action Toolbar -->
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-uom="{{ json_encode($uomData) }}" 
                                    onclick="window.openDetailUomModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Detail Satuan UOM">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}')" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Edit Satuan UOM">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                                @if($isSuperAdmin || $inUseCount === 0)
                                    <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="{{ $inUseCount > 0 ? 'Hapus UOM (Otorisasi Super Admin)' : 'Hapus UOM' }}">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @else
                                    <div class="flex-1 py-2 px-3 bg-slate-100 dark:bg-slate-700/50 text-slate-400 font-medium rounded-xl text-[13px] flex items-center justify-center gap-1 cursor-not-allowed" title="Satuan terkunci karena masih dipakai di Katalog Produk">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                        <span>Terkunci</span>
                                    </div>
                                @endif
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data satuan UOM.</div>
                @endforelse
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
                    <div class="p-6 text-center text-slate-500 text-xs bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data satuan UOM.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $uoms->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah UOM -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-indigo-600 dark:text-indigo-400"></i>
                <span>Tambah UOM Baru</span>
            </h4>
            <form action="{{ route('master.uom.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Satuan (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.uom_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" placeholder="misal: PCS / BOX">
                        <button type="button" @click="autoUomCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Nama Satuan UOM <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="misal: Box / Pieces / Kilogram">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Keterangan / Fungsi</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="Catatan fungsi atau standar kemasan..."></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 text-[13px]">
                    <span class="btn-text">Simpan Satuan UOM</span>
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
            @click="openAddUomModal()" 
            class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold shadow-xl shadow-indigo-600/40 border-2 border-white dark:border-slate-800 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
            title="Tambah Satuan UOM Baru">
        <i class="fa-solid fa-plus text-lg"></i>
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
