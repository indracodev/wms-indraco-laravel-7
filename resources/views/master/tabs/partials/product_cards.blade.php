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
    
    <div class="product-card-item bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]" data-sku="{{ $p->id }}">
        <!-- Card Header: SKU, Konversi & Status -->
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
            <div class="flex items-center space-x-2">
                <span class="font-mono font-bold text-[#004b87] dark:text-sky-400 text-sm tracking-wide">{{ $p->id }}</span>
                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-full font-bold text-xs whitespace-nowrap">
                    {{ $p->conversions->count() }} Konversi
                </span>
            </div>
            <span class="px-2.5 py-0.5 rounded-lg font-bold text-xs whitespace-nowrap {{ $p->is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30' }}">
                {{ $p->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Card Body: Full Product Name & Details -->
        <div>
            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">{{ $p->name }}</h4>
            <div class="grid grid-cols-2 gap-2 mt-2.5 text-slate-600 dark:text-slate-300 text-[13px] bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700/60">
                <div>Brand: <strong class="text-slate-900 dark:text-white">{{ $p->brand ?? '-' }}</strong></div>
                <div>Base UOM: <strong class="text-emerald-700 dark:text-emerald-400 font-mono">{{ $p->uom_inventory }}</strong></div>
                <div>Kemasan: <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $p->uom_group ?? '-' }}</strong></div>
                <div>Isi: <strong class="text-amber-700 dark:text-amber-300 font-bold">{{ $p->qty_per_uom_group ?? 1 }}</strong></div>
                <div class="col-span-2 text-slate-500 dark:text-slate-400 text-xs pt-1 border-t border-slate-200 dark:border-slate-700/40">
                    Group: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $p->item_group ?? '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Dropdown Accordion Trigger for Conversions -->
        <div>
            <button type="button" 
                    @click="toggleSku('{{ $p->id }}')" 
                    class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-900/80 dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white rounded-xl border border-slate-300 dark:border-slate-700 flex items-center justify-between transition cursor-pointer text-[13px]">
                <span class="font-bold">
                    <i class="fa-solid fa-arrows-split-up-and-left mr-1.5 text-indigo-600 dark:text-sky-400"></i>
                    <span>Rincian Konversi Satuan ({{ $p->conversions->count() }})</span>
                </span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                   :class="isSkuExpanded('{{ $p->id }}') ? 'rotate-180 text-indigo-600 dark:text-indigo-400' : ''"></i>
            </button>

            <!-- Collapsible Section -->
            <div x-show="isSkuExpanded('{{ $p->id }}')" 
                 x-cloak 
                 class="mt-2.5 p-3 bg-slate-50 dark:bg-slate-950/70 border border-slate-300 dark:border-slate-800 rounded-xl space-y-2">
                <div class="text-[13px] text-slate-700 dark:text-slate-300 font-bold mb-1">Daftar Konversi Satuan:</div>
                @forelse($p->conversions as $conv)
                    <div class="flex items-center justify-between p-2.5 bg-white dark:bg-slate-900/60 rounded-lg text-[13px] border border-slate-200 dark:border-slate-800/80 shadow-xs">
                        <div>
                            <span class="font-bold font-mono text-sky-800 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/40 px-2 py-0.5 rounded border border-sky-200 dark:border-sky-800">{{ $conv->uom_convert }}</span>
                            <span class="text-slate-400 mx-1.5">&rarr;</span>
                            <span class="text-emerald-800 dark:text-emerald-400 font-mono font-semibold">{{ $conv->uom_base }}</span>
                        </div>
                        <div class="font-mono font-extrabold text-amber-700 dark:text-amber-300">
                            1 : {{ number_format($conv->qty_convert) }}
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500 italic text-center py-2 text-[13px]">Satuan tunggal (1:1 base unit).</div>
                @endforelse
            </div>
        </div>

        <!-- Card Footer Action Toolbar -->
        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
            <button type="button" 
                    data-product="{{ json_encode($mobileProdData) }}" 
                    onclick="window.openDetailProductModal(this)" 
                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                    title="Detail Produk SKU">
                <i class="fa-solid fa-eye text-xs"></i>
                <span>Detail</span>
            </button>

            @canAccessModule('master', 'update')
            <button type="button" 
                    data-product="{{ json_encode($mobileProdData) }}" 
                    onclick="window.openEditProductModal(this)" 
                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                    title="Edit Produk SKU">
                <i class="fa-solid fa-pen-to-square text-xs"></i>
                <span>Edit</span>
            </button>
            @endcanAccessModule

            @canAccessModule('master', 'delete')
            <form action="{{ route('master.product.destroy', $p->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="Hapus Produk SKU">
                    <i class="fa-solid fa-trash text-xs"></i>
                    <span>Hapus</span>
                </button>
            </form>
            @endcanAccessModule
        </div>
    </div>
@empty
    @if(!request()->filled('lazy'))
        <div id="no-products-mobile-msg" class="p-6 text-center text-slate-500 text-[13px] bg-slate-100 dark:bg-slate-800/40 rounded-xl">Tidak ada data produk SKU.</div>
    @endif
@endforelse
