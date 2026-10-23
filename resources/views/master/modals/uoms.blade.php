<!-- ========================================================================= -->
<!-- MODAL POPUP: TAMBAH SATUAN UOM BARU                                       -->
<!-- ========================================================================= -->
<div x-show="showAddUomModal" 
     class="fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="showAddUomModal = false"
     x-cloak>
    <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-full overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="showAddUomModal = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg border border-indigo-100 dark:border-indigo-800/40 shadow-sm">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">Tambah Satuan UOM Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Daftarkan Master Satuan UOM Inventory</p>
                </div>
            </div>
            <button type="button" @click="showAddUomModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.uom.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Kode Satuan (ID) <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Format: PCS / BOX</span>
                </label>
                <div class="flex items-center space-x-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </div>
                        <input type="text" name="id" x-model="forms.uom_id" required 
                               class="w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold tracking-wider uppercase focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                               placeholder="PCS / BOX">
                    </div>
                    <button type="button" @click="autoUomCode()" 
                            class="px-4 py-3 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300 font-mono font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-800/50 transition shadow-sm cursor-pointer active:scale-95 flex items-center space-x-1.5 shrink-0"
                            title="Generate Kode Otomatis">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        <span>Auto</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Nama Satuan UOM <span class="text-rose-500">*</span></span>
                </label>
                <input type="text" name="name" required 
                       class="w-full p-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                       placeholder="misal: Box / Pieces / Kilogram">
            </div>

            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold">Keterangan / Fungsi</label>
                <textarea name="description" rows="2" 
                          class="w-full p-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                          placeholder="Catatan fungsi atau standar kemasan..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" @click="showAddUomModal = false" 
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer active:scale-95 text-center">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95 text-center">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span class="btn-text">Simpan UOM</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL SATUAN UOM                                            -->
<!-- ========================================================================= -->
<div x-show="showDetailUomModal" 
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="showDetailUomModal = false"
     x-cloak>
    <div class="glass-panel w-full max-w-2xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] overflow-y-auto my-auto" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="showDetailUomModal = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 pb-3">
            <div class="flex items-center space-x-3 text-sky-600 dark:text-sky-400">
                <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950/50 flex items-center justify-center text-lg border border-sky-100 dark:border-sky-800/40 shadow-sm">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">Detail Satuan UOM Inventory</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Informasi master satuan unit dasar dan daftar katalog produk SKU terkait</p>
                </div>
            </div>
            <button type="button" @click="showDetailUomModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <template x-if="detailUom">
            <div class="space-y-4 text-xs">
                <!-- Header Info Card -->
                <div class="p-4 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-inner">
                    <div>
                        <div class="text-slate-400 text-[11px] font-medium uppercase tracking-wider">Kode Satuan (ID)</div>
                        <div class="text-xl font-mono font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5" x-text="detailUom.id"></div>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1.5 rounded-xl font-extrabold text-xs bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 shadow-xs inline-flex items-center" 
                              x-text="(detailUom.products ? detailUom.products.length : (detailUom.products_count || 0)) + ' Produk SKU Terdaftar'">
                        </span>
                    </div>
                </div>

                <!-- Main Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                        <div class="text-slate-400 text-[11px] mb-1 font-medium">Nama Satuan UOM</div>
                        <div class="font-bold text-slate-900 dark:text-white text-sm" x-text="detailUom.name"></div>
                    </div>
                    <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                        <div class="text-slate-400 text-[11px] mb-1 font-medium">Keterangan / Fungsi Kemasan</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="detailUom.description || 'Tidak ada keterangan'"></div>
                    </div>
                </div>

                <!-- Related Products Section -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 dark:text-slate-200 flex items-center space-x-1.5">
                            <i class="fa-solid fa-boxes-stacked text-indigo-500 dark:text-indigo-400"></i>
                            <span>Daftar Produk Menggunakan Satuan Ini</span>
                        </div>
                        <template x-if="detailUom.products && detailUom.products.length > 0">
                            <button type="button" 
                                    @click="showDetailUomModal = false; filterProductsTabByUom(detailUom.name + ' (' + detailUom.id + ')')"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold text-[11px] flex items-center gap-1 cursor-pointer">
                                <span>Buka di Tab Produk SKU</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </button>
                        </template>
                    </div>

                    <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden max-h-60 overflow-y-auto bg-white dark:bg-slate-950/60 shadow-inner">
                        <table class="w-full text-left">
                            <thead class="bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-400 uppercase text-[10px] font-bold sticky top-0 z-10 border-b border-slate-200 dark:border-slate-800">
                                <tr>
                                    <th class="p-2.5">Kode SKU</th>
                                    <th class="p-2.5">Nama Produk</th>
                                    <th class="p-2.5">Brand</th>
                                    <th class="p-2.5">Group</th>
                                    <th class="p-2.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                <template x-for="(prod, idx) in (detailUom.products || [])" :key="idx">
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/60 transition">
                                        <td class="p-2.5 font-bold font-mono text-indigo-600 dark:text-indigo-400" x-text="prod.id"></td>
                                        <td class="p-2.5 font-semibold text-slate-900 dark:text-white" x-text="prod.name"></td>
                                        <td class="p-2.5 text-slate-600 dark:text-slate-300" x-text="prod.brand || '-'"></td>
                                        <td class="p-2.5 text-slate-500 dark:text-slate-400 text-[11px]" x-text="prod.item_group || '-'"></td>
                                        <td class="p-2.5 text-center">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                  :class="prod.is_active ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30'"
                                                  x-text="prod.is_active ? 'Active' : 'Inactive'">
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="!detailUom.products || detailUom.products.length === 0">
                                    <tr>
                                        <td colspan="5" class="p-6 text-center text-slate-500 italic">
                                            Belum ada produk SKU yang terdaftar menggunakan satuan ini.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center justify-between border-t border-slate-200 dark:border-slate-800 pt-3">
                    <button type="button" @click="showDetailUomModal = false" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer active:scale-95">
                        Tutup
                    </button>

                    @canAccessModule('master', 'update')
                    <button type="button" 
                            @click="showDetailUomModal = false; openEditUomModal(detailUom.id, detailUom.name, detailUom.description || '')" 
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer flex items-center space-x-1.5 active:scale-95">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                        <span>Edit Satuan UOM</span>
                    </button>
                    @endcanAccessModule
                </div>
            </div>
        </template>
    </div>
</div>
