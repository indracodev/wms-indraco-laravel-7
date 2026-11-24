@extends('layouts.app')

@section('title', 'Transfer Antar Palet & Relokasi BIN')

@section('styles')
<style>
    .modal-backdrop-blur {
        background-color: rgba(15, 23, 42, 0.7) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
    }

    /* CRITICAL THEME OVERRIDE: Container #004b87 has pure white text by default */
    html.light .transfer-main-container,
    html.light .transfer-main-container h1,
    html.light .transfer-main-container h2,
    html.light .transfer-main-container h3,
    html.light .transfer-main-container h4,
    html.light .transfer-main-container p,
    html.light .transfer-main-container label,
    html.light .transfer-main-container .font-extrabold,
    html.light .transfer-main-container .text-white,
    html.light [style*="#004b87"] h3,
    html.light [style*="#004b87"] .font-extrabold,
    .transfer-main-container,
    .transfer-main-container h2,
    .transfer-main-container h3,
    .transfer-main-container p,
    .transfer-main-container label,
    .transfer-main-container .text-white {
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
        $binsData = [];
    }
    if (!isset($palletsData)) {
        $palletsData = [];
    }
@endphp

@section('content')
<div class="space-y-6" x-data="transferWorkflow()">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white">
                Transfer & Relokasi Stok
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Transfer Produk Antar Palet (Mendukung Campur Produk) & Relokasi Palet ke BIN Lain</p>
        </div>
    </div>

    <!-- MAIN FORM CONTAINER (#004b87 Background with Pure White Text) -->
    <div class="transfer-main-container p-4 sm:p-8 rounded-2xl shadow-2xl space-y-6"
         style="background-color: #004b87 !important; border: 2px solid #003661 !important; color: #ffffff !important;">

        <!-- Workflow Tab Switcher -->
        <div class="flex flex-wrap items-center gap-2 border-b border-sky-600/50 pb-3">
            <button type="button" 
                    @click="activeMode = 'pallet_to_pallet'" 
                    :style="activeMode === 'pallet_to_pallet' 
                        ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' 
                        : 'background-color: rgba(0, 39, 77, 0.6) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 255, 255, 0.25) !important;'"
                    class="px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-bold shadow-md">
                <span>1. Transfer Antar Palet (Campur Produk)</span>
            </button>

            <button type="button" 
                    @click="activeMode = 'pallet_to_bin'" 
                    :style="activeMode === 'pallet_to_bin' 
                        ? 'background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;' 
                        : 'background-color: rgba(0, 39, 77, 0.6) !important; color: #ffffff !important; border: 1.5px solid rgba(255, 255, 255, 0.25) !important;'"
                    class="px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center cursor-pointer font-bold shadow-md">
                <span>2. Relokasi Palet ke BIN Lain</span>
            </button>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: TRANSFER PRODUK ANTAR PALET                                        -->
        <!-- ========================================================================= -->
        <div x-show="activeMode === 'pallet_to_pallet'" class="space-y-6">
            <div class="border-b border-sky-600/50 pb-3">
                <h3 style="color: #ffffff !important;" class="text-base font-extrabold text-white">
                    Transfer Produk Antar Palet (Konsolidasi & Mixed-Pallet)
                </h3>
                <p style="color: #e0f2fe !important;" class="text-xs text-sky-100 mt-1">
                    Pindahkan sebagian atau seluruh box dari Palet Asal ke Palet Tujuan. Fitur transfer ini mengizinkan penerimaan produk berbeda pada palet tujuan (Mixed-Pallet).
                </p>
            </div>

            <form action="{{ route('transaction.transfer.pallet_to_pallet') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- SISI KIRI: PALET & PRODUK ASAL (WHITE CARD) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="card-white p-4 sm:p-5 rounded-2xl shadow-lg space-y-4">
                        <div class="font-extrabold text-xs uppercase tracking-wider text-[#00274d] border-b border-slate-200 pb-2.5">
                            <span class="text-darkblue">Sumber: Palet & Box Asal</span>
                        </div>

                        <!-- Scan / Input Barcode Palet Asal -->
                        <div>
                            <label class="block text-xs font-bold text-[#00274d] mb-1.5">
                                Scan Barcode Palet Asal <span class="text-danger">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       name="source_pallet_id" 
                                       id="source_pallet_input"
                                       x-model="transferData.source_pallet_id" 
                                       @change="loadSourcePalletInfo()"
                                       required 
                                       placeholder="e.g. PLT-001" 
                                       style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl font-mono text-sm uppercase font-black focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                                <button type="button" 
                                        @click="openCameraScanner(val => { transferData.source_pallet_id = val; loadSourcePalletInfo(); })" 
                                        style="background-color: #004b87 !important; color: #ffffff !important;"
                                        class="btn-preserve px-4 py-3 rounded-xl shadow text-xs font-bold cursor-pointer whitespace-nowrap hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-camera mr-1 text-white"></i>
                                    <span class="text-white">Kamera</span>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Cari Palet Asal (Mobile & Desktop Friendly) -->
                        <div>
                            <label class="block text-[11px] text-muted mb-1 font-semibold">Atau Pilih Palet Asal Aktif (Modal Cari):</label>
                            
                            <button type="button" 
                                    @click="openPalletSearchModal('source')"
                                    style="background-color: #f8fafc !important; color: #004b87 !important; border: 1.5px solid #cbd5e1 !important;"
                                    class="w-full p-3 rounded-xl flex items-center justify-between text-left transition active:scale-[0.99] shadow-sm hover:bg-sky-50 cursor-pointer group">
                                <div class="truncate mr-2">
                                    <template x-if="transferData.source_pallet_id">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <div>
                                                <div class="font-mono font-black text-sm text-[#00274d] tracking-wide" x-text="'Palet: ' + transferData.source_pallet_id"></div>
                                                <div class="text-[10px] text-slate-500 font-semibold" x-text="sourceBoxes.length > 0 ? sourceBoxes.length + ' Box Tersedia' : 'Klik untuk ganti palet'"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!transferData.source_pallet_id">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                            <span class="text-xs font-semibold text-slate-500 italic">Pilih Palet</span>
                                        </div>
                                    </template>
                                </div>
                                <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                                      class="btn-preserve px-3.5 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow group-hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-magnifying-glass text-xs" style="color: #ffffff !important;"></i>
                                    <span style="color: #ffffff !important;" x-text="transferData.source_pallet_id ? 'Ganti Palet' : 'Cari Palet'"></span>
                                </span>
                            </button>
                        </div>

                        <!-- Box Asal Selector -->
                        <div x-show="sourceBoxes.length > 0" class="pt-1">
                            <label class="block text-xs font-bold text-[#00274d] mb-1.5">
                                Pilih Box / Batch Produk yang Dipindahkan <span class="text-danger">*</span>
                            </label>
                            <select name="source_box_id" 
                                    id="source_box_id_select"
                                    x-model="transferData.source_box_id" 
                                    @change="onSourceBoxChange()" 
                                    required 
                                    style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                    class="w-full p-3 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                                <option value="">-- Pilih Box Spesifik --</option>
                                <template x-for="box in sourceBoxes" :key="box.id">
                                    <option :value="box.id" x-text="box.id + ' (' + box.product_name + ') - Stok: ' + box.qty + ' ' + box.uom_base + ' [ED: ' + box.expired_date + ']'"></option>
                                </template>
                            </select>
                        </div>

                        <!-- Qty & Unit Input -->
                        <div class="grid grid-cols-2 gap-3 pt-1" x-show="selectedSourceBox">
                            <div>
                                <label class="block text-xs font-bold text-[#00274d] mb-1">
                                    Qty Dipindahkan <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       step="any" 
                                       name="qty" 
                                       x-model.number="transferData.qty" 
                                       required 
                                       min="0.01" 
                                       :max="selectedSourceBox ? selectedSourceBox.qty : 999999"
                                       style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl text-sm font-black focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#00274d] mb-1">Satuan</label>
                                <input type="text" 
                                       name="unit" 
                                       x-model="transferData.unit" 
                                       readonly 
                                       style="background-color: #e2e8f0 !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl text-sm font-black cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- SISI KANAN: PALET TUJUAN (WHITE CARD) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="card-white p-4 sm:p-5 rounded-2xl shadow-lg space-y-4">
                        <div class="font-extrabold text-xs uppercase tracking-wider text-[#00274d] border-b border-slate-200 pb-2.5">
                            <span class="text-darkblue">Tujuan: Palet Penerima (Mixed Pallet)</span>
                        </div>

                        <!-- Scan / Input Barcode Palet Tujuan -->
                        <div>
                            <label class="block text-xs font-bold text-[#00274d] mb-1.5">
                                Scan Barcode Palet Tujuan <span class="text-danger">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       name="target_pallet_id" 
                                       id="target_pallet_input"
                                       x-model="transferData.target_pallet_id" 
                                       @change="loadTargetPalletInfo()"
                                       required 
                                       placeholder="e.g. PLT-002" 
                                       style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl font-mono text-sm uppercase font-black focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                                <button type="button" 
                                        @click="openCameraScanner(val => { transferData.target_pallet_id = val; loadTargetPalletInfo(); })" 
                                        style="background-color: #004b87 !important; color: #ffffff !important;"
                                        class="btn-preserve px-4 py-3 rounded-xl shadow text-xs font-bold cursor-pointer whitespace-nowrap hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-camera mr-1 text-white"></i>
                                    <span class="text-white">Kamera</span>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Cari Palet Tujuan (Mobile & Desktop Friendly) -->
                        <div>
                            <label class="block text-[11px] text-muted mb-1 font-semibold">Atau Pilih Palet Tujuan Terdaftar (Modal Cari):</label>
                            
                            <button type="button" 
                                    @click="openPalletSearchModal('target')"
                                    style="background-color: #f8fafc !important; color: #004b87 !important; border: 1.5px solid #cbd5e1 !important;"
                                    class="w-full p-3 rounded-xl flex items-center justify-between text-left transition active:scale-[0.99] shadow-sm hover:bg-sky-50 cursor-pointer group">
                                <div class="truncate mr-2">
                                    <template x-if="transferData.target_pallet_id">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <div>
                                                <div class="font-mono font-black text-sm text-[#00274d] tracking-wide" x-text="'Palet: ' + transferData.target_pallet_id"></div>
                                                <div class="text-[10px] text-slate-500 font-semibold" x-text="targetPalletInfo.is_new ? 'Palet Baru' : 'Lokasi: ' + (targetPalletInfo.pallet_status === 'on_rack' ? 'Rak BIN ' + targetPalletInfo.bin_id : 'In Transit')"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!transferData.target_pallet_id">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                            <span class="text-xs font-semibold text-slate-500 italic">Pilih Palet</span>
                                        </div>
                                    </template>
                                </div>
                                <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                                      class="btn-preserve px-3.5 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow group-hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-magnifying-glass text-xs" style="color: #ffffff !important;"></i>
                                    <span style="color: #ffffff !important;" x-text="transferData.target_pallet_id ? 'Ganti Palet' : 'Cari Palet'"></span>
                                </span>
                            </button>
                        </div>

                        <!-- Status Info Palet Tujuan -->
                        <div style="background-color: #f8fafc !important; border: 1.5px solid #e2e8f0 !important;" 
                             class="p-4 rounded-xl space-y-2.5 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-muted font-semibold">Status Palet Tujuan:</span>
                                <span class="font-black text-[#004b87]" x-text="targetPalletInfo.is_new ? 'Palet Baru' : (targetPalletInfo.pallet_status === 'on_rack' ? 'Di Rak BIN ' + targetPalletInfo.bin_id : 'In Transit')"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-muted font-semibold">Muatan Saat Ini:</span>
                                <span class="font-black text-amber-600" x-text="(targetPalletInfo.total_qty || 0) + ' Unit'"></span>
                            </div>

                            <!-- Mixed Pallet Note -->
                            <div class="pt-2 border-t border-slate-200 text-[11px] font-semibold text-emerald-700">
                                <span>Mengizinkan penerimaan produk berbeda (campur produk).</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-t border-sky-600/50 flex justify-end">
                    <button type="submit" 
                            :disabled="!transferData.source_pallet_id || !transferData.source_box_id || !transferData.target_pallet_id || !transferData.qty"
                            :class="(!transferData.source_pallet_id || !transferData.source_box_id || !transferData.target_pallet_id || !transferData.qty) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#003661] shadow-lg shadow-sky-950/40'"
                            style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;"
                            class="px-8 py-3.5 font-black text-sm rounded-xl transition flex items-center justify-center gap-2 cursor-pointer active:scale-95">
                        <span style="color: #004b87 !important;">Eksekusi Transfer Antar Palet</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 2: RELOKASI PALET KE BIN DENGAN MODAL PENCARIAN                       -->
        <!-- ========================================================================= -->
        <div x-show="activeMode === 'pallet_to_bin'" class="space-y-6">
            <div class="border-b border-sky-600/50 pb-3">
                <h3 style="color: #ffffff !important;" class="text-base font-extrabold text-white">
                    Relokasi Posisi Palet ke Rak / BIN Baru
                </h3>
                <p style="color: #e0f2fe !important;" class="text-xs text-sky-100 mt-1">
                    Memindahkan satu palet fisik secara utuh beserta seluruh isinya ke slot BIN tujuan.
                </p>
            </div>

            <form action="{{ route('transaction.transfer') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Palet yang Dipindahkan (WHITE CARD) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="card-white p-4 sm:p-5 rounded-2xl shadow-lg space-y-4">
                        <div class="font-extrabold text-xs uppercase tracking-wider text-[#00274d] border-b border-slate-200 pb-2.5">
                            <span class="text-darkblue">1. Palet yang Dipindahkan</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#00274d] mb-1.5">
                                Scan Barcode Palet <span class="text-danger">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       name="pallet_id" 
                                       id="relocate_pallet_input" 
                                       x-model="selectedRelocatePallet"
                                       required 
                                       placeholder="e.g. PLT-001" 
                                       style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl font-mono text-sm uppercase font-black focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                                <button type="button" 
                                        @click="openCameraScanner(val => { selectedRelocatePallet = val; document.getElementById('relocate_pallet_input').value = val; })" 
                                        style="background-color: #004b87 !important; color: #ffffff !important;"
                                        class="btn-preserve px-4 py-3 rounded-xl shadow text-xs font-bold cursor-pointer whitespace-nowrap hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-camera mr-1 text-white"></i>
                                    <span class="text-white">Kamera</span>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Cari Palet Relokasi -->
                        <div>
                            <label class="block text-[11px] text-muted mb-1 font-semibold">Atau Pilih Palet dari Database (Modal Cari):</label>
                            
                            <button type="button" 
                                    @click="openPalletSearchModal('relocate')"
                                    style="background-color: #f8fafc !important; color: #004b87 !important; border: 1.5px solid #cbd5e1 !important;"
                                    class="w-full p-3 rounded-xl flex items-center justify-between text-left transition active:scale-[0.99] shadow-sm hover:bg-sky-50 cursor-pointer group">
                                <div class="truncate mr-2">
                                    <template x-if="selectedRelocatePallet">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <div>
                                                <div class="font-mono font-black text-sm text-[#00274d] tracking-wide" x-text="'Palet: ' + selectedRelocatePallet"></div>
                                                <div class="text-[10px] text-slate-500 font-semibold">Klik untuk mengganti palet</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedRelocatePallet">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                            <span class="text-xs font-semibold text-slate-500 italic">Pilih Palet</span>
                                        </div>
                                    </template>
                                </div>
                                <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                                      class="btn-preserve px-3.5 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow group-hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-magnifying-glass text-xs" style="color: #ffffff !important;"></i>
                                    <span style="color: #ffffff !important;" x-text="selectedRelocatePallet ? 'Ganti Palet' : 'Cari Palet'"></span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- BIN Tujuan Menggunakan Modal Pencarian (WHITE CARD) -->
                    <div style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="card-white p-4 sm:p-5 rounded-2xl shadow-lg space-y-4">
                        <div class="font-extrabold text-xs uppercase tracking-wider text-[#00274d] border-b border-slate-200 pb-2.5">
                            <span class="text-darkblue">2. Slot BIN Tujuan</span>
                        </div>

                        <!-- Scan Manual / Barcode Scanner Fisik -->
                        <div>
                            <label class="block text-xs font-bold text-[#00274d] mb-1.5">
                                Scan Barcode Slot BIN Fisik <span class="text-danger">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       name="target_bin_id" 
                                       id="relocate_bin_input" 
                                       x-model="selectedRelocateBin"
                                       required 
                                       placeholder="e.g. B.10.1" 
                                       style="background-color: #f8fafc !important; color: #00274d !important; border: 1.5px solid #cbd5e1 !important;"
                                       class="w-full p-3 rounded-xl font-mono text-sm uppercase font-black focus:outline-none focus:ring-2 focus:ring-[#004b87]">
                                <button type="button" 
                                        @click="openCameraScanner(val => { selectedRelocateBin = val; document.getElementById('relocate_bin_input').value = val; })" 
                                        style="background-color: #004b87 !important; color: #ffffff !important;"
                                        class="btn-preserve px-4 py-3 rounded-xl shadow text-xs font-bold cursor-pointer whitespace-nowrap hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-camera mr-1 text-white"></i>
                                    <span class="text-white">Kamera</span>
                                </button>
                            </div>
                        </div>

                        <!-- Pilih BIN Menggunakan Modal Pencarian -->
                        <div>
                            <label class="block text-[11px] text-muted mb-1 font-semibold">Atau Pilih BIN dari Database (Modal Pencarian):</label>
                            
                            <!-- Trigger Card Modal Pencarian -->
                            <button type="button" 
                                    @click="openBinSearchModal()"
                                    style="background-color: #f8fafc !important; color: #004b87 !important; border: 1.5px solid #cbd5e1 !important;"
                                    class="w-full p-3 rounded-xl flex items-center justify-between text-left transition active:scale-[0.99] shadow-sm hover:bg-sky-50 cursor-pointer group">
                                <div class="truncate mr-2">
                                    <template x-if="selectedRelocateBin">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                                            <div>
                                                <div class="font-mono font-black text-sm text-[#00274d] tracking-wide" x-text="'BIN Terpilih: ' + selectedRelocateBin"></div>
                                                <div class="text-[10px] text-slate-500 font-semibold">Klik untuk mengganti lokasi BIN</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedRelocateBin">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0 animate-pulse"></span>
                                            <span class="text-xs font-semibold text-slate-500 italic">Pilih Slot BIN Tujuan dari Database...</span>
                                        </div>
                                    </template>
                                </div>
                                <span style="background-color: #004b87 !important; color: #ffffff !important;" 
                                      class="btn-preserve px-3.5 py-2 rounded-xl text-xs font-black shrink-0 flex items-center gap-1.5 shadow group-hover:bg-[#003661] active:scale-95 transition">
                                    <i class="fa-solid fa-magnifying-glass text-xs" style="color: #ffffff !important;"></i>
                                    <span style="color: #ffffff !important;" x-text="selectedRelocateBin ? 'Ganti BIN' : 'Cari BIN'"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 border-t border-sky-600/50 flex justify-end">
                    <button type="submit" 
                            style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #ffffff !important;"
                            class="px-8 py-3.5 font-black text-sm rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-sky-950/40 hover:bg-[#003661] active:scale-95">
                        <span style="color: #004b87 !important;">Eksekusi Relokasi BIN</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 1. MODAL PENCARIAN PALET (Pallet Search Modal - Mobile & Desktop Friendly) -->
    <!-- ========================================================================= -->
    <div x-show="showPalletModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 modal-backdrop-blur"
         @keydown.escape.window="closePalletSearchModal()">
        
        <div class="fixed inset-0" @click="closePalletSearchModal()"></div>

        <div class="relative w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col z-10 max-h-[88vh] sm:max-h-[85vh] border-2 border-sky-400"
             style="background-color: #004b87 !important;">
            
            <!-- Header Modal -->
            <div class="p-4 sm:p-5 flex items-center justify-between border-b border-sky-600/50"
                 style="background-color: #003661 !important;">
                <div>
                    <h4 style="color: #ffffff !important;" class="text-base font-extrabold text-white" x-text="palletModalTitle"></h4>
                    <p style="color: #bae6fd !important;" class="text-xs text-sky-200">Cari dari daftar palet aktif di gudang atau in transit</p>
                </div>
                <button type="button" 
                        @click="closePalletSearchModal()" 
                        style="background-color: #004b87 !important; color: #ffffff !important;"
                        class="btn-preserve w-8 h-8 rounded-full flex items-center justify-center hover:bg-sky-700 text-white transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm" style="color: #ffffff !important;"></i>
                </button>
            </div>

            <!-- Search Input Box -->
            <div class="p-4 space-y-3 shrink-0" style="background-color: rgba(0, 39, 77, 0.4) !important;">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model.debounce.200ms="palletSearchQuery" 
                           @input="palletDisplayLimit = 40; searchPalletsFromServer();"
                           id="transfer_pallet_search_input"
                           placeholder="Ketik ID palet, SKU, nama produk, atau lokasi..." 
                           style="background-color: #ffffff !important; color: #00274d !important; border: 2px solid #bae6fd !important;"
                           class="w-full pl-9 pr-8 py-2.5 rounded-xl text-xs sm:text-sm font-mono uppercase font-black placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-white shadow-inner">
                    <button x-show="palletSearchQuery" 
                            @click="palletSearchQuery = ''; palletDisplayLimit = 40;" 
                            type="button"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>

                <!-- Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button type="button" 
                            @click="palletFilterTab = 'all'; palletDisplayLimit = 40;" 
                            :style="palletFilterTab === 'all' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Semua Palet (<span x-text="allPallets.length"></span>)
                    </button>
                    <button type="button" 
                            @click="palletFilterTab = 'has_boxes'; palletDisplayLimit = 40;" 
                            :style="palletFilterTab === 'has_boxes' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Ada Muatan Box (<span x-text="allPallets.filter(p => p.has_content).length"></span>)
                    </button>
                    <button type="button" 
                            @click="palletFilterTab = 'empty'; palletDisplayLimit = 40;" 
                            :style="palletFilterTab === 'empty' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Kosong / In Transit (<span x-text="allPallets.filter(p => !p.has_content).length"></span>)
                    </button>
                </div>
            </div>

            <!-- List Result Cards (White cards with Dark Blue text - Virtualized Chunking) -->
            <div class="p-3 sm:p-4 overflow-y-auto space-y-2.5 flex-1" style="background-color: #003661 !important;">
                <template x-for="p in displayedPallets" :key="p.id">
                    <div @click="selectPalletFromModal(p)"
                         style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-3 sm:p-3.5 rounded-xl shadow-md hover:border-amber-400 hover:shadow-lg cursor-pointer transition active:scale-[0.99] flex items-center justify-between gap-3 group">
                        
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-sm sm:text-base text-[#00274d] group-hover:text-[#004b87]" x-text="p.id"></span>
                                <template x-if="p.has_content">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span x-text="(p.box_count || 1) + ' Box'"></span>
                                    </span>
                                </template>
                                <template x-if="!p.has_content">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-300">
                                        Kosong
                                    </span>
                                </template>
                            </div>
                            <div class="text-xs text-slate-500 font-medium truncate">
                                <span class="font-semibold text-[#004b87]" x-text="p.location"></span>
                                <span x-show="p.product_id" x-text="' | ' + p.product_id + (p.product_name ? ' (' + p.product_name + ')' : '')"></span>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-black text-[#004b87]" x-text="(p.total_qty || 0) + ' Unit'"></div>
                            <span class="text-[11px] font-bold text-amber-600 group-hover:underline">Pilih Palet &rarr;</span>
                        </div>
                    </div>
                </template>

                <!-- Load More Button for Chunked Rendering -->
                <div x-show="filteredPallets.length > palletDisplayLimit" class="pt-2 text-center pb-2">
                    <button type="button" 
                            @click="palletDisplayLimit += 40" 
                            class="w-full py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 active:scale-98 cursor-pointer shadow">
                        <i class="fa-solid fa-angles-down text-[11px]"></i>
                        <span>Muat 40 Palet Lainnya (Sisa <span x-text="filteredPallets.length - palletDisplayLimit"></span>)</span>
                    </button>
                </div>

                <div x-show="filteredPallets.length === 0" 
                     style="background-color: #ffffff !important; color: #004b87 !important;"
                     class="p-6 rounded-xl text-center text-xs shadow-md">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-base mb-1 block"></i>
                    <span class="font-bold text-[#00274d]">Tidak ditemukan palet yang sesuai dengan kriteria.</span>
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="p-3 border-t border-sky-600/50 text-center text-xs text-sky-200 shrink-0" style="background-color: #00274d !important;">
                Menampilkan <strong class="text-white" x-text="Math.min(palletDisplayLimit, filteredPallets.length)"></strong> dari <span x-text="filteredPallets.length"></span> Palet ditemukan
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. MODAL PENCARIAN BIN GUDANG (Search Modal for Slot BIN)                 -->
    <!-- ========================================================================= -->
    <div x-show="showBinModal" 
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 modal-backdrop-blur"
         @keydown.escape.window="closeBinSearchModal()">
        
        <div class="fixed inset-0" @click="closeBinSearchModal()"></div>

        <div class="relative w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col z-10 max-h-[88vh] sm:max-h-[85vh] border-2 border-sky-400"
             style="background-color: #004b87 !important;">
            
            <!-- Header Modal -->
            <div class="p-4 sm:p-5 flex items-center justify-between border-b border-sky-600/50"
                 style="background-color: #003661 !important;">
                <div>
                    <h4 style="color: #ffffff !important;" class="text-base font-extrabold text-white">Pilih Slot BIN Tujuan</h4>
                    <p style="color: #bae6fd !important;" class="text-xs text-sky-200">Cari dari daftar seluruh slot BIN gudang yang tersedia</p>
                </div>
                <button type="button" 
                        @click="closeBinSearchModal()" 
                        style="background-color: #004b87 !important; color: #ffffff !important;"
                        class="btn-preserve w-8 h-8 rounded-full flex items-center justify-center hover:bg-sky-700 text-white transition active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm" style="color: #ffffff !important;"></i>
                </button>
            </div>

            <!-- Search Input Box -->
            <div class="p-4 space-y-3 shrink-0" style="background-color: rgba(0, 39, 77, 0.4) !important;">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model.debounce.200ms="binSearchQuery" 
                           @input="binDisplayLimit = 40; searchBinsFromServer();"
                           id="transfer_bin_search_input"
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
                            @click="binFilterTab = 'empty'; binDisplayLimit = 40;" 
                            :style="binFilterTab === 'empty' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Kosong / Belum Penuh (<span x-text="allBins.filter(b => !b.has_stock).length"></span>)
                    </button>
                    <button type="button" 
                            @click="binFilterTab = 'has_stock'; binDisplayLimit = 40;" 
                            :style="binFilterTab === 'has_stock' ? 'background-color: #ffffff !important; color: #004b87 !important; border: 1.5px solid #ffffff !important;' : 'background-color: rgba(255, 255, 255, 0.15) !important; color: #ffffff !important; border: 1px solid rgba(255, 255, 255, 0.3) !important;'"
                            class="px-3 py-1.5 rounded-lg font-bold transition whitespace-nowrap active:scale-95 cursor-pointer">
                        Ada Stok (<span x-text="allBins.filter(b => b.has_stock).length"></span>)
                    </button>
                </div>
            </div>

            <!-- List Result Cards (White cards with Dark Blue text - Virtualized Chunking) -->
            <div class="p-3 sm:p-4 overflow-y-auto space-y-2.5 flex-1" style="background-color: #003661 !important;">
                <template x-for="b in displayedBins" :key="b.id">
                    <div @click="selectBinFromModal(b.id)"
                         style="background-color: #ffffff !important; color: #004b87 !important; border: 2px solid #cbd5e1 !important;"
                         class="p-3 sm:p-3.5 rounded-xl shadow-md hover:border-amber-400 hover:shadow-lg cursor-pointer transition active:scale-[0.99] flex items-center justify-between gap-3 group">
                        
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-black text-sm sm:text-base text-[#00274d] group-hover:text-[#004b87]" x-text="b.id"></span>
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

                <!-- Load More Button for Chunked Rendering -->
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
</div>

@section('scripts')
<script>
    function transferWorkflow() {
        return {
            activeMode: 'pallet_to_pallet',
            sourceBoxes: [],
            selectedSourceBox: null,

            // BIN Modal Search State
            showBinModal: false,
            binSearchQuery: '',
            binFilterTab: 'all',
            binDisplayLimit: 40,
            allBins: {!! json_encode($binsData ?? []) !!},
            selectedRelocateBin: '',

            // Pallet Modal Search State (Mobile & Desktop Friendly)
            showPalletModal: false,
            palletModalTarget: 'source', // 'source' | 'target' | 'relocate'
            palletSearchQuery: '',
            palletFilterTab: 'all',
            palletDisplayLimit: 40,
            allPallets: {!! json_encode($palletsData ?? []) !!},
            selectedRelocatePallet: '',

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

            get palletModalTitle() {
                if (this.palletModalTarget === 'source') return 'Pilih Palet Asal';
                if (this.palletModalTarget === 'target') return 'Pilih Palet Tujuan';
                return 'Pilih Palet yang Ingin Direlokasi';
            },

            // --- PALLET SEARCH MODAL LOGIC ---
            openPalletSearchModal(target) {
                this.palletModalTarget = target;
                this.showPalletModal = true;
                this.palletSearchQuery = '';
                this.palletDisplayLimit = 40;
                this.$nextTick(() => {
                    const inp = document.getElementById('transfer_pallet_search_input');
                    if (inp) inp.focus();
                });
            },

            closePalletSearchModal() {
                this.showPalletModal = false;
            },

            selectPalletFromModal(p) {
                if (this.palletModalTarget === 'source') {
                    this.transferData.source_pallet_id = p.id;
                    const inp = document.getElementById('source_pallet_input');
                    if (inp) inp.value = p.id;
                    this.loadSourcePalletInfo();
                } else if (this.palletModalTarget === 'target') {
                    this.transferData.target_pallet_id = p.id;
                    const inp = document.getElementById('target_pallet_input');
                    if (inp) inp.value = p.id;
                    this.loadTargetPalletInfo();
                } else if (this.palletModalTarget === 'relocate') {
                    this.selectedRelocatePallet = p.id;
                    const inp = document.getElementById('relocate_pallet_input');
                    if (inp) {
                        inp.value = p.id;
                        inp.dispatchEvent(new Event('input', { bubbles: true }));
                        inp.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                this.closePalletSearchModal();
            },

            get filteredPallets() {
                let list = this.allPallets;
                const q = (this.palletSearchQuery || '').trim().toLowerCase();

                if (q) {
                    list = list.filter(p => 
                        (p.id && p.id.toLowerCase().includes(q)) || 
                        (p.location && p.location.toLowerCase().includes(q)) ||
                        (p.product_id && p.product_id.toLowerCase().includes(q)) ||
                        (p.product_name && p.product_name.toLowerCase().includes(q))
                    );
                }

                if (this.palletFilterTab === 'has_boxes') {
                    list = list.filter(p => p.has_content);
                } else if (this.palletFilterTab === 'empty') {
                    list = list.filter(p => !p.has_content);
                }

                return list;
            },

            get displayedPallets() {
                return this.filteredPallets.slice(0, this.palletDisplayLimit);
            },

            searchPalletsFromServer() {
                const q = (this.palletSearchQuery || '').trim();
                if (q.length < 2) return;

                fetch(`/api/pallets/search?q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            const existingMap = new Map(this.allPallets.map(p => [p.id, p]));
                            data.forEach(np => existingMap.set(np.id, np));
                            this.allPallets = Array.from(existingMap.values());
                        }
                    })
                    .catch(err => console.error('Error searching pallets:', err));
            },

            // --- BIN SEARCH MODAL LOGIC ---
            openBinSearchModal() {
                this.showBinModal = true;
                this.binSearchQuery = '';
                this.binDisplayLimit = 40;
                this.$nextTick(() => {
                    const inp = document.getElementById('transfer_bin_search_input');
                    if (inp) inp.focus();
                });
            },

            closeBinSearchModal() {
                this.showBinModal = false;
            },

            selectBinFromModal(binId) {
                this.selectedRelocateBin = binId;
                const input = document.getElementById('relocate_bin_input');
                if (input) {
                    input.value = binId;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.closeBinSearchModal();
            },

            get filteredBins() {
                let list = this.allBins;
                const q = (this.binSearchQuery || '').trim().toLowerCase();
                
                if (q) {
                    list = list.filter(b => 
                        (b.id && b.id.toLowerCase().includes(q)) || 
                        (b.rack_id && b.rack_id.toLowerCase().includes(q)) ||
                        (b.warehouse_name && b.warehouse_name.toLowerCase().includes(q))
                    );
                }

                if (this.binFilterTab === 'has_stock') {
                    list = list.filter(b => b.has_stock);
                } else if (this.binFilterTab === 'empty') {
                    list = list.filter(b => !b.has_stock);
                }

                return list;
            },

            get displayedBins() {
                return this.filteredBins.slice(0, this.binDisplayLimit);
            },

            searchBinsFromServer() {
                const q = (this.binSearchQuery || '').trim();
                if (q.length < 2) return;
                
                fetch(`/transaction/outbound/search-bins?q=${encodeURIComponent(q)}&tab=${this.binFilterTab}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success' && data.bins) {
                            const newBins = data.bins;
                            const existingMap = new Map(this.allBins.map(b => [b.id, b]));
                            newBins.forEach(nb => existingMap.set(nb.id, nb));
                            this.allBins = Array.from(existingMap.values());
                        }
                    })
                    .catch(err => console.error('Error searching bins:', err));
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
