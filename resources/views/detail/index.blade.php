@extends('layouts.app')

@section('title', 'Stock Locator')

@section('content')
<!-- Top Progress Bar (Loading State Indicator) -->
<div id="top-progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-indigo-500 via-emerald-400 to-purple-500 z-[100] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<!-- Toast Container for Floating Alerts -->
<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none"></div>

<div class="space-y-4" 
     id="detail_data_wrapper"
     x-data="{
        filterModalOpen: false,
        searchVal: '{{ addslashes($search ?? '') }}',
        productIdVal: '{{ addslashes($filterProduct ?? '') }}',
        binIdVal: '{{ addslashes($filterBin ?? '') }}',
        get activeFilterCount() {
            let count = 0;
            if (this.searchVal && this.searchVal.trim() !== '') count++;
            if (this.productIdVal && this.productIdVal.trim() !== '') count++;
            if (this.binIdVal && this.binIdVal.trim() !== '') count++;
            return count;
        },
        getProductName(id) {
            return productsLookup[id] || id;
        }
     }">

    <!-- Page Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold font-display text-white">Stock Locator & Inventory Detail</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-0.5">Pelacakan posisi fisik stok per BIN, Palet, Produk, & Masa Expired</p>
        </div>
    </div>

    <!-- Compact Sleek Search & Filter Control Bar -->
    <div class="glass-panel p-3 sm:p-4 rounded-2xl flex items-center gap-2">
        <!-- Quick Search Bar -->
        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            <input type="text" 
                   id="stock_locator_quick_search" 
                   x-model="searchVal"
                   @keyup.enter="DetailDataApp.applyQuickSearch()" 
                   placeholder="Cari Barcode, Palet, BIN, SKU Produk..." 
                   class="w-full pl-9 pr-8 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-white placeholder-slate-400 text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            <button type="button" 
                    x-show="searchVal && searchVal.length > 0" 
                    @click="searchVal = ''; DetailDataApp.applyQuickSearch()" 
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 cursor-pointer">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Quick Scan Camera Button -->
        <button type="button" 
                onclick="openCameraScanner(val => { const el = document.getElementById('stock_locator_quick_search'); el.value = val; el.dispatchEvent(new Event('input')); DetailDataApp.applyQuickSearch(); })" 
                class="py-2.5 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-md shadow-indigo-600/30 flex items-center justify-center cursor-pointer transition active:scale-95 text-xs font-semibold"
                title="Scan Barcode Kamera">
            <i class="fa-solid fa-camera text-sm"></i>
        </button>

        <!-- Filter Modal Trigger Button -->
        <button type="button" 
                @click="filterModalOpen = true" 
                class="py-2.5 px-3.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl flex items-center gap-2 text-xs font-bold transition active:scale-95 cursor-pointer relative"
                :class="activeFilterCount > 0 ? 'border-indigo-500/70 bg-indigo-950/40 text-indigo-300' : ''">
            <i class="fa-solid fa-sliders text-indigo-400"></i>
            <span class="hidden sm:inline">Filter</span>
            <!-- Active Counter Badge -->
            <span x-show="activeFilterCount > 0" 
                  x-text="activeFilterCount" 
                  class="w-4 h-4 rounded-full bg-indigo-600 text-white text-[10px] font-black flex items-center justify-center shadow -mr-1"></span>
        </button>
    </div>

    <!-- Active Filter Chips / Pills (Tampil hanya jika ada filter aktif) -->
    <div x-show="activeFilterCount > 0" class="flex flex-wrap items-center gap-1.5 text-xs" x-cloak>
        <span class="text-[11px] text-slate-400 font-semibold mr-1">Filter Aktif:</span>

        <!-- Search Chip -->
        <template x-if="searchVal && searchVal.trim() !== ''">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-[11px] font-medium">
                <span>Keyword: "<strong x-text="searchVal"></strong>"</span>
                <button type="button" @click="searchVal = ''; DetailDataApp.applyQuickSearch()" class="hover:text-white cursor-pointer ml-0.5">✕</button>
            </span>
        </template>

        <!-- Product Chip -->
        <template x-if="productIdVal && productIdVal.trim() !== ''">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[11px] font-medium">
                <span>Produk: <strong x-text="getProductName(productIdVal)"></strong></span>
                <button type="button" @click="productIdVal = ''; DetailDataApp.applyQuickSearch()" class="hover:text-white cursor-pointer ml-0.5">✕</button>
            </span>
        </template>

        <!-- BIN Chip -->
        <template x-if="binIdVal && binIdVal.trim() !== ''">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-medium">
                <span>BIN: <strong x-text="binIdVal"></strong></span>
                <button type="button" @click="binIdVal = ''; DetailDataApp.applyQuickSearch()" class="hover:text-white cursor-pointer ml-0.5">✕</button>
            </span>
        </template>

        <!-- Clear All Button -->
        <button type="button" 
                @click="DetailDataApp.resetAllFilters()" 
                class="text-[11px] text-rose-400 hover:underline font-semibold ml-1 cursor-pointer">
            Reset Semua
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL FILTER & PENCARIAN STOK (MODERN BLUR BACKDROP)                     -->
    <!-- ========================================================================= -->
    <div x-show="filterModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 modal-backdrop-blur overflow-y-auto pb-20 sm:pb-4"
         style="background-color: rgba(15, 23, 42, 0.45) !important; backdrop-filter: blur(8px) !important; -webkit-backdrop-filter: blur(8px) !important;">
        
        <div @click.away="filterModalOpen = false"
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
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 dark:text-white">Filter & Pencarian Stok</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur parameter pencarian inventaris</p>
                    </div>
                </div>
                <button type="button" 
                        @click="filterModalOpen = false" 
                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form id="stockLocatorFilterForm" @submit.prevent="DetailDataApp.submitFilterModal()">
                <div class="space-y-4 text-xs">
                    <!-- Filter 1: Kata Kunci / Barcode -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cari Barcode / ID / Palet / Produk
                        </label>
                        <div class="flex items-center space-x-1.5">
                            <input type="text" 
                                   name="search" 
                                   id="modal_search_input"
                                   x-model="searchVal"
                                   placeholder="Ketik PLT-, BOX-, B.10.2, dll..."
                                   class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <button type="button" 
                                    onclick="openCameraScanner(val => { const el = document.getElementById('modal_search_input'); el.value = val; el.dispatchEvent(new Event('input')); })" 
                                    class="p-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center justify-center cursor-pointer transition active:scale-95" 
                                    title="Scan Barcode Kamera">
                                <i class="fa-solid fa-camera text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter 2: Dropdown Produk -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Filter Produk SKU
                        </label>
                        <select name="product_id" 
                                x-model="productIdVal"
                                class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer">
                            <option value="">-- Semua Produk --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter 3: Dropdown Lokasi BIN -->
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Filter Lokasi BIN / Rak
                        </label>
                        <select name="bin_id" 
                                x-model="binIdVal"
                                class="w-full p-3 bg-slate-50 dark:bg-slate-950 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none cursor-pointer">
                            <option value="">-- Semua BIN Lokasi Rak --</option>
                            @foreach($bins as $b)
                                <option value="{{ $b->id }}">BIN: {{ $b->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Modal Action Buttons -->
                <div class="grid grid-cols-2 gap-3 pt-5 border-t border-slate-200 dark:border-slate-800 mt-5">
                    <button type="button" 
                            @click="DetailDataApp.resetAllFilters()" 
                            class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs sm:text-sm rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer active:scale-95 text-center">
                        Reset Filter
                    </button>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-xs sm:text-sm rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer active:scale-95 flex items-center justify-center gap-1.5 text-center">
                        <i class="fa-solid fa-filter"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DYNAMICALLY LOADED ASYNC TABLE & LIST CONTAINER -->
    <div id="detail-data-container" class="transition-all duration-200 min-h-[300px]">
        @include('detail.partials.table')
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Product ID to Name lookup map
    const productsLookup = {
        @foreach($products as $p)
            "{{ $p->id }}": "{{ addslashes($p->name) }}",
        @endforeach
    };

    // Centralized Detail Data SPA Application Engine
    const DetailDataApp = {
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

            const container = document.getElementById('detail-data-container');
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
                console.error('Error loading detail data:', err);
                this.showToast('error', 'Gagal memuat data stok barang.');
                if (container) container.style.opacity = '1';
            } finally {
                this.hideProgressBar();
            }
        },

        applyQuickSearch() {
            const wrapper = document.getElementById('detail_data_wrapper');
            const alpineData = wrapper ? Alpine.$data(wrapper) : null;
            const search = alpineData ? alpineData.searchVal : document.getElementById('stock_locator_quick_search')?.value;
            const prod = alpineData ? alpineData.productIdVal : '';
            const bin = alpineData ? alpineData.binIdVal : '';

            const url = new URL('{{ route("detail.index") }}', window.location.origin);
            if (search && search.trim() !== '') url.searchParams.set('search', search.trim());
            if (prod && prod.trim() !== '') url.searchParams.set('product_id', prod.trim());
            if (bin && bin.trim() !== '') url.searchParams.set('bin_id', bin.trim());

            this.loadData(url.toString());
        },

        submitFilterModal() {
            const wrapper = document.getElementById('detail_data_wrapper');
            if (wrapper) {
                Alpine.$data(wrapper).filterModalOpen = false;
            }
            this.applyQuickSearch();
        },

        resetAllFilters() {
            const wrapper = document.getElementById('detail_data_wrapper');
            if (wrapper) {
                const alpineData = Alpine.$data(wrapper);
                alpineData.searchVal = '';
                alpineData.productIdVal = '';
                alpineData.binIdVal = '';
                alpineData.filterModalOpen = false;
            }
            this.loadData('{{ route("detail.index") }}');
        },

        bindPaginationLinks() {
            const container = document.getElementById('detail-data-container');
            if (!container) return;

            const links = container.querySelectorAll('.pagination a');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    DetailDataApp.loadData(link.href);
                });
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        DetailDataApp.bindPaginationLinks();

        // Handle Browser Back / Forward History navigation
        window.addEventListener('popstate', function() {
            DetailDataApp.loadData(window.location.href);
        });
    });
</script>
@endsection
