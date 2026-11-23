<!-- ========================================================================= -->
<!-- TAB 1: OFFICES (KANTOR PUSAT)                                             -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar Kantor Pusat & Perwakilan</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar (Gambar 2 Style) -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="w-full">
                <input type="hidden" name="tab" value="offices">
                <div class="search-input-group">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'offices']) }}', 'offices')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_office" value="{{ request('search_office') }}" placeholder="Cari Kode / Nama Kantor..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari Kantor">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Kantor</th>
                            <th class="p-3">Nama Kantor</th>
                            <th class="p-3">Alamat</th>
                            <th class="p-3 whitespace-nowrap text-center">Jumlah Plant</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
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
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">{{ $off->id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white break-words">{{ $off->name }}</td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 leading-relaxed break-words">{{ $off->address ?? '-' }}</td>
                                <td class="p-3 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold rounded-lg border border-emerald-200 dark:border-emerald-800 text-[13px]">
                                        {{ $off->branches_count }} Plant
                                    </span>
                                </td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-office="{{ json_encode($offData) }}" 
                                            onclick="window.openDetailOfficeModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Show Detail Kantor">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-office="{{ json_encode($offData) }}" 
                                                onclick="window.openEditOfficeModal(this)" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit Kantor">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="Hapus Kantor">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500 text-[13px]">Tidak ada data kantor.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List (No Cutoff, Proper & Proportional) -->
            <div class="space-y-3.5 md:hidden">
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
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <!-- Card Header: ID & Plant Badge -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-[#004b87] dark:text-sky-400 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $off->id }}
                            </span>
                            <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $off->branches_count }} Plant
                            </span>
                        </div>

                        <!-- Card Body: Full Name & Address (No Cutoff) -->
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">
                                {{ $off->name }}
                            </h4>
                            <div class="text-[13px] text-slate-500 dark:text-slate-400 flex items-start gap-1.5 pt-0.5 leading-relaxed break-words">
                                <i class="fa-solid fa-location-dot text-slate-400 mt-1 flex-shrink-0 text-xs"></i>
                                <span>{{ $off->address ?: 'Belum ada alamat' }}</span>
                            </div>
                        </div>

                        <!-- Card Footer Action Toolbar -->
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-office="{{ json_encode($offData) }}" 
                                    onclick="window.openDetailOfficeModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Detail Kantor">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-office="{{ json_encode($offData) }}" 
                                    onclick="window.openEditOfficeModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Edit Kantor">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="Hapus Kantor">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data kantor.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $offices->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah Kantor -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-indigo-600 dark:text-indigo-400"></i>
                <span>Tambah Kantor Baru</span>
            </h4>
            <form action="{{ route('master.office.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Kantor (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.office_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" placeholder="misal: KTR-JKT">
                        <button type="button" @click="autoOfficeCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Nama Kantor <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="Kantor Perwakilan Surabaya">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Alamat</label>
                    <textarea name="address" rows="2" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 text-[13px]">
                    <span class="btn-text">Simpan Kantor</span>
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
            @click="openAddOfficeModal()" 
            class="w-12 h-12 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold shadow-xl shadow-indigo-600/40 border-2 border-white dark:border-slate-800 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
            title="Tambah Kantor Baru">
        <i class="fa-solid fa-plus text-lg"></i>
    </button>
</div>
@endcanAccessModule
