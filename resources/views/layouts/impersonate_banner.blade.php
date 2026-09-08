@if(session()->has('impersonator_id'))
    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white px-4 py-2 flex items-center justify-between shadow-md z-50 sticky top-0">
        <div class="flex items-center space-x-3 text-sm font-medium">
            <i class="fa-solid fa-user-secret text-lg animate-pulse"></i>
            <span>
                <strong>IMPERSONATION MODE:</strong> Anda saat ini sedang bertindak sebagai 
                <u class="font-bold">{{ auth()->user()->name }}</u> 
                (<span class="bg-amber-800/80 px-2 py-0.5 rounded text-xs uppercase tracking-wider">{{ auth()->user()->role->name }}</span>).
            </span>
        </div>
        <form action="{{ route('impersonate.revert') }}" method="POST">
            @csrf
            <button type="submit" class="bg-white text-slate-900 hover:bg-amber-100 font-semibold px-3 py-1 text-xs rounded-lg shadow transition flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left-long"></i>
                <span>Kembali ke Akun Admin</span>
            </button>
        </form>
    </div>
@endif
