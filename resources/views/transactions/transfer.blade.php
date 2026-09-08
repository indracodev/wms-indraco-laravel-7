@extends('layouts.app')

@section('title', 'Transfer BIN')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-arrows-up-down-left-right text-sky-400"></i>
                <span>Form Pindah Lokasi BIN (Transfer Palet)</span>
            </h2>
            <p class="text-sm text-slate-400">Memindahkan seluruh palet beserta box produk dari BIN asal ke BIN tujuan</p>
        </div>
    </div>

    <div class="glass-panel p-8 rounded-2xl shadow-xl">
        <form action="{{ route('transaction.transfer') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-300">Scan / Input Palet yang Dipindahkan <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" name="pallet_id" id="transfer_pallet_input" required placeholder="e.g. PLT-0001" class="w-full p-3 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <i class="fa-solid fa-pallet absolute left-3.5 top-3.5 text-slate-500"></i>
                        </div>
                        <button type="button" onclick="openCameraScanner('pallet_id')" class="px-3.5 py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Kamera</span>
                        </button>
                    </div>
                    <div class="pt-1">
                        <select onchange="document.getElementById('transfer_pallet_input').value = this.value" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-300 text-xs">
                            <option value="">-- Atau Pilih Palet dari Daftar --</option>
                            @foreach($pallets as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} (Saat ini di BIN: {{ $plt->bin_id ?? 'Transit' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-slate-300">Scan / Input Slot BIN Tujuan <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" name="target_bin_id" id="transfer_bin_input" required placeholder="e.g. B.10.1" class="w-full p-3 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <i class="fa-solid fa-barcode absolute left-3.5 top-3.5 text-slate-500"></i>
                        </div>
                        <button type="button" onclick="openCameraScanner('target_bin_id')" class="px-3.5 py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Kamera</span>
                        </button>
                    </div>
                    <div class="pt-1">
                        <select onchange="document.getElementById('transfer_bin_input').value = this.value" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-300 text-xs">
                            <option value="">-- Atau Pilih BIN Tujuan --</option>
                            @foreach($bins as $b)
                                @php $count = $b->pallets->where('is_active', true)->count(); @endphp
                                <option value="{{ $b->id }}" {{ $count >= $b->max_pallet ? 'disabled' : '' }}>
                                    BIN: {{ $b->id }} (Terisi {{ $count }}/{{ $b->max_pallet }} Palet {{ $count >= $b->max_pallet ? '- FULL ⚠️' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[11px] text-amber-400 mt-1">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Sistem memvalidasi batas max 2 palet di BIN tujuan. Jika penuh, transaksi ditolak.
                    </p>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3">
                <a href="{{ route('dashboard') }}" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold rounded-xl border border-slate-700">Batal</a>
                <button type="submit" class="px-6 py-3 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-sm rounded-xl shadow-lg shadow-sky-600/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-right-left"></i>
                    <span>Eksekusi Pemindahan BIN</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
