<header class="bg-[#004b87] border-b-2 border-[#003763] text-white sticky top-0 z-50 px-[20px] py-[24px] md:px-6 md:py-2.5 min-h-[92px] md:min-h-0 flex items-center justify-between shadow-md transition-colors duration-200">
    <!-- Left Title & Hamburger Button -->
    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Hamburger Button to Toggle Mobile Sidebar Drawer (Mobile Only, Hidden on Desktop) -->
        <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                type="button" 
                class="md:hidden w-[42px] h-[42px] text-white hover:bg-white/15 rounded-lg border border-white/30 transition focus:outline-none flex items-center justify-center cursor-pointer flex-shrink-0" 
                title="Buka Sidebar Navigasi">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Brand Logo Title: Mobile 2-line layout (Sesuai Gambar 1: SIM Akademik / Nama Univ) -->
        <div class="flex flex-col justify-center md:hidden">
            <span class="text-[12px] font-semibold text-white/80 leading-tight">WMS INDRACO</span>
            <span class="text-[15px] font-extrabold text-white tracking-tight leading-snug">SUPERADMIN PANEL</span>
        </div>

        <!-- Desktop Title & Engine Badge -->
        <div class="hidden md:flex items-center space-x-3">
            <h1 class="text-base sm:text-lg font-extrabold font-display text-white tracking-tight flex items-center gap-2">
                <span>WMS INDRACO</span>
            </h1>
            <span class="hidden lg:inline-flex items-center px-2.5 py-0.5 rounded-full text-[13px] font-bold bg-white/15 text-white border border-white/20">
                Laravel 7.x Engine
            </span>
        </div>
    </div>

    <!-- Right Controls: Profile Only on Mobile (Sesuai Gambar 1), Full Control Set on Desktop -->
    <div class="flex items-center space-x-2 sm:space-x-3">
        <!-- Theme Toggle Button (Sun / Moon) - Hidden on Mobile to match Gambar 1 -->
        <button id="theme-toggle" type="button" class="hidden md:flex p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition cursor-pointer" title="Toggle Mode Terang / Gelap">
            <i class="fa-solid fa-moon text-lg dark:hidden text-white"></i>
            <i class="fa-solid fa-sun text-lg hidden dark:block text-amber-300"></i>
        </button>

        <!-- Notification Bell Icon - Hidden on Mobile to match Gambar 1 -->
        <div class="relative hidden md:block">
            <a href="{{ route('detail.index') }}" class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition cursor-pointer relative flex items-center justify-center" title="Pemberitahuan">
                <i class="fa-solid fa-bell text-lg text-white"></i>
                <span class="absolute top-1 right-1 bg-[#ea580c] text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full leading-tight shadow-xs">
                    {{ $fullBinsCount ?? '76' }}
                </span>
            </a>
        </div>

        <!-- Apps Grid Icon - Hidden on Mobile to match Gambar 1 -->
        <a href="{{ route('master.index') }}" class="hidden md:flex p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-xl transition cursor-pointer flex items-center justify-center" title="Menu Layanan Master">
            <i class="fa-solid fa-table-cells text-lg text-white"></i>
        </a>

        <!-- User Profile Dropdown: Profile Icon + Caret Down (Sesuai Gambar 1 & Gambar 4) -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" 
                    type="button" 
                    class="flex items-center gap-1.5 p-0.5 rounded-full hover:bg-white/10 transition-all duration-200 focus:outline-none cursor-pointer group" 
                    title="Menu Akun & Profil">
                
                <!-- Avatar Circular Wrapper matching Gambar 1 & 4 -->
                <div class="w-[40px] h-[40px] rounded-full p-[2px] border-2 border-white/80 shadow-xs flex items-center justify-center flex-shrink-0 bg-[#0284c7] overflow-hidden group-hover:border-white transition">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0284c7&color=fff&size=80" 
                         alt="{{ auth()->user()->name ?? 'User' }}" 
                         class="w-full h-full object-cover rounded-full">
                </div>

                <!-- Small White Caret Down Arrow (Matching Gambar 1 & 4) -->
                <i class="fa-solid fa-caret-down text-white text-[12px] ml-0.5 transition-transform duration-200 group-hover:text-white" :class="{ 'rotate-180': userMenuOpen }"></i>
            </button>

            <!-- User Menu Dropdown Panel -->
            <div x-show="userMenuOpen" 
                 @click.outside="userMenuOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2.5 w-64 rounded-2xl bg-white dark:bg-[#00274d] border border-slate-200 dark:border-[#003661] shadow-2xl z-50 overflow-hidden py-1 divide-y divide-slate-100 dark:divide-[#003661] text-[13px] text-slate-800 dark:text-slate-100" 
                 x-cloak>
                
                <!-- Dropdown Header Info -->
                <div class="px-4 py-3 bg-sky-50/80 dark:bg-[#001e38]">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-white/80 shadow-xs flex-shrink-0 bg-[#0284c7]">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=0284c7&color=fff&size=80" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <div class="font-extrabold text-slate-900 dark:text-white text-sm truncate">{{ auth()->user()->name }}</div>
                            <div class="text-[13px] text-slate-500 dark:text-slate-400 font-mono truncate">@ {{ auth()->user()->username }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded-md text-[13px] font-bold uppercase tracking-wider bg-sky-100 dark:bg-[#003661] text-[#004b87] dark:text-sky-200 border border-sky-200 dark:border-sky-700">
                            {{ auth()->user()->role->name }}
                        </span>
                        <span class="text-[13px] font-semibold text-slate-600 dark:text-slate-300 bg-slate-200 dark:bg-[#003661]/80 px-2 py-0.5 rounded">Shift {{ auth()->user()->shift }}</span>
                    </div>
                </div>

                <!-- Link 1: Profile & Security -->
                <div class="py-1">
                    <a href="{{ route('profile.index') }}" @click="userMenuOpen = false" class="flex items-center space-x-3 px-4 py-2.5 text-slate-700 dark:text-slate-200 hover:bg-sky-50 dark:hover:bg-[#003661] hover:text-[#004b87] dark:hover:text-sky-300 transition font-medium">
                        <i class="fa-solid fa-id-card text-base w-4 text-center text-[#004b87] dark:text-sky-400"></i>
                        <span>Profil Akun & Keamanan</span>
                    </a>
                </div>

                <!-- Link 2: Logout -->
                <div class="py-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center space-x-3 px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-600/30 hover:text-rose-700 dark:hover:text-rose-300 transition font-medium cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-base w-4 text-center text-rose-500"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
