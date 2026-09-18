<!-- ========================================================================= -->
<!-- MODAL POPUP: DETAIL BIN                                                   -->
<!-- ========================================================================= -->
<div x-show="showDetailBinModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-2xl p-6 rounded-2xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[90vh] overflow-y-auto" @click.away="showDetailBinModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-circle-info text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Detail Slot Lokasi BIN</h3>
            </div>
            <button type="button" @click="showDetailBinModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <template x-if="detailBin">
            <div class="space-y-4 text-xs">
                <!-- Header Info Card -->
                <div class="p-4 bg-slate-900/80 border border-slate-800 rounded-xl flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">Kode Barcode BIN (ID)</div>
                        <div class="text-base font-mono font-bold text-amber-400" x-text="detailBin.id"></div>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-full font-bold text-xs" :class="(detailBin.active_pallets_count >= detailBin.max_pallet) ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'" x-text="(detailBin.active_pallets_count || 0) + ' / ' + detailBin.max_pallet + ' Palet ' + (detailBin.active_pallets_count >= detailBin.max_pallet ? '(FULL)' : '')"></span>
                    </div>
                </div>

                <!-- Main Grid Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Rak Induk & Gudang</div>
                        <div class="font-bold text-slate-800 dark:text-white text-sm" x-text="'Rak ' + detailBin.rack_id + (detailBin.rack && detailBin.rack.warehouse ? ' (' + detailBin.rack.warehouse.name + ')' : '')"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl">
                        <div class="text-slate-400 text-[11px] mb-1">Posisi Slot</div>
                        <div class="font-bold font-mono text-indigo-300" x-text="'Kolom ' + detailBin.column_no + ' • Level ' + detailBin.level_no"></div>
                    </div>
                    <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-xl sm:col-span-2">
                        <div class="text-slate-400 text-[11px] mb-1">Kapasitas Maksimal</div>
                        <div class="font-medium text-slate-700 dark:text-slate-300" x-text="'Maksimal ' + detailBin.max_pallet + ' Palet dalam slot BIN ini'"></div>
                    </div>
                </div>

                <!-- Active Pallets Inside BIN -->
                <div class="space-y-2">
                    <div class="font-bold text-slate-700 dark:text-slate-300 flex items-center space-x-1.5">
                        <i class="fa-solid fa-pallet text-indigo-400"></i>
                        <span>Palet Aktif yang Sedang Disimpan</span>
                    </div>
                    <div class="border border-slate-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px]">
                                <tr>
                                    <th class="p-2.5">Kode Palet (ID)</th>
                                    <th class="p-2.5">Tipe Palet</th>
                                    <th class="p-2.5">Kapasitas Box</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                                <template x-for="(plt, idx) in detailBin.pallets" :key="idx">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2.5 font-bold font-mono text-indigo-400" x-text="plt.id"></td>
                                        <td class="p-2.5 font-medium text-slate-300" x-text="plt.pallet_type_id || '-'"></td>
                                        <td class="p-2.5 font-bold text-emerald-400" x-text="(plt.max_box || '-') + ' Box'"></td>
                                    </tr>
                                </template>
                                <template x-if="!detailBin.pallets || detailBin.pallets.length === 0">
                                    <tr>
                                        <td colspan="3" class="p-3 text-center text-slate-500 italic">Slot BIN ini saat ini kosong (tidak ada palet aktif).</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-700/60 text-slate-400 text-[11px]">
                    <span>Dibuat: <strong class="text-slate-300" x-text="detailBin.created_at || '-'"></strong></span>
                    <button type="button" @click="showDetailBinModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Tutup</button>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL POPUP: EDIT BIN                                                     -->
<!-- ========================================================================= -->
<div x-show="showEditBinModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto" @click.away="showEditBinModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-amber-400">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Slot Lokasi BIN</h3>
            </div>
            <button type="button" @click="showEditBinModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form :action="'{{ url('/master-data/bin') }}/' + editBinForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kode Barcode BIN (ID)</label>
                <input type="text" :value="editBinForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-amber-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Rak Induk <span class="text-rose-400">*</span></label>
                <select id="select_edit_bin_rack_id" name="rack_id" x-model="editBinForm.rack_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <option value="">-- Pilih Rak Induk --</option>
                    @foreach($allRacks as $r)
                        <option value="{{ $r->id }}">{{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Nomor Kolom <span class="text-rose-400">*</span></label>
                    <input type="number" min="1" name="column_no" x-model="editBinForm.column_no" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Nomor Level <span class="text-rose-400">*</span></label>
                    <input type="number" min="1" name="level_no" x-model="editBinForm.level_no" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-slate-400 mb-1 font-semibold">Kapasitas Maksimal Palet <span class="text-rose-400">*</span></label>
                <input type="number" min="1" max="5" name="max_pallet" x-model="editBinForm.max_pallet" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showEditBinModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Perbarui BIN</span>
                </button>
            </div>
        </form>
    </div>
</div>
