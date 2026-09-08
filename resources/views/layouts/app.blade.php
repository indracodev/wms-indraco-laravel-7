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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
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
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        darkbg: '#0f172a',
                        darkcard: '#1e293b',
                        darkborder: '#334155'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
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
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glow-effect {
            box-shadow: 0 0 25px -5px rgba(99, 102, 241, 0.3);
        }

        /* -------------------------------------------------------------
         * HIGH-CONTRAST LIGHT MODE DESIGN SYSTEM (html.light)
         * ------------------------------------------------------------- */
        html.light body {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Surface & Panels */
        html.light .glass-panel,
        html.light header,
        html.light aside,
        html.light nav.fixed.bottom-0,
        html.light .bg-slate-900,
        html.light .bg-slate-800,
        html.light .bg-slate-800\/80,
        html.light .bg-slate-800\/60,
        html.light .bg-slate-800\/40,
        html.light .bg-slate-800\/50,
        html.light .bg-slate-800\/90 {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
        }

        html.light header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        html.light aside {
            background-color: #ffffff !important;
            border-right: 1px solid #cbd5e1 !important;
        }

        html.light nav.fixed.bottom-0 {
            background-color: rgba(255, 255, 255, 0.98) !important;
            border-top: 1px solid #cbd5e1 !important;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        /* Text Sharp Contrast Overrides */
        html.light h1, html.light h2, html.light h3, html.light h4, html.light h5, html.light h6,
        html.light .text-slate-100,
        html.light .text-slate-200 {
            color: #0f172a !important;
        }

        html.light .text-slate-300,
        html.light .text-slate-400 {
            color: #334155 !important;
        }

        html.light .text-slate-500 {
            color: #475569 !important;
        }

        /* Ensure elements with blue / indigo / vibrant backgrounds ALWAYS have WHITE text */
        html.light .bg-indigo-600,
        html.light .bg-indigo-700,
        html.light .bg-indigo-500,
        html.light .bg-blue-600,
        html.light .bg-blue-700,
        html.light .bg-brand-600,
        html.light .bg-emerald-600,
        html.light .bg-rose-600,
        html.light button.bg-indigo-600,
        html.light button.bg-indigo-700,
        html.light button.bg-blue-600,
        html.light a.bg-indigo-600,
        html.light a.bg-indigo-700,
        html.light .btn-primary {
            color: #ffffff !important;
        }

        html.light .bg-indigo-600 *,
        html.light .bg-indigo-700 *,
        html.light .bg-indigo-500 *,
        html.light .bg-blue-600 *,
        html.light .bg-blue-700 *,
        html.light .bg-brand-600 *,
        html.light .bg-emerald-600 *,
        html.light .bg-rose-600 * {
            color: #ffffff !important;
        }

        /* Stat numbers in cards */
        html.light .text-3xl,
        html.light .text-2xl,
        html.light .font-extrabold {
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

        /* Input Forms & Controls */
        html.light input,
        html.light select,
        html.light textarea {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #64748b !important;
        }

        /* Banners & Alert Panels */
        html.light .from-slate-800.to-indigo-950,
        html.light .from-slate-900.to-indigo-950\/80 {
            background: linear-gradient(to right, #e0e7ff, #f1f5f9) !important;
            border: 1px solid #c7d2fe !important;
            color: #1e1b4b !important;
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

        /* Bottom Nav Links */
        html.light nav.fixed.bottom-0 a {
            color: #64748b !important;
        }
        html.light nav.fixed.bottom-0 a.text-indigo-400,
        html.light nav.fixed.bottom-0 a.text-emerald-400,
        html.light nav.fixed.bottom-0 a.text-amber-400,
        html.light nav.fixed.bottom-0 a.text-sky-400,
        html.light nav.fixed.bottom-0 a.text-purple-400 {
            color: #4338ca !important;
            background-color: #e0e7ff !important;
        }

        /* Sidebar active & hover in light mode */
        html.light aside a.bg-indigo-600 {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        html.light aside a.text-slate-400 {
            color: #475569 !important;
        }
        html.light aside a.text-slate-400:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        /* Pagination in Light Mode */
        html.light nav[role="navigation"] a {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }
        html.light nav[role="navigation"] a:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }
        html.light nav[role="navigation"] span.bg-indigo-600 {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4338ca !important;
        }
        html.light nav[role="navigation"] span.text-slate-500,
        html.light nav[role="navigation"] span.text-slate-600 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
        }

        /* Welcome Banner Light Mode Overrides */
        html.light .from-indigo-900\/50 {
            background: linear-gradient(135deg, #e0e7ff 0%, #ffffff 50%, #e0e7ff 100%) !important;
            border: 1px solid #c7d2fe !important;
            box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.15) !important;
        }
        html.light .from-indigo-900\/50 h2 {
            color: #1e1b4b !important;
        }
        html.light .from-indigo-900\/50 p {
            color: #334155 !important;
        }
        html.light .from-indigo-900\/50 p .text-indigo-400 {
            color: #4338ca !important;
        }
        html.light .from-indigo-900\/50 a.bg-indigo-600 {
            background-color: #4338ca !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(67, 56, 202, 0.3) !important;
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
    @yield('styles')
</head>
<body x-data="{ mobileSidebarOpen: false }" class="bg-slate-900 text-slate-100 font-sans min-h-screen antialiased transition-colors duration-300">

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

                @if ($errors->any())
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
