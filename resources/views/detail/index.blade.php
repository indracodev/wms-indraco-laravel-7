@extends('layouts.app')

@section('title', 'Stock Locator')

@section('content')
<!-- Top Progress Bar (Loading State Indicator) -->
<div id="top-progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-indigo-500 via-emerald-400 to-purple-500 z-[100] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<!-- Toast Container for Floating Alerts -->
<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none"></div>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Stock Locator & Inventory Detail</h2>
            <p class="text-sm text-slate-400">Pencarian dan Pelacakan Posisi Fisik Stok per BIN, Palet, Box, & Tanggal Expired</p>
        </div>
    </div>

    <!-- Filter & Search Bar (SPA Zero Page Reload) -->
    <div class="glass-panel p-5 rounded-2xl">
        <form method="GET" action="{{ route('detail.index') }}" onsubmit="DetailDataApp.submitFilter(event, this)" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <label class="block text-slate-400 font-semibold mb-1">Cari Barcode / ID / Produk</label>
                <div class="flex items-center space-x-1.5">
                    <input type="text" name="search" id="stock_locator_search" value="{{ $search }}" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Ketik PLT-, BOX-, B.10.2, dll...">
                    <button type="button" onclick="openCameraScanner('stock_locator_search')" class="p-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow flex items-center justify-center cursor-pointer transition" title="Scan Barcode Kamera">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-slate-400 font-semibold mb-1">Filter Produk</label>
                <select name="product_id" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">-- Semua Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ $filterProduct == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->id }})</option>
                    @endforeach
                </select>
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
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 font-semibold text-white rounded-xl shadow transition cursor-pointer flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-filter mr-1"></i> <span class="btn-text">Filter Data</span>
                </button>
                <button type="button" onclick="DetailDataApp.loadData('{{ route('detail.index') }}')" class="py-2.5 px-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl border border-slate-700 transition cursor-pointer">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- DYNAMICALLY LOADED ASYNC TABLE & LIST CONTAINER -->
    <div id="detail-data-container" class="transition-all duration-200 min-h-[300px]">
        @include('detail.partials.table')
    </div>
</div>

@section('scripts')
<script>
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

        async submitFilter(event, formEl) {
            event.preventDefault();
            const formData = new FormData(formEl);
            const params = new URLSearchParams(formData).toString();
            const actionUrl = `${formEl.action}?${params}`;

            await this.loadData(actionUrl);
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
@endsection
