<header class="bg-slate-800/80 border-b border-slate-700/60 sticky top-0 z-50 backdrop-blur-md px-4 sm:px-6 py-3 flex items-center justify-between">
    <!-- Left Title & Hamburger Button -->
    <div class="flex items-center space-x-3">
        <!-- Hamburger Button to Toggle Mobile Sidebar Drawer -->
        <button @click="mobileSidebarOpen = !mobileSidebarOpen" 
                type="button" 
                class="p-2 text-slate-300 hover:text-white hover:bg-slate-700/60 rounded-xl transition focus:outline-none flex items-center justify-center cursor-pointer" 
                title="Buka Sidebar Navigasi">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>

        <!-- Brand Logo Title -->
        <h1 class="text-lg sm:text-xl font-bold font-display text-white tracking-tight flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-indigo-400"></i>
            <span>WMS INDRACO</span>
        </h1>
        <span class="hidden lg:inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
            Laravel 7.x Engine
        </span>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3 sm:space-x-4">
        <!-- Theme Toggle Button (Sun / Moon) -->
        <button id="theme-toggle" type="button" class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-slate-700/50 transition cursor-pointer" title="Toggle Mode Terang / Gelap">
            <i class="fa-solid fa-moon text-lg dark:hidden"></i>
            <i class="fa-solid fa-sun text-lg hidden dark:block text-amber-400"></i>
        </button>

        <!-- Interactive User Avatar Dropdown (Profile & Logout) -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" 
                    type="button" 
                    class="flex items-center space-x-2.5 p-1 rounded-xl hover:bg-slate-700/50 transition focus:outline-none cursor-pointer" 
                    title="Menu Akun & Profil">
                <!-- User Avatar Circle -->
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-bold text-white shadow-md ring-2 ring-indigo-400/40 text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <!-- User Info Label (Desktop) -->
                <div class="hidden sm:block text-left">
                    <div class="text-sm font-semibold text-white leading-tight flex items-center gap-1">
                        <span>{{ auth()->user()->name ?? 'User' }}</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }"></i>
                    </div>
                    <div class="text-[11px] text-indigo-300 font-medium">
                        {{ auth()->user()->role->name ?? 'Role' }} • Shift {{ auth()->user()->shift ?? 1 }}
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
                 class="absolute right-0 mt-2 w-60 rounded-2xl bg-slate-900 border border-slate-700/80 shadow-2xl z-50 overflow-hidden py-1 divide-y divide-slate-800 text-xs" 
                 x-cloak>
                
                <!-- Dropdown Header Info -->
                <div class="px-4 py-3 bg-slate-800/60">
                    <div class="font-bold text-white text-sm">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">@ {{ auth()->user()->username }}</div>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            {{ auth()->user()->role->name }}
                        </span>
                        <span class="text-[10px] text-slate-400">Shift {{ auth()->user()->shift }}</span>
                    </div>
                </div>

                <!-- Link 1: Profile & Security -->
                <div class="py-1">
                    <a href="{{ route('profile.index') }}" @click="userMenuOpen = false" class="flex items-center space-x-3 px-4 py-2.5 text-slate-200 hover:bg-indigo-600 hover:text-white transition font-medium">
                        <i class="fa-solid fa-id-card text-base w-4 text-center text-indigo-400"></i>
                        <span>Profil Akun & Keamanan</span>
                    </a>
                </div>

                <!-- Link 2: Logout -->
                <div class="py-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center space-x-3 px-4 py-2.5 text-rose-400 hover:bg-rose-600 hover:text-white transition font-medium cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-base w-4 text-center"></i>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
