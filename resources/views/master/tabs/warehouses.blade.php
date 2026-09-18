<!-- ========================================================================= -->
<!-- TAB 3: WAREHOUSES (GUDANG)                                                -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-warehouse text-indigo-400"></i>
            <span>Daftar Gudang</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <input type="hidden" name="tab" value="warehouses">
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Filter Plant Induk</label>
                    <select id="select_branch_filter" name="branch_filter" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- Semua Plant --</option>
                        @foreach($allBranches as $b)
                            <option value="{{ $b->id }}" {{ request('branch_filter') == $b->id ? 'selected' : '' }}>Plant: {{ $b->name }} ({{ $b->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Cari Keyword Gudang</label>
                    <input type="text" name="search_wh" value="{{ request('search_wh') }}" placeholder="🔍 Cari Kode / Nama Gudang..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Filter</button>
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'warehouses']) }}', 'warehouses')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Gudang</th>
                            <th class="p-3">Plant</th>
                            <th class="p-3">Nama Gudang</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($warehouses as $wh)
                            @php
                                $whData = [
                                    'id' => $wh->id,
                                    'branch_id' => $wh->branch_id,
                                    'branch' => $wh->branch ? ['id' => $wh->branch->id, 'name' => $wh->branch->name] : null,
                                    'name' => $wh->name,
                                    'address' => $wh->address,
                                    'created_at' => $wh->created_at ? $wh->created_at->format('d M Y H:i') : '-',
                                    'racks' => $wh->racks ? $wh->racks->map(function($r) {
                                        return ['id' => $r->id, 'rack_name' => $r->rack_name];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-indigo-400">{{ $wh->id }}</td>
                                <td class="p-3 text-slate-300">{{ $wh->branch->name ?? $wh->branch_id }}</td>
                                <td class="p-3 font-medium text-slate-800 dark:text-white">{{ $wh->name }}</td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" 
                                            data-warehouse="{{ json_encode($whData) }}" 
                                            onclick="window.openDetailWhModal(this)" 
                                            class="p-1.5 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg transition cursor-pointer" 
                                            title="Show Detail Gudang">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-warehouse="{{ json_encode($whData) }}" 
                                                onclick="window.openEditWhModal(this)" 
                                                class="p-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg transition cursor-pointer" 
                                                title="Edit Gudang">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.warehouse.destroy', $wh->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Gudang">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-500">Tidak ada data gudang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List -->
            <div class="space-y-2.5 md:hidden">
                @forelse($warehouses as $wh)
                    @php
                        $whData = [
                            'id' => $wh->id,
                            'branch_id' => $wh->branch_id,
                            'branch' => $wh->branch ? ['id' => $wh->branch->id, 'name' => $wh->branch->name] : null,
                            'name' => $wh->name,
                            'address' => $wh->address,
                            'created_at' => $wh->created_at ? $wh->created_at->format('d M Y H:i') : '-',
                            'racks' => $wh->racks ? $wh->racks->map(function($r) {
                                return ['id' => $r->id, 'rack_name' => $r->rack_name];
                            })->values() : []
                        ];
                    @endphp
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-mono font-bold text-indigo-400">{{ $wh->id }}</div>
                                <div class="font-bold text-white text-sm mt-0.5">{{ $wh->name }}</div>
                            </div>
                            <div class="text-right text-slate-300 font-medium">
                                Plant: {{ $wh->branch->name ?? $wh->branch_id }}
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-700/60">
                            <button type="button" 
                                    data-warehouse="{{ json_encode($whData) }}" 
                                    onclick="window.openDetailWhModal(this)" 
                                    class="px-2.5 py-1.5 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>
                            @canAccessModule('master', 'update')
                                <button type="button" 
                                        data-warehouse="{{ json_encode($whData) }}" 
                                        onclick="window.openEditWhModal(this)" 
                                        class="px-2.5 py-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    <span>Edit</span>
                                </button>
                            @endcanAccessModule
                            @canAccessModule('master', 'delete')
                                <form action="{{ route('master.warehouse.destroy', $wh->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Gudang">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada data gudang.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $warehouses->links() }}
            </div>
        </div>

        <!-- Form Tambah Gudang -->
        @canAccessModule('master', 'create')
        <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-400"></i>
                <span>Tambah Gudang Baru</span>
            </h4>
            <form action="{{ route('master.warehouse.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Plant Induk <span class="text-rose-400">*</span></label>
                    <select id="select_branch_id" name="branch_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($allBranches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kode Gudang (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.wh_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: GSK.678.II">
                        <button type="button" @click="autoWhCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Nama Gudang <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Gudang Bahan Baku">
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Gudang</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>
