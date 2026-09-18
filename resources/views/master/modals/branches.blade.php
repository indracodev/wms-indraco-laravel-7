<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL PLANT (BRANCH)                                        -->
<!-- ========================================================================= -->
<div x-show="showDetailBranchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-2xl p-6 rounded-2xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[90vh] overflow-y-auto" @click.away="showDetailBranchModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-circle-info text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Detail Plant Operasional</h3>
            </div>
            <button type="button" @click="showDetailBranchModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <template x-if="detailBranch">
            <div class="space-y-4 text-xs">
                <!-- Header Info Card -->
                <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-xl flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">Kode Plant (ID)</div>
                        <div class="text-base font-mono font-bold text-indigo-400" x-text="detailBranch.id"></div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-full font-bold text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="(detailBranch.warehouses ? detailBranch.warehouses.length : 0) + ' Gudang Terdaftar'"></span>
                    </div>
                </div>

                <!-- Main Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Nama Plant</div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm" x-text="detailBranch.name"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Kantor Induk</div>
                        <div class="font-bold text-indigo-400" x-text="detailBranch.office ? detailBranch.office.name + ' (' + detailBranch.office_id + ')' : detailBranch.office_id"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl sm:col-span-2">
                        <div class="text-slate-400 text-[11px] mb-1">Kota / Wilayah</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="detailBranch.city || '-'"></div>
                    </div>
                </div>

                <!-- Warehouses Under Branch -->
                <div class="space-y-2">
                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center space-x-1.5">
                        <i class="fa-solid fa-warehouse text-indigo-400"></i>
                        <span>Daftar Gudang di Plant Ini</span>
                    </div>
                    <div class="border border-slate-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px]">
                                <tr>
                                    <th class="p-2.5">Kode Gudang</th>
                                    <th class="p-2.5">Nama Gudang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                                <template x-for="(wh, idx) in detailBranch.warehouses" :key="idx">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2.5 font-bold font-mono text-indigo-400" x-text="wh.id"></td>
                                        <td class="p-2.5 font-medium text-slate-800 dark:text-white" x-text="wh.name"></td>
                                    </tr>
                                </template>
                                <template x-if="!detailBranch.warehouses || detailBranch.warehouses.length === 0">
                                    <tr>
                                        <td colspan="2" class="p-3 text-center text-slate-500 italic">Belum ada Gudang yang terdaftar pada Plant ini.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-700/60 text-slate-400 text-[11px]">
                    <span>Dibuat: <strong class="text-slate-300" x-text="detailBranch.created_at || '-'"></strong></span>
                    <button type="button" @click="showDetailBranchModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: EDIT PLANT (BRANCH)                                          -->
<!-- ========================================================================= -->
<div x-show="showEditBranchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto" @click.away="showEditBranchModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-amber-400">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Plant Operasional</h3>
            </div>
            <button type="button" @click="showEditBranchModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form :action="'{{ url('/master-data/branch') }}/' + editBranchForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kode Plant (ID)</label>
                <input type="text" :value="editBranchForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-indigo-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kantor Induk <span class="text-rose-400">*</span></label>
                <select id="select_edit_branch_office_id" name="office_id" x-model="editBranchForm.office_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">-- Pilih Kantor Induk --</option>
                    @foreach($allOffices as $o)
                        <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Nama Plant <span class="text-rose-400">*</span></label>
                <input type="text" name="name" x-model="editBranchForm.name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Nama Plant">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kota / Wilayah</label>
                <input type="text" name="city" x-model="editBranchForm.city" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Surabaya / Gresik / Sidoarjo">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showEditBranchModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Perbarui Plant</span>
                </button>
            </div>
        </form>
    </div>
</div>
