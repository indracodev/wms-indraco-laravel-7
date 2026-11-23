<!-- ========================================================================= -->
<!-- TAB 8: PALLETS (MASTER PALET)                                             -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar Master Palet Storage</span>
        </h3>
        <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">
            Total: {{ $pallets->total() }} Palet Terdaftar
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter & Search Bar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex flex-col sm:flex-row items-center gap-2.5 text-[13px]">
                <input type="hidden" name="tab" value="pallets">
                <div class="search-input-group flex-1 w-full sm:w-auto">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'pallets']) }}', 'pallets')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_pallet" value="{{ request('search_pallet') }}" placeholder="Cari Kode Palet / Lokasi BIN..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari Palet">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <div class="w-full sm:w-44">
                    <select name="type_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Semua Tipe --</option>
                        @foreach($allPalletTypes as $pt)
                            <option value="{{ $pt->id }}" {{ request('type_filter') == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-44">
                    <select name="bin_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Semua Lokasi BIN --</option>
                        @foreach($allBins as $b)
                            <option value="{{ $b->id }}" {{ request('bin_filter') == $b->id ? 'selected' : '' }}>BIN: {{ $b->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-36">
                    <select name="status_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Status --</option>
                        <option value="on_rack" {{ request('status_filter') == 'on_rack' ? 'selected' : '' }}>On Rack</option>
                        <option value="in_transit" {{ request('status_filter') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Palet</th>
                            <th class="p-3 whitespace-nowrap">Tipe / Jenis Palet</th>
                            <th class="p-3 whitespace-nowrap">Lokasi Penempatan</th>
                            <th class="p-3 whitespace-nowrap text-center">Isi Box / Kapasitas</th>
                            <th class="p-3 whitespace-nowrap text-center">Status</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
                        @forelse($pallets as $plt)
                            @php
                                $activeBoxesCount = $plt->active_boxes_count ?? 0;
                                $pltData = [
                                    'id' => $plt->id,
                                    'pallet_type_id' => $plt->pallet_type_id,
                                    'type_name' => $plt->type->name ?? $plt->pallet_type_id,
                                    'bin_id' => $plt->bin_id ?? '',
                                    'warehouse_name' => $plt->bin->rack->warehouse->name ?? ($plt->bin ? 'Gudang Utama' : 'TRANSIT'),
                                    'rack_id' => $plt->bin->rack_id ?? '-',
                                    'status' => $plt->status,
                                    'max_capacity' => $plt->max_capacity ?? 72,
                                    'is_active' => (bool)$plt->is_active,
                                    'active_boxes_count' => $activeBoxesCount,
                                    'created_at' => $plt->created_at ? $plt->created_at->format('d M Y H:i') : '-',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">
                                    {{ $plt->id }}
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-xs font-semibold">
                                        {{ $plt->type->name ?? $plt->pallet_type_id }}
                                    </span>
                                </td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium break-words">
                                    @if(!empty($plt->bin_id))
                                        <span class="font-bold text-slate-900 dark:text-white font-mono">{{ $plt->bin_id }}</span>
                                        <span class="text-slate-500 dark:text-slate-400 text-xs">({{ $plt->bin->rack->warehouse->name ?? 'Gudang' }})</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 text-xs font-bold">
                                            TRANSIT
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap {{ $activeBoxesCount > 0 ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-300 dark:border-slate-700' }}">
                                        {{ $activeBoxesCount }} / {{ $plt->max_capacity ?? 72 }} Box
                                    </span>
                                </td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    @if($plt->is_active)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 text-xs font-bold">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-pallet="{{ json_encode($pltData) }}" 
                                            onclick="window.openDetailPalletModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Lihat Detail Palet">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-pallet="{{ json_encode($pltData) }}" 
                                                onclick="window.openEditPalletModal(this)" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit Palet">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.pallet.destroy', $plt->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="Hapus Palet">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-slate-500 text-[13px]">Belum ada data palet terdaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card List View -->
            <div class="space-y-3.5 md:hidden">
                @forelse($pallets as $plt)
                    @php
                        $activeBoxesCount = $plt->active_boxes_count ?? 0;
                        $pltData = [
                            'id' => $plt->id,
                            'pallet_type_id' => $plt->pallet_type_id,
                            'type_name' => $plt->type->name ?? $plt->pallet_type_id,
                            'bin_id' => $plt->bin_id ?? '',
                            'warehouse_name' => $plt->bin->rack->warehouse->name ?? ($plt->bin ? 'Gudang Utama' : 'TRANSIT'),
                            'rack_id' => $plt->bin->rack_id ?? '-',
                            'status' => $plt->status,
                            'max_capacity' => $plt->max_capacity ?? 72,
                            'is_active' => (bool)$plt->is_active,
                            'active_boxes_count' => $activeBoxesCount,
                            'created_at' => $plt->created_at ? $plt->created_at->format('d M Y H:i') : '-',
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-800/70 text-blue-800 dark:text-blue-300 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $plt->id }}
                            </span>
                            <span class="px-2 py-0.5 rounded-xl text-xs font-bold border shadow-xs whitespace-nowrap {{ $plt->is_active ? 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30' : 'bg-rose-50 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-500/30' }}">
                                {{ $plt->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Tipe Palet:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $plt->type->name ?? $plt->pallet_type_id }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Lokasi Penempatan:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $plt->bin_id ?: 'TRANSIT' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Muatan Box:</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $activeBoxesCount }} / {{ $plt->max_capacity ?? 72 }} Box</span>
                            </div>
                        </div>

                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-pallet="{{ json_encode($pltData) }}" 
                                    onclick="window.openDetailPalletModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-pallet="{{ json_encode($pltData) }}" 
                                    onclick="window.openEditPalletModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.pallet.destroy', $plt->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Belum ada data palet terdaftar.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $pallets->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah Palet Baru -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-blue-500"></i>
                <span>Tambah Master Palet Baru</span>
            </h4>
            <form action="{{ route('master.pallet.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Barcode Palet (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.pallet_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-blue-500 focus:outline-none shadow-sm" placeholder="misal: PLT-0099">
                        <button type="button" @click="autoPalletCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Tipe / Jenis Palet <span class="text-rose-500">*</span></label>
                    <select name="pallet_type_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        <option value="">-- Pilih Jenis Palet --</option>
                        @foreach($allPalletTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }} ({{ $pt->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Lokasi Slot BIN (Opsional)</label>
                    <select name="bin_id" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        <option value="">-- Transit (Tanpa Slot BIN) --</option>
                        @foreach($allBins as $b)
                            <option value="{{ $b->id }}">BIN: {{ $b->id }} (Rak: {{ $b->rack_id }})</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika palet baru berstatus TRANSIT di lantai gudang.</p>
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kapasitas Maksimal Box</label>
                    <input type="number" min="1" name="max_capacity" value="72" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold shadow-sm text-[13px]">
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Status Keaktifan</label>
                    <select name="is_active" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        <option value="1">Aktif</option>
                        <option value="0">Non-Aktif</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 text-[13px]">
                    <span class="btn-text">Simpan Palet</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>

<!-- FLOATING ACTION BUTTON (FAB) FOR MOBILE -->
@canAccessModule('master', 'create')
<div class="fixed bottom-[125px] right-4 z-40 md:hidden">
    <button type="button" 
            @click="openAddPalletModal()" 
            class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-600 to-sky-500 hover:from-blue-500 hover:to-sky-400 text-white font-bold shadow-xl shadow-blue-600/40 border-2 border-white dark:border-slate-800 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
            title="Tambah Palet Baru">
        <i class="fa-solid fa-plus text-lg"></i>
    </button>
</div>
@endcanAccessModule
