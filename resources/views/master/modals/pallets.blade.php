<!-- ========================================================================= -->
<!-- MODAL DETAIL PALET                                                        -->
<!-- ========================================================================= -->
<div x-show="showDetailPalletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-blue-500/40 max-h-[90vh] overflow-y-auto" @click.away="showDetailPalletModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-pallet text-xl"></i>
                <h3 class="text-base font-bold text-white">Detail Master Palet Storage</h3>
            </div>
            <button type="button" @click="showDetailPalletModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <template x-if="detailPallet">
            <div class="space-y-4 text-xs">
                <div class="p-4 bg-slate-900/80 rounded-xl border border-slate-800 space-y-2.5">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Kode Barcode Palet:</span>
                        <span class="font-mono font-extrabold text-sky-400 text-sm" x-text="detailPallet.id"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Jenis / Tipe Palet:</span>
                        <span class="font-bold text-white" x-text="detailPallet.type_name || detailPallet.pallet_type_id"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Lokasi Penempatan BIN:</span>
                        <span class="font-mono font-bold text-amber-400" x-text="detailPallet.bin_id ? detailPallet.bin_id : 'TRANSIT (Lantai Gudang)'"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Gudang / Lokasi:</span>
                        <span class="font-bold text-slate-300" x-text="detailPallet.warehouse_name || 'Gudang Utama'"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Muatan Box Saat Ini:</span>
                        <span class="font-bold text-emerald-400" x-text="(detailPallet.active_boxes_count || 0) + ' / ' + (detailPallet.max_capacity || 72) + ' Box'"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-slate-400">Status Keaktifan:</span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-bold" 
                              :class="detailPallet.is_active ? 'bg-emerald-950/60 text-emerald-400 border border-emerald-800' : 'bg-rose-950/60 text-rose-400 border border-rose-800'"
                              x-text="detailPallet.is_active ? 'Aktif' : 'Non-Aktif'"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Terdaftar Pada:</span>
                        <span class="text-slate-400" x-text="detailPallet.created_at || '-'"></span>
                    </div>
                </div>
            </div>
        </template>

        <div class="flex justify-end pt-2">
            <button type="button" @click="showDetailPalletModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer text-xs">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT PALET                                                          -->
<!-- ========================================================================= -->
<div x-show="showEditPalletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto" @click.away="showEditPalletModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-amber-400">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h3 class="text-base font-bold text-white">Edit Master Palet</h3>
            </div>
            <button type="button" @click="showEditPalletModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form :action="'{{ url('/master-data/pallet') }}/' + editPalletForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-slate-300 font-semibold mb-1">Kode Barcode Palet (ID)</label>
                <input type="text" x-model="editPalletForm.id" readonly class="w-full p-2.5 bg-slate-800 border border-slate-700 rounded-xl text-sky-400 font-mono font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Tipe / Jenis Palet <span class="text-rose-400">*</span></label>
                <select name="pallet_type_id" x-model="editPalletForm.pallet_type_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    @foreach($allPalletTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Lokasi Slot BIN</label>
                <select name="bin_id" x-model="editPalletForm.bin_id" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Transit (Tanpa Slot BIN) --</option>
                    @foreach($allBins as $b)
                        <option value="{{ $b->id }}">BIN: {{ $b->id }} (Rak: {{ $b->rack_id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Kapasitas Maksimal Box</label>
                <input type="number" min="1" name="max_capacity" x-model="editPalletForm.max_capacity" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-bold">
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Status Keaktifan <span class="text-rose-400">*</span></label>
                <select name="is_active" x-model="editPalletForm.is_active" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showEditPalletModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-floppy-disk text-xs mr-1"></i>
                    <span class="btn-text">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL MOBILE: TAMBAH PALET                                                -->
<!-- ========================================================================= -->
<div x-show="showAddPalletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
    <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-blue-500/40 max-h-[90vh] overflow-y-auto" @click.away="showAddPalletModal = false">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-sky-400">
                <i class="fa-solid fa-plus-circle text-xl"></i>
                <h3 class="text-base font-bold text-white">Tambah Master Palet Baru</h3>
            </div>
            <button type="button" @click="showAddPalletModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('master.pallet.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block text-slate-300 font-semibold mb-1">Kode Barcode Palet (ID) <span class="text-rose-400">*</span></label>
                <div class="flex items-center space-x-2">
                    <input type="text" name="id" x-model="forms.pallet_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="misal: PLT-0099">
                    <button type="button" @click="autoPalletCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-xs rounded-xl transition cursor-pointer">Auto</button>
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Tipe / Jenis Palet <span class="text-rose-400">*</span></label>
                <select name="pallet_type_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Pilih Jenis Palet --</option>
                    @foreach($allPalletTypes as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Lokasi Slot BIN (Opsional)</label>
                <select name="bin_id" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="">-- Transit (Tanpa Slot BIN) --</option>
                    @foreach($allBins as $b)
                        <option value="{{ $b->id }}">BIN: {{ $b->id }} (Rak: {{ $b->rack_id }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Kapasitas Maksimal Box</label>
                <input type="number" min="1" name="max_capacity" value="72" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-bold">
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Status Keaktifan</label>
                <select name="is_active" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                <button type="button" @click="showAddPalletModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2 bg-[#3850c9] hover:bg-[#2b3ea6] text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-floppy-disk text-xs mr-1"></i>
                    <span class="btn-text">Simpan Palet</span>
                </button>
            </div>
        </form>
    </div>
</div>
