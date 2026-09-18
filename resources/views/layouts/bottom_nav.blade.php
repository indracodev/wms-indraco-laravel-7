<!-- ========================================================================= -->
<!-- ERGONOMIC AMBIDEXTROUS MOBILE BOTTOM NAVIGATION SYSTEM                     -->
<!-- Designed for Left-Hand and Right-Hand One-Handed Thumb Operation          -->
<!-- ========================================================================= -->

<!-- Quick Action Thumb Hub Overlay / Sheet (Pops up above bottom bar) -->
<div x-show="quickActionOpen" 
     x-cloak 
     @click="quickActionOpen = false" 
     x-transition:enter="transition-opacity ease-out duration-200" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="transition-opacity ease-in duration-150" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 md:hidden"></div>

<div x-show="quickActionOpen" 
     x-cloak 
     x-transition:enter="transition ease-out duration-250 transform" 
     x-transition:enter-start="opacity-0 translate-y-10 scale-95" 
     x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
     x-transition:leave="transition ease-in duration-150 transform" 
     x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
     x-transition:leave-end="opacity-0 translate-y-10 scale-95" 
     class="fixed bottom-[114px] inset-x-3 sm:inset-x-6 z-50 md:hidden bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-4 shadow-2xl space-y-3.5">
    
    <!-- Sheet Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
        <div class="flex items-center space-x-2">
            <span class="w-7 h-7 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-400 flex items-center justify-center text-xs font-bold shadow-sm">
                <i class="fa-solid fa-bolt"></i>
            </span>
            <div>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white">Aksi Cepat Transaksi & Scan</h4>
                <p class="text-[10px] text-slate-500 dark:text-slate-400">Jangkauan jempol tangan kiri / kanan</p>
            </div>
        </div>
        <button type="button" @click="quickActionOpen = false" class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Thumb Action Grid -->
    <div class="grid grid-cols-4 gap-2 text-center text-xs">
        <!-- 1. Scan Camera -->
        <button type="button" 
                onclick="openCameraScanner();" 
                @click="quickActionOpen = false"
                class="flex flex-col items-center p-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-indigo-200 dark:border-slate-700 transition active:scale-95 cursor-pointer">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-base shadow-md mb-1.5">
                <i class="fa-solid fa-camera"></i>
            </div>
            <span class="font-bold text-slate-900 dark:text-white text-[11px]">Kamera</span>
        </button>

        <!-- 2. Inbound Putaway -->
        @canAccessModule('inbound', 'read')
        <a href="{{ route('transaction.inbound.form') }}" 
           class="flex flex-col items-center p-2.5 rounded-2xl bg-emerald-50 hover:bg-emerald-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-emerald-200 dark:border-slate-700 transition active:scale-95">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shadow-md mb-1.5">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <span class="font-bold text-slate-900 dark:text-white text-[11px]">Inbound</span>
        </a>
        @endcanAccessModule

        <!-- 3. Outbound Picking -->
        @canAccessModule('outbound', 'read')
        <a href="{{ route('transaction.outbound.form') }}" 
           class="flex flex-col items-center p-2.5 rounded-2xl bg-amber-50 hover:bg-amber-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-amber-200 dark:border-slate-700 transition active:scale-95">
            <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center text-base shadow-md mb-1.5">
                <i class="fa-solid fa-dolly"></i>
            </div>
            <span class="font-bold text-slate-900 dark:text-white text-[11px]">Outbound</span>
        </a>
        @endcanAccessModule

        <!-- 4. Transfer BIN -->
        @canAccessModule('transfer', 'read')
        <a href="{{ route('transaction.transfer.form') }}" 
           class="flex flex-col items-center p-2.5 rounded-2xl bg-sky-50 hover:bg-sky-100 dark:bg-slate-800/80 dark:hover:bg-slate-800 border border-sky-200 dark:border-slate-700 transition active:scale-95">
            <div class="w-10 h-10 rounded-xl bg-sky-600 text-white flex items-center justify-center text-base shadow-md mb-1.5">
                <i class="fa-solid fa-arrows-up-down-left-right"></i>
            </div>
            <span class="font-bold text-slate-900 dark:text-white text-[11px]">Transfer</span>
        </a>
        @endcanAccessModule
    </div>

    <!-- Secondary Quick Action: Scanner Simulator Bar -->
    <a href="{{ route('scanner.index') }}" class="w-full py-2.5 px-3 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/50 border border-purple-200 dark:border-purple-800 rounded-xl flex items-center justify-between text-xs text-purple-900 dark:text-purple-300 font-bold transition">
        <span class="flex items-center space-x-2">
            <i class="fa-solid fa-barcode text-purple-600 dark:text-purple-400"></i>
            <span>Buka Scanner Simulator Cepat</span>
        </span>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
    </a>
</div>

<!-- Bottom Navigation Bar (Main Thumb Dock) -->
<nav class="fixed bottom-0 inset-x-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 z-50 md:hidden px-2 pt-1 pb-2 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] dark:shadow-[0_-4px_25px_rgba(0,0,0,0.4)]">
    <div class="grid grid-cols-5 items-end justify-items-center relative max-w-lg mx-auto">
        
        <!-- Position 1: Dashboard (Left Thumb Primary) -->
        <a href="{{ route('dashboard') }}" 
           class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 {{ request()->routeIs('dashboard') ? 'bg-indigo-100 dark:bg-indigo-500/20 shadow-sm' : '' }}">
                <i class="fa-solid fa-chart-pie text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight">Home</span>
        </a>

        <!-- Position 2: Master Data (Left Thumb Secondary) -->
        @canAccessModule('master', 'read')
        <a href="{{ route('master.index') }}" 
           class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 {{ request()->routeIs('master.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 {{ request()->routeIs('master.*') ? 'bg-indigo-100 dark:bg-indigo-500/20 shadow-sm' : '' }}">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight">Master</span>
        </a>
        @else
        <a href="{{ route('pallets.index') }}" 
           class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 {{ request()->routeIs('pallets.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 {{ request()->routeIs('pallets.*') ? 'bg-indigo-100 dark:bg-indigo-500/20 shadow-sm' : '' }}">
                <i class="fa-solid fa-pallet text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight">Palet</span>
        </a>
        @endcanAccessModule

        <!-- Position 3: CENTER THUMB SWEET SPOT (Quick Action Hub) -->
        <button type="button" 
                @click="quickActionOpen = !quickActionOpen" 
                class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 text-purple-600 dark:text-purple-400 cursor-pointer"
                :title="quickActionOpen ? 'Tutup Aksi Cepat' : 'Buka Aksi Cepat Jempol'">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 shadow-sm transition-colors duration-200"
                 :class="quickActionOpen ? 'bg-purple-600 text-white shadow-purple-500/40' : 'bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400'">
                <i class="fa-solid text-base transition-transform duration-200" 
                   :class="quickActionOpen ? 'fa-xmark rotate-90 text-white' : 'fa-bolt text-purple-600 dark:text-purple-400'"></i>
            </div>
            <span class="text-[10px] font-bold tracking-tight text-purple-700 dark:text-purple-300">Aksi Cepat</span>
        </button>

        <!-- Position 4: Stock Locator (Right Thumb Secondary) -->
        @canAccessModule('detail', 'read')
        <a href="{{ route('detail.index') }}" 
           class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 {{ request()->routeIs('detail.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 {{ request()->routeIs('detail.*') ? 'bg-indigo-100 dark:bg-indigo-500/20 shadow-sm' : '' }}">
                <i class="fa-solid fa-magnifying-glass-location text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight">Locator</span>
        </a>
        @else
        <a href="{{ route('scanner.index') }}" 
           class="w-full flex flex-col items-center py-1 rounded-2xl transition active:scale-95 {{ request()->routeIs('scanner.*') ? 'text-indigo-600 dark:text-indigo-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5 {{ request()->routeIs('scanner.*') ? 'bg-indigo-100 dark:bg-indigo-500/20 shadow-sm' : '' }}">
                <i class="fa-solid fa-barcode text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight">Scan</span>
        </a>
        @endcanAccessModule

        <!-- Position 5: Menu Drawer (Right Thumb Primary) -->
        <button type="button" 
                @click="mobileSidebarOpen = true; quickActionOpen = false" 
                class="w-full flex flex-col items-center py-1 rounded-2xl text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition active:scale-95 cursor-pointer">
            <div class="w-9 h-7 flex items-center justify-center rounded-xl mb-0.5">
                <i class="fa-solid fa-bars text-base"></i>
            </div>
            <span class="text-[10px] tracking-tight font-medium">Menu</span>
        </button>

    </div>
</nav>

