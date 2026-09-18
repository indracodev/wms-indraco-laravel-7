<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL KANTOR                                                -->
<!-- ========================================================================= -->
<div x-show="showDetailOfficeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-2xl p-6 rounded-2xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[90vh] overflow-y-auto" @click.away="showDetailOfficeModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-circle-info text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Detail Kantor Pusat / Perwakilan</h3>
            </div>
            <button type="button" @click="showDetailOfficeModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <template x-if="detailOffice">
            <div class="space-y-4 text-xs">
                <!-- Header Info Card -->
                <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-xl flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">Kode Kantor (ID)</div>
                        <div class="text-base font-mono font-bold text-indigo-400" x-text="detailOffice.id"></div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-full font-bold text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30" x-text="(detailOffice.branches ? detailOffice.branches.length : 0) + ' Plant Terdaftar'"></span>
                    </div>
                </div>

                <!-- Main Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Nama Kantor</div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm" x-text="detailOffice.name"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Alamat</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="detailOffice.address || '-'"></div>
                    </div>
                </div>

                <!-- Plants Under Office -->
                <div class="space-y-2">
                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center space-x-1.5">
                        <i class="fa-solid fa-industry text-indigo-400"></i>
                        <span>Daftar Plant Terkait</span>
                    </div>
                    <div class="border border-slate-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px]">
                                <tr>
                                    <th class="p-2.5">Kode Plant</th>
                                    <th class="p-2.5">Nama Plant</th>
                                    <th class="p-2.5">Kota / Wilayah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                                <template x-for="(br, idx) in detailOffice.branches" :key="idx">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2.5 font-bold font-mono text-indigo-400" x-text="br.id"></td>
                                        <td class="p-2.5 font-medium text-slate-800 dark:text-white" x-text="br.name"></td>
                                        <td class="p-2.5 text-slate-400" x-text="br.city || '-'"></td>
                                    </tr>
                                </template>
                                <template x-if="!detailOffice.branches || detailOffice.branches.length === 0">
                                    <tr>
                                        <td colspan="3" class="p-3 text-center text-slate-500 italic">Belum ada Plant yang terdaftar pada kantor ini.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-700/60 text-slate-400 text-[11px]">
                    <span>Dibuat: <strong class="text-slate-300" x-text="detailOffice.created_at || '-'"></strong></span>
                    <button type="button" @click="showDetailOfficeModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: EDIT KANTOR                                                  -->
<!-- ========================================================================= -->
<div x-show="showEditOfficeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto" @click.away="showEditOfficeModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-amber-400">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Kantor Pusat / Perwakilan</h3>
            </div>
            <button type="button" @click="showEditOfficeModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form :action="'{{ url('/master-data/office') }}/' + editOfficeForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kode Kantor (ID)</label>
                <input type="text" :value="editOfficeForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-indigo-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Nama Kantor <span class="text-rose-400">*</span></label>
                <input type="text" name="name" x-model="editOfficeForm.name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Nama Kantor">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Alamat</label>
                <textarea name="address" x-model="editOfficeForm.address" rows="3" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Alamat lengkap kantor..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showEditOfficeModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Perbarui Kantor</span>
                </button>
            </div>
        </form>
    </div>
</div>
