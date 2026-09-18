<!-- ========================================================================= -->
<!-- TAB 1: OFFICES (KANTOR PUSAT)                                             -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-building text-indigo-400"></i>
            <span>Daftar Kantor Pusat & Perwakilan</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex items-center space-x-2 text-xs">
                <input type="hidden" name="tab" value="offices">
                <input type="text" name="search_office" value="{{ request('search_office') }}" placeholder="🔍 Cari Kode / Nama Kantor..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Cari</button>
                <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'offices']) }}', 'offices')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Kantor</th>
                            <th class="p-3">Nama Kantor</th>
                            <th class="p-3">Alamat</th>
                            <th class="p-3">Jumlah Plant</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($offices as $off)
                            @php
                                $offData = [
                                    'id' => $off->id,
                                    'name' => $off->name,
                                    'address' => $off->address,
                                    'created_at' => $off->created_at ? $off->created_at->format('d M Y H:i') : '-',
                                    'branches' => $off->branches ? $off->branches->map(function($b) {
                                        return ['id' => $b->id, 'name' => $b->name, 'city' => $b->city];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-indigo-400">{{ $off->id }}</td>
                                <td class="p-3 font-medium text-slate-800 dark:text-white">{{ $off->name }}</td>
                                <td class="p-3 text-slate-400">{{ $off->address ?? '-' }}</td>
                                <td class="p-3 font-bold text-emerald-400">{{ $off->branches_count }} Plant</td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" 
                                            data-office="{{ json_encode($offData) }}" 
                                            onclick="window.openDetailOfficeModal(this)" 
                                            class="p-1.5 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg transition cursor-pointer" 
                                            title="Show Detail Kantor">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-office="{{ json_encode($offData) }}" 
                                                onclick="window.openEditOfficeModal(this)" 
                                                class="p-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg transition cursor-pointer" 
                                                title="Edit Kantor">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Kantor">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada data kantor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List -->
            <div class="space-y-2.5 md:hidden">
                @forelse($offices as $off)
                    @php
                        $offData = [
                            'id' => $off->id,
                            'name' => $off->name,
                            'address' => $off->address,
                            'created_at' => $off->created_at ? $off->created_at->format('d M Y H:i') : '-',
                            'branches' => $off->branches ? $off->branches->map(function($b) {
                                return ['id' => $b->id, 'name' => $b->name, 'city' => $b->city];
                            })->values() : []
                        ];
                    @endphp
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-mono font-bold text-indigo-400">{{ $off->id }}</div>
                                <div class="font-bold text-slate-800 dark:text-white text-sm mt-0.5">{{ $off->name }}</div>
                                <div class="text-[11px] text-slate-400 mt-1">{{ $off->address ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 font-bold rounded text-[11px]">
                                    {{ $off->branches_count }} Plant
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-700/60">
                            <button type="button" 
                                    data-office="{{ json_encode($offData) }}" 
                                    onclick="window.openDetailOfficeModal(this)" 
                                    class="px-2.5 py-1.5 bg-sky-600/80 hover:bg-sky-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition cursor-pointer">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>
                            @canAccessModule('master', 'update')
                                <button type="button" 
                                        data-office="{{ json_encode($offData) }}" 
                                        onclick="window.openEditOfficeModal(this)" 
                                        class="px-2.5 py-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg font-semibold flex items-center space-x-1 transition cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    <span>Edit</span>
                                </button>
                            @endcanAccessModule
                            @canAccessModule('master', 'delete')
                                <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="Hapus Kantor">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada data kantor.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $offices->links() }}
            </div>
        </div>

        <!-- Form Tambah Kantor -->
        @canAccessModule('master', 'create')
        <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-400"></i>
                <span>Tambah Kantor Baru</span>
            </h4>
            <form action="{{ route('master.office.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kode Kantor (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.office_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: KTR-JKT">
                        <button type="button" @click="autoOfficeCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Nama Kantor <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Kantor Perwakilan Surabaya">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Alamat</label>
                    <textarea name="address" rows="2" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Kantor</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>
