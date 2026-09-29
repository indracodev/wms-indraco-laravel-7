<!-- ========================================================================= -->
<!-- TAB 6: UOMS (MASTER UOM INVENTORY - BASE UNIT)                            -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-scale-balanced text-indigo-600 dark:text-indigo-400"></i>
            <span>Daftar Master Satuan UOM Inventory (Base Unit)</span>
        </h3>
        @canAccessModule('master', 'create')
        <button type="button" 
                @click="openAddUomModal()" 
                class="md:hidden px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5 active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah</span>
        </button>
        @endcanAccessModule
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex items-center space-x-2 text-xs">
                <input type="hidden" name="tab" value="uoms">
                <input type="text" name="search_uom" value="{{ request('search_uom') }}" placeholder="🔍 Cari Kode Satuan / Nama / Deskripsi UOM..." class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer shrink-0">Cari</button>
                <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'uoms']) }}', 'uoms')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer shrink-0">Reset</button>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3">Kode Satuan (ID)</th>
                            <th class="p-3">Nama Satuan UOM</th>
                            <th class="p-3">Keterangan / Fungsi</th>
                            <th class="p-3 text-center">Penggunaan SKU</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40">
                        @forelse($uoms as $u)
                            @php 
                                $inUseCount = $u->products_count ?? 0;
                                $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-indigo-600 dark:text-indigo-400">{{ $u->id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $u->name }}</td>
                                <td class="p-3 text-slate-500 dark:text-slate-400">{{ $u->description ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    @if($inUseCount > 0)
                                        <button type="button" 
                                                @click="openUomProductsModal('{{ $u->id }}', '{{ addslashes($u->name) }}', {{ $u->products->toJson() }})"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30 transition cursor-pointer inline-flex items-center justify-center gap-1.5 group"
                                                title="Klik untuk lihat {{ $inUseCount }} produk SKU dengan satuan {{ $u->name }}">
                                            <i class="fa-solid fa-boxes-stacked text-[10px] group-hover:scale-110 transition-transform"></i>
                                            <span>{{ $inUseCount }} Produk</span>
                                        </button>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                            0 Produk
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}')" 
                                                class="p-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                                title="Edit Satuan UOM">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule

                                    @canAccessModule('master', 'delete')
                                        @if($isSuperAdmin || $inUseCount === 0)
                                            <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition shadow-sm cursor-pointer" title="{{ $inUseCount > 0 ? 'Hapus UOM (Otorisasi Super Admin - Terkait ' . $inUseCount . ' produk)' : 'Hapus UOM' }}">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-400 inline-flex items-center gap-1 font-medium cursor-not-allowed" title="Satuan terkunci karena masih dipakai di Katalog Produk">
                                                <i class="fa-solid fa-lock text-slate-400"></i> Terkunci
                                            </span>
                                        @endif
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada data satuan UOM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Swipeable Interactive Card List -->
            <div class="space-y-3 md:hidden">
                @forelse($uoms as $u)
                    @php 
                        $inUseCount = $u->products_count ?? 0;
                        $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
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
                            @if($isSuperAdmin || $inUseCount === 0)
                            <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus UOM">
                                    <i class="fa-solid fa-trash-can text-lg mb-1"></i>
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase">Hapus</span>
                                </button>
                            </form>
                            @else
                            <div class="w-full flex flex-col items-center justify-center text-white/60 p-2">
                                <i class="fa-solid fa-lock text-base mb-1"></i>
                                <span class="text-[10px] font-bold">Terkunci</span>
                            </div>
                            @endif
                            @else
                            <div class="w-full flex flex-col items-center justify-center text-white/60 p-2">
                                <i class="fa-solid fa-lock text-base mb-1"></i>
                                <span class="text-[10px] font-bold">No Akses</span>
                            </div>
                            @endcanAccessModule
                        </div>

                        <!-- RIGHT ACTION TRAY (Revealed on Swipe Left: EDIT) -->
                        <div class="absolute inset-y-0 right-0 w-24 flex items-stretch z-0">
                            <!-- Edit Button -->
                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}'); closeTray();" 
                                    class="w-full flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                                    title="Edit Satuan UOM">
                                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
                            </button>
                            @endcanAccessModule
                        </div>

                        <!-- MAIN SWIPEABLE CARD CONTENT LAYER -->
                        <div :style="'transform: translateX(' + offset + 'px)'" 
                             class="relative z-10 bg-white dark:bg-slate-800 p-4 transition-transform duration-150 ease-out flex items-center justify-between gap-3">
                            
                            <!-- Left: Icon Avatar + Main Info -->
                            <div class="flex items-start space-x-3 min-w-0 flex-1">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base border border-indigo-100 dark:border-indigo-800/40 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-scale-balanced"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-xs">{{ $u->id }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mt-0.5 leading-snug truncate">{{ $u->name }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1">
                                        {{ $u->description ?? 'Tidak ada keterangan' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Clickable Product Count Badge -->
                            <div class="shrink-0 text-right">
                                @if($inUseCount > 0)
                                    <button type="button" 
                                            @click="openUomProductsModal('{{ $u->id }}', '{{ addslashes($u->name) }}', {{ $u->products->toJson() }})"
                                            class="px-2.5 py-1.5 rounded-xl text-[11px] font-extrabold bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 transition cursor-pointer inline-flex items-center gap-1 active:scale-95 shadow-sm"
                                            title="Klik untuk lihat {{ $inUseCount }} produk SKU">
                                        <i class="fa-solid fa-boxes-stacked text-[10px]"></i>
                                        <span>{{ $inUseCount }} Produk</span>
                                    </button>
                                @else
                                    <span class="px-2.5 py-1 rounded-xl text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 inline-flex items-center gap-1">
                                        0 Produk
                                    </span>
                                @endif
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
        <div class="hidden lg:block bg-slate-100 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-500"></i>
                <span>Tambah UOM Baru</span>
            </h4>
            <form action="{{ route('master.uom.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Kode Satuan (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.uom_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: PCS / BOX">
                        <button type="button" @click="autoUomCode()" class="px-3 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Nama Satuan UOM <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: Box / Pieces / Kilogram">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Keterangan / Fungsi</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Catatan fungsi atau standar kemasan..."></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Satuan UOM</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>

    <!-- FLOATING ACTION BUTTON (FAB) FOR MOBILE -->
    @canAccessModule('master', 'create')
    <div class="fixed bottom-20 right-4 z-40 md:hidden">
        <button type="button" 
                @click="openAddUomModal()" 
                class="w-13 h-13 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold shadow-xl shadow-indigo-600/40 border border-indigo-400/40 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
                title="Tambah Satuan UOM Baru">
            <i class="fa-solid fa-plus text-xl"></i>
        </button>
    </div>
    @endcanAccessModule
</div>
