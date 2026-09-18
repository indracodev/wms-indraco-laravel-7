<!-- ========================================================================= -->
<!-- TAB 7: PRODUCTS (PRODUK SKU) - GROUPED ACCORDION VIEW                     -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-indigo-600 dark:text-indigo-400"></i>
                <span>Master Katalog Produk SKU</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tampilan data dikelompokkan berdasarkan Kode SKU dengan rincian konversi & kemasan barang.</p>
        </div>
        @canAccessModule('master', 'create')
            <button type="button" 
                    @click="openAddProductModal()" 
                    onclick="window.openAddProductModal()"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center space-x-1.5 cursor-pointer self-start sm:self-auto">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Produk SKU Baru</span>
            </button>
        @endcanAccessModule
    </div>

    <!-- Filter & Search Toolbar -->
    <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
        <input type="hidden" name="tab" value="products">
        <div>
            <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Cari Keyword Produk</label>
            <input type="text" name="search_product" value="{{ request('search_product') }}" placeholder="🔍 Cari SKU / Nama Produk..." class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
        </div>
        <div>
            <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Filter Brand</label>
            <select id="select_brand_filter" name="brand_filter" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                <option value="">-- Semua Brand --</option>
                @foreach($brands as $br)
                    <option value="{{ $br }}" {{ request('brand_filter') == $br ? 'selected' : '' }}>{{ $br }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Filter Group</label>
            <select id="select_group_filter" name="group_filter" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                <option value="">-- Semua Group --</option>
                @foreach($itemGroups as $ig)
                    <option value="{{ $ig }}" {{ request('group_filter') == $ig ? 'selected' : '' }}>{{ $ig }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">Filter</button>
            <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'products']) }}', 'products')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer">Reset</button>
        </div>
    </form>

    <!-- Toolbar Action Bar: Total SKU & Expand/Collapse Control -->
    @php
        $currentPageSkuList = $products->pluck('id')->values()->all();
    @endphp
    <div class="flex flex-wrap items-center justify-between gap-3 text-xs bg-slate-100 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-300 dark:border-slate-800 shadow-sm">
        <div class="flex items-center space-x-2 text-slate-700 dark:text-slate-300 font-medium">
            <i class="fa-solid fa-layer-group text-indigo-600 dark:text-indigo-400"></i>
            <span>Total: <strong class="text-slate-900 dark:text-white font-bold">{{ $products->total() }}</strong> SKU Produk Terdaftar</span>
            <span class="text-slate-400 dark:text-slate-500">|</span>
            <span class="text-slate-500 dark:text-slate-400">Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" 
                    @click="expandAllSkus({{ json_encode($currentPageSkuList) }})" 
                    class="px-3 py-1.5 bg-white hover:bg-sky-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-sky-700 dark:text-sky-300 hover:text-sky-800 dark:hover:text-white rounded-lg border border-slate-300 dark:border-slate-700 shadow-sm transition flex items-center space-x-1.5 font-semibold cursor-pointer">
                <i class="fa-solid fa-angles-down text-xs text-sky-600 dark:text-sky-400"></i>
                <span>Buka Semua Dropdown</span>
            </button>
            <button type="button" 
                    @click="collapseAllSkus()" 
                    class="px-3 py-1.5 bg-white hover:bg-amber-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-amber-700 dark:text-amber-300 hover:text-amber-800 dark:hover:text-white rounded-lg border border-slate-300 dark:border-slate-700 shadow-sm transition flex items-center space-x-1.5 font-semibold cursor-pointer">
                <i class="fa-solid fa-angles-up text-xs text-amber-600 dark:text-amber-400"></i>
                <span>Tutup Semua</span>
            </button>
        </div>
    </div>

    <!-- Grouped Master SKU Table with Accordion Dropdown -->
    <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-transparent shadow-sm">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase tracking-wider font-bold border-b border-slate-300 dark:border-slate-700">
                <tr>
                    <th class="p-3 w-12 text-center">#</th>
                    <th class="p-3">Kode Item SKU</th>
                    <th class="p-3">Nama Item Produk</th>
                    <th class="p-3">Group Item</th>
                    <th class="p-3">UOM Inventory</th>
                    <th class="p-3">Grup UOM</th>
                    <th class="p-3">Isi/Grup</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($products as $p)
                    @php
                        $prodData = [
                            'id' => $p->id,
                            'name' => $p->name,
                            'item_group' => $p->item_group,
                            'uom_inventory' => $p->uom_inventory,
                            'uom_group' => $p->uom_group,
                            'qty_per_uom_group' => $p->qty_per_uom_group,
                            'brand' => $p->brand,
                            'is_active' => (bool)$p->is_active,
                            'created_at' => $p->created_at ? $p->created_at->format('d M Y H:i') : '-',
                            'conversions' => $p->conversions ? $p->conversions->map(function($c) {
                                return [
                                    'uom_convert' => $c->uom_convert,
                                    'qty_convert' => $c->qty_convert,
                                    'uom_base' => $c->uom_base,
                                ];
                            })->values() : []
                        ];
                        $conversionsCount = $p->conversions->count();
                    @endphp

                    <!-- Main SKU Row -->
                    <tr class="transition cursor-pointer select-none border-b border-slate-200 dark:border-slate-800"
                        :class="isSkuExpanded('{{ $p->id }}') ? 'bg-indigo-50/90 dark:bg-indigo-950/40 border-l-4 border-l-indigo-600 dark:border-l-indigo-500' : 'bg-white dark:bg-transparent hover:bg-slate-50 dark:hover:bg-slate-800/40'"
                        @click="toggleSku('{{ $p->id }}')">
                        <td class="p-3 text-center">
                            <button type="button" 
                                    @click.stop="toggleSku('{{ $p->id }}')" 
                                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-600 hover:text-white text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shadow-sm transition cursor-pointer"
                                    :title="isSkuExpanded('{{ $p->id }}') ? 'Tutup Rincian SKU' : 'Buka Rincian SKU'">
                                <i class="fa-solid fa-chevron-right text-xs transition-transform duration-200"
                                   :class="isSkuExpanded('{{ $p->id }}') ? 'rotate-90 text-indigo-600 dark:text-indigo-400' : ''"></i>
                            </button>
                        </td>
                        <td class="p-3">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-bold text-indigo-700 dark:text-indigo-400 text-sm tracking-wide">{{ $p->id }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition"
                                      :class="isSkuExpanded('{{ $p->id }}') ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700'">
                                    {{ $conversionsCount }} Unit
                                </span>
                            </div>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $p->name }}</div>
                        </td>
                        <td class="p-3 text-slate-600 dark:text-slate-400 text-[11px] font-medium">{{ $p->item_group ?? '-' }}</td>
                        <td class="p-3">
                            <span class="px-2.5 py-1 rounded-lg font-mono font-bold text-emerald-800 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-300 dark:border-emerald-500/20 text-xs shadow-sm">
                                {{ $p->uom_inventory }}
                            </span>
                        </td>
                        <td class="p-3 font-mono font-semibold text-slate-700 dark:text-slate-300">{{ $p->uom_group ?? '-' }}</td>
                        <td class="p-3 font-bold text-amber-700 dark:text-amber-400 text-xs">{{ $p->qty_per_uom_group ?? '-' }}</td>
                        <td class="p-3 text-slate-700 dark:text-slate-300 font-medium">{{ $p->brand ?? '-' }}</td>
                        <td class="p-3">
                            @if($p->is_active)
                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30 rounded font-bold text-[10px]">Active</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30 rounded font-bold text-[10px]">Inactive</span>
                            @endif
                        </td>
                        <td class="p-3 text-center space-x-1 whitespace-nowrap" @click.stop>
                            <button type="button" 
                                    data-product="{{ json_encode($prodData) }}" 
                                    onclick="window.openDetailProductModal(this)" 
                                    class="p-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                    title="Show Detail Produk SKU">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </button>

                            @canAccessModule('master', 'update')
                                <button type="button" 
                                        data-product="{{ json_encode($prodData) }}" 
                                        onclick="window.openEditProductModal(this)" 
                                        class="p-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                        title="Edit Produk SKU">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                                <form action="{{ route('master.product.destroy', $p->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition shadow-sm cursor-pointer" title="Hapus Produk SKU">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcanAccessModule
                        </td>
                    </tr>

                    <!-- Expandable Dropdown / Accordion Row: Detail Barang & Konversi Satuan -->
                    <tr x-show="isSkuExpanded('{{ $p->id }}')" 
                        x-cloak 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="bg-indigo-50/40 dark:bg-slate-950/80 border-b-2 border-indigo-200 dark:border-indigo-500/30">
                        <td colspan="10" class="p-4 pl-12 bg-slate-50 dark:bg-slate-900/90 border-l-4 border-l-indigo-600 dark:border-l-indigo-500">
                            <div class="space-y-3">
                                <!-- Dropdown Sub-Header -->
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-300 dark:border-slate-800 pb-3">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 flex items-center justify-center text-xs shadow-sm font-bold border border-indigo-200 dark:border-transparent">
                                            <i class="fa-solid fa-arrows-split-up-and-left"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 dark:text-white text-xs">
                                                Daftar Konversi Satuan & Kemasan untuk SKU: 
                                                <span class="font-mono text-indigo-700 dark:text-indigo-300 font-extrabold">{{ $p->id }}</span>
                                            </h4>
                                            <div class="text-[11px] text-slate-600 dark:text-slate-400 font-medium">
                                                {{ $p->name }} &bull; Brand: <strong class="text-slate-900 dark:text-white">{{ $p->brand ?? '-' }}</strong> &bull; Group: <strong class="text-slate-900 dark:text-white">{{ $p->item_group ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs">
                                        <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-700 text-emerald-800 dark:text-emerald-300 rounded-lg font-medium">Satuan Dasar (Base): <strong class="font-mono font-bold">{{ $p->uom_inventory }}</strong></span>
                                        <span class="px-2.5 py-1 bg-sky-50 dark:bg-sky-950/50 border border-sky-300 dark:border-sky-700 text-sky-800 dark:text-sky-300 rounded-lg font-medium">Grup Kemasan: <strong class="font-mono font-bold">{{ $p->uom_group ?? '-' }}</strong></span>
                                        <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-700 text-amber-900 dark:text-amber-300 rounded-lg font-medium">Isi per Kemasan: <strong class="font-bold">{{ $p->qty_per_uom_group ?? 1 }}</strong></span>
                                    </div>
                                </div>

                                <!-- Conversions Nested Table -->
                                <div class="border border-slate-300 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-950/60 shadow-sm">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 uppercase text-[10px] tracking-wider font-bold border-b border-slate-300 dark:border-slate-800">
                                            <tr>
                                                <th class="p-2.5 w-12 text-center">No</th>
                                                <th class="p-2.5">Satuan Konversi (UOM Convert)</th>
                                                <th class="p-2.5">Qty Konversi</th>
                                                <th class="p-2.5">Satuan Dasar (UOM Base)</th>
                                                <th class="p-2.5">Formula Ekivalensi Satuan</th>
                                                <th class="p-2.5 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800/80">
                                            @forelse($p->conversions as $idx => $conv)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/60 transition">
                                                    <td class="p-2.5 text-center text-slate-500 font-bold font-mono">{{ $idx + 1 }}</td>
                                                    <td class="p-2.5 font-mono font-bold">
                                                        <span class="px-2.5 py-1 bg-sky-100 dark:bg-sky-500/10 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/20 rounded-md font-mono font-bold text-xs shadow-sm">
                                                            {{ $conv->uom_convert }}
                                                        </span>
                                                    </td>
                                                    <td class="p-2.5 font-mono font-extrabold text-indigo-700 dark:text-indigo-300 text-sm">
                                                        {{ number_format($conv->qty_convert) }}
                                                    </td>
                                                    <td class="p-2.5 font-mono font-bold">
                                                        <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/20 rounded font-mono text-xs">
                                                            {{ $conv->uom_base }}
                                                        </span>
                                                    </td>
                                                    <td class="p-2.5 font-mono text-[11px]">
                                                        <span class="text-slate-900 dark:text-white font-bold">1 {{ $conv->uom_convert }}</span>
                                                        <span class="text-slate-400 mx-1">=</span>
                                                        <span class="text-amber-700 dark:text-amber-300 font-extrabold">{{ number_format($conv->qty_convert) }} {{ $conv->uom_base }}</span>
                                                    </td>
                                                    <td class="p-2.5 text-center">
                                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/20 shadow-sm">
                                                            Valid Ekivalen
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="p-3 text-center text-slate-500 italic">
                                                        Hanya memiliki satuan dasar standar (1 : 1 {{ $p->uom_inventory }}).
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="p-8 text-center text-slate-500">
                            <i class="fa-solid fa-boxes-stacked text-3xl mb-2 block text-slate-400"></i>
                            <span>Tidak ada data produk SKU yang sesuai dengan pencarian atau filter.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Grouped Card List with Accordion -->
    <div class="space-y-3 md:hidden">
        @forelse($products as $p)
            @php
                $mobileProdData = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'item_group' => $p->item_group,
                    'uom_inventory' => $p->uom_inventory,
                    'uom_group' => $p->uom_group,
                    'qty_per_uom_group' => $p->qty_per_uom_group,
                    'brand' => $p->brand,
                    'is_active' => (bool)$p->is_active,
                    'created_at' => $p->created_at ? $p->created_at->format('d M Y H:i') : '-',
                    'conversions' => $p->conversions ? $p->conversions->map(function($c) {
                        return [
                            'uom_convert' => $c->uom_convert,
                            'qty_convert' => $c->qty_convert,
                            'uom_base' => $c->uom_base,
                        ];
                    })->values() : []
                ];
            @endphp
            <div class="bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/70 p-4 rounded-xl space-y-3 text-xs shadow-sm">
                <!-- Card Header -->
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 pb-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-mono font-bold text-indigo-700 dark:text-indigo-400 text-sm">{{ $p->id }}</span>
                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-full font-bold text-[10px]">
                            {{ $p->conversions->count() }} Konversi
                        </span>
                    </div>
                    <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30' }}">
                        {{ $p->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Card Body -->
                <div>
                    <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $p->name }}</div>
                    <div class="grid grid-cols-2 gap-2 mt-2 text-slate-600 dark:text-slate-400">
                        <div>Brand: <strong class="text-slate-900 dark:text-white">{{ $p->brand ?? '-' }}</strong></div>
                        <div>Base UOM: <strong class="text-emerald-700 dark:text-emerald-400 font-mono">{{ $p->uom_inventory }}</strong></div>
                        <div>Kemasan: <strong class="text-slate-800 dark:text-slate-300 font-mono">{{ $p->uom_group ?? '-' }}</strong></div>
                        <div>Isi: <strong class="text-amber-700 dark:text-amber-300 font-bold">{{ $p->qty_per_uom_group ?? 1 }}</strong></div>
                    </div>
                </div>

                <!-- Dropdown / Accordion Trigger for Mobile -->
                <div>
                    <button type="button" 
                            @click="toggleSku('{{ $p->id }}')" 
                            class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900/80 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-lg border border-slate-300 dark:border-slate-700 flex items-center justify-between transition cursor-pointer">
                        <span class="flex items-center space-x-1.5 font-bold text-[11px]">
                            <i class="fa-solid fa-arrows-split-up-and-left text-indigo-600 dark:text-sky-400"></i>
                            <span>Rincian Konversi Satuan</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                           :class="isSkuExpanded('{{ $p->id }}') ? 'rotate-180 text-indigo-600 dark:text-indigo-400' : ''"></i>
                    </button>

                    <!-- Collapsible Section -->
                    <div x-show="isSkuExpanded('{{ $p->id }}')" 
                         x-cloak 
                         class="mt-2.5 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-300 dark:border-slate-800 rounded-xl space-y-2">
                        <div class="text-[11px] text-slate-700 dark:text-slate-400 font-bold mb-1">Daftar Konversi Satuan:</div>
                        @forelse($p->conversions as $conv)
                            <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-900/60 rounded-lg text-[11px] border border-slate-200 dark:border-slate-800/80 shadow-sm">
                                <div>
                                    <span class="font-bold font-mono text-sky-800 dark:text-sky-300 bg-sky-50 dark:bg-transparent px-1.5 py-0.5 rounded border border-sky-200 dark:border-transparent">{{ $conv->uom_convert }}</span>
                                    <span class="text-slate-400 mx-1">&rarr;</span>
                                    <span class="text-emerald-800 dark:text-slate-400 font-mono font-semibold">{{ $conv->uom_base }}</span>
                                </div>
                                <div class="font-mono font-extrabold text-amber-700 dark:text-amber-300">
                                    1 : {{ number_format($conv->qty_convert) }}
                                </div>
                            </div>
                        @empty
                            <div class="text-slate-500 italic text-center py-1">Satuan tunggal (1:1 base unit).</div>
                        @endforelse
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-200 dark:border-slate-700/60">
                    <button type="button" 
                            data-product="{{ json_encode($mobileProdData) }}" 
                            onclick="window.openDetailProductModal(this)" 
                            class="px-2.5 py-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition shadow-sm cursor-pointer">
                        <i class="fa-solid fa-eye text-xs"></i>
                        <span>Detail</span>
                    </button>
                    @canAccessModule('master', 'update')
                        <button type="button" 
                                data-product="{{ json_encode($mobileProdData) }}" 
                                onclick="window.openEditProductModal(this)" 
                                chip-tooltip="Edit Produk SKU"
                                class="px-2.5 py-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition shadow-sm cursor-pointer">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span>Edit</span>
                        </button>
                    @endcanAccessModule
                    @canAccessModule('master', 'delete')
                        <form action="{{ route('master.product.destroy', $p->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition shadow-sm cursor-pointer" title="Hapus Produk SKU">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    @endcanAccessModule
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-slate-500 text-xs bg-slate-100 dark:bg-slate-800/40 rounded-xl">Tidak ada data produk SKU.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>

@canAccessModule('master', 'create')
    <!-- Ergonomic Thumb Floating Action Button (Mobile Only) -->
    <!-- Positioned outside .glass-panel so position: fixed binds directly to viewport -->
    <div class="fixed bottom-[114px] right-4 z-40 md:hidden">
        <button type="button" 
                @click="openAddProductModal()" 
                onclick="window.openAddProductModal()"
                class="w-13 h-13 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white shadow-2xl shadow-emerald-600/50 flex items-center justify-center text-2xl active:scale-90 transition-transform cursor-pointer border-2 border-white dark:border-slate-800"
                title="Tambah Produk SKU">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
@endcanAccessModule
