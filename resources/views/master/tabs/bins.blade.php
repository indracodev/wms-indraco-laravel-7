<!-- ========================================================================= -->
<!-- TAB 5: BINS (BIN SLOT)                                                    -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-qrcode text-indigo-400"></i>
            <span>Daftar BIN Storage Slot</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <input type="hidden" name="tab" value="bins">
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Filter Rak</label>
                    <select id="select_rack_filter" name="rack_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Rak --</option>
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}" {{ request('rack_filter') == $r->id ? 'selected' : '' }}>Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Cari Keyword BIN</label>
                    <input type="text" name="search_bin" value="{{ request('search_bin') }}" placeholder="🔍 Cari ID BIN..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Filter</button>
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'bins']) }}', 'bins')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Barcode BIN</th>
                            <th class="p-3">Rak & Gudang</th>
                            <th class="p-3">Posisi (Kolom & Level)</th>
                            <th class="p-3">Kapasitas Palet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($bins as $bn)
                            @php $activePalletsCount = $bn->active_pallets_count ?? 0; @endphp
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-amber-400">{{ $bn->id }}</td>
                                <td class="p-3 text-slate-300">Rak {{ $bn->rack_id }} (Gudang: {{ $bn->rack->warehouse_id ?? '-' }})</td>
                                <td class="p-3 text-slate-400 font-mono">Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}</td>
                                <td class="p-3 font-bold">
                                    <span class="px-2 py-0.5 rounded text-[11px] {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                        {{ $activePalletsCount }} / {{ $bn->max_pallet }} Palet {{ $activePalletsCount >= $bn->max_pallet ? '(FULL)' : '' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500">Tidak ada data BIN.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List -->
            <div class="space-y-2.5 md:hidden">
                @forelse($bins as $bn)
                    @php $activePalletsCount = $bn->active_pallets_count ?? 0; @endphp
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <div class="font-mono font-bold text-amber-400">{{ $bn->id }}</div>
                            <div class="text-slate-300 mt-0.5">Rak {{ $bn->rack_id }}</div>
                            <div class="text-[11px] text-slate-400 mt-1">Kolom {{ $bn->column_no }} • Level {{ $bn->level_no }}</div>
                        </div>
                        <div class="text-right font-bold">
                            <span class="px-2 py-1 rounded text-[11px] {{ $activePalletsCount >= $bn->max_pallet ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                                {{ $activePalletsCount }}/{{ $bn->max_pallet }} Palet
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada data BIN.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $bins->links() }}
            </div>
        </div>

        <!-- Form Tambah BIN -->
        @canAccessModule('master', 'create')
        <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-400"></i>
                <span>Tambah BIN Baru</span>
            </h4>
            <form action="{{ route('master.bin.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Rak Induk <span class="text-rose-400">*</span></label>
                    <select id="select_rack_id" name="rack_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($allRacks as $r)
                            <option value="{{ $r->id }}">Rak: {{ $r->id }} (Gudang: {{ $r->warehouse_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kode Barcode BIN (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.bin_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: B.10.2">
                        <button type="button" @click="autoBinCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1 font-medium">Kolom No</label>
                        <input type="number" name="column_no" required min="1" value="1" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1 font-medium">Level No</label>
                        <input type="number" name="level_no" required min="1" value="1" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kapasitas Maksimal Palet</label>
                    <input type="number" name="max_pallet" required min="1" max="5" value="2" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan BIN</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>
