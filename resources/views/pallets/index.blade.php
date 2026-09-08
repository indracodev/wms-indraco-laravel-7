@extends('layouts.app')

@section('title', 'Manajemen & Tracking Palet')

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
        background-color: #10b981 !important;
        color: #ffffff !important;
    }
    .ts-dropdown .option.selected {
        background-color: #1e293b !important;
        color: #34d399 !important;
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
        background-color: #10b981 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<!-- Top Progress Bar (Loading State Indicator) -->
<div id="top-progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-emerald-400 via-indigo-500 to-purple-500 z-[100] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<!-- Toast Container for Floating Alerts -->
<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none"></div>

<div class="space-y-6" x-data="palletManagement()">
    <!-- Header Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-pallet text-indigo-400"></i>
                <span>Manajemen & Tracking Palet Gudang</span>
            </h2>
            <p class="text-sm text-slate-400">Pendaftaran palet baru, penambahan produk/box ke palet, check isi stok, lokasi BIN, & barcode tracking</p>
        </div>
        <div>
            @canAccessModule('pallet', 'create')
                <button type="button" 
                        @click="openRegisterModal()" 
                        class="px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs sm:text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-plus text-sm"></i>
                    <span>Daftarkan Palet Baru</span>
                </button>
            @endcanAccessModule
        </div>
    </div>

    <!-- Filter & Search Bar (SPA Zero Page Reload) -->
    <div class="glass-panel p-5 rounded-2xl">
        <form method="GET" action="{{ route('pallets.index') }}" onsubmit="PalletApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3.5 text-xs">
            <div>
                <label class="block text-slate-400 font-semibold mb-1">Cari Barcode / Lokasi BIN</label>
                <div class="flex items-center space-x-1.5">
                    <input type="text" 
                           name="search" 
                           id="pallet_search_input" 
                           value="{{ $search }}" 
                           class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase font-mono" 
                           placeholder="e.g. PLT-0001, B.10.2">
                    <button type="button" 
                            onclick="openCameraScanner(val => { document.getElementById('pallet_search_input').value = val; })" 
                            class="p-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center justify-center cursor-pointer transition" 
                            title="Scan Barcode Kamera">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-slate-400 font-semibold mb-1">Filter Lokasi BIN</label>
                <select name="bin_id" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua BIN --</option>
                    @foreach($bins as $b)
                        <option value="{{ $b->id }}" {{ $filterBin == $b->id ? 'selected' : '' }}>BIN: {{ $b->id }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 font-semibold mb-1">Filter Jenis Palet</label>
                <select name="pallet_type_id" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua Jenis Palet --</option>
                    @foreach($palletTypes as $pt)
                        <option value="{{ $pt->id }}" {{ $filterType == $pt->id ? 'selected' : '' }}>{{ $pt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-slate-400 font-semibold mb-1">Filter Status Stock</label>
                <select name="status" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ $filterStatus == 'active' ? 'selected' : '' }}>Terisi Stok (Aktif)</option>
                    <option value="empty" {{ $filterStatus == 'empty' ? 'selected' : '' }}>Kosong / Transit</option>
                    <option value="inactive" {{ $filterStatus == 'inactive' ? 'selected' : '' }}>Non-Aktif / Maintenance</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow transition cursor-pointer flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-filter mr-1"></i> <span class="btn-text">Filter</span>
                </button>
                <button type="button" onclick="PalletApp.loadData('{{ route('pallets.index') }}')" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- DYNAMICALLY LOADED ASYNC TABLE & STATS CONTAINER -->
    <div id="pallet-data-container" class="transition-all duration-200 min-h-[400px]">
        @include('pallets.partials.table')
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: CHECK & INSPECT ISI PALET                                       -->
    <!-- ========================================================================= -->
    <div x-show="showInspectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-3xl p-6 sm:p-8 rounded-2xl shadow-2xl space-y-5 border border-indigo-500/40 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-3 text-indigo-400">
                    <i class="fa-solid fa-boxes-stacked text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Detail Isi & Stok Palet: <span class="font-mono text-indigo-400" x-text="inspectData.pallet.id"></span></h3>
                        <p class="text-xs text-slate-400">Lokasi: <span class="text-amber-300 font-bold font-mono" x-text="inspectData.pallet.bin_id"></span> | Jenis: <span class="text-slate-200" x-text="inspectData.pallet.type_name"></span></p>
                    </div>
                </div>
                <button type="button" @click="showInspectModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div x-show="loadingInspect" class="p-8 text-center text-slate-400 text-sm space-y-2">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-500"></i>
                <div>Memuat rincian isi palet...</div>
            </div>

            <!-- Inspect Content -->
            <div x-show="!loadingInspect" class="space-y-5">
                <!-- Stock Totals Breakdown (Smallest Unit) -->
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Akumulasi Stok Produk (Satuan Terkecil)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="total in inspectData.stock_totals" :key="total.product_id">
                            <div class="p-3.5 bg-slate-900/80 border border-slate-800 rounded-xl flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-white text-xs" x-text="total.product_name"></div>
                                    <div class="text-[10px] text-slate-400" x-text="'SKU: ' + total.product_id + ' • ' + total.boxes_count + ' Box'"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-extrabold text-emerald-400 font-mono" x-text="total.total_qty"></div>
                                    <div class="text-[10px] text-slate-400 uppercase font-semibold" x-text="total.uom_base"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- List of Active Boxes -->
                <div>
                    <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Daftar Box Terdaftar Pada Palet</h4>
                    <div class="overflow-x-auto border border-slate-800 rounded-xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-800 text-slate-300 uppercase text-[11px]">
                                <tr>
                                    <th class="p-2.5">Box ID</th>
                                    <th class="p-2.5">Produk SKU</th>
                                    <th class="p-2.5">Stok Saat Ini (Base)</th>
                                    <th class="p-2.5">Awal Inbound</th>
                                    <th class="p-2.5">Expired Date</th>
                                    <th class="p-2.5">Kode Mesin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <template x-for="box in inspectData.boxes" :key="box.id">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2.5 font-mono font-bold text-indigo-300" x-text="box.id"></td>
                                        <td class="p-2.5 font-medium text-white" x-text="box.product_name"></td>
                                        <td class="p-2.5 font-extrabold text-emerald-400 font-mono" x-text="box.qty + ' ' + box.uom_base"></td>
                                        <td class="p-2.5 text-slate-400 font-mono" x-text="box.initial_qty + ' ' + box.uom_base"></td>
                                        <td class="p-2.5 text-amber-300 font-medium" x-text="box.expired_date"></td>
                                        <td class="p-2.5 text-slate-300 font-mono" x-text="box.machine_code"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Footer with Direct Add Product Button -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-700/60">
                @canAccessModule('inbound', 'create')
                    <button type="button" 
                            @click="showInspectModal = false; openAddBoxModal(inspectData.pallet.id)" 
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 flex items-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-square-plus"></i>
                        <span>+ Tambah Box / Produk Ke Palet Ini</span>
                    </button>
                @endcanAccessModule
                <button type="button" @click="showInspectModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: PENDAFTARAN PALET BARU                                          -->
    <!-- ========================================================================= -->
    <div x-show="showRegisterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 sm:p-8 rounded-2xl shadow-2xl space-y-5 border border-indigo-500/40">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-3 text-indigo-400">
                    <i class="fa-solid fa-plus-circle text-2xl"></i>
                    <h3 class="text-lg font-bold text-white">Registrasi Barcode Palet Baru</h3>
                </div>
                <button type="button" @click="showRegisterModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('pallets.store') }}" method="POST" onsubmit="PalletApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Barcode Palet ID <span class="text-rose-400">*</span></label>
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input type="text" 
                                   name="id" 
                                   x-model="registerForm.id" 
                                   required 
                                   placeholder="e.g. PLT-8899" 
                                   class="w-full p-3 pl-9 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <i class="fa-solid fa-barcode absolute left-3 top-3.5 text-slate-500"></i>
                        </div>
                        <button type="button" 
                                @click="openCameraScanner(val => registerForm.id = val.toUpperCase())" 
                                class="px-3.5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center space-x-1 text-xs font-semibold cursor-pointer" 
                                title="Scan lewat Kamera HP/Laptop">
                            <i class="fa-solid fa-camera text-sm"></i>
                            <span class="hidden sm:inline">Scan</span>
                        </button>
                        <button type="button" 
                                @click="registerForm.id = 'PLT-' + Math.floor(1000 + Math.random() * 9000)" 
                                class="px-3 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-mono text-xs font-semibold rounded-xl border border-slate-700 cursor-pointer"
                                title="Auto-Generate Kode Palet">
                            Auto
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Jenis Palet <span class="text-rose-400">*</span></label>
                    <select name="pallet_type_id" x-model="registerForm.pallet_type_id" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($palletTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Penempatan BIN Awal (Opsional)</label>
                    <select name="bin_id" x-model="registerForm.bin_id" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="">-- TRANSIT / UNASSIGNED --</option>
                        @foreach($bins as $b)
                            <option value="{{ $b->id }}">
                                BIN: {{ $b->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" name="is_active" id="reg_is_active" value="1" checked class="rounded border-slate-700 text-indigo-600 focus:ring-indigo-500">
                    <label for="reg_is_active" class="text-slate-300 font-medium">Status Palet Aktif (Ready For Use)</label>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showRegisterModal = false" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 cursor-pointer flex items-center justify-center space-x-1.5">
                        <span class="btn-text">Simpan Palet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: EDIT & RELOKASI PALET                                           -->
    <!-- ========================================================================= -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 sm:p-8 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-3 text-amber-400">
                    <i class="fa-solid fa-pen-to-square text-2xl"></i>
                    <h3 class="text-lg font-bold text-white">Edit Palet: <span class="font-mono text-amber-400" x-text="editForm.id"></span></h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('/pallets') }}/' + editForm.id" method="POST" onsubmit="PalletApp.submitEditForm(event, this)" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Jenis Palet <span class="text-rose-400">*</span></label>
                    <select name="pallet_type_id" x-model="editForm.pallet_type_id" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        @foreach($palletTypes as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Lokasi BIN Target</label>
                    <div class="flex items-center space-x-2">
                        <select name="bin_id" x-model="editForm.bin_id" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            <option value="">-- TRANSIT / UNASSIGNED --</option>
                            @foreach($bins as $b)
                                <option value="{{ $b->id }}">
                                    BIN: {{ $b->id }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" 
                                @click="openCameraScanner(val => editForm.bin_id = val.toUpperCase())" 
                                class="px-3 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow flex items-center space-x-1 text-xs font-semibold cursor-pointer" 
                                title="Scan Barcode BIN lewat Kamera">
                            <i class="fa-solid fa-camera text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Status Keaktifan Palet</label>
                    <select name="is_active" x-model="editForm.is_active" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white">
                        <option value="1">Aktif / Siap Gunakan</option>
                        <option value="0">Non-Aktif / Maintenance</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg shadow-amber-600/30 cursor-pointer flex items-center justify-center space-x-1.5">
                        <span class="btn-text">Update Palet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: PALLET TRACKING TIMELINE LOGS                                   -->
    <!-- ========================================================================= -->
    <div x-show="showTrackingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-2xl p-6 sm:p-8 rounded-2xl shadow-2xl space-y-5 border border-purple-500/40 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-3 text-purple-400">
                    <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Pallet Tracking Timeline: <span class="font-mono text-purple-300" x-text="trackingPalletId"></span></h3>
                        <p class="text-xs text-slate-400">Jejak histori mutasi Inbound, Outbound, & Transfer pada palet ini</p>
                    </div>
                </div>
                <button type="button" @click="showTrackingModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Loading Spinner -->
            <div x-show="loadingTracking" class="p-8 text-center text-slate-400 text-sm space-y-2">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl text-purple-500"></i>
                <div>Memuat histori tracking...</div>
            </div>

            <div x-show="!loadingTracking" class="space-y-3">
                <template x-for="log in trackingLogs" :key="log.id">
                    <div class="p-3.5 bg-slate-900/80 border border-slate-800 rounded-xl space-y-1.5 text-xs">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                            <span class="px-2 py-0.5 rounded font-bold text-[10px]" 
                                  :class="log.status === 'In' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : 'bg-rose-950 text-rose-300 border border-rose-500/40'" 
                                  x-text="'MUTASI ' + log.status.toUpperCase()"></span>
                            <span class="text-slate-400 font-mono text-[11px]" x-text="log.date"></span>
                        </div>
                        <div class="flex items-start justify-between pt-1">
                            <div>
                                <div class="font-bold text-white text-sm" x-text="log.notes"></div>
                                <div class="text-[11px] text-slate-400">PIC Operator: <strong class="text-slate-200" x-text="log.pic"></strong></div>
                            </div>
                            <div class="text-right font-extrabold text-emerald-400 font-mono text-sm">
                                <div x-text="log.qty_input + ' ' + log.uom_input"></div>
                                <div class="text-[10px] text-sky-400 font-normal" x-show="log.uom_input !== log.uom_base" x-text="'= ' + log.qty_base + ' ' + log.uom_base"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="trackingLogs.length === 0" class="p-6 text-center text-slate-500 text-xs">
                    Belum ada riwayat transaksi log untuk palet ini.
                </div>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-700/60">
                <button type="button" @click="showTrackingModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: PRINT LABEL BARCODE & QR CODE PALET                             -->
    <!-- ========================================================================= -->
    <div x-show="showPrintModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-md p-6 rounded-2xl shadow-2xl space-y-4 border border-sky-500/40">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-sky-400">
                    <i class="fa-solid fa-barcode text-xl"></i>
                    <h3 class="text-base font-bold text-white">Label Barcode Palet Gudang</h3>
                </div>
                <button type="button" @click="showPrintModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Print Physical Card Area -->
            <div id="printablePalletTag" class="bg-white text-slate-900 p-5 rounded-xl border-2 border-slate-900 space-y-3 shadow-lg">
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-2">
                    <div>
                        <div class="font-black text-lg tracking-wider leading-none">WMS INDRACO</div>
                        <div class="text-[9px] font-bold text-slate-600 uppercase">Pallet Identification Tag</div>
                    </div>
                    <div class="px-2 py-0.5 bg-slate-900 text-white font-mono font-bold text-xs rounded">
                        <span x-text="printData.bin"></span>
                    </div>
                </div>

                <div class="text-center py-2 space-y-1">
                    <div class="font-mono font-black text-2xl tracking-widest text-indigo-950" x-text="printData.id"></div>
                    <!-- Visual Simulated CODE128 Barcode -->
                    <div class="flex items-center justify-center space-x-0.5 h-12 py-1">
                        <template x-for="i in 35" :key="i">
                            <div class="h-full bg-slate-900" :style="'width: ' + ((i % 3 === 0) ? '3px' : (i % 2 === 0 ? '1px' : '2px')) + '; margin-right: 1px;'"></div>
                        </template>
                    </div>
                    <div class="font-mono text-[10px] text-slate-600 tracking-widest uppercase">* <span x-text="printData.id"></span> *</div>
                </div>

                <div class="pt-2 border-t border-slate-300 flex items-center justify-between text-[11px] font-semibold text-slate-700">
                    <div>Jenis: <strong class="text-slate-900" x-text="printData.type"></strong></div>
                    <div>Printed: <span class="font-mono text-slate-600">{{ date('d/m/Y H:i') }}</span></div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <button type="button" @click="showPrintModal = false" class="px-4 py-2 bg-slate-800 text-slate-300 text-xs font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="button" @click="window.print()" class="px-5 py-2 bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold rounded-xl shadow flex items-center space-x-1.5 cursor-pointer">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Label Physical</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 6: PENDAFTARAN BOX & PRODUK KE PALET                                -->
    <!-- ========================================================================= -->
    <div x-show="showAddBoxModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-lg p-6 sm:p-8 rounded-2xl shadow-2xl space-y-5 border border-emerald-500/40 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-3 text-emerald-400">
                    <i class="fa-solid fa-square-plus text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Box & Produk ke Palet: <span class="font-mono text-emerald-400" x-text="addBoxForm.pallet_id"></span></h3>
                        <p class="text-xs text-slate-400">Pilih produk SKU, tentukan UOM, dan daftarkan box ke palet target ini</p>
                    </div>
                </div>
                <button type="button" @click="showAddBoxModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('transaction.inbound.box_to_pallet') }}" method="POST" onsubmit="PalletApp.submitAddBoxForm(event, this)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="pallet_id" x-model="addBoxForm.pallet_id">

                <!-- Target Pallet Info Badge -->
                <div class="p-3 bg-emerald-950/40 border border-emerald-500/30 rounded-xl flex items-center justify-between">
                    <span class="text-slate-300 text-xs">Palet Target Terpilih:</span>
                    <span class="font-mono font-bold text-emerald-400 text-sm" x-text="addBoxForm.pallet_id"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Box Barcode ID -->
                    <div class="sm:col-span-2">
                        <label class="block text-slate-300 font-semibold mb-1">Barcode Box ID (Opsional Auto-Generate)</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" 
                                   name="box_id" 
                                   x-model="addBoxForm.box_id" 
                                   placeholder="BOX-xxxx" 
                                   class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <button type="button" 
                                    @click="openCameraScanner(val => addBoxForm.box_id = val.toUpperCase())" 
                                    class="px-3.5 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl shadow flex items-center space-x-1 text-xs font-semibold cursor-pointer" 
                                    title="Scan Kamera">
                                <i class="fa-solid fa-camera text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- SKU Produk Select Search -->
                    <div class="sm:col-span-2">
                        <label class="block text-slate-300 font-semibold mb-1">Cari & Select Produk SKU <span class="text-rose-400">*</span></label>
                        <select id="select_add_box_product" name="product_id" x-model="addBoxForm.product_id" @change="onAddBoxProductChange()" required class="w-full">
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (SKU: {{ $p->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Qty Transaksi -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Jumlah Qty Transaksi <span class="text-rose-400">*</span></label>
                        <input type="number" step="any" name="qty" x-model.number="addBoxForm.qty" required min="0.01" class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white text-base font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <!-- Satuan Transaksi (Select Search) -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Cari & Select Satuan (UOM) <span class="text-rose-400">*</span></label>
                        <select id="select_add_box_unit" name="unit" x-model="addBoxForm.unit" required class="w-full">
                        </select>
                    </div>

                    <!-- Live Conversion Badge -->
                    <div class="sm:col-span-2 p-3 bg-sky-500/10 border border-sky-500/30 rounded-xl flex items-center justify-between">
                        <span class="text-sky-300 text-xs font-medium">Setara Satuan Terkecil (Base Unit):</span>
                        <span class="text-base font-extrabold text-sky-400 font-mono">
                            <span x-text="calculatedAddBoxBaseQty"></span> <span x-text="addBoxBaseUnit"></span>
                        </span>
                    </div>

                    <!-- Expired Date -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tanggal Expired <span class="text-rose-400">*</span></label>
                        <input type="date" name="expired_date" x-model="addBoxForm.expired_date" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <!-- Machine Code -->
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Kode Mesin Produksi <span class="text-rose-400">*</span></label>
                        <input type="text" name="machine_code" x-model="addBoxForm.machine_code" required class="w-full p-3 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <!-- Shift (Select Search) -->
                    <div class="sm:col-span-2">
                        <label class="block text-slate-300 font-semibold mb-1">Shift Kerja Operator</label>
                        <select id="select_add_box_shift" name="shift" x-model="addBoxForm.shift" required class="w-full">
                            <option value="1">Shift 1 (Pagi)</option>
                            <option value="2">Shift 2 (Siang)</option>
                            <option value="3">Shift 3 (Malam)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showAddBoxModal = false" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span class="btn-text">Daftarkan Box Ke Palet</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
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

    let tsAddBoxInstances = {};

    function initAddBoxTomSelect(id, onChangeCallback) {
        const el = document.getElementById(id);
        if (!el) return null;

        if (tsAddBoxInstances[id]) {
            tsAddBoxInstances[id].destroy();
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

        tsAddBoxInstances[id] = ts;
        return ts;
    }

    // Centralized Pallet Management SPA Application Engine
    const PalletApp = {
        showProgressBar() {
            const bar = document.getElementById('top-progress-bar');
            if (bar) {
                bar.style.opacity = '1';
                bar.style.width = '70%';
            }
        },

        hideProgressBar() {
            const bar = document.getElementById('top-progress-bar');
            if (bar) {
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.opacity = '0';
                    bar.style.width = '0';
                }, 300);
            }
        },

        showToast(type, message) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-3.5 rounded-xl text-xs font-semibold shadow-2xl flex items-center space-x-2 transition-all transform translate-y-2 opacity-0 text-white ${
                type === 'success' ? 'bg-emerald-600/90 border border-emerald-400/30' : 'bg-rose-600/90 border border-rose-400/30'
            }`;
            
            toast.innerHTML = `
                <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'} text-sm"></i>
                <span>${message}</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            }, 50);

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        },

        async loadData(url) {
            this.showProgressBar();

            const container = document.getElementById('pallet-data-container');
            if (container) {
                container.style.opacity = '0.5';
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (data.status === 'success' && data.html) {
                    if (container) {
                        container.innerHTML = data.html;
                        container.style.opacity = '1';
                    }
                    window.history.pushState({}, '', url);
                    this.bindPaginationLinks();
                }
            } catch (err) {
                console.error('Error loading pallet data:', err);
                this.showToast('error', 'Gagal memuat data palet.');
                if (container) container.style.opacity = '1';
            } finally {
                this.hideProgressBar();
            }
        },

        async submitFilter(event, formEl) {
            event.preventDefault();
            const formData = new FormData(formEl);
            const params = new URLSearchParams(formData).toString();
            const actionUrl = `${formEl.action}?${params}`;

            await this.loadData(actionUrl);
        },

        async submitCreateForm(event, formEl) {
            event.preventDefault();
            this.showProgressBar();

            const submitBtn = formEl.querySelector('button[type="submit"]');
            const btnTextEl = submitBtn ? submitBtn.querySelector('.btn-text') : null;
            const originalText = btnTextEl ? btnTextEl.textContent : (submitBtn ? submitBtn.textContent : '');

            if (submitBtn) {
                submitBtn.disabled = true;
                if (btnTextEl) btnTextEl.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...`;
            }

            try {
                const formData = new FormData(formEl);
                const response = await fetch(formEl.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    this.showToast('success', data.message);
                    formEl.reset();

                    if (window.Alpine) {
                        const app = document.querySelector('[x-data]');
                        if (app && app._x_dataStack && app._x_dataStack[0]) {
                            app._x_dataStack[0].showRegisterModal = false;
                        }
                    }

                    await this.loadData(window.location.href);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal registrasi palet.');
                    this.showToast('error', msg);
                }
            } catch (err) {
                console.error('Create error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat mendaftarkan palet.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (btnTextEl) btnTextEl.textContent = originalText;
                }
                this.hideProgressBar();
            }
        },

        async submitEditForm(event, formEl) {
            event.preventDefault();
            this.showProgressBar();

            const submitBtn = formEl.querySelector('button[type="submit"]');
            const btnTextEl = submitBtn ? submitBtn.querySelector('.btn-text') : null;
            const originalText = btnTextEl ? btnTextEl.textContent : (submitBtn ? submitBtn.textContent : '');

            if (submitBtn) {
                submitBtn.disabled = true;
                if (btnTextEl) btnTextEl.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Updating...`;
            }

            try {
                const formData = new FormData(formEl);
                const response = await fetch(formEl.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    this.showToast('success', data.message);

                    if (window.Alpine) {
                        const app = document.querySelector('[x-data]');
                        if (app && app._x_dataStack && app._x_dataStack[0]) {
                            app._x_dataStack[0].showEditModal = false;
                        }
                    }

                    await this.loadData(window.location.href);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal memperbarui palet.');
                    this.showToast('error', msg);
                }
            } catch (err) {
                console.error('Edit error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat memperbarui palet.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (btnTextEl) btnTextEl.textContent = originalText;
                }
                this.hideProgressBar();
            }
        },

        async submitAddBoxForm(event, formEl) {
            event.preventDefault();
            this.showProgressBar();

            const submitBtn = formEl.querySelector('button[type="submit"]');
            const btnTextEl = submitBtn ? submitBtn.querySelector('.btn-text') : null;
            const originalText = btnTextEl ? btnTextEl.textContent : (submitBtn ? submitBtn.textContent : '');

            if (submitBtn) {
                submitBtn.disabled = true;
                if (btnTextEl) btnTextEl.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...`;
            }

            try {
                const formData = new FormData(formEl);
                const response = await fetch(formEl.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && (data.status === 'success' || data.success)) {
                    this.showToast('success', data.message || 'Box & produk berhasil ditambahkan ke palet!');
                    formEl.reset();

                    if (window.Alpine) {
                        const app = document.querySelector('[x-data]');
                        if (app && app._x_dataStack && app._x_dataStack[0]) {
                            app._x_dataStack[0].showAddBoxModal = false;
                        }
                    }

                    await this.loadData(window.location.href);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal memproses penambahan box.');
                    this.showToast('error', msg);
                }
            } catch (err) {
                console.error('Add box error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat menambahkan box ke palet.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    if (btnTextEl) btnTextEl.textContent = originalText;
                }
                this.hideProgressBar();
            }
        },

        async deleteItem(event, formEl) {
            event.preventDefault();

            if (!confirm('Apakah Anda yakin ingin menghapus palet kosong ini?')) {
                return;
            }

            this.showProgressBar();
            try {
                const formData = new FormData(formEl);
                const response = await fetch(formEl.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    this.showToast('success', data.message);
                    await this.loadData(window.location.href);
                } else {
                    this.showToast('error', data.message || 'Gagal menghapus palet.');
                }
            } catch (err) {
                console.error('Delete error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat menghapus palet.');
            } finally {
                this.hideProgressBar();
            }
        },

        bindPaginationLinks() {
            const container = document.getElementById('pallet-data-container');
            if (!container) return;

            const links = container.querySelectorAll('.pagination a');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    PalletApp.loadData(link.href);
                });
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        PalletApp.bindPaginationLinks();

        // Handle Browser Back / Forward History navigation
        window.addEventListener('popstate', function() {
            PalletApp.loadData(window.location.href);
        });
    });

    function palletManagement() {
        return {
            showInspectModal: false,
            loadingInspect: false,
            inspectData: { pallet: {}, stock_totals: [], boxes: [] },

            showRegisterModal: false,
            registerForm: { id: 'PLT-' + Math.floor(1000 + Math.random() * 9000), pallet_type_id: '{{ $palletTypes->first()->id ?? "loscam" }}', bin_id: '', is_active: 1 },

            showEditModal: false,
            editForm: { id: '', pallet_type_id: '', bin_id: '', is_active: 1 },

            showTrackingModal: false,
            loadingTracking: false,
            trackingPalletId: '',
            trackingLogs: [],

            showPrintModal: false,
            printData: { id: '', bin: '', type: '' },

            showAddBoxModal: false,
            addBoxForm: {
                pallet_id: '',
                box_id: '',
                product_id: '{{ $products->first()->id ?? "" }}',
                qty: 1,
                unit: 'Box',
                expired_date: '{{ date("Y-m-d", strtotime("+1 year")) }}',
                machine_code: 'MC-01',
                shift: '{{ auth()->user()->shift ?? 1 }}'
            },
            addBoxConversions: {},
            addBoxBaseUnit: 'Box',

            get calculatedAddBoxBaseQty() {
                const conv = this.addBoxConversions[this.addBoxForm.unit];
                const factor = conv ? parseFloat(conv.qty_convert) : 1;
                return (parseFloat(this.addBoxForm.qty) || 0) * factor;
            },

            inspectPallet(id) {
                this.showInspectModal = true;
                this.loadingInspect = true;
                fetch('{{ url("/api/pallets") }}/' + id)
                    .then(res => res.json())
                    .then(data => {
                        this.inspectData = data;
                        this.loadingInspect = false;
                    })
                    .catch(err => {
                        alert('Gagal mengambil detail isi palet: ' + err);
                        this.loadingInspect = false;
                    });
            },

            trackPallet(id) {
                this.trackingPalletId = id;
                this.showTrackingModal = true;
                this.loadingTracking = true;
                fetch('{{ url("/api/pallets") }}/' + id)
                    .then(res => res.json())
                    .then(data => {
                        this.trackingLogs = data.tracking_logs || [];
                        this.loadingTracking = false;
                    })
                    .catch(err => {
                        alert('Gagal mengambil history tracking palet: ' + err);
                        this.loadingTracking = false;
                    });
            },

            openRegisterModal() {
                this.registerForm.id = 'PLT-' + Math.floor(1000 + Math.random() * 9000);
                this.showRegisterModal = true;
            },

            openAddBoxModal(palletId) {
                this.addBoxForm.pallet_id = palletId;
                this.addBoxForm.box_id = 'BOX-' + Math.floor(100000 + Math.random() * 900000);
                const firstProdId = '{{ $products->first()->id ?? "" }}';
                this.addBoxForm.product_id = firstProdId;
                this.showAddBoxModal = true;
                this.onAddBoxProductChange();

                this.$nextTick(() => {
                    initAddBoxTomSelect('select_add_box_product', val => {
                        this.addBoxForm.product_id = val;
                        this.onAddBoxProductChange();
                    });
                    initAddBoxTomSelect('select_add_box_shift', val => this.addBoxForm.shift = val);
                });
            },

            onAddBoxProductChange() {
                const prod = productsMap[this.addBoxForm.product_id];
                if (prod) {
                    this.addBoxConversions = prod.conversions;
                    this.addBoxBaseUnit = prod.uom_inventory;
                    const uomKeys = Object.keys(this.addBoxConversions);
                    if (!this.addBoxConversions[this.addBoxForm.unit]) {
                        this.addBoxForm.unit = uomKeys[0] || prod.uom_inventory;
                    }

                    // Re-sync unit select options in TomSelect
                    this.$nextTick(() => {
                        const unitEl = document.getElementById('select_add_box_unit');
                        if (unitEl) {
                            if (tsAddBoxInstances['select_add_box_unit']) {
                                tsAddBoxInstances['select_add_box_unit'].destroy();
                                delete tsAddBoxInstances['select_add_box_unit'];
                            }
                            unitEl.innerHTML = '';
                            for (let uomName in this.addBoxConversions) {
                                const c = this.addBoxConversions[uomName];
                                const opt = document.createElement('option');
                                opt.value = uomName;
                                opt.textContent = uomName + ' (x' + c.qty_convert + ' ' + c.uom_base + ')';
                                if (uomName === this.addBoxForm.unit) opt.selected = true;
                                unitEl.appendChild(opt);
                            }
                            unitEl.value = this.addBoxForm.unit;
                            initAddBoxTomSelect('select_add_box_unit', val => {
                                this.addBoxForm.unit = val;
                            });
                        }
                    });
                }
            },

            editPallet(id, typeId, binId, isActive) {
                this.editForm = { id: id, pallet_type_id: typeId, bin_id: binId || '', is_active: isActive };
                this.showEditModal = true;
            },

            printLabel(id, bin, type) {
                this.printData = { id: id, bin: bin, type: type };
                this.showPrintModal = true;
            }
        }
    }
</script>
@endsection
@endsection
