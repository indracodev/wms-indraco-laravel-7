<!-- ========================================================================= -->
<!-- TAB 6: UOMS (MASTER UOM INVENTORY - BASE UNIT)                            -->
<!-- ========================================================================= -->
<div class="glass-panel p-6 rounded-2xl space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-scale-balanced text-indigo-400"></i>
            <span>Daftar Master Satuan UOM Inventory (Base Unit)</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex items-center space-x-2 text-xs">
                <input type="hidden" name="tab" value="uoms">
                <input type="text" name="search_uom" value="{{ request('search_uom') }}" placeholder="🔍 Cari Kode Satuan / Nama / Deskripsi UOM..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl transition cursor-pointer">Cari</button>
                <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'uoms']) }}', 'uoms')" class="px-3 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">Reset</button>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-800 rounded-xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-800/90 text-slate-300 uppercase font-semibold">
                        <tr>
                            <th class="p-3">Kode Satuan (ID)</th>
                            <th class="p-3">Nama Satuan UOM</th>
                            <th class="p-3">Keterangan / Fungsi</th>
                            <th class="p-3 text-center">Penggunaan SKU</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($uoms as $u)
                            @php 
                                $inUseCount = $u->products_count ?? 0;
                                $isSuperAdmin = auth()->user() && auth()->user()->role_id === 'super_admin';
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-bold text-indigo-400">{{ $u->id }}</td>
                                <td class="p-3 font-bold text-slate-800 dark:text-white">{{ $u->name }}</td>
                                <td class="p-3 text-slate-400">{{ $u->description ?? '-' }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold {{ $inUseCount > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-700/40 text-slate-400 border border-slate-600/30' }}">
                                        {{ $inUseCount }} Produk
                                    </span>
                                </td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}')" 
                                                class="p-1.5 bg-amber-600/80 hover:bg-amber-500 text-white rounded-lg transition cursor-pointer" 
                                                title="Edit Satuan UOM">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule

                                    @canAccessModule('master', 'delete')
                                        @if($isSuperAdmin || $inUseCount === 0)
                                            <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-rose-600/80 hover:bg-rose-500 text-white rounded-lg transition cursor-pointer" title="{{ $inUseCount > 0 ? 'Hapus UOM (Otorisasi Super Admin - Terkait ' . $inUseCount . ' produk)' : 'Hapus UOM' }}">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] text-slate-500 inline-flex items-center gap-1 font-medium cursor-not-allowed" title="Satuan terkunci karena masih dipakai di Katalog Produk">
                                                <i class="fa-solid fa-lock text-slate-500"></i> Terkunci
                                            </span>
                                        @endif
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500">Tidak ada data satuan UOM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List -->
            <div class="space-y-2.5 md:hidden">
                @forelse($uoms as $u)
                    @php $inUseCount = $u->products_count ?? 0; @endphp
                    <div class="bg-slate-800/80 border border-slate-700/70 p-3.5 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-indigo-400">{{ $u->id }}</span>
                                <span class="font-bold text-slate-800 dark:text-white text-sm">{{ $u->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-1">{{ $u->description ?? '-' }}</div>
                        </div>
                        <div class="text-right space-y-1">
                            <div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $inUseCount > 0 ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-700/50 text-slate-400' }}">
                                    {{ $inUseCount }} Produk
                                </span>
                            </div>
                            <div class="flex items-center justify-end space-x-2 pt-1">
                                @canAccessModule('master', 'update')
                                    <button type="button" 
                                            @click="openEditUomModal('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->description ?? '') }}')" 
                                            class="p-1 text-amber-400 hover:text-amber-300 cursor-pointer" 
                                            title="Edit Satuan UOM">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                @endcanAccessModule
                                @canAccessModule('master', 'delete')
                                    @if($isSuperAdmin || $inUseCount === 0)
                                        <form action="{{ route('master.uom.destroy', $u->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-rose-400 hover:text-rose-300 cursor-pointer" title="Hapus UOM">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-slate-500 inline-flex items-center gap-1 font-medium">
                                            <i class="fa-solid fa-lock text-slate-500"></i> Terkunci
                                        </span>
                                    @endif
                                @endcanAccessModule
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-500 text-xs">Tidak ada data satuan UOM.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $uoms->links() }}
            </div>
        </div>

        <!-- Form Tambah UOM -->
        @canAccessModule('master', 'create')
        <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-400"></i>
                <span>Tambah UOM Baru</span>
            </h4>
            <form action="{{ route('master.uom.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Kode Satuan (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.uom_id" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: PCS / BOX">
                        <button type="button" @click="autoUomCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Nama Satuan UOM <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: Box / Pieces / Kilogram">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-medium">Keterangan / Fungsi</label>
                    <textarea name="description" rows="2" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Catatan fungsi atau standar kemasan..."></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Satuan UOM</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>
