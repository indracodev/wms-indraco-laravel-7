<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL GUDANG (WAREHOUSE)                                    -->
<!-- ========================================================================= -->
<div x-show="showDetailWhModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" @click.self="showDetailWhModal = false" x-cloak>
    <div class="glass-panel w-full max-w-2xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] overflow-y-auto my-auto" @click.away="showDetailWhModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-circle-info text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Detail Gudang Operasional</h3>
            </div>
            <button type="button" @click="showDetailWhModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <template x-if="detailWh">
            <div class="space-y-4 text-xs">
                <!-- Header Info Card -->
                <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-xl flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">Kode Gudang (ID)</div>
                        <div class="text-base font-mono font-bold text-indigo-400" x-text="detailWh.id"></div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-full font-bold text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="(detailWh.racks ? detailWh.racks.length : 0) + ' Rak Terdaftar'"></span>
                    </div>
                </div>

                <!-- Main Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Nama Gudang</div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm" x-text="detailWh.name"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Plant Induk</div>
                        <div class="font-bold text-indigo-400" x-text="detailWh.branch ? detailWh.branch.name + ' (' + detailWh.branch_id + ')' : detailWh.branch_id"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl sm:col-span-2">
                        <div class="text-slate-400 text-[11px] mb-1">Alamat</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="detailWh.address || '-'"></div>
                    </div>
                </div>

                <!-- Racks Under Warehouse -->
                <div class="space-y-2">
                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center space-x-1.5">
                        <i class="fa-solid fa-bars-staggered text-indigo-400"></i>
                        <span>Daftar Rak di Gudang Ini</span>
                    </div>
                    <div class="border border-slate-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px]">
                                <tr>
                                    <th class="p-2.5">Kode Barcode Rak</th>
                                    <th class="p-2.5">Nama Rak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                                <template x-for="(rk, idx) in detailWh.racks" :key="idx">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2.5 font-bold font-mono text-indigo-400" x-text="rk.id"></td>
                                        <td class="p-2.5 font-medium text-slate-800 dark:text-white" x-text="'Rak ' + rk.rack_name"></td>
                                    </tr>
                                </template>
                                <template x-if="!detailWh.racks || detailWh.racks.length === 0">
                                    <tr>
                                        <td colspan="2" class="p-3 text-center text-slate-500 italic">Belum ada Rak yang terdaftar pada Gudang ini.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-700/60 text-slate-400 text-[11px]">
                    <span>Dibuat: <strong class="text-slate-300" x-text="detailWh.created_at || '-'"></strong></span>
                    <button type="button" @click="showDetailWhModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: EDIT GUDANG (WAREHOUSE)                                      -->
<!-- ========================================================================= -->
<div x-show="showEditWhModal" class="fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md" @click.self="showEditWhModal = false" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-full overflow-y-auto" @click.away="showEditWhModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-amber-400">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Gudang Operasional</h3>
            </div>
            <button type="button" @click="showEditWhModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form :action="'{{ url('/master-data/warehouse') }}/' + editWhForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kode Gudang (ID)</label>
                <input type="text" :value="editWhForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-indigo-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Plant Induk <span class="text-rose-400">*</span></label>
                <select id="select_edit_wh_branch_id" name="branch_id" x-model="editWhForm.branch_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">-- Pilih Plant Induk --</option>
                    @foreach($allBranches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Nama Gudang <span class="text-rose-400">*</span></label>
                <input type="text" name="name" x-model="editWhForm.name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Nama Gudang">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Alamat</label>
                <textarea name="address" x-model="editWhForm.address" rows="3" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Alamat atau lokasi detail gudang..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showEditWhModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Perbarui Gudang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: TAMBAH GUDANG (WAREHOUSE) BARU                               -->
<!-- ========================================================================= -->
<div x-show="showAddWhModal" 
     class="fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="showAddWhModal = false"
     x-cloak>
    <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-full overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="showAddWhModal = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg border border-indigo-100 dark:border-indigo-800/40 shadow-sm">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">Tambah Gudang Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Daftarkan unit gudang operasional baru</p>
                </div>
            </div>
            <button type="button" @click="showAddWhModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.warehouse.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            
            <!-- Plant Induk -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Plant Induk <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Lokasi Plant</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                        <i class="fa-solid fa-industry text-sm"></i>
                    </div>
                    <select name="branch_id" required class="w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold text-xs focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 appearance-none shadow-sm cursor-pointer transition">
                        @foreach($allBranches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->id }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Kode Gudang (ID) -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Kode Gudang (ID) <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Format: WHS-XXX / GSK.01</span>
                </label>
                <div class="flex items-center space-x-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </div>
                        <input type="text" name="id" x-model="forms.wh_id" required 
                               class="w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold tracking-wider uppercase focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                               placeholder="misal: GSK.678.II">
                    </div>
                    <button type="button" @click="autoWhCode()" 
                            class="px-4 py-3 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300 font-mono font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-800/50 transition shadow-sm cursor-pointer active:scale-95 flex items-center space-x-1.5 shrink-0"
                            title="Generate Kode Otomatis">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        <span>Auto</span>
                    </button>
                </div>
            </div>

            <!-- Nama Gudang -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Nama Gudang <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Nama unit gudang</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                        <i class="fa-solid fa-warehouse text-sm"></i>
                    </div>
                    <input type="text" name="name" required 
                           class="w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                           placeholder="Contoh: Gudang Bahan Baku A">
                </div>
            </div>

            <!-- Alamat Gudang -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Alamat / Detail Lokasi</span>
                    <span class="text-[10px] text-slate-400 font-normal">Lokasi fisik gudang</span>
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                        <i class="fa-solid fa-location-dot text-sm"></i>
                    </div>
                    <textarea name="address" rows="2"
                              class="w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                              placeholder="Alamat atau lokasi detail gudang..."></textarea>
                </div>
            </div>

            <!-- Action Buttons Grid (Ergonomic Full-Width for Mobile) -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" @click="showAddWhModal = false" 
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer active:scale-95 text-center">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95 text-center">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span class="btn-text">Simpan Gudang</span>
                </button>
            </div>
        </form>
    </div>
</div>
