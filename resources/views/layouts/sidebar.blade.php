<!-- Desktop Sidebar -->
<aside class="hidden md:flex w-64 bg-slate-900 border-r border-slate-800 flex-col flex-shrink-0 min-h-screen">
    <!-- Brand Logo -->
    <div class="p-6 border-b border-slate-800 flex items-center space-x-3">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg glow-effect">
            <i class="fa-solid fa-cubes text-xl"></i>
        </div>
        <div>
            <div class="font-display font-bold text-lg text-white tracking-wide leading-none">WMS INDRACO</div>
            <div class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase mt-1">Warehouse Management</div>
        </div>
    </div>

    <!-- Dynamic Menu Items -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <i class="fa-solid fa-chart-pie text-base w-5 text-center"></i>
            <span>Dashboard</span>
        </a>

        <!-- Master Data -->
        @canAccessModule('master', 'read')
            <a href="{{ route('master.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('master.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-sitemap text-base w-5 text-center"></i>
                <span>Master Data</span>
            </a>
        @endcanAccessModule

        <!-- Detail Data & Locator -->
        @canAccessModule('detail', 'read')
            <a href="{{ route('detail.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('detail.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-magnifying-glass-location text-base w-5 text-center"></i>
                <span>Stock Locator</span>
            </a>
        @endcanAccessModule

        <!-- Pallet Management System -->
        @canAccessModule('pallet', 'read')
            <a href="{{ route('pallets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('pallets.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-pallet text-base w-5 text-center text-indigo-400"></i>
                <span>Manajemen Palet</span>
            </a>
        @endcanAccessModule

        <!-- Transactions Section Header -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            Modul Transaksi
        </div>

        @canAccessModule('inbound', 'read')
            <a href="{{ route('transaction.inbound.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.inbound*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-truck-ramp-box text-base w-5 text-center text-emerald-400"></i>
                <span>Inbound (Barang Masuk)</span>
            </a>
        @endcanAccessModule

        @canAccessModule('outbound', 'read')
            <a href="{{ route('transaction.outbound.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.outbound*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-dolly text-base w-5 text-center text-amber-400"></i>
                <span>Outbound (Barang Keluar)</span>
            </a>
        @endcanAccessModule

        @canAccessModule('transfer', 'read')
            <a href="{{ route('transaction.transfer.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.transfer*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-arrows-up-down-left-right text-base w-5 text-center text-sky-400"></i>
                <span>Transfer & Relokasi</span>
            </a>
        @endcanAccessModule

        <!-- Barcode Scanner -->
        <a href="{{ route('scanner.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <i class="fa-solid fa-barcode text-base w-5 text-center text-purple-400"></i>
            <span>Scanner Simulator</span>
        </a>

        <!-- System & Security Header -->
        <div class="pt-4 pb-1 px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
            Sistem & Otorisasi
        </div>

        @canAccessModule('users', 'read')
            <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-users text-base w-5 text-center"></i>
                <span>Manajemen User</span>
            </a>
        @endcanAccessModule

        @canAccessModule('rbac', 'read')
            <a href="{{ route('rbac.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('rbac.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-shield-halved text-base w-5 text-center"></i>
                <span>Matriks RBAC</span>
            </a>
        @endcanAccessModule

        @canAccessModule('logs', 'read')
            <a href="{{ route('logs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('logs.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center"></i>
                <span>Audit Logs</span>
            </a>
        @endcanAccessModule

        <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
            <i class="fa-solid fa-id-card text-base w-5 text-center"></i>
            <span>Profil Akun</span>
        </a>
    </nav>
</aside>

<!-- Mobile Slide-Over Sidebar Drawer -->
<div x-show="mobileSidebarOpen" class="fixed inset-0 z-50 flex" x-cloak>
    <!-- Backdrop Overlay -->
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false" 
         class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <!-- Drawer Panel -->
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="relative flex-1 flex flex-col max-w-xs w-full bg-slate-900 shadow-2xl border-r border-slate-800 z-50">
        
        <!-- Close Button -->
        <div class="absolute top-3 right-3 z-10">
            <button @click="mobileSidebarOpen = false" class="flex items-center justify-center h-9 w-9 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Brand Logo -->
        <div class="p-6 border-b border-slate-800 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg glow-effect">
                <i class="fa-solid fa-cubes text-xl"></i>
            </div>
            <div>
                <div class="font-display font-bold text-lg text-white tracking-wide leading-none">WMS INDRACO</div>
                <div class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase mt-1">Warehouse Management</div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-chart-pie text-base w-5 text-center"></i>
                <span>Dashboard</span>
            </a>

            @canAccessModule('master', 'read')
                <a href="{{ route('master.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('master.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-sitemap text-base w-5 text-center"></i>
                    <span>Master Data</span>
                </a>
            @endcanAccessModule

            @canAccessModule('detail', 'read')
                <a href="{{ route('detail.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('detail.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-magnifying-glass-location text-base w-5 text-center"></i>
                    <span>Stock Locator</span>
                </a>
            @endcanAccessModule

            @canAccessModule('pallet', 'read')
                <a href="{{ route('pallets.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('pallets.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-pallet text-base w-5 text-center text-indigo-400"></i>
                    <span>Manajemen Palet</span>
                </a>
            @endcanAccessModule

            <div class="pt-4 pb-1 px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                Modul Transaksi
            </div>

            @canAccessModule('inbound', 'read')
                <a href="{{ route('transaction.inbound.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.inbound*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-truck-ramp-box text-base w-5 text-center text-emerald-400"></i>
                    <span>Inbound (Putaway)</span>
                </a>
            @endcanAccessModule

            @canAccessModule('outbound', 'read')
                <a href="{{ route('transaction.outbound.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.outbound*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-dolly text-base w-5 text-center text-amber-400"></i>
                    <span>Outbound (Picking)</span>
                </a>
            @endcanAccessModule

            @canAccessModule('transfer', 'read')
                <a href="{{ route('transaction.transfer.form') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('transaction.transfer*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-arrows-up-down-left-right text-base w-5 text-center text-sky-400"></i>
                    <span>Transfer BIN</span>
                </a>
            @endcanAccessModule

            <a href="{{ route('scanner.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-barcode text-base w-5 text-center text-purple-400"></i>
                <span>Scanner Simulator</span>
            </a>

            <div class="pt-4 pb-1 px-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                Sistem & Otorisasi
            </div>

            @canAccessModule('users', 'read')
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-users text-base w-5 text-center"></i>
                    <span>Manajemen User</span>
                </a>
            @endcanAccessModule

            @canAccessModule('rbac', 'read')
                <a href="{{ route('rbac.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('rbac.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-shield-halved text-base w-5 text-center"></i>
                    <span>Matriks RBAC</span>
                </a>
            @endcanAccessModule

            @canAccessModule('logs', 'read')
                <a href="{{ route('logs.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('logs.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center"></i>
                    <span>Audit Logs</span>
                </a>
            @endcanAccessModule

            <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <i class="fa-solid fa-id-card text-base w-5 text-center"></i>
                <span>Profil Akun</span>
            </a>
        </nav>
    </div>
</div>
