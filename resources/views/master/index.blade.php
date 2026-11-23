@extends('layouts.app')

@section('title', 'Master Data')

@section('styles')
<!-- TomSelect Searchable Dropdown CSS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    /* -------------------------------------------------------------
     * MASTER DATA DESIGN SYSTEM: Identik gifanya-adhiyaksa-jaya
     * ------------------------------------------------------------- */
    
    /* TomSelect Dropdown Customization */
    .ts-control {
        background-color: #001e38 !important;
        border: 1.5px solid #003661 !important;
        color: #ffffff !important;
        border-radius: 8px !important;
        padding: 0.6rem 0.85rem !important;
        font-size: 0.82rem !important;
    }
    .ts-dropdown {
        background-color: #00274d !important;
        border: 1px solid #003661 !important;
        color: #ffffff !important;
        border-radius: 10px !important;
        font-size: 0.82rem !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4) !important;
    }
    .ts-dropdown .option {
        color: #e2e8f0 !important;
        padding: 8px 12px !important;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background-color: #004b87 !important;
        color: #ffffff !important;
    }
    html.light .ts-control {
        background-color: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light .ts-control:focus, html.light .ts-control.focus {
        border-color: #004b87 !important;
        box-shadow: 0 0 0 3px rgba(0, 75, 135, 0.15) !important;
    }
    html.light .ts-dropdown {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        box-shadow: 0 10px 25px rgba(0, 43, 91, 0.1) !important;
    }
    html.light .ts-dropdown .option {
        color: #1e293b !important;
    }
    html.light .ts-dropdown .option.active, html.light .ts-dropdown .option:hover {
        background-color: #f0f7ff !important;
        color: #004b87 !important;
        font-weight: 600 !important;
    }

    /* Container Glass Panel -> Solid Clean White Card (gifanya-adhiyaksa-jaya style) */
    html.light #master-data-root .glass-panel {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        box-shadow: 0 1px 3px rgba(0, 43, 91, 0.05) !important;
        padding: 20px !important;
    }
    html.dark #master-data-root .glass-panel {
        background-color: #00274d !important;
        border: 1px solid #003661 !important;
        border-radius: 16px !important;
        box-shadow: 0 4px 20px rgba(0, 15, 35, 0.4) !important;
        padding: 20px !important;
    }

    /* Table Head Styling */
    html.light #master-data-root table thead,
    html.light #master-data-root .bg-slate-100 {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    html.light #master-data-root table thead th {
        color: #475569 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        font-weight: 700 !important;
        padding: 12px 14px !important;
    }
    html.dark #master-data-root table thead {
        background-color: #001e38 !important;
        border-bottom: 2px solid #003661 !important;
    }
    html.dark #master-data-root table thead th {
        color: #93c5fd !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.5px !important;
        text-transform: uppercase !important;
        font-weight: 700 !important;
        padding: 12px 14px !important;
    }

    /* Table Body Rows */
    html.light #master-data-root table tbody tr {
        border-bottom: 1px solid #f1f5f9 !important;
        transition: background-color 0.15s ease !important;
    }
    html.light #master-data-root table tbody tr:hover {
        background-color: #f8fafc !important;
    }
    html.light #master-data-root table tbody td {
        padding: 13px 14px !important;
        vertical-align: middle !important;
        font-size: 0.84rem !important;
    }
    html.dark #master-data-root table tbody tr {
        border-bottom: 1px solid #00274d !important;
    }
    html.dark #master-data-root table tbody tr:hover {
        background-color: rgba(0, 75, 135, 0.2) !important;
    }
    html.dark #master-data-root table tbody td {
        padding: 13px 14px !important;
        vertical-align: middle !important;
        font-size: 0.84rem !important;
    }

    /* Table Outer Container Box */
    html.light #master-data-root .overflow-x-auto.border {
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        background: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
    }
    html.dark #master-data-root .overflow-x-auto.border {
        border: 1px solid #003661 !important;
        border-radius: 14px !important;
        background: #00274d !important;
    }

    /* Tab Button Navigation */
    .tab-btn {
        transition: all 0.2s ease !important;
    }
    html.light .tab-btn:hover {
        transform: translateY(-1px);
    }

    /* Form Controls in Light Mode */
    html.light #master-data-root input[type="text"]:not(.search-input-field),
    html.light #master-data-root input[type="number"],
    html.light #master-data-root select,
    html.light #master-data-root textarea {
        background-color: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
        border-radius: 8px !important;
        font-size: 0.82rem !important;
    }
    html.light #master-data-root input:not(.search-input-field):focus,
    html.light #master-data-root select:focus,
    html.light #master-data-root textarea:focus {
        background-color: #ffffff !important;
        border-color: #004b87 !important;
        box-shadow: 0 0 0 3px rgba(0, 75, 135, 0.15) !important;
    }

    /* Global Search Component for Master Data (Identical to Detail Data & Pallets) */
    #master-data-root .search-input-group {
        display: flex !important;
        align-items: stretch !important;
        width: 100% !important;
        height: 36px !important;
        max-height: 36px !important;
        position: relative !important;
        box-sizing: border-box !important;
    }
    #master-data-root .search-btn-reset {
        background-color: #f39c12 !important;
        color: #ffffff !important;
        border: 1px solid #f39c12 !important;
        border-radius: 4px 0 0 4px !important;
        padding: 8px 12px !important;
        height: 36px !important;
        min-height: 36px !important;
        max-height: 36px !important;
        min-width: 37.16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: background-color 0.15s ease, border-color 0.15s ease !important;
        font-size: 13px !important;
        font-family: 'Poppins', sans-serif !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        flex-shrink: 0 !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        z-index: 1;
    }
    #master-data-root .search-btn-reset:hover {
        background-color: #e08e0b !important;
        border-color: #e08e0b !important;
        color: #ffffff !important;
    }
    #master-data-root .search-input-field,
    html.light #master-data-root .search-input-field,
    html.light #master-data-root input.search-input-field {
        flex: 1 1 auto !important;
        background-color: #ffffff !important;
        border: 1px solid #ced4da !important;
        border-radius: 0 !important;
        margin: 0 0 0 -1px !important;
        padding: 6px 12px !important;
        height: 36px !important;
        min-height: 36px !important;
        max-height: 36px !important;
        font-size: 13px !important;
        font-family: 'Poppins', sans-serif !important;
        line-height: normal !important;
        color: #1e293b !important;
        outline: none !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
        box-shadow: none !important;
        z-index: 2;
    }
    html.dark #master-data-root .search-input-field,
    html.dark #master-data-root input.search-input-field {
        background-color: #001e38 !important;
        border-color: #003661 !important;
        color: #ffffff !important;
    }
    #master-data-root .search-input-field:focus,
    html.light #master-data-root .search-input-field:focus,
    html.light #master-data-root input.search-input-field:focus {
        outline: none !important;
        border-color: #3850c9 !important;
        box-shadow: 0 0 0 1px #3850c9 !important;
        z-index: 3;
    }
    #master-data-root .search-btn-submit {
        background-color: #3850c9 !important;
        color: #ffffff !important;
        border: 1px solid #3850c9 !important;
        border-radius: 0 4px 4px 0 !important;
        margin: 0 0 0 -1px !important;
        padding: 8px 12px !important;
        height: 36px !important;
        min-height: 36px !important;
        max-height: 36px !important;
        width: auto !important;
        min-width: 37.16px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: background-color 0.15s ease, border-color 0.15s ease !important;
        font-size: 13px !important;
        flex-shrink: 0 !important;
        box-sizing: border-box !important;
        z-index: 1;
    }
    #master-data-root .search-btn-submit:hover {
        background-color: #2b3ea6 !important;
        border-color: #2b3ea6 !important;
        color: #ffffff !important;
    }

    /* Card Form Input on the right side */
    html.light #master-data-root .lg\:block.bg-slate-100 {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 14px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        padding: 20px !important;
    }
    html.dark #master-data-root .lg\:block.bg-slate-100 {
        background-color: #00274d !important;
        border: 1px solid #003661 !important;
        border-radius: 14px !important;
        padding: 20px !important;
    }

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    /* Enforce Minimal 13px Font Across Master Data without breaking icon fonts */
    #master-data-root input,
    #master-data-root select,
    #master-data-root textarea,
    #master-data-root button:not([class*="fa"]),
    #master-data-root .tab-btn:not([class*="fa"]),
    #master-data-root .desktop-tab-btn:not([class*="fa"]),
    #master-data-root table th,
    #master-data-root table td,
    #master-data-root label,
    #master-data-root p,
    #master-data-root span:not([class*="fa"]):not([class*="icon"]),
    #master-data-root div:not([class*="fa"]):not([class*="icon"]) {
        font-family: 'Poppins', sans-serif;
    }

    /* CRITICAL: Force Font Awesome Icons inside Master Data to preserve glyphs with exact weights */
    #master-data-root .fa-solid,
    #master-data-root .fas,
    #master-data-root i.fa-solid,
    #master-data-root i.fas,
    #master-data-root [class*="fa-solid"],
    #master-data-root [class*="fas"],
    #master-data-root button i.fa-solid,
    #master-data-root a i.fa-solid,
    #master-data-root h2 i.fa-solid,
    #master-data-root h3 i.fa-solid {
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
        font-style: normal !important;
        font-variant: normal !important;
        text-rendering: auto !important;
        line-height: 1 !important;
        display: inline-block !important;
    }
    #master-data-root .fa-regular,
    #master-data-root .far,
    #master-data-root i.fa-regular,
    #master-data-root i.far {
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 400 !important;
        font-style: normal !important;
        font-variant: normal !important;
        text-rendering: auto !important;
        line-height: 1 !important;
        display: inline-block !important;
    }
    #master-data-root i.fa-brands,
    #master-data-root i.fab,
    #master-data-root .fa-brands,
    #master-data-root .fab {
        font-family: "Font Awesome 6 Brands" !important;
        font-weight: 400 !important;
        font-style: normal !important;
        font-variant: normal !important;
        text-rendering: auto !important;
        line-height: 1 !important;
        display: inline-block !important;
    }
    #master-data-root [class*="fa-"]::before {
        font-family: inherit !important;
        font-weight: inherit !important;
        font-style: inherit !important;
    }

    #master-data-root,
    #master-data-root input,
    #master-data-root select,
    #master-data-root textarea,
    #master-data-root button,
    #master-data-root .tab-btn,
    #master-data-root .desktop-tab-btn,
    #master-data-root table th,
    #master-data-root table td,
    #master-data-root label,
    #master-data-root p,
    #master-data-root span,
    #master-data-root td,
    #master-data-root th {
        font-size: 13px;
    }
    #master-data-root table th {
        font-size: 13px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    #master-data-root table td {
        font-size: 13px !important;
        overflow-wrap: break-word !important;
    }
    #master-data-root input,
    #master-data-root select,
    #master-data-root textarea {
        font-size: 13px !important;
    }
    #master-data-root button,
    #master-data-root .btn {
        font-size: 13px !important;
    }
    #master-data-root .text-xs,
    #master-data-root .text-\[10px\],
    #master-data-root .text-\[11px\],
    #master-data-root .text-\[12px\],
    .fixed .glass-panel .text-xs,
    .fixed .glass-panel .text-\[10px\],
    .fixed .glass-panel .text-\[11px\],
    .fixed .glass-panel .text-\[12px\] {
        font-size: 13px !important;
    }

    /* Desktop Tabs Styling (EduMaster Navigation) */
    .desktop-tab-btn {
        border-radius: 12px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        padding: 9px 16px !important;
        transition: all 0.2s ease !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #ffffff !important;
        color: #334155 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        white-space: nowrap !important;
        cursor: pointer !important;
    }
    .desktop-tab-btn:not(.tab-active) i {
        color: #004b87 !important;
        font-size: 14px !important;
        transition: color 0.2s ease !important;
    }
    .desktop-tab-btn:hover {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }
    .desktop-tab-btn.tab-active,
    html.light .desktop-tab-btn.tab-active,
    html.dark .desktop-tab-btn.tab-active {
        background-color: #004b87 !important;
        border-color: #004b87 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 75, 135, 0.25) !important;
    }
    .desktop-tab-btn.tab-active *,
    .desktop-tab-btn.tab-active i,
    .desktop-tab-btn.tab-active span,
    html.light .desktop-tab-btn.tab-active *,
    html.light .desktop-tab-btn.tab-active i,
    html.light .desktop-tab-btn.tab-active span,
    html.dark .desktop-tab-btn.tab-active *,
    html.dark .desktop-tab-btn.tab-active i,
    html.dark .desktop-tab-btn.tab-active span {
        color: #ffffff !important;
    }
    html.dark .desktop-tab-btn:not(.tab-active) {
        background-color: #00274d !important;
        border-color: #003661 !important;
        color: #e2e8f0 !important;
    }
    html.dark .desktop-tab-btn:not(.tab-active) i {
        color: #38bdf8 !important;
    }

    /* Action Buttons: Exact Palette from User Images */
    /* Cyan: #00c0ef */
    #master-data-root .bg-sky-600,
    #master-data-root .btn-cyan,
    #master-data-root button[title*="Detail"],
    #master-data-root button[title*="Show"] {
        background-color: #00c0ef !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
    }
    #master-data-root .bg-sky-600:hover,
    #master-data-root .btn-cyan:hover,
    #master-data-root button[title*="Detail"]:hover,
    #master-data-root button[title*="Show"]:hover {
        background-color: #00a7d0 !important;
    }

    /* Amber / Orange: #f39c12 */
    #master-data-root .bg-amber-600,
    #master-data-root .btn-amber,
    #master-data-root button[title*="Edit"] {
        background-color: #f39c12 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
    }
    #master-data-root .bg-amber-600:hover,
    #master-data-root .btn-amber:hover,
    #master-data-root button[title*="Edit"]:hover {
        background-color: #e08e0b !important;
    }

    /* Danger Red: #dd4b39 */
    #master-data-root .bg-rose-600,
    #master-data-root .btn-danger,
    #master-data-root button[title*="Hapus"] {
        background-color: #dd4b39 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
    }
    #master-data-root .bg-rose-600:hover,
    #master-data-root .btn-danger:hover,
    #master-data-root button[title*="Hapus"]:hover {
        background-color: #c23321 !important;
    }

    /* Green: #59c594 */
    #master-data-root .bg-emerald-600,
    #master-data-root .btn-green {
        background-color: #59c594 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
    }
    #master-data-root .bg-emerald-600:hover,
    #master-data-root .btn-green:hover {
        background-color: #48b282 !important;
    }

    /* Royal Blue: #3850c9 */
    #master-data-root .bg-blue-600,
    #master-data-root .btn-blue,
    #master-data-root .btn-primary-blue {
        background-color: #3850c9 !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
    }
    #master-data-root .bg-blue-600:hover,
    #master-data-root .btn-blue:hover,
    #master-data-root .btn-primary-blue:hover {
        background-color: #2b3ea6 !important;
    }

    /* Modal Styling Aligned with gifanya-adhiyaksa-jaya */
    html.light .fixed .glass-panel {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 25px 50px -12px rgba(0, 43, 91, 0.25) !important;
        color: #0f172a !important;
    }
    html.light .fixed .glass-panel h3,
    html.light .fixed .glass-panel label {
        color: #0f172a !important;
    }
    html.light .fixed .glass-panel input,
    html.light .fixed .glass-panel select,
    html.light .fixed .glass-panel textarea {
        background-color: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
    }
    html.light .fixed .glass-panel input:focus,
    html.light .fixed .glass-panel select:focus,
    html.light .fixed .glass-panel textarea:focus {
        background-color: #ffffff !important;
        border-color: #004b87 !important;
        box-shadow: 0 0 0 3px rgba(0, 75, 135, 0.15) !important;
    }

    /* Mobile Tab Slider */
    .mobile-tab-slider .tab-btn {
        background-color: #f1f5f9 !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    }
    .mobile-tab-slider .tab-btn:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }
    .mobile-tab-slider .tab-btn.bg-indigo-600 {
        background-color: #004b87 !important;
        color: #ffffff !important;
        border-color: #003661 !important;
        box-shadow: 0 4px 12px rgba(0, 75, 135, 0.35) !important;
    }
    html.dark .mobile-tab-slider .tab-btn:not(.bg-indigo-600) {
        background-color: #00274d !important;
        color: #e2e8f0 !important;
        border: 1px solid #003661 !important;
    }
</style>
@endsection

@section('content')
<!-- Top Progress Bar (Loading State Indicator) -->
<div id="top-progress-bar" class="fixed top-0 left-0 h-1.5 bg-gradient-to-r from-emerald-400 via-indigo-500 to-purple-500 z-[100] transition-all duration-300 w-0 opacity-0 pointer-events-none"></div>

<!-- Toast Container for Floating Alerts -->
<div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none"></div>

<div id="master-data-root" class="space-y-6 pb-32 md:pb-0" x-data="masterDataForm('{{ $tab }}')">
    <!-- Header Title (Matching gifanya-adhiyaksa-jaya style) -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2.5">
                <span>Manajemen Master Data</span>
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Struktur hirarki lokasi (Kantor &rarr; Plant &rarr; Gudang &rarr; Rak &rarr; BIN &rarr; Palet), Master UOM, dan Katalog Produk SKU</p>
        </div>
    </div>

    <!-- DESKTOP Tab Navigation Bar (EduMaster Academic Tabs) -->
    <div class="hidden md:flex space-x-2 border-b border-slate-200 dark:border-slate-700/80 pb-3 overflow-x-auto">
        <a href="{{ route('master.index', ['tab' => 'offices']) }}" onclick="MasterDataApp.loadTab(this.href, 'offices'); return false;" class="desktop-tab-btn {{ $tab === 'offices' ? 'tab-active' : '' }}" data-tab="offices">
            <span>1. Kantor Pusat</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'branches']) }}" onclick="MasterDataApp.loadTab(this.href, 'branches'); return false;" class="desktop-tab-btn {{ $tab === 'branches' ? 'tab-active' : '' }}" data-tab="branches">
            <span>2. Plant</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'warehouses']) }}" onclick="MasterDataApp.loadTab(this.href, 'warehouses'); return false;" class="desktop-tab-btn {{ $tab === 'warehouses' ? 'tab-active' : '' }}" data-tab="warehouses">
            <span>3. Gudang</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'racks']) }}" onclick="MasterDataApp.loadTab(this.href, 'racks'); return false;" class="desktop-tab-btn {{ $tab === 'racks' ? 'tab-active' : '' }}" data-tab="racks">
            <span>4. Rak</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'bins']) }}" onclick="MasterDataApp.loadTab(this.href, 'bins'); return false;" class="desktop-tab-btn {{ $tab === 'bins' ? 'tab-active' : '' }}" data-tab="bins">
            <span>5. BIN</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'uoms']) }}" onclick="MasterDataApp.loadTab(this.href, 'uoms'); return false;" class="desktop-tab-btn {{ $tab === 'uoms' ? 'tab-active' : '' }}" data-tab="uoms">
            <span>6. UOM Inventory</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'products']) }}" onclick="MasterDataApp.loadTab(this.href, 'products'); return false;" class="desktop-tab-btn {{ $tab === 'products' ? 'tab-active' : '' }}" data-tab="products">
            <span>7. Produk SKU</span>
        </a>

        <a href="{{ route('master.index', ['tab' => 'pallets']) }}" onclick="MasterDataApp.loadTab(this.href, 'pallets'); return false;" class="desktop-tab-btn {{ $tab === 'pallets' ? 'tab-active' : '' }}" data-tab="pallets">
            <span>8. Palet</span>
        </a>
    </div>

    <!-- ERGONOMIC MOBILE BOTTOM TAB SLIDER (Positioned directly above bottom navigation for thumb-reach) -->
    <div class="fixed bottom-[52px] inset-x-0 z-40 md:hidden bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 px-3 py-2 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] dark:shadow-[0_-4px_20px_rgba(0,0,0,0.35)]">
        <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar scroll-smooth mobile-tab-slider">
            <a href="{{ route('master.index', ['tab' => 'offices']) }}" onclick="MasterDataApp.loadTab(this.href, 'offices'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'offices' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="offices">
                <span>1. Kantor Pusat</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'branches']) }}" onclick="MasterDataApp.loadTab(this.href, 'branches'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'branches' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="branches">
                <span>2. Plant</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'warehouses']) }}" onclick="MasterDataApp.loadTab(this.href, 'warehouses'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'warehouses' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="warehouses">
                <span>3. Gudang</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'racks']) }}" onclick="MasterDataApp.loadTab(this.href, 'racks'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'racks' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="racks">
                <span>4. Rak</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'bins']) }}" onclick="MasterDataApp.loadTab(this.href, 'bins'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'bins' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="bins">
                <span>5. BIN</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'uoms']) }}" onclick="MasterDataApp.loadTab(this.href, 'uoms'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'uoms' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="uoms">
                <span>6. UOM Inventory</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'products']) }}" onclick="MasterDataApp.loadTab(this.href, 'products'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'products' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="products">
                <span>7. Produk SKU</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'pallets']) }}" onclick="MasterDataApp.loadTab(this.href, 'pallets'); return false;" class="tab-btn shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition flex items-center justify-center cursor-pointer whitespace-nowrap active:scale-95 {{ $tab === 'pallets' ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'bg-slate-200 text-slate-700 hover:bg-slate-300 dark:bg-slate-700/80 dark:text-slate-200' }}" data-tab="pallets">
                <span>8. Palet</span>
            </a>
        </div>
    </div>

    <!-- DYNAMICALLY LOADED ASYNC TAB CONTAINER -->
    <div id="tab-content-container" class="transition-all duration-200 min-h-[400px]">
        @include('master.tabs.' . $tab)
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: TAMBAH PRODUK SKU BARU                                      -->
    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- MODAL POPUP: TAMBAH PRODUK SKU BARU                                      -->
    <!-- ========================================================================= -->
    <div x-show="showAddProductModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-2xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-indigo-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] overflow-y-auto my-auto" @click.away="showAddProductModal = false">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-indigo-400">
                    <i class="fa-solid fa-boxes-stacked text-xl"></i>
                    <div>
                        <h3 class="text-base font-bold text-white">Tambah Produk SKU Baru</h3>
                        <p class="text-[11px] text-slate-400">Daftarkan SKU baru beserta konfigurasi kemasan & konversi satuan</p>
                    </div>
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
                            <button type="button" @click="autoProductCode()" class="px-3 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 font-mono font-bold text-xs rounded-xl border border-slate-600 cursor-pointer" title="Generate SKU Otomatis">Auto</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Item Produk <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" required placeholder="misal: BaliCafé Arabica Gold..." class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Group Item</label>
                        <input type="text" name="item_group" list="list_item_groups" placeholder="FINISHED GOODS (MANUFAKTUR)" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <datalist id="list_item_groups">
                            @foreach($itemGroups as $ig)
                                <option value="{{ $ig }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Brand</label>
                        <input type="text" name="brand" list="list_brands" placeholder="BaliCafé / Excelso / Intirasa" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <datalist id="list_brands">
                            @foreach($brands as $br)
                                <option value="{{ $br }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">UOM Inventory (Base Unit) <span class="text-rose-400">*</span></label>
                        <select id="select_uom_inventory" name="uom_inventory" required @change="onBaseUomChange($event.target.value)" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Satuan UOM --</option>
                            @foreach($allUoms as $u)
                                <option value="{{ $u->name }}">{{ $u->name }} ({{ $u->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Keaktifan <span class="text-rose-400">*</span></label>
                        <select id="select_is_active" name="is_active" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white">
                            <option value="1">Aktif (Yes)</option>
                            <option value="0">Non-Aktif (No)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Grup UOM / Kemasan</label>
                        <input type="text" name="uom_group" placeholder="misal: BOX24, CAN05, CTN12" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Qty per Grup UOM (Isi Kemasan)</label>
                        <input type="number" step="any" min="0" name="qty_per_uom_group" value="1" placeholder="1" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <!-- Section: Dynamic UOM Conversions Builder -->
                <div class="space-y-2.5 pt-3 border-t border-slate-700/60">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-200 flex items-center space-x-1.5">
                                <i class="fa-solid fa-arrows-split-up-and-left text-indigo-400"></i>
                                <span>Konversi Satuan UOM (Multi-Satuan)</span>
                            </span>
                            <p class="text-[11px] text-slate-400">Konfigurasikan satuan kemasan bertingkat (misal: 1 KARTON = 24 BOX).</p>
                        </div>
                        <button type="button" 
                                @click="addNewConversion()" 
                                class="px-3 py-1.5 bg-indigo-600/80 hover:bg-indigo-600 text-white rounded-lg font-semibold text-xs flex items-center space-x-1 transition cursor-pointer">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>Tambah Konversi</span>
                        </button>
                    </div>

                    <!-- Repeater Table -->
                    <div class="border border-slate-800 rounded-xl overflow-hidden bg-slate-900/50">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900 text-slate-400 uppercase text-[10px]">
                                <tr>
                                    <th class="p-2.5">Satuan Konversi</th>
                                    <th class="p-2.5">Qty Konversi</th>
                                    <th class="p-2.5">Satuan Dasar (Base)</th>
                                    <th class="p-2.5 w-10 text-center">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                <template x-for="(conv, idx) in newProductConversions" :key="idx">
                                    <tr class="hover:bg-slate-800/40">
                                        <td class="p-2">
                                            <input type="text" 
                                                   :name="'conversions[' + idx + '][uom_convert]'" 
                                                   x-model="conv.uom_convert" 
                                                   required 
                                                   placeholder="misal: KARTON / BOX" 
                                                   class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono uppercase text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" 
                                                   step="any" 
                                                   min="0.01" 
                                                   :name="'conversions[' + idx + '][qty_convert]'" 
                                                   x-model="conv.qty_convert" 
                                                   required 
                                                   placeholder="24" 
                                                   class="w-full p-2 bg-slate-900 border border-slate-700 rounded-lg text-white font-mono text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" 
                                                   :name="'conversions[' + idx + '][uom_base]'" 
                                                   x-model="conv.uom_base" 
                                                   readonly 
                                                   class="w-full p-2 bg-slate-800/70 border border-slate-700 rounded-lg text-emerald-400 font-mono text-xs cursor-not-allowed">
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" 
                                                    @click="removeNewConversion(idx)" 
                                                    class="w-7 h-7 flex items-center justify-center mx-auto text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-lg transition cursor-pointer"
                                                    title="Hapus baris konversi">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="newProductConversions.length === 0">
                                    <tr>
                                        <td colspan="4" class="p-3 text-center text-slate-500 italic text-[11px]">
                                            Opsional. Jika tidak ditambahkan, sistem otomatis membuat 1 konversi dasar (1 : 1 Base Unit).
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showAddProductModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-floppy-disk text-xs mr-1"></i>
                        <span class="btn-text">Simpan Produk SKU</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: EDIT SATUAN UOM                                              -->
    <!-- ========================================================================= -->
    <div x-show="showEditUomModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-lg p-6 rounded-2xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-amber-400">
                    <i class="fa-solid fa-pen-to-square text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Satuan UOM Inventory</h3>
                </div>
                <button type="button" @click="showEditUomModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('/master-data/uom') }}/' + editUomForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Kode Satuan (ID)</label>
                    <input type="text" :value="editUomForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-indigo-400 font-mono font-bold cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Nama Satuan UOM <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" x-model="editUomForm.name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="misal: Box / Pieces / Kilogram">
                </div>

                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Keterangan / Fungsi</label>
                    <textarea name="description" x-model="editUomForm.description" rows="3" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Catatan fungsi atau standar kemasan..."></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showEditUomModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                        <span class="btn-text">Perbarui Satuan UOM</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: DETAIL PRODUK SKU                                            -->
    <!-- ========================================================================= -->
    <div x-show="showDetailProductModal" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" 
         x-cloak>
        <div class="glass-panel w-full max-w-2xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-sky-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] overflow-y-auto my-auto" 
             @click.away="showDetailProductModal = false">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-sky-500 dark:text-sky-400">
                    <i class="fa-solid fa-circle-info text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Detail Produk SKU</h3>
                </div>
                <button type="button" @click="showDetailProductModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl p-1 cursor-pointer transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <template x-if="detailProduct">
                <div class="space-y-4 text-xs">
                    <!-- Header Info Card -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-inner">
                        <div>
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] font-medium uppercase tracking-wider">Kode SKU</div>
                            <div class="text-lg font-mono font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5" x-text="detailProduct.id"></div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full font-bold text-xs" :class="detailProduct.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/30' : 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-300 dark:border-rose-500/30'" x-text="detailProduct.is_active ? 'Aktif (Active)' : 'Non-Aktif (Inactive)'"></span>
                        </div>
                    </div>

                    <!-- Main Grid Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">Nama Produk</div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm" x-text="detailProduct.name"></div>
                        </div>
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">Item Group</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200" x-text="detailProduct.item_group || '-'"></div>
                        </div>
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">Brand</div>
                            <div class="font-semibold text-slate-800 dark:text-slate-200" x-text="detailProduct.brand || '-'"></div>
                        </div>
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">UOM Inventory (Base Unit)</div>
                            <div class="font-bold font-mono text-emerald-700 dark:text-emerald-400 text-sm" x-text="detailProduct.uom_inventory"></div>
                        </div>
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">UOM Group / Kemasan</div>
                            <div class="font-bold font-mono text-slate-800 dark:text-slate-200" x-text="detailProduct.uom_group || '-'"></div>
                        </div>
                        <div class="p-3.5 bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xs">
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] mb-1 font-medium">Qty per UOM Group</div>
                            <div class="font-bold text-amber-600 dark:text-amber-400 text-sm" x-text="detailProduct.qty_per_uom_group || '-'"></div>
                        </div>
                    </div>

                    <!-- UOM Conversions Table -->
                    <div class="space-y-2">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">
                            <span>Daftar Konversi Satuan UOM</span>
                        </div>
                        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-950/70 shadow-xs">
                            <table class="w-full text-left">
                                <thead class="bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-300 uppercase text-[10px] font-extrabold tracking-wider border-b border-slate-200 dark:border-slate-800">
                                    <tr>
                                        <th class="p-2.5 sm:p-3">Satuan Konversi</th>
                                        <th class="p-2.5 sm:p-3">Qty Konversi</th>
                                        <th class="p-2.5 sm:p-3">Satuan Dasar (Base)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900/40">
                                    <template x-for="(c, idx) in detailProduct.conversions" :key="idx">
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                                            <td class="p-2.5 sm:p-3">
                                                <span class="px-2 py-0.5 rounded font-mono font-extrabold text-xs bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300 border border-sky-300 dark:border-sky-700/60 shadow-xs" x-text="c.uom_convert"></span>
                                            </td>
                                            <td class="p-2.5 sm:p-3 font-mono font-extrabold text-xs text-amber-600 dark:text-amber-400" x-text="c.qty_convert"></td>
                                            <td class="p-2.5 sm:p-3 font-mono font-bold text-xs text-slate-900 dark:text-slate-100" x-text="c.uom_base"></td>
                                        </tr>
                                    </template>
                                    <template x-if="!detailProduct.conversions || detailProduct.conversions.length === 0">
                                        <tr>
                                            <td colspan="3" class="p-4 text-center text-slate-500 dark:text-slate-400 italic">Hanya satuan dasar (1 : 1 base unit).</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 text-[11px]">
                        <span>Dibuat: <strong class="text-slate-700 dark:text-slate-200" x-text="detailProduct.created_at"></strong></span>
                        <button type="button" @click="showDetailProductModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl border border-slate-300 dark:border-slate-700 transition cursor-pointer active:scale-95 shadow-sm">Tutup</button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: EDIT PRODUK SKU                                              -->
    <!-- ========================================================================= -->
    <div x-show="showEditProductModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 pb-28 md:p-6 md:pb-6 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-panel w-full max-w-xl p-5 sm:p-6 rounded-2xl sm:rounded-3xl shadow-2xl space-y-5 border border-amber-500/40 max-h-[calc(100vh-140px)] md:max-h-[90vh] overflow-y-auto my-auto" @click.away="showEditProductModal = false">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center space-x-2 text-amber-400">
                    <i class="fa-solid fa-pen-to-square text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white">Edit Produk SKU</h3>
                </div>
                <button type="button" @click="showEditProductModal = false" class="text-slate-400 hover:text-white text-xl p-1 cursor-pointer">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form :action="'{{ url('/master-data/product') }}/' + editProductForm.id" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Kode SKU (ID)</label>
                        <input type="text" :value="editProductForm.id" readonly disabled class="w-full p-2.5 bg-slate-800/60 border border-slate-700 rounded-xl text-indigo-400 font-mono font-bold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Nama Item Produk <span class="text-rose-400">*</span></label>
                        <input type="text" name="name" x-model="editProductForm.name" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none" placeholder="Nama Produk">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Group Item</label>
                        <input type="text" name="item_group" x-model="editProductForm.item_group" placeholder="misal: FINISHED GOODS (MANUFAKTUR)" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">UOM Inventory (Base Unit) <span class="text-rose-400">*</span></label>
                        <select id="select_edit_uom_inventory" name="uom_inventory" x-model="editProductForm.uom_inventory" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">-- Pilih Satuan UOM --</option>
                            @foreach($allUoms as $u)
                                <option value="{{ $u->name }}">{{ $u->name }} ({{ $u->id }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">UOM Group / Kemasan</label>
                        <input type="text" name="uom_group" x-model="editProductForm.uom_group" placeholder="misal: BOX / CTN" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Qty per UOM Group <span class="text-rose-400">*</span></label>
                        <input type="number" step="any" min="0" name="qty_per_uom_group" x-model="editProductForm.qty_per_uom_group" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Brand</label>
                        <input type="text" name="brand" x-model="editProductForm.brand" placeholder="misal: BaliCafé / Excelso" class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-slate-800 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1 font-semibold">Status Keaktifan <span class="text-rose-400">*</span></label>
                        <select id="select_edit_is_active" name="is_active" x-model="editProductForm.is_active" required class="w-full p-2.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="1">Aktif (Yes)</option>
                            <option value="0">Non-Aktif (No)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-slate-700/60">
                    <button type="button" @click="showEditProductModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg transition cursor-pointer flex items-center justify-center space-x-1.5">
                        <span class="btn-text">Perbarui Produk SKU</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sub-Modals for Office, Branch, Warehouse, Rack, Bin, and UOM Products -->
    @include('master.modals.offices')
    @include('master.modals.branches')
    @include('master.modals.warehouses')
    @include('master.modals.racks')
    @include('master.modals.bins')
    @include('master.modals.uoms')
    @include('master.modals.uom_products')
    @include('master.modals.pallets')
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
            'select_brand_filter', 'select_group_filter', 'select_is_active',
            'select_uom_inventory',
            'select_edit_uom_inventory', 'select_edit_is_active'
        ];
        masterSelectIds.forEach(id => {
            initMasterTomSelect(id);
        });
    }

    // Mobile Swipeable Card Action Engine (Swipe Left: Detail & Edit, Swipe Right: Delete)
    window.swipeableCard = function() {
        return {
            startX: 0,
            startY: 0,
            offset: 0,
            isDragging: false,
            isScrolling: false,
            maxLeftSwipe: -144, // reveals Right Tray (Detail & Edit)
            maxRightSwipe: 96,  // reveals Left Tray (Delete)

            touchStart(e) {
                this.startX = e.touches[0].clientX;
                this.startY = e.touches[0].clientY;
                this.isDragging = true;
                this.isScrolling = false;
            },

            touchMove(e) {
                if (!this.isDragging) return;
                const diffX = e.touches[0].clientX - this.startX;
                const diffY = e.touches[0].clientY - this.startY;

                // Detect vertical page scrolling
                if (!this.isScrolling && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 8) {
                    this.isScrolling = true;
                    this.isDragging = false;
                    return;
                }

                if (this.isScrolling) return;

                // Horizontal swipe detected
                if (Math.abs(diffX) > 10 && e.cancelable) {
                    e.preventDefault();
                }

                // Resistance past limits
                if (diffX > 0) {
                    this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                } else {
                    this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                }
            },

            touchEnd(e) {
                if (!this.isDragging) return;
                this.isDragging = false;
                if (this.offset > 40) {
                    this.offset = this.maxRightSwipe;
                } else if (this.offset < -45) {
                    this.offset = this.maxLeftSwipe;
                } else {
                    this.offset = 0;
                }
            },

            mouseDown(e) {
                if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                this.startX = e.clientX;
                this.isDragging = true;
            },

            mouseMove(e) {
                if (!this.isDragging) return;
                const diffX = e.clientX - this.startX;
                if (Math.abs(diffX) > 5) {
                    if (diffX > 0) {
                        this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                    } else {
                        this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                    }
                }
            },

            mouseUp(e) {
                if (!this.isDragging) return;
                this.isDragging = false;
                if (this.offset > 40) {
                    this.offset = this.maxRightSwipe;
                } else if (this.offset < -45) {
                    this.offset = this.maxLeftSwipe;
                } else {
                    this.offset = 0;
                }
            },

            closeTray() {
                this.offset = 0;
            }
        };
    };

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

            // Update Desktop Tabs UI (EduMaster Academic Tabs)
            document.querySelectorAll('.desktop-tab-btn').forEach(btn => {
                const isActive = btn.getAttribute('data-tab') === tabName;
                if (isActive) {
                    btn.classList.add('tab-active');
                } else {
                    btn.classList.remove('tab-active');
                }
            });

            // Update Mobile Bottom Slider Tabs UI
            document.querySelectorAll('.mobile-tab-slider .tab-btn').forEach(btn => {
                const isActive = btn.getAttribute('data-tab') === tabName;
                if (isActive) {
                    btn.classList.remove('bg-slate-200', 'text-slate-700');
                    btn.classList.add('bg-indigo-600', 'text-white', 'font-bold', 'shadow-md', 'shadow-indigo-600/30');
                } else {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'font-bold', 'shadow-md', 'shadow-indigo-600/30');
                    btn.classList.add('bg-slate-200', 'text-slate-700');
                }
            });

            // Smoothly auto-scroll active tab into center view in mobile bottom slider
            const activeMobileBtn = document.querySelector('.mobile-tab-slider .tab-btn[data-tab="' + tabName + '"]');
            if (activeMobileBtn) {
                activeMobileBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
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
                        // Re-execute scripts embedded in dynamic tab content
                        container.querySelectorAll('script').forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                        if (window.Alpine) {
                            Alpine.initTree(container);
                        }
                    }
                    window.history.pushState({}, '', url);
                    initAllMasterTomSelects();
                    this.bindPaginationLinks();
                    if (tabName === 'products' && typeof window.initProductInfiniteScroll === 'function') {
                        setTimeout(() => window.initProductInfiniteScroll(), 50);
                    }
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
                    
                    // Close modals if AlpineJS context exists
                    const masterAlpine = getMasterAlpine();
                    if (masterAlpine) {
                        masterAlpine.showAddProductModal = false;
                        masterAlpine.showEditUomModal = false;
                        masterAlpine.showAddUomModal = false;
                        masterAlpine.showUomProductsModal = false;
                        masterAlpine.showEditProductModal = false;
                        masterAlpine.showDetailProductModal = false;
                        masterAlpine.showDetailOfficeModal = false;
                        masterAlpine.showEditOfficeModal = false;
                        masterAlpine.showAddOfficeModal = false;
                        masterAlpine.showDetailBranchModal = false;
                        masterAlpine.showEditBranchModal = false;
                        masterAlpine.showAddBranchModal = false;
                        masterAlpine.showDetailWhModal = false;
                        masterAlpine.showEditWhModal = false;
                        masterAlpine.showAddWhModal = false;
                        masterAlpine.showDetailRackModal = false;
                        masterAlpine.showEditRackModal = false;
                        masterAlpine.showAddRackModal = false;
                        masterAlpine.showDetailBinModal = false;
                        masterAlpine.showEditBinModal = false;
                        masterAlpine.showAddBinModal = false;
                        masterAlpine.showDetailPalletModal = false;
                        masterAlpine.showEditPalletModal = false;
                        masterAlpine.showAddPalletModal = false;
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

    // Helper to get master data Alpine instance (avoids body x-data collision)
    function getMasterAlpine() {
        const root = document.getElementById('master-data-root');
        if (!root) return null;
        if (window.Alpine && typeof window.Alpine.$data === 'function') {
            return window.Alpine.$data(root);
        }
        return root._x_dataStack ? root._x_dataStack[0] : null;
    }

    // Global helper modal triggers (resilient to DOM replacements and SPA navigation)
    window.openDetailProductModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-product');
            const prod = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailProduct = prod;
                alpine.showDetailProductModal = true;
            }
        } catch (e) {
            console.error('Error opening detail product modal:', e);
        }
    };

    window.openEditProductModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-product');
            const prod = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditProductModal(prod);
            }
        } catch (e) {
            console.error('Error opening edit product modal:', e);
        }
    };

    window.openAddProductModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddProductModal();
        }
    };

    window.openUomProductsModal = function(id, name, products) {
        try {
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openUomProductsModal(id, name, products);
            }
        } catch (e) {
            console.error('Error opening UOM products modal:', e);
        }
    };

    // Offices
    window.openDetailOfficeModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-office');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailOffice = data;
                alpine.showDetailOfficeModal = true;
            }
        } catch (e) {
            console.error('Error opening detail office modal:', e);
        }
    };
    window.openEditOfficeModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-office');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditOfficeModal(data);
            }
        } catch (e) {
            console.error('Error opening edit office modal:', e);
        }
    };
    window.openAddOfficeModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddOfficeModal();
        }
    };

    // Branches
    window.openDetailBranchModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-branch');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailBranch = data;
                alpine.showDetailBranchModal = true;
            }
        } catch (e) {
            console.error('Error opening detail branch modal:', e);
        }
    };
    window.openEditBranchModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-branch');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditBranchModal(data);
            }
        } catch (e) {
            console.error('Error opening edit branch modal:', e);
        }
    };
    window.openAddBranchModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddBranchModal();
        }
    };

    // Warehouses
    window.openDetailWhModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-warehouse');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailWh = data;
                alpine.showDetailWhModal = true;
            }
        } catch (e) {
            console.error('Error opening detail warehouse modal:', e);
        }
    };
    window.openEditWhModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-warehouse');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditWhModal(data);
            }
        } catch (e) {
            console.error('Error opening edit warehouse modal:', e);
        }
    };
    window.openAddWhModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddWhModal();
        }
    };

    // Racks
    window.openDetailRackModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-rack');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailRack = data;
                alpine.showDetailRackModal = true;
            }
        } catch (e) {
            console.error('Error opening detail rack modal:', e);
        }
    };
    window.openEditRackModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-rack');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditRackModal(data);
            }
        } catch (e) {
            console.error('Error opening edit rack modal:', e);
        }
    };
    window.openAddRackModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddRackModal();
        }
    };

    // Bins
    window.openDetailBinModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-bin');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailBin = data;
                alpine.showDetailBinModal = true;
            }
        } catch (e) {
            console.error('Error opening detail bin modal:', e);
        }
    };
    window.openEditBinModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-bin');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditBinModal(data);
            }
        } catch (e) {
            console.error('Error opening edit bin modal:', e);
        }
    };
    window.openAddBinModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddBinModal();
        }
    };
    window.openAddUomModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddUomModal();
        }
    };

    window.openDetailUomModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-uom');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailUom = data;
                alpine.showDetailUomModal = true;
            }
        } catch (e) {
            console.error('Error opening detail UOM modal:', e);
        }
    };

    window.openDetailPalletModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-pallet');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.detailPallet = data;
                alpine.showDetailPalletModal = true;
            }
        } catch (e) {
            console.error('Error opening detail pallet modal:', e);
        }
    };

    window.openEditPalletModal = function(btn) {
        try {
            const raw = typeof btn === 'string' ? btn : btn.getAttribute('data-pallet');
            const data = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const alpine = getMasterAlpine();
            if (alpine) {
                alpine.openEditPalletModal(data);
            }
        } catch (e) {
            console.error('Error opening edit pallet modal:', e);
        }
    };

    window.openAddPalletModal = function() {
        const alpine = getMasterAlpine();
        if (alpine) {
            alpine.openAddPalletModal();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        initAllMasterTomSelects();
        MasterDataApp.bindPaginationLinks();

        // Auto-center active mobile tab on page load
        const initialMobileBtn = document.querySelector('.mobile-tab-slider .tab-btn[data-tab="{{ $tab }}"]');
        if (initialMobileBtn) {
            setTimeout(() => {
                initialMobileBtn.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }, 150);
        }

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

            // SKU Grouping & Collapsible Accordion Dropdown
            expandedSkus: [],
            isSkuExpanded(sku) {
                return this.expandedSkus.includes(sku);
            },
            toggleSku(sku) {
                if (this.expandedSkus.includes(sku)) {
                    this.expandedSkus = this.expandedSkus.filter(id => id !== sku);
                } else {
                    this.expandedSkus.push(sku);
                }
            },
            expandAllSkus(skus) {
                if (Array.isArray(skus)) {
                    this.expandedSkus = Array.from(new Set([...this.expandedSkus, ...skus]));
                }
            },
            collapseAllSkus() {
                this.expandedSkus = [];
            },

            // Add Product SKU State & Methods
            showAddProductModal: false,
            newProductConversions: [],
            openAddProductModal() {
                this.forms.product_id = '';
                this.forms.product_uom_inventory = '';
                this.newProductConversions = [];
                this.showAddProductModal = true;
                this.$nextTick(() => {
                    initMasterTomSelect('select_uom_inventory');
                    if (tsMasterInstances['select_uom_inventory']) {
                        tsMasterInstances['select_uom_inventory'].clear();
                    }
                });
            },
            addNewConversion() {
                const baseUom = this.forms.product_uom_inventory || 
                    (document.getElementById('select_uom_inventory') ? document.getElementById('select_uom_inventory').value : '');
                this.newProductConversions.push({
                    uom_convert: '',
                    qty_convert: 1,
                    uom_base: baseUom || ''
                });
            },
            removeNewConversion(idx) {
                this.newProductConversions.splice(idx, 1);
            },
            onBaseUomChange(val) {
                this.forms.product_uom_inventory = val;
                this.newProductConversions.forEach(c => {
                    c.uom_base = val;
                });
            },

            showEditUomModal: false,
            showDetailUomModal: false,
            detailUom: null,
            openDetailUomModal(uom) {
                this.detailUom = uom;
                this.showDetailUomModal = true;
            },
            showUomProductsModal: false,
            uomProductsTitle: '',
            uomProductsList: [],
            uomProductSearchKeyword: '',

            get filteredUomProducts() {
                if (!this.uomProductSearchKeyword || !this.uomProductSearchKeyword.trim()) {
                    return this.uomProductsList;
                }
                const q = this.uomProductSearchKeyword.toLowerCase().trim();
                return this.uomProductsList.filter(p => {
                    return (p.id && p.id.toLowerCase().includes(q)) ||
                           (p.name && p.name.toLowerCase().includes(q)) ||
                           (p.brand && p.brand.toLowerCase().includes(q)) ||
                           (p.item_group && p.item_group.toLowerCase().includes(q)) ||
                           (p.uom_group && p.uom_group.toLowerCase().includes(q));
                });
            },

            openUomProductsModal(id, name, products) {
                this.uomProductsTitle = name + ' (' + id + ')';
                this.uomProductsList = Array.isArray(products) ? products : (typeof products === 'string' ? JSON.parse(products) : []);
                this.uomProductSearchKeyword = '';
                this.showUomProductsModal = true;
            },

            filterProductsTabByUom(uomTitle) {
                this.showUomProductsModal = false;
                const uomName = uomTitle ? uomTitle.split(' (')[0] : '';
                const url = '{{ route('master.index') }}?tab=products&search_product=' + encodeURIComponent(uomName);
                MasterDataApp.loadTab(url, 'products');
            },

            showDetailProductModal: false,
            showEditProductModal: false,
            detailProduct: null,
            editProductForm: {
                id: '',
                name: '',
                item_group: '',
                uom_inventory: '',
                uom_group: '',
                qty_per_uom_group: 1,
                brand: '',
                is_active: '1'
            },

            openDetailProductModal(prod) {
                this.detailProduct = prod;
                this.showDetailProductModal = true;
            },

            openEditProductModal(prod) {
                this.editProductForm.id = prod.id;
                this.editProductForm.name = prod.name;
                this.editProductForm.item_group = prod.item_group || '';
                this.editProductForm.uom_inventory = prod.uom_inventory || '';
                this.editProductForm.uom_group = prod.uom_group || '';
                this.editProductForm.qty_per_uom_group = prod.qty_per_uom_group || 1;
                this.editProductForm.brand = prod.brand || '';
                this.editProductForm.is_active = prod.is_active ? '1' : '0';
                this.showEditProductModal = true;

                this.$nextTick(() => {
                    initMasterTomSelect('select_edit_uom_inventory');
                    if (tsMasterInstances['select_edit_uom_inventory']) {
                        tsMasterInstances['select_edit_uom_inventory'].setValue(this.editProductForm.uom_inventory, true);
                    }
                    const elActive = document.getElementById('select_edit_is_active');
                    if (elActive) {
                        elActive.value = this.editProductForm.is_active;
                    }
                });
            },

            editUomForm: {
                id: '',
                name: '',
                description: ''
            },

            // Offices
            showDetailOfficeModal: false,
            showEditOfficeModal: false,
            showAddOfficeModal: false,
            detailOffice: null,
            editOfficeForm: { id: '', name: '', address: '' },
            openAddOfficeModal() {
                this.forms.office_id = '';
                this.autoOfficeCode();
                this.showAddOfficeModal = true;
            },
            openEditOfficeModal(off) {
                this.editOfficeForm.id = off.id;
                this.editOfficeForm.name = off.name;
                this.editOfficeForm.address = off.address || '';
                this.showEditOfficeModal = true;
            },

            // Branches
            showDetailBranchModal: false,
            showEditBranchModal: false,
            showAddBranchModal: false,
            detailBranch: null,
            editBranchForm: { id: '', office_id: '', name: '', city: '' },
            openAddBranchModal() {
                this.forms.branch_id = '';
                this.autoBranchCode();
                this.showAddBranchModal = true;
            },
            openEditBranchModal(br) {
                this.editBranchForm.id = br.id;
                this.editBranchForm.office_id = br.office_id;
                this.editBranchForm.name = br.name;
                this.editBranchForm.city = br.city || '';
                this.showEditBranchModal = true;
                this.$nextTick(() => {
                    initMasterTomSelect('select_edit_branch_office_id');
                    if (tsMasterInstances['select_edit_branch_office_id']) {
                        tsMasterInstances['select_edit_branch_office_id'].setValue(this.editBranchForm.office_id, true);
                    }
                });
            },

            // Warehouses
            showDetailWhModal: false,
            showEditWhModal: false,
            showAddWhModal: false,
            detailWh: null,
            editWhForm: { id: '', branch_id: '', name: '', address: '' },
            openAddWhModal() {
                this.forms.wh_id = '';
                this.autoWhCode();
                this.showAddWhModal = true;
            },
            openEditWhModal(wh) {
                this.editWhForm.id = wh.id;
                this.editWhForm.branch_id = wh.branch_id;
                this.editWhForm.name = wh.name;
                this.editWhForm.address = wh.address || '';
                this.showEditWhModal = true;
                this.$nextTick(() => {
                    initMasterTomSelect('select_edit_wh_branch_id');
                    if (tsMasterInstances['select_edit_wh_branch_id']) {
                        tsMasterInstances['select_edit_wh_branch_id'].setValue(this.editWhForm.branch_id, true);
                    }
                });
            },

            // Racks
            showDetailRackModal: false,
            showEditRackModal: false,
            showAddRackModal: false,
            detailRack: null,
            editRackForm: { id: '', warehouse_id: '', rack_name: '' },
            openAddRackModal() {
                this.forms.rack_id = '';
                this.autoRackCode();
                this.showAddRackModal = true;
            },
            openEditRackModal(rk) {
                this.editRackForm.id = rk.id;
                this.editRackForm.warehouse_id = rk.warehouse_id;
                this.editRackForm.rack_name = rk.rack_name;
                this.showEditRackModal = true;
                this.$nextTick(() => {
                    initMasterTomSelect('select_edit_rack_wh_id');
                    if (tsMasterInstances['select_edit_rack_wh_id']) {
                        tsMasterInstances['select_edit_rack_wh_id'].setValue(this.editRackForm.warehouse_id, true);
                    }
                });
            },

            // Bins
            showDetailBinModal: false,
            showEditBinModal: false,
            showAddBinModal: false,
            detailBin: null,
            editBinForm: { id: '', rack_id: '', column_no: 1, level_no: 1, max_pallet: 1 },
            openAddBinModal() {
                this.forms.bin_id = '';
                this.autoBinCode();
                this.showAddBinModal = true;
            },
            openEditBinModal(bn) {
                this.editBinForm.id = bn.id;
                this.editBinForm.rack_id = bn.rack_id;
                this.editBinForm.column_no = bn.column_no;
                this.editBinForm.level_no = bn.level_no;
                this.editBinForm.max_pallet = bn.max_pallet;
                this.showEditBinModal = true;
                this.$nextTick(() => {
                    initMasterTomSelect('select_edit_bin_rack_id');
                    if (tsMasterInstances['select_edit_bin_rack_id']) {
                        tsMasterInstances['select_edit_bin_rack_id'].setValue(this.editBinForm.rack_id, true);
                    }
                });
            },

            showAddUomModal: false,
            openAddUomModal() {
                this.forms.uom_id = '';
                this.autoUomCode();
                this.showAddUomModal = true;
            },

            openEditUomModal(id, name, desc) {
                this.editUomForm.id = id;
                this.editUomForm.name = name;
                this.editUomForm.description = desc || '';
                this.showEditUomModal = true;
            },

            showDetailPalletModal: false,
            showEditPalletModal: false,
            showAddPalletModal: false,
            detailPallet: null,
            editPalletForm: { id: '', pallet_type_id: '', bin_id: '', max_capacity: 72, is_active: 1 },
            openAddPalletModal() {
                this.forms.pallet_id = '';
                this.autoPalletCode();
                this.showAddPalletModal = true;
            },
            openEditPalletModal(plt) {
                this.editPalletForm.id = plt.id;
                this.editPalletForm.pallet_type_id = plt.pallet_type_id;
                this.editPalletForm.bin_id = plt.bin_id || '';
                this.editPalletForm.max_capacity = plt.max_capacity || 72;
                this.editPalletForm.is_active = plt.is_active ? 1 : 0;
                this.showEditPalletModal = true;
            },

            forms: {
                office_id: '',
                branch_id: '',
                wh_id: '',
                rack_id: '',
                bin_id: '',
                uom_id: '',
                product_id: '',
                pallet_id: ''
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

            autoUomCode() {
                const prefixes = ['BOX', 'PCS', 'KG', 'CAN', 'BAG', 'BTL', 'CTN', 'PAC', 'ROL', 'SET'];
                const rand = prefixes[Math.floor(Math.random() * prefixes.length)] + '-' + Math.floor(10 + Math.random() * 90);
                this.forms.uom_id = rand;
            },

            autoProductCode() {
                this.forms.product_id = 'FGD' + Math.floor(10000 + Math.random() * 90000);
            },

            autoPalletCode() {
                this.forms.pallet_id = 'PLT-' + Math.floor(1000 + Math.random() * 9000);
            }
        }
    }
</script>
@endsection
@endsection
