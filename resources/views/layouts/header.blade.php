<header class="bg-white/90 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50 backdrop-blur-md px-4 sm:px-6 py-2.5 flex items-center justify-between transition-colors duration-200 shadow-xs">
    <!-- Left Title & Hamburger Button -->
    <div class="flex items-center space-x-3">
        <!-- Hamburger Button to Toggle Mobile Sidebar Drawer -->
        <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                type="button" 
                class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-300 dark:hover:text-white dark:hover:bg-slate-800/80 rounded-xl transition focus:outline-none flex items-center justify-center cursor-pointer" 
                title="Buka Sidebar Navigasi">
            <i class="fa-solid fa-bars text-lg sm:text-xl"></i>
        </button>

        <!-- Brand Logo Title -->
        <h1 class="text-base sm:text-lg font-extrabold font-display text-slate-900 dark:text-white tracking-tight">
            <span>WMS INDRACO</span>
        </h1>
        <span class="hidden lg:inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:border-indigo-500/30">
            Laravel 7.x Engine
        </span>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Theme Toggle Button (Sun / Moon) -->
        <button id="theme-toggle" type="button" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/80 rounded-xl transition cursor-pointer" title="Toggle Mode Terang / Gelap">
            <i class="fa-solid fa-moon text-lg dark:hidden text-slate-600"></i>
            <i class="fa-solid fa-sun text-lg hidden dark:block text-amber-400"></i>
        </button>

        <!-- Interactive User Avatar Dropdown (Profile & Logout Pill) -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" 
                    type="button" 
                    class="flex items-center space-x-3 p-1.5 pr-3 rounded-full sm:rounded-2xl bg-slate-100 hover:bg-slate-200/80 dark:bg-slate-800/90 dark:hover:bg-slate-700/90 border border-slate-200/80 dark:border-slate-700/70 transition-all duration-200 focus:outline-none cursor-pointer group shadow-xs hover:shadow-sm" 
                    title="Menu Akun & Profil">
                
                <!-- User Avatar Circle -->
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 flex items-center justify-center font-extrabold text-white shadow-sm ring-2 ring-indigo-500/30 text-xs tracking-wider flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>

                <!-- User Info Label (Desktop) -->
                <div class="hidden sm:block text-left pr-0.5">
                    <div class="text-xs sm:text-sm font-extrabold text-slate-900 dark:text-white leading-tight flex items-center gap-1.5">
                        <span>{{ auth()->user()->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }"></i>
                    </div>
                    <div class="text-[11px] font-medium text-slate-600 dark:text-slate-300 flex items-center gap-1.5 mt-0.5">
                        <span class="font-semibold text-indigo-700 dark:text-indigo-300">{{ auth()->user()->role->name ?? 'Role' }}</span>
                        <span class="text-slate-300 dark:text-slate-600">•</span>
                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-slate-200 dark:bg-slate-700/80 text-slate-700 dark:text-slate-200 border border-slate-300/80 dark:border-slate-600">Shift {{ auth()->user()->shift ?? 1 }}</span>
                    </div>
                </div>
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
                 class="absolute right-0 mt-2.5 w-64 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xl z-50 overflow-hidden py-1 divide-y divide-slate-100 dark:divide-slate-800 text-xs" 
                 x-cloak>
                
                <!-- Dropdown Header Info -->
                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/60">
                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">@ {{ auth()->user()->username }}</div>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">
                            {{ auth()->user()->role->name }}
                        </span>
                        <span class="text-[10px] font-semibold text-slate-600 dark:text-slate-300 bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded">Shift {{ auth()->user()->shift }}</span>
                    </div>
                </div>

                <!-- Link 1: Profile & Security -->
                <div class="py-1">
                    <a href="{{ route('profile.index') }}" @click="userMenuOpen = false" class="flex items-center space-x-3 px-4 py-2.5 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-600/30 hover:text-indigo-600 dark:hover:text-indigo-300 transition font-medium">
                        <i class="fa-solid fa-id-card text-base w-4 text-center text-indigo-600 dark:text-indigo-400"></i>
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
