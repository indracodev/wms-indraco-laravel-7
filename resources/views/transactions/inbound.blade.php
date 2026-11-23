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
        background-color: #004b87 !important;
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
        background-color: #004b87 !important;
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

    /* CRITICAL OVERRIDE: All text inside #004b87 containers and colored buttons MUST be white */
    [style*="#004b87"],
    [style*="#004b87"] *,
    html.light [style*="#004b87"],
    html.light [style*="#004b87"] *,
    html.light [style*="#004b87"] .font-extrabold,
    html.light [style*="#004b87"] .font-bold,
    html.light [style*="#004b87"] .font-black,
    html.light [style*="#004b87"] strong,
    html.light [style*="#004b87"] span,
    html.light [style*="#004b87"] div,
    button.bg-indigo-600,
    button.bg-indigo-600 *,
    html.light button.bg-indigo-600,
    html.light button.bg-indigo-600 *,
    html.light .btn-primary,
    html.light .btn-primary * {
        color: #ffffff !important;
    }

    /* Exceptions for white elements/buttons inside #004b87 */
    [style*="#004b87"] .bg-white,
    [style*="#004b87"] .bg-white *,
    [style*="#004b87"] [style*="background-color: #ffffff"],
    [style*="#004b87"] [style*="background-color: #ffffff"] *,
    html.light [style*="#004b87"] .bg-white,
    html.light [style*="#004b87"] .bg-white *,
    html.light [style*="#004b87"] [style*="background-color: #ffffff"],
    html.light [style*="#004b87"] [style*="background-color: #ffffff"] * {
        color: #004b87 !important;
    }

    [style*="#004b87"] .text-sky-100, html.light [style*="#004b87"] .text-sky-100 {
        color: #e0f2fe !important;
    }
    [style*="#004b87"] .text-sky-200, html.light [style*="#004b87"] .text-sky-200 {
        color: #bae6fd !important;
    }
    [style*="#004b87"] .text-amber-300, html.light [style*="#004b87"] .text-amber-300 {
        color: #fcd34d !important;
    }
    [style*="#004b87"] .text-amber-400, html.light [style*="#004b87"] .text-amber-400 {
        color: #fbbf24 !important;
    }
    html.light [style*="#004b87"] .text-sky-300 {
        color: #7dd3fc !important;
    }

    /* Yellow / Amber alert cards must always have dark/black text */
    [style*="#fef3c7"],
    [style*="#fef3c7"] *,
    .bg-amber-50, .bg-amber-50 *,
    .bg-amber-100, .bg-amber-100 *,
    html.light .bg-amber-500\/10, html.light .bg-amber-500\/10 *,
    html.light .bg-amber-500\/20, html.light .bg-amber-500\/20 * {
        color: #000000 !important;
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
            <span>Alur Penerimaan Barang</span>
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
                        :class="currentStep === 1 ? 'bg-indigo-600 shadow-md shadow-indigo-600/30' : (currentStep > 1 ? 'bg-slate-700/80 hover:bg-slate-700' : 'bg-slate-700/80 hover:bg-slate-700')"
                        style="color: #ffffff !important;">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :style="currentStep === 1 ? 'color: #4338ca !important; background-color: #ffffff !important;' : 'color: #ffffff !important; background-color: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;'">
                        <template x-if="currentStep > 1"><span style="color: #ffffff !important;">✓</span></template>
                        <template x-if="currentStep <= 1"><span :style="currentStep === 1 ? 'color: #4338ca !important;' : 'color: #ffffff !important;'">1</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" style="color: #ffffff !important;">Palet</div>
                        <div class="hidden md:block text-[9px]" style="color: #e2e8f0 !important;">Scan Barcode</div>
                    </div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" 
                        @click="goToStep(2)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 2 ? 'bg-indigo-600 shadow-md shadow-indigo-600/30' : (currentStep > 2 ? 'bg-slate-700/80 hover:bg-slate-700' : 'bg-slate-700/80 hover:bg-slate-700')"
                        style="color: #ffffff !important;">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :style="currentStep === 2 ? 'color: #4338ca !important; background-color: #ffffff !important;' : 'color: #ffffff !important; background-color: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;'">
                        <template x-if="currentStep > 2"><span style="color: #ffffff !important;">✓</span></template>
                        <template x-if="currentStep <= 2"><span :style="currentStep === 2 ? 'color: #4338ca !important;' : 'color: #ffffff !important;'">2</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" style="color: #ffffff !important;">Produk</div>
                        <div class="hidden md:block text-[9px]" style="color: #e2e8f0 !important;">Pilih SKU Varian</div>
                    </div>
                </button>

                <!-- Step 3 Tab -->
                <button type="button" 
                        @click="goToStep(3)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 3 ? 'bg-indigo-600 shadow-md shadow-indigo-600/30' : (currentStep > 3 ? 'bg-slate-700/80 hover:bg-slate-700' : 'bg-slate-700/80 hover:bg-slate-700')"
                        style="color: #ffffff !important;">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :style="currentStep === 3 ? 'color: #4338ca !important; background-color: #ffffff !important;' : 'color: #ffffff !important; background-color: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;'">
                        <template x-if="currentStep > 3"><span style="color: #ffffff !important;">✓</span></template>
                        <template x-if="currentStep <= 3"><span :style="currentStep === 3 ? 'color: #4338ca !important;' : 'color: #ffffff !important;'">3</span></template>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" style="color: #ffffff !important;">Detail</div>
                        <div class="hidden md:block text-[9px]" style="color: #e2e8f0 !important;">Qty, ED & Notes</div>
                    </div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" 
                        @click="goToStep(4)" 
                        class="flex flex-col sm:flex-row items-center justify-center p-2 rounded-xl transition cursor-pointer"
                        :class="currentStep === 4 ? 'bg-indigo-600 shadow-md shadow-indigo-600/30' : 'bg-slate-700/80 hover:bg-slate-700'"
                        style="color: #ffffff !important;">
                    <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-black mr-0 sm:mr-2 mb-1 sm:mb-0 shadow-sm"
                          :style="currentStep === 4 ? 'color: #4338ca !important; background-color: #ffffff !important;' : 'color: #ffffff !important; background-color: rgba(255, 255, 255, 0.25) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;'">
                        <span :style="currentStep === 4 ? 'color: #4338ca !important;' : 'color: #ffffff !important;'">4</span>
                    </span>
                    <div class="text-center sm:text-left">
                        <div class="text-[10px] sm:text-xs font-bold leading-tight" style="color: #ffffff !important;">Simpan Rak</div>
                        <div class="hidden md:block text-[9px]" style="color: #e2e8f0 !important;">In Transit / Rak</div>
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
                                   style="background-color: #facc15 !important; border: 1.5px solid #eab308 !important; color: #000000 !important;"
                                   class="w-full py-2.5 px-3 hover:bg-yellow-400 rounded-xl flex items-center justify-center text-xs font-bold transition active:scale-95 shadow-sm">
                                    <span style="color: #000000 !important; font-weight: 800;">Buka Scanner Simulator Barcode Bar</span>
                                </a>
                            </div>

                            <!-- Method 1 (Search Dropdown): Select Search Palet Terdaftar (Desktop) -->
                            <div class="hidden md:block pt-1.5">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-semibold text-slate-400">Atau Cari & Pilih dari Dropdown (Select Search):</label>
                                    <span class="text-[10px] text-slate-500">Cari dari 11.000+ palet</span>
                                </div>
                                <select id="select_existing_pallet" class="w-full">
                                    <option value="">Ketik untuk mencari palet (e.g. K125, PLS, LOS)...</option>
                                    @foreach($pallets as $plt)
                                        <option value="{{ $plt->id }}">
                                            Palet: {{ $plt->id }} ({{ $plt->bin_id ? 'Rak ' . $plt->bin_id : 'In Transit' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Method 1 (Mobile Search Modal Trigger): Khusus Mobile -->
                            <div class="md:hidden pt-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1.5">Atau Cari & Pilih Palet Terdaftar:</label>
                                <button type="button" 
                                        @click.stop="openMobilePalletModal()" 
                                        style="background-color: #004b87 !important; border: 2px solid #003661 !important;"
                                        class="w-full p-3.5 rounded-xl flex items-center justify-between text-left text-xs transition active:scale-[0.99] shadow-lg cursor-pointer">
                                    <div class="truncate mr-3">
                                        <template x-if="form.pallet_id">
                                            <span class="text-white font-mono font-black text-sm tracking-wide truncate" x-text="'Palet: ' + form.pallet_id"></span>
                                        </template>
                                        <template x-if="!form.pallet_id">
                                            <span class="text-sky-200 text-xs font-semibold italic">anda belum menentukan palet</span>
                                        </template>
                                    </div>
                                    <span style="background-color: #ffffff !important; color: #004b87 !important;" 
                                          class="px-4 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow-md hover:bg-sky-50 active:scale-95 transition">
                                        <i class="fa-solid fa-magnifying-glass text-xs" style="color: #004b87 !important;"></i>
                                        <span style="color: #004b87 !important;">Cari</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="max_capacity" x-model="form.max_capacity">
                    <input type="hidden" name="pallet_type_id" x-model="form.pallet_type_id">

                    <!-- Alert Ketentuan 1: Palet Terkunci 1 Produk -->
                    <div x-show="palletInfo.active_product_id" 
                         style="background-color: #fef3c7 !important; border: 1.5px solid #f59e0b !important; color: #000000 !important;"
                         class="p-3.5 rounded-xl space-y-1 shadow-sm text-slate-900">
                        <div class="flex items-center justify-between text-xs font-bold" style="color: #000000 !important;">
                            <span class="font-extrabold" style="color: #000000 !important;">Ketentuan 1: Palet Sudah Berisi Produk</span>
                            <span class="font-mono text-[11px] font-bold" style="color: #000000 !important;" x-text="'SKU: ' + palletInfo.active_product_id"></span>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: #000000 !important;">
                            Palet ini sudah memiliki muatan produk <strong class="font-black" style="color: #000000 !important;" x-text="palletInfo.active_product_name"></strong>. Saat penerimaan awal, hanya produk yang sama yang dapat ditambahkan ke palet ini.
                        </p>
                    </div>

                    <!-- Multi-ED Breakdown Display (Ketentuan 6) -->
                    <div x-show="palletInfo.boxes && palletInfo.boxes.length > 0" class="pt-2 border-t border-slate-800">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">
                            Daftar Batch Expired Date di Palet Ini (Multi-ED Aktif):
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="box in palletInfo.boxes" :key="box.id">
                                <div style="background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #cbd5e1 !important;"
                                     class="p-2.5 rounded-xl text-xs space-y-1 shadow-sm">
                                    <div class="flex justify-between items-center text-[11px]">
                                        <span class="font-mono font-bold" style="color: #00274d !important;" x-text="box.id"></span>
                                        <span class="font-black" style="color: #0284c7 !important;" x-text="box.qty + ' ' + box.uom_base"></span>
                                    </div>
                                    <div class="text-[11px] font-bold" style="color: #d97706 !important;">
                                        ED: <span class="font-black" x-text="box.expired_date"></span>
                                    </div>
                                    <div class="text-[10px] truncate" style="color: #64748b !important;" x-show="box.notes" x-text="'Ket: ' + box.notes"></div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Frame 1 (Thumb-friendly full width on mobile) -->
                <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row sm:justify-end gap-2">
                    <button type="button" 
                            @click="nextStep()" 
                            class="w-full sm:w-auto px-8 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center justify-center active:scale-95 cursor-pointer">
                        <span>Next</span>
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
                
                <!-- Pallet Context Summary Banner (#004b87 Background with White Text) -->
                <div class="p-3.5 rounded-xl flex items-center justify-between text-xs shadow-md"
                     style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
                    <div class="text-white">
                        <span class="text-sky-100 font-medium">Palet Terpilih:</span>
                        <strong class="font-mono font-black text-sm ml-1 text-white" x-text="form.pallet_id"></strong>
                        <span class="text-sky-200 ml-1.5 font-semibold" x-text="'(' + palletStatusLabel + ')'"></span>
                    </div>
                    <button type="button" @click="goToStep(1)" class="px-3 py-1.5 bg-white text-[#004b87] rounded-lg text-xs font-black shadow hover:bg-sky-50 transition active:scale-95">Ubah Palet</button>
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
                                   style="background-color: #facc15 !important; border: 1.5px solid #eab308 !important; color: #000000 !important;"
                                   class="w-full py-2.5 px-3 hover:bg-yellow-400 rounded-xl flex items-center justify-center text-xs font-bold transition active:scale-95 shadow-sm">
                                    <span style="color: #000000 !important; font-weight: 800;">Buka Scanner Simulator Barcode Bar</span>
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

                    <!-- Selected Product Details Card (#004b87 Background with White Text) -->
                    <div class="p-4 rounded-xl space-y-2.5 shadow-md"
                         style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
                        <div class="text-xs font-bold text-sky-100 uppercase tracking-wider">
                            Rincian Produk Terpilih:
                        </div>
                        <div class="text-base font-extrabold text-white" style="color: #ffffff !important;" x-text="selectedProductName"></div>
                        
                        <!-- Varian Satuan UOM Pills -->
                        <div class="pt-2.5 border-t border-sky-600/50 space-y-2">
                            <div class="text-xs text-sky-100 font-semibold">Varian Satuan Tersedia:</div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="(conv, uomName) in availableConversions" :key="uomName">
                                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm"
                                          :style="uomName === form.unit ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'">
                                        <span x-text="uomName"></span>
                                        <span class="text-[10px] opacity-80" x-text="'(x' + conv.qty_convert + ' ' + conv.uom_base + ')'"></span>
                                    </span>
                                </template>
                            </div>
                            <div class="text-xs text-sky-100 pt-1 font-medium">
                                Satuan Terkecil (Base): <strong class="text-white font-extrabold" style="color: #ffffff !important;" x-text="currentBaseUnit"></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Frame 2 -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-3">
                    <button type="button" 
                            @click="prevStep()" 
                            style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                            class="px-6 py-3 hover:bg-slate-100 text-xs sm:text-sm font-black rounded-xl shadow-md transition active:scale-95">
                        Back
                    </button>
                    <button type="button" 
                            @click="nextStep()" 
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition active:scale-95 cursor-pointer">
                        <span>Next</span>
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
                
                <!-- Pallet & Product Summary Context (#004b87 Background with White Text) -->
                <div class="p-3.5 rounded-xl flex flex-wrap items-center justify-between gap-2 text-xs shadow-md"
                     style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
                    <div class="text-white" style="color: #ffffff !important;">
                        <span class="text-sky-100 font-medium" style="color: #e0f2fe !important;">Palet:</span>
                        <strong class="font-mono font-black text-sm ml-1 text-white" style="color: #ffffff !important;" x-text="form.pallet_id"></strong>
                        <span class="text-sky-200 mx-1.5 font-bold" style="color: #bae6fd !important;">|</span>
                        <span class="text-sky-100 font-medium" style="color: #e0f2fe !important;">Produk:</span>
                        <strong class="text-white font-extrabold ml-1" style="color: #ffffff !important;" x-text="selectedProductName"></strong>
                    </div>
                    <button type="button" @click="goToStep(2)" style="background-color: #ffffff !important; color: #004b87 !important;" class="px-3 py-1.5 rounded-lg text-xs font-black shadow hover:bg-sky-50 transition active:scale-95">Ubah Produk</button>
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
                                            class="p-3 min-h-[58px] rounded-xl border-2 text-left transition-all active:scale-95 cursor-pointer flex flex-col justify-between shadow-sm"
                                            :style="form.unit === uomName 
                                                ? 'background-color: #004b87 !important; color: #ffffff !important; border-color: #003661 !important;' 
                                                : 'background-color: #ffffff !important; color: #004b87 !important; border-color: #cbd5e1 !important;'">
                                        <div class="flex items-center justify-between">
                                            <span class="font-black text-sm tracking-wide" 
                                                  :style="form.unit === uomName ? 'color: #ffffff !important;' : 'color: #00274d !important;'" 
                                                  x-text="uomName"></span>
                                            <span class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px]"
                                                  :style="form.unit === uomName ? 'border-color: #ffffff !important; background-color: #ffffff !important;' : 'border-color: #004b87 !important; background-color: transparent !important;'">
                                                <span x-show="form.unit === uomName" style="color: #004b87 !important; font-weight: 900;">✓</span>
                                            </span>
                                        </div>
                                        <div class="text-[11px] mt-1 font-semibold" 
                                              :style="form.unit === uomName ? 'color: #e0f2fe !important;' : 'color: #004b87 !important;'">
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

                    <!-- RINGKASAN MUATAN PALET (#004b87 Background with White Text) -->
                    <div class="p-4 rounded-xl space-y-2 mt-2 shadow-md"
                         style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-extrabold text-white text-xs" style="color: #ffffff !important;">Ringkasan Muatan Palet Ini:</span>
                            <span class="font-mono font-black text-sm text-sky-200" style="color: #bae6fd !important;" x-text="totalForecastQty + ' ' + currentBaseUnit + ' Total'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs text-sky-100 pt-2 border-t border-sky-600/50" style="color: #e0f2fe !important;">
                            <div style="color: #e0f2fe !important;">Muatan sebelumnya: <strong class="text-white font-extrabold" style="color: #ffffff !important;" x-text="(palletInfo.total_qty || 0) + ' ' + currentBaseUnit"></strong></div>
                            <div style="color: #e0f2fe !important;">Tambahan input: <strong class="text-white font-black" style="color: #ffffff !important;" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></strong></div>
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
                            <span class="font-extrabold text-white" style="color: #ffffff !important;">Next</span>
                            <span class="text-[10px] text-indigo-100 font-normal mt-0.5" style="color: #e0e7ff !important;">Pilih / scan barcode rak fisik</span>
                        </button>
                    </div>

                    <div class="pt-2 space-y-1.5">
                        <div class="text-xs font-bold" style="color: #ef4444 !important;">
                            * Palet In Transit dapat dipindah ke rak kapan saja
                        </div>
                        <div class="flex items-center justify-start">
                            <button type="button" 
                                    @click="prevStep()" 
                                    style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                                    class="px-6 py-2.5 hover:bg-slate-100 text-xs sm:text-sm font-black rounded-xl shadow-md transition active:scale-95">
                                Back
                            </button>
                        </div>
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
                <div style="background-color: #fef3c7 !important; border: 1.5px solid #f59e0b !important; color: #000000 !important;"
                     class="p-3 rounded-xl flex items-start gap-2.5 text-xs shadow-sm">
                    <span style="background-color: #fde68a !important; color: #000000 !important; border: 1px solid #d97706 !important;"
                          class="px-2 py-0.5 font-black rounded text-[10px] tracking-wide uppercase shrink-0 mt-0.5">
                        In Transit
                    </span>
                    <div style="color: #000000 !important;" class="text-[11px] leading-relaxed">
                        Jika palet belum diangkut forklift ke rak, Anda dapat langsung menekan tombol <strong style="color: #000000 !important;">Simpan Sementara (In Transit)</strong>. Palet tetap tersimpan resmi dan dapat dipindahkan ke rak kapan saja melalui menu <em>"Putaway Palet Transit ke Rak"</em>.
                    </div>
                </div>

                <!-- RINGKASAN REVIEW TRANSAKSI (#004b87 Background with White Text) -->
                <div class="p-4 rounded-xl space-y-3 text-xs shadow-md"
                     style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
                    <div class="font-extrabold text-white uppercase tracking-wider text-xs pb-1.5 border-b border-sky-600/50" style="color: #ffffff !important;">
                        Ringkasan Penerimaan Barang:
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Palet Target:</span>
                            <div class="font-mono font-black text-white text-base tracking-wide" style="color: #ffffff !important;" x-text="form.pallet_id"></div>
                        </div>
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Status Palet:</span>
                            <div class="font-bold text-sky-200" style="color: #bae6fd !important;" x-text="palletStatusLabel"></div>
                        </div>
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Produk SKU:</span>
                            <div class="font-bold text-white truncate" style="color: #ffffff !important;" x-text="selectedProductName"></div>
                        </div>
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Jumlah Input:</span>
                            <div class="font-black text-amber-300 text-sm" style="color: #fcd34d !important;" x-text="form.qty + ' ' + form.unit"></div>
                        </div>
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Setara Satuan Dasar:</span>
                            <div class="font-bold text-sky-200" style="color: #bae6fd !important;" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></div>
                        </div>
                        <div>
                            <span class="text-sky-100 font-semibold" style="color: #e0f2fe !important;">Expired Date:</span>
                            <div class="font-bold text-white" style="color: #ffffff !important;" x-text="form.expired_date"></div>
                        </div>
                    </div>
                    <div class="pt-2 text-xs text-sky-100 border-t border-sky-600/40" style="color: #e0f2fe !important;" x-show="form.notes">
                        Catatan: <span class="text-white font-semibold" style="color: #ffffff !important;" x-text="form.notes"></span>
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
                                    @php $count = $b->pallets_count ?? 0; @endphp
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
                                @php $count = $b->pallets_count ?? 0; @endphp
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

                    <div x-show="!form.bin_id" 
                         style="background-color: #004b87 !important; border: 1.5px solid #003661 !important;"
                         class="p-3.5 rounded-xl text-xs text-white shadow-sm flex items-start gap-2.5">
                        <i class="fa-solid fa-circle-info text-sky-300 text-sm mt-0.5 shrink-0"></i>
                        <div class="text-sky-100 text-xs leading-relaxed">
                            <strong class="text-white">Catatan:</strong> Jika barang masih berada di area bongkar muat dan belum diangkut forklift ke rak, Anda dapat langsung menekan tombol <strong class="text-white">Simpan Sementara (In Transit)</strong>.
                        </div>
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
                                style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                                class="px-6 py-2.5 hover:bg-slate-100 text-xs sm:text-sm font-black rounded-xl shadow-md transition active:scale-95">
                            Back
                        </button>
                        <button type="button" 
                                @click="resetInboundForm()" 
                                class="text-xs text-slate-500 hover:text-rose-600 font-bold underline cursor-pointer">
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
                            @foreach($putawayPallets as $plt)
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
                                @php $count = $b->pallets_count ?? 0; @endphp
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
            <div x-show="!form.bin_id" 
                 style="background-color: #fef3c7 !important; border: 1.5px solid #f59e0b !important; color: #000000 !important;"
                 class="p-3 rounded-xl flex items-start gap-2.5 text-xs shadow-sm">
                <i class="fa-solid fa-circle-info text-amber-700 text-sm mt-0.5 shrink-0"></i>
                <div style="color: #000000 !important;" class="text-[11px] leading-relaxed">
                    Palet akan disimpan dengan status <strong style="color: #000000 !important;">In Transit</strong> (berada di dock/staging). Anda dapat memindahkannya ke rak fisik sewaktu-waktu melalui tab <em>"Putaway Palet Transit ke Rak"</em>.
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

<!-- ========================================================================= -->
<!-- MODAL PENCARIAN PALET MOBILE (OPTIMAL & FAST)                             -->
<!-- ========================================================================= -->
<div x-show="showMobilePalletModal" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed inset-0 z-50 flex flex-col justify-end sm:justify-center p-0 sm:p-4 bg-slate-950/80 backdrop-blur-md" 
     style="background-color: rgba(15, 23, 42, 0.7) !important; backdrop-filter: blur(8px) !important; -webkit-backdrop-filter: blur(8px) !important;">
    <div class="w-full sm:max-w-md h-[90vh] sm:h-[80vh] bg-slate-900 border border-slate-700 rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col overflow-hidden" 
         @click.away="showMobilePalletModal = false">
        
        <!-- Header Modal Mobile (#004b87 Header with White Text) -->
        <div class="p-4 sticky top-0 z-10 space-y-3" style="background-color: #004b87 !important; border-bottom: 2px solid #003661 !important;">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base shadow-inner">
                        <i class="fa-solid fa-pallet"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-white">Cari Palet Terdaftar</h3>
                        <p class="text-[11px] text-sky-100 font-medium">Database 11.000+ Master Palet Storage</p>
                    </div>
                </div>
                <button type="button" 
                        @click="showMobilePalletModal = false" 
                        style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;"
                        class="w-8 h-8 rounded-full hover:bg-white/30 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Input Pencarian Real-Time Mobile -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="mobileSearchQuery" 
                       @input.debounce.200ms="searchPalletsMobile()" 
                       id="mobile_pallet_search_input"
                       placeholder="Ketik kode (misal: PLS, K125, LOS)..." 
                       style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #bae6fd !important;"
                       class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs font-mono uppercase font-black placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-white shadow-inner">
                <button x-show="mobileSearchQuery" 
                        @click="mobileSearchQuery = ''; searchPalletsMobile()" 
                        type="button" 
                        class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>

            <!-- Quick Filter Chips -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs no-scrollbar">
                <button type="button" 
                        @click="setMobileFilter('')" 
                        :style="!mobileFilterType ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;'"
                        class="px-3 py-1.5 rounded-lg shrink-0 transition cursor-pointer font-extrabold text-xs shadow-sm">
                    Semua
                </button>
                <button type="button" 
                        @click="setMobileFilter('PLS')" 
                        :style="mobileFilterType === 'PLS' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;'"
                        class="px-3 py-1.5 rounded-lg shrink-0 transition cursor-pointer font-extrabold text-xs shadow-sm">
                    Plastik (PLS)
                </button>
                <button type="button" 
                        @click="setMobileFilter('LOS')" 
                        :style="mobileFilterType === 'LOS' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;'"
                        class="px-3 py-1.5 rounded-lg shrink-0 transition cursor-pointer font-extrabold text-xs shadow-sm">
                    Loscam (LOS)
                </button>
                <button type="button" 
                        @click="setMobileFilter('K125')" 
                        :style="mobileFilterType === 'K125' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;'"
                        class="px-3 py-1.5 rounded-lg shrink-0 transition cursor-pointer font-extrabold text-xs shadow-sm">
                    Kayu 125
                </button>
                <button type="button" 
                        @click="setMobileFilter('K135')" 
                        :style="mobileFilterType === 'K135' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.25) !important;'"
                        class="px-3 py-1.5 rounded-lg shrink-0 transition cursor-pointer font-extrabold text-xs shadow-sm">
                    Kayu 135
                </button>
            </div>
        </div>

        <!-- Daftar Hasil Pencarian Palet Mobile (White Cards with Dark Blue Text) -->
        <div class="flex-1 overflow-y-auto p-3.5 space-y-2.5" style="background-color: #f1f5f9 !important;">
            <div x-show="isSearchingMobile" class="py-8 text-center text-slate-600 text-xs font-bold">
                <i class="fa-solid fa-spinner fa-spin text-lg text-[#004b87] mb-2"></i>
                <p>Memuat daftar palet...</p>
            </div>

            <template x-for="item in mobilePalletResults" :key="item.id">
                <div @click="selectMobilePallet(item.id)" 
                     style="background-color: #ffffff !important; border: 1.5px solid #cbd5e1 !important;"
                     class="p-3.5 rounded-xl transition cursor-pointer active:scale-[0.98] flex items-center justify-between shadow-sm hover:border-[#004b87] hover:shadow-md">
                    <div class="space-y-1.5">
                        <div class="flex items-center space-x-2.5">
                            <!-- Pallet Code (Dark Blue Bold) -->
                            <span class="font-mono font-black text-base tracking-wider" style="color: #00274d !important;" x-text="item.id"></span>
                            <!-- Location Badge -->
                            <span class="px-2 py-0.5 rounded-md text-[11px] font-black uppercase tracking-wide border shadow-sm"
                                  :class="item.bin_id ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-amber-100 text-amber-800 border-amber-300'"
                                  x-text="item.location"></span>
                        </div>
                        <!-- Pallet Type Name (Dark Blue Navy) -->
                        <div class="text-xs font-bold" style="color: #004b87 !important;" x-text="item.type_name"></div>
                        <!-- Muatan Info -->
                        <template x-if="item.has_content">
                            <div class="text-xs font-black flex items-center gap-1.5" style="color: #0284c7 !important;">
                                <i class="fa-solid fa-box-open text-[11px]"></i>
                                <span x-text="'Muatan: SKU ' + item.product_id + ' (' + item.total_qty + ' unit)'"></span>
                            </div>
                        </template>
                    </div>
                    <div class="text-right shrink-0 ml-3">
                        <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                              class="px-3.5 py-2 hover:bg-[#003661] rounded-xl text-xs font-black shadow-md flex items-center gap-1.5 transition active:scale-95">
                            <span>Pilih</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </div>
            </template>

            <div x-show="!isSearchingMobile && mobilePalletResults.length === 0" class="py-12 text-center text-slate-500 text-xs font-medium">
                <i class="fa-solid fa-circle-question text-2xl text-slate-400 mb-2"></i>
                <p>Tidak ada palet yang cocok dengan kata kunci pencarian.</p>
            </div>
        </div>

        <!-- Footer Modal Mobile (#004b87 Footer with White Text) -->
        <div class="p-3.5 flex items-center justify-between text-xs" style="background-color: #004b87 !important; border-top: 2px solid #003661 !important;">
            <span class="font-bold text-white" x-text="mobilePalletResults.length + ' palet ditemukan'"></span>
            <button type="button" 
                    @click="showMobilePalletModal = false" 
                    style="background-color: #ffffff !important; color: #004b87 !important;"
                    class="px-5 py-2 rounded-xl font-black text-xs cursor-pointer transition active:scale-95 shadow-md hover:bg-sky-50">
                Tutup
            </button>
        </div>
    </div>
</div>
</div>
@endsection

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
    const palletSearchCache = {};

    function initPalletTomSelect(id, onChangeCallback) {
        const el = document.getElementById(id);
        if (!el) return null;

        if (tsInstances[id]) {
            tsInstances[id].destroy();
        }

        const ts = new TomSelect(el, {
            valueField: 'id',
            labelField: 'label',
            searchField: ['id', 'label', 'type_name'],
            maxOptions: 50,
            loadThrottle: 200,
            placeholder: 'Ketik untuk mencari palet (e.g. K125, PLS, LOS)...',
            load: function(query, callback) {
                query = (query || '').trim().toUpperCase();
                if (palletSearchCache[query]) {
                    return callback(palletSearchCache[query]);
                }
                const url = '{{ route("transaction.inbound.search_pallets") }}?q=' + encodeURIComponent(query);
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        palletSearchCache[query] = data;
                        callback(data);
                    })
                    .catch(() => callback());
            },
            render: {
                option: function(data, escape) {
                    const statusClass = data.bin_id ? 'text-emerald-400' : 'text-amber-400';
                    const hasBox = data.has_content ? '<span class="text-sky-300 ml-1.5 font-semibold text-[10px] bg-sky-950 px-1 py-0.5 rounded">Muatan: ' + escape(data.product_id) + '</span>' : '';
                    return '<div class="py-1.5 px-2.5 flex items-center justify-between text-xs hover:bg-indigo-950/40">' +
                               '<div>' +
                                   '<span class="font-mono font-bold text-indigo-300">' + escape(data.id) + '</span>' +
                                   ' <span class="text-slate-400 text-[11px]">(' + escape(data.type_name || data.pallet_type_id || '') + ')</span>' +
                                   hasBox +
                               '</div>' +
                               '<span class="text-[10px] font-semibold ' + statusClass + '">' + escape(data.location || 'In Transit') + '</span>' +
                           '</div>';
                },
                item: function(data, escape) {
                    return '<div class="text-xs font-mono font-bold text-indigo-400">Palet: ' + escape(data.id) + ' (' + escape(data.location || 'In Transit') + ')</div>';
                }
            },
            onChange: function(val) {
                el.value = val;
                el.dispatchEvent(new Event('change', { bubbles: true }));
                if (onChangeCallback) onChangeCallback(val);
            }
        });

        tsInstances[id] = ts;
        return ts;
    }

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
                1: 'Palet',
                2: 'Produk',
                3: 'Detail',
                4: 'Simpan Rak'
            },

            form: {
                pallet_id: '',
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

            // Mobile Pallet Search Modal State & Methods
            showMobilePalletModal: false,
            mobileSearchQuery: '',
            mobileFilterType: '',
            mobilePalletResults: [],
            isSearchingMobile: false,

            openMobilePalletModal() {
                this.showMobilePalletModal = true;
                this.mobileSearchQuery = '';
                this.mobileFilterType = '';
                this.searchPalletsMobile();
                this.$nextTick(() => {
                    const input = document.getElementById('mobile_pallet_search_input');
                    if (input) input.focus();
                });
            },

            setMobileFilter(prefix) {
                this.mobileFilterType = prefix;
                this.mobileSearchQuery = prefix;
                this.searchPalletsMobile();
            },

            searchPalletsMobile() {
                this.isSearchingMobile = true;
                const q = (this.mobileSearchQuery || '').trim();
                const cacheKey = q.toUpperCase();
                if (palletSearchCache[cacheKey]) {
                    this.mobilePalletResults = palletSearchCache[cacheKey];
                    this.isSearchingMobile = false;
                    return;
                }
                const url = '{{ route("transaction.inbound.search_pallets") }}?q=' + encodeURIComponent(q);

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        palletSearchCache[cacheKey] = data;
                        this.mobilePalletResults = data;
                        this.isSearchingMobile = false;
                    })
                    .catch(() => {
                        this.isSearchingMobile = false;
                    });
            },

            selectMobilePallet(palletId) {
                this.form.pallet_id = palletId;
                this.showMobilePalletModal = false;
                this.checkPalletInfo();
                if (tsInstances['select_existing_pallet']) {
                    tsInstances['select_existing_pallet'].setValue(palletId, true);
                }
            },

            initSelectSearchAll() {
                this.$nextTick(() => {
                    initPalletTomSelect('select_existing_pallet', val => {
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
                            if (tsInstances['select_existing_pallet'] && tsInstances['select_existing_pallet'].getValue() !== pltId) {
                                tsInstances['select_existing_pallet'].addOption({ id: pltId, label: 'Palet: ' + pltId + ' (' + (data.bin_id ? 'Rak ' + data.bin_id : 'In Transit') + ')' });
                                tsInstances['select_existing_pallet'].setValue(pltId, true);
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
                this.form.pallet_id = '';
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
