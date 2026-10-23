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
    
    <div x-data="swipeableCard()" 
         data-sku="{{ $p->id }}"
         @touchstart="touchStart($event)"
         @touchmove="touchMove($event)"
         @touchend="touchEnd($event)"
         @mousedown="mouseDown($event)"
         @mousemove="mouseMove($event)"
         @mouseup="mouseUp($event)"
         @mouseleave="mouseUp($event)"
         class="product-card-item relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm select-none bg-white dark:bg-slate-800/80">
        
        <!-- LEFT ACTION TRAY (Revealed on Swipe Right: DELETE) -->
        <div class="absolute inset-y-0 left-0 w-24 bg-rose-600 flex items-stretch z-0">
            @canAccessModule('master', 'delete')
            <form action="{{ route('master.product.destroy', $p->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus Produk SKU">
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
                    data-product="{{ json_encode($mobileProdData) }}" 
                    onclick="window.openDetailProductModal(this)" 
                    @click="closeTray()"
                    class="flex-1 flex flex-col items-center justify-center bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white transition p-2 cursor-pointer"
                    title="Detail Produk SKU">
                <i class="fa-solid fa-eye text-base mb-1"></i>
                <span class="text-[10px] font-extrabold tracking-wider uppercase">Detail</span>
            </button>

            <!-- Edit Button -->
            @canAccessModule('master', 'update')
            <button type="button" 
                    data-product="{{ json_encode($mobileProdData) }}" 
                    onclick="window.openEditProductModal(this)" 
                    @click="closeTray()"
                    class="flex-1 flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                    title="Edit Produk SKU">
                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
            </button>
            @endcanAccessModule
        </div>

        <!-- FOREGROUND CARD (Slides with swipe gesture) -->
        <div :style="'transform: translateX(' + offset + 'px)'" 
             :class="{ 'duration-200': !isDragging }"
             class="relative z-10 bg-white dark:bg-slate-800 p-4 transition-transform ease-out cursor-grab active:cursor-grabbing space-y-3 text-xs">
            
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
                    <span class="font-bold text-[11px]">
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
    @if(!request()->filled('lazy'))
        <div id="no-products-mobile-msg" class="p-6 text-center text-slate-500 text-xs bg-slate-100 dark:bg-slate-800/40 rounded-xl">Tidak ada data produk SKU.</div>
    @endif
@endforelse
