@extends('layouts.app')

@section('title', 'Inbound Putaway & Box Registration')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Tom Select Custom Styling for WMS Dark & Light Theme */
    .ts-wrapper .ts-control {
        background-color: #0f172a !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        color: #ffffff !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        box-shadow: none !important;
    }
    .ts-wrapper.single .ts-control:after {
        border-color: #94a3b8 transparent transparent transparent !important;
    }
    .ts-dropdown {
        background-color: #0f172a !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        color: #ffffff !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
        z-index: 9999 !important;
    }
    .ts-dropdown .option {
        padding: 0.6rem 1rem !important;
        color: #cbd5e1 !important;
        font-size: 0.875rem !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
    .ts-dropdown .option.selected {
        background-color: #1e293b !important;
        color: #60a5fa !important;
        font-weight: bold !important;
    }
    .ts-control input {
        color: #ffffff !important;
    }

    /* Light mode styling overrides */
    html.light .ts-wrapper .ts-control {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light .ts-control input {
        color: #0f172a !important;
    }
    html.light .ts-dropdown {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }
    html.light .ts-dropdown .option {
        color: #334155 !important;
    }
    html.light .ts-dropdown .option.active, html.light .ts-dropdown .option:hover {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="space-y-6" x-data="inboundWorkflow()" x-init="initSelectSearchAll()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box text-emerald-400"></i>
                <span>Form Inbound Putaway & Registrasi Stok</span>
            </h2>
            <p class="text-sm text-slate-400">Pilih alur penempatan Palet ke BIN atau pendataan Box ke Palet dengan Select Search & Scan Barcode</p>
        </div>
    </div>

    <!-- Workflow Tab Switcher -->
    <div class="flex items-center space-x-2 border-b border-slate-700/80 pb-2">
        <button type="button" 
                @click="switchMode('pallet_bin')" 
                :class="activeMode === 'pallet_bin' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-5 py-3 rounded-xl text-xs sm:text-sm transition flex items-center space-x-2 cursor-pointer">
            <i class="fa-solid fa-pallet text-base"></i>
            <span>1. Penempatan Palet ke BIN</span>
        </button>

        <button type="button" 
                @click="switchMode('box_pallet')" 
                :class="activeMode === 'box_pallet' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-5 py-3 rounded-xl text-xs sm:text-sm transition flex items-center space-x-2 cursor-pointer">
            <i class="fa-solid fa-box text-base"></i>
            <span>2. Pendataan Box ke Palet</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- MODE 1: PENEMPATAN PALET KE BIN (Pallet to BIN Flow)                      -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'pallet_bin'" class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-indigo-400"></i>
                <span>Alur 1: Scan Barcode BIN & Scan Palet</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">Lakukan scan/cari barcode BIN target, dilanjutkan scan barcode Palet. Konfirmasikan sebelum disimpan.</p>
        </div>

        <form id="palletBinForm" action="{{ route('transaction.inbound.pallet_to_bin') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Step 1: Scan / Search Barcode BIN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Step 1: Scan / Input Barcode BIN <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="bin_id" 
                                   x-model="palletBin.bin_id" 
                                   required 
                                   placeholder="e.g. B.10.2"
                                   class="w-full p-3.5 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <i class="fa-solid fa-barcode absolute left-3.5 top-4 text-slate-500"></i>
                        </div>
                        <button type="button" 
                                @click="openCameraScanner(val => palletBin.bin_id = val)" 
                                class="px-3.5 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" 
                                title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Kamera</span>
                        </button>
                    </div>

                    <!-- Select Search BIN List -->
                    <div class="pt-1">
                        <label class="block text-[11px] text-slate-400 mb-1">Cari / Select BIN Target:</label>
                        <select id="select_bin_id" @change="palletBin.bin_id = $event.target.value" class="w-full">
                            <option value="">🔍 Cari Kode BIN / Lokasi...</option>
                            @foreach($bins as $b)
                                @php $count = $b->pallets->where('is_active', true)->count(); @endphp
                                <option value="{{ $b->id }}" {{ $count >= $b->max_pallet ? 'disabled' : '' }}>
                                    BIN: {{ $b->id }} (Terisi {{ $count }}/{{ $b->max_pallet }} Palet {{ $count >= $b->max_pallet ? '- FULL ⚠️' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Step 2: Scan Barcode Palet -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Step 2: Scan / Input Barcode Palet <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="pallet_id" 
                                   x-model="palletBin.pallet_id" 
                                   required 
                                   placeholder="e.g. PLT-001"
                                   class="w-full p-3.5 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <i class="fa-solid fa-pallet absolute left-3.5 top-4 text-slate-500"></i>
                        </div>
                        <button type="button" 
                                @click="openCameraScanner(val => palletBin.pallet_id = val)" 
                                class="px-3.5 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" 
                                title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Kamera</span>
                        </button>
                    </div>

                    <!-- Jenis Palet Select Search -->
                    <div class="pt-1">
                        <label class="block text-[11px] text-slate-400 mb-1">Cari / Select Jenis Palet:</label>
                        <select id="select_pallet_type_id" name="pallet_type_id" x-model="palletBin.pallet_type_id" required class="w-full">
                            @foreach($palletTypes as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit & Confirmation Trigger -->
            <div class="pt-4 border-t border-slate-700/50 flex items-center justify-end space-x-3">
                <button type="button" 
                        @click="resetPalletBinForm()" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700">
                    Batal / Reset
                </button>

                <button type="button" 
                        @click="triggerPalletBinConfirm()" 
                        :disabled="!palletBin.bin_id || !palletBin.pallet_id"
                        :class="(!palletBin.bin_id || !palletBin.pallet_id) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500 shadow-lg shadow-emerald-600/30'"
                        class="px-6 py-3 bg-emerald-600 text-white font-semibold text-xs sm:text-sm rounded-xl transition flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Lanjutkan & Konfirmasikan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODE 2: PENDATAAN BOX KE PALET (Box to Pallet Flow)                       -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'box_pallet'" class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-amber-400"></i>
                <span>Alur 2: Scan Barcode Palet & Scan Barcode Box (Select Search Multi-UOM)</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">Lakukan pencarian/scan barcode Palet, pilih Produk SKU via Select Search, dan isi rincian UOM.</p>
        </div>

        <form id="boxPalletForm" action="{{ route('transaction.inbound.box_to_pallet') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Step 1: Scan / Select Search Barcode Palet -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Step 1: Scan / Input Barcode Palet Target <span class="text-rose-400">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="pallet_id" 
                                   x-model="boxPallet.pallet_id" 
                                   required 
                                   placeholder="e.g. PLT-001"
                                   class="w-full p-3.5 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <i class="fa-solid fa-pallet absolute left-3.5 top-4 text-slate-500"></i>
                        </div>
                        <button type="button" 
                                @click="openCameraScanner(val => boxPallet.pallet_id = val)" 
                                class="px-3.5 py-3.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" 
                                title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Kamera</span>
                        </button>
                    </div>

                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Cari / Select Palet Aktif:</label>
                        <select id="select_active_pallet" @change="boxPallet.pallet_id = $event.target.value" class="w-full">
                            <option value="">🔍 Cari ID Palet / Lokasi BIN...</option>
                            @foreach($pallets as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} (BIN: {{ $plt->bin_id ?? 'TRANSIT' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Detail Box & Produk -->
            <div class="space-y-4 pt-3 border-t border-slate-700/40">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                    Step 2: Scan / Input Detail Box & Produk Multi-UOM <span class="text-rose-400">*</span>
                </label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1">Barcode Box ID (Optional Auto-Generate)</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="box_id" x-model="boxPallet.box_id" placeholder="BOX-xxxx (Opsional)" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                            <button type="button" 
                                    @click="openCameraScanner(val => boxPallet.box_id = val)" 
                                    class="px-3 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow flex items-center space-x-1 text-xs font-semibold cursor-pointer" 
                                    title="Scan lewat Kamera HP/Laptop">
                                <i class="fa-solid fa-camera text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Select Search SKU Produk -->
                    <div>
                        <label class="block text-slate-400 mb-1">Cari & Select Produk SKU <span class="text-rose-400">*</span></label>
                        <select id="select_product_id" name="product_id" x-model="boxPallet.product_id" @change="onProductChange()" required class="w-full">
                            <option value="">🔍 Ketik SKU / Nama Produk / Brand...</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->id }}) - {{ $p->brand ?? 'No Brand' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1">Jumlah Qty Transaksi <span class="text-rose-400">*</span></label>
                        <input type="number" step="any" name="qty" x-model.number="boxPallet.qty" required min="0.01" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-base font-bold">
                    </div>

                    <!-- Select Search UOM Unit -->
                    <div>
                        <label class="block text-slate-400 mb-1">Cari & Select Satuan Transaksi (UOM Input) <span class="text-rose-400">*</span></label>
                        <select id="select_unit" name="unit" x-model="boxPallet.unit" required class="w-full">
                        </select>
                    </div>

                    <!-- Live Calculation Badge -->
                    <div class="md:col-span-2 p-3.5 bg-sky-500/10 border border-sky-500/30 rounded-xl flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-sky-300">
                            <i class="fa-solid fa-calculator text-base"></i>
                            <span class="font-medium text-xs">Setara Satuan Terkecil (UOM Base):</span>
                        </div>
                        <div class="text-base font-extrabold text-sky-400">
                            <span x-text="calculatedBaseQty"></span> <span x-text="currentBaseUnit"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1">Tanggal Expired <span class="text-rose-400">*</span></label>
                        <input type="date" name="expired_date" x-model="boxPallet.expired_date" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1">Kode Mesin Produksi <span class="text-rose-400">*</span></label>
                        <input type="text" name="machine_code" x-model="boxPallet.machine_code" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono">
                    </div>

                    <!-- Select Search Shift -->
                    <div class="md:col-span-2">
                        <label class="block text-slate-400 mb-1">Shift Kerja Operator</label>
                        <select id="select_shift" name="shift" x-model="boxPallet.shift" required class="w-full">
                            <option value="1">Shift 1 (Pagi)</option>
                            <option value="2">Shift 2 (Siang)</option>
                            <option value="3">Shift 3 (Malam)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit & Confirmation Trigger -->
            <div class="pt-4 border-t border-slate-700/50 flex items-center justify-end space-x-3">
                <button type="button" 
                        @click="resetBoxPalletForm()" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700">
                    Batal / Reset
                </button>

                <button type="button" 
                        @click="triggerBoxPalletConfirm()" 
                        :disabled="!boxPallet.pallet_id || !boxPallet.product_id"
                        :class="(!boxPallet.pallet_id || !boxPallet.product_id) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-500 shadow-lg shadow-amber-600/30'"
                        class="px-6 py-3 bg-amber-600 text-white font-semibold text-xs sm:text-sm rounded-xl transition flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Lanjutkan & Konfirmasikan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP KONFIRMASI (YA / BATAL)                                       -->
    <!-- ========================================================================= -->

    <!-- Modal Konfirmasi 1: Palet ke BIN -->
    <div x-show="showPalletBinModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4 border border-indigo-500/40">
            <div class="flex items-center space-x-3 text-indigo-400">
                <i class="fa-solid fa-circle-question text-2xl"></i>
                <h3 class="text-lg font-bold text-white">Konfirmasi Penempatan Palet</h3>
            </div>

            <div class="space-y-3 text-xs text-slate-300 bg-slate-900/80 p-4 rounded-xl border border-slate-800">
                <p class="text-sm font-medium text-white">
                    Apakah Anda yakin ingin memasukkan <strong class="text-indigo-400 font-mono" x-text="palletBin.pallet_id"></strong> ke dalam <strong class="text-amber-400 font-mono" x-text="palletBin.bin_id"></strong>?
                </p>
                <div class="pt-2 border-t border-slate-800 space-y-1 text-[11px] text-slate-400">
                    <div>Target BIN: <span class="font-mono text-slate-200 font-bold" x-text="palletBin.bin_id"></span></div>
                    <div>Palet Barcode: <span class="font-mono text-slate-200 font-bold" x-text="palletBin.pallet_id"></span></div>
                    <div>Jenis Palet ID: <span class="font-mono text-slate-200" x-text="palletBin.pallet_type_id"></span></div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="showPalletBinModal = false" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    Tidak / Batal
                </button>

                <button type="button" 
                        @click="submitPalletBinForm()" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Iya, Masukkan Palet</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi 2: Box ke Palet -->
    <div x-show="showBoxPalletModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4 border border-amber-500/40">
            <div class="flex items-center space-x-3 text-amber-400">
                <i class="fa-solid fa-circle-question text-2xl"></i>
                <h3 class="text-lg font-bold text-white">Konfirmasi Pendataan Box</h3>
            </div>

            <div class="space-y-3 text-xs text-slate-300 bg-slate-900/80 p-4 rounded-xl border border-slate-800">
                <p class="text-sm font-medium text-white">
                    Apakah Anda yakin ingin mendaftarkan <strong class="text-amber-400 font-mono" x-text="boxPallet.box_id || 'Box Baru'"></strong> ke dalam Palet <strong class="text-indigo-400 font-mono" x-text="boxPallet.pallet_id"></strong>?
                </p>
                <div class="pt-2 border-t border-slate-800 space-y-1 text-[11px] text-slate-400">
                    <div>Palet Target: <span class="font-mono text-slate-200 font-bold" x-text="boxPallet.pallet_id"></span></div>
                    <div>SKU Produk: <span class="font-bold text-slate-200" x-text="boxPallet.product_id"></span></div>
                    <div>Jumlah Input: <span class="font-bold text-amber-400" x-text="boxPallet.qty + ' ' + boxPallet.unit"></span></div>
                    <div>Stok Base Terkecil: <span class="font-bold text-sky-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span></div>
                    <div>Tanggal Expired: <span class="text-slate-200" x-text="boxPallet.expired_date"></span></div>
                    <div>Kode Mesin: <span class="font-mono text-slate-200" x-text="boxPallet.machine_code"></span></div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="showBoxPalletModal = false" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    Tidak / Batal
                </button>

                <button type="button" 
                        @click="submitBoxPalletForm()" 
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Iya, Daftarkan Box</span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const productsMap = {
        @foreach($products as $p)
            "{{ $p->id }}": {
                name: "{{ addslashes($p->name) }}",
                uom_inventory: "{{ $p->uom_inventory }}",
                conversions: {
                    "{{ $p->uom_inventory }}": { uom_convert: "{{ $p->uom_inventory }}", qty_convert: 1, uom_base: "{{ $p->uom_inventory }}" },
                    @foreach($p->conversions as $c)
                        "{{ $c->uom_convert }}": { uom_convert: "{{ $c->uom_convert }}", qty_convert: {{ $c->qty_convert }}, uom_base: "{{ $c->uom_base ?: $p->uom_inventory }}" },
                    @endforeach
                }
            },
        @endforeach
    };

    let tsInstances = {};

    function initTomSelect(id, onChangeCallback) {
        const el = document.getElementById(id);
        if (!el) return null;

        if (tsInstances[id]) {
            tsInstances[id].destroy();
        }

        const ts = new TomSelect(el, {
            create: false,
            maxOptions: 500,
            placeholder: '🔍 Ketik untuk mencari...',
            onChange: function(val) {
                el.value = val;
                el.dispatchEvent(new Event('change', { bubbles: true }));
                if (onChangeCallback) onChangeCallback(val);
            }
        });

        tsInstances[id] = ts;
        return ts;
    }

    function inboundWorkflow() {
        const initialProdId = '{{ $products->first()->id ?? "" }}';
        const initialProd = productsMap[initialProdId] || { uom_inventory: 'Box', conversions: {} };

        return {
            activeMode: 'pallet_bin',
            showPalletBinModal: false,
            showBoxPalletModal: false,

            palletBin: {
                bin_id: '',
                pallet_id: 'PLT-' + Math.floor(1000 + Math.random() * 9000),
                pallet_type_id: '{{ $palletTypes->first()->id ?? "wooden" }}'
            },

            boxPallet: {
                pallet_id: '',
                box_id: '',
                product_id: initialProdId,
                qty: 1,
                unit: initialProd.uom_inventory,
                expired_date: '{{ date("Y-m-d", strtotime("+1 year")) }}',
                machine_code: 'MC-01',
                shift: '{{ auth()->user()->shift ?? 1 }}'
            },

            availableConversions: initialProd.conversions,
            currentBaseUnit: initialProd.uom_inventory,

            get calculatedBaseQty() {
                const conv = this.availableConversions[this.boxPallet.unit];
                const factor = conv ? parseFloat(conv.qty_convert) : 1;
                return (parseFloat(this.boxPallet.qty) || 0) * factor;
            },

            initSelectSearchAll() {
                this.$nextTick(() => {
                    initTomSelect('select_bin_id', val => this.palletBin.bin_id = val);
                    initTomSelect('select_pallet_type_id', val => this.palletBin.pallet_type_id = val);
                    initTomSelect('select_active_pallet', val => this.boxPallet.pallet_id = val);
                    initTomSelect('select_product_id', val => {
                        this.boxPallet.product_id = val;
                        this.onProductChange();
                    });
                    initTomSelect('select_unit', val => this.boxPallet.unit = val);
                    initTomSelect('select_shift', val => this.boxPallet.shift = val);
                });
            },

            switchMode(mode) {
                this.activeMode = mode;
                this.$nextTick(() => {
                    this.initSelectSearchAll();
                });
            },

            onProductChange() {
                const prod = productsMap[this.boxPallet.product_id];
                if (prod) {
                    this.availableConversions = prod.conversions;
                    this.currentBaseUnit = prod.uom_inventory;
                    const uomKeys = Object.keys(this.availableConversions);
                    if (!this.availableConversions[this.boxPallet.unit]) {
                        this.boxPallet.unit = uomKeys[0] || prod.uom_inventory;
                    }

                    // Re-sync unit select options in TomSelect
                    this.$nextTick(() => {
                        const unitEl = document.getElementById('select_unit');
                        if (unitEl) {
                            if (tsInstances['select_unit']) {
                                tsInstances['select_unit'].destroy();
                                delete tsInstances['select_unit'];
                            }
                            unitEl.innerHTML = '';
                            for (let uomName in this.availableConversions) {
                                const c = this.availableConversions[uomName];
                                const opt = document.createElement('option');
                                opt.value = uomName;
                                opt.textContent = uomName + ' (x' + c.qty_convert + ' ' + c.uom_base + ')';
                                if (uomName === this.boxPallet.unit) opt.selected = true;
                                unitEl.appendChild(opt);
                            }
                            unitEl.value = this.boxPallet.unit;
                            initTomSelect('select_unit', val => {
                                this.boxPallet.unit = val;
                            });
                        }
                    });
                }
            },

            triggerPalletBinConfirm() {
                if (!this.palletBin.bin_id || !this.palletBin.pallet_id) {
                    alert('Mohon isi Barcode BIN dan Barcode Palet terlebih dahulu!');
                    return;
                }
                this.showPalletBinModal = true;
            },

            resetPalletBinForm() {
                this.palletBin.bin_id = '';
                this.palletBin.pallet_id = 'PLT-' + Math.floor(1000 + Math.random() * 9000);
                if (tsInstances['select_bin_id']) tsInstances['select_bin_id'].clear();
                this.showPalletBinModal = false;
            },

            submitPalletBinForm() {
                document.getElementById('palletBinForm').submit();
            },

            triggerBoxPalletConfirm() {
                if (!this.boxPallet.pallet_id || !this.boxPallet.product_id) {
                    alert('Mohon isi Barcode Palet Target dan pilih SKU Produk!');
                    return;
                }
                if (!this.boxPallet.box_id) {
                    this.boxPallet.box_id = 'BOX-' + Math.floor(100000 + Math.random() * 900000);
                }
                this.showBoxPalletModal = true;
            },

            resetBoxPalletForm() {
                this.boxPallet.pallet_id = '';
                this.boxPallet.box_id = '';
                if (tsInstances['select_active_pallet']) tsInstances['select_active_pallet'].clear();
                this.showBoxPalletModal = false;
            },

            submitBoxPalletForm() {
                document.getElementById('boxPalletForm').submit();
            }
        }
    }
</script>
@endsection
@endsection
