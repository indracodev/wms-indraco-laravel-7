<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WMS Indraco') - Warehouse Management System</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Gift Learn (EduMaster) Brand Color System
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0284c7', // Brand Secondary
                            600: '#004b87', // Brand Primary Navy (EduMaster)
                            700: '#003661', // Brand Header Navy
                            800: '#00274d', // Brand Deep Dark Drawer
                            900: '#001b36',
                            gold: '#ffc107',
                            'gold-dark': '#eab308',
                        },
                        // Map indigo to EduMaster Navy Blue palette for 100% ecosystem consistency
                        indigo: {
                            50: '#f0f7ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0284c7',
                            600: '#004b87', // Primary EduMaster Navy
                            700: '#003661', // Header SIM Navy
                            800: '#00274d', // Dark Drawer Navy
                            900: '#001b36',
                            950: '#001226',
                        },
                        darkbg: '#001b36',
                        darkcard: '#00274d',
                        darkborder: '#003661'
                    },
                    fontSize: {
                        '2xs': ['13px', '18px'],
                        'xs': ['13px', '18px'],
                        'sm': ['13.5px', '19px'],
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Custom Styles -->
    <style>
        html, body, p, h1, h2, h3, h4, h5, h6, label, .font-sans, .font-display {
            font-family: 'Poppins', sans-serif;
        }
        html, body {
            font-size: 13px;
        }
        body, button:not([class*="fa"]), input, select, textarea, .font-sans, .font-display {
            font-family: 'Poppins', sans-serif;
        }
        
        /* -------------------------------------------------------------
         * ATURAN GLOBAL: SEMUA UKURAN FONT MINIMAL 13PX
         * ------------------------------------------------------------- */
        .text-xs,
        .text-\[8px\],
        .text-\[9px\],
        .text-\[10px\],
        .text-\[11px\],
        .text-\[12px\],
        .text-\[0\.68rem\],
        .text-\[0\.7rem\],
        .text-\[0\.72rem\],
        .text-\[0\.75rem\],
        .text-\[0\.8rem\],
        .text-\[0\.82rem\],
        .text-\[0\.84rem\],
        small,
        .small {
            font-size: 13px !important;
            line-height: 1.4 !important;
        }

        /* Form Controls, Buttons, Labels, and Table Cells minimal 13px */
        input, select, textarea, button, .btn, label, table th, table td {
            font-size: 13px !important;
        }
        .font-mono, code, pre, [class*="font-mono"] {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
        }
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(0, 39, 77, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(224, 242, 254, 0.12);
            padding: 20px !important; /* Standar Resmi design_system.md: 15-20px */
            border-radius: 16px;
        }
        html.light .glass-panel {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(0, 43, 91, 0.05) !important;
            color: #0f172a !important;
            padding: 20px !important;
        }
        html.dark .glass-panel {
            background: #00274d !important;
            border: 1px solid #003661 !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 15, 35, 0.4) !important;
            padding: 20px !important;
        }
        .glow-effect {
            box-shadow: 0 0 25px -5px rgba(0, 75, 135, 0.35);
        }
        .modal-backdrop-blur,
        html.light .modal-backdrop-blur,
        html.dark .modal-backdrop-blur {
            background-color: rgba(0, 27, 54, 0.5) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
        }

        /* -------------------------------------------------------------
         * DESIGN SYSTEM SPECIFICATIONS: EduMaster Gift Learn
         * ------------------------------------------------------------- */
        .card-body-content {
            padding: 20px !important; /* Minimal 15-20px dari tepian border */
        }

        .btn-sim-primary {
            background-color: #004b87 !important;
            border-color: #004b87 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 8px 18px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important; /* Standar Jarak Ikon & Teks: 10px - 14px */
            transition: all 0.2s ease;
        }
        .btn-sim-primary:hover {
            background-color: #003661 !important;
            border-color: #003661 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
        }

        .btn-back-giftlearn {
            background-color: #ffc107 !important;
            color: #0f172a !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-radius: 8px !important;
            font-size: 0.82rem !important;
            font-weight: 700 !important;
            height: 36px !important;
            padding: 0 16px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 10px !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            transition: all 0.2s ease !important;
        }
        .btn-back-giftlearn:hover {
            background-color: #eab308 !important;
            color: #000000 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25) !important;
        }

        /* -------------------------------------------------------------
         * HIGH-CONTRAST LIGHT MODE DESIGN SYSTEM (html.light)
         * Matching gifanya-adhiyaksa-jaya (EduMaster Superadmin Theme)
         * ------------------------------------------------------------- */
        html.light body {
            background-color: #f4f7fb !important;
            color: #0f172a !important;
        }

        /* Top Header: Solid Navy Blue Bar matching gifanya-adhiyaksa-jaya Superadmin */
        header,
        html.light header {
            background-color: #004b87 !important;
            border-bottom: 2px solid #003661 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 12px rgba(0, 43, 91, 0.15) !important;
        }

        html.light header h1,
        html.light header h1 span,
        html.light header .text-white,
        html.light header span.text-white,
        html.light header div.text-white,
        html.light header p.text-white,
        html.light header i.text-white,
        html.light header button .text-white {
            color: #ffffff !important;
        }

        html.light header .text-\[\#bfdbfe\],
        html.light header span.text-\[\#bfdbfe\],
        html.light header div.text-\[\#bfdbfe\] {
            color: #bfdbfe !important;
        }

        html.light header .text-slate-600,
        html.light header .text-slate-500,
        html.light header .text-slate-400 {
            color: #bfdbfe !important;
        }

        html.light header button {
            color: #ffffff !important;
        }

        /* Sidebar: Deep Navy Blue Gradient matching gifanya-adhiyaksa-jaya */
        aside,
        html.light aside,
        .mobile-sidebar-drawer,
        html.light .mobile-sidebar-drawer {
            background: linear-gradient(180deg, #003a6c 0%, #00274d 100%) !important;
            border-right: 1px solid #00274d !important;
            box-shadow: 4px 0 20px rgba(0, 43, 91, 0.12) !important;
        }

        /* Protect all white/light text and icons inside sidebar & drawer in light mode */
        aside .text-white,
        html.light aside .text-white,
        aside div.text-white,
        html.light aside div.text-white,
        aside span.text-white,
        html.light aside span.text-white,
        aside a.text-white,
        html.light aside a.text-white,
        aside i.text-white,
        html.light aside i.text-white,
        .mobile-sidebar-drawer .text-white,
        html.light .mobile-sidebar-drawer .text-white,
        .mobile-sidebar-drawer div.text-white,
        html.light .mobile-sidebar-drawer div.text-white,
        .mobile-sidebar-drawer span.text-white,
        html.light .mobile-sidebar-drawer span.text-white,
        .mobile-sidebar-drawer a.text-white,
        html.light .mobile-sidebar-drawer a.text-white,
        .mobile-sidebar-drawer i.text-white,
        html.light .mobile-sidebar-drawer i.text-white {
            color: #ffffff !important;
        }

        aside .text-\[\#dbeafe\],
        html.light aside .text-\[\#dbeafe\],
        .mobile-sidebar-drawer .text-\[\#dbeafe\],
        html.light .mobile-sidebar-drawer .text-\[\#dbeafe\] {
            color: #dbeafe !important;
        }

        aside .text-\[\#93c5fd\],
        html.light aside .text-\[\#93c5fd\],
        .mobile-sidebar-drawer .text-\[\#93c5fd\],
        html.light .mobile-sidebar-drawer .text-\[\#93c5fd\] {
            color: #93c5fd !important;
        }

        aside .text-\[\#f87171\],
        html.light aside .text-\[\#f87171\],
        .mobile-sidebar-drawer .text-\[\#f87171\],
        html.light .mobile-sidebar-drawer .text-\[\#f87171\] {
            color: #f87171 !important;
        }

        /* Stat Cards & Icon Box matching gifanya-adhiyaksa-jaya */
        .stat-card,
        html.light .stat-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 16px !important;
            padding: 20px !important;
            box-shadow: 0 2px 8px rgba(0, 43, 91, 0.04) !important;
            transition: transform 0.25s ease, box-shadow 0.25s ease !important;
        }
        .stat-card:hover,
        html.light .stat-card:hover {
            border-color: #93c5fd !important;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.12) !important;
            transform: translateY(-3px) !important;
        }
        .icon-box {
            width: 52px !important;
            height: 52px !important;
            border-radius: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.35rem !important;
            flex-shrink: 0 !important;
        }

        /* Mobile Bottom Navigation Bar (Clean Light Theme) */
        nav.fixed.bottom-0,
        html.light nav.fixed.bottom-0 {
            background: rgba(255, 255, 255, 0.95) !important;
            border-top: 1px solid #e2e8f0 !important;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05) !important;
        }
        html.dark nav.fixed.bottom-0 {
            background: rgba(0, 39, 77, 0.95) !important;
            border-top: 1px solid #003661 !important;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3) !important;
        }

        /* Content Headings Contrast in Light Mode */
        html.light h1:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *),
        html.light h2:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *),
        html.light h3:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *),
        html.light h4:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *),
        html.light h5:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *),
        html.light h6:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *) {
            color: #0f172a !important;
        }

        html.light .text-slate-300:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *),
        html.light .text-slate-400:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *) {
            color: #334155 !important;
        }

        html.light .text-slate-500:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *) {
            color: #475569 !important;
        }

        /* Ensure table data in Light Mode has proper contrast */
        html.light table tbody td.text-slate-900,
        html.light table tbody td .text-slate-900,
        html.light .table-cell-title {
            color: #0f172a !important;
        }

        /* -------------------------------------------------------------------------
         * WHITE TEXT ENFORCEMENT ON COLORED BUTTONS & TABS (NEVER OVERRIDDEN)
         * ------------------------------------------------------------------------- */
        .text-white,
        header .text-white,
        aside .text-white,
        .btn-cyan, .btn-cyan *,
        .btn-amber, .btn-amber *,
        .btn-danger, .btn-danger *,
        .btn-green, .btn-green *,
        .btn-blue, .btn-blue *,
        .btn-royal, .btn-royal *,
        .search-btn-reset, .search-btn-reset *,
        .search-btn-submit, .search-btn-submit *,
        .desktop-tab-btn.tab-active, .desktop-tab-btn.tab-active *,
        .desktop-tab-btn.tab-active i, .desktop-tab-btn.tab-active span {
            color: #ffffff !important;
        }

        /* -------------------------------------------------------------------------
         * EXACT BUTTON COLORS MATCHING USER SPECIFICATIONS (CYAN, AMBER, GREEN, ROYAL)
         * ------------------------------------------------------------------------- */

        /* 1. Tombol Biru Langit / Cyan (#00c0ef - Kembali ke Daftar / Info / Scan Kamera) */
        .btn-cyan,
        .btn-sim-cyan,
        .btn-info,
        button.bg-sky-500, button.bg-sky-600, a.bg-sky-500, a.bg-sky-600,
        button.bg-cyan-500, button.bg-cyan-600, a.bg-cyan-500, a.bg-cyan-600,
        button.bg-sky-600\/80, a.bg-sky-600\/80,
        html.light button.bg-sky-500, html.light button.bg-sky-600, html.light a.bg-sky-500, html.light a.bg-sky-600,
        html.light button.bg-cyan-500, html.light button.bg-cyan-600, html.light a.bg-cyan-500, html.light a.bg-cyan-600,
        html.light button.bg-sky-600\/80, html.light a.bg-sky-600\/80 {
            background-color: #00c0ef !important;
            border: 1px solid #00acd6 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(0, 192, 239, 0.2) !important;
        }
        .btn-cyan:hover,
        .btn-sim-cyan:hover,
        .btn-info:hover,
        button.bg-sky-500:hover, button.bg-sky-600:hover, a.bg-sky-500:hover, a.bg-sky-600:hover,
        button.bg-cyan-500:hover, button.bg-cyan-600:hover, a.bg-cyan-500:hover, a.bg-cyan-600:hover,
        button.bg-sky-600\/80:hover, a.bg-sky-600\/80:hover,
        html.light button.bg-sky-500:hover, html.light button.bg-sky-600:hover, html.light a.bg-sky-500:hover, html.light a.bg-sky-600:hover,
        html.light button.bg-cyan-500:hover, html.light button.bg-cyan-600:hover, html.light a.bg-cyan-500:hover, html.light a.bg-cyan-600:hover {
            background-color: #00acd6 !important;
            border-color: #0099bf !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(0, 192, 239, 0.35) !important;
        }

        /* 2. Tombol Kuning Oranye / Amber (#f39c12 - Edit Massal / Aksi / Warning) */
        .btn-amber,
        .btn-orange,
        .btn-sim-amber,
        .btn-warning,
        button.bg-amber-500, button.bg-amber-600, a.bg-amber-500, a.bg-amber-600,
        button.bg-orange-500, button.bg-orange-600, a.bg-orange-500, a.bg-orange-600,
        button.bg-amber-600\/80, a.bg-amber-600\/80,
        html.light button.bg-amber-500, html.light button.bg-amber-600, html.light a.bg-amber-500, html.light a.bg-amber-600,
        html.light button.bg-orange-500, html.light button.bg-orange-600, html.light a.bg-orange-500, html.light a.bg-orange-600,
        html.light button.bg-amber-600\/80, html.light a.bg-amber-600\/80 {
            background-color: #f39c12 !important;
            border: 1px solid #e08e0b !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(243, 156, 18, 0.2) !important;
        }
        .btn-amber:hover,
        .btn-orange:hover,
        .btn-sim-amber:hover,
        .btn-warning:hover,
        button.bg-amber-500:hover, button.bg-amber-600:hover, a.bg-amber-500:hover, a.bg-amber-600:hover,
        button.bg-orange-500:hover, button.bg-orange-600:hover, a.bg-orange-500:hover, a.bg-orange-600:hover,
        button.bg-amber-600\/80:hover, a.bg-amber-600\/80:hover,
        html.light button.bg-amber-500:hover, html.light button.bg-amber-600:hover, html.light a.bg-amber-500:hover, html.light a.bg-amber-600:hover,
        html.light button.bg-orange-500:hover, html.light button.bg-orange-600:hover, html.light a.bg-orange-500:hover, html.light a.bg-orange-600:hover {
            background-color: #d68910 !important;
            border-color: #b9770e !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(243, 156, 18, 0.35) !important;
        }

        /* 3. Tombol Hijau / Emerald (#59c594 - Upload Excel / Tambah / Simpan / Sukses) */
        .btn-emerald,
        .btn-green,
        .btn-sim-green,
        .btn-success,
        button.bg-emerald-500, button.bg-emerald-600, a.bg-emerald-500, a.bg-emerald-600,
        button.bg-green-500, button.bg-green-600, a.bg-green-500, a.bg-green-600,
        button.bg-emerald-600\/80, a.bg-emerald-600\/80,
        html.light button.bg-emerald-500, html.light button.bg-emerald-600, html.light a.bg-emerald-500, html.light a.bg-emerald-600,
        html.light button.bg-green-500, html.light button.bg-green-600, html.light a.bg-green-500, html.light a.bg-green-600,
        html.light button.bg-emerald-600\/80, html.light a.bg-emerald-600\/80 {
            background-color: #59c594 !important;
            border: 1px solid #48b383 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(89, 197, 148, 0.2) !important;
        }
        .btn-emerald:hover,
        .btn-green:hover,
        .btn-sim-green:hover,
        .btn-success:hover,
        button.bg-emerald-500:hover, button.bg-emerald-600:hover, a.bg-emerald-500:hover, a.bg-emerald-600:hover,
        button.bg-green-500:hover, button.bg-green-600:hover, a.bg-green-500:hover, a.bg-green-600:hover,
        button.bg-emerald-600\/80:hover, a.bg-emerald-600\/80:hover,
        html.light button.bg-emerald-500:hover, html.light button.bg-emerald-600:hover, html.light a.bg-emerald-500:hover, html.light a.bg-emerald-600:hover,
        html.light button.bg-green-500:hover, html.light button.bg-green-600:hover, html.light a.bg-green-500:hover, html.light a.bg-green-600:hover {
            background-color: #48b383 !important;
            border-color: #3aa073 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(89, 197, 148, 0.35) !important;
        }

        /* 4. Tombol Biru Royal / Indigo (#3850c9 - Panduan Penilaian / Primary CTA / Filter) */
        .btn-royal,
        .btn-blue,
        .btn-sim-blue,
        .btn-primary,
        button.bg-indigo-500, button.bg-indigo-600, button.bg-indigo-700,
        a.bg-indigo-500, a.bg-indigo-600:not(aside a):not(.mobile-sidebar-drawer a), a.bg-indigo-700:not(aside a):not(.mobile-sidebar-drawer a),
        button.bg-blue-600, a.bg-blue-600,
        button.bg-indigo-600\/80, a.bg-indigo-600\/80,
        html.light button.bg-indigo-500, html.light button.bg-indigo-600, html.light button.bg-indigo-700,
        html.light a.bg-indigo-500, html.light a.bg-indigo-600:not(aside a):not(.mobile-sidebar-drawer a), html.light a.bg-indigo-700:not(aside a):not(.mobile-sidebar-drawer a),
        html.light button.bg-blue-600, html.light a.bg-blue-600,
        html.light button.bg-indigo-600\/80, html.light a.bg-indigo-600\/80 {
            background-color: #3850c9 !important;
            border: 1px solid #2c3fa8 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(56, 80, 201, 0.2) !important;
        }
        .btn-royal:hover,
        .btn-blue:hover,
        .btn-sim-blue:hover,
        .btn-primary:hover,
        button.bg-indigo-500:hover, button.bg-indigo-600:hover, button.bg-indigo-700:hover,
        a.bg-indigo-500:hover, a.bg-indigo-600:hover:not(aside a):not(.mobile-sidebar-drawer a), a.bg-indigo-700:hover:not(aside a):not(.mobile-sidebar-drawer a),
        button.bg-blue-600:hover, a.bg-blue-600:hover,
        button.bg-indigo-600\/80:hover, a.bg-indigo-600\/80:hover,
        html.light button.bg-indigo-500:hover, html.light button.bg-indigo-600:hover, html.light button.bg-indigo-700:hover,
        html.light a.bg-indigo-500:hover, html.light a.bg-indigo-600:hover:not(aside a):not(.mobile-sidebar-drawer a), html.light a.bg-indigo-700:hover:not(aside a):not(.mobile-sidebar-drawer a),
        html.light button.bg-blue-600:hover, html.light a.bg-blue-600:hover {
            background-color: #2c3fa8 !important;
            border-color: #233389 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(56, 80, 201, 0.35) !important;
        }

        /* 5. Tombol Merah / Crimson Danger (#dd4b39 - Hapus / Delete / Batal) */
        .btn-danger,
        .btn-red,
        button.bg-rose-500, button.bg-rose-600, a.bg-rose-500, a.bg-rose-600,
        button.bg-red-500, button.bg-red-600, a.bg-red-500, a.bg-red-600,
        button.bg-rose-600\/80, a.bg-rose-600\/80,
        html.light button.bg-rose-500, html.light button.bg-rose-600, html.light a.bg-rose-500, html.light a.bg-rose-600,
        html.light button.bg-red-500, html.light button.bg-red-600, html.light a.bg-red-500, html.light a.bg-red-600,
        html.light button.bg-rose-600\/80, html.light a.bg-rose-600\/80 {
            background-color: #dd4b39 !important;
            border: 1px solid #c0392b !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(221, 75, 57, 0.2) !important;
        }
        .btn-danger:hover,
        .btn-red:hover,
        button.bg-rose-500:hover, button.bg-rose-600:hover, a.bg-rose-500:hover, a.bg-rose-600:hover,
        button.bg-red-500:hover, button.bg-red-600:hover, a.bg-red-500:hover, a.bg-red-600:hover,
        button.bg-rose-600\/80:hover, a.bg-rose-600\/80:hover,
        html.light button.bg-rose-500:hover, html.light button.bg-rose-600:hover, html.light a.bg-rose-500:hover, html.light a.bg-rose-600:hover,
        html.light button.bg-red-500:hover, html.light button.bg-red-600:hover, html.light a.bg-red-500:hover, html.light a.bg-red-600:hover {
            background-color: #c0392b !important;
            border-color: #a83224 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(221, 75, 57, 0.35) !important;
        }

        /* 6. Tombol Ungu / Purple (#8e44ad - Tracking) */
        button.bg-purple-600, a.bg-purple-600,
        button.bg-purple-600\/80, a.bg-purple-600\/80,
        html.light button.bg-purple-600, html.light a.bg-purple-600,
        html.light button.bg-purple-600\/80, html.light a.bg-purple-600\/80 {
            background-color: #8e44ad !important;
            border: 1px solid #732d91 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
        }
        button.bg-purple-600:hover, a.bg-purple-600:hover,
        button.bg-purple-600\/80:hover, a.bg-purple-600\/80:hover,
        html.light button.bg-purple-600:hover, html.light a.bg-purple-600:hover {
            background-color: #732d91 !important;
            border-color: #5d2475 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
        }

        /* Ensure all text and icons inside these buttons are crisp WHITE */
        .btn-cyan *, .btn-sim-cyan *, .btn-info *,
        .btn-amber *, .btn-orange *, .btn-sim-amber *, .btn-warning *,
        .btn-emerald *, .btn-green *, .btn-sim-green *, .btn-success *,
        .btn-royal *, .btn-blue *, .btn-sim-blue *, .btn-sim-primary *, .btn-primary *,
        .btn-danger *, .btn-red *,
        button.bg-sky-500 *, button.bg-sky-600 *, a.bg-sky-500 *, a.bg-sky-600 *,
        button.bg-cyan-500 *, button.bg-cyan-600 *, a.bg-cyan-500 *, a.bg-cyan-600 *,
        button.bg-amber-500 *, button.bg-amber-600 *, a.bg-amber-500 *, a.bg-amber-600 *,
        button.bg-emerald-500 *, button.bg-emerald-600 *, a.bg-emerald-500 *, a.bg-emerald-600 *,
        button.bg-indigo-500 *, button.bg-indigo-600 *, button.bg-indigo-700 *,
        a.bg-indigo-500 *, a.bg-indigo-600:not(aside a):not(.mobile-sidebar-drawer a) *, a.bg-indigo-700:not(aside a):not(.mobile-sidebar-drawer a) *,
        button.bg-blue-600 *, a.bg-blue-600 *,
        button.bg-rose-500 *, button.bg-rose-600 *, a.bg-rose-500 *, a.bg-rose-600 *,
        button.bg-purple-600 *, a.bg-purple-600 * {
            color: #ffffff !important;
        }

        /* Exception: Badges or inner elements with white background inside active buttons */
        .bg-indigo-600 .bg-white,
        .bg-indigo-600 .bg-white *,
        html.light .bg-indigo-600 .bg-white,
        html.light .bg-indigo-600 .bg-white * {
            color: #3850c9 !important;
        }

        /* Stat numbers in cards */
        html.light .text-3xl:not(.text-white):not([class*="text-white"]),
        html.light .text-2xl:not(.text-white):not([class*="text-white"]),
        html.light .font-extrabold:not(header *):not(aside *):not(.mobile-sidebar-drawer *):not(.btn *):not(.badge *):not(button *):not([style*="#004b87"] *):not(.outbound-main-container *):not(.inbound-main-container *):not(.text-white):not([class*="text-white"]) {
            color: #0f172a !important;
        }

        /* Table Headers & Dividers */
        html.light table thead,
        html.light .bg-slate-800\/90,
        html.light .bg-slate-800\/80 {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border-bottom: 2px solid #cbd5e1 !important;
        }
        html.light table thead th {
            color: #475569 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 12px 14px !important;
        }
        html.light table tbody td {
            color: #1e293b !important;
            font-size: 13px !important;
            padding: 12px 14px !important;
        }

        html.light table tbody tr {
            border-bottom-color: #e2e8f0 !important;
        }

        html.light table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        html.light .divide-slate-800 > :not([hidden]) ~ :not([hidden]),
        html.light .border-slate-800,
        html.light .border-slate-700,
        html.light .border-slate-700\/70,
        html.light .border-slate-700\/60,
        html.light .border-slate-700\/50,
        html.light .border-slate-700\/40 {
            border-color: #e2e8f0 !important;
        }

        /* -------------------------------------------------------------------------
         * GLOBAL SEARCH INPUT GROUP COMPONENT (Exact Match to DevTools Screenshot)
         * button.btn.btn-sm.btn-primary: 37.16 x 36 | #3850C9 | margin: 0 0 0 -1px | padding: 8px 12px
         * ------------------------------------------------------------------------- */
        .search-input-group {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            height: 36px !important;
            max-height: 36px !important;
            position: relative !important;
            box-sizing: border-box !important;
        }
        .search-btn-reset,
        .search-input-group button.btn-warning,
        .search-input-group a.btn-warning {
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
            text-decoration: none !important;
            margin: 0 !important;
            z-index: 1;
        }
        .search-btn-reset:hover,
        .search-input-group button.btn-warning:hover,
        .search-input-group a.btn-warning:hover {
            background-color: #e08e0b !important;
            border-color: #e08e0b !important;
            color: #ffffff !important;
        }
        .search-input-field,
        .search-input-group input.form-control {
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
            z-index: 2;
        }
        html.dark .search-input-field,
        html.dark .search-input-group input.form-control {
            background-color: #001e38 !important;
            border-color: #003661 !important;
            color: #ffffff !important;
        }
        .search-input-field:focus,
        .search-input-group input.form-control:focus {
            outline: none !important;
            border-color: #3850c9 !important;
            box-shadow: 0 0 0 1px #3850c9 !important;
            z-index: 3;
        }
        .search-btn-submit,
        .search-input-group button.btn-primary,
        button.btn.btn-sm.btn-primary.search-btn-submit {
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
        .search-btn-submit:hover,
        .search-input-group button.btn-primary:hover,
        button.btn.btn-sm.btn-primary.search-btn-submit:hover {
            background-color: #2b3ea6 !important;
            border-color: #2b3ea6 !important;
            color: #ffffff !important;
        }

        /* CRITICAL: Protect Font Awesome Icons from font-family and font-weight overrides */
        .fa-solid, .fas, i.fa-solid, i.fas,
        [class*="fa-solid"], [class*="fas"],
        button i.fa-solid, a i.fa-solid, h1 i.fa-solid, h2 i.fa-solid, h3 i.fa-solid, h4 i.fa-solid, div i.fa-solid, span i.fa-solid,
        button i.fas, a i.fas {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1 !important;
            display: inline-block !important;
        }
        .fa-regular, .far, i.fa-regular, i.far,
        [class*="fa-regular"], [class*="far"],
        button i.fa-regular, a i.fa-regular, h1 i.fa-regular, h2 i.fa-regular, h3 i.fa-regular, h4 i.fa-regular, div i.fa-regular, span i.fa-regular {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1 !important;
            display: inline-block !important;
        }
        .fa-brands, .fab, i.fa-brands, i.fab,
        [class*="fa-brands"], [class*="fab"],
        button i.fa-brands, a i.fa-brands, h1 i.fa-brands, h2 i.fa-brands, h3 i.fa-brands, h4 i.fa-brands, div i.fa-brands, span i.fa-brands {
            font-family: "Font Awesome 6 Brands" !important;
            font-weight: 400 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1 !important;
            display: inline-block !important;
        }
        .fa-solid::before, .fas::before,
        .fa-regular::before, .far::before,
        .fa-brands::before, .fab::before,
        [class*="fa-"]::before {
            font-family: inherit !important;
            font-weight: inherit !important;
            font-style: inherit !important;
        }

        /* Input Forms & Controls */
        html.light input:not(.search-input-field),
        html.light select,
        html.light textarea {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            border-radius: 8px !important;
        }

        html.light input:focus,
        html.light select:focus,
        html.light textarea:focus {
            border-color: #004b87 !important;
            box-shadow: 0 0 0 3px rgba(0, 75, 135, 0.15) !important;
        }

        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #64748b !important;
        }

        /* Banners & Alert Panels */
        html.light .from-slate-800.to-indigo-950,
        html.light .from-slate-900.to-indigo-950\/80 {
            background: linear-gradient(to right, #e0f2fe, #f8fafc) !important;
            border: 1px solid #bae6fd !important;
            color: #00274d !important;
        }

        html.light .from-amber-950\/60.to-slate-900 {
            background: linear-gradient(to right, #fffbeb, #fef3c7) !important;
            border: 1px solid #fde68a !important;
            color: #78350f !important;
        }

        /* Badges & Alert Pills */
        html.light .bg-emerald-950,
        html.light .bg-emerald-500\/20 {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #a7f3d0 !important;
        }

        html.light .bg-amber-950,
        html.light .bg-amber-500\/20 {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fde68a !important;
        }

        html.light .bg-rose-950,
        html.light .bg-rose-500\/20 {
            background-color: #ffe4e6 !important;
            color: #9f1239 !important;
            border: 1px solid #fecdd3 !important;
        }

        html.light .bg-purple-950,
        html.light .bg-purple-500\/20 {
            background-color: #f3e8ff !important;
            color: #6b21a8 !important;
            border: 1px solid #e9d5ff !important;
        }

        html.light .bg-sky-950,
        html.light .bg-sky-500\/20 {
            background-color: #e0f2fe !important;
            color: #075985 !important;
            border: 1px solid #bae6fd !important;
        }

        /* Sidebar active & hover in light mode (Sidebar stays prestigious deep navy) */
        html.light aside a.bg-indigo-600 {
            background-color: #004b87 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 75, 135, 0.4) !important;
        }
        html.light aside a.text-slate-400 {
            color: #bae6fd !important;
        }
        html.light aside a.text-slate-400:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }

        /* Pagination in Light Mode */
        html.light nav[role="navigation"] a {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }
        html.light nav[role="navigation"] a:hover {
            background-color: #f0f7ff !important;
            color: #004b87 !important;
            border-color: #bae6fd !important;
        }
        html.light nav[role="navigation"] span.bg-indigo-600 {
            background-color: #004b87 !important;
            color: #ffffff !important;
            border-color: #003661 !important;
        }
        html.light nav[role="navigation"] span.text-slate-500,
        html.light nav[role="navigation"] span.text-slate-600 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
        }

        /* Welcome Banner Light Mode Overrides */
        html.light .from-indigo-900\/50 {
            background: linear-gradient(135deg, #e0f2fe 0%, #ffffff 50%, #f0f7ff 100%) !important;
            border: 1px solid #bae6fd !important;
            box-shadow: 0 4px 15px -3px rgba(0, 75, 135, 0.1) !important;
        }
        html.light .from-indigo-900\/50 h2 {
            color: #00274d !important;
        }
        html.light .from-indigo-900\/50 p {
            color: #334155 !important;
        }
        html.light .from-indigo-900\/50 p .text-indigo-400 {
            color: #004b87 !important;
        }
        html.light .from-indigo-900\/50 a.bg-indigo-600 {
            background-color: #004b87 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 75, 135, 0.3) !important;
        }
        html.light .from-indigo-900\/50 a.bg-indigo-600 * {
            color: #ffffff !important;
        }

        /* VIBRANT & HIGH-CONTRAST ALERT & WARNING BOXES (LIGHT MODE) */
        /* Critical BIN Capacity Warning Box Outer Container */
        html.light .bg-amber-950\/60 {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
            border: 2px solid #f59e0b !important;
            color: #78350f !important;
            box-shadow: 0 6px 20px -4px rgba(245, 158, 11, 0.25) !important;
        }
        html.light .bg-amber-950\/60 h3,
        html.light .bg-amber-950\/60 h3 span {
            color: #78350f !important;
            font-weight: 800 !important;
        }
        html.light .bg-amber-950\/60 h3 i {
            color: #d97706 !important;
        }
        /* Pill Badge inside Warning Header ("1 BIN Penuh") */
        html.light .bg-amber-950\/60 .bg-amber-900\/80 {
            background-color: #d97706 !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 800 !important;
        }
        /* Inner BIN Item Cards inside Warning Container */
        html.light .bg-amber-950\/60 .bg-slate-900\/80 {
            background-color: #fffbeb !important;
            border: 1px solid #fcd34d !important;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.12) !important;
        }
        html.light .bg-amber-950\/60 .bg-slate-900\/80 .font-bold.text-white {
            color: #78350f !important;
            font-weight: 800 !important;
            font-size: 0.95rem !important;
        }
        html.light .bg-amber-950\/60 .bg-slate-900\/80 .text-slate-400 {
            color: #92400e !important;
            font-weight: 600 !important;
        }
        /* FULL Status Badge inside BIN Card ("2 / 2 Palet (FULL)") */
        html.light .bg-amber-950\/60 .bg-slate-900\/80 .bg-amber-500\/20 {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border: none !important;
            font-weight: 800 !important;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3) !important;
        }

        /* Flash Alerts in Light Mode */
        html.light .bg-emerald-950\/80 {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%) !important;
            border: 2px solid #10b981 !important;
            color: #064e3b !important;
        }
        html.light .bg-emerald-950\/80 * {
            color: #064e3b !important;
        }

        html.light .bg-rose-950\/80 {
            background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%) !important;
            border: 2px solid #f43f5e !important;
            color: #881337 !important;
        }
        html.light .bg-rose-950\/80 * {
            color: #881337 !important;
        }

        html.light .bg-amber-950\/80 {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
            border: 2px solid #f59e0b !important;
            color: #78350f !important;
        }
        html.light .bg-amber-950\/80 * {
            color: #78350f !important;
        }
    </style>
    <!-- Fast Top Loading Progress Bar Engine -->
    <script>
        (function() {
            window.topProgressBar = {
                el: null,
                bar: null,
                timer: null,
                currentWidth: 0,
                init: function() {
                    this.el = document.getElementById('global-progress-bar-container');
                    this.bar = document.getElementById('global-progress-bar');
                },
                start: function() {
                    if (!this.el) this.init();
                    if (!this.el || !this.bar) return;
                    clearInterval(this.timer);
                    this.currentWidth = 15;
                    this.el.style.opacity = '1';
                    this.bar.style.width = '15%';
                    
                    this.timer = setInterval(() => {
                        if (this.currentWidth < 85) {
                            this.currentWidth += (85 - this.currentWidth) * 0.18;
                            this.bar.style.width = Math.round(this.currentWidth) + '%';
                        }
                    }, 120);
                },
                set: function(percent) {
                    if (!this.el) this.init();
                    if (!this.el || !this.bar) return;
                    this.currentWidth = percent;
                    this.bar.style.width = percent + '%';
                },
                finish: function() {
                    if (!this.el) this.init();
                    if (!this.el || !this.bar) return;
                    clearInterval(this.timer);
                    this.bar.style.width = '100%';
                    setTimeout(() => {
                        if (this.el) this.el.style.opacity = '0';
                        setTimeout(() => {
                            if (this.bar) this.bar.style.width = '0%';
                            this.currentWidth = 0;
                        }, 250);
                    }, 180);
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                if (window.topProgressBar) {
                    window.topProgressBar.init();
                    window.topProgressBar.finish();
                }

                // Global link click handler for instant loading feedback
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (link && link.href && !link.target && !link.hasAttribute('download')) {
                        const url = link.href;
                        if (url.startsWith(window.location.origin) && !url.includes('#') && url !== window.location.href) {
                            window.topProgressBar.start();
                        }
                    }
                });

                // Global form submit handler
                document.addEventListener('submit', function() {
                    window.topProgressBar.start();
                });

                window.addEventListener('beforeunload', function() {
                    window.topProgressBar.start();
                });
            });
        })();
    </script>
    @yield('styles')
</head>
<body x-data="{ mobileSidebarOpen: false, quickActionOpen: false }" class="bg-slate-900 text-slate-100 font-sans min-h-screen antialiased transition-colors duration-300">

    <!-- GLOBAL HIGH-PERFORMANCE TOP LOADING PROGRESS BAR -->
    <div id="global-progress-bar-container" class="fixed top-0 left-0 w-full h-[3.5px] z-[999999] pointer-events-none opacity-0 transition-opacity duration-200">
        <div id="global-progress-bar" class="h-full w-0 bg-gradient-to-r from-sky-400 via-[#004b87] to-amber-400 shadow-[0_0_10px_rgba(2,132,199,0.9)] transition-all duration-150 ease-out"></div>
    </div>

    <!-- Impersonation Banner -->
    @include('layouts.impersonate_banner')

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header Navbar -->
            @include('layouts.header')

            <!-- Main Scrollable Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 pb-24 md:pb-8 space-y-6">
                <!-- Flash Alert Messages -->
                @if(session('success'))
                    <div class="p-4 mb-4 text-sm text-emerald-200 bg-emerald-950/80 border border-emerald-500/40 rounded-xl flex items-center shadow-lg transition-all animate-bounce">
                        <i class="fa-solid fa-circle-check text-emerald-400 text-xl mr-3"></i>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 mb-4 text-sm text-rose-200 bg-rose-950/80 border border-rose-500/40 rounded-xl flex items-center shadow-lg">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400 text-xl mr-3"></i>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="p-4 mb-4 text-sm text-amber-200 bg-amber-950/80 border border-amber-500/40 rounded-xl">
                        <div class="flex items-center mb-2">
                            <i class="fa-solid fa-circle-exclamation text-amber-400 text-xl mr-3"></i>
                            <span class="font-bold">Mohon periksa input form:</span>
                        </div>
                        <ul class="list-disc list-inside ml-8 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Fixed Bottom Navigation -->
    @include('layouts.bottom_nav')

    <!-- Global Camera Barcode Scanner Modal -->
    @include('layouts.camera_modal')

    <!-- Theme Toggle & Common Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    document.documentElement.classList.toggle('dark');
                    document.documentElement.classList.toggle('light');
                    const isDark = document.documentElement.classList.contains('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            }
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
