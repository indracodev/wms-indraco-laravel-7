<!-- ========================================================================= -->
<!-- TAB 4: RACKS (RAK - dilengkapi KODE GUDANG warehouse_id)                  -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-bars-staggered text-indigo-400"></i>
            <span>Daftar Rak Penyimpanan (Dilengkapi Kode Gudang)</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <input type="hidden" name="tab" value="racks">
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Filter Gudang</label>
                    <select id="select_wh_filter" name="wh_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Gudang --</option>
                        @foreach($allWarehouses as $w)
                            <option value="{{ $w->id }}" {{ request('wh_filter') == $w->id ? 'selected' : '' }}>Gudang: {{ $w->name }} ({{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Cari Keyword Rak</label>
                    <input type="text" name="search_rack" value="{{ request('search_rack') }}" placeholder="🔍 Cari Kode Rak / Kode Gudang..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Filter</button>
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'racks']) }}', 'racks')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
                </div>
            </form>

            <!-- Desktop Table View with KODE GUDANG Column -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Barcode Rak</th>
                            <th class="p-3">Kode Gudang</th>
                            <th class="p-3">Nama Gudang</th>
                            <th class="p-3">Nama Rak</th>
                            <th class="p-3">Jumlah Slot BIN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($racks as $rk)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-indigo-400">{{ $rk->id }}</td>
                                <td class="p-3 font-mono font-bold text-amber-300 bg-amber-500/10 border border-amber-500/20 rounded">{{ $rk->warehouse_id }}</td>
                                <td class="p-3 text-slate-300">{{ $rk->warehouse->name ?? '-' }}</td>
                                <td class="p-3 font-medium text-white">Rak {{ $rk->rack_name }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ $rk->bins_count }} BIN</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada data rak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List -->
            <div class="space-y-2.5 md:hidden">
                @forelse($racks as $rk)
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <div class="font-mono font-bold text-indigo-400">{{ $rk->id }}</div>
                            <div class="font-bold text-white text-sm mt-0.5">Rak {{ $rk->rack_name }}</div>
                            <div class="text-[11px] text-amber-300 font-mono mt-1">Kode Gudang: {{ $rk->warehouse_id }} ({{ $rk->warehouse->name ?? '-' }})</div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 font-bold rounded text-[11px]">
                                {{ $rk->bins_count }} BIN
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada data rak.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $racks->links() }}
            </div>
        </div>

        <!-- Form Tambah Rak -->
        @canAccessModule('master', 'create')
        <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-400"></i>
                <span>Tambah Rak Baru</span>
            </h4>
            <form action="{{ route('master.rack.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Gudang Induk <span class="text-rose-400">*</span></label>
                    <select id="select_warehouse_id" name="warehouse_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($allWarehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }} (Kode: {{ $w->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kode Barcode Rak (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.rack_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: RK-D">
                        <button type="button" @click="autoRackCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Nama Rak <span class="text-rose-400">*</span></label>
                    <input type="text" name="rack_name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="D">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Rak</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>
