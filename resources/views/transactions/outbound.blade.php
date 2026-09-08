@extends('layouts.app')

@section('title', 'Outbound Picking Granular Multi-UOM')

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
<div class="space-y-6" x-data="outboundWorkflow()" x-init="initSelectSearchAll()">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-dolly text-amber-400"></i>
                <span>Form Barang Keluar (Outbound Picking Multi-UOM)</span>
            </h2>
            <p class="text-sm text-slate-400">Pengambilan stok barang dari palet & box aktif hingga level satuan terkecil (Sachet, PCS, Karton, dll.)</p>
        </div>
    </div>

    <div class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-amber-400"></i>
                <span>Pencatatan Outbound Granular Satuan Terkecil</span>
            </h3>
            <p class="text-xs text-slate-400 mt-1">Scan/pilih Palet dan Box target, tentukan kuantitas & satuan pengambilan (Multi-UOM), lalu konfirmasikan.</p>
        </div>

        <form id="outboundForm" action="{{ route('transaction.outbound') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Step 1: Scan / Select Palet Asal -->
                <div class="md:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Step 1: Scan / Input Barcode Palet Asal <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center space-x-2">
                            <div class="relative flex-1">
                                <input type="text" 
                                       name="pallet_id" 
                                       id="pallet_id_input"
                                       x-model="form.pallet_id" 
                                       @input="onPalletInput()"
                                       required 
                                       placeholder="e.g. PLT-0001" 
                                       class="w-full p-3.5 pl-10 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                                <i class="fa-solid fa-pallet absolute left-3.5 top-4 text-slate-500"></i>
                            </div>
                            <button type="button" 
                                    @click="openCameraScanner(val => { form.pallet_id = val; onPalletInput(); })" 
                                    class="px-3.5 py-3.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow flex items-center space-x-1.5 text-xs font-semibold cursor-pointer" 
                                    title="Scan lewat Kamera HP/Laptop">
                                <i class="fa-solid fa-camera text-sm"></i>
                                <span class="hidden sm:inline">Kamera</span>
                            </button>
                        </div>

                        <div>
                            <select id="select_outbound_pallet" @change="form.pallet_id = $event.target.value; onPalletInput()" class="w-full">
                                <option value="">🔍 Ketik Kode Palet / Lokasi BIN...</option>
                                @foreach($pallets as $plt)
                                    <option value="{{ $plt->id }}">
                                        Palet: {{ $plt->id }} (BIN: {{ $plt->bin_id ?? 'TRANSIT' }}) - {{ $plt->boxes->count() }} Box Aktif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Box Selector on Pallet -->
                <div class="md:col-span-2 space-y-1.5" x-show="activeBoxes.length > 0">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                        Step 2: Pilih Box Spesifik pada Palet <span class="text-rose-400">*</span>
                    </label>
                    <select id="select_outbound_box" name="box_id" x-model="form.box_id" @change="onBoxChange()" class="w-full">
                        <option value="">-- Pilih Box Spesifik --</option>
                        <template x-for="box in activeBoxes" :key="box.id">
                            <option :value="box.id" x-text="box.id + ' (' + box.product_name + ') - Stok: ' + box.qty + ' ' + box.uom_base + ' [Exp: ' + box.expired_date + ']'"></option>
                        </template>
                    </select>
                </div>

                <!-- Produk SKU -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Cari & Select Produk SKU <span class="text-rose-400">*</span>
                    </label>
                    <select id="select_outbound_product" name="product_id" x-model="form.product_id" @change="onProductChange()" required class="w-full">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->id }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Satuan Transaksi (Multi-UOM down to smallest unit) -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Satuan Pengambilan (UOM Input) <span class="text-rose-400">*</span>
                    </label>
                    <select id="select_outbound_unit" name="unit" x-model="form.unit" required class="w-full">
                    </select>
                </div>

                <!-- Qty Pengambilan -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Jumlah Qty Diambil <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" step="any" name="qty" x-model.number="form.qty" required min="0.01" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-base font-bold">
                </div>

                <!-- Shift Operator -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Shift Kerja Operator
                    </label>
                    <select id="select_outbound_shift" name="shift" x-model="form.shift" required class="w-full">
                        <option value="1">Shift 1 (Pagi)</option>
                        <option value="2">Shift 2 (Siang)</option>
                        <option value="3">Shift 3 (Malam)</option>
                    </select>
                </div>

                <!-- Live Conversion Badge & Box Stock Check -->
                <div class="md:col-span-2 p-4 bg-amber-500/10 border border-amber-500/30 rounded-xl space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-amber-300 font-medium flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-base"></i>
                            <span>Setara Satuan Terkecil yang Dipotong (Base Unit):</span>
                        </span>
                        <span class="text-base font-extrabold text-amber-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                    </div>

                    <div class="text-xs pt-2 border-t border-amber-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-2" x-show="selectedBoxQty !== null">
                        <div class="text-slate-300">
                            Stok Box Tersedia: <strong class="text-white font-mono" x-text="selectedBoxQty + ' ' + currentBaseUnit"></strong>
                        </div>
                        <div>
                            Sisa Stok Setelah Outbound: 
                            <strong :class="(selectedBoxQty - calculatedBaseQty < 0) ? 'text-rose-400 font-bold' : 'text-emerald-400 font-bold'" 
                                    class="font-mono"
                                    x-text="(selectedBoxQty - calculatedBaseQty) + ' ' + currentBaseUnit"></strong>
                        </div>
                    </div>

                    <div x-show="selectedBoxQty !== null && (selectedBoxQty - calculatedBaseQty < 0)" class="text-xs text-rose-400 font-bold flex items-center space-x-1.5 pt-1">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                        <span>Peringatan: Qty pengambilan melebihi stok yang tersedia pada Box terpilih!</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-slate-700/50">
                <a href="{{ route('dashboard') }}" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700">Batal</a>
                
                <button type="button" 
                        @click="triggerOutboundConfirm()" 
                        :disabled="!form.pallet_id || (selectedBoxQty !== null && (selectedBoxQty - calculatedBaseQty < 0))"
                        :class="(!form.pallet_id || (selectedBoxQty !== null && (selectedBoxQty - calculatedBaseQty < 0))) ? 'opacity-50 cursor-not-allowed bg-slate-700' : 'bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-600/30'"
                        class="px-6 py-3 text-white font-semibold text-xs sm:text-sm rounded-xl transition flex items-center space-x-2">
                    <i class="fa-solid fa-minus-circle"></i>
                    <span>Lanjutkan & Konfirmasikan Outbound</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP KONFIRMASI OUTBOUND (YA / BATAL)                             -->
    <!-- ========================================================================= -->
    <div x-show="showOutboundModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4 border border-amber-500/40">
            <div class="flex items-center space-x-3 text-amber-400">
                <i class="fa-solid fa-circle-question text-2xl"></i>
                <h3 class="text-lg font-bold text-white">Konfirmasi Outbound Picking</h3>
            </div>

            <div class="space-y-3 text-xs text-slate-300 bg-slate-900/80 p-4 rounded-xl border border-slate-800">
                <p class="text-sm font-medium text-white">
                    Apakah Anda yakin ingin mengeluarkan <strong class="text-amber-400 font-bold" x-text="form.qty + ' ' + form.unit"></strong> (<strong class="text-sky-400 font-bold" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></strong>) dari Palet <strong class="text-indigo-400 font-mono" x-text="form.pallet_id"></strong>?
                </p>
                <div class="pt-2 border-t border-slate-800 space-y-1.5 text-[11px] text-slate-400">
                    <div>Palet ID: <span class="font-mono text-slate-200 font-bold" x-text="form.pallet_id"></span></div>
                    <div>Box ID Target: <span class="font-mono text-amber-300 font-bold" x-text="form.box_id || 'Auto-Select Box'"></span></div>
                    <div>SKU Produk: <span class="font-bold text-slate-200" x-text="form.product_id"></span></div>
                    <div>Qty Input Pengambilan: <span class="font-bold text-amber-400" x-text="form.qty + ' ' + form.unit"></span></div>
                    <div>Setara Satuan Terkecil: <span class="font-bold text-sky-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span></div>
                    <div>Sisa Stok Box Setelah Outbound: <span class="font-bold text-emerald-400" x-text="(selectedBoxQty !== null ? (selectedBoxQty - calculatedBaseQty) : '-') + ' ' + currentBaseUnit"></span></div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="showOutboundModal = false" 
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700 transition">
                    Tidak / Batal
                </button>

                <button type="button" 
                        @click="submitOutboundForm()" 
                        class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-check"></i>
                    <span>Iya, Proses Outbound</span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const palletsMap = {
        @foreach($pallets as $plt)
            "{{ $plt->id }}": [
                @foreach($plt->boxes as $b)
                    {
                        id: "{{ $b->id }}",
                        product_id: "{{ $b->product_id }}",
                        product_name: "{{ addslashes($b->product->name ?? $b->product_id) }}",
                        qty: {{ $b->qty }},
                        uom_base: "{{ $b->uom_base }}",
                        expired_date: "{{ $b->expired_date }}"
                    },
                @endforeach
            ],
        @endforeach
    };

    const productsMap = {
        @foreach($products as $p)
            @php
                $convMap = [];
                $baseUnit = $p->uom_inventory ?: 'Box';
                $convMap[$baseUnit] = [
                    'uom_convert' => $baseUnit,
                    'qty_convert' => 1.0,
                    'uom_base' => $baseUnit
                ];
                foreach($p->conversions as $c) {
                    $convMap[$c->uom_convert] = [
                        'uom_convert' => $c->uom_convert,
                        'qty_convert' => (float)$c->qty_convert,
                        'uom_base' => $c->uom_base ?: $baseUnit
                    ];
                    if ($c->uom_base && !isset($convMap[$c->uom_base])) {
                        $convMap[$c->uom_base] = [
                            'uom_convert' => $c->uom_base,
                            'qty_convert' => 1.0,
                            'uom_base' => $c->uom_base
                        ];
                    }
                }
            @endphp
            "{{ $p->id }}": {
                name: "{{ addslashes($p->name) }}",
                uom_inventory: "{{ $baseUnit }}",
                conversions: {
                    @foreach($convMap as $uomName => $c)
                        "{{ $uomName }}": { uom_convert: "{{ $c['uom_convert'] }}", qty_convert: {{ $c['qty_convert'] }}, uom_base: "{{ $c['uom_base'] }}" },
                    @endforeach
                }
            },
        @endforeach
    };

    let tsOutboundInstances = {};

    function initOutboundTomSelect(id, onChangeCallback) {
        const el = document.getElementById(id);
        if (!el) return null;

        if (tsOutboundInstances[id]) {
            tsOutboundInstances[id].destroy();
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

        tsOutboundInstances[id] = ts;
        return ts;
    }

    function outboundWorkflow() {
        const initialProdId = '{{ $products->first()->id ?? "" }}';
        const initialProd = productsMap[initialProdId] || { uom_inventory: 'Box', conversions: {} };

        return {
            showOutboundModal: false,
            form: {
                pallet_id: '',
                box_id: '',
                product_id: initialProdId,
                qty: 1,
                unit: Object.keys(initialProd.conversions)[0] || initialProd.uom_inventory,
                shift: '{{ auth()->user()->shift ?? 1 }}'
            },
            activeBoxes: [],
            availableConversions: initialProd.conversions,
            currentBaseUnit: initialProd.uom_inventory,
            selectedBoxQty: null,

            get calculatedBaseQty() {
                const conv = this.availableConversions[this.form.unit];
                const factor = conv ? parseFloat(conv.qty_convert) : 1;
                return (parseFloat(this.form.qty) || 0) * factor;
            },

            initSelectSearchAll() {
                this.$nextTick(() => {
                    initOutboundTomSelect('select_outbound_pallet', val => {
                        this.form.pallet_id = val;
                        this.onPalletInput();
                    });
                    initOutboundTomSelect('select_outbound_product', val => {
                        this.form.product_id = val;
                        this.onProductChange();
                    });
                    initOutboundTomSelect('select_outbound_unit', val => this.form.unit = val);
                    initOutboundTomSelect('select_outbound_shift', val => this.form.shift = val);
                });
            },

            onPalletInput() {
                const boxes = palletsMap[this.form.pallet_id] || [];
                this.activeBoxes = boxes;

                this.$nextTick(() => {
                    // Sync Box Options in DOM & TomSelect
                    const boxEl = document.getElementById('select_outbound_box');
                    if (boxEl) {
                        boxEl.innerHTML = '';
                        const defaultOpt = document.createElement('option');
                        defaultOpt.value = '';
                        defaultOpt.textContent = '-- Pilih Box Spesifik --';
                        boxEl.appendChild(defaultOpt);

                        boxes.forEach(b => {
                            const opt = document.createElement('option');
                            opt.value = b.id;
                            opt.textContent = `${b.id} (${b.product_name}) - Stok: ${b.qty} ${b.uom_base} [Exp: ${b.expired_date}]`;
                            boxEl.appendChild(opt);
                        });

                        initOutboundTomSelect('select_outbound_box', val => {
                            this.form.box_id = val;
                            this.onBoxChange();
                        });
                    }

                    if (boxes.length > 0) {
                        this.form.box_id = boxes[0].id;
                        if (tsOutboundInstances['select_outbound_box']) {
                            tsOutboundInstances['select_outbound_box'].setValue(boxes[0].id);
                        }
                        this.form.product_id = boxes[0].product_id;
                        this.selectedBoxQty = boxes[0].qty;
                        this.onProductChange();
                    } else {
                        this.form.box_id = '';
                        if (tsOutboundInstances['select_outbound_box']) {
                            tsOutboundInstances['select_outbound_box'].clear();
                        }
                        this.selectedBoxQty = null;
                    }
                });
            },

            onBoxChange() {
                const box = this.activeBoxes.find(b => b.id === this.form.box_id);
                if (box) {
                    this.form.product_id = box.product_id;
                    this.selectedBoxQty = box.qty;
                    this.onProductChange();
                }
            },

            onProductChange() {
                const prod = productsMap[this.form.product_id];
                if (prod) {
                    this.availableConversions = prod.conversions;
                    this.currentBaseUnit = prod.uom_inventory;

                    // Prefer smallest unit or current unit
                    const uomKeys = Object.keys(this.availableConversions);
                    if (!this.availableConversions[this.form.unit]) {
                        this.form.unit = uomKeys[0] || prod.uom_inventory;
                    }

                    // Sync Product Select TomSelect UI
                    if (tsOutboundInstances['select_outbound_product'] && tsOutboundInstances['select_outbound_product'].getValue() !== this.form.product_id) {
                        tsOutboundInstances['select_outbound_product'].setValue(this.form.product_id);
                    }

                    // Re-sync unit select options in TomSelect
                    this.$nextTick(() => {
                        const unitEl = document.getElementById('select_outbound_unit');
                        if (unitEl) {
                            if (tsOutboundInstances['select_outbound_unit']) {
                                tsOutboundInstances['select_outbound_unit'].destroy();
                                delete tsOutboundInstances['select_outbound_unit'];
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
                            initOutboundTomSelect('select_outbound_unit', val => {
                                this.form.unit = val;
                            });
                        }
                    });
                }
            },

            triggerOutboundConfirm() {
                if (!this.form.pallet_id) {
                    alert('Mohon isi atau pilih Barcode Palet Asal!');
                    return;
                }
                if (this.selectedBoxQty !== null && (this.selectedBoxQty - this.calculatedBaseQty < 0)) {
                    alert('Gagal: Qty pengambilan melebihi stok yang tersedia pada Box terpilih!');
                    return;
                }
                this.showOutboundModal = true;
            },

            submitOutboundForm() {
                document.getElementById('outboundForm').submit();
            }
        }
    }
</script>
@endsection
@endsection
