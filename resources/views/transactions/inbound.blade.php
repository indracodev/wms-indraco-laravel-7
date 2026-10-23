@extends('layouts.app')

@section('title', 'Inbound Putaway & Registrasi Stok V2')

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

    /* Hide browser default datalist arrow icon */
    input::-webkit-calendar-picker-indicator {
        display: none !important;
        -webkit-appearance: none !important;
        opacity: 0 !important;
    }

    .modal-backdrop-blur,
    html.light .modal-backdrop-blur,
    html.dark .modal-backdrop-blur {
        background-color: rgba(15, 23, 42, 0.45) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }
</style>
@endsection

@section('content')
<div class="space-y-6" x-data="inboundV2Workflow()" x-init="initSelectSearchAll()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold font-display text-white">
                Penerimaan Barang Masuk (Inbound V2)
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Alur Wizard 4 Tahap: Palet &rarr; Produk &rarr; Qty & ED &rarr; Simpan (In Transit / Rak)</p>
        </div>
    </div>

    <!-- Workflow Tab Switcher -->
    <div class="flex items-center space-x-2 border-b border-slate-700/80 pb-2">
        <button type="button" 
                @click="switchMode('inbound_flow')" 
                :class="activeMode === 'inbound_flow' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-medium">
            <span>Alur Penerimaan Barang (4 Langkah)</span>
        </button>

        <button type="button" 
                @click="switchMode('putaway_flow')" 
                :class="activeMode === 'putaway_flow' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30 font-bold' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200'"
                class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-medium">
            <span>Putaway Palet Transit ke Rak</span>
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- ALUR 1: 4-STEP DEDICATED MOBILE & DESKTOP WIZARD (SEPARATE FRAMES)       -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'inbound_flow'" class="space-y-4">

        <!-- STEPPER HEADER / PROGRESS TIMELINE -->
        <div class="glass-panel p-3 sm:p-4 rounded-2xl border border-slate-800 shadow-lg">
            <!-- 4 Segment Clickable Tabs -->
            <div class="grid grid-cols-4 gap-1.5 sm:gap-3 text-center">
                <!-- Step 1 Tab -->
                <button type="button" 
                        @click="goToStep(1)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 1 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : (currentStep > 1 ? 'bg-indigo-950/50 text-indigo-300 hover:bg-slate-800' : 'bg-slate-900/60 text-slate-500')">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :class="currentStep === 1 ? 'bg-white text-indigo-700 dark:text-indigo-600' : (currentStep > 1 ? 'bg-indigo-500/30 text-indigo-300' : 'bg-slate-800 text-slate-400')"
                          :style="currentStep === 1 ? 'color: #4338ca !important; background-color: #ffffff !important;' : ''">
                        <template x-if="currentStep > 1"><span>✓</span></template>
                        <template x-if="currentStep <= 1"><span style="color: inherit !important;">1</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" :class="currentStep === 1 ? 'text-white' : ''">Palet</div>
                        <div class="hidden md:block text-[9px] text-slate-300/80">Scan Barcode</div>
                    </div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" 
                        @click="goToStep(2)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 2 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : (currentStep > 2 ? 'bg-indigo-950/50 text-indigo-300 hover:bg-slate-800' : 'bg-slate-900/60 text-slate-500')">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :class="currentStep === 2 ? 'bg-white text-indigo-700 dark:text-indigo-600' : (currentStep > 2 ? 'bg-indigo-500/30 text-indigo-300' : 'bg-slate-800 text-slate-400')"
                          :style="currentStep === 2 ? 'color: #4338ca !important; background-color: #ffffff !important;' : ''">
                        <template x-if="currentStep > 2"><span>✓</span></template>
                        <template x-if="currentStep <= 2"><span style="color: inherit !important;">2</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" :class="currentStep === 2 ? 'text-white' : ''">Produk</div>
                        <div class="hidden md:block text-[9px] text-slate-300/80">Pilih SKU Varian</div>
                    </div>
                </button>

                <!-- Step 3 Tab -->
                <button type="button" 
                        @click="goToStep(3)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 3 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : (currentStep > 3 ? 'bg-indigo-950/50 text-indigo-300 hover:bg-slate-800' : 'bg-slate-900/60 text-slate-500')">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :class="currentStep === 3 ? 'bg-white text-indigo-700 dark:text-indigo-600' : (currentStep > 3 ? 'bg-indigo-500/30 text-indigo-300' : 'bg-slate-800 text-slate-400')"
                          :style="currentStep === 3 ? 'color: #4338ca !important; background-color: #ffffff !important;' : ''">
                        <template x-if="currentStep > 3"><span>✓</span></template>
                        <template x-if="currentStep <= 3"><span style="color: inherit !important;">3</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" :class="currentStep === 3 ? 'text-white' : ''">Detail</div>
                        <div class="hidden md:block text-[9px] text-slate-300/80">Qty, ED & Notes</div>
                    </div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" 
                        @click="goToStep(4)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 4 ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900/60 text-slate-500'">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :class="currentStep === 4 ? 'bg-white text-indigo-700 dark:text-indigo-600' : 'bg-slate-800 text-slate-400'"
                          :style="currentStep === 4 ? 'color: #4338ca !important; background-color: #ffffff !important;' : ''">
                        <span style="color: inherit !important;">4</span>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" :class="currentStep === 4 ? 'text-white' : ''">Simpan / Rak</div>
                        <div class="hidden md:block text-[9px] text-slate-300/80">In Transit / Rak</div>
                    </div>
                </button>
            </div>

            <!-- Mobile Progress Bar & Title -->
            <div class="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <div class="text-slate-300 font-medium">
                    Tahap <strong class="text-indigo-400" x-text="currentStep"></strong>/4: 
                    <span class="text-white font-bold" x-text="stepTitles[currentStep]"></span>
                </div>
                <div class="font-mono text-[11px] text-indigo-400 font-bold" x-text="(currentStep * 25) + '%'"></div>
            </div>
            <div class="w-full bg-slate-800 h-1 rounded-full overflow-hidden mt-1.5">
                <div class="bg-indigo-500 h-full transition-all duration-300" :style="'width: ' + (currentStep * 25) + '%'"></div>
            </div>
        </div>

        <!-- FORM CONTAINER (Single Unified Form, Split Views) -->
        <form id="inboundV2Form" action="{{ route('transaction.inbound') }}" method="POST">
            @csrf
            <input type="hidden" name="box_id" x-model="form.box_id">

            <!-- ========================================================================= -->
            <!-- FRAME 1: LANGKAH 1 - IDENTIFIKASI PALET & KAPASITAS                      -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 1" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="glass-panel p-5 sm:p-7 rounded-2xl shadow-xl space-y-5">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-white flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                            <span>Langkah 1: Identifikasi Barcode Palet</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Scan barcode fisik palet dan periksa muatan saat ini.</p>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                              :class="{
                                  'bg-amber-500/20 text-amber-300 border border-amber-500/30': palletInfo.pallet_status === 'in_transit',
                                  'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': palletInfo.pallet_status === 'on_rack',
                                  'bg-sky-500/20 text-sky-300 border border-sky-500/30': palletInfo.is_new
                              }"
                              x-text="palletStatusLabel">
                        </span>
                    </div>
                </div>

                <!-- Input Barcode Palet (3 Metode Input: Manual/Select Search, Kamera, Barcode Bar Scanner) -->
                <div class="space-y-4">
                    <div>
                        <!-- Label & Mode Tabs -->
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Barcode Palet <span class="text-rose-400">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400">Pilih salah satu metode</span>
                        </div>

                        <!-- Main Method 1: Input Kode Palet + Datalist Suggestions & Quick Action Buttons -->
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           name="pallet_id" 
                                           id="pallet_id_input"
                                           x-model="form.pallet_id" 
                                           @change="checkPalletInfo()"
                                           @keyup.enter="checkPalletInfo()"
                                           list="pallet_suggestions"
                                           required 
                                           placeholder="Ketik/Pilih Kode Palet..."
                                           class="w-full p-3.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-base uppercase font-bold focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    <datalist id="pallet_suggestions">
                                        @foreach($pallets as $plt)
                                            <option value="{{ $plt->id }}">{{ $plt->id }} ({{ $plt->bin_id ? 'Rak ' . $plt->bin_id : 'In Transit' }})</option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Thumb Action: Kamera Scanner (Sebelah Kanan Jempol) -->
                                <button type="button" 
                                        @click="openCameraScanner(val => { form.pallet_id = val; checkPalletInfo(); })" 
                                        class="px-4 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/30 text-xs font-bold cursor-pointer whitespace-nowrap active:scale-95 transition">
                                    Kamera
                                </button>
                            </div>

                            <!-- Method 3: Barcode Bar / Simulator Fast Bar -->
                            <div class="flex items-center space-x-2 pt-0.5">
                                <a href="{{ route('scanner.index') }}" target="_blank"
                                   class="w-full py-2.5 px-3 bg-purple-950/40 hover:bg-purple-900/50 border border-purple-500/30 rounded-xl flex items-center justify-center text-xs text-purple-300 font-semibold transition active:scale-95">
                                    <span>Buka Scanner Simulator Barcode Bar</span>
                                </a>
                            </div>

                            <!-- Method 1 (Search Dropdown): Select Search Palet Terdaftar -->
                            <div class="pt-1.5">
                                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Atau Cari & Pilih dari Dropdown (Select Search):</label>
                                <select id="select_existing_pallet" @change="form.pallet_id = $event.target.value; checkPalletInfo()" class="w-full">
                                    <option value="">Cari Palet Terdaftar...</option>
                                    @foreach($pallets as $plt)
                                        <option value="{{ $plt->id }}">
                                            Palet: {{ $plt->id }} ({{ $plt->bin_id ? 'Rak ' . $plt->bin_id : 'In Transit' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="max_capacity" x-model="form.max_capacity">
                    <input type="hidden" name="pallet_type_id" x-model="form.pallet_type_id">

                    <!-- Alert Ketentuan 1: Palet Terkunci 1 Produk -->
                    <div x-show="palletInfo.active_product_id" class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-amber-300">Ketentuan 1: Palet Sudah Berisi Produk</span>
                            <span class="text-amber-400 font-mono text-[11px]" x-text="'SKU: ' + palletInfo.active_product_id"></span>
                        </div>
                        <p class="text-xs text-slate-300">
                            Palet ini sudah memiliki muatan produk <strong class="text-white" x-text="palletInfo.active_product_name"></strong>. Saat penerimaan awal, hanya produk yang sama yang dapat ditambahkan ke palet ini.
                        </p>
                    </div>

                    <!-- Multi-ED Breakdown Display (Ketentuan 6) -->
                    <div x-show="palletInfo.boxes && palletInfo.boxes.length > 0" class="pt-2 border-t border-slate-800">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Daftar Batch Expired Date di Palet Ini (Multi-ED Aktif):
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="box in palletInfo.boxes" :key="box.id">
                                <div class="p-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs space-y-1">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-mono text-slate-400" x-text="box.id"></span>
                                        <span class="font-bold text-sky-400" x-text="box.qty + ' ' + box.uom_base"></span>
                                    </div>
                                    <div class="text-[11px] text-amber-300">
                                        ED: <span class="font-semibold" x-text="box.expired_date"></span>
                                    </div>
                                    <div class="text-[10px] text-slate-500 truncate" x-show="box.notes" x-text="'Ket: ' + box.notes"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Frame 1 (Thumb-friendly full width on mobile) -->
                <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" 
                            @click="nextStep()" 
                            class="w-full sm:w-auto px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 2: Scan Produk</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- FRAME 2: LANGKAH 2 - SCAN PRODUK & VARIAN SATUAN                         -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 2" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="glass-panel p-5 sm:p-7 rounded-2xl shadow-xl space-y-5">
                
                <!-- Pallet Context Summary Banner -->
                <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-400">Palet Terpilih:</span>
                        <strong class="text-indigo-400 font-mono ml-1" x-text="form.pallet_id"></strong>
                        <span class="text-slate-500 ml-1.5" x-text="'(' + palletStatusLabel + ')'"></span>
                    </div>
                    <button type="button" @click="goToStep(1)" class="text-indigo-400 hover:underline text-[11px] font-semibold">Ubah Palet</button>
                </div>

                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                        <span>Langkah 2: Scan / Pilih Produk & Varian Satuan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pindai barcode produk atau cari SKU. Sistem akan menampilkan varian satuan konversi.</p>
                </div>

                <!-- Input Produk SKU (3 Metode Input: Manual/Select Search, Kamera, Barcode Bar Scanner) -->
                <div class="space-y-4">
                    <div>
                        <!-- Label & Description -->
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                                Cari / Scan Produk SKU <span class="text-rose-400">*</span>
                            </label>
                            <span class="text-[10px] text-slate-400">Pilih salah satu metode</span>
                        </div>

                        <!-- Main Method 1: Input Kode Produk + Datalist Suggestions & Kamera Button -->
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           id="product_sku_manual_input"
                                           :value="form.product_id"
                                           @input="form.product_id = $event.target.value; onProductScanned($event.target.value)"
                                           @change="onProductScanned($event.target.value)"
                                           @keyup.enter="onProductScanned($event.target.value)"
                                           list="product_sku_suggestions"
                                           required 
                                           placeholder="Ketik/Pilih Kode SKU Produk..."
                                           class="w-full p-3.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-base uppercase font-bold focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    <datalist id="product_sku_suggestions">
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->id }} - {{ $p->name }}</option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <!-- Thumb Action: Kamera Scanner (Sebelah Kanan Jempol) -->
                                <button type="button" 
                                        @click="openCameraScanner(val => onProductScanned(val))" 
                                        class="px-4 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/30 text-xs font-bold cursor-pointer whitespace-nowrap active:scale-95 transition">
                                    Kamera
                                </button>
                            </div>

                            <!-- Method 3: Barcode Bar / Simulator Fast Bar -->
                            <div class="flex items-center space-x-2 pt-0.5">
                                <a href="{{ route('scanner.index') }}" target="_blank"
                                   class="w-full py-2.5 px-3 bg-purple-950/40 hover:bg-purple-900/50 border border-purple-500/30 rounded-xl flex items-center justify-center text-xs text-purple-300 font-semibold transition active:scale-95">
                                    <span>Buka Scanner Simulator Barcode Bar</span>
                                </a>
                            </div>

                            <!-- Method 1 (Search Dropdown): Select Search Produk Terdaftar -->
                            <div class="pt-1.5">
                                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Atau Cari & Pilih dari Dropdown (Select Search):</label>
                                <select id="select_product_id" 
                                        name="product_id" 
                                        x-model="form.product_id" 
                                        @change="onProductChange()" 
                                        required 
                                        class="w-full">
                                    <option value="">Ketik SKU / Nama Produk / Brand...</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->id }}) - {{ $p->brand ?? 'No Brand' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Product Details Card -->
                    <div class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-2">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Rincian Produk Terpilih:
                        </div>
                        <div class="text-base font-bold text-white" x-text="selectedProductName"></div>
                        
                        <!-- Varian Satuan UOM Pills -->
                        <div class="pt-2 border-t border-slate-800 space-y-1.5">
                            <div class="text-[11px] text-slate-400">Varian Satuan Tersedia:</div>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="(conv, uomName) in availableConversions" :key="uomName">
                                    <span class="px-2.5 py-1 bg-slate-900 text-slate-200 border border-slate-700 rounded-lg text-xs font-medium"
                                          :class="uomName === form.unit ? 'border-indigo-400 bg-indigo-900/40 text-indigo-300 font-bold' : ''">
                                        <span x-text="uomName"></span>
                                        <span class="text-[10px] text-slate-400" x-text="'(x' + conv.qty_convert + ' ' + conv.uom_base + ')'"></span>
                                    </span>
                                </template>
                            </div>
                            <div class="text-[11px] text-slate-400 pt-1">
                                Satuan Terkecil (Base): <strong class="text-sky-300" x-text="currentBaseUnit"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Frame 2 -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-3">
                    <button type="button" 
                            @click="prevStep()" 
                            class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                        &larr; Kembali (Palet)
                    </button>
                    <button type="button" 
                            @click="nextStep()" 
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 3: Qty & ED &rarr;</span>
                    </button>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- FRAME 3: LANGKAH 3 - INPUT QUANTITY, EXPIRED DATE & KETERANGAN          -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 3" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="glass-panel p-5 sm:p-7 rounded-2xl shadow-xl space-y-5">
                
                <!-- Pallet & Product Summary Context -->
                <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div>
                        <span class="text-slate-400">Palet:</span>
                        <strong class="text-indigo-400 font-mono ml-1" x-text="form.pallet_id"></strong>
                        <span class="text-slate-500 mx-1">|</span>
                        <span class="text-slate-400">Produk:</span>
                        <strong class="text-white ml-1" x-text="selectedProductName"></strong>
                    </div>
                    <button type="button" @click="goToStep(2)" class="text-indigo-400 hover:underline text-[11px] font-semibold">Ubah Produk</button>
                </div>

                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">3</span>
                        <span>Langkah 3: Input Qty, Expired Date & Keterangan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Masukkan kuantitas barang, satuan transaksi, tanggal kedaluwarsa, dan catatan penerimaan.</p>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Qty Input -->
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                Jumlah Qty Masuk <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" 
                                   step="any" 
                                   name="qty" 
                                   x-model.number="form.qty" 
                                   required 
                                   min="0.01" 
                                   class="w-full p-3.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-base font-bold focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <!-- Satuan Transaksi (UOM) - Clean Pill Selector & Native Select -->
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>Satuan Transaksi (UOM) <span class="text-rose-400">*</span></span>
                                <span class="text-[10px] text-slate-400 font-normal">Pilih salah satu satuan</span>
                            </label>
                            
                            <!-- UOM Quick Interactive Pill Cards (Thumb-Friendly & Responsive) -->
                            <div class="grid grid-cols-2 gap-2.5 mb-2">
                                <template x-for="(conv, uomName) in availableConversions" :key="uomName">
                                    <button type="button" 
                                            @click="form.unit = uomName" 
                                            class="p-3 min-h-[58px] rounded-xl border text-left transition-all active:scale-95 cursor-pointer flex flex-col justify-between"
                                            :class="form.unit === uomName 
                                                ? 'bg-indigo-600 text-white border-indigo-500 shadow-md shadow-indigo-600/30 ring-2 ring-indigo-500/40' 
                                                : 'bg-slate-950 text-slate-800 dark:text-slate-200 border-slate-300 dark:border-slate-700 hover:border-indigo-400'">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-sm tracking-wide" 
                                                  :class="form.unit === uomName ? 'text-white' : 'text-slate-900 dark:text-white'" 
                                                  x-text="uomName"></span>
                                            <span class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px]"
                                                  :class="form.unit === uomName ? 'border-white bg-white font-bold' : 'border-slate-400 dark:border-slate-600'">
                                                <span x-show="form.unit === uomName" class="text-indigo-600">✓</span>
                                            </span>
                                        </div>
                                        <div class="text-[11px] mt-1 font-medium" 
                                              :class="form.unit === uomName ? 'text-indigo-100' : 'text-slate-600 dark:text-slate-400'">
                                            <span x-text="'Isi: ' + conv.qty_convert + ' ' + conv.uom_base"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Native Hidden Select for Form Submission Safety -->
                            <select id="select_unit" name="unit" x-model="form.unit" required class="hidden">
                                <template x-for="(conv, uomName) in availableConversions" :key="uomName">
                                    <option :value="uomName" :selected="form.unit === uomName" x-text="uomName"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Live Conversion Card -->
                    <div class="p-3 bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/30 rounded-xl flex items-center justify-between text-xs">
                        <span class="text-sky-800 dark:text-sky-300 font-medium">Setara Satuan Dasar (Base Qty):</span>
                        <span class="text-sm font-extrabold text-sky-700 dark:text-sky-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                    </div>

                    <!-- Expired Date (Multi-ED Support) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Expired Date (ED) <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" 
                               name="expired_date" 
                               x-model="form.expired_date" 
                               required 
                               class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm">
                    </div>

                    <!-- Keterangan / Notes (Ketentuan 3) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Keterangan / No. PO / Catatan Fisik
                        </label>
                        <input type="text" 
                               name="notes" 
                               x-model="form.notes" 
                               placeholder="e.g. PO-2026/089, Batch QC Lolos, Kondisi Baik" 
                               class="w-full p-3 bg-slate-950 border border-slate-700 rounded-xl text-white text-sm">
                    </div>

                    <!-- Hidden Shift & Mesin (Default dari Shift User & Mesin Default) -->
                    <input type="hidden" name="shift" x-model="form.shift">
                    <input type="hidden" name="machine_code" x-model="form.machine_code">

                    <!-- RINGKASAN MUATAN PALET -->
                    <div class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-2 mt-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-300">Ringkasan Muatan Palet Ini:</span>
                            <span class="font-mono font-bold text-sky-400" x-text="totalForecastQty + ' ' + currentBaseUnit + ' Total'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-400 pt-1.5 border-t border-slate-800">
                            <div>Muatan sebelumnya: <strong class="text-slate-200" x-text="(palletInfo.total_qty || 0) + ' ' + currentBaseUnit"></strong></div>
                            <div>Tambahan input: <strong class="text-sky-300" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></strong></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Frame 3 -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Opsi 1: Langsung Simpan In Transit dari Langkah 3 -->
                        <button type="button" 
                                @click="triggerSubmitInbound(false)" 
                                :disabled="!form.pallet_id || !form.product_id || !form.qty"
                                class="w-full py-3.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-amber-600/30 transition flex flex-col items-center justify-center cursor-pointer active:scale-95">
                            <span class="font-extrabold flex items-center gap-1.5">
                                <span>Simpan (Status: In Transit)</span>
                            </span>
                            <span class="text-[10px] text-amber-100 font-normal mt-0.5">Barang di palet, belum ditaruh di rak</span>
                        </button>

                        <!-- Opsi 2: Lanjut ke Penempatan Rak (Langkah 4) -->
                        <button type="button" 
                                @click="nextStep()" 
                                class="w-full py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-indigo-600/30 transition flex flex-col items-center justify-center cursor-pointer active:scale-95">
                            <span class="font-extrabold">Lanjut: Tempatkan ke Rak &rarr;</span>
                            <span class="text-[10px] text-indigo-100 font-normal mt-0.5">Pilih / scan barcode rak fisik</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <button type="button" 
                                @click="prevStep()" 
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                            &larr; Kembali (Produk)
                        </button>
                        <span class="text-[11px] text-slate-400">
                            Palet In Transit dapat dipindah ke rak kapan saja
                        </span>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- FRAME 4: LANGKAH 4 - PENEMPATAN RAK & EKSEKUSI SIMPAN                   -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 4" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="glass-panel p-5 sm:p-7 rounded-2xl shadow-xl space-y-5">
                
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center space-x-2">
                        <span class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">4</span>
                        <span>Langkah 4: Penempatan Rak (Opsional) & Simpan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Palet dapat langsung disimpan dengan status "In Transit" (belum ke rak) atau langsung ditempatkan ke rak fisik.</p>
                </div>

                <!-- Info Alert: Fleksibilitas Status In Transit -->
                <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl flex items-start gap-2.5 text-xs">
                    <span class="px-2 py-0.5 bg-amber-500/20 text-amber-400 dark:text-amber-300 font-bold rounded text-[10px] tracking-wide uppercase shrink-0 mt-0.5">
                        In Transit
                    </span>
                    <div class="text-slate-700 dark:text-slate-300 text-[11px] leading-relaxed">
                        Jika palet belum diangkut forklift ke rak, Anda dapat langsung menekan tombol <strong>Simpan Sementara (In Transit)</strong>. Palet tetap tersimpan resmi dan dapat dipindahkan ke rak kapan saja melalui menu <em>"Putaway Palet Transit ke Rak"</em>.
                    </div>
                </div>

                <!-- RINGKASAN REVIEW TRANSAKSI -->
                <div class="p-4 bg-slate-950 border border-slate-800 rounded-xl space-y-2.5 text-xs text-slate-300">
                    <div class="font-bold text-slate-200 uppercase tracking-wider text-[11px] pb-1 border-b border-slate-800">
                        Ringkasan Penerimaan Barang:
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400">Palet Target:</span>
                            <div class="font-mono font-bold text-indigo-400 text-sm" x-text="form.pallet_id"></div>
                        </div>
                        <div>
                            <span class="text-slate-400">Status Palet:</span>
                            <div class="font-semibold text-indigo-300" x-text="palletStatusLabel"></div>
                        </div>
                        <div>
                            <span class="text-slate-400">Produk SKU:</span>
                            <div class="font-semibold text-white truncate" x-text="selectedProductName"></div>
                        </div>
                        <div>
                            <span class="text-slate-400">Jumlah Input:</span>
                            <div class="font-bold text-amber-400" x-text="form.qty + ' ' + form.unit"></div>
                        </div>
                        <div>
                            <span class="text-slate-400">Setara Satuan Dasar:</span>
                            <div class="font-bold text-sky-400" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></div>
                        </div>
                        <div>
                            <span class="text-slate-400">Expired Date:</span>
                            <div class="font-semibold text-slate-200" x-text="form.expired_date"></div>
                        </div>
                    </div>
                    <div class="pt-1 text-[11px] text-slate-400" x-show="form.notes">
                        Keterangan: <span class="text-slate-200" x-text="form.notes"></span>
                    </div>
                </div>

                <!-- Input Penempatan Rak / BIN (Opsional) -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider">
                            Penempatan Rak / BIN Tujuan <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <button type="button" 
                                x-show="form.bin_id" 
                                @click="form.bin_id = ''" 
                                class="text-[11px] text-rose-400 hover:underline flex items-center gap-1 cursor-pointer">
                            <span>Batalkan Rak (Tetap In Transit)</span>
                        </button>
                    </div>

                    <!-- Metode 1: Input Kode / Scan Kamera -->
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="bin_id" 
                                   id="inbound_bin_input"
                                   x-model="form.bin_id" 
                                   list="inbound_bin_datalist"
                                   placeholder="Ketik/Scan BIN (e.g. A.1.1) atau pilih di bawah..."
                                   class="w-full p-3.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase font-bold focus:ring-2 focus:ring-indigo-500 shadow-inner">
                            <datalist id="inbound_bin_datalist">
                                @foreach($bins as $b)
                                    @php $count = $b->pallets->where('is_active', true)->count(); @endphp
                                    <option value="{{ $b->id }}">{{ $b->id }} (Terisi {{ $count }}/{{ $b->max_pallet }} Palet{{ $count >= $b->max_pallet ? ' - FULL' : '' }})</option>
                                @endforeach
                            </datalist>
                        </div>
                        <button type="button" 
                                @click="openCameraScanner(val => form.bin_id = val)" 
                                class="px-4 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/30 text-xs font-bold cursor-pointer whitespace-nowrap active:scale-95 transition">
                            Kamera
                        </button>
                    </div>

                    <!-- Metode 2: Dropdown Bersih Native (Tanpa TomSelect) -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 mb-1">
                            Atau Pilih dari Daftar Rak / BIN:
                        </label>
                        <select x-model="form.bin_id" 
                                class="w-full p-3.5 bg-slate-950 border border-slate-700 rounded-xl text-white text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                            <option value="">-- Biarkan Kosong (Status: In Transit / Belum Masuk Rak) --</option>
                            @foreach($bins as $b)
                                @php $count = $b->pallets->where('is_active', true)->count(); @endphp
                                <option value="{{ $b->id }}" {{ $count >= $b->max_pallet ? 'disabled' : '' }}>
                                    BIN: {{ $b->id }} (Terisi {{ $count }}/{{ $b->max_pallet }} Palet{{ $count >= $b->max_pallet ? ' - FULL' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Visual Indikator Penempatan -->
                    <div x-show="form.bin_id" class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">✓</span>
                            <div>
                                <span class="text-slate-400">Lokasi Rak Terpilih:</span>
                                <strong class="text-emerald-400 font-mono text-sm ml-1" x-text="form.bin_id"></strong>
                            </div>
                        </div>
                        <button type="button" @click="form.bin_id = ''" class="text-xs text-rose-400 hover:underline">
                            Hapus Pilihan
                        </button>
                    </div>

                    <div x-show="!form.bin_id" class="p-3 bg-slate-900/80 border border-slate-800 rounded-xl text-[11px] text-slate-400">
                        Catatan: Jika barang masih berada di area bongkar muat dan belum diangkut forklift ke rak, Anda dapat langsung menekan tombol <strong>Simpan Sementara (In Transit)</strong>.
                    </div>
                </div>

                <!-- ACTION BUTTONS: DUA OPSI UTAMA UNTUK MOBILE & DESKTOP -->
                <div class="pt-4 border-t border-slate-800 space-y-2.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Action Button 1: Simpan Sementara (In Transit) -->
                        <button type="button" 
                                @click="triggerSubmitInbound(false)" 
                                :disabled="!form.pallet_id || !form.product_id || !form.qty"
                                class="w-full py-3.5 px-4 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex flex-col items-center justify-center cursor-pointer active:scale-95">
                            <span class="font-extrabold">Simpan Sementara (In Transit)</span>
                            <span class="text-[10px] text-amber-200 font-normal mt-0.5">Barang di palet, belum masuk rak</span>
                        </button>

                        <!-- Action Button 2: Simpan & Tempatkan ke Rak -->
                        <button type="button" 
                                @click="triggerSubmitInbound(true)" 
                                :disabled="!form.pallet_id || !form.product_id || !form.qty"
                                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition flex flex-col items-center justify-center cursor-pointer active:scale-95">
                            <span class="font-extrabold">Simpan & Tempatkan ke Rak</span>
                            <span class="text-[10px] text-emerald-200 font-normal mt-0.5">Wajib isi barcode Rak/BIN</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <button type="button" 
                                @click="prevStep()" 
                                class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition">
                            &larr; Kembali ke Detail
                        </button>
                        <button type="button" 
                                @click="resetInboundForm()" 
                                class="text-xs text-slate-500 hover:text-slate-300 underline">
                            Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- ALUR 2: PUTAWAY PALET IN-TRANSIT KE RAK                                   -->
    <!-- ========================================================================= -->
    <div x-show="activeMode === 'putaway_flow'" class="glass-panel p-5 sm:p-7 rounded-2xl shadow-xl space-y-6">
        <div class="border-b border-slate-700/60 pb-3">
            <h3 class="text-base font-bold text-white">
                Putaway Palet In-Transit ke Rak Fisik
            </h3>
            <p class="text-xs text-slate-400 mt-1">Gunakan form ini untuk memindahkan palet berstatus In Transit yang sudah selesai dibongkar ke rak BIN tujuan.</p>
        </div>

        <form action="{{ route('transaction.inbound.pallet_to_bin') }}" method="POST" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Pilih / Scan Palet In-Transit <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="pallet_id" id="putaway_pallet_id" required placeholder="e.g. PLT-001" class="w-full p-3.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                        <button type="button" 
                                @click="openCameraScanner(val => document.getElementById('putaway_pallet_id').value = val)" 
                                class="px-3.5 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer">
                            Kamera
                        </button>
                    </div>

                    <div class="pt-2">
                        <label class="block text-[11px] text-slate-400 mb-1">Daftar Palet In-Transit:</label>
                        <select onchange="document.getElementById('putaway_pallet_id').value = this.value" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-200 text-xs">
                            <option value="">-- Pilih Palet In Transit --</option>
                            @foreach($pallets->where('status', 'in_transit') as $plt)
                                <option value="{{ $plt->id }}">
                                    Palet: {{ $plt->id }} - {{ $plt->activeBoxes->first() ? $plt->activeBoxes->first()->product->name : 'Palet Kosong' }} ({{ $plt->activeBoxes->sum('qty') }} unit)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1">
                        Scan Barcode BIN / Rak Tujuan <span class="text-rose-400">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="bin_id" id="putaway_bin_id" required placeholder="e.g. B.10.1" class="w-full p-3.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-sm uppercase">
                        <button type="button" 
                                @click="openCameraScanner(val => document.getElementById('putaway_bin_id').value = val)" 
                                class="px-3.5 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow text-xs font-semibold cursor-pointer">
                            Kamera
                        </button>
                    </div>

                    <div class="pt-2">
                        <label class="block text-[11px] text-slate-400 mb-1">Daftar BIN Tersedia:</label>
                        <select onchange="document.getElementById('putaway_bin_id').value = this.value" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-200 text-xs">
                            <option value="">-- Pilih BIN Tujuan --</option>
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

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition">
                    Simpan Putaway ke Rak
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI INBOUND                                                 -->
    <!-- ========================================================================= -->
    <div x-show="showConfirmModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 modal-backdrop-blur overflow-y-auto pb-20 sm:pb-4"
         style="background-color: rgba(15, 23, 42, 0.45) !important; backdrop-filter: blur(8px) !important; -webkit-backdrop-filter: blur(8px) !important;">
        
        <div @click.away="showConfirmModal = false"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 sm:p-6 shadow-2xl space-y-4 my-auto relative">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/80 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg font-bold shadow-sm">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">Konfirmasi Penerimaan Barang</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pastikan detail fisik barang sudah sesuai:</p>
                    </div>
                </div>
                <button type="button" 
                        @click="showConfirmModal = false" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Structured Detail List (High Contrast Light & Dark) -->
            <div class="bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 rounded-xl p-3.5 divide-y divide-slate-200/80 dark:divide-slate-800/80 space-y-2.5 text-xs">
                <!-- Palet Target -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Palet Target</span>
                    <span class="font-mono font-bold text-xs px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 rounded-lg" x-text="form.pallet_id"></span>
                </div>

                <!-- Status Palet -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Status Palet</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="palletStatusLabel"></span>
                </div>

                <!-- Produk SKU & Name -->
                <div class="flex items-start justify-between pt-2">
                    <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0 mr-2">Produk</span>
                    <div class="text-right">
                        <div class="font-bold text-slate-900 dark:text-white truncate max-w-[200px]" x-text="selectedProductName"></div>
                        <div class="font-mono text-[10px] text-slate-500 dark:text-slate-400" x-text="form.product_id"></div>
                    </div>
                </div>

                <!-- Jumlah Input & Konversi Satuan Dasar -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Jumlah Penerimaan</span>
                    <div class="text-right">
                        <div class="font-extrabold text-sm text-indigo-600 dark:text-indigo-400" x-text="form.qty + ' ' + form.unit"></div>
                        <div class="text-[11px] font-semibold text-sky-600 dark:text-sky-400" x-text="'Setara: ' + calculatedBaseQty + ' ' + currentBaseUnit"></div>
                    </div>
                </div>

                <!-- Expired Date (ED) -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Expired Date (ED)</span>
                    <span class="font-mono font-bold text-xs text-slate-800 dark:text-slate-200 px-2 py-0.5 bg-white dark:bg-slate-800 rounded border border-slate-200 dark:border-slate-700" x-text="form.expired_date"></span>
                </div>

                <!-- Keterangan / Notes (Optional) -->
                <div class="flex items-start justify-between pt-2" x-show="form.notes">
                    <span class="text-slate-500 dark:text-slate-400 font-medium shrink-0 mr-2">Catatan / PO</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300 text-right max-w-[200px]" x-text="form.notes"></span>
                </div>

                <!-- Lokasi Simpan / Rak BIN -->
                <div class="flex items-center justify-between pt-2">
                    <span class="text-slate-500 dark:text-slate-400 font-medium">Lokasi Simpan</span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold"
                          :class="form.bin_id ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700'"
                          x-text="form.bin_id ? ('Rak BIN: ' + form.bin_id) : 'Status IN TRANSIT (Belum di Rak)'"></span>
                </div>
            </div>

            <!-- Notice Banner In Transit -->
            <div x-show="!form.bin_id" class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 rounded-xl flex items-start gap-2.5 text-xs">
                <i class="fa-solid fa-circle-info text-amber-600 dark:text-amber-400 text-sm mt-0.5 shrink-0"></i>
                <div class="text-amber-900 dark:text-amber-200 text-[11px] leading-relaxed">
                    Palet akan disimpan dengan status <strong>In Transit</strong> (berada di dock/staging). Anda dapat memindahkannya ke rak fisik sewaktu-waktu melalui tab <em>"Putaway Palet Transit ke Rak"</em>.
                </div>
            </div>

            <!-- Modal Action Buttons (Mobile Thumb Friendly) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" 
                        @click="showConfirmModal = false" 
                        class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs sm:text-sm rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer active:scale-95 text-center">
                    Batal
                </button>

                <button type="button" 
                        @click="submitInboundForm()" 
                        class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-600/30 transition cursor-pointer active:scale-95 flex items-center justify-center gap-1.5 text-center">
                    <i class="fa-solid fa-check"></i>
                    <span>Iya, Simpan</span>
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
                brand: "{{ addslashes($p->brand ?? '') }}",
                uom_inventory: "{{ $p->uom_inventory ?: 'Box' }}",
                conversions: {
                    "{{ $p->uom_inventory ?: 'Box' }}": { uom_convert: "{{ $p->uom_inventory ?: 'Box' }}", qty_convert: 1, uom_base: "{{ $p->uom_inventory ?: 'Box' }}" },
                    @foreach($p->conversions as $c)
                        "{{ $c->uom_convert }}": { uom_convert: "{{ $c->uom_convert }}", qty_convert: {{ $c->qty_convert }}, uom_base: "{{ $c->uom_base ?: ($p->uom_inventory ?: 'Box') }}" },
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

    function inboundV2Workflow() {
        const initialProdId = '{{ $products->first()->id ?? "" }}';
        const initialProd = productsMap[initialProdId] || { uom_inventory: 'Box', conversions: {} };

        return {
            activeMode: 'inbound_flow',
            currentStep: 1,
            showConfirmModal: false,

            stepTitles: {
                1: 'Scan Barcode Palet',
                2: 'Pilih Produk & Varian',
                3: 'Input Qty, ED & Notes',
                4: 'Penempatan Rak (Opsional) & Simpan'
            },

            form: {
                pallet_id: 'PLT-' + Math.floor(1000 + Math.random() * 9000),
                pallet_type_id: '{{ $palletTypes->first()->id ?? "wooden" }}',
                max_capacity: 72,
                product_id: initialProdId,
                box_id: '',
                qty: 1,
                unit: initialProd.uom_inventory || 'Box',
                expired_date: '{{ date("Y-m-d", strtotime("+1 year")) }}',
                notes: '',
                machine_code: 'MC-01',
                shift: '{{ auth()->user()->shift ?? 1 }}',
                bin_id: ''
            },

            palletInfo: {
                is_new: true,
                pallet_status: 'in_transit',
                max_capacity: 72,
                total_qty: 0,
                active_product_id: null,
                active_product_name: null,
                boxes: []
            },

            availableConversions: initialProd.conversions || {},
            currentBaseUnit: initialProd.uom_inventory || 'Box',

            get currentStepTitle() {
                return this.stepTitles[this.currentStep] || '';
            },

            get selectedProductName() {
                const p = productsMap[this.form.product_id];
                return p ? p.name : (this.form.product_id || 'Belum dipilih');
            },

            get palletStatusLabel() {
                if (this.palletInfo.is_new) return 'Palet Baru (Status: In Transit)';
                if (this.palletInfo.pallet_status === 'on_rack') return 'Palet di Rak (BIN: ' + (this.palletInfo.bin_id || '-') + ')';
                return 'Palet In Transit (Belum di Rak)';
            },

            get calculatedBaseQty() {
                const conv = this.availableConversions[this.form.unit];
                const factor = conv ? parseFloat(conv.qty_convert) : 1;
                return (parseFloat(this.form.qty) || 0) * factor;
            },

            get totalForecastQty() {
                const existing = parseFloat(this.palletInfo.total_qty) || 0;
                return existing + this.calculatedBaseQty;
            },

            get forecastPercentage() {
                const cap = parseFloat(this.form.max_capacity) || 72;
                if (cap <= 0) return 0;
                return Math.round((this.totalForecastQty / cap) * 100);
            },

            get remainingCapacity() {
                const cap = parseFloat(this.form.max_capacity) || 72;
                return Math.round((cap - this.totalForecastQty) * 100) / 100;
            },

            initSelectSearchAll() {
                this.$nextTick(() => {
                    initTomSelect('select_existing_pallet', val => {
                        this.form.pallet_id = val;
                        this.checkPalletInfo();
                    });

                    initTomSelect('select_product_id', val => {
                        this.onProductSelect(val);
                    });

                    this.onProductChange();
                });
            },

            switchMode(mode) {
                this.activeMode = mode;
                this.$nextTick(() => {
                    this.initSelectSearchAll();
                });
            },

            goToStep(step) {
                if (step > this.currentStep) {
                    if (this.currentStep === 1 && !this.form.pallet_id) {
                        alert('Harap isi atau scan Barcode Palet terlebih dahulu!');
                        return;
                    }
                    if (this.currentStep === 2 && !this.form.product_id) {
                        alert('Harap pilih atau scan Produk SKU terlebih dahulu!');
                        return;
                    }
                    if (this.currentStep === 3) {
                        if (!this.form.qty || this.form.qty <= 0) {
                            alert('Harap isi Jumlah Qty yang valid!');
                            return;
                        }
                        if (!this.form.expired_date) {
                            alert('Harap isi Tanggal Expired Date!');
                            return;
                        }
                    }
                }

                this.currentStep = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Refresh TomSelect instances if needed
                this.$nextTick(() => {
                    if (step === 2 && tsInstances['select_product_id']) {
                        tsInstances['select_product_id'].sync();
                    }
                });
            },

            nextStep() {
                if (this.currentStep < 4) {
                    this.goToStep(this.currentStep + 1);
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.goToStep(this.currentStep - 1);
                }
            },

            checkPalletInfo() {
                const pltId = (this.form.pallet_id || '').trim();
                if (!pltId) return;

                fetch('/api/pallet/' + encodeURIComponent(pltId) + '/info')
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            this.palletInfo = data;
                            if (data.max_capacity) {
                                this.form.max_capacity = data.max_capacity;
                            }
                            if (data.pallet_type_id) {
                                this.form.pallet_type_id = data.pallet_type_id;
                            }
                            if (data.bin_id) {
                                this.form.bin_id = data.bin_id;
                                if (tsInstances['select_inbound_bin']) {
                                    tsInstances['select_inbound_bin'].setValue(data.bin_id, true);
                                }
                            }

                            // Ketentuan 1: Jika palet sudah memiliki produk aktif, kunci produk
                            if (data.active_product_id) {
                                this.form.product_id = data.active_product_id;
                                if (tsInstances['select_product_id']) {
                                    tsInstances['select_product_id'].setValue(data.active_product_id, true);
                                    tsInstances['select_product_id'].disable();
                                }
                                this.onProductChange();
                            } else {
                                if (tsInstances['select_product_id']) {
                                    tsInstances['select_product_id'].enable();
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching pallet info:', err);
                    });
            },

            onProductScanned(scannedVal) {
                const clean = scannedVal.trim();
                let match = productsMap[clean];
                if (!match) {
                    for (let id in productsMap) {
                        if (id.toLowerCase() === clean.toLowerCase() || productsMap[id].name.toLowerCase().includes(clean.toLowerCase())) {
                            match = productsMap[id];
                            this.form.product_id = id;
                            break;
                        }
                    }
                } else {
                    this.form.product_id = clean;
                }

                if (tsInstances['select_product_id']) {
                    tsInstances['select_product_id'].setValue(this.form.product_id, true);
                }
                const manualInput = document.getElementById('product_sku_manual_input');
                if (manualInput) {
                    manualInput.value = this.form.product_id;
                }
                this.onProductChange();
            },

            onProductSelect(val) {
                // Ketentuan 1: check if pallet is locked to a different product
                if (this.palletInfo.active_product_id && val !== this.palletInfo.active_product_id) {
                    alert('Validasi Ditolak: Satu palet saat penerimaan pertama hanya bisa diisi satu macam produk (' + this.palletInfo.active_product_name + '). Gunakan Transfer Antar Palet untuk mencampur produk.');
                    if (tsInstances['select_product_id']) {
                        tsInstances['select_product_id'].setValue(this.palletInfo.active_product_id, true);
                    }
                    this.form.product_id = this.palletInfo.active_product_id;
                    const manualInput = document.getElementById('product_sku_manual_input');
                    if (manualInput) manualInput.value = this.form.product_id;
                    return;
                }

                this.form.product_id = val;
                const manualInput = document.getElementById('product_sku_manual_input');
                if (manualInput) manualInput.value = val;
                this.onProductChange();
            },

            onProductChange() {
                const prod = productsMap[this.form.product_id];
                if (prod) {
                    this.availableConversions = prod.conversions || {};
                    this.currentBaseUnit = prod.uom_inventory || 'Box';
                    const uomKeys = Object.keys(this.availableConversions);
                    if (!this.availableConversions[this.form.unit]) {
                        this.form.unit = uomKeys[0] || prod.uom_inventory;
                    }

                    // Sync default unit selection
                    this.$nextTick(() => {
                        const unitEl = document.getElementById('select_unit');
                        if (unitEl) {
                            unitEl.value = this.form.unit;
                        }
                    });
                }
            },

            triggerSubmitInbound(requireRack) {
                if (!this.form.pallet_id) {
                    alert('Mohon isi / scan Barcode Palet terlebih dahulu!');
                    this.goToStep(1);
                    return;
                }
                if (!this.form.product_id) {
                    alert('Mohon pilih / scan Produk terlebih dahulu!');
                    this.goToStep(2);
                    return;
                }
                if (!this.form.qty || this.form.qty <= 0) {
                    alert('Mohon isi Jumlah Qty Masuk yang valid!');
                    this.goToStep(3);
                    return;
                }

                if (requireRack) {
                    if (!this.form.bin_id) {
                        alert('Untuk aksi "Simpan & Tempatkan ke Rak", mohon scan atau pilih Barcode Rak/BIN!');
                        return;
                    }
                } else {
                    this.form.bin_id = '';
                }

                if (!this.form.box_id) {
                    this.form.box_id = 'BOX-' + Math.floor(100000 + Math.random() * 900000);
                }

                this.showConfirmModal = true;
            },

            resetInboundForm() {
                this.currentStep = 1;
                this.form.pallet_id = 'PLT-' + Math.floor(1000 + Math.random() * 9000);
                this.form.box_id = '';
                this.form.notes = '';
                this.form.bin_id = '';
                this.palletInfo = {
                    is_new: true,
                    pallet_status: 'in_transit',
                    max_capacity: 72,
                    total_qty: 0,
                    active_product_id: null,
                    active_product_name: null,
                    boxes: []
                };
                if (tsInstances['select_product_id']) tsInstances['select_product_id'].enable();
                if (tsInstances['select_existing_pallet']) tsInstances['select_existing_pallet'].clear();
                this.showConfirmModal = false;
            },

            submitInboundForm() {
                document.getElementById('inboundV2Form').submit();
            }
        }
    }
</script>
@endsection
@endsection
