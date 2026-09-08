@extends('layouts.app')

@section('title', 'Master Data')

@section('styles')
<!-- TomSelect Searchable Dropdown CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #ffffff !important;
        border-radius: 0.75rem !important;
        padding: 0.6rem 0.8rem !important;
        font-size: 0.75rem !important;
    }
    .ts-dropdown {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #ffffff !important;
        border-radius: 0.75rem !important;
        font-size: 0.75rem !important;
    }
    .ts-dropdown .option {
        color: #e2e8f0 !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
    html.light .ts-control {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light .ts-dropdown {
        background-color: #ffffff !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light .ts-dropdown .option {
        color: #0f172a !important;
    }
    html.light .ts-dropdown .option.active, html.light .ts-dropdown .option:hover {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<!-- Top Progress Bar (Loading State Indicator) -->
<div id="top-progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-emerald-400 via-indigo-500 to-purple-500 z-[100] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<!-- Toast Container for Floating Alerts -->
<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none"></div>

<div class="space-y-6" x-data="masterDataForm('{{ $tab }}')">
    <!-- Header Title -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2">
                <i class="fa-solid fa-sitemap text-indigo-400"></i>
                <span>Manajemen Master Data System</span>
            </h2>
            <p class="text-sm text-slate-400">Struktur Hirarki Lokasi (Kantor &rarr; Plant &rarr; Gudang &rarr; Rak &rarr; BIN) dan Katalog Produk SKU</p>
        </div>
    </div>

    <!-- Optimized Tab Navigation Bar (SPA Zero Page Reload) -->
    <div class="flex space-x-2 border-b border-slate-700/80 pb-2 overflow-x-auto">
        <a href="{{ route('master.index', ['tab' => 'offices']) }}" onclick="MasterDataApp.loadTab(this.href, 'offices'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'offices' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="offices">
            <i class="fa-solid fa-building text-sm"></i>
            <span>1. Kantor Pusat</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'branches']) }}" onclick="MasterDataApp.loadTab(this.href, 'branches'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'branches' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="branches">
            <i class="fa-solid fa-industry text-sm"></i>
            <span>2. Plant</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'warehouses']) }}" onclick="MasterDataApp.loadTab(this.href, 'warehouses'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'warehouses' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="warehouses">
            <i class="fa-solid fa-warehouse text-sm"></i>
            <span>3. Gudang</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'racks']) }}" onclick="MasterDataApp.loadTab(this.href, 'racks'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'racks' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="racks">
            <i class="fa-solid fa-bars-staggered text-sm"></i>
            <span>4. Rak</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'bins']) }}" onclick="MasterDataApp.loadTab(this.href, 'bins'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'bins' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="bins">
            <i class="fa-solid fa-qrcode text-sm"></i>
            <span>5. BIN</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'products']) }}" onclick="MasterDataApp.loadTab(this.href, 'products'); return false;" class="tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap {{ $tab === 'products' ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 text-slate-400 hover:text-slate-200' }}" data-tab="products">
            <i class="fa-solid fa-boxes-stacked text-sm"></i>
            <span>6. Produk SKU</span>
        </a>
    </div>

    <!-- DYNAMICALLY LOADED ASYNC TAB CONTAINER -->
    <div id="tab-content-container" class="transition-all duration-200 min-h-[400px]">
        @include('master.tabs.' . $tab)
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: TAMBAH PRODUK SKU BARU                                      -->
    <!-- ========================================================================= -->
    <div x-show="showAddProductModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-xl p-6 rounded-2xl shadow-2xl space-y-5 border border-indigo-500/40 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-indigo-400">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                    <h3 class="text-base font-bold text-white">Tambah Produk SKU Baru</h3>
                </div>
                <button type="button" @click="showAddProductModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('master.product.store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Kode Item SKU <span class="text-rose-400">*</span></label>
                        <div class="flex items-center space-x-2">
                            <input type="text" name="id" x-model="forms.product_id" required placeholder="misal: FGD00999" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono uppercase focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <button type="button" @click="autoProductCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer">Auto</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Item Produk <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" required placeholder="Nama Produk" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Group Item</label>
                        <input type="text" name="item_group" placeholder="FINISHED GOODS (MANUFAKTUR)" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">UOM Inventory (Base Unit) <span class="text-rose-400">*</span></label>
                        <input type="text" name="uom_inventory" required placeholder="Sachet / Box / PCS" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Brand</label>
                        <input type="text" name="brand" placeholder="Intirasa / BaliCafé" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Keaktifan</label>
                        <select id="select_is_active" name="is_active" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            <option value="1">Aktif (Yes)</option>
                            <option value="0">Non-Aktif (No)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showAddProductModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                        <span class="btn-text">Simpan Produk SKU</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<!-- TomSelect JS Script -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    let tsMasterInstances = {};

    function initMasterTomSelect(id) {
        const el = document.getElementById(id);
        if (!el) return;
        if (tsMasterInstances[id]) {
            tsMasterInstances[id].destroy();
        }
        tsMasterInstances[id] = new TomSelect(el, {
            create: false,
            maxOptions: 500,
            allowEmptyOption: true,
            onChange: function(val) {
                el.value = val;
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }

    function initAllMasterTomSelects() {
        const masterSelectIds = [
            'select_office_filter', 'select_office_id', 
            'select_branch_filter', 'select_branch_id', 
            'select_wh_filter', 'select_warehouse_id', 
            'select_rack_filter', 'select_rack_id', 
            'select_brand_filter', 'select_group_filter', 'select_is_active'
        ];
        masterSelectIds.forEach(id => {
            initMasterTomSelect(id);
        });
    }

    // Centralized Master Data SPA Application Engine
    const MasterDataApp = {
        currentTab: '{{ $tab }}',

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

        async loadTab(url, tabName) {
            this.showProgressBar();
            this.currentTab = tabName;

            const container = document.getElementById('tab-content-container');
            if (container) {
                container.style.opacity = '0.5';
            }

            // Update Tab Button UI States
            document.querySelectorAll('.tab-btn').forEach(btn => {
                if (btn.getAttribute('data-tab') === tabName) {
                    btn.className = 'tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30';
                } else {
                    btn.className = 'tab-btn px-4 py-2.5 text-xs font-semibold rounded-xl transition flex items-center space-x-1.5 cursor-pointer whitespace-nowrap bg-slate-800/80 text-slate-400 hover:text-slate-200';
                }
            });

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
                    initAllMasterTomSelects();
                    this.bindPaginationLinks();
                }
            } catch (err) {
                console.error('Error loading tab:', err);
                this.showToast('error', 'Gagal memuat data tab.');
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
            const tabName = formData.get('tab') || this.currentTab;

            await this.loadTab(actionUrl, tabName);
        },

        async submitCreateForm(event, formEl) {
            event.preventDefault();
            this.showProgressBar();

            const submitBtn = formEl.querySelector('button[type="submit"]');
            const btnTextEl = submitBtn ? submitBtn.querySelector('.btn-text') : null;
            const originalText = btnTextEl ? btnTextEl.textContent : (submitBtn ? submitBtn.textContent : '');

            if (submitBtn) {
                submitBtn.disabled = true;
                if (btnTextEl) {
                    btnTextEl.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...`;
                }
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
                    
                    // Close product modal if AlpineJS context exists
                    if (window.Alpine) {
                        const app = document.querySelector('[x-data]');
                        if (app && app._x_dataStack && app._x_dataStack[0]) {
                            app._x_dataStack[0].showAddProductModal = false;
                        }
                    }

                    // Reload active tab data seamlessly
                    const targetTab = data.tab || this.currentTab;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', targetTab);
                    await this.loadTab(currentUrl.toString(), targetTab);
                } else {
                    const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Gagal menyimpan data.');
                    this.showToast('error', msg);
                }
            } catch (err) {
                console.error('Submit error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat menyimpan data.');
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

            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
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
                    const targetTab = data.tab || this.currentTab;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('tab', targetTab);
                    await this.loadTab(currentUrl.toString(), targetTab);
                } else {
                    this.showToast('error', data.message || 'Gagal menghapus data.');
                }
            } catch (err) {
                console.error('Delete error:', err);
                this.showToast('error', 'Terjadi kesalahan sistem saat menghapus data.');
            } finally {
                this.hideProgressBar();
            }
        },

        bindPaginationLinks() {
            const container = document.getElementById('tab-content-container');
            if (!container) return;

            const links = container.querySelectorAll('.pagination a');
            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    MasterDataApp.loadTab(link.href, MasterDataApp.currentTab);
                });
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        initAllMasterTomSelects();
        MasterDataApp.bindPaginationLinks();

        // Handle Browser Back / Forward History navigation
        window.addEventListener('popstate', function() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab') || 'offices';
            MasterDataApp.loadTab(window.location.href, tab);
        });
    });

    function masterDataForm(initialTab) {
        return {
            activeTab: initialTab || 'offices',
            showAddProductModal: false,
            forms: {
                office_id: '',
                branch_id: '',
                wh_id: '',
                rack_id: '',
                bin_id: '',
                product_id: ''
            },

            autoOfficeCode() {
                this.forms.office_id = 'KTR-' + Math.floor(100 + Math.random() * 900);
            },

            autoBranchCode() {
                this.forms.branch_id = 'PLANT-' + Math.floor(100 + Math.random() * 900);
            },

            autoWhCode() {
                this.forms.wh_id = 'WHS-' + Math.floor(100 + Math.random() * 900);
            },

            autoRackCode() {
                const char = String.fromCharCode(65 + Math.floor(Math.random() * 26));
                this.forms.rack_id = 'RCK-' + char + '.' + Math.floor(1 + Math.random() * 99);
            },

            autoBinCode() {
                this.forms.bin_id = 'BIN-' + Math.floor(1000 + Math.random() * 9000);
            },

            autoProductCode() {
                this.forms.product_id = 'FGD' + Math.floor(10000 + Math.random() * 90000);
            }
        }
    }
</script>
@endsection
@endsection
