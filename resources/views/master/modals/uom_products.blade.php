<!-- ========================================================================= -->
<!-- MODAL POPUP: DAFTAR PRODUK DALAM SATUAN UOM                                -->
<!-- ========================================================================= -->
<div x-show="showUomProductsModal" 
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" 
     x-cloak>
    <div class="glass-panel w-full max-w-3xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-emerald-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] flex flex-col my-auto" 
         @click.away="showUomProductsModal = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 pb-3 flex-shrink-0">
            <div class="flex items-center space-x-3 text-emerald-600 dark:text-emerald-400">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-lg font-bold border border-emerald-200 dark:border-emerald-500/30 shadow-sm">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Daftar Produk SKU Satuan:</span>
                        <span class="font-mono text-indigo-600 dark:text-indigo-400 underline decoration-indigo-400/30" x-text="uomProductsTitle"></span>
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Menampilkan <strong class="text-emerald-600 dark:text-emerald-400" x-text="uomProductsList.length"></strong> SKU produk yang terdaftar menggunakan UOM ini
                    </p>
                </div>
            </div>
            <button type="button" @click="showUomProductsModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl p-1 cursor-pointer transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Filter / Quick Search inside Modal -->
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs flex-shrink-0 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="uomProductSearchKeyword" 
                       placeholder="🔍 Cari SKU / Nama Produk / Brand / Group..." 
                       class="w-full pl-8 pr-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none shadow-sm">
            </div>
            <button type="button" 
                    @click="filterProductsTabByUom(uomProductsTitle)" 
                    class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow transition flex items-center space-x-1.5 cursor-pointer">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Buka di Tab Katalog Produk SKU</span>
            </button>
        </div>

        <!-- Scrollable Table -->
        <div class="overflow-y-auto flex-1 border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-950/60 shadow-inner">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 uppercase text-[10px] font-bold border-b border-slate-200 dark:border-slate-800 sticky top-0 z-10">
                    <tr>
                        <th class="p-3 w-10 text-center">#</th>
                        <th class="p-3">Kode SKU</th>
                        <th class="p-3">Nama Item Produk</th>
                        <th class="p-3">Brand</th>
                        <th class="p-3">Group Item</th>
                        <th class="p-3">Kemasan (UOM Group)</th>
                        <th class="p-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    <template x-for="(p, idx) in filteredUomProducts" :key="p.id">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/60 transition">
                            <td class="p-3 text-center text-slate-400 font-bold font-mono" x-text="idx + 1"></td>
                            <td class="p-3">
                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs tracking-wide" x-text="p.id"></span>
                            </td>
                            <td class="p-3 font-bold text-slate-900 dark:text-white" x-text="p.name"></td>
                            <td class="p-3 text-slate-600 dark:text-slate-300 font-medium" x-text="p.brand || '-'"></td>
                            <td class="p-3 text-slate-500 dark:text-slate-400 text-[11px]" x-text="p.item_group || '-'"></td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700" x-text="p.uom_group || '-'"></span>
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold" 
                                      :class="p.is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30'"
                                      x-text="p.is_active ? 'Active' : 'Inactive'">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredUomProducts.length === 0">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">
                                <i class="fa-solid fa-boxes-stacked text-2xl mb-2 text-slate-400 block"></i>
                                <span>Tidak ditemukan produk SKU yang sesuai pencarian.</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-3 flex-shrink-0 text-slate-500 dark:text-slate-400 text-xs">
            <span>Menampilkan <strong class="text-slate-900 dark:text-white font-bold" x-text="filteredUomProducts.length"></strong> dari <strong class="text-slate-900 dark:text-white font-bold" x-text="uomProductsList.length"></strong> produk SKU</span>
            <button type="button" @click="showUomProductsModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>
