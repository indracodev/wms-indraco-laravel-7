<!-- ========================================================================= -->
<!-- TAB 2: PLANTS (Dahulunya Cabang)                                          -->
<!-- ========================================================================= -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">
            <span>Daftar Plant Operasional</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter & Search Bar (Identical to Detail Data & Pallets) -->
            <form method="GET" action="{{ route('master.index') }}" onsubmit="MasterDataApp.submitFilter(event, this)" class="flex flex-col sm:flex-row items-center gap-2.5 text-[13px]">
                <input type="hidden" name="tab" value="branches">
                <div class="search-input-group flex-1 w-full sm:w-auto">
                    <button type="button" onclick="MasterDataApp.loadTab('{{ route('master.index', ['tab' => 'branches']) }}', 'branches')" class="btn btn-sm btn-warning search-btn-reset" title="Reset Pencarian">
                        «
                    </button>
                    <input type="text" name="search_branch" value="{{ request('search_branch') }}" placeholder="Cari Kode / Nama Plant..." class="form-control search-input-field">
                    <button type="submit" class="btn btn-sm btn-primary search-btn-submit" title="Cari Plant">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
                <div class="w-full sm:w-56">
                    <select id="select_office_filter" name="office_filter" onchange="this.form.dispatchEvent(new Event('submit'))" class="w-full h-[36px] px-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-md text-slate-900 dark:text-white shadow-xs text-[13px]">
                        <option value="">-- Semua Kantor Induk --</option>
                        @foreach($allOffices as $o)
                            <option value="{{ $o->id }}" {{ request('office_filter') == $o->id ? 'selected' : '' }}>{{ $o->name }} ({{ $o->id }})</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto border border-slate-200 dark:border-slate-800 rounded-xl shadow-sm">
                <table class="w-full text-left text-[13px]">
                    <thead class="bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-300 uppercase font-bold border-b border-slate-200 dark:border-slate-700 text-[13px]">
                        <tr>
                            <th class="p-3 whitespace-nowrap">Kode Plant</th>
                            <th class="p-3">Kantor Induk</th>
                            <th class="p-3">Nama Plant</th>
                            <th class="p-3 whitespace-nowrap">Kota / Wilayah</th>
                            <th class="p-3 text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40 text-[13px]">
                        @forelse($branches as $br)
                            @php
                                $brData = [
                                    'id' => $br->id,
                                    'office_id' => $br->office_id,
                                    'office' => $br->office ? ['id' => $br->office->id, 'name' => $br->office->name] : null,
                                    'name' => $br->name,
                                    'city' => $br->city,
                                    'created_at' => $br->created_at ? $br->created_at->format('d M Y H:i') : '-',
                                    'warehouses' => $br->warehouses ? $br->warehouses->map(function($w) {
                                        return ['id' => $w->id, 'name' => $w->name];
                                    })->values() : []
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3 font-mono font-extrabold text-[#004b87] dark:text-sky-400 whitespace-nowrap">{{ $br->id }}</td>
                                <td class="p-3 text-slate-700 dark:text-slate-300 font-medium break-words">{{ $br->office->name ?? $br->office_id }}</td>
                                <td class="p-3 font-bold text-slate-900 dark:text-white break-words">{{ $br->name }}</td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">{{ $br->city ?? '-' }}</td>
                                <td class="p-3 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" 
                                            data-branch="{{ json_encode($brData) }}" 
                                            onclick="window.openDetailBranchModal(this)" 
                                            class="p-2 bg-[#00c0ef] hover:bg-[#00a7d0] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                            title="Show Detail Plant">
                                        <i class="fa-solid fa-eye text-[13px]"></i>
                                    </button>
                                    @canAccessModule('master', 'update')
                                        <button type="button" 
                                                data-branch="{{ json_encode($brData) }}" 
                                                onclick="window.openEditBranchModal(this)" 
                                                class="p-2 bg-[#f39c12] hover:bg-[#e08e0b] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" 
                                                title="Edit Plant">
                                            <i class="fa-solid fa-pen-to-square text-[13px]"></i>
                                        </button>
                                    @endcanAccessModule
                                    @canAccessModule('master', 'delete')
                                        <form action="{{ route('master.branch.destroy', $br->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-[#dd4b39] hover:bg-[#c23321] text-white rounded-lg transition shadow-sm cursor-pointer inline-flex items-center justify-center text-[13px]" title="Hapus Plant">
                                                <i class="fa-solid fa-trash text-[13px]"></i>
                                            </button>
                                        </form>
                                    @endcanAccessModule
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-slate-500 text-[13px]">Tidak ada data Plant.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Flat Card List (No Cutoff, Proper & Proportional) -->
            <div class="space-y-3.5 md:hidden">
                @forelse($branches as $br)
                    @php
                        $brData = [
                            'id' => $br->id,
                            'office_id' => $br->office_id,
                            'office' => $br->office ? ['id' => $br->office->id, 'name' => $br->office->name] : null,
                            'name' => $br->name,
                            'city' => $br->city,
                            'created_at' => $br->created_at ? $br->created_at->format('d M Y H:i') : '-',
                            'warehouses' => $br->warehouses ? $br->warehouses->map(function($w) {
                                return ['id' => $w->id, 'name' => $w->name];
                            })->values() : []
                        ];
                    @endphp
                    
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-4 space-y-3 shadow-xs text-[13px]">
                        <!-- Header: Badge ID, City & Warehouse Count -->
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/60 pb-2.5">
                            <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-[#004b87] dark:text-sky-400 font-mono font-extrabold rounded-lg text-[13px] whitespace-nowrap">
                                {{ $br->id }}
                            </span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2.5 py-0.5 bg-slate-100 dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 font-bold rounded-lg text-[13px] border border-slate-200 dark:border-slate-600 whitespace-nowrap">
                                    {{ $br->city ?? '-' }}
                                </span>
                                <span class="px-2.5 py-0.5 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 font-bold rounded-lg text-[13px] whitespace-nowrap">
                                    {{ $br->warehouses ? $br->warehouses->count() : 0 }} Gudang
                                </span>
                            </div>
                        </div>

                        <!-- Plant Name -->
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm leading-snug break-words">{{ $br->name }}</h4>
                        </div>

                        <!-- Office Info Box (Full, No Truncate) -->
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700/60 rounded-xl flex items-center justify-between text-[13px]">
                            <div class="flex items-center space-x-1.5 break-words">
                                <span class="text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">Kantor Induk:</span>
                                <span class="font-bold text-slate-900 dark:text-white break-words">{{ $br->office->name ?? $br->office_id }}</span>
                            </div>
                        </div>

                        <!-- Card Footer Action Toolbar -->
                        <div class="pt-2.5 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-2">
                            <button type="button" 
                                    data-branch="{{ json_encode($brData) }}" 
                                    onclick="window.openDetailBranchModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Detail Plant">
                                <i class="fa-solid fa-eye text-xs"></i>
                                <span>Detail</span>
                            </button>

                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-branch="{{ json_encode($brData) }}" 
                                    onclick="window.openEditBranchModal(this)" 
                                    class="flex-1 py-2 px-3 bg-[#f39c12] hover:bg-[#e08e0b] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer"
                                    title="Edit Plant">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </button>
                            @endcanAccessModule

                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.branch.destroy', $br->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="inline flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-2 px-3 bg-[#dd4b39] hover:bg-[#c23321] text-white font-bold rounded-xl transition text-[13px] flex items-center justify-center gap-1.5 shadow-xs cursor-pointer" title="Hapus Plant">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                            @endcanAccessModule
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-[13px] bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-800">Tidak ada data Plant.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $branches->links() }}
            </div>
        </div>

        @canAccessModule('master', 'create')
            <!-- Form Tambah Plant (Desktop View Only, Hidden on Mobile) -->
            <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <h4 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-indigo-600 dark:text-indigo-400"></i>
                <span>Tambah Plant Baru</span>
            </h4>
            <form action="{{ route('master.branch.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kantor Induk <span class="text-rose-500">*</span></label>
                    <select id="select_office_id" name="office_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white shadow-sm">
                        @foreach($allOffices as $o)
                            <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->id }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold">Kode Plant (ID) <span class="text-rose-500">*</span></label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="id" x-model="forms.branch_id" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" placeholder="misal: PLANT-GSK">
                        <button type="button" @click="autoBranchCode()" class="px-3.5 py-2.5 bg-[#00c0ef] hover:bg-[#00a7d0] text-white font-mono font-bold text-[13px] rounded-xl transition shadow-sm cursor-pointer">Auto</button>
                    </div>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Nama Plant <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="Plant Gresik Factory">
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 mb-1 font-semibold text-[13px]">Kota / Lokasi</label>
                    <input type="text" name="city" class="w-full p-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-[#3850c9] focus:outline-none shadow-sm text-[13px]" placeholder="Gresik">
                </div>
                <button type="submit" class="w-full py-2.5 bg-[#3850c9] hover:bg-[#2b3ea6] font-bold text-white rounded-xl shadow-md transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95 text-[13px]">
                    <span class="btn-text">Simpan Plant</span>
                </button>
            </form>
        </div>
        @endcanAccessModule
    </div>
</div>

@canAccessModule('master', 'create')
    <!-- Ergonomic Thumb Floating Action Button (Mobile Only: Tambah Plant Baru) -->
    <!-- Positioned outside .glass-panel so position: fixed binds directly to viewport -->
    <div class="fixed bottom-[125px] right-4 z-40 lg:hidden">
        <button type="button" 
                @click="openAddBranchModal()" 
                onclick="window.openAddBranchModal()"
                class="w-12 h-12 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-2xl shadow-indigo-600/50 flex items-center justify-center text-xl active:scale-90 transition-transform cursor-pointer border-2 border-white dark:border-slate-800"
                title="Tambah Plant Baru">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
@endcanAccessModule

<script>
    if (typeof window.swipeableCard !== 'function') {
        window.swipeableCard = function() {
            return {
                startX: 0,
                startY: 0,
                offset: 0,
                isDragging: false,
                isScrolling: false,
                maxLeftSwipe: -144,
                maxRightSwipe: 96,
                touchStart(e) {
                    this.startX = e.touches[0].clientX;
                    this.startY = e.touches[0].clientY;
                    this.isDragging = true;
                    this.isScrolling = false;
                },
                touchMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.touches[0].clientX - this.startX;
                    const diffY = e.touches[0].clientY - this.startY;
                    if (!this.isScrolling && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 8) {
                        this.isScrolling = true;
                        this.isDragging = false;
                        return;
                    }
                    if (this.isScrolling) return;
                    if (Math.abs(diffX) > 10 && e.cancelable) {
                        e.preventDefault();
                    }
                    if (diffX > 0) {
                        this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                    } else {
                        this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                    }
                },
                touchEnd(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                mouseDown(e) {
                    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                    this.startX = e.clientX;
                    this.isDragging = true;
                },
                mouseMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.clientX - this.startX;
                    if (Math.abs(diffX) > 5) {
                        if (diffX > 0) {
                            this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                        } else {
                            this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                        }
                    }
                },
                mouseUp(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                closeTray() {
                    this.offset = 0;
                }
            };
        };
    }
</script>
