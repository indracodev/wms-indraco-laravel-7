<!-- ========================================================================= -->
<!-- TAB 1: OFFICES (KANTOR PUSAT)                                             -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-building text-indigo-600 dark:text-indigo-400"></i>
            <span>Daftar Kantor Pusat & Perwakilan</span>
        </h3>
        @canAccessModule('master', 'create')
        <button type="button" 
                @click="openAddOfficeModal()" 
                class="md:hidden px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5 active:scale-95">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Tambah</span>
        </button>
        @endcanAccessModule
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Toolbar -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex items-center space-x-2 text-xs">
                <input type="hidden" name="tab" value="offices">
                <input type="text" name="search_office" value="{{ request('search_office') }}" placeholder="🔍 Cari Kode / Nama Kantor..." class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer shrink-0">Cari</button>
                <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'offices']) }}', 'offices')" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer shrink-0">Reset</button>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="p-3">Kode Kantor</th>
                            <th class="p-3">Nama Kantor</th>
                            <th class="p-3">Alamat</th>
                            <th class="p-3">Jumlah Plant</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40">
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
                                <td class="p-3 font-mono font-extrabold text-indigo-600 dark:text-indigo-400">{{ $off->id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white">{{ $off->name }}</td>
                                <td class="p-3 text-slate-500 dark:text-slate-400">{{ $off->address ?? '-' }}</td>
                                <td class="p-3 font-bold text-emerald-600 dark:text-emerald-400">{{ $off->branches_count }} Plant</td>
                                <td class="p-3 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" 
                                            data-office="{{ json_encode($offData) }}" 
                                            onclick="window.openDetailOfficeModal(this)" 
                                            class="p-1.5 bg-sky-600 hover:bg-sky-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                            title="Show Detail Kantor">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-office="{{ json_encode($offData) }}" 
                                                onclick="window.openEditOfficeModal(this)" 
                                                class="p-1.5 bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition shadow-sm cursor-pointer" 
                                                title="Edit Kantor">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-500 text-white rounded-lg transition shadow-sm cursor-pointer" title="Hapus Kantor">
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

            <!-- Mobile Swipeable Interactive Card List -->
            <div class="space-y-3 md:hidden">
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
                    
                    <div x-data="swipeableCard()" 
                         @touchstart="touchStart($event)"
                         @touchmove="touchMove($event)"
                         @touchend="touchEnd($event)"
                         @mousedown="mouseDown($event)"
                         @mousemove="mouseMove($event)"
                         @mouseup="mouseUp($event)"
                         @mouseleave="mouseUp($event)"
                         class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm select-none bg-white dark:bg-slate-800/80">
                        
                        <!-- LEFT ACTION TRAY (Revealed on Swipe Right: DELETE) -->
                        <div class="absolute inset-y-0 left-0 w-24 bg-rose-600 flex items-stretch z-0">
                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.office.destroy', $off->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus Kantor">
                                    <i class="fa-solid fa-trash-can text-lg mb-1"></i>
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase">Hapus</span>
                                </button>
                            </form>
                            @else
                            <div class="w-full flex flex-col items-center justify-center text-white/60 p-2">
                                <i class="fa-solid fa-lock text-base mb-1"></i>
                                <span class="text-[10px] font-bold">No Akses</span>
                            </div>
                            @endcanAccessModule
                        </div>

                        <!-- RIGHT ACTION TRAY (Revealed on Swipe Left: DETAIL & EDIT) -->
                        <div class="absolute inset-y-0 right-0 w-36 flex items-stretch z-0">
                            <!-- Show / Detail Button -->
                            <button type="button" 
                                    data-office="{{ json_encode($offData) }}" 
                                    onclick="window.openDetailOfficeModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white transition p-2 cursor-pointer"
                                    title="Detail Kantor">
                                <i class="fa-solid fa-eye text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Detail</span>
                            </button>

                            <!-- Edit Button -->
                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-office="{{ json_encode($offData) }}" 
                                    onclick="window.openEditOfficeModal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                                    title="Edit Kantor">
                                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
                            </button>
                            @endcanAccessModule
                        </div>

                        <!-- MAIN SWIPEABLE CARD CONTENT LAYER -->
                        <div :style="'transform: translateX(' + offset + 'px)'" 
                             class="relative z-10 bg-white dark:bg-slate-800 p-4 transition-transform duration-150 ease-out flex items-center justify-between gap-3">
                            
                            <!-- Left: Icon Avatar + Main Info -->
                            <div class="flex items-start space-x-3 min-w-0 flex-1">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-base border border-indigo-100 dark:border-indigo-800/40 shrink-0 shadow-sm">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono font-extrabold text-indigo-600 dark:text-indigo-400 text-xs">{{ $off->id }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm mt-0.5 leading-snug truncate">{{ $off->name }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>
                                        <span>{{ $off->address ?? 'Belum ada alamat' }}</span>
                                    </p>
                                </div>
                            </div>

                            <!-- Right: Plant Badge -->
                            <div class="shrink-0 text-right">
                                <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-extrabold rounded-xl text-[11px] border border-emerald-200 dark:border-emerald-500/30 shadow-sm inline-flex items-center gap-1">
                                    <i class="fa-solid fa-industry text-[10px]"></i>
                                    <span>{{ $off->branches_count }} Plant</span>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-800">Tidak ada data kantor.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $offices->links() }}
            </div>
        </div>

        <!-- Desktop Form Tambah Kantor -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-slate-100 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-200 dark:border-slate-700 h-fit space-y-3">
            <h4 class="font-bold text-slate-900 dark:text-white text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-indigo-500"></i>
                <span>Tambah Kantor Baru</span>
            </h4>
            <form action="{{ route('master.office.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Kode Kantor (ID) <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.office_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="misal: KTR-JKT">
                        <button type="button" @click="autoOfficeCode()" class="px-3 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-300 dark:border-slate-600 cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Nama Kantor <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Kantor Perwakilan Surabaya">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-400 mb-1 font-medium">Alamat</label>
                    <textarea name="address" rows="2" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <span class="btn-text">Simpan Kantor</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>

    <!-- FLOATING ACTION BUTTON (FAB) FOR MOBILE -->
    @canAccessModule('master', 'create')
    <div class="fixed bottom-20 right-4 z-40 md:hidden">
        <button type="button" 
                @click="openAddOfficeModal()" 
                class="w-13 h-13 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-bold shadow-xl shadow-indigo-600/40 border border-indigo-400/40 flex items-center justify-center transition transform active:scale-95 cursor-pointer"
                title="Tambah Kantor Baru">
            <i class="fa-solid fa-plus text-xl"></i>
        </button>
    </div>
    @endcanAccessModule
</div>
