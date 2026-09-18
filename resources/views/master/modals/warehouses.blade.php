<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL GUDANG (WAREHOUSE)                                    -->
<!-- ========================================================================= -->
<div x-show="showDetailWhModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-2xl p-6 rounded-2xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[90vh] overflow-y-auto" @click.away="showDetailWhModal = false">
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
<div x-show="showEditWhModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto" @click.away="showEditWhModal = false">
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
