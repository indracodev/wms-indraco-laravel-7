@extends('layouts.app')

@section('title', 'Outbound Picking V2')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Tom Select Custom Styling */
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
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
    .ts-dropdown .option.selected {
        background-color: #1e293b !important;
        color: #fbbf24 !important;
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
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="space-y-6" x-data="outboundV2Workflow()" x-init="initSelectSearchAll()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">
                Pengeluaran Barang (Outbound V2)
            </h2>
            <p class="text-sm text-slate-400 mt-1">Alur: Scan Rak &rarr; Pilih Palet & Box (FEFO) &rarr; Tentukan Status (Keluar Ekspedisi / Keluar In Transit)</p>
        </div>
    </div>

    <!-- MAIN FORM CONTAINER (Ketentuan 4) -->
    <div class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <form id="outboundV2Form" action="{{ route('transaction.outbound') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="outbound_type" x-model="form.outbound_type">

            <!-- STEP 1: SCAN RAK / LOKASI FISIK (Ketentuan 4) -->
            <div class="p-5 bg-slate-900/70 border border-slate-700/80 rounded-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-xs font-bold">1</span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider">Langkah 1: Scan / Input Barcode Rak atau BIN</h3>
                    </div>
                    <span class="text-xs text-amber-400" x-show="rackItems.length > 0" x-text="rackItems.length + ' Item / Batch Ditemukan'"></span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Scan Barcode Rak / BIN <span class="text-rose-400">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   name="rack_id" 
                                   id="rack_id_input"
                                   x-model="form.rack_id" 
                                   @change="loadRackInventory()"
                                   required 
                                   placeholder="e.g. B.10.1 atau RK-B"
                                   class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <button type="button" 
                                    @click="openCameraScanner(val => { form.rack_id = val; loadRackInventory(); })" 
                                    class="px-3.5 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer whitespace-nowrap">
                                Kamera
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Atau Pilih BIN dari Daftar:
                        </label>
                        <select id="select_outbound_bin" @change="form.rack_id = $event.target.value; loadRackInventory()" class="w-full">
                            <option value="">Cari Kode BIN / Rak...</option>
                            @foreach($bins as $b)
                                @php $activePltCount = $b->pallets->where('is_active', true)->count(); @endphp
                                <option value="{{ $b->id }}" {{ $activePltCount == 0 ? 'disabled' : '' }}>
                                    BIN: {{ $b->id }} ({{ $activePltCount }} Palet Aktif {{ $activePltCount == 0 ? '- Kosong' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- LIST PALET & BOX PADA RAK (FEFO RECOMMENDATION) -->
                <div x-show="isLoadingRack" class="py-6 text-center text-slate-400 text-xs">
                    Memuat data stok pada rak...
                </div>

                <div x-show="!isLoadingRack && rackItems.length > 0" class="pt-2 space-y-3">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Pilih Palet / Box di Rak Ini (Rekomendasi Prioritas FEFO):
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="(item, idx) in rackItems" :key="item.box_id">
                            <div @click="selectItem(item)" 
                                 :class="form.box_id === item.box_id ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/20' : 'border-slate-800 bg-slate-950 hover:border-slate-600'"
                                 class="p-3.5 rounded-xl border transition cursor-pointer text-xs space-y-2 relative">
                                <!-- FEFO Badge for earliest expired -->
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-slate-200" x-text="item.pallet_id"></span>
                                    <template x-if="idx === 0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                            FEFO Prioritas
                                        </span>
                                    </template>
                                </div>

                                <div>
                                    <div class="font-bold text-white text-sm" x-text="item.product_name"></div>
                                    <div class="text-[11px] text-slate-400" x-text="'SKU: ' + item.product_id + ' | ' + item.brand"></div>
                                </div>

                                <div class="flex items-center justify-between pt-1 border-t border-slate-800 text-[11px]">
                                    <span class="text-slate-400">Sisa Stok:</span>
                                    <span class="font-bold text-amber-400" x-text="item.qty + ' ' + item.uom_base"></span>
                                </div>

                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Expired Date:</span>
                                    <span class="font-semibold text-rose-300" x-text="item.expired_date"></span>
                                </div>

                                <div class="text-[10px] text-slate-500 truncate" x-show="item.notes" x-text="'Ket: ' + item.notes"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="!isLoadingRack && hasSearched && rackItems.length === 0" class="p-4 bg-slate-950 border border-slate-800 rounded-xl text-center text-xs text-slate-400">
                    Tidak ada palet atau box aktif pada Rak / BIN ini.
                </div>
            </div>

            <!-- STEP 2: DETAIL PENGAMBILAN QTY & SATUAN (Ketentuan 4) -->
            <div class="p-5 bg-slate-900/70 border border-slate-700/80 rounded-2xl space-y-4" x-show="selectedItem">
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-xs font-bold">2</span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Langkah 2: Tentukan Kuantitas & Satuan Pengambilan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pallet & Box Hidden Inputs -->
                    <input type="hidden" name="pallet_id" x-model="form.pallet_id">
                    <input type="hidden" name="box_id" x-model="form.box_id">

                    <!-- Selected Product Summary -->
                    <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-xl space-y-1">
                        <div class="text-[11px] text-slate-400">Barang Terpilih:</div>
                        <div class="font-bold text-white text-sm" x-text="selectedItem ? selectedItem.product_name : '-'"></div>
                        <div class="text-xs text-slate-400">
                            Palet: <strong class="text-slate-200 font-mono" x-text="form.pallet_id"></strong> | Box: <span class="font-mono text-slate-300" x-text="form.box_id"></span>
                        </div>
                    </div>

                    <!-- Qty Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Jumlah Qty Diambil <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" 
                               step="any" 
                               name="qty" 
                               x-model.number="form.qty" 
                               required 
                               min="0.01" 
                               class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-base font-bold">
                    </div>

                    <!-- Satuan Transaksi (UOM) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Satuan Transaksi (UOM) <span class="text-rose-400">*</span>
                        </label>
                        <select id="select_outbound_unit" name="unit" x-model="form.unit" required class="w-full">
                        </select>
                    </div>

                    <!-- Shift -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Shift</label>
                        <select name="shift" x-model="form.shift" class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm">
                            <option value="1">Shift 1</option>
                            <option value="2">Shift 2</option>
                            <option value="3">Shift 3</option>
                        </select>
                    </div>

                    <!-- Live Calculation & Stock Preview -->
                    <div class="md:col-span-2 p-3.5 bg-slate-950 border border-slate-800 rounded-xl space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Total Pengambilan (Satuan Dasar):</span>
                            <span class="font-bold text-amber-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-800">
                            <span class="text-slate-400">Sisa Stok Box Setelah Outbound:</span>
                            <span class="font-bold" :class="remainingBoxQty < 0 ? 'text-rose-400' : 'text-emerald-400'" x-text="remainingBoxQty + ' ' + currentBaseUnit"></span>
                        </div>
                        <div x-show="remainingBoxQty < 0" class="text-rose-400 text-[11px] pt-1">
                            Peringatan: Jumlah pengambilan melebihi stok yang tersedia pada box ini!
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: PILIH STATUS PENGELUARAN (Ketentuan 4) -->
            <div class="p-5 bg-slate-900/70 border border-slate-700/80 rounded-2xl space-y-4" x-show="selectedItem">
                <div class="flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span class="w-6 h-6 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center text-xs font-bold">3</span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Langkah 3: Pilih Status Pengeluaran Barang</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- OPSI 1: KELUAR EKSPEDISI -->
                    <div class="p-4 bg-slate-950 border border-slate-800 hover:border-emerald-500/60 rounded-xl space-y-3 transition flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="font-bold text-emerald-400 text-sm">A. Keluar Ekspedisi</div>
                            <p class="text-xs text-slate-300">
                                Barang dikeluarkan untuk pengiriman armada ekspedisi / distributor. <strong>Stok gudang dipotong permanen</strong>. Jika seluruh box pada palet habis, palet dinonaktifkan.
                            </p>
                        </div>
                        <button type="button" 
                                @click="triggerSubmitOutbound('ekspedisi')" 
                                :disabled="!form.box_id || remainingBoxQty < 0 || form.qty <= 0"
                                :class="(!form.box_id || remainingBoxQty < 0 || form.qty <= 0) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500 shadow-lg shadow-emerald-600/30'"
                                class="w-full py-3 bg-emerald-600 text-white font-bold text-xs sm:text-sm rounded-xl transition flex items-center justify-center">
                            <span>Konfirmasi Keluar Ekspedisi</span>
                        </button>
                    </div>

                    <!-- OPSI 2: KELUAR IN TRANSIT -->
                    <div class="p-4 bg-slate-950 border border-slate-800 hover:border-amber-500/60 rounded-xl space-y-3 transition flex flex-col justify-between">
                        <div class="space-y-1.5">
                            <div class="font-bold text-amber-400 text-sm">B. Keluar In Transit</div>
                            <p class="text-xs text-slate-300">
                                Palet diturunkan dari rak fisik untuk dibawa ke area transit (staging dock, repack, sortir). <strong>Palet dilepas dari rak</strong> dan statusnya beralih menjadi <strong>In Transit</strong>. Stok tetap ada di gudang.
                            </p>
                        </div>
                        <button type="button" 
                                @click="triggerSubmitOutbound('in_transit')" 
                                :disabled="!form.box_id || remainingBoxQty < 0 || form.qty <= 0"
                                :class="(!form.box_id || remainingBoxQty < 0 || form.qty <= 0) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-500 shadow-lg shadow-amber-600/30'"
                                class="w-full py-3 bg-amber-600 text-white font-bold text-xs sm:text-sm rounded-xl transition flex items-center justify-center">
                            <span>Konfirmasi Keluar In Transit</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI OUTBOUND                                                -->
    <!-- ========================================================================= -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4 border"
             :class="form.outbound_type === 'ekspedisi' ? 'border-emerald-500/40' : 'border-amber-500/40'">
            <div>
                <h3 class="text-lg font-bold text-white">
                    Konfirmasi Pengeluaran: <span :class="form.outbound_type === 'ekspedisi' ? 'text-emerald-400' : 'text-amber-400'" x-text="form.outbound_type === 'ekspedisi' ? 'Keluar Ekspedisi' : 'Keluar In Transit'"></span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Harap periksa kembali rincian pengeluaran:</p>
            </div>

            <div class="space-y-2.5 text-xs text-slate-300 bg-slate-900/90 p-4 rounded-xl border border-slate-800">
                <div class="flex justify-between">
                    <span class="text-slate-400">Lokasi Rak / BIN:</span>
                    <span class="font-mono font-bold text-white" x-text="form.rack_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Palet Asal:</span>
                    <span class="font-mono font-bold text-amber-400" x-text="form.pallet_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Box ID:</span>
                    <span class="font-mono text-slate-200" x-text="form.box_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Produk:</span>
                    <span class="font-semibold text-slate-200" x-text="selectedItem ? selectedItem.product_name : '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jumlah Keluar:</span>
                    <span class="font-bold text-white" x-text="form.qty + ' ' + form.unit"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Setara Satuan Dasar:</span>
                    <span class="font-bold text-sky-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-800">
                    <span class="text-slate-400">Status Transaksi:</span>
                    <span class="font-bold uppercase" 
                          :class="form.outbound_type === 'ekspedisi' ? 'text-emerald-400' : 'text-amber-400'"
                          x-text="form.outbound_type === 'ekspedisi' ? 'Keluar Ekspedisi (Potong Stok)' : 'Keluar In Transit (Lepas Rak)'">
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="showConfirmModal = false" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    Batal
                </button>

                <button type="button" 
                        @click="submitOutboundForm()" 
                        :class="form.outbound_type === 'ekspedisi' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500'"
                        class="px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-lg transition">
                    Iya, Jalankan Outbound
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
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
            placeholder: 'Ketik untuk mencari...',
            onChange: function(val) {
                el.value = val;
                el.dispatchEvent(new Event('change', { bubbles: true }));
                if (onChangeCallback) onChangeCallback(val);
            }
        });

        tsInstances[id] = ts;
        return ts;
    }

    function outboundV2Workflow() {
        return {
            showConfirmModal: false,
            isLoadingRack: false,
            hasSearched: false,
            rackItems: [],
            selectedItem: null,

            form: {
                rack_id: '',
                pallet_id: '',
                box_id: '',
                qty: 1,
                unit: 'Box',
                shift: '{{ auth()->user()->shift ?? 1 }}',
                outbound_type: 'ekspedisi'
            },

            availableConversions: {},
            currentBaseUnit: 'Box',

            get calculatedBaseQty() {
                const conv = this.availableConversions[this.form.unit];
                const factor = conv ? parseFloat(conv.qty_convert) : 1;
                return (parseFloat(this.form.qty) || 0) * factor;
            },

            get remainingBoxQty() {
                if (!this.selectedItem) return 0;
                const currentQty = parseFloat(this.selectedItem.qty) || 0;
                return Math.round((currentQty - this.calculatedBaseQty) * 100) / 100;
            },

            initSelectSearchAll() {
                this.$nextTick(() => {
                    initTomSelect('select_outbound_bin', val => {
                        this.form.rack_id = val;
                        this.loadRackInventory();
                    });
                });
            },

            loadRackInventory() {
                const rackId = (this.form.rack_id || '').trim();
                if (!rackId) return;

                this.isLoadingRack = true;
                this.hasSearched = true;
                this.selectedItem = null;
                this.rackItems = [];

                fetch('/api/rack/' + encodeURIComponent(rackId) + '/inventory')
                    .then(res => res.json())
                    .then(data => {
                        this.isLoadingRack = false;
                        if (data.status === 'success') {
                            this.rackItems = data.items || [];
                            if (this.rackItems.length > 0) {
                                // Auto select the first FEFO item
                                this.selectItem(this.rackItems[0]);
                            }
                        }
                    })
                    .catch(err => {
                        this.isLoadingRack = false;
                        console.error('Error fetching rack inventory:', err);
                    });
            },

            selectItem(item) {
                this.selectedItem = item;
                this.form.pallet_id = item.pallet_id;
                this.form.box_id = item.box_id;
                this.currentBaseUnit = item.uom_base || 'Box';

                // Build conversions map
                const convMap = {};
                convMap[this.currentBaseUnit] = { uom_convert: this.currentBaseUnit, qty_convert: 1, uom_base: this.currentBaseUnit };
                if (item.conversions && Array.isArray(item.conversions)) {
                    item.conversions.forEach(c => {
                        convMap[c.uom_convert] = {
                            uom_convert: c.uom_convert,
                            qty_convert: parseFloat(c.qty_convert),
                            uom_base: c.uom_base || this.currentBaseUnit
                        };
                    });
                }
                this.availableConversions = convMap;
                this.form.unit = this.currentBaseUnit;
                this.form.qty = 1;

                // Rebuild unit TomSelect
                this.$nextTick(() => {
                    const unitEl = document.getElementById('select_outbound_unit');
                    if (unitEl) {
                        if (tsInstances['select_outbound_unit']) {
                            tsInstances['select_outbound_unit'].destroy();
                            delete tsInstances['select_outbound_unit'];
                        }
                        unitEl.innerHTML = '';
                        for (let uomName in this.availableConversions) {
                            const c = this.availableConversions[uomName];
                            const opt = document.createElement('option');
                            opt.value = uomName;
                            opt.textContent = uomName + ' (x' + c.qty_convert + ' ' + c.uom_base + ')';
                            if (uomName === this.form.unit) opt.selected = true;
                            unitEl.appendChild(opt);
                        }
                        unitEl.value = this.form.unit;
                        initTomSelect('select_outbound_unit', val => {
                            this.form.unit = val;
                        });
                    }
                });
            },

            triggerSubmitOutbound(type) {
                if (!this.form.rack_id) {
                    alert('Mohon scan / pilih Rak atau BIN terlebih dahulu!');
                    return;
                }
                if (!this.form.box_id) {
                    alert('Mohon pilih Box / Batch yang akan dikeluarkan!');
                    return;
                }
                if (!this.form.qty || this.form.qty <= 0) {
                    alert('Mohon isi kuantitas pengeluaran yang valid!');
                    return;
                }
                if (this.remainingBoxQty < 0) {
                    alert('Jumlah kuantitas melebihi stok yang tersedia!');
                    return;
                }

                this.form.outbound_type = type;
                this.showConfirmModal = true;
            },

            submitOutboundForm() {
                document.getElementById('outboundV2Form').submit();
            }
        }
    }
</script>
@endsection
@endsection
