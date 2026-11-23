@extends('layouts.app')

@section('title', 'Outbound Picking V2')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Tom Select Custom Styling */
    .ts-wrapper .ts-control {
        background-color: #ffffff !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        color: #00274d !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.875rem !important;
        box-shadow: none !important;
    }
    .ts-wrapper.single .ts-control:after {
        border-color: #004b87 transparent transparent transparent !important;
    }
    .ts-dropdown {
        background-color: #ffffff !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        color: #00274d !important;
        box-shadow: 0 10px 25px -5px rgba(0, 43, 91, 0.2) !important;
        z-index: 9999 !important;
    }
    .ts-dropdown .option {
        padding: 0.6rem 1rem !important;
        color: #00274d !important;
        font-size: 0.875rem !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #004b87 !important;
        color: #ffffff !important;
    }
    .ts-dropdown .option.selected {
        background-color: #f1f5f9 !important;
        color: #004b87 !important;
        font-weight: bold !important;
    }
    .ts-control input {
        color: #00274d !important;
    }

    .modal-backdrop-blur {
        background-color: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }

    /* CRITICAL THEME OVERRIDE: Container #004b87 has pure white text by default */
    html.light .outbound-main-container,
    html.light .outbound-main-container h1,
    html.light .outbound-main-container h2,
    html.light .outbound-main-container h3,
    html.light .outbound-main-container h4,
    html.light .outbound-main-container p,
    html.light .outbound-main-container label,
    html.light .outbound-main-container .font-extrabold,
    html.light .outbound-main-container .text-white,
    html.light [style*="#004b87"] h3,
    html.light [style*="#004b87"] .font-extrabold,
    .outbound-main-container,
    .outbound-main-container h2,
    .outbound-main-container h3,
    .outbound-main-container p,
    .outbound-main-container label,
    .outbound-main-container .text-white {
        color: #ffffff !important;
    }

    /* CRITICAL REQUIREMENT: For cards with white background, all texts MUST be dark blue (#004b87 / #00274d) */
    .card-white,
    .card-white *,
    [style*="background-color: #ffffff"],
    [style*="background-color: #ffffff"] *:not(.badge-preserve):not(.badge-preserve *):not(.btn-preserve):not(.btn-preserve *):not(.text-preserve) {
        color: #004b87 !important;
    }

    /* Exception colors inside white cards */
    .card-white .text-darkblue, [style*="background-color: #ffffff"] .text-darkblue {
        color: #00274d !important;
    }
    .card-white .text-danger, [style*="background-color: #ffffff"] .text-danger {
        color: #e11d48 !important;
    }
    .card-white .text-success, [style*="background-color: #ffffff"] .text-success {
        color: #059669 !important;
    }
    .card-white .text-warning, [style*="background-color: #ffffff"] .text-warning {
        color: #d97706 !important;
    }
    .card-white .text-muted, [style*="background-color: #ffffff"] .text-muted {
        color: #64748b !important;
    }
</style>
@endsection

@php
    if (!isset($binsData)) {
        $binsData = isset($bins) ? $bins->map(function($b) {
            $activePlts = $b->pallets->where('is_active', true);
            $totalBoxes = $activePlts->sum(function($p) {
                return $p->activeBoxes ? $p->activeBoxes->count() : 0;
            });
            return [
                'id' => $b->id,
                'rack_id' => $b->rack_id ?? ($b->rack->id ?? '-'),
                'warehouse_name' => $b->rack && $b->rack->warehouse ? $b->rack->warehouse->name : 'Gudang Utama',
                'active_pallets_count' => $activePlts->count(),
                'total_boxes_count' => $totalBoxes,
                'has_stock' => $activePlts->count() > 0,
            ];
        }) : [];
    }
@endphp

@section('content')
<div class="space-y-6" x-data="outboundV2Workflow()" x-init="initSelectSearchAll()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white">
                Pengeluaran Barang (Outbound V2)
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Alur: Pilih / Scan Rak &rarr; Pilih Palet & Box (FEFO) &rarr; Tentukan Status (Keluar Ekspedisi / Keluar In Transit)</p>
        </div>
    </div>

    <!-- MAIN FORM CONTAINER (#004b87 Background with Pure White Text) -->
    <div class="outbound-main-container p-5 sm:p-8 rounded-2xl shadow-2xl space-y-6"
         style="background-color: #004b87 !important; border: 2px solid #003661 !important; color: #ffffff !important;">
        
        <form id="outboundV2Form" action="{{ route('transaction.outbound') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="outbound_type" x-model="form.outbound_type">

            <!-- ========================================================================= -->
            <!-- STEP 1: SCAN RAK / PILIH BIN DENGAN MODAL PENCARIAN                       -->
            <!-- ========================================================================= -->
            <div class="p-5 rounded-2xl space-y-4 shadow-md"
                 style="background-color: rgba(0, 43, 91, 0.45) !important; border: 2px solid #0284c7 !important;">
                
                <div class="flex items-center justify-between border-b border-sky-600/50 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-7 h-7 rounded-full bg-white text-[#004b87] flex items-center justify-center text-xs font-black shadow">1</span>
                        <h3 style="color: #ffffff !important;" class="text-sm sm:text-base font-extrabold text-white uppercase tracking-wider">Langkah 1: Tentukan Lokasi Rak / BIN</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-amber-300" x-show="rackItems.length > 0" x-text="rackItems.length + ' Item / Batch Ditemukan'"></span>
                        <button type="button" 
                                x-show="form.rack_id" 
                                @click="loadRackInventory(true)" 
                                title="Muat ulang stok terbaru dari server (Bypass Cache)"
                                class="px-2.5 py-1 rounded-lg bg-sky-800/80 hover:bg-sky-700 text-sky-100 text-xs font-bold flex items-center gap-1.5 transition active:scale-95 cursor-pointer shadow-xs">
                            <i class="fa-solid fa-arrows-rotate text-[11px]" :class="{ 'fa-spin': isLoadingRack }"></i>
                            <span class="hidden sm:inline">Refresh Stok</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Method A: Scan Manual / Barcode Scanner Fisik -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-sky-100 uppercase tracking-wide">
                            Scan Barcode Rak / BIN Fisik:
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   name="rack_id" 
                                   id="rack_id_input"
                                   x-model="form.rack_id" 
                                   @change="loadRackInventory()"
                                   @keyup.enter="loadRackInventory()"
                                   required 
                                   placeholder="e.g. I B.1.1 atau RK-B"
                                   style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #cbd5e1 !important;"
                                   class="w-full p-3 rounded-xl font-mono text-sm uppercase font-black focus:outline-none focus:ring-2 focus:ring-white shadow-inner">
                            <button type="button" 
                                    @click="openCameraScanner(val => { form.rack_id = val; loadRackInventory(); })" 
                                    class="px-4 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/30 text-xs font-bold cursor-pointer whitespace-nowrap active:scale-95 transition">
                                Kamera
                            </button>
                        </div>
                        
                        <!-- Fast Simulator Bar Button (Yellow background with black text) -->
                        <div class="pt-1">
                            <a href="{{ route('scanner.index') }}" target="_blank"
                               style="background-color: #facc15 !important; border: 1.5px solid #eab308 !important; color: #000000 !important;"
                               class="w-full py-2 px-3 hover:bg-yellow-400 rounded-xl flex items-center justify-center text-xs font-bold transition active:scale-95 shadow-sm">
                                <span style="color: #000000 !important; font-weight: 800;">Buka Scanner Simulator Barcode Bar</span>
                            </a>
                        </div>
                    </div>

                    <!-- Method B: Pilih BIN Menggunakan Modal Pencarian -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-sky-100 uppercase tracking-wide">
                            Atau Pilih BIN dari Database (Modal Pencarian):
                        </label>
                        
                        <!-- Trigger Card (White card with dark blue text) -->
                        <button type="button" 
                                @click="openBinSearchModal()"
                                style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                                class="w-full p-3 sm:p-3.5 rounded-xl flex items-center justify-between text-left transition active:scale-[0.99] shadow-md hover:bg-sky-50 cursor-pointer group">
                            <div class="truncate mr-3">
                                <template x-if="form.rack_id">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <div>
                                            <div class="font-mono font-black text-sm text-[#00274d] tracking-wide" x-text="'BIN Terpilih: ' + form.rack_id"></div>
                                            <div class="text-[10px] text-slate-500 font-semibold">Klik untuk mengganti lokasi BIN</div>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!form.rack_id">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                        <span class="text-xs font-semibold text-slate-500 italic">Anda belum menentukan Lokasi Rak / BIN</span>
                                    </div>
                                </template>
                            </div>
                            <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                                  class="px-4 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow group-hover:bg-[#003661] active:scale-95 transition">
                                <i class="fa-solid fa-magnifying-glass text-xs" style="color: #ffffff !important;"></i>
                                <span style="color: #ffffff !important;" x-text="form.rack_id ? 'Ganti BIN' : 'Cari BIN'"></span>
                            </span>
                        </button>

                        <p class="text-[11px] text-sky-200 pt-0.5">
                            * Pencarian modal mempermudah pencarian dari seluruh slot BIN gudang beserta status palet & stoknya.
                        </p>
                    </div>
                </div>

                <!-- LIST PALET & BOX PADA RAK (FEFO RECOMMENDATION CARDS) -->
                <div x-show="isLoadingRack" class="py-8 text-center text-sky-100 text-xs font-semibold flex items-center justify-center gap-2">
                    <i class="fa-solid fa-spinner fa-spin text-lg text-white"></i>
                    <span>Memuat data stok pada rak...</span>
                </div>

                <div x-show="!isLoadingRack && rackItems.length > 0" class="pt-3 space-y-3">
                    <div class="flex items-center justify-between">
                        <label style="color: #ffffff !important;" class="block text-xs font-extrabold text-white uppercase tracking-wider">
                            Pilih Palet / Box di Rak Ini (Rekomendasi Prioritas FEFO):
                        </label>
                        <span class="text-xs text-sky-200 font-medium">Klik pada salah satu kartu untuk memilih</span>
                    </div>

                    <!-- FEFO Cards: White cards with Dark Blue text -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <template x-for="(item, idx) in rackItems" :key="item.box_id">
                            <div @click="selectItem(item)" 
                                 :style="form.box_id === item.box_id 
                                     ? 'background-color: #ffffff !important; color: #004b87 !important; border: 3px solid #f59e0b !important; transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4) !important;' 
                                     : 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;'"
                                 class="p-4 rounded-xl transition-all cursor-pointer text-xs space-y-2.5 relative shadow-md hover:shadow-lg active:scale-[0.99]">
                                
                                <!-- Header Card: Pallet & FEFO Badge -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-box text-xs text-[#004b87]"></i>
                                        <span class="font-mono font-black text-sm text-[#00274d]" x-text="item.pallet_id"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <template x-if="idx === 0">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs">
                                                FEFO Prioritas
                                            </span>
                                        </template>
                                        <template x-if="form.box_id === item.box_id">
                                            <span class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-black">
                                                ✓
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div>
                                    <div class="font-black text-[#00274d] text-sm leading-snug" x-text="item.product_name"></div>
                                    <div class="text-[11px] text-slate-500 mt-0.5" x-text="'SKU: ' + item.product_id + ' | Brand: ' + item.brand"></div>
                                </div>

                                <!-- Stock & ED Info -->
                                <div class="pt-2 border-t border-slate-200 space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 font-semibold">Sisa Stok Box:</span>
                                        <span class="font-black text-[#004b87] text-sm" x-text="item.qty + ' ' + item.uom_base"></span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 font-semibold">Expired Date:</span>
                                        <span class="font-extrabold text-rose-600" x-text="item.expired_date"></span>
                                    </div>
                                </div>

                                <div class="text-[10px] text-slate-500 truncate pt-1 border-t border-slate-100" x-show="item.notes" x-text="'Catatan: ' + item.notes"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="!isLoadingRack && hasSearched && rackItems.length === 0" 
                     style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                     class="p-5 rounded-xl text-center text-xs shadow-md">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base mb-1 block"></i>
                    <span class="font-bold text-[#00274d]">Tidak ada palet atau box barang aktif pada Rak / BIN ini.</span>
                    <p class="text-slate-500 text-[11px] mt-0.5">Silakan scan / pilih lokasi BIN lain yang memiliki muatan barang.</p>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 2: DETAIL PENGAMBILAN QTY & SATUAN (WHITE CARDS)                    -->
            <!-- ========================================================================= -->
            <div class="p-5 rounded-2xl space-y-4 shadow-md"
                 style="background-color: rgba(0, 43, 91, 0.45) !important; border: 2px solid #0284c7 !important;" 
                 x-show="selectedItem">
                
                <div class="flex items-center space-x-2.5 border-b border-sky-600/50 pb-3">
                    <span class="w-7 h-7 rounded-full bg-white text-[#004b87] flex items-center justify-center text-xs font-black shadow">2</span>
                    <h3 style="color: #ffffff !important;" class="text-sm sm:text-base font-extrabold text-white uppercase tracking-wider">Langkah 2: Tentukan Kuantitas & Satuan Pengambilan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pallet & Box Hidden Inputs -->
                    <input type="hidden" name="pallet_id" x-model="form.pallet_id">
                    <input type="hidden" name="box_id" x-model="form.box_id">

                    <!-- Selected Product Summary Card (White Card with Dark Blue Text) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-4 rounded-xl space-y-1.5 shadow-md">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Barang Terpilih:</div>
                        <div class="font-black text-[#00274d] text-base leading-snug" x-text="selectedItem ? selectedItem.product_name : '-'"></div>
                        <div class="text-xs pt-1 border-t border-slate-200">
                            Palet: <strong class="text-[#004b87] font-mono font-bold" x-text="form.pallet_id"></strong> 
                            <span class="mx-1 text-slate-300">|</span> 
                            Box: <span class="font-mono text-slate-700 font-bold" x-text="form.box_id"></span>
                        </div>
                    </div>

                    <!-- Qty Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-sky-100 uppercase tracking-wide">
                            Jumlah Qty Diambil <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" 
                               step="any" 
                               name="qty" 
                               x-model.number="form.qty" 
                               required 
                               min="0.01" 
                               style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #cbd5e1 !important;"
                               class="w-full p-3.5 rounded-xl text-base font-black focus:outline-none focus:ring-2 focus:ring-white shadow-inner">
                    </div>

                    <!-- Satuan Transaksi (UOM) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-sky-100 uppercase tracking-wide">
                            Satuan Transaksi (UOM) <span class="text-rose-400">*</span>
                        </label>
                        <select id="select_outbound_unit" name="unit" x-model="form.unit" required class="w-full">
                        </select>
                    </div>

                    <!-- Shift -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-sky-100 uppercase tracking-wide">Shift Kerja</label>
                        <select name="shift" 
                                x-model="form.shift" 
                                style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #cbd5e1 !important;"
                                class="w-full p-3.5 rounded-xl font-bold text-sm">
                            <option value="1">Shift 1 (Pagi)</option>
                            <option value="2">Shift 2 (Siang)</option>
                            <option value="3">Shift 3 (Malam)</option>
                        </select>
                    </div>

                    <!-- Live Calculation & Stock Preview Card (White Card with Dark Blue Text) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="md:col-span-2 p-4 rounded-xl space-y-2 shadow-md">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 font-bold">Total Pengambilan (Satuan Dasar):</span>
                            <span class="font-black text-[#00274d] text-sm" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200">
                            <span class="text-slate-600 font-bold">Sisa Stok Box Setelah Outbound:</span>
                            <span class="font-black text-sm" 
                                  :style="remainingBoxQty < 0 ? 'color: #e11d48 !important;' : 'color: #059669 !important;'"
                                  x-text="remainingBoxQty + ' ' + currentBaseUnit"></span>
                        </div>
                        <div x-show="remainingBoxQty < 0" class="text-rose-600 font-extrabold text-xs pt-1">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i>
                            Peringatan: Jumlah pengambilan melebihi stok yang tersedia pada box ini!
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 3: PILIH STATUS PENGELUARAN (WHITE CARDS)                           -->
            <!-- ========================================================================= -->
            <div class="p-5 rounded-2xl space-y-4 shadow-md"
                 style="background-color: rgba(0, 43, 91, 0.45) !important; border: 2px solid #0284c7 !important;" 
                 x-show="selectedItem">
                
                <div class="flex items-center space-x-2.5 border-b border-sky-600/50 pb-3">
                    <span class="w-7 h-7 rounded-full bg-white text-[#004b87] flex items-center justify-center text-xs font-black shadow">3</span>
                    <h3 style="color: #ffffff !important;" class="text-sm sm:text-base font-extrabold text-white uppercase tracking-wider">Langkah 3: Pilih Status Pengeluaran Barang</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- OPSI 1: KELUAR EKSPEDISI (White Card with Dark Blue Text) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-5 rounded-xl space-y-3.5 transition flex flex-col justify-between shadow-md hover:border-emerald-500">
                        <div class="space-y-1.5">
                            <div class="font-black text-emerald-700 text-base flex items-center gap-1.5">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span>A. Keluar Ekspedisi</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Barang dikeluarkan untuk pengiriman armada ekspedisi / distributor. <strong class="text-slate-800">Stok gudang dipotong permanen</strong>. Jika seluruh box pada palet habis, palet dinonaktifkan.
                            </p>
                        </div>
                        <button type="button" 
                                @click="triggerSubmitOutbound('ekspedisi')" 
                                :disabled="!form.box_id || remainingBoxQty < 0 || form.qty <= 0"
                                :class="(!form.box_id || remainingBoxQty < 0 || form.qty <= 0) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 active:scale-95 cursor-pointer'"
                                class="w-full py-3.5 bg-emerald-600 text-white font-extrabold text-xs sm:text-sm rounded-xl transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Konfirmasi Keluar Ekspedisi</span>
                        </button>
                    </div>

                    <!-- OPSI 2: KELUAR IN TRANSIT (White Card with Dark Blue Text) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-5 rounded-xl space-y-3.5 transition flex flex-col justify-between shadow-md hover:border-amber-500">
                        <div class="space-y-1.5">
                            <div class="font-black text-amber-700 text-base flex items-center gap-1.5">
                                <i class="fa-solid fa-dolly"></i>
                                <span>B. Keluar In Transit</span>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Palet diturunkan dari rak fisik untuk dibawa ke area transit (staging dock, repack, sortir). <strong class="text-slate-800">Palet dilepas dari rak</strong> dan statusnya beralih menjadi <strong class="text-slate-800">In Transit</strong>. Stok tetap ada di gudang.
                            </p>
                        </div>
                        <button type="button" 
                                @click="triggerSubmitOutbound('in_transit')" 
                                :disabled="!form.box_id || remainingBoxQty < 0 || form.qty <= 0"
                                :class="(!form.box_id || remainingBoxQty < 0 || form.qty <= 0) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-amber-500 shadow-lg shadow-amber-600/30 active:scale-95 cursor-pointer'"
                                class="w-full py-3.5 bg-amber-600 text-white font-extrabold text-xs sm:text-sm rounded-xl transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                            <span>Konfirmasi Keluar In Transit</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL PENCARIAN BIN (DEDICATED SEARCH MODAL)                              -->
    <!-- ========================================================================= -->
    <div x-show="showBinModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 modal-backdrop-blur"
         x-cloak>
        
        <div class="w-full sm:max-w-2xl max-h-[90vh] sm:max-h-[85vh] rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col overflow-hidden"
             style="background-color: #004b87 !important; border: 2px solid #003661 !important;"
             @click.away="showBinModal = false">
            
            <!-- Header Modal -->
            <div class="p-4 sm:p-5 border-b border-sky-600/50 flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-white text-[#004b87] flex items-center justify-center font-black text-base shadow">
                        <i class="fa-solid fa-cubes-stacked"></i>
                    </div>
                    <div>
                        <h3 style="color: #ffffff !important;" class="text-base sm:text-lg font-extrabold text-white">Cari & Pilih Lokasi Rak / BIN</h3>
                        <p class="text-xs text-sky-200 font-medium" x-text="'Total ' + allBins.length + ' Slot BIN Terdaftar'"></p>
                    </div>
                </div>
                <button type="button" 
                        @click="showBinModal = false" 
                        style="background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important;"
                        class="w-8 h-8 rounded-full hover:bg-white/30 flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Search Input & Filter Tabs -->
            <div class="p-4 space-y-3 shrink-0" style="background-color: rgba(0, 39, 77, 0.4) !important;">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model.debounce.200ms="binSearchQuery" 
                           @input="binDisplayLimit = 40; searchBinsFromServer();"
                           id="bin_search_input"
                           placeholder="Ketik kode BIN, nama rak, atau gudang..." 
                           style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #bae6fd !important;"
                           class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs sm:text-sm font-mono uppercase font-black placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-white shadow-inner">
                    <button x-show="binSearchQuery" 
                            @click="binSearchQuery = ''; binDisplayLimit = 40;" 
                            type="button"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <!-- Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button type="button" 
                            @click="binFilterTab = 'all'; binDisplayLimit = 40;" 
                            :style="binFilterTab === 'all' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Semua BIN (<span x-text="allBins.length"></span>)
                    </button>
                    <button type="button" 
                            @click="binFilterTab = 'has_stock'; binDisplayLimit = 40;" 
                            :style="binFilterTab === 'has_stock' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Ada Stok (<span x-text="allBins.filter(b => b.has_stock).length"></span>)
                    </button>
                    <button type="button" 
                            @click="binFilterTab = 'empty'; binDisplayLimit = 40;" 
                            :style="binFilterTab === 'empty' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Kosong (<span x-text="allBins.filter(b => !b.has_stock).length"></span>)
                    </button>
                </div>
            </div>

            <!-- List Result Cards (White cards with Dark Blue text - Virtualized Rendering) -->
            <div class="p-4 overflow-y-auto space-y-2.5 flex-1" style="background-color: #003661 !important;">
                <template x-for="b in displayedBins" :key="b.id">
                    <div @click="selectBinFromModal(b.id)"
                         style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-3.5 rounded-xl shadow-md hover:border-amber-400 hover:shadow-lg cursor-pointer transition active:scale-[0.99] flex items-center justify-between gap-3 group">
                        
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-base text-[#00274d] group-hover:text-[#004b87]" x-text="b.id"></span>
                                <template x-if="b.has_stock">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Ada Stok
                                    </span>
                                </template>
                                <template x-if="!b.has_stock">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                        Kosong
                                    </span>
                                </template>
                            </div>
                            <div class="text-xs text-slate-500 font-medium truncate" x-text="'Rak: ' + b.rack_id + ' | ' + b.warehouse_name"></div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-black text-[#004b87]" x-text="b.active_pallets_count + ' Palet' + (b.total_boxes_count > 0 ? ' (' + b.total_boxes_count + ' Box)' : '')"></div>
                            <span class="text-[11px] font-bold text-amber-600 group-hover:underline">Pilih BIN &rarr;</span>
                        </div>
                    </div>
                </template>

                <!-- Load More Button for Chunked Rendering (Fast UI Performance) -->
                <div x-show="filteredBins.length > binDisplayLimit" class="pt-2 text-center pb-2">
                    <button type="button" 
                            @click="binDisplayLimit += 40" 
                            class="w-full py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-98 cursor-pointer shadow">
                        <i class="fa-solid fa-angles-down text-[11px]"></i>
                        <span>Muat 40 BIN Lainnya (Sisa <span x-text="filteredBins.length - binDisplayLimit"></span>)</span>
                    </button>
                </div>

                <div x-show="filteredBins.length === 0" 
                     style="background-color: #ffffff !important; color: #004b87 !important;"
                     class="p-6 rounded-xl text-center text-xs shadow-md">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-base mb-1 block"></i>
                    <span class="font-bold text-[#00274d]">Tidak ditemukan slot BIN yang sesuai dengan kriteria.</span>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="p-3 border-t border-sky-600/50 text-center text-xs text-sky-200 shrink-0" style="background-color: #00274d !important;">
                Menampilkan <strong class="text-white" x-text="Math.min(binDisplayLimit, filteredBins.length)"></strong> dari <span x-text="filteredBins.length"></span> BIN ditemukan (Total: <span x-text="allBins.length"></span>)
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL KONFIRMASI OUTBOUND                                                -->
    <!-- ========================================================================= -->
    <div x-show="showConfirmModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop-blur" 
         x-cloak>
        
        <div class="w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4"
             style="background-color: #004b87 !important; border: 2px solid #003661 !important;">
            
            <div>
                <h3 style="color: #ffffff !important;" class="text-lg font-extrabold text-white">
                    Konfirmasi Pengeluaran: <span :class="form.outbound_type === 'ekspedisi' ? 'text-emerald-300' : 'text-amber-300'" x-text="form.outbound_type === 'ekspedisi' ? 'Keluar Ekspedisi' : 'Keluar In Transit'"></span>
                </h3>
                <p class="text-xs text-sky-200 mt-0.5">Harap periksa kembali rincian pengeluaran barang:</p>
            </div>

            <!-- Review Card: White Card with Dark Blue Text -->
            <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                 class="space-y-2.5 text-xs p-4 rounded-xl shadow-md">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Lokasi Rak / BIN:</span>
                    <span class="font-mono font-black text-[#00274d]" x-text="form.rack_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Palet Asal:</span>
                    <span class="font-mono font-black text-[#004b87]" x-text="form.pallet_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Box ID:</span>
                    <span class="font-mono font-bold text-slate-700" x-text="form.box_id"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Produk:</span>
                    <span class="font-bold text-[#00274d] text-right truncate max-w-[200px]" x-text="selectedItem ? selectedItem.product_name : '-'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Jumlah Keluar:</span>
                    <span class="font-black text-[#00274d]" x-text="form.qty + ' ' + form.unit"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold">Setara Satuan Dasar:</span>
                    <span class="font-black text-[#004b87]" x-text="calculatedBaseQty + ' ' + currentBaseUnit"></span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200">
                    <span class="text-slate-500 font-semibold">Status Transaksi:</span>
                    <span class="font-black uppercase" 
                          :class="form.outbound_type === 'ekspedisi' ? 'text-emerald-700' : 'text-amber-700'"
                          x-text="form.outbound_type === 'ekspedisi' ? 'Keluar Ekspedisi (Potong Stok)' : 'Keluar In Transit (Lepas Rak)'">
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" 
                        @click="showConfirmModal = false" 
                        style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                        class="px-5 py-2.5 hover:bg-slate-100 font-black text-xs rounded-xl shadow transition active:scale-95 cursor-pointer">
                    Batal
                </button>

                <button type="button" 
                        @click="submitOutboundForm()" 
                        :class="form.outbound_type === 'ekspedisi' ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-amber-600 hover:bg-amber-500'"
                        class="px-5 py-2.5 text-white font-extrabold text-xs rounded-xl shadow-lg transition active:scale-95 cursor-pointer">
                    Iya, Jalankan Outbound
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

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
            placeholder: 'Pilih satuan konversi...',
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
            showBinModal: false,
            isLoadingRack: false,
            hasSearched: false,
            rackItems: [],
            selectedItem: null,

            // Client-side Caching (Memory + SessionStorage)
            inventoryCache: {},
            cacheTTL: 60000, // 60 detik

            allBins: @json($binsData),
            binSearchQuery: '',
            binFilterTab: 'all',
            binDisplayLimit: 40,
            isSearchingBins: false,

            searchBinsFromServer() {
                const q = (this.binSearchQuery || '').trim();
                if (q.length < 2) return;
                this.isSearchingBins = true;
                if (window.topProgressBar) window.topProgressBar.start();

                fetch('/transaction/outbound/search-bins?q=' + encodeURIComponent(q) + '&tab=' + this.binFilterTab)
                    .then(res => res.json())
                    .then(data => {
                        this.isSearchingBins = false;
                        if (window.topProgressBar) window.topProgressBar.finish();
                        if (data.status === 'success' && data.bins) {
                            const existingIds = new Set(this.allBins.map(b => b.id));
                            data.bins.forEach(b => {
                                if (!existingIds.has(b.id)) {
                                    this.allBins.push(b);
                                    existingIds.add(b.id);
                                }
                            });
                        }
                    })
                    .catch(() => {
                        this.isSearchingBins = false;
                        if (window.topProgressBar) window.topProgressBar.finish();
                    });
            },

            get filteredBins() {
                const q = (this.binSearchQuery || '').trim().toLowerCase();
                return this.allBins.filter(b => {
                    const matchesQuery = !q || 
                        (b.id && b.id.toLowerCase().includes(q)) || 
                        (b.rack_id && b.rack_id.toLowerCase().includes(q)) || 
                        (b.warehouse_name && b.warehouse_name.toLowerCase().includes(q));
                    if (!matchesQuery) return false;
                    if (this.binFilterTab === 'has_stock') return b.has_stock;
                    if (this.binFilterTab === 'empty') return !b.has_stock;
                    return true;
                });
            },

            get displayedBins() {
                return this.filteredBins.slice(0, this.binDisplayLimit);
            },

            openBinSearchModal() {
                this.showBinModal = true;
                this.binSearchQuery = '';
                this.binFilterTab = 'all';
                this.binDisplayLimit = 40;
                this.$nextTick(() => {
                    const input = document.getElementById('bin_search_input');
                    if (input) input.focus();
                });
            },

            selectBinFromModal(binId) {
                this.form.rack_id = binId;
                this.showBinModal = false;
                this.loadRackInventory();
            },

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
                // Initialize unit selector if already rendered
            },

            loadRackInventory(forceRefresh = false) {
                const rackId = (this.form.rack_id || '').trim();
                if (!rackId) return;

                const now = Date.now();

                // 1. Check In-Memory JavaScript Cache
                if (!forceRefresh && this.inventoryCache[rackId] && (now - this.inventoryCache[rackId].timestamp < this.cacheTTL)) {
                    this.isLoadingRack = false;
                    this.hasSearched = true;
                    this.rackItems = this.inventoryCache[rackId].items || [];
                    if (this.rackItems.length > 0) {
                        this.selectItem(this.rackItems[0]);
                    } else {
                        this.selectedItem = null;
                    }
                    return;
                }

                // 2. Check SessionStorage Cache (Instan saat reload halaman)
                if (!forceRefresh) {
                    try {
                        const rawSession = sessionStorage.getItem('wms_inv_' + rackId);
                        if (rawSession) {
                            const parsed = JSON.parse(rawSession);
                            if (now - parsed.timestamp < this.cacheTTL) {
                                this.inventoryCache[rackId] = parsed;
                                this.isLoadingRack = false;
                                this.hasSearched = true;
                                this.rackItems = parsed.items || [];
                                if (this.rackItems.length > 0) {
                                    this.selectItem(this.rackItems[0]);
                                } else {
                                    this.selectedItem = null;
                                }
                                return;
                            }
                        }
                    } catch (e) {}
                }

                // 3. Network Fetch jika cache miss atau tombol Refresh Stok ditekan
                this.isLoadingRack = true;
                this.hasSearched = true;
                this.selectedItem = null;
                this.rackItems = [];
                if (window.topProgressBar) window.topProgressBar.start();

                const url = '/api/rack/' + encodeURIComponent(rackId) + '/inventory' + (forceRefresh ? '?_t=' + now : '');
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        this.isLoadingRack = false;
                        if (window.topProgressBar) window.topProgressBar.finish();
                        if (data.status === 'success') {
                            const items = data.items || [];
                            this.rackItems = items;

                            // Simpan ke Cache Memory & SessionStorage
                            const cachePayload = { timestamp: now, items: items };
                            this.inventoryCache[rackId] = cachePayload;
                            try {
                                sessionStorage.setItem('wms_inv_' + rackId, JSON.stringify(cachePayload));
                            } catch (e) {}

                            if (items.length > 0) {
                                // Auto select the first FEFO item
                                this.selectItem(items[0]);
                            }
                        }
                    })
                    .catch(err => {
                        this.isLoadingRack = false;
                        if (window.topProgressBar) window.topProgressBar.finish();
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
                try {
                    // Purge client-side cache for this rack ID before submit
                    if (this.form.rack_id) {
                        sessionStorage.removeItem('wms_inv_' + this.form.rack_id);
                        delete this.inventoryCache[this.form.rack_id];
                    }
                } catch(e) {}
                document.getElementById('outboundV2Form').submit();
            }
        }
    }
</script>
@endsection
