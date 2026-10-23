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
     class="fixed inset-0 bg-slate-950/20 backdrop-blur-[2px] z-40 md:hidden"></div>

<div x-show="quickActionOpen" 
     x-cloak 
     x-transition:enter="transition ease-out duration-250 transform" 
     x-transition:enter-start="opacity-0 translate-y-6 scale-95" 
     x-transition:enter-end="opacity-100 translate-y-0 scale-100" 
     x-transition:leave="transition ease-in duration-150 transform" 
     x-transition:leave-start="opacity-100 translate-y-0 scale-100" 
     x-transition:leave-end="opacity-0 translate-y-6 scale-95" 
     class="fixed bottom-[68px] inset-x-3 sm:inset-x-6 z-50 md:hidden bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-slate-200/90 dark:border-slate-800 rounded-2xl p-3.5 shadow-xl space-y-3">
    
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

<!-- Bottom Navigation Bar (Main Thumb Dock - Clean Modern Theme) -->
<nav class="fixed bottom-0 inset-x-0 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 z-50 md:hidden px-2 py-2 shadow-[0_-4px_20px_rgba(0,0,0,0.06)]">
    <div class="grid grid-cols-5 items-center justify-items-center relative max-w-lg mx-auto gap-1">
        
        <!-- Position 1: Dashboard (Home) -->
        <a href="{{ route('dashboard') }}" 
           class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs"
           :class="(!quickActionOpen && {{ request()->routeIs('dashboard') ? 'true' : 'false' }}) ? 'bottom-nav-active bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'">
            <i class="fa-solid fa-house text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Home</span>
        </a>

        <!-- Position 2: Master Data (Master) -->
        @canAccessModule('master', 'read')
        <a href="{{ route('master.index') }}" 
           class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs"
           :class="(!quickActionOpen && {{ request()->routeIs('master.*') ? 'true' : 'false' }}) ? 'bottom-nav-active bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'">
            <i class="fa-solid fa-database text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Master</span>
        </a>
        @else
        <a href="{{ route('pallets.index') }}" 
           class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs"
           :class="(!quickActionOpen && {{ request()->routeIs('pallets.*') ? 'true' : 'false' }}) ? 'bottom-nav-active bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'">
            <i class="fa-solid fa-pallet text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Palet</span>
        </a>
        @endcanAccessModule

        <!-- Position 3: CENTER THUMB SWEET SPOT (Quick Action Hub / In & Out) -->
        <button type="button" 
                @click="quickActionOpen = !quickActionOpen" 
                class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs cursor-pointer"
                :class="quickActionOpen ? 'bottom-nav-active bg-indigo-600 text-white font-bold shadow-md shadow-indigo-500/25' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium'"
                :title="quickActionOpen ? 'Tutup Menu In & Out' : 'Buka Menu In & Out'">
            <i class="fa-solid text-sm mb-0.5 transition-transform duration-200" :class="quickActionOpen ? 'fa-xmark rotate-90 text-white' : 'fa-right-left text-indigo-500 dark:text-indigo-400'"></i>
            <span class="text-[10px] tracking-tight truncate">In & Out</span>
        </button>

        <!-- Position 4: Stock Locator (Locator) -->
        @canAccessModule('detail', 'read')
        <a href="{{ route('detail.index') }}" 
           class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs"
           :class="(!quickActionOpen && {{ request()->routeIs('detail.*') ? 'true' : 'false' }}) ? 'bottom-nav-active bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'">
            <i class="fa-solid fa-location-dot text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Locator</span>
        </a>
        @else
        <a href="{{ route('scanner.index') }}" 
           class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl transition text-center active:scale-95 text-xs"
           :class="(!quickActionOpen && {{ request()->routeIs('scanner.*') ? 'true' : 'false' }}) ? 'bottom-nav-active bg-indigo-50 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-bold' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 font-medium'">
            <i class="fa-solid fa-barcode text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Scan</span>
        </a>
        @endcanAccessModule

        <!-- Position 5: Menu Drawer (Menu) -->
        <button type="button" 
                @click="mobileSidebarOpen = true; quickActionOpen = false" 
                class="w-full flex flex-col items-center justify-center py-1 px-1 rounded-xl text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition text-center active:scale-95 cursor-pointer text-xs font-medium">
            <i class="fa-solid fa-bars text-sm mb-0.5"></i>
            <span class="text-[10px] tracking-tight">Menu</span>
        </button>

    </div>
</nav>

