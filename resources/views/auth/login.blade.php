<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WMS Indraco</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <style>
        body {
            transition: background 0.4s ease, color 0.4s ease;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Light Mode Styling (#ECF4E5 & #E1DFEA background) */
        html:not(.dark) body {
            background: radial-gradient(circle at 50% 10%, #ECF4E5 0%, #E1DFEA 100%);
            color: #111812;
        }
        html:not(.dark) .glass-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(17, 24, 18, 0.12);
            box-shadow: 0 20px 40px -15px rgba(17, 24, 18, 0.12);
        }
        html:not(.dark) .text-head-title { color: #0B1215; }
        html:not(.dark) .text-head-sub { color: #334155; }
        html:not(.dark) .card-header-title { color: #111812; }
        html:not(.dark) .form-label { color: #111812; }
        html:not(.dark) .form-input {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: rgba(17, 24, 18, 0.2);
            color: #0B1215;
        }
        html:not(.dark) .form-input::placeholder { color: #64748B; }
        html:not(.dark) .demo-box {
            background-color: rgba(255, 255, 255, 0.6);
            border-color: rgba(17, 24, 18, 0.15);
        }

        /* Dark Mode Styling (#111812 & #0B1215 background) */
        html.dark body {
            background: radial-gradient(circle at 50% 10%, #111812 0%, #0B1215 100%);
            color: #ECF4E5;
        }
        html.dark .glass-card {
            background: rgba(11, 18, 21, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(236, 244, 229, 0.12);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        html.dark .text-head-title { color: #FFFFFF; }
        html.dark .text-head-sub { color: #94A3B8; }
        html.dark .card-header-title { color: #FFFFFF; }
        html.dark .form-label { color: #CBD5E1; }
        html.dark .form-input {
            background-color: rgba(15, 23, 42, 0.8);
            border-color: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
        }
        html.dark .form-input::placeholder { color: #64748B; }
        html.dark .demo-box {
            background-color: rgba(15, 23, 42, 0.6);
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased font-sans relative">

    <!-- Theme-Aware Polygonal Particle Canvas (z-index: -1) -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none" style="z-index: -1;"></canvas>

    <!-- Theme Toggle Button -->
    <div class="fixed top-4 right-4 z-50">
        <button id="theme-toggle" type="button" class="px-3 py-2 rounded-xl bg-slate-800/70 hover:bg-slate-700/80 text-white border border-white/20 backdrop-blur-md transition-all shadow-lg cursor-pointer flex items-center space-x-2 text-xs font-semibold">
            <i id="theme-toggle-dark-icon" class="fa-solid fa-moon text-amber-300"></i>
            <i id="theme-toggle-light-icon" class="fa-solid fa-sun text-amber-400 hidden"></i>
            <span id="theme-toggle-text">Dark Mode</span>
        </button>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-emerald-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-500/20 ring-4 ring-emerald-500/20">
                <i class="fa-solid fa-cubes text-3xl text-white"></i>
            </div>
            <h1 class="font-display font-extrabold text-3xl tracking-tight text-head-title">WMS INDRACO</h1>
            <p class="text-head-sub text-sm mt-1">Warehouse Management System • Production Ready</p>
        </div>

        <!-- Login Card -->
        <div class="glass-card rounded-2xl p-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-6 text-center card-header-title">Masuk ke Akun Anda</h2>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-rose-950/80 border border-rose-500/40 rounded-xl text-rose-200 text-xs flex items-center">
                    <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg mr-2"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="username" class="block text-xs font-semibold uppercase tracking-wider mb-2 form-label">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                            class="w-full pl-10 pr-4 py-3 form-input rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
                            placeholder="Masukkan username...">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider mb-2 form-label">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" required
                            class="w-full pl-10 pr-4 py-3 form-input rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 text-xs form-label cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-slate-800 border-slate-700 text-emerald-600 focus:ring-emerald-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/30 transition transform active:scale-95 flex items-center justify-center space-x-2">
                    <span>Masuk ke Dashboard</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-8 pt-6 border-t border-slate-500/20">
                <div class="text-xs font-semibold mb-2 flex items-center justify-between form-label">
                    <span>Demo Credentials:</span>
                    <span class="text-[10px] text-emerald-500 font-medium">Klik untuk isi otomatis</span>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <button type="button" onclick="setDemo('admin', 'admin123')" class="demo-box p-2 rounded-lg border hover:bg-emerald-950/30 hover:border-emerald-500/50 transition cursor-pointer focus:outline-none group">
                        <div class="font-bold text-emerald-500 group-hover:text-emerald-400">admin</div>
                        <div class="text-[10px] opacity-75">Super Admin</div>
                    </button>
                    <button type="button" onclick="setDemo('budi', 'budi123')" class="demo-box p-2 rounded-lg border hover:bg-amber-950/30 hover:border-amber-500/50 transition cursor-pointer focus:outline-none group">
                        <div class="font-bold text-amber-500 group-hover:text-amber-400">budi</div>
                        <div class="text-[10px] opacity-75">Supervisor</div>
                    </button>
                    <button type="button" onclick="setDemo('gilang', 'gilang123')" class="demo-box p-2 rounded-lg border hover:bg-indigo-950/30 hover:border-indigo-500/50 transition cursor-pointer focus:outline-none group">
                        <div class="font-bold text-indigo-500 group-hover:text-indigo-400">gilang</div>
                        <div class="text-[10px] opacity-75">Operator</div>
                    </button>
                </div>
            </div>
        </div>

        <div class="text-center text-xs opacity-60 mt-6 font-medium">
            &copy; 2026 PT Indraco Global Indonesia. All rights reserved.
        </div>
    </div>

    <!-- Interactive Canvas & Theme Handler Scripts -->
    <script>
        // 1. Theme Switcher Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleText = document.getElementById('theme-toggle-text');

        function syncThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                themeDarkIcon.classList.remove('hidden');
                themeLightIcon.classList.add('hidden');
                themeToggleText.textContent = 'Dark Mode';
            } else {
                themeLightIcon.classList.remove('hidden');
                themeDarkIcon.classList.add('hidden');
                themeToggleText.textContent = 'Light Mode';
            }
        }

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                syncThemeUI();
            });
        }

        // Initialize Theme from localStorage or prefers-color-scheme
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
        syncThemeUI();

        // 2. Polygonal Particle Background Engine
        class PolygonalParticleEngine {
            constructor(canvasId) {
                this.canvas = document.getElementById(canvasId);
                if (!this.canvas) return;
                this.ctx = this.canvas.getContext('2d');
                this.particles = [];
                this.mouse = { x: null, y: null, radius: 170 };
                
                this.init();
                this.bindEvents();
                this.animate();
            }

            getColors() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    // Dark Mode Colors: Light Custom (#ECF4E5 & #E1DFEA)
                    return {
                        node: '236, 244, 229',        // #ECF4E5
                        line: '225, 223, 234',        // #E1DFEA
                        polyFill: '236, 244, 229'
                    };
                } else {
                    // Light Mode Colors: Dark Obsidian & Dark Green (#111812 & #0B1215)
                    return {
                        node: '17, 24, 18',           // #111812
                        line: '11, 18, 21',           // #0B1215
                        polyFill: '17, 24, 18'
                    };
                }
            }

            init() {
                this.resize();
                this.createParticles();
            }

            resize() {
                this.width = this.canvas.width = window.innerWidth;
                this.height = this.canvas.height = window.innerHeight;
            }

            createParticles() {
                this.particles = [];
                // Density proportional to viewport size
                const particleCount = Math.min(Math.floor((this.width * this.height) / 11000), 85);
                for (let i = 0; i < particleCount; i++) {
                    this.particles.push({
                        x: Math.random() * this.width,
                        y: Math.random() * this.height,
                        vx: (Math.random() - 0.5) * 0.75,
                        vy: (Math.random() - 0.5) * 0.75,
                        radius: Math.random() * 1.5 + 1.5
                    });
                }
            }

            bindEvents() {
                window.addEventListener('resize', () => {
                    this.resize();
                    this.createParticles();
                });

                // Desktop Mouse Movement Listener
                window.addEventListener('mousemove', (e) => {
                    this.mouse.x = e.clientX;
                    this.mouse.y = e.clientY;
                });

                window.addEventListener('mouseleave', () => {
                    this.mouse.x = null;
                    this.mouse.y = null;
                });
            }

            animate() {
                this.ctx.clearRect(0, 0, this.width, this.height);
                const colors = this.getColors();
                const connDistance = 135;

                // 1. Update and Render Nodes
                for (let i = 0; i < this.particles.length; i++) {
                    let p = this.particles[i];

                    p.x += p.vx;
                    p.y += p.vy;

                    // Bounce on screen boundaries
                    if (p.x < 0 || p.x > this.width) p.vx *= -1;
                    if (p.y < 0 || p.y > this.height) p.vy *= -1;

                    // Interactive repulsion / attraction force with mouse
                    if (this.mouse.x !== null && this.mouse.y !== null) {
                        let dx = this.mouse.x - p.x;
                        let dy = this.mouse.y - p.y;
                        let dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < this.mouse.radius) {
                            let force = (this.mouse.radius - dist) / this.mouse.radius;
                            p.x -= (dx / dist) * force * 1.5;
                            p.y -= (dy / dist) * force * 1.5;
                        }
                    }

                    // Render node circle
                    this.ctx.beginPath();
                    this.ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    this.ctx.fillStyle = `rgba(${colors.node}, 0.65)`;
                    this.ctx.fill();
                }

                // 2. Render Mesh Lines & Polygonal Triangles
                for (let i = 0; i < this.particles.length; i++) {
                    for (let j = i + 1; j < this.particles.length; j++) {
                        let p1 = this.particles[i];
                        let p2 = this.particles[j];
                        let dx = p1.x - p2.x;
                        let dy = p1.y - p2.y;
                        let dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < connDistance) {
                            let alpha = (1 - dist / connDistance) * 0.32;
                            this.ctx.beginPath();
                            this.ctx.moveTo(p1.x, p1.y);
                            this.ctx.lineTo(p2.x, p2.y);
                            this.ctx.strokeStyle = `rgba(${colors.line}, ${alpha})`;
                            this.ctx.lineWidth = 1;
                            this.ctx.stroke();

                            // Triangular Polygonal Mesh Fill
                            for (let k = j + 1; k < this.particles.length; k++) {
                                let p3 = this.particles[k];
                                let dx2 = p1.x - p3.x;
                                let dy2 = p1.y - p3.y;
                                let dist2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                                let dx3 = p2.x - p3.x;
                                let dy3 = p2.y - p3.y;
                                let dist3 = Math.sqrt(dx3 * dx3 + dy3 * dy3);

                                if (dist2 < connDistance && dist3 < connDistance) {
                                    let polyAlpha = (1 - (dist + dist2 + dist3) / (connDistance * 3)) * 0.07;
                                    this.ctx.beginPath();
                                    this.ctx.moveTo(p1.x, p1.y);
                                    this.ctx.lineTo(p2.x, p2.y);
                                    this.ctx.lineTo(p3.x, p3.y);
                                    this.ctx.closePath();
                                    this.ctx.fillStyle = `rgba(${colors.polyFill}, ${polyAlpha})`;
                                    this.ctx.fill();
                                }
                            }
                        }
                    }

                    // Dynamic Interactive Connection to Mouse Cursor
                    if (this.mouse.x !== null && this.mouse.y !== null) {
                        let p = this.particles[i];
                        let dx = p.x - this.mouse.x;
                        let dy = p.y - this.mouse.y;
                        let dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < this.mouse.radius) {
                            let alpha = (1 - dist / this.mouse.radius) * 0.45;
                            this.ctx.beginPath();
                            this.ctx.moveTo(p.x, p.y);
                            this.ctx.lineTo(this.mouse.x, this.mouse.y);
                            this.ctx.strokeStyle = `rgba(${colors.line}, ${alpha})`;
                            this.ctx.lineWidth = 1.2;
                            this.ctx.stroke();
                        }
                    }
                }

                requestAnimationFrame(() => this.animate());
            }
        }

        // Initialize Canvas Engine on DOM Content Loaded
        document.addEventListener('DOMContentLoaded', () => {
            new PolygonalParticleEngine('particle-canvas');
        });

        // 3. Quick Demo Filler Helper Function
        function setDemo(username, password) {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            if (usernameInput && passwordInput) {
                usernameInput.value = username;
                passwordInput.value = password;
                
                [usernameInput, passwordInput].forEach(el => {
                    el.classList.add('ring-2', 'ring-emerald-500');
                    setTimeout(() => el.classList.remove('ring-2', 'ring-emerald-500'), 500);
                });
            }
        }
    </script>
</body>
</html>

