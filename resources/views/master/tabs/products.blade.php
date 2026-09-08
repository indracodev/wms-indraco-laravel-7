<!-- ========================================================================= -->
<!-- TAB 6: PRODUCTS (PRODUK SKU)                                              -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-indigo-400"></i>
            <span>Master Katalog Produk SKU</span>
        </h3>
        @canAccessModule('master', 'create')
            <button type="button" @click="showAddProductModal = true; $nextTick(() => initMasterTomSelect('select_is_active'))" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center space-x-1.5 cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Produk SKU Baru</span>
            </button>
        @endcanAccessModule
    </div>

    <!-- Filter & Search Toolbar -->
    <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
        <input type="hidden" name="tab" value="products">
        <div>
            <label class="block text-slate-400 mb-1 font-medium">Cari Keyword Produk</label>
            <input type="text" name="search_product" value="{{ request('search_product') }}" placeholder="🔍 Cari SKU / Nama Produk..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-slate-400 mb-1 font-medium">Filter Brand</label>
            <select id="select_brand_filter" name="brand_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                <option value="">-- Semua Brand --</option>
                @foreach($brands as $br)
                    <option value="{{ $br }}" {{ request('brand_filter') == $br ? 'selected' : '' }}>{{ $br }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-slate-400 mb-1 font-medium">Filter Group</label>
            <select id="select_group_filter" name="group_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                <option value="">-- Semua Group --</option>
                @foreach($itemGroups as $ig)
                    <option value="{{ $ig }}" {{ request('group_filter') == $ig ? 'selected' : '' }}>{{ $ig }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end space-x-2">
            <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Filter</button>
            <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'products']) }}', 'products')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
        </div>
    </form>

    <!-- Flat Master Item Products Table -->
    <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-800/90 text-slate-300 uppercase tracking-wider font-semibold">
                <tr>
                    <th class="p-3">Kode Item SKU</th>
                    <th class="p-3">Nama Item</th>
                    <th class="p-3">Group Item</th>
                    <th class="p-3">UOM Inventory</th>
                    <th class="p-3">Grup UOM</th>
                    <th class="p-3">Isi/Grup</th>
                    <th class="p-3">Brand</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">UOM Convert</th>
                    <th class="p-3">Qty Convert</th>
                    <th class="p-3">UOM Base</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($flatProducts as $fp)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="p-3 font-mono font-bold text-indigo-400">{{ $fp->id }}</td>
                        <td class="p-3 font-bold text-white">{{ $fp->name }}</td>
                        <td class="p-3 text-slate-400 text-[11px]">{{ $fp->item_group ?? '-' }}</td>
                        <td class="p-3 font-bold text-emerald-400">{{ $fp->uom_inventory }}</td>
                        <td class="p-3 text-slate-300 font-mono">{{ $fp->uom_group ?? '-' }}</td>
                        <td class="p-3 font-bold text-amber-300">{{ $fp->qty_per_uom_group ?? '-' }}</td>
                        <td class="p-3 text-slate-300">{{ $fp->brand ?? '-' }}</td>
                        <td class="p-3">
                            @if($fp->is_active)
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 rounded font-bold text-[10px]">Yes</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-500/20 text-rose-300 rounded font-bold text-[10px]">No</span>
                            @endif
                        </td>
                        <td class="p-3 font-bold text-sky-300 font-mono">{{ $fp->uom_convert }}</td>
                        <td class="p-3 font-mono font-bold text-indigo-300">{{ number_format($fp->qty_convert) }}</td>
                        <td class="p-3 font-mono text-slate-400">{{ $fp->uom_base }}</td>
                        <td class="p-3 text-center space-x-1">
                            @canAccessModule('master', 'delete')
                                <form action="{{ route('master.product.destroy', $fp->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Produk SKU">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcanAccessModule
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="p-6 text-center text-slate-500">Tidak ada data produk SKU.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Flat Card List -->
    <div class="space-y-3 md:hidden">
        @forelse($products as $p)
            <div class="bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl space-y-2 text-xs">
                <div class="flex items-center justify-between border-b border-slate-700/60 pb-2">
                    <div class="font-mono font-bold text-indigo-400 text-sm">{{ $p->id }}</div>
                    <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                        {{ $p->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="font-bold text-white text-sm">{{ $p->name }}</div>
                <div class="flex items-center justify-between text-slate-400">
                    <span>Brand: <strong class="text-white">{{ $p->brand ?? '-' }}</strong></span>
                    <span>Base UOM: <strong class="text-emerald-400 font-mono">{{ $p->uom_inventory }}</strong></span>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-slate-500 text-xs bg-slate-800/40 rounded-xl">Tidak ada data produk SKU.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
