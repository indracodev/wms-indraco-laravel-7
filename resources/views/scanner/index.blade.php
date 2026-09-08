@extends('layouts.app')

@section('title', 'Scanner Simulator')

@section('styles')
<style>
    /* ===== html5-qrcode containment override ===== */
    /* Force the viewfinder container to act as CSS stacking context */
    #scanner-viewfinder-box {
        position: relative !important;
        overflow: hidden !important;
    }

    /* The html5-qrcode lib injects a div inside #page-camera-reader */
    #page-camera-reader {
        position: relative !important;
        width: 100% !important;
    }

    /* Prevent the library injected wrapper from going fixed/absolute */
    #page-camera-reader > div {
        position: relative !important;
        width: 100% !important;
    }

    /* Video element itself */
    #page-camera-reader video {
        position: relative !important;
        display: block !important;
        width: 100% !important;
        height: auto !important;
        max-height: 360px !important;
        object-fit: cover !important;
        border-radius: 0.75rem !important;
        margin: 0 auto !important;
    }

    /* Hide html5-qrcode auto-generated UI controls (buttons, inputs) */
    #page-camera-reader__dashboard,
    #page-camera-reader__filescan_input,
    #page-camera-reader__header_message {
        display: none !important;
    }

    /* Make scan region transparent */
    #page-camera-reader__scan_region {
        background: transparent !important;
        overflow: hidden !important;
    }

    /* Hide the canvas overlay drawn by the library */
    #page-camera-reader__scan_region canvas {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="space-y-6" x-data="scannerApp()">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 max-w-2xl mx-auto w-full">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-barcode text-purple-400"></i>
                <span>Barcode Scanner Simulator</span>
            </h2>
            <p class="text-sm text-slate-400">Mode pemindaian langsung via kamera & opsi input kode manual</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span x-text="isCameraActive ? 'Kamera Aktif' : 'Modus Manual'"></span>
            </span>
        </div>
    </div>

    <!-- MAIN CAMERA MODE PANEL (PROPORTIONAL CENTERED CARD) -->
    <div class="glass-panel max-w-2xl mx-auto w-full p-5 sm:p-6 rounded-2xl shadow-xl space-y-5 border border-purple-500/30">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <div class="flex items-center space-x-2 text-purple-400 font-bold text-sm">
                <i class="fa-solid fa-video text-base"></i>
                <span>Scanner Kamera HP / Laptop</span>
            </div>
            <div id="camera-status-text" class="text-xs font-mono text-indigo-300" x-text="cameraStatus">
                Menyiapkan kamera...
            </div>
        </div>

        <!-- Live Camera Viewfinder Box (Proportional Aspect Ratio) -->
        <div id="scanner-viewfinder-box" class="relative rounded-2xl border-2 border-purple-500/50 bg-slate-950 overflow-hidden shadow-inner max-w-lg mx-auto w-full aspect-[4/3] sm:aspect-[16/9] max-h-[320px] flex items-center justify-center">
            <!-- Native video element - fully contained -->
            <video id="scanner-video-native"
                   autoplay playsinline muted
                   style="display:block; width:100%; height:100%; object-fit:cover; border-radius:0.75rem;">
            </video>

            <!-- Placeholder when camera not yet active -->
            <div x-show="!isCameraActive"
                 class="absolute inset-0 flex flex-col items-center justify-center text-slate-500 gap-3">
                <i class="fa-solid fa-video-slash text-4xl"></i>
                <span class="text-sm font-medium" x-text="cameraStatus"></span>
            </div>

            <!-- Overlay Reticle & Scan Line (When Camera Active) -->
            <div x-show="isCameraActive"
                 class="pointer-events-none absolute inset-0 flex items-center justify-center">
                <div class="w-52 sm:w-64 h-32 sm:h-40 border-2 border-dashed border-purple-400/80 rounded-xl relative shadow-[0_0_15px_rgba(168,85,247,0.3)]">
                    <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-purple-400 to-transparent animate-pulse top-1/2"></div>
                </div>
            </div>

            <!-- Hidden canvas for BarcodeDetector scanning -->
            <canvas id="scanner-canvas" style="display:none;"></canvas>
        </div>

        <!-- CAMERA CONTROLS: TOMBOL SCAN & TOMBOL INPUT MANUAL -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <!-- Tombol Scan Barcode -->
            <button type="button" @click="triggerScanButton()" 
                    class="w-full py-3.5 px-4 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl shadow-lg shadow-purple-600/30 transition flex items-center justify-center space-x-2 cursor-pointer text-sm">
                <i class="fa-solid fa-qrcode text-lg"></i>
                <span>Scan Barcode</span>
            </button>

            <!-- Tombol Input Code Manual -->
            <button type="button" @click="openManualModal()" 
                    class="w-full py-3.5 px-4 bg-slate-800 hover:bg-slate-700 text-indigo-300 font-bold rounded-xl border border-indigo-500/40 shadow-lg transition flex items-center justify-center space-x-2 cursor-pointer text-sm">
                <i class="fa-solid fa-keyboard text-lg"></i>
                <span>Input Code Manual</span>
            </button>
        </div>

        <!-- Recent Scan Bar Input Display -->
        <template x-if="scanInput">
            <div class="p-3 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between text-xs font-mono">
                <span class="text-slate-400">Kode Terakhir Dipindai / Diinput:</span>
                <span class="font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded border border-emerald-500/30" x-text="scanInput"></span>
            </div>
        </template>
    </div>

    <!-- MODAL INPUT CODE MANUAL -->
    <div x-show="showManualModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-indigo-500/50 text-slate-100"
             @click.outside="showManualModal = false">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                <div class="flex items-center space-x-2 text-indigo-400 font-bold text-base">
                    <i class="fa-solid fa-keyboard text-xl"></i>
                    <h3>Input Kode Barcode Manual</h3>
                </div>
                <button type="button" @click="showManualModal = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Form Input -->
            <div class="space-y-4">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Ketik Kode Barcode</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">
                        <i class="fa-solid fa-barcode text-lg"></i>
                    </span>
                    <input type="text" x-model="manualInput" x-ref="manualInputField" @keydown.enter.prevent="submitManualInput()"
                        class="w-full pl-11 pr-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono text-base placeholder-slate-500 focus:ring-2 focus:ring-indigo-500"
                        placeholder="Contoh: PLT-0001, PRD-99812, B.10.2, BOX-0001">
                </div>

                <!-- Quick Sample Buttons inside Modal -->
                <div>
                    <div class="text-[11px] font-semibold text-slate-400 mb-2">Contoh Barcode Cepat:</div>
                    <div class="flex flex-wrap gap-2 text-xs font-mono">
                        <button type="button" @click="manualInput = 'PLT-0001'; submitManualInput()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-indigo-300 rounded-lg border border-slate-700 transition">PLT-0001</button>
                        <button type="button" @click="manualInput = 'PLT-0002'; submitManualInput()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-indigo-300 rounded-lg border border-slate-700 transition">PLT-0002</button>
                        <button type="button" @click="manualInput = 'B.10.2'; submitManualInput()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-lg border border-slate-700 transition">BIN: B.10.2</button>
                        <button type="button" @click="manualInput = 'PRD-99812'; submitManualInput()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-emerald-300 rounded-lg border border-slate-700 transition">PRD-99812</button>
                        <button type="button" @click="manualInput = 'BOX-0001'; submitManualInput()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-sky-300 rounded-lg border border-slate-700 transition">BOX-0001</button>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="pt-3 flex items-center justify-end space-x-3 border-t border-slate-700/80">
                <button type="button" @click="showManualModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs rounded-xl border border-slate-700">
                    Batal
                </button>
                <button type="button" @click="submitManualInput()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-600/30 flex items-center space-x-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Proses Barcode</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Scan Results View -->
    <div x-show="scanResult" class="max-w-2xl mx-auto w-full space-y-4" x-cloak>
        <div class="flex items-center justify-between px-1">
            <h3 class="font-bold text-white text-base flex items-center gap-2">
                <i class="fa-solid fa-square-poll-vertical text-emerald-400"></i>
                <span>Hasil Pemindaian Barcode</span>
            </h3>
            <button @click="showRawJson = !showRawJson" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold underline cursor-pointer">
                <span x-text="showRawJson ? '📋 Tampilan Card UI' : '📄 Format Raw JSON'"></span>
            </button>
        </div>

        <!-- RAW JSON VIEW -->
        <div x-show="showRawJson" class="glass-panel p-4 rounded-2xl shadow-xl overflow-x-auto border border-slate-700 font-mono text-xs text-emerald-400">
            <pre x-text="JSON.stringify(scanResult ? scanResult.data : {}, null, 2)"></pre>
        </div>

        <!-- RICH MOBILE CARD UI -->
        <div x-show="!showRawJson" class="space-y-4">

            <!-- 1. BIN ENTITY CARD -->
            <template x-if="scanResult && scanResult.type === 'bin'">
                <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl space-y-4 border border-amber-500/40">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3 gap-2">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                Slot BIN Gudang
                            </span>
                            <h4 class="text-xl font-bold font-mono text-white mt-1 flex items-center gap-2">
                                <i class="fa-solid fa-barcode text-amber-400"></i>
                                <span x-text="'BIN: ' + scanResult.data.id"></span>
                            </h4>
                            <p class="text-xs text-slate-400" x-text="scanResult.data.warehouse"></p>
                        </div>
                        <span :class="scanResult.data.is_full ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'" 
                              class="px-3 py-1 rounded-full text-xs font-bold border flex items-center gap-1 shrink-0">
                            <i :class="scanResult.data.is_full ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-check'"></i>
                            <span x-text="scanResult.data.is_full ? 'BIN FULL (2/2 Palet)' : 'TERSEDIA (' + scanResult.data.current_pallets_count + '/' + scanResult.data.max_pallet + ' Palet)'"></span>
                        </span>
                    </div>

                    <!-- Pallets List inside BIN -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-300">Daftar Palet Terdaftar:</div>
                        
                        <template x-if="scanResult.data.pallets && scanResult.data.pallets.length > 0">
                            <div class="space-y-2.5">
                                <template x-for="plt in scanResult.data.pallets" :key="plt.pallet_id">
                                    <div @click="scanInput = plt.pallet_id; processScan()" 
                                         class="p-3.5 bg-slate-900/90 hover:bg-slate-800/90 rounded-xl border border-slate-800 hover:border-indigo-500/60 flex items-center justify-between text-xs cursor-pointer transition shadow-sm group">
                                        <div class="space-y-0.5">
                                            <div class="font-bold font-mono text-indigo-300 group-hover:text-indigo-200 text-sm flex items-center gap-1.5">
                                                <i class="fa-solid fa-pallet text-slate-400 group-hover:text-indigo-400"></i>
                                                <span x-text="plt.pallet_id"></span>
                                            </div>
                                            <div class="text-slate-400" x-text="plt.type"></div>
                                            <div class="text-[10px] text-indigo-400 font-medium flex items-center gap-1">
                                                <span>Klik untuk lihat detail palet</span>
                                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-200 font-bold border border-indigo-500/30" x-text="plt.total_qty + ' Karton'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!scanResult.data.pallets || scanResult.data.pallets.length === 0">
                            <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 text-center text-xs text-slate-400 italic">
                                Belum ada palet terdaftar di BIN ini.
                            </div>
                        </template>
                    </div>

                    <!-- Quick Actions -->
                    <div class="pt-3 border-t border-slate-700/50 flex items-center gap-2">
                        <a href="{{ route('transaction.inbound.form') }}" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-xl text-center shadow">
                            <i class="fa-solid fa-plus-circle mr-1"></i> Inbound ke BIN Ini
                        </a>
                        <a href="{{ route('transaction.transfer.form') }}" class="flex-1 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-xl text-center shadow">
                            <i class="fa-solid fa-right-left mr-1"></i> Transfer BIN
                        </a>
                    </div>
                </div>
            </template>

            <!-- 2. PALLET ENTITY CARD -->
            <template x-if="scanResult && scanResult.type === 'pallet'">
                <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl space-y-4 border border-indigo-500/40">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3 gap-2">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/40">
                                Palet Terdaftar
                            </span>
                            <h4 class="text-xl font-bold font-mono text-white mt-1 flex items-center gap-2">
                                <i class="fa-solid fa-pallet text-indigo-400"></i>
                                <span x-text="scanResult.data.id"></span>
                            </h4>
                            <p class="text-xs text-slate-400" x-text="scanResult.data.type_name"></p>
                        </div>
                        <div class="text-right shrink-0" @click="if(scanResult.data.bin_id !== 'Transit / Belum di BIN') { scanInput = scanResult.data.bin_id; processScan(); }">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 cursor-pointer hover:bg-amber-500/30 transition">
                                <i class="fa-solid fa-location-dot mr-1"></i>
                                <span x-text="'BIN: ' + scanResult.data.bin_id"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Boxes List inside Pallet -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-300">Isi Box Karton pada Palet Ini:</div>
                        
                        <template x-if="scanResult.data.boxes && scanResult.data.boxes.length > 0">
                            <div class="space-y-2.5">
                                <template x-for="box in scanResult.data.boxes" :key="box.box_id">
                                    <div @click="scanInput = box.box_id; processScan()" 
                                         class="p-3.5 bg-slate-900/90 hover:bg-slate-800/90 rounded-xl border border-slate-800 hover:border-sky-500/60 space-y-1.5 text-xs cursor-pointer transition shadow-sm group">
                                        <div class="flex items-center justify-between">
                                            <div class="font-bold text-white group-hover:text-indigo-200 text-sm" x-text="box.product_name"></div>
                                            <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30" x-text="box.qty + ' Karton'"></span>
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                                            <span class="font-mono text-sky-400 group-hover:text-sky-300" x-text="'Box ID: ' + box.box_id"></span>
                                            <span x-text="'Exp: ' + (box.expired_date || '-') + ' • ' + (box.machine_code || '-')"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!scanResult.data.boxes || scanResult.data.boxes.length === 0">
                            <div class="p-4 bg-slate-900/50 rounded-xl border border-slate-800 text-center text-xs text-slate-400 italic">
                                Belum ada box karton terdaftar di palet ini.
                            </div>
                        </template>
                    </div>

                    <!-- Quick Actions -->
                    <div class="pt-3 border-t border-slate-700/50 flex items-center gap-2">
                        <a href="{{ route('transaction.outbound.form') }}" class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs rounded-xl text-center shadow">
                            <i class="fa-solid fa-dolly mr-1"></i> Outbound Picking
                        </a>
                        <a href="{{ route('transaction.transfer.form') }}" class="flex-1 py-2.5 bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs rounded-xl text-center shadow">
                            <i class="fa-solid fa-arrows-up-down-left-right mr-1"></i> Pindahkan Palet
                        </a>
                    </div>
                </div>
            </template>

            <!-- 3. PRODUCT ENTITY CARD -->
            <template x-if="scanResult && scanResult.type === 'product'">
                <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl space-y-4 border border-emerald-500/40">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3 gap-2">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                SKU Produk Master
                            </span>
                            <h4 class="text-lg font-bold text-white mt-1" x-text="scanResult.data.name"></h4>
                            <p class="text-xs font-mono text-indigo-300" x-text="'SKU: ' + scanResult.data.id"></p>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Total Stok</div>
                            <span class="text-xl font-bold text-emerald-400" x-text="scanResult.data.total_stock + ' ' + (scanResult.data.unit || 'Karton')"></span>
                        </div>
                    </div>

                    <!-- Locations List -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold uppercase tracking-wider text-slate-300">Sebaran Lokasi Stok di Gudang:</div>
                        
                        <template x-if="scanResult.data.locations && scanResult.data.locations.length > 0">
                            <div class="space-y-2.5">
                                <template x-for="loc in scanResult.data.locations" :key="loc.box_id">
                                    <div @click="scanInput = loc.pallet_id; processScan()" 
                                         class="p-3.5 bg-slate-900/90 hover:bg-slate-800/90 rounded-xl border border-slate-800 hover:border-amber-500/60 flex items-center justify-between text-xs cursor-pointer transition shadow-sm group">
                                        <div class="space-y-0.5">
                                            <div class="font-mono text-amber-300 group-hover:text-amber-200 font-bold" x-text="'BIN: ' + loc.bin_id + ' | Palet: ' + loc.pallet_id"></div>
                                            <div class="text-[11px] text-slate-400" x-text="'Box ID: ' + loc.box_id + ' • Exp: ' + loc.expired_date"></div>
                                            <div class="text-[10px] text-indigo-400 font-medium flex items-center gap-1">
                                                <span>Klik untuk lihat detail palet ini</span>
                                                <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30 shrink-0" x-text="loc.qty + ' Karton'"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- 4. BOX ENTITY CARD -->
            <template x-if="scanResult && scanResult.type === 'box'">
                <div class="glass-panel p-5 sm:p-6 rounded-2xl shadow-xl space-y-4 border border-sky-500/40">
                    <div class="flex items-start justify-between border-b border-slate-700/60 pb-3 gap-2">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-sky-500/20 text-sky-300 border border-sky-500/40">
                                Box Karton Fisik
                            </span>
                            <h4 class="text-xl font-bold font-mono text-white mt-1 flex items-center gap-2">
                                <i class="fa-solid fa-box text-sky-400"></i>
                                <span x-text="scanResult.data.id"></span>
                            </h4>
                            <p class="text-xs font-semibold text-indigo-300" x-text="scanResult.data.product_name"></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0" x-text="scanResult.data.qty + ' Karton'"></span>
                    </div>

                    <!-- Box Info Grid -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-900/90 p-4 rounded-xl border border-slate-800">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Palet ID</div>
                            <div class="font-mono text-slate-200 font-bold" x-text="scanResult.data.pallet_id"></div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Lokasi BIN Slot</div>
                            <div class="font-mono text-amber-300 font-bold" x-text="scanResult.data.bin_id"></div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Tanggal Expired</div>
                            <div class="text-slate-200 font-medium" x-text="scanResult.data.expired_date || '-'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">Kode Mesin</div>
                            <div class="font-mono text-slate-200" x-text="scanResult.data.machine_code || '-'"></div>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>

    <!-- Error Alert View -->
    <div x-show="errorMessage" class="p-4 bg-rose-950/80 border border-rose-500/40 rounded-2xl text-rose-200 text-xs flex items-center shadow-lg" x-cloak>
        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-xl mr-3 shrink-0"></i>
        <span class="font-medium" x-text="errorMessage"></span>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function scannerApp() {
        return {
            scanInput: '',
            scanResult: null,
            errorMessage: null,
            showRawJson: false,
            showManualModal: false,
            manualInput: '',
            isCameraActive: false,
            cameraStatus: 'Memuat kamera...',
            _stream: null,
            _scanInterval: null,
            _barcodeDetector: null,

            init() {
                // Start camera after DOM is fully rendered by Alpine
                this.$nextTick(() => {
                    this.startCamera();
                });
            },

            async startCamera() {
                const video = document.getElementById('scanner-video-native');
                if (!video) {
                    this.cameraStatus = 'Elemen video tidak ditemukan.';
                    return;
                }

                this.cameraStatus = 'Mengakses kamera...';

                try {
                    const constraints = {
                        video: {
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    };
                    this._stream = await navigator.mediaDevices.getUserMedia(constraints);
                    video.srcObject = this._stream;
                    await video.play();

                    this.isCameraActive = true;
                    this.cameraStatus = 'Kamera Aktif • Arahkan ke Barcode';

                    // Start scanning using BarcodeDetector (if available) or Html5Qrcode fallback
                    this.startBarcodeScanning(video);

                } catch (err) {
                    console.error('Camera error:', err);
                    this.isCameraActive = false;
                    this.cameraStatus = 'Izin kamera diperlukan / kamera tidak ditemukan.';
                }
            },

            startBarcodeScanning(video) {
                // Use BarcodeDetector API if supported (Chrome 83+, Edge, Android Chrome)
                if ('BarcodeDetector' in window) {
                    this._barcodeDetector = new BarcodeDetector({
                        formats: ['qr_code', 'code_128', 'code_39', 'ean_13', 'ean_8', 'upc_a', 'upc_e', 'itf', 'data_matrix']
                    });
                    this._scanInterval = setInterval(async () => {
                        if (!this.isCameraActive || video.readyState < 2) return;
                        try {
                            const barcodes = await this._barcodeDetector.detect(video);
                            if (barcodes.length > 0) {
                                const code = barcodes[0].rawValue;
                                this.onBarcodeDetected(code);
                            }
                        } catch (e) {}
                    }, 300);
                } else {
                    // Fallback: Use Html5Qrcode to scan from video frame via canvas
                    this.startHtml5QrFallback(video);
                }
            },

            startHtml5QrFallback(video) {
                const canvas = document.getElementById('scanner-canvas');
                const ctx = canvas.getContext('2d');

                this._scanInterval = setInterval(async () => {
                    if (!this.isCameraActive || video.readyState < 2) return;
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convert canvas to blob and scan with Html5Qrcode
                    canvas.toBlob(async (blob) => {
                        if (!blob) return;
                        const file = new File([blob], 'frame.jpg', { type: 'image/jpeg' });
                        try {
                            const qr = new Html5Qrcode('__tmp_scanner');
                            const result = await qr.scanFile(file, false);
                            if (result) {
                                this.onBarcodeDetected(result);
                            }
                        } catch (e) {
                            // No barcode found in this frame, continue scanning
                        }
                    }, 'image/jpeg', 0.8);
                }, 500);
            },

            stopCamera() {
                if (this._scanInterval) {
                    clearInterval(this._scanInterval);
                    this._scanInterval = null;
                }
                if (this._stream) {
                    this._stream.getTracks().forEach(t => t.stop());
                    this._stream = null;
                }
                this.isCameraActive = false;
                this.cameraStatus = 'Kamera dihentikan.';
            },

            onBarcodeDetected(code) {
                if (this.scanInput === code) return; // avoid duplicate
                this.playBeepSound();
                this.scanInput = code;
                this.processScan();
            },

            triggerScanButton() {
                if (this.scanInput) {
                    this.processScan();
                } else if (this.isCameraActive) {
                    this.cameraStatus = 'Pindai sedang berjalan. Arahkan ke kode barcode...';
                } else {
                    this.openManualModal();
                }
            },

            openManualModal() {
                this.showManualModal = true;
                this.manualInput = this.scanInput || '';
                this.$nextTick(() => {
                    if (this.$refs.manualInputField) {
                        this.$refs.manualInputField.focus();
                    }
                });
            },

            submitManualInput() {
                if (!this.manualInput) return;
                this.playBeepSound();
                this.scanInput = this.manualInput;
                this.showManualModal = false;
                this.processScan();
            },

            processScan() {
                if (!this.scanInput) return;
                this.scanResult = null;
                this.errorMessage = null;

                fetch("{{ route('scanner.scan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: this.scanInput })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        this.scanResult = data;
                    } else {
                        this.errorMessage = data.message || 'Terjadi kesalahan saat memproses barcode.';
                    }
                })
                .catch(err => {
                    this.errorMessage = 'Gagal terhubung ke server backend.';
                });
            },

            playBeepSound() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = 'sine';
                    osc.frequency.value = 1200;
                    gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.12);
                } catch (e) {}
            },

            destroy() {
                this.stopCamera();
            }
        }
    }
</script>
<!-- Hidden tmp div for Html5Qrcode fallback file scanner (must exist in DOM) -->
<div id="__tmp_scanner" style="display:none;"></div>
@endsection
