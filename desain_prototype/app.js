/* WMS Indraco Core State Engine - Prototype Console */

// Initial Database Mock (Loaded if localStorage is empty)
const INITIAL_DB = {
    offices: [
        { id: 'KTR-SBY', name: 'Kantor Pusat Surabaya', address: 'Jl. Kombes Pol M. Duriyat, Surabaya' },
        { id: 'KTR-JKT', name: 'Kantor Cabang Jakarta', address: 'Jl. Jend. Sudirman Kav 21, Jakarta' }
    ],
    branches: [
        { id: 'CBG-GSK', office_id: 'KTR-SBY', name: 'Cabang Gresik', city: 'Gresik' },
        { id: 'CBG-CPT', office_id: 'KTR-JKT', name: 'Cabang Ciputat', city: 'Tangerang Selatan' }
    ],
    warehouses: [
        { id: 'GSK.678.I', branch_id: 'CBG-GSK', name: 'Gudang Gresik Utama', address: 'Kawasan Industri Gresik Blok A-10' },
        { id: 'CPT.101.II', branch_id: 'CBG-CPT', name: 'Gudang Ciputat Baru', address: 'Jl. Raya Ciputat No. 45' }
    ],
    racks: [
        { id: 'RK-B', warehouse_id: 'GSK.678.I', rack_name: 'B' },
        { id: 'RK-C', warehouse_id: 'GSK.678.I', rack_name: 'C' },
        { id: 'RK-K', warehouse_id: 'GSK.678.I', rack_name: 'K' },
        { id: 'RK-L', warehouse_id: 'GSK.678.I', rack_name: 'L' },
        { id: 'RK-D', warehouse_id: 'GSK.678.I', rack_name: 'D' }
    ],
    bins: [
        { id: 'B.10.2', rack_id: 'RK-B', column_no: 10, level_no: 2, max_pallet: 2 },
        { id: 'B.8.1', rack_id: 'RK-B', column_no: 8, level_no: 1, max_pallet: 2 },
        { id: 'K.1.1', rack_id: 'RK-K', column_no: 1, level_no: 1, max_pallet: 2 },
        { id: 'C.1.1', rack_id: 'RK-C', column_no: 1, level_no: 1, max_pallet: 2 },
        { id: 'L.5.1', rack_id: 'RK-L', column_no: 5, level_no: 1, max_pallet: 2 },
        { id: 'C.20.3', rack_id: 'RK-C', column_no: 20, level_no: 3, max_pallet: 2 },
        { id: 'D.7.3', rack_id: 'RK-D', column_no: 7, level_no: 3, max_pallet: 2 }
    ],
    products: [
        { id: 'PRD-001', name: 'Uang Emas Special 350g', default_unit: 'Karton' },
        { id: 'PRD-002', name: 'Kopi Kapal Api 165g', default_unit: 'Karton' },
        { id: 'PRD-003', name: 'Gula Putih 1Kg', default_unit: 'Bag' }
    ],
    pallets: [
        { id: 'PLT-001', pallet_type: 'Loscam', bin_id: 'B.10.2', is_active: true },
        { id: 'PLT-002', pallet_type: 'Kayu 125x125', bin_id: 'B.8.1', is_active: true },
        { id: 'PLT-003', pallet_type: 'Kayu 135x125', bin_id: 'K.1.1', is_active: true },
        { id: 'PLT-004', pallet_type: 'Plastik', bin_id: 'K.1.1', is_active: true },
        { id: 'PLT-005', pallet_type: 'Loscam', bin_id: 'C.1.1', is_active: true },
        { id: 'PLT-006', pallet_type: 'Loscam', bin_id: 'C.1.1', is_active: true },
        { id: 'PLT-007', pallet_type: 'Kayu 125x125', bin_id: 'L.5.1', is_active: true },
        { id: 'PLT-008', pallet_type: 'Plastik', bin_id: 'C.20.3', is_active: true },
        { id: 'PLT-009', pallet_type: 'Loscam', bin_id: 'D.7.3', is_active: true }
    ],
    boxes: [
        { id: 'BOX-001', pallet_id: 'PLT-001', product_id: 'PRD-001', qty: 100, expired_date: '2028-02-18', machine_code: '1I0PB01' },
        { id: 'BOX-002', pallet_id: 'PLT-002', product_id: 'PRD-002', qty: 50, expired_date: '2027-11-05', machine_code: '2H1ZA09' },
        { id: 'BOX-003', pallet_id: 'PLT-003', product_id: 'PRD-003', qty: 200, expired_date: '2026-09-12', machine_code: '3K2XX04' },
        { id: 'BOX-004', pallet_id: 'PLT-004', product_id: 'PRD-001', qty: 80, expired_date: '2028-02-18', machine_code: '1I0PB01' },
        { id: 'BOX-005', pallet_id: 'PLT-005', product_id: 'PRD-001', qty: 120, expired_date: '2028-03-01', machine_code: '1I0PB02' },
        { id: 'BOX-006', pallet_id: 'PLT-006', product_id: 'PRD-002', qty: 60, expired_date: '2027-12-10', machine_code: '2H1ZA11' },
        { id: 'BOX-007', pallet_id: 'PLT-007', product_id: 'PRD-003', qty: 150, expired_date: '2026-10-20', machine_code: '3K2XX05' },
        { id: 'BOX-008', pallet_id: 'PLT-008', product_id: 'PRD-001', qty: 90, expired_date: '2028-02-18', machine_code: '1I0PB01' },
        { id: 'BOX-009', pallet_id: 'PLT-009', product_id: 'PRD-002', qty: 70, expired_date: '2027-11-25', machine_code: '2H1ZA10' }
    ],
    users: [
        { name: 'Rivaldy (Admin Asli)', username: 'rivaldy', role_id: 'Super Admin', shift: 1, is_active: true, password: 'password' },
        { name: 'Diana (Supervisor)', username: 'diana', role_id: 'Warehouse Supervisor', shift: 1, is_active: true, password: 'password' },
        { name: 'Gilang (Operator)', username: 'gilang', role_id: 'Operator Gudang', shift: 1, is_active: true, password: 'password' }
    ],
    roles: [
        { id: 'Super Admin', description: 'Hak akses penuh untuk mengelola master data, user, log, dan impersonate.' },
        { id: 'Warehouse Supervisor', description: 'Pemantauan persediaan, pelacakan koordinat, ekspor rekap log.' },
        { id: 'Operator Gudang', description: 'Fokus operasional mutasi barang masuk/keluar/pindah dan scanner.' }
    ],
    role_permissions: [
        // Super Admin
        { role_id: 'Super Admin', module: 'dashboard', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'master-data', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'detail-data', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'scanner', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'users', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'rbac', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'logs', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'settings', can_read: true, can_create: true, can_update: true, can_delete: true },
        { role_id: 'Super Admin', module: 'profile', can_read: true, can_create: true, can_update: true, can_delete: true },
        // Warehouse Supervisor
        { role_id: 'Warehouse Supervisor', module: 'dashboard', can_read: true, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'master-data', can_read: true, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'detail-data', can_read: true, can_create: true, can_update: true, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'scanner', can_read: true, can_create: true, can_update: true, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'users', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'rbac', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'logs', can_read: true, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'settings', can_read: true, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Warehouse Supervisor', module: 'profile', can_read: true, can_create: true, can_update: true, can_delete: false },
        // Operator Gudang
        { role_id: 'Operator Gudang', module: 'dashboard', can_read: true, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'master-data', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'detail-data', can_read: true, can_create: true, can_update: true, can_delete: false },
        { role_id: 'Operator Gudang', module: 'scanner', can_read: true, can_create: true, can_update: true, can_delete: false },
        { role_id: 'Operator Gudang', module: 'users', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'rbac', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'logs', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'settings', can_read: false, can_create: false, can_update: false, can_delete: false },
        { role_id: 'Operator Gudang', module: 'profile', can_read: true, can_create: true, can_update: true, can_delete: false }
    ],
    audit_logs: [
        { timestamp: new Date(Date.now() - 3600000 * 2).toISOString(), user_id: 'system', module: 'SYSTEM', action_type: 'CREATE', description: 'Inisialisasi dataset awal prototype WMS Indraco.' },
        { timestamp: new Date(Date.now() - 3600000).toISOString(), user_id: 'rivaldy', module: 'MASTER', action_type: 'CREATE', description: 'Menambahkan data master produk Kopi Kapal Api.' }
    ],
    settings: {
        limitBinPallets: true,
        maxPalletsPerBin: 2,
        expAlertMonths: 18,
        scanBeep: true,
        scanAutoClear: true
    },
    session: {
        isLoggedIn: false,
        currentUser: null,
        originalUser: null,
        isImpersonating: false,
        activeWarehouseId: 'GSK.678.I',
        currentTab: 'dashboard'
    }
};

// State Manager Global
let state = {};

// Load State from Local Storage or Load Initial Database
function loadState() {
    try {
        const localData = localStorage.getItem('WMS_INDRACO_STATE');
        if (localData) {
            state = JSON.parse(localData);
            // Ensure session exists
            if (!state.session) {
                state.session = JSON.parse(JSON.stringify(INITIAL_DB.session));
            }
            // Ensure settings exists
            if (!state.settings) {
                state.settings = JSON.parse(JSON.stringify(INITIAL_DB.settings));
            }
            // Ensure other vital database tables exist
            const tables = ['users', 'roles', 'role_permissions', 'audit_logs', 'offices', 'branches', 'warehouses', 'racks', 'bins', 'products', 'pallets', 'boxes'];
            tables.forEach(t => {
                if (!state[t]) {
                    state[t] = JSON.parse(JSON.stringify(INITIAL_DB[t]));
                }
            });
        } else {
            state = JSON.parse(JSON.stringify(INITIAL_DB));
            saveState();
        }
    } catch (e) {
        console.error("Gagal memuat state dari localStorage, menggunakan state awal:", e);
        state = JSON.parse(JSON.stringify(INITIAL_DB));
        saveState();
    }
}

// Save State to Local Storage
function saveState() {
    localStorage.setItem('WMS_INDRACO_STATE', JSON.stringify(state));
}

// Write Global Audit Log
function writeAuditLog(userId, module, actionType, description) {
    state.audit_logs.unshift({
        timestamp: new Date().toISOString(),
        user_id: userId,
        module: module.toUpperCase(),
        action_type: actionType.toUpperCase(),
        description: description
    });
    saveState();
}

// Check Permission
function checkPermission(moduleName, action) {
    const user = state.users.find(u => u.username === state.session.currentUser);
    if (!user) return false;
    
    // Super admin overrides everything
    if (user.role_id === 'Super Admin') return true;

    const permission = state.role_permissions.find(p => p.role_id === user.role_id && p.module === moduleName);
    if (!permission) return false;

    if (action === 'read') return permission.can_read;
    if (action === 'create') return permission.can_create;
    if (action === 'update') return permission.can_update;
    if (action === 'delete') return permission.can_delete;
    
    return false;
}

// Display Custom Notification (Toast)
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        // Create container dynamically if missing
        const div = document.createElement('div');
        div.id = 'toast-container';
        div.className = 'position-fixed top-0 end-0 p-3';
        div.style.zIndex = '3000';
        document.body.appendChild(div);
    }

    const id = 'toast-' + Date.now();
    const borderClass = type === 'success' ? 'border-success' : type === 'warning' ? 'border-warning' : 'border-danger';
    const bgHeader = type === 'success' ? 'bg-emerald-950 text-emerald-400' : type === 'warning' ? 'bg-amber-950 text-amber-400' : 'bg-rose-950 text-rose-400';
    const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-dash-circle-fill';
    
    const toastHtml = `
        <div id="${id}" class="toast align-items-center bg-darkblue-800 border ${borderClass} text-white mb-2 shadow-2xl" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgHeader} border-b border-white/5 py-1 px-3 flex justify-between">
                <strong class="me-auto text-xxs uppercase tracking-wider flex items-center gap-1.5"><i class="bi ${icon}"></i> WMS Notifikasi</strong>
                <button type="button" class="btn-close btn-close-white text-xs" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-xs p-3 font-medium">
                ${message}
            </div>
        </div>
    `;
    
    document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(id);
    const toastObj = new bootstrap.Toast(toastEl, { delay: 4000 });
    toastObj.show();
    
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// format date Indonesian
function formatIndoDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ----------------------------------------------------
// AUTHENTICATION & LOGIN LOGIC
// ----------------------------------------------------
function handleLogin(e) {
    if (e) e.preventDefault();
    const usernameInput = document.getElementById('login-username').value.trim().toLowerCase();
    const passwordInput = document.getElementById('login-password').value;

    const user = state.users.find(u => u.username === usernameInput);
    if (!user || user.password !== passwordInput) {
        showToast('Username atau password salah!', 'danger');
        return;
    }

    if (!user.is_active) {
        showToast('Akun Anda telah dinonaktifkan oleh administrator!', 'danger');
        return;
    }

    state.session.isLoggedIn = true;
    state.session.currentUser = user.username;
    state.session.originalUser = user.username;
    state.session.isImpersonating = false;
    state.session.currentTab = 'dashboard';
    
    writeAuditLog(user.username, 'SYSTEM', 'LOGIN', `Pengguna ${user.name} berhasil login ke dalam sistem.`);
    saveState();
    window.location.replace('dashboard.html');
}

function quickLogin(roleKey) {
    let targetUsername = 'gilang';
    if (roleKey === 'admin') targetUsername = 'rivaldy';
    if (roleKey === 'supervisor') targetUsername = 'diana';
    
    const uInput = document.getElementById('login-username');
    const pInput = document.getElementById('login-password');
    if (uInput && pInput) {
        uInput.value = targetUsername;
        pInput.value = 'password';
        handleLogin();
    }
}

function handleLogout() {
    const user = state.users.find(u => u.username === state.session.currentUser);
    writeAuditLog(state.session.currentUser, 'SYSTEM', 'LOGOUT', `Pengguna ${user ? user.name : 'Unknown'} keluar dari sesi console.`);
    
    state.session.isLoggedIn = false;
    state.session.currentUser = null;
    state.session.originalUser = null;
    state.session.isImpersonating = false;
    
    saveState();
    showToast('Anda telah keluar dari aplikasi!', 'success');
    window.location.replace('login.html');
}

// ----------------------------------------------------
// IMPERSONATE (LOGIN AS) ENGINE
// ----------------------------------------------------
function loginAs(targetUsername) {
    if (state.session.currentUser !== 'rivaldy' && state.session.originalUser !== 'rivaldy') {
        showToast('Aksi ditolak: Hanya Super Admin yang diizinkan meniru user!', 'danger');
        return;
    }

    const targetUser = state.users.find(u => u.username === targetUsername);
    if (!targetUser) {
        showToast('Pengguna target tidak ditemukan!', 'danger');
        return;
    }

    // Write Impersonation Log
    state.session.isImpersonating = true;
    state.session.originalUser = 'rivaldy';
    state.session.currentUser = targetUsername;
    state.session.currentTab = 'dashboard';

    writeAuditLog('rivaldy', 'IMPERSONATE', 'IMPERSONATE', `Super Admin mulai menirukan identitas ${targetUser.name} (${targetUser.role_id}).`);
    saveState();
    
    showToast(`Mode Impersonasi Aktif: Meniru ${targetUser.name}`, 'warning');
    window.location.replace('dashboard.html');
}

function revertImpersonation() {
    if (!state.session.isImpersonating) return;

    const targetUser = state.users.find(u => u.username === state.session.currentUser);
    state.session.isImpersonating = false;
    state.session.currentUser = 'rivaldy';
    state.session.originalUser = 'rivaldy';
    state.session.currentTab = 'dashboard';

    writeAuditLog('rivaldy', 'IMPERSONATE', 'REVERT', `Super Admin mengakhiri sesi peniruan identitas ${targetUser ? targetUser.name : 'Target'}.`);
    saveState();

    showToast('Kembali ke sesi Super Admin!', 'success');
    window.location.replace('dashboard.html');
}

// ----------------------------------------------------
// TAB AND VIEW MANAGEMENT
// ----------------------------------------------------
function switchTab(tabId) {
    // Check Permission
    if (!checkPermission(tabId, 'read')) {
        showToast(`Akses Ditolak: Peran Anda tidak memiliki izin mengakses modul ${tabId.toUpperCase()}!`, 'danger');
        return;
    }

    state.session.currentTab = tabId;
    saveState();

    window.location.href = tabId + '.html';
}

// ----------------------------------------------------
// MOBILE RESPONSIVE SIDEBAR TOGGLE
// ----------------------------------------------------
function toggleSidebar(show) {
    const sidebar = document.getElementById('sidebar-nav');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!sidebar) return;
    
    if (show) {
        sidebar.classList.add('show-sidebar');
        if (backdrop) backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.remove('show-sidebar');
        if (backdrop) backdrop.classList.add('hidden');
    }
}

// ----------------------------------------------------
// WAREHOUSE MANAGEMENT AND SELECTION
// ----------------------------------------------------
function changeActiveWarehouse(whId) {
    state.session.activeWarehouseId = whId;
    saveState();
    const whObj = state.warehouses.find(w => w.id === whId);
    showToast(`Gudang aktif diubah ke: ${whObj ? whObj.name : whId}`, 'success');
    initializeConsole();
}

// ----------------------------------------------------
// DYNAMIC DASHBOARD RENDERER (ROLE SPECIFIC)
// ----------------------------------------------------
function renderDashboard() {
    const user = state.users.find(u => u.username === state.session.currentUser);
    const container = document.getElementById('role-dashboard-content');
    if (!container || !user) return;

    document.getElementById('dashboard-role-title').textContent = user.role_id;

    const whId = state.session.activeWarehouseId;
    
    // Gather statistics for calculations
    const whRacks = state.racks.filter(r => r.warehouse_id === whId);
    const whBins = state.bins.filter(b => whRacks.some(r => r.id === b.rack_id));
    const whPallets = state.pallets.filter(p => whBins.some(b => b.id === p.bin_id));
    
    // Calculate occupation percentage
    const maxCapacityPallets = whBins.length * (state.settings.maxPalletsPerBin || 2);
    const currentPalletsCount = whPallets.length;
    const occupancyPercentage = maxCapacityPallets > 0 ? Math.round((currentPalletsCount / maxCapacityPallets) * 100) : 0;

    let htmlContent = '';

    // ==========================================
    // 1. SUPER ADMIN DASHBOARD
    // ==========================================
    if (user.role_id === 'Super Admin') {
        htmlContent = `
            <!-- Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="glass-card p-4 flex items-center justify-between">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Total Kantor</span>
                        <div class="text-2xl font-bold text-white mt-1">${state.offices.length}</div>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-400"><i class="bi bi-building"></i></div>
                </div>
                <div class="glass-card p-4 flex items-center justify-between">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Total Gudang</span>
                        <div class="text-2xl font-bold text-white mt-1">${state.warehouses.length}</div>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400"><i class="bi bi-houses"></i></div>
                </div>
                <div class="glass-card p-4 flex items-center justify-between">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Palet Aktif</span>
                        <div class="text-2xl font-bold text-white mt-1">${state.pallets.filter(p => p.is_active).length}</div>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="glass-card p-4 flex items-center justify-between">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Pengguna Aktif</span>
                        <div class="text-2xl font-bold text-white mt-1">${state.users.length}</div>
                    </div>
                    <div class="h-10 w-10 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400"><i class="bi bi-people"></i></div>
                </div>
            </div>

            <!-- Visualization Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="glass-panel p-5 lg:col-span-2 space-y-4">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider border-b border-slate-800 pb-2">Okupansi Gudang Global</h3>
                    <div class="h-[260px] relative flex items-center justify-center">
                        <canvas id="chart-global-occupancy"></canvas>
                    </div>
                </div>

                <div class="glass-panel p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white uppercase tracking-wider border-b border-slate-800 pb-2 mb-3">Impersonate Cepat</h3>
                        <p class="text-xs text-slate-400 mb-3">Pilih user di bawah untuk langsung meniru identitas dan melihat dashboard sesuai perannya:</p>
                        
                        <div class="space-y-2">
                            ${state.users.filter(u => u.username !== 'rivaldy').map(u => `
                                <div class="flex items-center justify-between p-2.5 bg-slate-900/40 rounded border border-slate-800 hover:border-slate-700 transition gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-white leading-tight">${u.name}</div>
                                        <div class="text-xxs text-slate-400 mt-0.5 leading-snug">${u.role_id} • Shift ${u.shift}</div>
                                    </div>
                                    <button onclick="loginAs('${u.username}')" class="btn btn-xs shrink-0 whitespace-nowrap py-1 px-2.5 bg-sky-500 text-darkblue-950 hover:bg-sky-600 border-none font-bold text-xxs rounded flex items-center gap-1.5 transition">
                                        <i class="bi bi-incognito"></i> Login As
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div class="text-xxs text-slate-500 border-t border-slate-800/80 pt-3 mt-4">
                        *Catatan: Sesi Super Admin disalin ke memori dan dapat dipulihkan kapan saja.
                    </div>
                </div>
            </div>
        `;
        container.innerHTML = htmlContent;
        
        // Render Chart for Super Admin
        setTimeout(() => {
            const ctx = document.getElementById('chart-global-occupancy').getContext('2d');
            const warehousesLabels = state.warehouses.map(w => w.name);
            const dataOccupancy = state.warehouses.map(w => {
                const wRacks = state.racks.filter(r => r.warehouse_id === w.id);
                const wBins = state.bins.filter(b => wRacks.some(r => r.id === b.rack_id));
                const wPallets = state.pallets.filter(p => wBins.some(b => b.id === p.bin_id));
                const max = wBins.length * (state.settings.maxPalletsPerBin || 2);
                return max > 0 ? Math.round((wPallets.length / max) * 100) : 0;
            });

            const isDark = document.body.classList.contains('dark');
            const chartColor = isDark ? '#38bdf8' : '#0284c7';
            const chartBg = isDark ? 'rgba(56, 189, 248, 0.4)' : 'rgba(2, 132, 199, 0.3)';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(15, 23, 42, 0.05)';
            const textColor = isDark ? '#94a3b8' : '#475569';

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: warehousesLabels,
                    datasets: [{
                        label: 'Persentase Okupansi (%)',
                        data: dataOccupancy,
                        backgroundColor: chartBg,
                        borderColor: chartColor,
                        borderWidth: 1.5,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { min: 0, max: 100, grid: { color: gridColor }, ticks: { color: textColor } },
                        x: { grid: { display: false }, ticks: { color: textColor } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }, 100);
    }

    // ==========================================
    // 2. WAREHOUSE SUPERVISOR DASHBOARD
    // ==========================================
    else if (user.role_id === 'Warehouse Supervisor') {
        // Expired Checker
        const expThresholdMonths = state.settings.expAlertMonths || 18;
        const now = new Date();
        const thresholdDate = new Date(now.setMonth(now.getMonth() + expThresholdMonths));
        
        // Find boxes nearing expiration
        const expiringBoxes = state.boxes.filter(box => {
            const expDate = new Date(box.expired_date);
            return expDate <= thresholdDate && state.pallets.find(p => p.id === box.pallet_id)?.bin_id;
        });

        htmlContent = `
            <!-- Metric Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Okupansi Card Gauge -->
                <div class="glass-panel p-5 flex flex-col justify-between h-48">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Okupansi Gudang Aktif</span>
                        <h4 class="text-sm font-bold text-white mt-1">${whId}</h4>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-3xl font-extrabold text-white">${occupancyPercentage}%</div>
                        <div class="flex-grow bg-slate-800 rounded-full h-3.5 overflow-hidden">
                            <div class="bg-gradient-to-r ${occupancyPercentage > 85 ? 'from-rose-500 to-rose-600' : 'from-sky-400 to-sky-500'} h-full rounded-full" style="width: ${occupancyPercentage}%"></div>
                        </div>
                    </div>
                    <div class="text-xxs text-slate-400">
                        Terpakai: <span class="text-white font-bold">${currentPalletsCount}</span> dari <span class="text-white font-bold">${maxCapacityPallets}</span> Kapasitas Palet BIN.
                    </div>
                </div>

                <!-- Expired Alert Box -->
                <div class="glass-panel p-5 flex flex-col justify-between h-48 border-amber-500/20">
                    <div>
                        <span class="text-xxs text-amber-400 uppercase tracking-wider font-semibold flex items-center gap-1.5">
                            <i class="bi bi-exclamation-triangle-fill"></i> Peringatan Kedaluwarsa (< ${expThresholdMonths} Bln)
                        </span>
                        <div class="text-3xl font-extrabold text-amber-500 mt-2">${expiringBoxes.length} Box</div>
                    </div>
                    <p class="text-xxs text-slate-400">Box terdeteksi memiliki masa kedaluwarsa mendekati batas ambang sistem.</p>
                    <button onclick="switchTab('detail-data')" class="btn btn-outline-warning text-xxs py-1 w-full border-amber-500/30 text-amber-400 hover:bg-amber-500/15">Tinjau Data Box</button>
                </div>

                <!-- Active Bins Summary -->
                <div class="glass-panel p-5 flex flex-col justify-between h-48">
                    <div>
                        <span class="text-xxs text-slate-400 uppercase tracking-wider font-semibold">Total BIN di Gudang</span>
                        <div class="text-2xl font-bold text-white mt-1">${whBins.length} Alamat BIN</div>
                    </div>
                    <div class="text-xxs text-slate-400 space-y-1">
                        <div>Terisi Penuh (2 Palet): <strong class="text-rose-400">${whBins.filter(b => whPallets.filter(p => p.bin_id === b.id).length >= 2).length}</strong> BIN</div>
                        <div>Terisi Sebagian (1 Palet): <strong class="text-amber-400">${whBins.filter(b => whPallets.filter(p => p.bin_id === b.id).length === 1).length}</strong> BIN</div>
                        <div>Kosong: <strong class="text-emerald-400">${whBins.filter(b => whPallets.filter(p => p.bin_id === b.id).length === 0).length}</strong> BIN</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="glass-panel p-5">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider border-b border-slate-800 pb-2 mb-3">Item Kedaluwarsa Kritis</h3>
                <div class="overflow-x-auto">
                    <table class="table table-dark align-middle custom-table text-xs">
                        <thead>
                            <tr>
                                <th>Barcode Box</th>
                                <th>Barcode Palet</th>
                                <th>Lokasi BIN</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Expired Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${expiringBoxes.length === 0 ? '<tr><td colspan="6" class="text-center text-slate-500 py-4">Tidak ada box dengan expired kritis.</td></tr>' : 
                            expiringBoxes.map(b => {
                                const pallet = state.pallets.find(p => p.id === b.pallet_id);
                                const product = state.products.find(p => p.id === b.product_id);
                                return `
                                    <tr class="bg-amber-500/5">
                                        <td class="font-bold text-amber-400">${b.id}</td>
                                        <td>${b.pallet_id}</td>
                                        <td><span class="badge badge-bin-location font-mono">${pallet ? pallet.bin_id : '-'}</span></td>
                                        <td>${product ? product.name : '-'}</td>
                                        <td>${b.qty}</td>
                                        <td class="text-amber-500 font-semibold">${formatIndoDate(b.expired_date)}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        container.innerHTML = htmlContent;
    }

    // ==========================================
    // 3. OPERATOR GUDANG DASHBOARD
    // ==========================================
    else if (user.role_id === 'Operator Gudang') {
        htmlContent = `
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Operational Info Panel -->
                <div class="glass-panel p-5 lg:col-span-2 space-y-4">
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider border-b border-slate-800 pb-2">Task Menu & Shortcuts</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <button onclick="switchTab('scanner')" class="p-4 bg-sky-500/5 hover:bg-sky-500/10 border border-sky-500/20 hover:border-sky-500/40 rounded-xl text-left transition flex items-center gap-4">
                            <div class="h-12 w-12 bg-sky-500/25 rounded-lg flex items-center justify-center text-sky-400 text-xl"><i class="bi bi-qr-code-scan"></i></div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Scanner Logistik</h4>
                                <p class="text-xxs text-slate-400 mt-0.5">Pemindaian cepat produk/palet/BIN.</p>
                            </div>
                        </button>

                        <button onclick="switchTab('detail-data')" class="p-4 bg-emerald-500/5 hover:bg-emerald-500/10 border border-emerald-500/20 hover:border-emerald-500/40 rounded-xl text-left transition flex items-center gap-4">
                            <div class="h-12 w-12 bg-emerald-500/25 rounded-lg flex items-center justify-center text-emerald-400 text-xl"><i class="bi bi-box-arrow-in-down"></i></div>
                            <div>
                                <h4 class="text-xs font-bold text-white">Mutasi Barang (In/Out/Move)</h4>
                                <p class="text-xxs text-slate-400 mt-0.5">Kelola isi box dan lokasi palet gudang.</p>
                            </div>
                        </button>
                    </div>

                    <div class="p-3 bg-slate-900/30 rounded-lg border border-slate-800 mt-4 text-xs">
                        <div class="font-bold text-white flex items-center gap-1.5 mb-1"><i class="bi bi-info-circle text-sky-400"></i> Panduan Shift Tugas Anda:</div>
                        <p class="text-xxs text-slate-400 leading-relaxed">
                            Pastikan Anda menggunakan barcode scanner handheld yang sudah dikonfigurasi. Setiap meletakkan palet pada BIN rak, lakukan scan barcode rak dan scan barcode palet untuk memverifikasi kecocokan lokasi sistem dengan fisik.
                        </p>
                    </div>
                </div>

                <!-- Recent Activities (Audit Log) -->
                <div class="glass-panel p-5 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white uppercase tracking-wider border-b border-slate-800 pb-2 mb-3">Aktivitas Terakhir Saya</h3>
                        <div class="space-y-3" id="op-recent-logs-list">
                            <!-- Dynamic -->
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-800/80 mt-4">
                        <button onclick="switchTab('profile')" class="btn btn-outline-secondary custom-input w-full text-xxs py-1">Semua Jejak Kerja Saya</button>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML = htmlContent;

        // Render recent Operator logs
        const listContainer = document.getElementById('op-recent-logs-list');
        if (listContainer) {
            const opLogs = state.audit_logs.filter(l => l.user_id === user.username).slice(0, 3);
            if (opLogs.length === 0) {
                listContainer.innerHTML = '<div class="text-center text-slate-500 text-xs py-4">Belum ada aktivitas tercatat hari ini.</div>';
            } else {
                listContainer.innerHTML = opLogs.map(l => `
                    <div class="p-2 bg-slate-900/30 rounded border border-slate-800 text-xxs">
                        <div class="flex justify-between font-mono text-slate-500 mb-1">
                            <span>${new Date(l.timestamp).toLocaleTimeString('id-ID')}</span>
                            <span class="text-sky-400 font-semibold">${l.module}</span>
                        </div>
                        <p class="text-slate-300 mb-0 font-medium">${l.description}</p>
                    </div>
                `).join('');
            }
        }
    }
}

// ----------------------------------------------------
// DATA MASTER TAB LOGIC
// ----------------------------------------------------
let activeMasterTab = 'offices';
let masterPagination = {
    currentPage: 1,
    perPage: 5
};

function switchMasterTab(tabName) {
    activeMasterTab = tabName;
    
    // Reset search query
    const searchInput = document.getElementById('master-search-input');
    if (searchInput) searchInput.value = '';
    
    document.querySelectorAll('#master-subtabs button').forEach(btn => {
        btn.classList.remove('active', 'bg-sky-500', 'text-darkblue-950');
        btn.classList.add('text-slate-400');
        if (btn.textContent.toLowerCase() === getTabLabel(tabName).toLowerCase()) {
            btn.classList.add('active', 'bg-sky-500', 'text-darkblue-950');
            btn.classList.remove('text-slate-400');
        }
    });

    masterPagination.currentPage = 1;
    rebuildMasterFilterSelect();
    loadMasterDataTab();
}

function getTabLabel(tab) {
    if (tab === 'offices') return 'Kantor';
    if (tab === 'branches') return 'Cabang';
    if (tab === 'warehouses') return 'Gudang';
    if (tab === 'racks') return 'Rak';
    if (tab === 'bins') return 'BIN';
    if (tab === 'products') return 'Produk';
    return '';
}

function rebuildMasterFilterSelect() {
    const filterSelect = document.getElementById('master-filter-select');
    if (!filterSelect) return;

    if (activeMasterTab === 'offices') {
        filterSelect.classList.add('hidden');
        filterSelect.innerHTML = '';
        return;
    }

    filterSelect.classList.remove('hidden');
    let html = '';

    if (activeMasterTab === 'branches') {
        html = '<option value="">Semua Kantor</option>' + 
            state.offices.map(o => `<option value="${o.id}">${o.name}</option>`).join('');
    } else if (activeMasterTab === 'warehouses') {
        html = '<option value="">Semua Cabang</option>' + 
            state.branches.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    } else if (activeMasterTab === 'racks') {
        html = '<option value="">Semua Gudang</option>' + 
            state.warehouses.map(w => `<option value="${w.id}">${w.name}</option>`).join('');
    } else if (activeMasterTab === 'bins') {
        html = `
            <option value="">Semua Status BIN</option>
            <option value="Kosong">Kosong</option>
            <option value="Sebagian">Terisi Sebagian</option>
            <option value="Penuh">Penuh (2/2)</option>
        `;
    } else if (activeMasterTab === 'products') {
        html = `
            <option value="">Semua Satuan</option>
            <option value="Karton">Karton</option>
            <option value="Bag">Bag</option>
            <option value="Box">Box</option>
            <option value="Pcs">Pcs</option>
            <option value="Kg">Kg</option>
        `;
    }

    filterSelect.innerHTML = html;
}

function loadMasterDataTab() {
    const thead = document.getElementById('master-thead');
    const tbody = document.getElementById('master-tbody');
    const title = document.getElementById('master-table-title');
    if (!thead || !tbody) return;

    title.textContent = `Daftar Data ${getTabLabel(activeMasterTab)}`;

    // 1. Get raw items based on activeMasterTab
    let items = [];
    if (activeMasterTab === 'offices') items = [...state.offices];
    else if (activeMasterTab === 'branches') items = [...state.branches];
    else if (activeMasterTab === 'warehouses') items = [...state.warehouses];
    else if (activeMasterTab === 'racks') items = [...state.racks];
    else if (activeMasterTab === 'bins') items = [...state.bins];
    else if (activeMasterTab === 'products') items = [...state.products];

    // 2. Apply search filter
    const searchInput = document.getElementById('master-search-input');
    const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
    if (searchQuery) {
        items = items.filter(item => {
            if (activeMasterTab === 'offices') {
                return item.id.toLowerCase().includes(searchQuery) || item.name.toLowerCase().includes(searchQuery) || item.address.toLowerCase().includes(searchQuery);
            } else if (activeMasterTab === 'branches') {
                const office = state.offices.find(o => o.id === item.office_id);
                const officeName = office ? office.name.toLowerCase() : '';
                return item.id.toLowerCase().includes(searchQuery) || item.name.toLowerCase().includes(searchQuery) || item.city.toLowerCase().includes(searchQuery) || officeName.includes(searchQuery);
            } else if (activeMasterTab === 'warehouses') {
                const branch = state.branches.find(b => b.id === item.branch_id);
                const branchName = branch ? branch.name.toLowerCase() : '';
                return item.id.toLowerCase().includes(searchQuery) || item.name.toLowerCase().includes(searchQuery) || item.address.toLowerCase().includes(searchQuery) || branchName.includes(searchQuery);
            } else if (activeMasterTab === 'racks') {
                const wh = state.warehouses.find(w => w.id === item.warehouse_id);
                const whName = wh ? wh.name.toLowerCase() : '';
                return item.id.toLowerCase().includes(searchQuery) || item.rack_name.toLowerCase().includes(searchQuery) || whName.includes(searchQuery);
            } else if (activeMasterTab === 'bins') {
                const rack = state.racks.find(r => r.id === item.rack_id);
                const rackLabel = rack ? `rak ${rack.rack_name}`.toLowerCase() : '';
                return item.id.toLowerCase().includes(searchQuery) || rackLabel.includes(searchQuery) || `kolom ${item.column_no}`.includes(searchQuery) || `sap ${item.level_no}`.includes(searchQuery);
            } else if (activeMasterTab === 'products') {
                return item.id.toLowerCase().includes(searchQuery) || item.name.toLowerCase().includes(searchQuery) || item.default_unit.toLowerCase().includes(searchQuery);
            }
            return false;
        });
    }

    // 3. Apply select dropdown filter
    const filterSelect = document.getElementById('master-filter-select');
    const filterValue = filterSelect ? filterSelect.value : '';
    if (filterValue) {
        items = items.filter(item => {
            if (activeMasterTab === 'branches') {
                return item.office_id === filterValue;
            } else if (activeMasterTab === 'warehouses') {
                return item.branch_id === filterValue;
            } else if (activeMasterTab === 'racks') {
                return item.warehouse_id === filterValue;
            } else if (activeMasterTab === 'bins') {
                const palletsInBin = state.pallets.filter(p => p.bin_id === item.id && p.is_active);
                const count = palletsInBin.length;
                if (filterValue === 'Kosong') return count === 0;
                if (filterValue === 'Sebagian') return count > 0 && count < (state.settings.maxPalletsPerBin || 2);
                if (filterValue === 'Penuh') return count >= (state.settings.maxPalletsPerBin || 2);
            } else if (activeMasterTab === 'products') {
                return item.default_unit === filterValue;
            }
            return true;
        });
    }

    // 4. Pagination calculations
    const totalItems = items.length;
    const perPageSelect = document.getElementById('master-per-page');
    const perPage = perPageSelect ? parseInt(perPageSelect.value) : masterPagination.perPage;
    masterPagination.perPage = perPage;

    const totalPages = Math.ceil(totalItems / perPage) || 1;
    if (masterPagination.currentPage > totalPages) {
        masterPagination.currentPage = totalPages;
    }
    const startIdx = (masterPagination.currentPage - 1) * perPage;
    const endIdx = Math.min(startIdx + perPage, totalItems);

    const paginatedItems = items.slice(startIdx, endIdx);

    // Build Table Header & Body
    let theadHtml = '';
    let tbodyHtml = '';
    const canUpdate = checkPermission('master-data', 'update');
    const canDelete = checkPermission('master-data', 'delete');

    if (activeMasterTab === 'offices') {
        theadHtml = `
            <tr>
                <th>ID Kantor</th>
                <th>Nama Kantor</th>
                <th>Alamat Lengkap</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="4" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(o => `
                <tr>
                    <td class="font-bold text-sky-400">${o.id}</td>
                    <td>${o.name}</td>
                    <td>${o.address}</td>
                    <td class="text-end">
                        <button onclick="openEditMasterModal('offices', '${o.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                        <button onclick="deleteMasterItem('offices', '${o.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }
    else if (activeMasterTab === 'branches') {
        theadHtml = `
            <tr>
                <th>ID Cabang</th>
                <th>Kantor Pusat Induk</th>
                <th>Nama Cabang</th>
                <th>Kota</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="5" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(b => {
                const office = state.offices.find(o => o.id === b.office_id);
                return `
                    <tr>
                        <td class="font-bold text-sky-400">${b.id}</td>
                        <td>${office ? office.name : b.office_id}</td>
                        <td>${b.name}</td>
                        <td>${b.city}</td>
                        <td class="text-end">
                            <button onclick="openEditMasterModal('branches', '${b.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="deleteMasterItem('branches', '${b.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }
    else if (activeMasterTab === 'warehouses') {
        theadHtml = `
            <tr>
                <th>Kode Gudang</th>
                <th>Cabang</th>
                <th>Nama Gudang</th>
                <th>Alamat Gudang</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="5" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(w => {
                const branch = state.branches.find(b => b.id === w.branch_id);
                return `
                    <tr>
                        <td class="font-bold text-sky-400">${w.id}</td>
                        <td>${branch ? branch.name : w.branch_id}</td>
                        <td>${w.name}</td>
                        <td>${w.address}</td>
                        <td class="text-end">
                            <button onclick="openEditMasterModal('warehouses', '${w.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="deleteMasterItem('warehouses', '${w.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }
    else if (activeMasterTab === 'racks') {
        theadHtml = `
            <tr>
                <th>Barcode Rak</th>
                <th>Gudang Penempatan</th>
                <th>Nama Huruf Rak</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="4" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(r => {
                const wh = state.warehouses.find(w => w.id === r.warehouse_id);
                return `
                    <tr>
                        <td class="font-bold text-sky-400">${r.id}</td>
                        <td>${wh ? wh.name : r.warehouse_id}</td>
                        <td class="font-semibold text-amber-400 font-mono">${r.rack_name}</td>
                        <td class="text-end">
                            <button onclick="openEditMasterModal('racks', '${r.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="deleteMasterItem('racks', '${r.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }
    else if (activeMasterTab === 'bins') {
        theadHtml = `
            <tr>
                <th>Barcode BIN</th>
                <th>Rak Induk</th>
                <th>Koordinat (Kolom/Tingkat)</th>
                <th>Maks Palet</th>
                <th>Kondisi Terisi</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="6" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(b => {
                const rack = state.racks.find(r => r.id === b.rack_id);
                const palletsInBin = state.pallets.filter(p => p.bin_id === b.id && p.is_active);
                const count = palletsInBin.length;
                const badgeClass = count === 0 ? 'badge-status-empty' : count >= (state.settings.maxPalletsPerBin || 2) ? 'badge-status-full' : 'badge-status-partial';
                const statusLabel = count === 0 ? 'Kosong' : count >= (state.settings.maxPalletsPerBin || 2) ? 'Penuh (2/2)' : `Terisi Sebagian (${count}/2)`;

                return `
                    <tr>
                        <td class="font-bold text-sky-400 font-mono">${b.id}</td>
                        <td>${rack ? `Rak ${rack.rack_name}` : b.rack_id}</td>
                        <td>Kolom ${b.column_no}, SAP ${b.level_no}</td>
                        <td>${b.max_pallet} Palet</td>
                        <td><span class="badge ${badgeClass} text-xxs py-1">${statusLabel}</span></td>
                        <td class="text-end">
                            <button onclick="openEditMasterModal('bins', '${b.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                            <button onclick="deleteMasterItem('bins', '${b.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    }
    else if (activeMasterTab === 'products') {
        theadHtml = `
            <tr>
                <th>SKU Produk</th>
                <th>Nama Produk</th>
                <th>Satuan Default</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        if (paginatedItems.length === 0) {
            tbodyHtml = `<tr><td colspan="4" class="text-center text-slate-500 py-4">Tidak ada data ditemukan</td></tr>`;
        } else {
            tbodyHtml = paginatedItems.map(p => `
                <tr>
                    <td class="font-bold text-sky-400 font-mono">${p.id}</td>
                    <td class="font-medium text-white">${p.name}</td>
                    <td><span class="badge badge-pallet-type text-xxs py-1">${p.default_unit}</span></td>
                    <td class="text-end">
                        <button onclick="openEditMasterModal('products', '${p.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                        <button onclick="deleteMasterItem('products', '${p.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    }

    thead.innerHTML = theadHtml;
    tbody.innerHTML = tbodyHtml;

    // Render Mobile Cards List
    const mobileCardsContainer = document.getElementById('master-mobile-cards');
    if (mobileCardsContainer) {
        if (paginatedItems.length === 0) {
            mobileCardsContainer.innerHTML = `<div class="text-center text-slate-500 py-4 text-xs">Tidak ada data ditemukan</div>`;
        } else {
            mobileCardsContainer.innerHTML = paginatedItems.map(item => {
                let cardBodyHtml = '';
                let titleVal = '';
                
                if (activeMasterTab === 'offices') {
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Nama Kantor</span>
                            <span class="text-xs text-slate-100 font-medium">${item.name}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Alamat</span>
                            <span class="text-xs text-slate-300 text-wrap">${item.address}</span>
                        </div>
                    `;
                } else if (activeMasterTab === 'branches') {
                    const office = state.offices.find(o => o.id === item.office_id);
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Kantor Pusat Induk</span>
                            <span class="text-xs text-slate-100 font-medium">${office ? office.name : item.office_id}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Nama Cabang</span>
                            <span class="text-xs text-slate-300">${item.name}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Kota</span>
                            <span class="text-xs text-slate-300">${item.city}</span>
                        </div>
                    `;
                } else if (activeMasterTab === 'warehouses') {
                    const branch = state.branches.find(b => b.id === item.branch_id);
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Cabang</span>
                            <span class="text-xs text-slate-100 font-medium">${branch ? branch.name : item.branch_id}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Nama Gudang</span>
                            <span class="text-xs text-slate-300">${item.name}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Alamat Gudang</span>
                            <span class="text-xs text-slate-300 text-wrap">${item.address}</span>
                        </div>
                    `;
                } else if (activeMasterTab === 'racks') {
                    const wh = state.warehouses.find(w => w.id === item.warehouse_id);
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Gudang Penempatan</span>
                            <span class="text-xs text-slate-100 font-medium">${wh ? wh.name : item.warehouse_id}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Huruf Rak</span>
                            <span class="text-xs text-amber-400 font-mono font-semibold">${item.rack_name}</span>
                        </div>
                    `;
                } else if (activeMasterTab === 'bins') {
                    const rack = state.racks.find(r => r.id === item.rack_id);
                    const palletsInBin = state.pallets.filter(p => p.bin_id === item.id && p.is_active);
                    const count = palletsInBin.length;
                    const badgeClass = count === 0 ? 'badge-status-empty' : count >= (state.settings.maxPalletsPerBin || 2) ? 'badge-status-full' : 'badge-status-partial';
                    const statusLabel = count === 0 ? 'Kosong' : count >= (state.settings.maxPalletsPerBin || 2) ? 'Penuh (2/2)' : `Terisi Sebagian (${count}/2)`;
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Rak Induk</span>
                            <span class="text-xs text-slate-100 font-medium">${rack ? `Rak ${rack.rack_name}` : item.rack_id}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Koordinat</span>
                            <span class="text-xs text-slate-300">Kolom ${item.column_no}, SAP ${item.level_no}</span>
                        </div>
                        <div class="mt-1.5 flex items-center gap-4">
                            <div>
                                <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Kapasitas</span>
                                <span class="text-xs text-slate-300">${item.max_pallet} Palet</span>
                            </div>
                            <div>
                                <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Status BIN</span>
                                <span class="badge ${badgeClass} text-xxs py-1 mt-0.5">${statusLabel}</span>
                            </div>
                        </div>
                    `;
                } else if (activeMasterTab === 'products') {
                    titleVal = item.id;
                    cardBodyHtml = `
                        <div>
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Nama Produk</span>
                            <span class="text-xs text-slate-100 font-medium text-wrap">${item.name}</span>
                        </div>
                        <div class="mt-1.5">
                            <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Satuan Default</span>
                            <span class="badge badge-pallet-type text-xxs py-1 mt-0.5">${item.default_unit}</span>
                        </div>
                    `;
                }

                return `
                    <div class="py-3.5 border-b border-slate-200/50 dark:border-slate-800/80 flex flex-col space-y-2">
                        <div class="flex items-center justify-between pb-1.5">
                            <span class="font-bold text-sky-500 font-mono text-xs">${titleVal}</span>
                            <div class="flex gap-1.5">
                                <button onclick="openEditMasterModal('${activeMasterTab}', '${item.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                                <button onclick="deleteMasterItem('${activeMasterTab}', '${item.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-slate-400">
                            ${cardBodyHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }
    }

    // Render pagination controls
    renderMasterPagination(totalItems, perPage, totalPages, startIdx, endIdx);
    
    // Manage Create Button Permission
    const createBtn = document.getElementById('btn-master-create');
    if (createBtn) {
        if (checkPermission('master-data', 'create')) {
            createBtn.classList.remove('disabled');
        } else {
            createBtn.classList.add('disabled');
        }
    }
}

function filterMasterTable() {
    masterPagination.currentPage = 1;
    loadMasterDataTab();
}

function renderMasterPagination(totalItems, perPage, totalPages, startIdx, endIdx) {
    const infoSpan = document.getElementById('master-pagination-info');
    const container = document.getElementById('master-pagination-container');
    if (!infoSpan || !container) return;

    if (totalItems === 0) {
        infoSpan.textContent = 'Menampilkan 0 data';
        container.innerHTML = '';
        return;
    }

    infoSpan.textContent = `Menampilkan ${startIdx + 1} - ${endIdx} dari ${totalItems} data`;

    let paginationHtml = '';

    // Previous Button
    const prevDisabled = masterPagination.currentPage === 1 ? 'disabled' : '';
    paginationHtml += `
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" onclick="changeMasterPage(${masterPagination.currentPage - 1}); return false;" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;

    // Page Numbers
    for (let i = 1; i <= totalPages; i++) {
        const activeClass = i === masterPagination.currentPage ? 'active' : '';
        paginationHtml += `
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" onclick="changeMasterPage(${i}); return false;">${i}</a>
            </li>
        `;
    }

    // Next Button
    const nextDisabled = masterPagination.currentPage === totalPages ? 'disabled' : '';
    paginationHtml += `
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" onclick="changeMasterPage(${masterPagination.currentPage + 1}); return false;" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;

    container.innerHTML = paginationHtml;
}

function changeMasterPage(pageNum) {
    masterPagination.currentPage = pageNum;
    loadMasterDataTab();
}

function changeMasterPerPage(val) {
    masterPagination.perPage = parseInt(val);
    masterPagination.currentPage = 1;
    loadMasterDataTab();
}


// ----------------------------------------------------
// MASTER FORM CRUD OPERATION MODALS
// ----------------------------------------------------
function openCreateMasterModal() {
    if (!checkPermission('master-data', 'create')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses menambah data master!', 'danger');
        return;
    }

    document.getElementById('master-form-action').value = 'create';
    document.getElementById('master-form-target-id').value = '';
    document.getElementById('masterModalLabel').textContent = `Tambah Data ${getTabLabel(activeMasterTab)}`;

    buildMasterFormFields();
    
    const myModal = new bootstrap.Modal(document.getElementById('masterModal'));
    myModal.show();
}

function openEditMasterModal(tabName, id) {
    if (!checkPermission('master-data', 'update')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses mengedit data master!', 'danger');
        return;
    }

    document.getElementById('master-form-action').value = 'edit';
    document.getElementById('master-form-target-id').value = id;
    document.getElementById('masterModalLabel').textContent = `Ubah Data ${getTabLabel(activeMasterTab)}: ${id}`;

    buildMasterFormFields(id);
    
    const myModal = new bootstrap.Modal(document.getElementById('masterModal'));
    myModal.show();
}

function buildMasterFormFields(editId = null) {
    const container = document.getElementById('master-form-fields-container');
    if (!container) return;

    let fieldsHtml = '';

    if (activeMasterTab === 'offices') {
        const o = editId ? state.offices.find(item => item.id === editId) : { id: '', name: '', address: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">ID Kantor (Kode Unik)</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. KTR-SBY" value="${o.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Nama Kantor</label>
                <input type="text" id="m-field-name" class="form-control custom-input" placeholder="e.g. Kantor Cabang Surabaya" value="${o.name}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Alamat Lengkap</label>
                <textarea id="m-field-address" class="form-control custom-input" placeholder="Alamat..." required>${o.address}</textarea>
            </div>
        `;
    }
    else if (activeMasterTab === 'branches') {
        const b = editId ? state.branches.find(item => item.id === editId) : { id: '', office_id: '', name: '', city: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">ID Cabang (Kode Unik)</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. CBG-GSK" value="${b.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Kantor Pusat Induk</label>
                <select id="m-field-office_id" class="form-select custom-input" required>
                    ${state.offices.map(o => `<option value="${o.id}" ${o.id === b.office_id ? 'selected' : ''}>${o.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Nama Cabang</label>
                <input type="text" id="m-field-name" class="form-control custom-input" placeholder="e.g. Cabang Gresik" value="${b.name}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Kota Cabang</label>
                <input type="text" id="m-field-city" class="form-control custom-input" placeholder="e.g. Gresik" value="${b.city}" required>
            </div>
        `;
    }
    else if (activeMasterTab === 'warehouses') {
        const w = editId ? state.warehouses.find(item => item.id === editId) : { id: '', branch_id: '', name: '', address: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">Kode Gudang (Unique)</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. GSK.678.I" value="${w.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Cabang Penanggung Jawab</label>
                <select id="m-field-branch_id" class="form-select custom-input" required>
                    ${state.branches.map(br => `<option value="${br.id}" ${br.id === w.branch_id ? 'selected' : ''}>${br.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Nama Gudang</label>
                <input type="text" id="m-field-name" class="form-control custom-input" placeholder="e.g. Gudang Gresik Utama" value="${w.name}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Alamat Gudang</label>
                <textarea id="m-field-address" class="form-control custom-input" placeholder="Alamat..." required>${w.address}</textarea>
            </div>
        `;
    }
    else if (activeMasterTab === 'racks') {
        const r = editId ? state.racks.find(item => item.id === editId) : { id: '', warehouse_id: '', rack_name: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">Barcode Rak (Kode Unik)</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. RK-A" value="${r.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Gudang Penempatan</label>
                <select id="m-field-warehouse_id" class="form-select custom-input" required>
                    ${state.warehouses.map(w => `<option value="${w.id}" ${w.id === r.warehouse_id ? 'selected' : ''}>${w.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Nama Huruf Rak (Alfabet/Kode)</label>
                <input type="text" id="m-field-rack_name" class="form-control custom-input" placeholder="e.g. B atau A" value="${r.rack_name}" required>
            </div>
        `;
    }
    else if (activeMasterTab === 'bins') {
        const bn = editId ? state.bins.find(item => item.id === editId) : { id: '', rack_id: '', column_no: '', level_no: '', max_pallet: 2 };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">Barcode BIN (Format: RAK.KOLOM.TINGKAT)</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. B.10.2" value="${bn.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Rak Penampung</label>
                <select id="m-field-rack_id" class="form-select custom-input" required>
                    ${state.racks.map(rk => `<option value="${rk.id}" ${rk.id === bn.rack_id ? 'selected' : ''}>Rak ${rk.rack_name} - ${rk.warehouse_id}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Nomor Kolom</label>
                <input type="number" id="m-field-column_no" min="1" max="100" class="form-control custom-input" value="${bn.column_no}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Nomor SAP / Tingkat</label>
                <input type="number" id="m-field-level_no" min="1" max="10" class="form-control custom-input" value="${bn.level_no}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Maksimal Kapasitas Palet</label>
                <input type="number" id="m-field-max_pallet" min="1" max="10" class="form-control custom-input" value="${bn.max_pallet}" required>
            </div>
        `;
    }
    else if (activeMasterTab === 'products') {
        const p = editId ? state.products.find(item => item.id === editId) : { id: '', name: '', default_unit: 'Karton' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">SKU / Kode Barcode Produk</label>
                <div class="input-group">
                    <input type="text" id="m-field-id" class="form-control custom-input" placeholder="e.g. PRD-004" value="${p.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="generateAutoCode('m-field-id')" title="Auto Generate Kode">
                        <i class="bi bi-magic"></i> Gen
                    </button>
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 font-semibold text-xxs px-2.5 py-1.5" type="button" onclick="startCameraScan('m-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i> Scan
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Nama Lengkap Varian Produk</label>
                <input type="text" id="m-field-name" class="form-control custom-input" placeholder="e.g. Kopi Indraco Gold" value="${p.name}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Satuan Barang Default</label>
                <select id="m-field-default_unit" class="form-select custom-input" required>
                    <option value="Karton" ${p.default_unit === 'Karton' ? 'selected' : ''}>Karton (Carton)</option>
                    <option value="Bag" ${p.default_unit === 'Bag' ? 'selected' : ''}>Bag</option>
                    <option value="Box" ${p.default_unit === 'Box' ? 'selected' : ''}>Box</option>
                    <option value="Pcs" ${p.default_unit === 'Pcs' ? 'selected' : ''}>Pcs</option>
                    <option value="Kg" ${p.default_unit === 'Kg' ? 'selected' : ''}>Kg (Kilogram)</option>
                </select>
            </div>
        `;
    }

    container.innerHTML = fieldsHtml;
}

function handleMasterFormSubmit(e) {
    e.preventDefault();
    const action = document.getElementById('master-form-action').value;
    const targetId = document.getElementById('master-form-target-id').value;
    
    // Read fields dynamically
    const data = {};
    if (document.getElementById('m-field-id')) data.id = document.getElementById('m-field-id').value.trim();
    if (document.getElementById('m-field-name')) data.name = document.getElementById('m-field-name').value.trim();
    if (document.getElementById('m-field-address')) data.address = document.getElementById('m-field-address').value.trim();
    if (document.getElementById('m-field-office_id')) data.office_id = document.getElementById('m-field-office_id').value;
    if (document.getElementById('m-field-branch_id')) data.branch_id = document.getElementById('m-field-branch_id').value;
    if (document.getElementById('m-field-city')) data.city = document.getElementById('m-field-city').value.trim();
    if (document.getElementById('m-field-warehouse_id')) data.warehouse_id = document.getElementById('m-field-warehouse_id').value;
    if (document.getElementById('m-field-rack_name')) data.rack_name = document.getElementById('m-field-rack_name').value.trim();
    if (document.getElementById('m-field-rack_id')) data.rack_id = document.getElementById('m-field-rack_id').value;
    if (document.getElementById('m-field-column_no')) data.column_no = parseInt(document.getElementById('m-field-column_no').value);
    if (document.getElementById('m-field-level_no')) data.level_no = parseInt(document.getElementById('m-field-level_no').value);
    if (document.getElementById('m-field-max_pallet')) data.max_pallet = parseInt(document.getElementById('m-field-max_pallet').value);
    if (document.getElementById('m-field-default_unit')) data.default_unit = document.getElementById('m-field-default_unit').value;

    const list = state[activeMasterTab];

    if (action === 'create') {
        // Validate duplication
        const duplicate = list.find(item => item.id.toLowerCase() === data.id.toLowerCase());
        if (duplicate) {
            showToast(`Gagal: Kode ID '${data.id}' sudah digunakan di tabel master!`, 'danger');
            return;
        }
        list.push(data);
        writeAuditLog(state.session.currentUser, 'MASTER', 'CREATE', `Menambahkan master ${activeMasterTab} baru: ${data.id} (${data.name || data.rack_name || ''})`);
    } else {
        // Update existing item
        const index = list.findIndex(item => item.id === targetId);
        if (index !== -1) {
            list[index] = { ...list[index], ...data };
            writeAuditLog(state.session.currentUser, 'MASTER', 'UPDATE', `Mengubah master ${activeMasterTab} ID: ${targetId}`);
        }
    }

    saveState();
    loadMasterDataTab();
    
    // Close Modal
    const modalEl = document.getElementById('masterModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();
    
    showToast(`Data ${getTabLabel(activeMasterTab)} berhasil disimpan!`, 'success');
}

function deleteMasterItem(tabName, id) {
    if (!checkPermission('master-data', 'delete')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses menghapus data master!', 'danger');
        return;
    }

    // Check Organization Dependencies
    if (tabName === 'offices') {
        const dependent = state.branches.find(b => b.office_id === id);
        if (dependent) {
            showToast(`Gagal: Kantor '${id}' tidak bisa dihapus karena masih memuat Cabang '${dependent.name}'!`, 'danger');
            return;
        }
    }
    else if (tabName === 'branches') {
        const dependent = state.warehouses.find(w => w.branch_id === id);
        if (dependent) {
            showToast(`Gagal: Cabang '${id}' tidak bisa dihapus karena memiliki Gudang '${dependent.name}'!`, 'danger');
            return;
        }
    }
    else if (tabName === 'warehouses') {
        const dependent = state.racks.find(r => r.warehouse_id === id);
        if (dependent) {
            showToast(`Gagal: Gudang '${id}' memiliki data Rak di dalamnya!`, 'danger');
            return;
        }
    }
    else if (tabName === 'racks') {
        const dependent = state.bins.find(b => b.rack_id === id);
        if (dependent) {
            showToast(`Gagal: Rak '${id}' memiliki koordinat BIN terdaftar!`, 'danger');
            return;
        }
    }
    else if (tabName === 'bins') {
        const dependent = state.pallets.find(p => p.bin_id === id && p.is_active);
        if (dependent) {
            showToast(`Gagal: Lokasi BIN '${id}' tidak kosong (memuat Palet '${dependent.id}')!`, 'danger');
            return;
        }
    }
    else if (tabName === 'products') {
        const dependent = state.boxes.find(b => b.product_id === id);
        if (dependent) {
            showToast(`Gagal: Produk '${id}' masih tersimpan di dalam Box '${dependent.id}' pada Palet '${dependent.pallet_id}'!`, 'danger');
            return;
        }
    }

    if (confirm(`Apakah Anda yakin ingin menghapus data master '${id}' dari sistem?`)) {
        state[tabName] = state[tabName].filter(item => item.id !== id);
        writeAuditLog(state.session.currentUser, 'MASTER', 'DELETE', `Menghapus master ${tabName} ID: ${id}`);
        saveState();
        loadMasterDataTab();
        showToast('Data master berhasil dihapus!', 'success');
    }
}

// ----------------------------------------------------
// DATA DETAIL (INVENTORY DETAIL) TAB LOGIC
// ----------------------------------------------------
let activeDetailTab = 'pallets';

function switchDetailTab(tabName) {
    activeDetailTab = tabName;
    document.querySelectorAll('#detail-subtabs button').forEach(btn => {
        btn.classList.remove('active', 'bg-sky-500', 'text-darkblue-950');
        btn.classList.add('text-slate-400');
        if ((tabName === 'pallets' && btn.textContent.includes('Palet')) || (tabName === 'boxes' && btn.textContent.includes('Box'))) {
            btn.classList.add('active', 'bg-sky-500', 'text-darkblue-950');
            btn.classList.remove('text-slate-400');
        }
    });

    loadDetailDataTab();
}

function loadDetailDataTab() {
    const thead = document.getElementById('detail-thead');
    const tbody = document.getElementById('detail-tbody');
    const title = document.getElementById('detail-table-title');
    const mobileCardsContainer = document.getElementById('detail-mobile-cards');
    if (!thead || !tbody) return;

    const canUpdate = checkPermission('detail-data', 'update');
    const canDelete = checkPermission('detail-data', 'delete');

    if (activeDetailTab === 'pallets') {
        title.textContent = "Daftar Palet Terdaftar";
        thead.innerHTML = `
            <tr>
                <th>Barcode Palet</th>
                <th>Jenis Palet</th>
                <th>Lokasi BIN Aktif</th>
                <th>Muatan Box</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        tbody.innerHTML = state.pallets.map(p => {
            const boxes = state.boxes.filter(b => b.pallet_id === p.id);
            const totalBoxesQty = boxes.reduce((acc, curr) => acc + curr.qty, 0);
            
            return `
                <tr>
                    <td class="font-bold text-sky-400 font-mono">${p.id}</td>
                    <td><span class="badge badge-pallet-type text-xxs py-1">${p.pallet_type}</span></td>
                    <td><span class="badge badge-bin-location font-mono py-1 px-2.5">${p.bin_id || 'DISPATCH / TRANSIT'}</span></td>
                    <td><strong class="text-white">${boxes.length} Box</strong> (${totalBoxesQty} Pcs)</td>
                    <td class="text-end">
                        <button onclick="openEditDetailModal('pallets', '${p.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                        <button onclick="deleteDetailItem('pallets', '${p.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');

        // Mobile list rendering
        if (mobileCardsContainer) {
            if (state.pallets.length === 0) {
                mobileCardsContainer.innerHTML = `<div class="text-center text-slate-500 py-4 text-xs">Tidak ada data ditemukan</div>`;
            } else {
                mobileCardsContainer.innerHTML = state.pallets.map(p => {
                    const boxes = state.boxes.filter(b => b.pallet_id === p.id);
                    const totalBoxesQty = boxes.reduce((acc, curr) => acc + curr.qty, 0);
                    
                    return `
                        <div class="py-3.5 border-b border-slate-200/50 dark:border-slate-800/80 flex flex-col space-y-2">
                            <div class="flex items-center justify-between pb-1.5">
                                <span class="font-bold text-sky-500 font-mono text-xs">${p.id}</span>
                                <div class="flex gap-1.5">
                                    <button onclick="openEditDetailModal('pallets', '${p.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                                    <button onclick="deleteDetailItem('pallets', '${p.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <div class="space-y-1.5 text-slate-400">
                                <div>
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Jenis Palet</span>
                                    <span class="badge badge-pallet-type text-xxs py-1 mt-0.5">${p.pallet_type}</span>
                                </div>
                                <div class="mt-1.5">
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Lokasi BIN Aktif</span>
                                    <span class="badge badge-bin-location font-mono py-1 px-2 mt-0.5 text-xxs">${p.bin_id || 'DISPATCH / TRANSIT'}</span>
                                </div>
                                <div class="mt-1.5">
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Muatan Box</span>
                                    <span class="text-xs text-slate-100 font-medium">${boxes.length} Box (${totalBoxesQty} Pcs)</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
    }
    else if (activeDetailTab === 'boxes') {
        title.textContent = "Daftar Box dalam Palet";
        thead.innerHTML = `
            <tr>
                <th>Barcode Box</th>
                <th>Palet Penampung</th>
                <th>Lokasi BIN</th>
                <th>Varian Produk</th>
                <th>Jumlah Qty</th>
                <th>Expired Date</th>
                <th>Kode Mesin</th>
                <th class="text-end">Aksi</th>
            </tr>
        `;
        tbody.innerHTML = state.boxes.map(b => {
            const pallet = state.pallets.find(p => p.id === b.pallet_id);
            const product = state.products.find(p => p.id === b.product_id);
            
            // Check expiry alerts
            const now = new Date();
            const expLimitDate = new Date(now.setMonth(now.getMonth() + (state.settings.expAlertMonths || 18)));
            const boxExpDate = new Date(b.expired_date);
            const isExp = boxExpDate <= expLimitDate;
            const expColorClass = isExp ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-700 dark:text-slate-300';
            
            return `
                <tr>
                    <td class="font-bold text-sky-400 font-mono">${b.id}</td>
                    <td class="font-semibold text-slate-300">${b.pallet_id}</td>
                    <td><span class="badge badge-pallet-type font-mono">${pallet ? pallet.bin_id || 'Transit' : '-'}</span></td>
                    <td class="font-medium text-white">${product ? product.name : b.product_id}</td>
                    <td><strong>${b.qty}</strong> ${product ? product.default_unit : ''}</td>
                    <td class="${expColorClass}">${formatIndoDate(b.expired_date)} ${isExp ? '⚠️' : ''}</td>
                    <td class="font-mono text-slate-400">${b.machine_code}</td>
                    <td class="text-end">
                        <button onclick="openEditDetailModal('boxes', '${b.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-700/50 text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                        <button onclick="deleteDetailItem('boxes', '${b.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-700/50 text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');

        // Mobile list rendering
        if (mobileCardsContainer) {
            if (state.boxes.length === 0) {
                mobileCardsContainer.innerHTML = `<div class="text-center text-slate-500 py-4 text-xs">Tidak ada data ditemukan</div>`;
            } else {
                mobileCardsContainer.innerHTML = state.boxes.map(b => {
                    const pallet = state.pallets.find(p => p.id === b.pallet_id);
                    const product = state.products.find(p => p.id === b.product_id);
                    
                    // Check expiry alerts
                    const now = new Date();
                    const expLimitDate = new Date(now.setMonth(now.getMonth() + (state.settings.expAlertMonths || 18)));
                    const boxExpDate = new Date(b.expired_date);
                    const isExp = boxExpDate <= expLimitDate;
                    const expColorClass = isExp ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-700 dark:text-slate-300';
                    
                    return `
                        <div class="py-3.5 border-b border-slate-200/50 dark:border-slate-800/80 flex flex-col space-y-2">
                            <div class="flex items-center justify-between pb-1.5">
                                <span class="font-bold text-sky-500 font-mono text-xs">${b.id}</span>
                                <div class="flex gap-1.5">
                                    <button onclick="openEditDetailModal('boxes', '${b.id}')" class="btn btn-xs btn-outline-info py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-sky-600 dark:text-sky-400 hover:bg-sky-500/10 text-xxs ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                                    <button onclick="deleteDetailItem('boxes', '${b.id}')" class="btn btn-xs btn-outline-danger py-0.5 px-2 border-slate-300 dark:border-slate-700/50 text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 text-xxs ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                            <div class="space-y-1.5 text-slate-400">
                                <div>
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Palet Penampung</span>
                                    <span class="text-xs text-slate-100 font-medium font-mono">${b.pallet_id}</span>
                                </div>
                                <div class="mt-1.5">
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Lokasi BIN</span>
                                    <span class="badge badge-pallet-type font-mono text-xxs py-1 mt-0.5">${pallet ? pallet.bin_id || 'Transit' : '-'}</span>
                                </div>
                                <div class="mt-1.5">
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Varian Produk</span>
                                    <span class="text-xs text-slate-100 font-medium text-wrap">${product ? product.name : b.product_id}</span>
                                </div>
                                <div class="mt-1.5 flex items-center gap-4">
                                    <div>
                                        <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Jumlah Qty</span>
                                        <span class="text-xs text-slate-100 font-medium">${b.qty} ${product ? product.default_unit : ''}</span>
                                    </div>
                                    <div>
                                        <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Kode Mesin</span>
                                        <span class="text-xs text-slate-100 font-mono">${b.machine_code}</span>
                                    </div>
                                </div>
                                <div class="mt-1.5">
                                    <span class="text-xxs text-slate-500 uppercase tracking-wider block font-semibold">Expired Date</span>
                                    <span class="text-xs ${expColorClass}">${formatIndoDate(b.expired_date)} ${isExp ? '⚠️' : ''}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }
    }

    // Manage Create Button Permission
    const createBtn = document.getElementById('btn-detail-create');
    if (createBtn) {
        if (checkPermission('detail-data', 'create')) {
            createBtn.classList.remove('disabled');
        } else {
            createBtn.classList.add('disabled');
        }
    }
}

function filterDetailTable() {
    const q = document.getElementById('detail-search-input').value.toLowerCase();
    
    // Desktop rows
    const rows = document.querySelectorAll('#detail-tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
    
    // Mobile cards
    const mCards = document.querySelectorAll('#detail-mobile-cards > div');
    mCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
    });
}

// ----------------------------------------------------
// DETAIL CRUD MODALS
// ----------------------------------------------------
function openCreateDetailModal() {
    if (!checkPermission('detail-data', 'create')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses menambah detail inventaris!', 'danger');
        return;
    }

    document.getElementById('detail-form-action').value = 'create';
    document.getElementById('detail-form-target-id').value = '';
    document.getElementById('detailModalLabel').textContent = `Tambah ${activeDetailTab === 'pallets' ? 'Palet' : 'Box'}`;

    buildDetailFormFields();

    const myModal = new bootstrap.Modal(document.getElementById('detailModal'));
    myModal.show();
}

function openEditDetailModal(tabName, id) {
    if (!checkPermission('detail-data', 'update')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses mengubah detail inventaris!', 'danger');
        return;
    }

    document.getElementById('detail-form-action').value = 'edit';
    document.getElementById('detail-form-target-id').value = id;
    document.getElementById('detailModalLabel').textContent = `Edit ${tabName === 'pallets' ? 'Palet' : 'Box'}: ${id}`;

    buildDetailFormFields(id);

    const myModal = new bootstrap.Modal(document.getElementById('detailModal'));
    myModal.show();
}

function buildDetailFormFields(editId = null) {
    const container = document.getElementById('detail-form-fields-container');
    if (!container) return;

    let fieldsHtml = '';

    if (activeDetailTab === 'pallets') {
        const p = editId ? state.pallets.find(item => item.id === editId) : { id: '', pallet_type: 'Loscam', bin_id: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">Barcode Palet (Kode Unik)</label>
                <div class="input-group">
                    <input type="text" id="d-field-id" class="form-control custom-input" placeholder="e.g. PLT-010" value="${p.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400" type="button" onclick="startCameraScan('d-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Jenis Struktur Palet</label>
                <select id="d-field-pallet_type" class="form-select custom-input" required>
                    <option value="Loscam" ${p.pallet_type === 'Loscam' ? 'selected' : ''}>Loscam</option>
                    <option value="Kayu 125x125" ${p.pallet_type === 'Kayu 125x125' ? 'selected' : ''}>Kayu 125x125</option>
                    <option value="Kayu 135x125" ${p.pallet_type === 'Kayu 135x125' ? 'selected' : ''}>Kayu 135x125</option>
                    <option value="Plastik" ${p.pallet_type === 'Plastik' ? 'selected' : ''}>Plastik</option>
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Alokasi BIN Lokasi (Kosongkan jika di Transit)</label>
                <select id="d-field-bin_id" class="form-select custom-input">
                    <option value="">-- AREA TRANSIT / DISPATCH --</option>
                    ${state.bins.map(b => {
                        const palletsInBin = state.pallets.filter(item => item.bin_id === b.id && item.is_active && item.id !== editId);
                        const count = palletsInBin.length;
                        const isFull = state.settings.limitBinPallets && count >= (state.settings.maxPalletsPerBin || 2);
                        return `<option value="${b.id}" ${b.id === p.bin_id ? 'selected' : ''} ${isFull ? 'disabled' : ''}>${b.id} (${count}/2 Palet) ${isFull ? '⚠️ PENUH' : ''}</option>`;
                    }).join('')}
                </select>
            </div>
        `;
    }
    else if (activeDetailTab === 'boxes') {
        const b = editId ? state.boxes.find(item => item.id === editId) : { id: '', pallet_id: '', product_id: '', qty: '', expired_date: '', machine_code: '' };
        fieldsHtml = `
            <div>
                <label class="form-label text-slate-300">Barcode Box (Kode Unik)</label>
                <div class="input-group">
                    <input type="text" id="d-field-id" class="form-control custom-input" placeholder="e.g. BOX-100" value="${b.id}" ${editId ? 'readonly' : 'required'}>
                    ${editId ? '' : `
                    <button class="btn btn-outline-info border-slate-700/50 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400" type="button" onclick="startCameraScan('d-field-id')" title="Scan via Kamera">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                    `}
                </div>
            </div>
            <div>
                <label class="form-label text-slate-300">Palet Penampung</label>
                <select id="d-field-pallet_id" class="form-select custom-input" required>
                    ${state.pallets.map(p => `<option value="${p.id}" ${p.id === b.pallet_id ? 'selected' : ''}>${p.id} (${p.bin_id || 'Transit'})</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Pilih Varian Produk</label>
                <select id="d-field-product_id" class="form-select custom-input" required>
                    ${state.products.map(pr => `<option value="${pr.id}" ${pr.id === b.product_id ? 'selected' : ''}>${pr.name}</option>`).join('')}
                </select>
            </div>
            <div>
                <label class="form-label text-slate-300">Jumlah Qty Barang</label>
                <input type="number" id="d-field-qty" min="1" class="form-control custom-input" value="${b.qty}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Expired Date (Masa Kedaluwarsa)</label>
                <input type="date" id="d-field-expired_date" class="form-control custom-input" value="${b.expired_date}" required>
            </div>
            <div>
                <label class="form-label text-slate-300">Kode Mesin Produksi</label>
                <input type="text" id="d-field-machine_code" class="form-control custom-input" placeholder="e.g. 1I0PB01" value="${b.machine_code}" required>
            </div>
        `;
    }

    container.innerHTML = fieldsHtml;
}

// ----------------------------------------------------
// CAMERA BARCODE SCANNER LOGIC FOR CRUD FORMS
// ----------------------------------------------------
let activeCameraTargetInputId = null;
let scannerStream = null;
let cameraScanTimeout = null;

function startCameraScan(targetInputId) {
    activeCameraTargetInputId = targetInputId;
    
    // Clear any previous timeouts
    if (cameraScanTimeout) {
        clearTimeout(cameraScanTimeout);
    }

    // Open Camera scanner modal
    const modalEl = document.getElementById('cameraScannerModal');
    if (!modalEl) return;
    
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Access system camera
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
        .then(stream => {
            scannerStream = stream;
            const video = document.getElementById('scanner-video');
            if (video) {
                video.srcObject = stream;
                video.play().catch(e => console.warn("Video play interrupted", e));
            }
        })
        .catch(err => {
            console.warn("Physical camera stream not available:", err.message);
            showToast("Kamera fisik tidak terdeteksi. Menjalankan simulator pemindaian...", "info");
        });

    // Auto-detect and simulate scan success after 2.5s for hyper-realistic feel
    cameraScanTimeout = setTimeout(() => {
        simulateCameraScanCapture();
    }, 2500);
}

function stopCameraScan() {
    if (cameraScanTimeout) {
        clearTimeout(cameraScanTimeout);
        cameraScanTimeout = null;
    }

    if (scannerStream) {
        scannerStream.getTracks().forEach(track => track.stop());
        scannerStream = null;
    }

    const video = document.getElementById('scanner-video');
    if (video) {
        video.srcObject = null;
    }

    const modalEl = document.getElementById('cameraScannerModal');
    if (modalEl) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
}

function simulateCameraScanCapture() {
    if (!activeCameraTargetInputId) return;
    const inputEl = document.getElementById(activeCameraTargetInputId);
    if (inputEl) {
        let generatedCode = '';
        if (activeCameraTargetInputId === 'm-field-id') {
            // Master Data Tabs
            if (activeMasterTab === 'offices') {
                let nextNum = state.offices.length + 1;
                while (true) {
                    generatedCode = `KTR-${String(nextNum).padStart(3, '0')}`;
                    if (!state.offices.find(o => o.id.toUpperCase() === generatedCode)) break;
                    nextNum++;
                }
            } else if (activeMasterTab === 'branches') {
                let nextNum = state.branches.length + 1;
                while (true) {
                    generatedCode = `CBG-${String(nextNum).padStart(3, '0')}`;
                    if (!state.branches.find(b => b.id.toUpperCase() === generatedCode)) break;
                    nextNum++;
                }
            } else if (activeMasterTab === 'warehouses') {
                let nextNum = state.warehouses.length + 1;
                while (true) {
                    generatedCode = `GDG-${String(nextNum).padStart(3, '0')}`;
                    if (!state.warehouses.find(w => w.id.toUpperCase() === generatedCode)) break;
                    nextNum++;
                }
            } else if (activeMasterTab === 'racks') {
                let nextNum = state.racks.length + 1;
                while (true) {
                    generatedCode = `RK-${String(nextNum).padStart(3, '0')}`;
                    if (!state.racks.find(r => r.id.toUpperCase() === generatedCode)) break;
                    nextNum++;
                }
            } else if (activeMasterTab === 'bins') {
                const rackSelect = document.getElementById('m-field-rack_id');
                const rackId = rackSelect ? rackSelect.value : (state.racks[0] ? state.racks[0].id : 'RK-A');
                const colVal = document.getElementById('m-field-column_no') ? document.getElementById('m-field-column_no').value || '01' : '01';
                const lvlVal = document.getElementById('m-field-level_no') ? document.getElementById('m-field-level_no').value || '1' : '1';
                generatedCode = `${rackId}.${String(colVal).padStart(2, '0')}.${lvlVal}`;
            } else if (activeMasterTab === 'products') {
                let nextNum = state.products.length + 1;
                while (true) {
                    generatedCode = `PRD-${String(nextNum).padStart(3, '0')}`;
                    if (!state.products.find(p => p.id.toUpperCase() === generatedCode)) break;
                    nextNum++;
                }
            }
        } else {
            // Detail Data Tabs
            if (activeDetailTab === 'pallets') {
                let nextNum = 1;
                while (true) {
                    const code = `PLT-${String(nextNum).padStart(3, '0')}`;
                    if (!state.pallets.find(p => p.id.toUpperCase() === code)) {
                        generatedCode = code;
                        break;
                    }
                    nextNum++;
                }
            } else if (activeDetailTab === 'boxes') {
                let nextNum = 1;
                while (true) {
                    const code = `BOX-${String(nextNum).padStart(3, '0')}`;
                    if (!state.boxes.find(b => b.id.toUpperCase() === code)) {
                        generatedCode = code;
                        break;
                    }
                    nextNum++;
                }
            }
        }
        
        inputEl.value = generatedCode;
        showToast(`Barcode terdeteksi: ${generatedCode}`, 'success');
        
        // Trigger manual input/change dispatch
        inputEl.dispatchEvent(new Event('input'));
        inputEl.dispatchEvent(new Event('change'));
    }
    stopCameraScan();
}

function generateAutoCode(targetInputId) {
    const inputEl = document.getElementById(targetInputId);
    if (!inputEl) return;
    
    let code = '';
    if (activeMasterTab === 'offices') {
        let nextNum = state.offices.length + 1;
        while (true) {
            code = `KTR-${String(nextNum).padStart(3, '0')}`;
            if (!state.offices.find(o => o.id.toUpperCase() === code)) break;
            nextNum++;
        }
    } else if (activeMasterTab === 'branches') {
        let nextNum = state.branches.length + 1;
        while (true) {
            code = `CBG-${String(nextNum).padStart(3, '0')}`;
            if (!state.branches.find(b => b.id.toUpperCase() === code)) break;
            nextNum++;
        }
    } else if (activeMasterTab === 'warehouses') {
        let nextNum = state.warehouses.length + 1;
        while (true) {
            code = `GDG-${String(nextNum).padStart(3, '0')}`;
            if (!state.warehouses.find(w => w.id.toUpperCase() === code)) break;
            nextNum++;
        }
    } else if (activeMasterTab === 'racks') {
        let nextNum = state.racks.length + 1;
        while (true) {
            code = `RK-${String(nextNum).padStart(3, '0')}`;
            if (!state.racks.find(r => r.id.toUpperCase() === code)) break;
            nextNum++;
        }
    } else if (activeMasterTab === 'bins') {
        const rackSelect = document.getElementById('m-field-rack_id');
        const rackId = rackSelect ? rackSelect.value : (state.racks[0] ? state.racks[0].id : 'RK-A');
        const colVal = document.getElementById('m-field-column_no') ? document.getElementById('m-field-column_no').value || '01' : '01';
        const lvlVal = document.getElementById('m-field-level_no') ? document.getElementById('m-field-level_no').value || '1' : '1';
        code = `${rackId}.${String(colVal).padStart(2, '0')}.${lvlVal}`;
    } else if (activeMasterTab === 'products') {
        let nextNum = state.products.length + 1;
        while (true) {
            code = `PRD-${String(nextNum).padStart(3, '0')}`;
            if (!state.products.find(p => p.id.toUpperCase() === code)) break;
            nextNum++;
        }
    }
    
    inputEl.value = code;
    showToast(`Kode otomatis di-generate: ${code}`, 'success');
    inputEl.dispatchEvent(new Event('input'));
    inputEl.dispatchEvent(new Event('change'));
}

function handleDetailFormSubmit(e) {
    e.preventDefault();
    const action = document.getElementById('detail-form-action').value;
    const targetId = document.getElementById('detail-form-target-id').value;

    const data = {};
    if (document.getElementById('d-field-id')) data.id = document.getElementById('d-field-id').value.trim();
    if (document.getElementById('d-field-pallet_type')) data.pallet_type = document.getElementById('d-field-pallet_type').value;
    if (document.getElementById('d-field-bin_id')) data.bin_id = document.getElementById('d-field-bin_id').value;
    if (document.getElementById('d-field-pallet_id')) data.pallet_id = document.getElementById('d-field-pallet_id').value;
    if (document.getElementById('d-field-product_id')) data.product_id = document.getElementById('d-field-product_id').value;
    if (document.getElementById('d-field-qty')) data.qty = parseInt(document.getElementById('d-field-qty').value);
    if (document.getElementById('d-field-expired_date')) data.expired_date = document.getElementById('d-field-expired_date').value;
    if (document.getElementById('d-field-machine_code')) data.machine_code = document.getElementById('d-field-machine_code').value.trim();

    // Capacity checks for Pallet Inbound/Edit to BIN
    if (activeDetailTab === 'pallets' && data.bin_id !== '') {
        const binPalletsCount = state.pallets.filter(p => p.bin_id === data.bin_id && p.is_active && p.id !== targetId).length;
        if (state.settings.limitBinPallets && binPalletsCount >= (state.settings.maxPalletsPerBin || 2)) {
            showToast(`Gagal: Lokasi BIN '${data.bin_id}' sudah penuh (Maksimal 2 Palet)!`, 'danger');
            writeAuditLog(state.session.currentUser, 'TRANSFER', 'FAILED', `Gagal memindahkan palet ke ${data.bin_id}: Kapasitas BIN Penuh.`);
            return;
        }
    }

    const list = state[activeDetailTab];

    if (action === 'create') {
        const duplicate = list.find(item => item.id.toLowerCase() === data.id.toLowerCase());
        if (duplicate) {
            showToast(`Gagal: Kode '${data.id}' sudah terdaftar!`, 'danger');
            return;
        }
        
        if (activeDetailTab === 'pallets') {
            data.is_active = true;
        }

        list.push(data);
        writeAuditLog(state.session.currentUser, 'DETAIL', 'CREATE', `Menambahkan detail ${activeDetailTab} baru: ${data.id}`);
    } else {
        const index = list.findIndex(item => item.id === targetId);
        if (index !== -1) {
            const oldBin = list[index].bin_id;
            list[index] = { ...list[index], ...data };
            
            // If bin changed, log as move
            if (activeDetailTab === 'pallets' && oldBin !== data.bin_id) {
                writeAuditLog(state.session.currentUser, 'TRANSFER', 'MOVE', `Memindahkan Palet ${targetId} dari ${oldBin || 'Transit'} ke ${data.bin_id || 'Transit'}`);
            } else {
                writeAuditLog(state.session.currentUser, 'DETAIL', 'UPDATE', `Mengubah detail ${activeDetailTab} ID: ${targetId}`);
            }
        }
    }

    saveState();
    loadDetailDataTab();

    const modalEl = document.getElementById('detailModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    showToast(`Data detail ${activeDetailTab === 'pallets' ? 'Palet' : 'Box'} berhasil disimpan!`, 'success');
}

function deleteDetailItem(tabName, id) {
    if (!checkPermission('detail-data', 'delete')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses menghapus data detail!', 'danger');
        return;
    }

    // Pallet dependency check (cannot delete pallet if box still loaded)
    if (tabName === 'pallets') {
        const dependent = state.boxes.find(b => b.pallet_id === id);
        if (dependent) {
            showToast(`Gagal: Palet '${id}' tidak bisa dihapus karena masih memuat Box '${dependent.id}'!`, 'danger');
            return;
        }
    }

    if (confirm(`Apakah Anda yakin ingin menghapus detail '${id}' dari sistem?`)) {
        state[tabName] = state[tabName].filter(item => item.id !== id);
        writeAuditLog(state.session.currentUser, 'DETAIL', 'DELETE', `Menghapus detail ${tabName} ID: ${id}`);
        saveState();
        loadDetailDataTab();
        showToast('Data detail berhasil dihapus!', 'success');
    }
}

// ----------------------------------------------------
// BARCODE SCANNER SIMULATOR LOGIC
// ----------------------------------------------------
function loadScannerTab() {
    const palletContainer = document.getElementById('scan-shortcut-pallets');
    const binContainer = document.getElementById('scan-shortcut-bins');
    const productContainer = document.getElementById('scan-shortcut-products');
    const boxContainer = document.getElementById('scan-shortcut-boxes');
    if (!palletContainer || !binContainer || !productContainer) return;

    // Load active simulator buttons for easy testing
    palletContainer.innerHTML = state.pallets.slice(0, 4).map(p => `
        <button onclick="triggerScannerShortcut('${p.id}')" class="btn btn-scanner-pallet text-xxs font-mono py-1 px-2 rounded">${p.id}</button>
    `).join('');

    binContainer.innerHTML = state.bins.slice(0, 4).map(b => `
        <button onclick="triggerScannerShortcut('${b.id}')" class="btn btn-scanner-bin text-xxs font-mono py-1 px-2 rounded">${b.id}</button>
    `).join('');

    productContainer.innerHTML = state.products.map(pr => `
        <button onclick="triggerScannerShortcut('${pr.id}')" class="btn btn-scanner-product text-xxs font-mono py-1 px-2 rounded">${pr.id}</button>
    `).join('');

    if (boxContainer) {
        boxContainer.innerHTML = state.boxes.slice(0, 4).map(bx => `
            <button onclick="triggerScannerShortcut('${bx.id}')" class="btn btn-scanner-box text-xxs font-mono py-1 px-2 rounded">${bx.id}</button>
        `).join('');
    }
}

function triggerScannerShortcut(val) {
    document.getElementById('scanner-manual-input').value = val;
    executeScan();
}

function playScanBeep() {
    if (!state.settings.scanBeep) return;
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.type = 'sine';
        oscillator.frequency.value = 1000;
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.15);
    } catch (e) {
        console.warn('Audio Context tidak didukung oleh browser ini sebelum interaksi.', e);
    }
}

function executeScan() {
    const inputEl = document.getElementById('scanner-manual-input');
    if (!inputEl) return;
    const code = inputEl.value.trim().toUpperCase();
    if (!code) {
        showToast('Masukkan kode barcode atau klik salah satu shortcut!', 'warning');
        return;
    }

    executeScanWithCode(code);

    // Auto clear input if configured
    if (state.settings.scanAutoClear) {
        inputEl.value = '';
    }
}

function executeScanWithCode(code) {
    playScanBeep();
    const resultBody = document.getElementById('scanner-result-body');
    if (!resultBody) return;

    let resultHtml = '';
    
    // 1. Match PALLET regex (starts with PLT-)
    if (code.startsWith('PLT-')) {
        const pallet = state.pallets.find(p => p.id === code);
        if (!pallet) {
            resultHtml = `
                <div class="alert alert-danger text-xxs">Barcode Palet <strong>${code}</strong> tidak ditemukan di database.</div>
            `;
        } else {
            const boxes = state.boxes.filter(b => b.pallet_id === code);
            const totalQty = boxes.reduce((acc, curr) => acc + curr.qty, 0);
            resultHtml = `
                <div class="p-3 bg-slate-900 rounded-lg border border-slate-800 space-y-2 mb-3">
                    <div class="flex justify-between"><span>ID Palet:</span> <span class="font-bold text-sky-400 font-mono">${pallet.id}</span></div>
                    <div class="flex justify-between"><span>Jenis Palet:</span> <span class="text-white">${pallet.pallet_type}</span></div>
                    <div class="flex justify-between"><span>Lokasi BIN:</span> <span class="badge bg-sky-500/10 text-sky-400 font-mono">${pallet.bin_id || 'Transit'}</span></div>
                    <div class="flex justify-between"><span>Status Palet:</span> <span class="text-emerald-400 font-bold">${pallet.is_active ? 'Aktif' : 'Non-aktif'}</span></div>
                </div>
                <h4 class="text-xs font-semibold text-white mb-2">Daftar Muatan Box (${boxes.length} Box terdeteksi)</h4>
                <div class="overflow-y-auto max-h-40">
                    <table class="table table-dark table-sm custom-table text-xxs">
                        <thead>
                            <tr>
                                <th>Barcode Box</th>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Exp</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${boxes.length === 0 ? '<tr><td colspan="4" class="text-center text-slate-500 py-2">Kosong</td></tr>' : 
                            boxes.map(box => {
                                const prod = state.products.find(p => p.id === box.product_id);
                                return `
                                    <tr>
                                        <td class="font-mono">${box.id}</td>
                                        <td>${prod ? prod.name : box.product_id}</td>
                                        <td>${box.qty}</td>
                                        <td>${box.expired_date}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }
    }
    // 2. Match BOX regex (starts with BOX-)
    else if (code.startsWith('BOX-')) {
        const box = state.boxes.find(b => b.id === code);
        if (!box) {
            resultHtml = `
                <div class="alert alert-danger text-xxs">Barcode Box <strong>${code}</strong> tidak ditemukan di database.</div>
            `;
        } else {
            const prod = state.products.find(p => p.id === box.product_id);
            const pallet = state.pallets.find(p => p.id === box.pallet_id);
            resultHtml = `
                <div class="p-3 bg-slate-900 rounded-lg border border-slate-800 space-y-2 mb-3">
                    <div class="flex justify-between"><span>Barcode Box:</span> <span class="font-bold text-indigo-400 font-mono">${box.id}</span></div>
                    <div class="flex justify-between"><span>Produk (SKU):</span> <span class="text-white font-medium">${prod ? prod.name : box.product_id} (${box.product_id})</span></div>
                    <div class="flex justify-between"><span>Jumlah Qty:</span> <span class="text-white font-bold">${box.qty} ${prod ? prod.default_unit : ''}</span></div>
                    <div class="flex justify-between"><span>Palet Penampung:</span> <span class="font-bold text-sky-400 font-mono">${box.pallet_id}</span></div>
                    <div class="flex justify-between"><span>Lokasi BIN:</span> <span class="badge bg-sky-500/10 text-sky-400 font-mono">${(pallet && pallet.bin_id) ? pallet.bin_id : 'Transit'}</span></div>
                    <div class="flex justify-between"><span>Tanggal Expired:</span> <span class="text-white">${box.expired_date}</span></div>
                    <div class="flex justify-between"><span>Mesin Produksi:</span> <span class="text-white font-mono">${box.machine_code || '-'}</span></div>
                </div>
            `;
        }
    }
    // 3. Match BIN regex (letter.number.number)
    else if (/^[A-Z]\.[0-9]+\.[0-9]+$/.test(code)) {
        const bin = state.bins.find(b => b.id === code);
        if (!bin) {
            resultHtml = `<div class="alert alert-danger text-xxs">Alamat BIN <strong>${code}</strong> tidak ditemukan di database.</div>`;
        } else {
            const rack = state.racks.find(r => r.id === bin.rack_id);
            const palletsInBin = state.pallets.filter(p => p.bin_id === code && p.is_active);
            resultHtml = `
                <div class="p-3 bg-slate-900 rounded-lg border border-slate-800 space-y-2 mb-3">
                    <div class="flex justify-between"><span>Koordinat BIN:</span> <span class="font-bold text-amber-500 font-mono">${bin.id}</span></div>
                    <div class="flex justify-between"><span>ID Rak:</span> <span class="text-white">${rack ? `Rak ${rack.rack_name}` : bin.rack_id}</span></div>
                    <div class="flex justify-between"><span>Tingkat / SAP:</span> <span class="text-white">SAP ${bin.level_no}</span></div>
                    <div class="flex justify-between"><span>Okupansi Kapasitas:</span> <span class="text-white">${palletsInBin.length} / ${bin.max_pallet} Palet</span></div>
                </div>
                <h4 class="text-xs font-semibold text-white mb-2">Palet di Lokasi Ini</h4>
                <div class="space-y-1.5">
                    ${palletsInBin.length === 0 ? '<p class="text-emerald-400 text-center font-semibold py-2">Kosong (Siap Menerima Palet)</p>' : 
                    palletsInBin.map(p => `
                        <div class="p-2 bg-slate-800 rounded border border-slate-700/50 flex justify-between items-center">
                            <div>
                                <span class="font-mono font-bold text-sky-400">${p.id}</span>
                                <span class="text-xxs text-slate-400 ml-2">Type: ${p.pallet_type}</span>
                            </div>
                            <span class="badge bg-sky-500/10 text-sky-400 text-xxs font-normal">Active</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }
    }
    // 4. Match PRODUCT regex (starts with PRD-)
    else if (code.startsWith('PRD-')) {
        const prod = state.products.find(p => p.id === code);
        if (!prod) {
            resultHtml = `<div class="alert alert-danger text-xxs">Produk SKU <strong>${code}</strong> tidak ditemukan di database.</div>`;
        } else {
            const boxesOfProd = state.boxes.filter(b => b.product_id === code);
            const totalStock = boxesOfProd.reduce((acc, curr) => acc + curr.qty, 0);
            
            // Map unique bin locations
            const locations = [];
            boxesOfProd.forEach(box => {
                const pallet = state.pallets.find(p => p.id === box.pallet_id);
                if (pallet && pallet.bin_id && !locations.includes(pallet.bin_id)) {
                    locations.push(pallet.bin_id);
                }
            });

            resultHtml = `
                <div class="p-3 bg-slate-900 rounded-lg border border-slate-800 space-y-2 mb-3">
                    <div class="flex justify-between"><span>SKU Produk:</span> <span class="font-bold text-emerald-400 font-mono">${prod.id}</span></div>
                    <div class="flex justify-between"><span>Nama Produk:</span> <span class="text-white font-medium">${prod.name}</span></div>
                    <div class="flex justify-between"><span>Satuan Barang:</span> <span class="text-white">${prod.default_unit}</span></div>
                    <div class="flex justify-between"><span>Total Stok:</span> <span class="text-white font-bold">${totalStock} ${prod.default_unit}</span></div>
                </div>
                <h4 class="text-xs font-semibold text-white mb-2">Sebaran Lokasi BIN Fisik</h4>
                <div class="flex flex-wrap gap-1.5">
                    ${locations.length === 0 ? '<span class="text-rose-400">Tidak Terdeteksi di Lokasi Rak Mana Pun</span>' : 
                    locations.map(loc => `<span class="badge bg-sky-500/10 text-sky-400 font-mono text-xxs py-1.5 px-2.5 border border-sky-500/20">${loc}</span>`).join('')}
                </div>
            `;
        }
    } else {
        resultHtml = `
            <div class="alert alert-warning text-xxs">
                <strong>Format Barcode Salah:</strong> Kode <code>${code}</code> tidak sesuai dengan kriteria logistik. <br>
                Gunakan format awalan <code>PLT-</code> (Palet), format <code>BOX-</code> (Box), format koordinat <code>A.B.C</code> (BIN), atau SKU <code>PRD-</code> (Produk).
            </div>
        `;
    }

    resultBody.innerHTML = resultHtml;
    
    // Show Modal
    const scanModal = new bootstrap.Modal(document.getElementById('scannerResultModal'));
    scanModal.show();
}

function executeDashboardScan(inputId) {
    const inputEl = document.getElementById(inputId);
    if (!inputEl) return;
    let code = inputEl.value.trim().toUpperCase();
    if (!code) {
        // Auto-populate with valid mock database barcode for demo scanning simulation
        if (inputId === 'dash-scan-prd') code = 'PRD-001';
        else if (inputId === 'dash-scan-bin') code = 'B.10.2';
        else if (inputId === 'dash-scan-plt') code = 'PLT-001';
        else if (inputId === 'dash-scan-box') code = 'BOX-001';
        
        if (code) {
            inputEl.value = code;
            showToast(`Simulasi Scan: Menggunakan contoh barcode '${code}'`, 'info');
        } else {
            showToast('Silakan masukkan kode barcode terlebih dahulu!', 'warning');
            return;
        }
    }
    executeScanWithCode(code);
    if (state.settings && state.settings.scanAutoClear) {
        // Wait 1.5s so the user has time to see the barcode value in the input box
        setTimeout(() => {
            if (inputEl.value === code) {
                inputEl.value = '';
            }
        }, 1500);
    }
}

// Helper: build impersonate button HTML
function _buildImpersonateBtn(username, canImpersonate, isSelf, style) {
    if (!canImpersonate || username === 'rivaldy' || isSelf) return '';
    if (style === 'mobile') {
        return `<button onclick="loginAs('${username}')" class="btn btn-xs text-xxs py-1 px-2 rounded flex items-center gap-1 font-semibold" style="background:rgba(245,158,11,0.12);color:#d97706;border:1px solid rgba(245,158,11,0.3)">
                    <i class="bi bi-incognito"></i> Login As
                </button>`;
    }
    return `<button onclick="loginAs('${username}')" class="btn btn-xs btn-outline-warning text-xxs py-1 px-2.5 border-amber-500/20 text-amber-500 hover:bg-amber-500/10 rounded flex items-center gap-1">
                <i class="bi bi-incognito"></i> Login As
            </button>`;
}

// Helper: render a single desktop table row
function _renderUserRow(u, ctx) {
    const { loggedInUser, canImpersonate, canUpdate, canDelete } = ctx;
    const isSelf = u.username === loggedInUser.username;
    const activeBadge = u.is_active
        ? '<span class="badge badge-status-empty">Aktif</span>'
        : '<span class="badge badge-status-full">Non-aktif</span>';
    const impersonateBtn = _buildImpersonateBtn(u.username, canImpersonate, isSelf, 'desktop');
    return `
        <tr>
            <td class="font-bold text-white">${u.name}${isSelf ? ' <span class="text-xxs text-slate-500">(Anda)</span>' : ''}</td>
            <td class="font-mono text-slate-400">${u.username}</td>
            <td><span class="badge badge-bin-location font-mono text-xxs py-1 px-2">${u.role_id}</span></td>
            <td>Shift ${u.shift}</td>
            <td>${activeBadge}</td>
            <td class="text-end">
                <div class="d-inline-flex gap-1 align-items-center">
                    ${impersonateBtn}
                    <button onclick="openEditUserModal('${u.username}')" class="btn btn-xs btn-outline-info py-1 px-2 border-slate-700 text-sky-400 hover:bg-sky-500/10 ${canUpdate ? '' : 'disabled'}"><i class="bi bi-pencil-square"></i></button>
                    <button onclick="deleteUser('${u.username}')" class="btn btn-xs btn-outline-danger py-1 px-2 border-slate-700 text-rose-400 hover:bg-rose-500/10 ${canDelete ? '' : 'disabled'}"><i class="bi bi-trash"></i></button>
                </div>
            </td>
        </tr>`;
}

// Helper: render a single mobile user card
function _renderUserCard(u, ctx) {
    const { loggedInUser, canImpersonate, canUpdate, canDelete } = ctx;
    const isSelf = u.username === loggedInUser.username;

    // Status badge
    const statusBadge = u.is_active
        ? '<span class="badge badge-status-empty" style="font-size:0.65rem;padding:2px 8px">Aktif</span>'
        : '<span class="badge badge-status-full" style="font-size:0.65rem;padding:2px 8px">Non-aktif</span>';

    // Shortened role label for display
    const roleLabel = u.role_id === 'Warehouse Supervisor' ? 'Supervisor'
        : u.role_id === 'Operator Gudang' ? 'Operator'
        : u.role_id;

    // Impersonate button
    const impersonateBtn = _buildImpersonateBtn(u.username, canImpersonate, isSelf, 'mobile');

    // Avatar: 2 initial letters, colour by role
    const initials = u.name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    const avatarColor = u.role_id === 'Super Admin'
        ? 'background:rgba(239,68,68,0.12);color:#dc2626;border-color:rgba(239,68,68,0.3)'
        : u.role_id === 'Warehouse Supervisor'
            ? 'background:rgba(245,158,11,0.12);color:#d97706;border-color:rgba(245,158,11,0.3)'
            : 'background:rgba(14,165,233,0.12);color:#0284c7;border-color:rgba(14,165,233,0.3)';

    return `
        <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/40 p-3.5 flex flex-col gap-3">

            <!-- Top row: Avatar · Name · Action buttons -->
            <div class="flex items-start gap-3">

                <!-- Avatar -->
                <div class="flex-shrink-0 w-10 h-10 rounded-full border flex items-center justify-center font-bold text-sm select-none" style="${avatarColor}">${initials}</div>

                <!-- Name & username -->
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="font-semibold text-slate-900 dark:text-white leading-tight" style="font-size:0.8125rem">
                        ${u.name}${isSelf ? '&nbsp;<span style="font-size:0.65rem;color:#94a3b8;font-weight:400">(Anda)</span>' : ''}
                    </div>
                    <div class="text-slate-500 mt-0.5" style="font-size:0.68rem;font-family:monospace">@${u.username}</div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-0.5 flex-shrink-0">
                    ${impersonateBtn}
                    <button onclick="openEditUserModal('${u.username}')" title="Edit"
                        class="w-8 h-8 rounded-lg flex items-center justify-center
                        ${canUpdate ? 'text-sky-500 hover:bg-sky-500/10 active:bg-sky-500/20' : 'text-slate-400 pointer-events-none opacity-40'}"
                        style="font-size:0.8rem">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button onclick="deleteUser('${u.username}')" title="Hapus"
                        class="w-8 h-8 rounded-lg flex items-center justify-center
                        ${canDelete ? 'text-rose-500 hover:bg-rose-500/10 active:bg-rose-500/20' : 'text-slate-400 pointer-events-none opacity-40'}"
                        style="font-size:0.8rem">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-100 dark:border-slate-700/50"></div>

            <!-- Metadata rows: role on top, shift+status below -->
            <div class="flex flex-col gap-1.5">

                <!-- Row 1: Role label + badge (full width, no overflow) -->
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 dark:text-slate-500 flex-shrink-0" style="font-size:0.6rem;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;min-width:2.6rem">Role</span>
                    <span class="badge badge-bin-location" style="font-size:0.67rem;padding:2px 8px;font-weight:500">${roleLabel}</span>
                </div>

                <!-- Row 2: Shift (left) + Status (right) -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 flex-1">
                        <span class="text-slate-400 dark:text-slate-500 flex-shrink-0" style="font-size:0.6rem;font-weight:600;letter-spacing:0.04em;text-transform:uppercase;min-width:2.6rem">Shift</span>
                        <span class="font-medium text-slate-700 dark:text-slate-200 flex items-center gap-1" style="font-size:0.72rem">
                            <i class="bi bi-clock" style="font-size:0.62rem;color:#94a3b8"></i> Shift ${u.shift}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-slate-400 dark:text-slate-500" style="font-size:0.6rem;font-weight:600;letter-spacing:0.04em;text-transform:uppercase">Status</span>
                        ${statusBadge}
                    </div>
                </div>

            </div>

        </div>`;
}

function loadUsersTab() {
    const tbody = document.getElementById('users-table-body');
    if (!tbody) return;
    // Reset filters on first load
    filterUsersTable();

    // Toggle Create Button
    const createBtn = document.getElementById('btn-user-create');
    if (createBtn) {
        if (checkPermission('users', 'create')) createBtn.classList.remove('disabled');
        else createBtn.classList.add('disabled');
    }
}

function filterUsersTable() {
    const tbody = document.getElementById('users-table-body');
    const mobileContainer = document.getElementById('users-mobile-cards');
    const resultCount = document.getElementById('users-result-count');
    if (!tbody) return;

    const query = (document.getElementById('users-search-input')?.value || '').toLowerCase().trim();
    const roleFilter = (document.getElementById('users-filter-role')?.value || '');
    const statusFilter = (document.getElementById('users-filter-status')?.value || '');

    const loggedInUser = state.users.find(u => u.username === state.session.currentUser);
    const originalUser = state.users.find(u => u.username === state.session.originalUser);
    const ctx = {
        loggedInUser,
        canImpersonate: originalUser.role_id === 'Super Admin',
        canUpdate: checkPermission('users', 'update'),
        canDelete: checkPermission('users', 'delete'),
    };

    const filtered = state.users.filter(u => {
        const matchQuery = !query || u.name.toLowerCase().includes(query) || u.username.toLowerCase().includes(query);
        const matchRole = !roleFilter || u.role_id === roleFilter;
        const matchStatus = !statusFilter
            || (statusFilter === 'aktif' && u.is_active)
            || (statusFilter === 'nonaktif' && !u.is_active);
        return matchQuery && matchRole && matchStatus;
    });

    // Update result count
    if (resultCount) {
        if (query || roleFilter || statusFilter) {
            resultCount.textContent = `Menampilkan ${filtered.length} dari ${state.users.length} user`;
        } else {
            resultCount.textContent = `Total ${state.users.length} user terdaftar`;
        }
    }

    // Desktop table
    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-slate-500 py-6 text-xs">Tidak ada user yang cocok dengan pencarian</td></tr>`;
    } else {
        tbody.innerHTML = filtered.map(u => _renderUserRow(u, ctx)).join('');
    }

    // Mobile cards
    if (mobileContainer) {
        if (filtered.length === 0) {
            mobileContainer.innerHTML = `<div class="text-center text-slate-500 py-8 text-xs">Tidak ada user yang cocok dengan pencarian</div>`;
        } else {
            mobileContainer.innerHTML = filtered.map(u => _renderUserCard(u, ctx)).join('');
        }
    }
}


function openCreateUserModal() {
    if (!checkPermission('users', 'create')) {
        showToast('Izin Ditolak: Anda tidak memiliki hak akses menambah user baru!', 'danger');
        return;
    }

    document.getElementById('user-form-action').value = 'create';
    document.getElementById('user-form-username-old').value = '';
    document.getElementById('userModalLabel').textContent = 'Tambah User / PIC Baru';
    
    // Clear inputs
    document.getElementById('user-input-name').value = '';
    document.getElementById('user-input-username').value = '';
    document.getElementById('user-input-username').readOnly = false;
    document.getElementById('user-input-password').value = '';
    document.getElementById('user-input-password').required = true;
    document.getElementById('user-input-role').value = 'Operator Gudang';
    document.getElementById('user-input-shift').value = '1';
    document.getElementById('user-input-active').checked = true;

    const myModal = new bootstrap.Modal(document.getElementById('userModal'));
    myModal.show();
}

function openEditUserModal(username) {
    if (!checkPermission('users', 'update')) {
        showToast('Izin Ditolak: Anda tidak memiliki hak akses mengedit user!', 'danger');
        return;
    }

    const u = state.users.find(item => item.username === username);
    if (!u) return;

    document.getElementById('user-form-action').value = 'edit';
    document.getElementById('user-form-username-old').value = username;
    document.getElementById('userModalLabel').textContent = `Ubah User PIC: ${username}`;

    document.getElementById('user-input-name').value = u.name;
    document.getElementById('user-input-username').value = u.username;
    document.getElementById('user-input-username').readOnly = true;
    document.getElementById('user-input-password').value = u.password;
    document.getElementById('user-input-password').required = false;
    document.getElementById('user-input-role').value = u.role_id;
    document.getElementById('user-input-shift').value = u.shift;
    document.getElementById('user-input-active').checked = u.is_active;

    const myModal = new bootstrap.Modal(document.getElementById('userModal'));
    myModal.show();
}

function handleUserFormSubmit(e) {
    e.preventDefault();
    const action = document.getElementById('user-form-action').value;
    const oldUsername = document.getElementById('user-form-username-old').value;

    const name = document.getElementById('user-input-name').value.trim();
    const username = document.getElementById('user-input-username').value.trim().toLowerCase();
    const password = document.getElementById('user-input-password').value;
    const role_id = document.getElementById('user-input-role').value;
    const shift = parseInt(document.getElementById('user-input-shift').value);
    const is_active = document.getElementById('user-input-active').checked;

    if (action === 'create') {
        const duplicate = state.users.find(item => item.username === username);
        if (duplicate) {
            showToast(`Gagal: Username '${username}' sudah terdaftar!`, 'danger');
            return;
        }
        state.users.push({ name, username, password, role_id, shift, is_active });
        writeAuditLog(state.session.currentUser, 'RBAC', 'CREATE', `Mendaftarkan user PIC baru: ${name} (${username}) dengan peran ${role_id}`);
    } else {
        const index = state.users.findIndex(item => item.username === oldUsername);
        if (index !== -1) {
            state.users[index] = { ...state.users[index], name, password, role_id, shift, is_active };
            writeAuditLog(state.session.currentUser, 'RBAC', 'UPDATE', `Mengubah profil user PIC: ${oldUsername}`);
        }
    }

    saveState();
    loadUsersTab();

    const modalEl = document.getElementById('userModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();

    showToast('Data user berhasil disimpan!', 'success');
}

function deleteUser(username) {
    if (!checkPermission('users', 'delete')) {
        showToast('Izin Ditolak: Anda tidak memiliki hak akses menghapus user!', 'danger');
        return;
    }

    if (username === 'rivaldy') {
        showToast('Gagal: User Admin utama sistem tidak boleh dihapus!', 'danger');
        return;
    }

    if (username === state.session.currentUser) {
        showToast('Gagal: Anda tidak boleh menghapus akun Anda sendiri saat sedang login!', 'danger');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus akun user '${username}'?`)) {
        state.users = state.users.filter(item => item.username !== username);
        writeAuditLog(state.session.currentUser, 'RBAC', 'DELETE', `Menghapus akun user: ${username}`);
        saveState();
        loadUsersTab();
        showToast('Akun user berhasil dihapus!', 'success');
    }
}

// ----------------------------------------------------
// ROLE & PERMISSIONS (RBAC MATRIX) LOGIC
// ----------------------------------------------------
let activeRbacRole = 'Super Admin';

function loadRbacTab() {
    const select = document.getElementById('rbac-role-select');
    if (select) {
        select.value = activeRbacRole;
    }
    loadRolePermissionsMatrix(activeRbacRole);
}

function loadRolePermissionsMatrix(roleId) {
    activeRbacRole = roleId;
    const tbody = document.getElementById('rbac-matrix-tbody');
    if (!tbody) return;

    // Filter modules
    const modules = [
        { key: 'dashboard', label: 'Dashboard & Chart' },
        { key: 'master-data', label: 'Data Master (Kantor/Rak/BIN)' },
        { key: 'detail-data', label: 'Data Detail (Palet & Box)' },
        { key: 'scanner', label: 'Barcode Scanner Simulator' },
        { key: 'users', label: 'User Management' },
        { key: 'rbac', label: 'Role & Matriks RBAC' },
        { key: 'logs', label: 'Audit Trail Logs' },
        { key: 'settings', label: 'Pengaturan Sistem' },
        { key: 'profile', label: 'Profile User' }
    ];

    const loggedInUser = state.users.find(u => u.username === state.session.currentUser);
    const isEditingSelfRole = loggedInUser.role_id === roleId;
    const canUpdate = checkPermission('rbac', 'update');

    tbody.innerHTML = modules.map(m => {
        const perm = state.role_permissions.find(p => p.role_id === roleId && p.module === m.key) || {
            can_read: false, can_create: false, can_update: false, can_delete: false
        };

        const disabledAttr = (!canUpdate || (isEditingSelfRole && m.key === 'rbac')) ? 'disabled' : '';

        return `
            <tr>
                <td class="font-bold text-white">${m.label}</td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="chk-${m.key}-read" ${perm.can_read ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="chk-${m.key}-create" ${perm.can_create ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="chk-${m.key}-update" ${perm.can_update ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="chk-${m.key}-delete" ${perm.can_delete ? 'checked' : ''} ${disabledAttr}>
                </td>
            </tr>
        `;
    }).join('');

    // Toggle save button permission visual
    const saveBtn = document.getElementById('btn-rbac-save');
    if (saveBtn) {
        if (canUpdate) saveBtn.classList.remove('disabled');
        else saveBtn.classList.add('disabled');
    }
}

function saveRolePermissions() {
    if (!checkPermission('rbac', 'update')) {
        showToast('Izin Ditolak: Anda tidak memiliki akses menyimpan matriks hak akses!', 'danger');
        return;
    }

    const modules = ['dashboard', 'master-data', 'detail-data', 'scanner', 'users', 'rbac', 'logs', 'settings', 'profile'];
    
    modules.forEach(m => {
        const can_read = document.getElementById(`chk-${m}-read`).checked;
        const can_create = document.getElementById(`chk-${m}-create`).checked;
        const can_update = document.getElementById(`chk-${m}-update`).checked;
        const can_delete = document.getElementById(`chk-${m}-delete`).checked;

        const index = state.role_permissions.findIndex(p => p.role_id === activeRbacRole && p.module === m);
        if (index !== -1) {
            state.role_permissions[index] = { role_id: activeRbacRole, module: m, can_read, can_create, can_update, can_delete };
        } else {
            state.role_permissions.push({ role_id: activeRbacRole, module: m, can_read, can_create, can_update, can_delete });
        }
    });

    writeAuditLog(state.session.currentUser, 'RBAC', 'UPDATE', `Mengubah konfigurasi matriks hak akses untuk role ${activeRbacRole}`);
    saveState();
    showToast(`Matriks hak akses untuk peran ${activeRbacRole} berhasil disimpan!`, 'success');
    
    // Refresh the console shells in case permissions of active user changed
    initializeConsole();
}

// ----------------------------------------------------
// AUDIT LOGS TAB LOGIC
// ----------------------------------------------------
function loadAuditLogsTab() {
    filterAuditLogs();
}

function filterAuditLogs() {
    const tbody = document.getElementById('global-logs-tbody');
    const label = document.getElementById('logs-count-display');
    if (!tbody) return;

    const moduleFilter = document.getElementById('log-filter-module').value;
    const actionFilter = document.getElementById('log-filter-action').value;

    const filtered = state.audit_logs.filter(log => {
        const matchModule = moduleFilter === '' || log.module === moduleFilter;
        const matchAction = actionFilter === '' || log.action_type === actionFilter;
        return matchModule && matchAction;
    });

    label.textContent = `Total: ${filtered.length} Log Terdeteksi`;

    tbody.innerHTML = filtered.map(log => {
        const user = state.users.find(u => u.username === log.user_id) || { name: log.user_id };
        const actionBadgeClass = log.action_type === 'CREATE' ? 'badge-in' : log.action_type === 'DELETE' ? 'badge-out' : 'badge-pallet-type';
        
        return `
            <tr>
                <td class="font-mono text-slate-500">${new Date(log.timestamp).toLocaleString('id-ID')}</td>
                <td><strong class="text-white">${user.name}</strong> <span class="font-mono text-xxs text-slate-400">(${log.user_id})</span></td>
                <td><span class="badge bg-slate-900 border border-slate-800 text-xxs text-sky-400 font-mono">${log.module}</span></td>
                <td><span class="badge badge-status ${actionBadgeClass} text-xxs">${log.action_type}</span></td>
                <td class="text-slate-300 font-medium">${log.description}</td>
            </tr>
        `;
    }).join('');

    // Toggle clear logs button
    const clearBtn = document.getElementById('btn-logs-clear');
    if (clearBtn) {
        if (checkPermission('logs', 'delete')) clearBtn.classList.remove('disabled');
        else clearBtn.classList.add('disabled');
    }
}

function clearAuditLogs() {
    if (!checkPermission('logs', 'delete')) {
        showToast('Izin Ditolak: Anda tidak memiliki wewenang mengosongkan log audit!', 'danger');
        return;
    }

    if (confirm('Apakah Anda yakin ingin mengosongkan seluruh riwayat log audit global? Tindakan ini tidak dapat dibatalkan.')) {
        state.audit_logs = [{
            timestamp: new Date().toISOString(),
            user_id: state.session.currentUser,
            module: 'SYSTEM',
            action_type: 'DELETE',
            description: 'Log audit dikosongkan oleh Super Admin.'
        }];
        saveState();
        loadAuditLogsTab();
        showToast('Seluruh riwayat log audit berhasil dikosongkan!', 'success');
    }
}

// ----------------------------------------------------
// SETTINGS TAB LOGIC
// ----------------------------------------------------
function loadSettingsTab() {
    const s = state.settings || { limitBinPallets: true, maxPalletsPerBin: 2, expAlertMonths: 18, scanBeep: true, scanAutoClear: true };
    document.getElementById('setting-bin-limit').checked = s.limitBinPallets;
    document.getElementById('setting-bin-max-val').value = s.maxPalletsPerBin;
    document.getElementById('setting-exp-alert-months').value = s.expAlertMonths;
    document.getElementById('setting-scan-beep').checked = s.scanBeep;
    document.getElementById('setting-scan-autoclear').checked = s.scanAutoClear;

    const saveBtn = document.getElementById('btn-settings-save');
    if (saveBtn) {
        if (checkPermission('settings', 'update')) saveBtn.classList.remove('disabled');
        else saveBtn.classList.add('disabled');
    }
}

function saveGlobalSettings(e) {
    e.preventDefault();
    if (!checkPermission('settings', 'update')) {
        showToast('Izin Ditolak: Anda tidak memiliki hak akses mengubah pengaturan!', 'danger');
        return;
    }

    state.settings = {
        limitBinPallets: document.getElementById('setting-bin-limit').checked,
        maxPalletsPerBin: parseInt(document.getElementById('setting-bin-max-val').value),
        expAlertMonths: parseInt(document.getElementById('setting-exp-alert-months').value),
        scanBeep: document.getElementById('setting-scan-beep').checked,
        scanAutoClear: document.getElementById('setting-scan-autoclear').checked
    };

    writeAuditLog(state.session.currentUser, 'SYSTEM', 'UPDATE', 'Mengubah konfigurasi aturan bisnis global sistem.');
    saveState();
    showToast('Konfigurasi berhasil disimpan!', 'success');
}

// ----------------------------------------------------
// PROFILE TAB LOGIC
// ----------------------------------------------------
function loadProfileTab() {
    const user = state.users.find(u => u.username === state.session.currentUser);
    if (!user) return;

    document.getElementById('profile-avatar-letters').textContent = user.name.charAt(0).toUpperCase();
    document.getElementById('profile-full-name').textContent = user.name;
    document.getElementById('profile-role-badge').textContent = user.role_id;
    document.getElementById('profile-shift-display').textContent = `Shift ${user.shift}`;

    document.getElementById('profile-edit-name').value = user.name;
    document.getElementById('profile-edit-username').value = user.username;
    
    // Clear passwords
    document.getElementById('profile-new-pw').value = '';
    document.getElementById('profile-confirm-pw').value = '';

    // Load personal logs
    const tbody = document.getElementById('personal-logs-tbody');
    if (tbody) {
        const myLogs = state.audit_logs.filter(l => l.user_id === user.username);
        if (myLogs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-slate-500 py-3">Tidak ada riwayat aktivitas.</td></tr>';
        } else {
            tbody.innerHTML = myLogs.map(l => `
                <tr>
                    <td class="font-mono text-slate-500">${new Date(l.timestamp).toLocaleString('id-ID')}</td>
                    <td><span class="badge bg-slate-900 border border-slate-800 text-sky-400 font-mono">${l.module}</span></td>
                    <td><span class="badge badge-pallet-type text-xxs py-1">${l.action_type}</span></td>
                    <td class="text-slate-300">${l.description}</td>
                </tr>
            `).join('');
        }
    }
}

function handlePasswordChange(e) {
    e.preventDefault();
    const newPw = document.getElementById('profile-new-pw').value;
    const confirmPw = document.getElementById('profile-confirm-pw').value;

    if (!newPw) {
        showToast('Password baru tidak boleh kosong!', 'warning');
        return;
    }

    if (newPw !== confirmPw) {
        showToast('Ulangi password baru dengan benar!', 'warning');
        return;
    }

    const index = state.users.findIndex(u => u.username === state.session.currentUser);
    if (index !== -1) {
        state.users[index].password = newPw;
        writeAuditLog(state.session.currentUser, 'SYSTEM', 'UPDATE', 'Mengubah kata sandi personal.');
        saveState();
        showToast('Password baru Anda berhasil disimpan!', 'success');
        document.getElementById('profile-new-pw').value = '';
        document.getElementById('profile-confirm-pw').value = '';
    }
}

// ----------------------------------------------------
// CONSOLE SHELL INITIALIZATION
// ----------------------------------------------------
function initializeConsole() {
    loadState();
    
    const sess = state.session;
    const activePage = document.body.getAttribute('data-page');

    if (activePage === 'login') {
        if (sess.isLoggedIn) {
            window.location.replace('dashboard.html');
            return;
        }
        return;
    }

    // User is logged in
    if (!sess.isLoggedIn) {
        window.location.replace('login.html');
        return;
    }

    const user = state.users.find(u => u.username === sess.currentUser);
    if (!user) {
        handleLogout();
        return;
    }

    // Check permission for active page
    if (!checkPermission(activePage, 'read')) {
        showToast('Akses Ditolak: Anda tidak memiliki wewenang mengakses halaman ini!', 'danger');
        const modules = ['dashboard', 'master-data', 'detail-data', 'scanner', 'users', 'rbac', 'logs', 'settings', 'profile'];
        const permitted = modules.find(m => checkPermission(m, 'read'));
        if (permitted) {
            window.location.replace(permitted + '.html');
        } else {
            handleLogout();
        }
        return;
    }

    // Update active tab state
    sess.currentTab = activePage;
    saveState();

    // Set layout elements safely
    const sidebarUsername = document.getElementById('sidebar-username');
    if (sidebarUsername) sidebarUsername.textContent = user.name;

    const sidebarRole = document.getElementById('sidebar-role');
    if (sidebarRole) sidebarRole.textContent = user.role_id;

    const sidebarShiftVal = document.getElementById('sidebar-shift-val');
    if (sidebarShiftVal) sidebarShiftVal.textContent = user.shift;

    const sidebarAvatarLetters = document.getElementById('sidebar-avatar-letters');
    if (sidebarAvatarLetters) sidebarAvatarLetters.textContent = user.name.charAt(0).toUpperCase();

    const headerAvatarLetters = document.getElementById('header-avatar-letters');
    if (headerAvatarLetters) headerAvatarLetters.textContent = user.name.charAt(0).toUpperCase();

    const headerUserDisplay = document.getElementById('header-user-display');
    if (headerUserDisplay) headerUserDisplay.textContent = user.name;

    const headerDropdownName = document.getElementById('header-dropdown-name');
    if (headerDropdownName) headerDropdownName.textContent = user.name;

    const headerDropdownRole = document.getElementById('header-dropdown-role');
    if (headerDropdownRole) headerDropdownRole.textContent = user.role_id;

    // For compatibility with legacy index.html elements:
    const legacyUserDisplay = document.getElementById('active-user-display');
    if (legacyUserDisplay) legacyUserDisplay.textContent = user.name;
    const legacyRoleDisplay = document.getElementById('active-role-display');
    if (legacyRoleDisplay) legacyRoleDisplay.textContent = user.role_id;
    const legacyShiftDisplay = document.getElementById('sidebar-shift-display');
    if (legacyShiftDisplay) legacyShiftDisplay.textContent = user.shift;
    const legacyWhDisplay = document.getElementById('sidebar-wh-display');
    if (legacyWhDisplay) legacyWhDisplay.textContent = sess.activeWarehouseId;
    const legacyAvatar = document.querySelector('.user-avatar-circle');
    if (legacyAvatar) legacyAvatar.textContent = user.name.charAt(0).toUpperCase();

    // Populate active warehouse selector/dropdown
    const whSelect = document.getElementById('header-wh-select');
    if (whSelect) {
        whSelect.innerHTML = state.warehouses.map(w => `<option value="${w.id}" ${w.id === sess.activeWarehouseId ? 'selected' : ''}>${w.name}</option>`).join('');
    }

    const whDropdownList = document.getElementById('wh-dropdown-list');
    if (whDropdownList) {
        whDropdownList.innerHTML = state.warehouses.map(w => `
            <li>
                <a class="dropdown-item text-xs text-slate-300 hover:text-white hover:bg-slate-700/50 py-2 px-3 flex items-center justify-between" href="#" onclick="changeActiveWarehouse('${w.id}')">
                    <span>${w.name}</span>
                    ${w.id === sess.activeWarehouseId ? '<i class="bi bi-check text-sky-400"></i>' : ''}
                </a>
            </li>
        `).join('');
    }

    const activeWhBtn = document.getElementById('active-wh-btn');
    if (activeWhBtn) {
        const whObj = state.warehouses.find(w => w.id === sess.activeWarehouseId);
        activeWhBtn.textContent = whObj ? whObj.name : sess.activeWarehouseId;
    }

    // Manage Impersonation Floating Banner
    const banner = document.getElementById('impersonate-banner');
    if (banner) {
        if (sess.isImpersonating) {
            banner.classList.remove('hidden');
            document.body.classList.add('impersonating');
            const impUserDisplay = document.getElementById('impersonated-user-display');
            if (impUserDisplay) impUserDisplay.textContent = user.name;
            const impRoleDisplay = document.getElementById('impersonated-role-display');
            if (impRoleDisplay) impRoleDisplay.textContent = user.role_id;
        } else {
            banner.classList.add('hidden');
            document.body.classList.remove('impersonating');
        }
    }

    // Build sidebar based on role modular access permissions (RBAC)
    const modules = ['dashboard', 'master-data', 'detail-data', 'scanner', 'users', 'rbac', 'logs', 'settings', 'profile'];
    modules.forEach(m => {
        const link = document.getElementById(`nav-${m}`);
        if (link) {
            if (checkPermission(m, 'read')) {
                link.classList.remove('hidden');
            } else {
                link.classList.add('hidden');
            }
        }
    });

    // Reset layout active navigation link
    document.querySelectorAll('.sidebar-link').forEach(link => {
        link.classList.remove('active');
    });
    const activeLink = document.getElementById(`nav-${activePage}`);
    if (activeLink) activeLink.classList.add('active');

    // Update bottom nav active state
    document.querySelectorAll('[id^="bottom-nav-"]').forEach(btn => {
        btn.classList.remove('text-sky-500', 'text-sky-600', 'text-sky-400', 'font-semibold');
        btn.classList.add('text-slate-500');
    });
    const activeBottomBtn = document.getElementById(`bottom-nav-${activePage}`);
    if (activeBottomBtn) {
        const isDark = document.body.classList.contains('dark');
        activeBottomBtn.classList.add(isDark ? 'text-sky-400' : 'text-sky-600', 'font-semibold');
        activeBottomBtn.classList.remove('text-slate-500');
    }

    // Run Tab Initializers
    if (activePage === 'dashboard') renderDashboard();
    if (activePage === 'master-data') {
        rebuildMasterFilterSelect();
        loadMasterDataTab();
    }
    if (activePage === 'detail-data') loadDetailDataTab();
    if (activePage === 'scanner') loadScannerTab();
    if (activePage === 'users') loadUsersTab();
    if (activePage === 'rbac') loadRbacTab();
    if (activePage === 'logs') loadAuditLogsTab();
    if (activePage === 'settings') loadSettingsTab();
    if (activePage === 'profile') loadProfileTab();
}

// ----------------------------------------------------
// THEME MANAGER (LIGHT MODE DEFAULT, DARK MODE OPSIONAL)
// ----------------------------------------------------
function applyTheme(theme) {
    const body = document.body;
    const icon = document.getElementById('theme-icon') || document.getElementById('theme-toggle-icon');
    const toggleBtn = document.getElementById('theme-toggle-btn');
    
    if (theme === 'dark') {
        body.classList.add('dark');
        if (icon) {
            icon.className = 'bi bi-sun-fill text-warning';
        }
        if (toggleBtn) {
            toggleBtn.title = "Ganti ke Tema Terang (Light Mode)";
        }
    } else {
        body.classList.remove('dark');
        if (icon) {
            icon.className = 'bi bi-moon-fill text-slate-500';
        }
        if (toggleBtn) {
            toggleBtn.title = "Ganti ke Tema Gelap (Dark Mode)";
        }
    }
}

function toggleTheme() {
    const isDark = document.body.classList.contains('dark');
    const targetTheme = isDark ? 'light' : 'dark';
    localStorage.setItem('WMS_THEME_PREFERENCE', targetTheme);
    applyTheme(targetTheme);
    writeAuditLog(state.session.currentUser || 'system', 'SYSTEM', 'UPDATE', `Mengubah tema visual aplikasi ke Mode ${targetTheme === 'dark' ? 'Gelap' : 'Terang'}.`);
    
    if (state.session && state.session.currentTab === 'dashboard') {
        renderDashboard();
    }
}

// ----------------------------------------------------
// BOOTSTRAP INIT EVENT
// ----------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('WMS_THEME_PREFERENCE') || 'light';
    applyTheme(savedTheme);
    initializeConsole();
});
