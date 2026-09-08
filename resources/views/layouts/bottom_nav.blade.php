<nav class="fixed bottom-0 inset-x-0 bg-slate-900/95 backdrop-blur-lg border-t border-slate-800/80 z-50 md:hidden px-2 py-1.5 flex items-center justify-around shadow-2xl">
    <!-- Home / Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('dashboard') ? 'text-indigo-400 font-bold bg-indigo-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-chart-pie text-lg mb-0.5"></i>
        <span>Home</span>
    </a>

    <!-- Stock Locator -->
    @canAccessModule('detail', 'read')
    <a href="{{ route('detail.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('detail.*') ? 'text-indigo-400 font-bold bg-indigo-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-magnifying-glass-location text-lg mb-0.5"></i>
        <span>Locator</span>
    </a>
    @endcanAccessModule

    <!-- Inbound Putaway -->
    @canAccessModule('inbound', 'read')
    <a href="{{ route('transaction.inbound.form') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.inbound*') ? 'text-emerald-400 font-bold bg-emerald-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-truck-ramp-box text-lg mb-0.5"></i>
        <span>Inbound</span>
    </a>
    @endcanAccessModule

    <!-- Outbound Picking -->
    @canAccessModule('outbound', 'read')
    <a href="{{ route('transaction.outbound.form') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.outbound*') ? 'text-amber-400 font-bold bg-amber-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-dolly text-lg mb-0.5"></i>
        <span>Outbound</span>
    </a>
    @endcanAccessModule

    <!-- Transfer BIN -->
    @canAccessModule('transfer', 'read')
    <a href="{{ route('transaction.transfer.form') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.transfer*') ? 'text-sky-400 font-bold bg-sky-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-arrows-up-down-left-right text-lg mb-0.5"></i>
        <span>Transfer</span>
    </a>
    @endcanAccessModule

    <!-- Scanner Simulator -->
    <a href="{{ route('scanner.index') }}" class="flex flex-col items-center py-1 px-2.5 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('scanner.*') ? 'text-purple-400 font-bold bg-purple-500/10' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-barcode text-lg mb-0.5"></i>
        <span>Scan</span>
    </a>
</nav>
