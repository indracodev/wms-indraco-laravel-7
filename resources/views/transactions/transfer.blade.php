@extends('layouts.app')

@section('title', 'Transfer Antar Palet & Relokasi BIN')

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
        background-color: #0284c7 !important;
        color: #ffffff !important;
    }
    .ts-dropdown .option.selected {
        background-color: #1e293b !important;
        color: #38bdf8 !important;
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
        background-color: #0284c7 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<div class="space-y-6" x-data="transferWorkflow()" x-init="initSelects()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">
                Transfer & Relokasi Stok
            </h2>
            <p class="text-sm text-slate-400 mt-1">Transfer Produk Antar Palet (Mendukung Campur Produk) & Relokasi Palet ke BIN Lain</p>
        </div>
    </div>

    <!-- Workflow Tab Switcher -->
    <div class="flex items-center space-x-2 border-b border-slate-700/80 pb-2">
        <button type="button" 
                @click="switchMode('pallet_to_pallet')" 
                :class="activeMode === 'pallet_to_pallet' ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-medium">
            <span>1. Transfer Produk Antar Palet (Campur Produk)</span>
        </button>

        <button type="button" 
                @click="switchMode('pallet_to_bin')" 
                :class="activeMode === 'pallet_to_bin' ? 'bg-sky-600 text-white shadow-lg shadow-sky-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-5 py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-medium">
            <span>2. Relokasi Palet ke BIN Lain</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: TRANSFER PRODUK ANTAR PALET (Ketentuan 2)                          -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'pallet_to_pallet'" class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-bold text-white">
                Transfer Produk Antar Palet (Konsolidasi & Mixed-Pallet)
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Pindahkan sebagian atau seluruh box dari Palet Asal ke Palet Tujuan. <strong>Ketentuan 2:</strong> Fitur transfer ini mengizinkan penerimaan produk berbeda pada palet tujuan (Mixed-Pallet).
            </p>
        </div>

        <form action="{{ route('transaction.transfer.pallet_to_pallet') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SISI KIRI: PALET & PRODUK ASAL -->
                <div class="p-5 bg-slate-900/80 border border-slate-800 rounded-2xl space-y-4">
                    <div class="font-bold text-xs uppercase tracking-wider text-sky-400 border-b border-slate-800 pb-2">
                        Sumber: Palet & Box Asal
                    </div>

                    <!-- Scan / Input Palet Asal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Scan Barcode Palet Asal <span class="text-rose-400">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   name="source_pallet_id" 
                                   id="source_pallet_input"
                                   x-model="transferData.source_pallet_id" 
                                   @change="loadSourcePalletInfo()"
                                   required 
                                   placeholder="e.g. PLT-001" 
                                   class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <button type="button" 
                                    @click="openCameraScanner(val => { transferData.source_pallet_id = val; loadSourcePalletInfo(); })" 
                                    class="px-3.5 py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer whitespace-nowrap">
                                Kamera
                            </button>
                        </div>
                    </div>

                    <!-- Pilih Palet Asal Terdaftar -->
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Atau Pilih Palet Asal Aktif:</label>
                        <select id="select_source_pallet" @change="transferData.source_pallet_id = $event.target.value; loadSourcePalletInfo()" class="w-full">
                            <option value="">-- Pilih Palet Asal --</option>
                            @foreach($pallets as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} ({{ $plt->bin_id ? 'Rak ' . $plt->bin_id : 'In Transit' }}) - {{ $plt->activeBoxes->count() }} Box
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Box Asal Selector -->
                    <div x-show="sourceBoxes.length > 0">
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Pilih Box / Batch Produk yang Dipindahkan <span class="text-rose-400">*</span>
                        </label>
                        <select name="source_box_id" 
                                id="source_box_id_select"
                                x-model="transferData.source_box_id" 
                                @change="onSourceBoxChange()" 
                                required 
                                class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs">
                            <option value="">-- Pilih Box Spesifik --</option>
                            <template x-for="box in sourceBoxes" :key="box.id">
                                <option :value="box.id" x-text="box.id + ' (' + box.product_name + ') - Stok: ' + box.qty + ' ' + box.uom_base + ' [ED: ' + box.expired_date + ']'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Qty & Unit Input -->
                    <div class="grid grid-cols-2 gap-3" x-show="selectedSourceBox">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                Qty Dipindahkan <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" 
                                   step="any" 
                                   name="qty" 
                                   x-model.number="transferData.qty" 
                                   required 
                                   min="0.01" 
                                   :max="selectedSourceBox ? selectedSourceBox.qty : 999999"
                                   class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">Satuan</label>
                            <input type="text" 
                                   name="unit" 
                                   x-model="transferData.unit" 
                                   readonly 
                                   class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-300 text-sm font-semibold">
                        </div>
                    </div>
                </div>

                <!-- SISI KANAN: PALET TUJUAN (MIXED-PALLET ALLOWED) -->
                <div class="p-5 bg-slate-900/80 border border-slate-800 rounded-2xl space-y-4">
                    <div class="font-bold text-xs uppercase tracking-wider text-emerald-400 border-b border-slate-800 pb-2">
                        Tujuan: Palet Penerima (Mixed Pallet)
                    </div>

                    <!-- Scan / Input Palet Tujuan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">
                            Scan Barcode Palet Tujuan <span class="text-rose-400">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   name="target_pallet_id" 
                                   id="target_pallet_input"
                                   x-model="transferData.target_pallet_id" 
                                   @change="loadTargetPalletInfo()"
                                   required 
                                   placeholder="e.g. PLT-002" 
                                   class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                            <button type="button" 
                                    @click="openCameraScanner(val => { transferData.target_pallet_id = val; loadTargetPalletInfo(); })" 
                                    class="px-3.5 py-3 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer whitespace-nowrap">
                                Kamera
                            </button>
                        </div>
                    </div>

                    <!-- Pilih Palet Tujuan Terdaftar -->
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Atau Pilih Palet Tujuan Terdaftar:</label>
                        <select id="select_target_pallet" @change="transferData.target_pallet_id = $event.target.value; loadTargetPalletInfo()" class="w-full">
                            <option value="">-- Pilih Palet Tujuan --</option>
                            @foreach($pallets as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} ({{ $plt->bin_id ? 'Rak ' . $plt->bin_id : 'In Transit' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Info Palet Tujuan -->
                    <div class="p-3.5 bg-slate-950 border border-slate-800 rounded-xl space-y-2 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Status Palet Tujuan:</span>
                            <span class="font-bold text-sky-400" x-text="targetPalletInfo.is_new ? 'Palet Baru' : (targetPalletInfo.pallet_status === 'on_rack' ? 'Di Rak BIN ' + targetPalletInfo.bin_id : 'In Transit')"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Muatan Saat Ini:</span>
                            <span class="font-bold text-amber-400" x-text="(targetPalletInfo.total_qty || 0) + ' Unit'"></span>
                        </div>

                        <!-- Mixed Pallet Note -->
                        <div class="pt-2 border-t border-slate-800 text-[11px] text-emerald-400">
                            Fitur transfer ini secara otomatis mengizinkan palet tujuan menerima produk berbeda (campur produk).
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" 
                        :disabled="!transferData.source_pallet_id || !transferData.source_box_id || !transferData.target_pallet_id || !transferData.qty"
                        :class="(!transferData.source_pallet_id || !transferData.source_box_id || !transferData.target_pallet_id || !transferData.qty) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-sky-500 shadow-lg shadow-sky-600/30'"
                        class="px-6 py-3 bg-sky-600 text-white font-bold text-sm rounded-xl transition flex items-center justify-center">
                    <span>Eksekusi Transfer Antar Palet</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: RELOKASI PALET KE BIN (Pallet to BIN Relocation)                    -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'pallet_to_bin'" class="glass-panel p-6 sm:p-8 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-bold text-white">
                Relokasi Posisi Palet ke Rak / BIN Baru
            </h3>
            <p class="text-xs text-slate-400 mt-1">Memindahkan satu palet fisik secara utuh beserta seluruh isinya ke slot BIN tujuan.</p>
        </div>

        <form action="{{ route('transaction.transfer') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Palet yang Dipindahkan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300">
                        Scan Barcode Palet <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               name="pallet_id" 
                               id="relocate_pallet_input" 
                               required 
                               placeholder="e.g. PLT-001" 
                               class="w-full p-3.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                        <button type="button" 
                                @click="openCameraScanner(val => document.getElementById('relocate_pallet_input').value = val)" 
                                class="px-3.5 py-3.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer whitespace-nowrap">
                            Kamera
                        </button>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-slate-400 mb-1">Atau Pilih Palet dari Daftar:</label>
                        <select onchange="document.getElementById('relocate_pallet_input').value = this.value" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-200 text-xs">
                            <option value="">-- Pilih Palet --</option>
                            @foreach($pallets as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} (Lokasi Saat Ini: {{ $plt->bin_id ?? 'In Transit' }}) - {{ $plt->activeBoxes->sum('qty') }} unit
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- BIN Tujuan -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300">
                        Scan Barcode Slot BIN Tujuan <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               name="target_bin_id" 
                               id="relocate_bin_input" 
                               required 
                               placeholder="e.g. B.10.1" 
                               class="w-full p-3.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                        <button type="button" 
                                @click="openCameraScanner(val => document.getElementById('relocate_bin_input').value = val)" 
                                class="px-3.5 py-3.5 bg-sky-600 hover:bg-sky-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer whitespace-nowrap">
                            Kamera
                        </button>
                    </div>

                    <div class="pt-1">
                        <label class="block text-[11px] text-slate-400 mb-1">Atau Pilih BIN Tujuan:</label>
                        <select onchange="document.getElementById('relocate_bin_input').value = this.value" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-200 text-xs">
                            <option value="">-- Pilih Slot BIN Tujuan --</option>
                            @foreach($bins as $b)
                                @php $count = $b->pallets->where('is_active', true)->count(); @endphp
                                <option value="{{ $b->id }}" {{ $count >= $b->max_pallet ? 'disabled' : '' }}>
                                    BIN: {{ $b->id }} (Terisi {{ $count }}/{{ $b->max_pallet }} Palet {{ $count >= $b->max_pallet ? '- FULL' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-sky-600 hover:bg-sky-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-sky-600/30 transition">
                    Eksekusi Relokasi BIN
                </button>
            </div>
        </form>
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

    function transferWorkflow() {
        return {
            activeMode: 'pallet_to_pallet',
            sourceBoxes: [],
            selectedSourceBox: null,

            transferData: {
                source_pallet_id: '',
                source_box_id: '',
                target_pallet_id: '',
                qty: 1,
                unit: 'Box'
            },

            targetPalletInfo: {
                is_new: true,
                pallet_status: 'in_transit',
                max_capacity: 72,
                total_qty: 0
            },

            initSelects() {
                this.$nextTick(() => {
                    initTomSelect('select_source_pallet', val => {
                        this.transferData.source_pallet_id = val;
                        this.loadSourcePalletInfo();
                    });

                    initTomSelect('select_target_pallet', val => {
                        this.transferData.target_pallet_id = val;
                        this.loadTargetPalletInfo();
                    });
                });
            },

            switchMode(mode) {
                this.activeMode = mode;
                this.$nextTick(() => {
                    this.initSelects();
                });
            },

            loadSourcePalletInfo() {
                const pltId = (this.transferData.source_pallet_id || '').trim();
                if (!pltId) return;

                fetch('/api/pallet/' + encodeURIComponent(pltId) + '/info')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.sourceBoxes = data.boxes || [];
                            if (this.sourceBoxes.length > 0) {
                                this.transferData.source_box_id = this.sourceBoxes[0].id;
                                this.onSourceBoxChange();
                            } else {
                                this.selectedSourceBox = null;
                            }
                        }
                    })
                    .catch(err => console.error('Error fetching source pallet:', err));
            },

            loadTargetPalletInfo() {
                const pltId = (this.transferData.target_pallet_id || '').trim();
                if (!pltId) return;

                fetch('/api/pallet/' + encodeURIComponent(pltId) + '/info')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.targetPalletInfo = data;
                        }
                    })
                    .catch(err => console.error('Error fetching target pallet:', err));
            },

            onSourceBoxChange() {
                const box = this.sourceBoxes.find(b => b.id === this.transferData.source_box_id);
                if (box) {
                    this.selectedSourceBox = box;
                    this.transferData.unit = box.uom_base;
                    this.transferData.qty = Math.min(1, box.qty);
                }
            }
        }
    }
</script>
@endsection
@endsection
