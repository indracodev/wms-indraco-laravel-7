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
