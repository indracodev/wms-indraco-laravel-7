<!-- Desktop Sidebar (Matching gifanya-adhiyaksa-jaya Superadmin Theme) -->
<aside class="hidden md:flex w-[270px] bg-gradient-to-b from-[#003a6c] to-[#00274d] border-r border-[#00274d] flex-col flex-shrink-0 min-h-screen shadow-xl">
    <!-- Brand Logo (Tanpa Icon Sesuai Gambar 2 Request User) -->
    <div class="px-6 py-5 border-b border-white/10 flex flex-col justify-center text-left">
        <div class="font-display font-extrabold text-lg text-white tracking-wide leading-tight">WMS INDRACO</div>
        <div class="text-[13px] text-[#ff6b6b] font-bold tracking-wider uppercase mt-1 whitespace-nowrap">SUPERADMIN PANEL</div>
    </div>

    <!-- Dynamic Menu Items -->
    <nav class="flex-1 p-3.5 space-y-1 overflow-y-auto">
        <!-- Section: Menu Utama -->
        <div class="pt-2 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
            Menu Utama
        </div>

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
            <i class="fa-solid fa-border-all text-base w-5 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
            <span>Dashboard</span>
        </a>

        <!-- Master Data -->
        @canAccessModule('master', 'read')
            <a href="{{ route('master.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('master.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-sitemap text-base w-5 text-center {{ request()->routeIs('master.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Master Data</span>
            </a>
        @endcanAccessModule

        <!-- Detail Data & Locator -->
        @canAccessModule('detail', 'read')
            <a href="{{ route('detail.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('detail.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-magnifying-glass-location text-base w-5 text-center {{ request()->routeIs('detail.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Stock Locator</span>
            </a>
        @endcanAccessModule

        <!-- Pallet Management System -->
        @canAccessModule('pallet', 'read')
            <a href="{{ route('pallets.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('pallets.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-pallet text-base w-5 text-center {{ request()->routeIs('pallets.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Manajemen Palet</span>
            </a>
        @endcanAccessModule

        <!-- Transactions Section Header -->
        <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
            Modul Transaksi
        </div>

        @canAccessModule('inbound', 'read')
            <a href="{{ route('transaction.inbound.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.inbound*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-truck-ramp-box text-base w-5 text-center {{ request()->routeIs('transaction.inbound*') ? 'text-white' : 'text-emerald-400' }}"></i>
                <span>Inbound (Barang Masuk)</span>
            </a>
        @endcanAccessModule

        @canAccessModule('outbound', 'read')
            <a href="{{ route('transaction.outbound.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.outbound*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-dolly text-base w-5 text-center {{ request()->routeIs('transaction.outbound*') ? 'text-white' : 'text-amber-400' }}"></i>
                <span>Outbound (Barang Keluar)</span>
            </a>
        @endcanAccessModule

        @canAccessModule('transfer', 'read')
            <a href="{{ route('transaction.transfer.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.transfer*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-arrows-up-down-left-right text-base w-5 text-center {{ request()->routeIs('transaction.transfer*') ? 'text-white' : 'text-sky-300' }}"></i>
                <span>Transfer & Relokasi</span>
            </a>
        @endcanAccessModule

        <!-- Barcode Scanner -->
        <a href="{{ route('scanner.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
            <i class="fa-solid fa-barcode text-base w-5 text-center {{ request()->routeIs('scanner.*') ? 'text-white' : 'text-sky-300' }}"></i>
            <span>Scanner Simulator</span>
        </a>

        <!-- System & Security Header -->
        <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
            Sistem & Otorisasi
        </div>

        @canAccessModule('users', 'read')
            <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-users text-base w-5 text-center {{ request()->routeIs('users.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Manajemen User</span>
            </a>
        @endcanAccessModule

        @canAccessModule('rbac', 'read')
            <a href="{{ route('rbac.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('rbac.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-shield-halved text-base w-5 text-center {{ request()->routeIs('rbac.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Matriks RBAC</span>
            </a>
        @endcanAccessModule

        @canAccessModule('logs', 'read')
            <a href="{{ route('logs.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('logs.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center {{ request()->routeIs('logs.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Audit Logs</span>
            </a>
        @endcanAccessModule

        <!-- Akun Header -->
        <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
            Akun
        </div>

        <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('profile.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
            <i class="fa-solid fa-user text-base w-5 text-center {{ request()->routeIs('profile.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
            <span>Profil Anda</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm text-rose-300 hover:bg-rose-500/20 hover:text-rose-200 transition font-medium cursor-pointer">
                <i class="fa-solid fa-right-from-bracket text-base w-5 text-center text-rose-400"></i>
                <span>Logout</span>
            </button>
        </form>
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
         class="fixed inset-0 bg-[#001b36]/80 backdrop-blur-sm"></div>

    <!-- Drawer Panel -->
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="mobile-sidebar-drawer relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-[#003a6c] to-[#00274d] shadow-2xl border-r border-[#00274d] z-50">
        
        <!-- Close Button -->
        <div class="absolute top-3 right-3 z-10">
            <button @click="mobileSidebarOpen = false" class="flex items-center justify-center h-9 w-9 rounded-xl text-sky-200 hover:text-white hover:bg-white/10 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Brand Logo (Tanpa Icon Sesuai Gambar 2 Request User) -->
        <div class="px-6 py-5 border-b border-white/10 flex flex-col justify-center text-left">
            <div class="font-display font-extrabold text-lg text-white tracking-wide leading-tight">WMS INDRACO</div>
            <div class="text-[13px] text-[#ff6b6b] font-bold tracking-wider uppercase mt-1 whitespace-nowrap">SUPERADMIN PANEL</div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 p-3.5 space-y-1 overflow-y-auto">
            <div class="pt-2 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
                Menu Utama
            </div>

            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-border-all text-base w-5 text-center {{ request()->routeIs('dashboard') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Dashboard</span>
            </a>

            @canAccessModule('master', 'read')
                <a href="{{ route('master.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('master.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-sitemap text-base w-5 text-center {{ request()->routeIs('master.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Master Data</span>
                </a>
            @endcanAccessModule

            @canAccessModule('detail', 'read')
                <a href="{{ route('detail.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('detail.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-magnifying-glass-location text-base w-5 text-center {{ request()->routeIs('detail.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Stock Locator</span>
                </a>
            @endcanAccessModule

            @canAccessModule('pallet', 'read')
                <a href="{{ route('pallets.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('pallets.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-pallet text-base w-5 text-center {{ request()->routeIs('pallets.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Manajemen Palet</span>
                </a>
            @endcanAccessModule

            <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
                Modul Transaksi
            </div>

            @canAccessModule('inbound', 'read')
                <a href="{{ route('transaction.inbound.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.inbound*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-truck-ramp-box text-base w-5 text-center {{ request()->routeIs('transaction.inbound*') ? 'text-white' : 'text-emerald-400' }}"></i>
                    <span>Inbound (Putaway)</span>
                </a>
            @endcanAccessModule

            @canAccessModule('outbound', 'read')
                <a href="{{ route('transaction.outbound.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.outbound*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-dolly text-base w-5 text-center {{ request()->routeIs('transaction.outbound*') ? 'text-white' : 'text-amber-400' }}"></i>
                    <span>Outbound (Picking)</span>
                </a>
            @endcanAccessModule

            @canAccessModule('transfer', 'read')
                <a href="{{ route('transaction.transfer.form') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('transaction.transfer*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-arrows-up-down-left-right text-base w-5 text-center {{ request()->routeIs('transaction.transfer*') ? 'text-white' : 'text-sky-300' }}"></i>
                    <span>Transfer BIN</span>
                </a>
            @endcanAccessModule

            <a href="{{ route('scanner.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('scanner.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-barcode text-base w-5 text-center {{ request()->routeIs('scanner.*') ? 'text-white' : 'text-sky-300' }}"></i>
                <span>Scanner Simulator</span>
            </a>

            <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
                Sistem & Otorisasi
            </div>

            @canAccessModule('users', 'read')
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-users text-base w-5 text-center {{ request()->routeIs('users.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Manajemen User</span>
                </a>
            @endcanAccessModule

            @canAccessModule('rbac', 'read')
                <a href="{{ route('rbac.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('rbac.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-shield-halved text-base w-5 text-center {{ request()->routeIs('rbac.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Matriks RBAC</span>
                </a>
            @endcanAccessModule

            @canAccessModule('logs', 'read')
                <a href="{{ route('logs.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('logs.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5 text-center {{ request()->routeIs('logs.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                    <span>Audit Logs</span>
                </a>
            @endcanAccessModule

            <div class="pt-4 pb-1.5 px-3 text-[13px] font-bold text-[#93c5fd] uppercase tracking-wider">
                Akun
            </div>

            <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('profile.*') ? 'bg-gradient-to-r from-[#0284c7] to-[#0369a1] text-white font-semibold shadow-md shadow-[#0284c7]/35' : 'text-[#dbeafe] hover:bg-white/10 hover:text-white font-medium' }}">
                <i class="fa-solid fa-user text-base w-5 text-center {{ request()->routeIs('profile.*') ? 'text-white' : 'text-[#93c5fd]' }}"></i>
                <span>Profil Anda</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left flex items-center space-x-3 px-3.5 py-2.5 rounded-lg text-sm text-rose-300 hover:bg-rose-500/20 hover:text-rose-200 transition font-medium cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket text-base w-5 text-center text-rose-400"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </div>
</div>
