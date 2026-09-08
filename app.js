// WMS Indraco Core Application State and Business Logic

// Initial transaction log data directly from the user's spreadsheet image
const initialTransactions = [
    { no: 1, pic: "Gilang", shift: 1, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Karton", expired: "2028-01-18", mesin: "1I0PB01", rak: "B.10.2", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-001" },
    { no: 2, pic: "Gilang", shift: 1, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Bag", expired: "2028-01-18", mesin: "1G0PB07", rak: "B.8.1", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-002" },
    { no: 3, pic: "Gilang", shift: 1, tanggal: "18 Juli 2026", barang: "Ucafe No Sugar Added B15", qty: 1, satuan: "Box", expired: "2028-07-18", mesin: "1I0M003", rak: "K.1.1", status: "In", paletJenis: "Plastik", gudang: "GSK.678.I", paletKode: "PLT-003" },
    { no: 4, pic: "Gilang", shift: 1, tanggal: "18 Juli 2026", barang: "Uang Emas Special 120g", qty: 20, satuan: "Pcs", expired: "2028-01-18", mesin: "1I0PP01", rak: "D.7.3", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-004" },
    { no: 5, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Karton", expired: "2028-01-18", mesin: "1I0PB01", rak: "B.10.2", status: "Out", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-001" },
    { no: 6, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Karton", expired: "2028-01-18", mesin: "1I0PB01", rak: "C.1.1", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-005" },
    { no: 7, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Bag", expired: "2028-01-18", mesin: "1G0PB07", rak: "B.8.1", status: "Out", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-002" },
    { no: 8, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 350g", qty: 1, satuan: "Bag", expired: "2028-01-18", mesin: "1G0PB07", rak: "C.1.1", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-006" },
    { no: 9, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Ucafe No Sugar Added B15", qty: 1, satuan: "Box", expired: "2028-07-18", mesin: "1I0M003", rak: "K.1.1", status: "Out", paletJenis: "Plastik", gudang: "GSK.678.I", paletKode: "PLT-003" },
    { no: 10, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Ucafe No Sugar Added B15", qty: 1, satuan: "Box", expired: "2028-07-18", mesin: "1I0M003", rak: "L.5.1", status: "In", paletJenis: "Plastik", gudang: "GSK.678.I", paletKode: "PLT-007" },
    { no: 11, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 120g", qty: 20, satuan: "Pcs", expired: "2028-01-18", mesin: "1I0PP01", rak: "D.7.3", status: "Out", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-004" },
    { no: 12, pic: "Risky", shift: 2, tanggal: "18 Juli 2026", barang: "Uang Emas Special 120g", qty: 20, satuan: "Pcs", expired: "2028-01-18", mesin: "1I0PP01", rak: "C.20.3", status: "In", paletJenis: "Loscam", gudang: "GSK.678.I", paletKode: "PLT-008" }
];

// Initial State definitions
const defaultState = {
    activeWarehouse: "GSK.678.I",
    currentUser: "Gilang",
    currentTab: "dashboard",
    locatorView: "rekap", // rekap or tarikan
    users: [
        { name: "Gilang", role: "Operator Gudang", shift: 1 },
        { name: "Risky", role: "Operator Gudang", shift: 2 },
        { name: "Diana", role: "Warehouse Supervisor", shift: 1 },
        { name: "Admin WMS", role: "Super Admin", shift: 1 }
    ],
    roles: {
        "Super Admin": {
            dashboard: { read: true, create: true, update: true, delete: true },
            inbound: { read: true, create: true, update: true, delete: true },
            outbound: { read: true, create: true, update: true, delete: true },
            transfer: { read: true, create: true, update: true, delete: true },
            locator: { read: true, create: true, update: true, delete: true },
            rbac: { read: true, create: true, update: true, delete: true },
            logs: { read: true, create: true, update: true, delete: true }
        },
        "Warehouse Supervisor": {
            dashboard: { read: true, create: true, update: true, delete: false },
            inbound: { read: true, create: true, update: true, delete: false },
            outbound: { read: true, create: true, update: true, delete: false },
            transfer: { read: true, create: true, update: true, delete: false },
            locator: { read: true, create: true, update: true, delete: false },
            rbac: { read: true, create: false, update: false, delete: false },
            logs: { read: true, create: false, update: false, delete: false }
        },
        "Operator Gudang": {
            dashboard: { read: true, create: true, update: false, delete: false },
            inbound: { read: true, create: true, update: false, delete: false },
            outbound: { read: true, create: true, update: false, delete: false },
            transfer: { read: true, create: true, update: false, delete: false },
            locator: { read: true, create: false, update: false, delete: false },
            rbac: { read: false, create: false, update: false, delete: false },
            logs: { read: false, create: false, update: false, delete: false }
        }
    },
    products: [
        { code: "PRD-001", name: "Uang Emas Special 350g" },
        { code: "PRD-002", name: "Ucafe No Sugar Added B15" },
        { code: "PRD-003", name: "Uang Emas Special 120g" }
    ],
    warehouses: [
        { code: "GSK.678.I", name: "GSK.678.I (Gudang Gresik)" },
        { code: "SBY.101.II", name: "SBY.101.II (Gudang Surabaya)" }
    ],
    racks: ["B.10.2", "B.8.1", "K.1.1", "D.7.3", "C.1.1", "L.5.1", "C.20.3"],
    transactions: [...initialTransactions],
    auditLogs: [
        { timestamp: "2026-07-18 08:30:00", user: "System", module: "SYSTEM", action: "CREATE", desc: "Inisialisasi sistem database WMS Indraco berhasil." }
    ]
};

// Global State
let state = {};
let barChart = null;
let doughnutChart = null;

// Initialize App
document.addEventListener("DOMContentLoaded", () => {
    loadState();
    setupClock();
    setupDropdownWarehouse();
    setupUserSimulator();
    renderUI();
});

// Load state from localStorage or use defaults
function loadState() {
    const saved = localStorage.getItem("wms_indraco_state");
    if (saved) {
        try {
            state = JSON.parse(saved);
        } catch (e) {
            state = { ...defaultState };
        }
    } else {
        state = { ...defaultState };
        saveState();
    }
}

function saveState() {
    localStorage.setItem("wms_indraco_state", JSON.stringify(state));
}

// Clock logic
function setupClock() {
    setInterval(() => {
        const now = new Date();
        const dateString = now.getFullYear() + '-' + 
            String(now.getMonth() + 1).padStart(2, '0') + '-' + 
            String(now.getDate()).padStart(2, '0') + ' ' + 
            String(now.getHours()).padStart(2, '0') + ':' + 
            String(now.getMinutes()).padStart(2, '0') + ':' + 
            String(now.getSeconds()).padStart(2, '0');
        const clockEl = document.getElementById("dashboard-clock");
        if (clockEl) clockEl.textContent = dateString;
    }, 1000);
}

// Setup top navbar warehouse dropdown
function setupDropdownWarehouse() {
    const select = document.getElementById("active-warehouse-select");
    select.innerHTML = state.warehouses.map(w => `<option value="${w.code}" ${w.code === state.activeWarehouse ? 'selected' : ''}>${w.name}</option>`).join('');
}

function changeWarehouse(code) {
    state.activeWarehouse = code;
    saveState();
    logAudit(state.currentUser, "SYSTEM", "UPDATE", `Mengubah gudang aktif ke ${code}`);
    showToast(`Gudang aktif diubah ke ${code}`, "success");
    renderUI();
}

// Setup user simulator list
function setupUserSimulator() {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    document.getElementById("header-user-name").textContent = activeUser.name;
    document.getElementById("header-user-role-badge").textContent = activeUser.role;
    document.getElementById("sidebar-active-shift").textContent = activeUser.shift;

    const userList = document.getElementById("user-simulator-list");
    userList.innerHTML = state.users.map(u => `
        <li>
            <a class="dropdown-item text-xs text-slate-300 hover:text-white hover:bg-slate-700/50 py-2 px-3 flex flex-col gap-0.5" href="#" onclick="switchUser('${u.name}')">
                <span class="font-semibold">${u.name} (Shift ${u.shift})</span>
                <span class="text-xxs text-sky-400">${u.role}</span>
            </a>
        </li>
    `).join('');
}

function switchUser(name) {
    const userObj = state.users.find(u => u.name === name);
    if (!userObj) return;

    state.currentUser = name;
    saveState();
    
    // Update headers and active shifts
    document.getElementById("header-user-name").textContent = userObj.name;
    document.getElementById("header-user-role-badge").textContent = userObj.role;
    document.getElementById("sidebar-active-shift").textContent = userObj.shift;
    
    // Write to audit log
    logAudit(name, "SYSTEM", "UPDATE", `Mengubah pengguna simulasi ke ${name} (Role: ${userObj.role})`);
    showToast(`Simulasi login sebagai: ${name}`, "info");
    
    // Refresh user lists and forms
    setupUserSimulator();
    
    // Auto-switch to dashboard if the user has no read permission for the current tab
    const userPermissions = state.roles[userObj.role] || state.roles["Operator Gudang"];
    if (!userPermissions[state.currentTab] || !userPermissions[state.currentTab].read) {
        switchTab("dashboard");
    } else {
        renderUI();
    }
}

// Navigation (Tab Switching)
function switchTab(tabId) {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];
    
    // Check READ permission
    if (userPermissions[tabId] && !userPermissions[tabId].read) {
        showToast(`Akses Ditolak: Role Anda (${activeUser.role}) tidak memiliki izin membaca modul ${tabId.toUpperCase()}!`, "danger");
        logAudit(activeUser.name, "SYSTEM", "DENIED", `Mencoba mengakses modul terlarang: ${tabId.toUpperCase()}`);
        return;
    }

    state.currentTab = tabId;
    saveState();

    // Toggle active classes in sidebar
    document.querySelectorAll(".sidebar-link").forEach(link => {
        link.classList.remove("active");
    });
    const activeLink = document.getElementById(`nav-${tabId}`);
    if (activeLink) activeLink.classList.add("active");

    // Hide all sections, show active one
    const sections = ["dashboard", "stock-locator", "inbound", "outbound", "transfer", "rbac", "logs"];
    sections.forEach(s => {
        const el = document.getElementById(`tab-${s}`);
        if (el) {
            if (s === tabId) {
                el.classList.remove("hidden");
            } else {
                el.classList.add("hidden");
            }
        }
    });

    renderUI();
}

// UI Render Router
function renderUI() {
    const currentTab = state.currentTab;
    
    // General Data Updates
    updateDashboardStats();

    if (currentTab === "dashboard") {
        renderDashboard();
    } else if (currentTab === "stock-locator") {
        renderStockLocator();
    } else if (currentTab === "inbound") {
        renderInbound();
    } else if (currentTab === "outbound") {
        renderOutbound();
    } else if (currentTab === "transfer") {
        renderTransfer();
    } else if (currentTab === "rbac") {
        renderRBAC();
    } else if (currentTab === "logs") {
        renderLogs();
    }
}

// Calculate pallets currently residing in a specific warehouse and rack
function getPalletsInBin(warehouseCode, rackCode) {
    const palletLatestTx = {};
    // Trace through sorted transactions to find latest location of each pallet
    state.transactions.forEach(t => {
        palletLatestTx[t.paletKode] = t;
    });

    const activePallets = [];
    for (const palletCode in palletLatestTx) {
        const tx = palletLatestTx[palletCode];
        if (tx.status === "In" && tx.gudang === warehouseCode && tx.rak === rackCode) {
            activePallets.push({
                code: palletCode,
                jenis: tx.paletJenis,
                barang: tx.barang,
                qty: tx.qty,
                satuan: tx.satuan,
                expired: tx.expired,
                mesin: tx.mesin
            });
        }
    }
    return activePallets;
}

// Calculate active pallets globally (where their last status was 'In')
function getActivePallets() {
    const palletLatestTx = {};
    state.transactions.forEach(t => {
        palletLatestTx[t.paletKode] = t;
    });

    const activePallets = [];
    for (const palletCode in palletLatestTx) {
        const tx = palletLatestTx[palletCode];
        if (tx.status === "In") {
            activePallets.push({
                code: palletCode,
                jenis: tx.paletJenis,
                barang: tx.barang,
                qty: tx.qty,
                satuan: tx.satuan,
                expired: tx.expired,
                mesin: tx.mesin,
                rak: tx.rak,
                gudang: tx.gudang,
                pic: tx.pic,
                shift: tx.shift,
                tanggal: tx.tanggal
            });
        }
    }
    return activePallets;
}

// Update Dashboard Statistics Card Values
function updateDashboardStats() {
    const activePallets = getActivePallets();
    const inboundToday = state.transactions.filter(t => t.status === "In").length;
    const outboundToday = state.transactions.filter(t => t.status === "Out").length;
    
    // Calculate BIN Occupancy Rate
    // Racks listed * 2 (as each rack/BIN can hold max 2 pallets)
    const totalCapacity = state.racks.length * 2;
    const currentOccupied = activePallets.filter(p => p.gudang === state.activeWarehouse).length;
    const occupancyPercentage = Math.round((currentOccupied / totalCapacity) * 100) || 0;

    const elPallets = document.getElementById("stat-active-pallets");
    const elOccupancy = document.getElementById("stat-occupancy-rate");
    const elInbound = document.getElementById("stat-inbound-today");
    const elOutbound = document.getElementById("stat-outbound-today");

    if (elPallets) elPallets.textContent = `${activePallets.length} Palet`;
    if (elOccupancy) elOccupancy.textContent = `${occupancyPercentage}%`;
    if (elInbound) elInbound.textContent = `${inboundToday} Transaksi`;
    if (elOutbound) elOutbound.textContent = `${outboundToday} Transaksi`;
}

// ----------------------------------------------------
// MODUL: DASHBOARD
// ----------------------------------------------------
function renderDashboard() {
    // 1. Populate Full BINs table
    const tableBody = document.getElementById("dashboard-full-bins-body");
    if (!tableBody) return;

    let fullBinRows = "";
    state.racks.forEach(rack => {
        const pallets = getPalletsInBin(state.activeWarehouse, rack);
        if (pallets.length === 2) {
            fullBinRows += `
                <tr>
                    <td class="font-semibold text-white">${rack}</td>
                    <td>
                        <div class="flex flex-col gap-0.5">
                            <span class="text-xxs text-slate-400">1. ${pallets[0].jenis}</span>
                            <span class="text-xxs text-slate-400">2. ${pallets[1].jenis}</span>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-col gap-0.5 text-xxs">
                            <span>[${pallets[0].code}] ${pallets[0].barang} (${pallets[0].qty} ${pallets[0].satuan})</span>
                            <span>[${pallets[1].code}] ${pallets[1].barang} (${pallets[1].qty} ${pallets[1].satuan})</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-penuh flex items-center gap-1 w-fit"><i class="bi bi-exclamation-circle-fill"></i> Penuh (2 Palet)</span>
                    </td>
                </tr>
            `;
        }
    });

    if (fullBinRows === "") {
        fullBinRows = `
            <tr>
                <td colspan="4" class="text-center py-4 text-slate-500">Tidak ada BIN yang terisi penuh (2 Palet) di gudang ini.</td>
            </tr>
        `;
    }
    tableBody.innerHTML = fullBinRows;

    // 2. Render Charts
    renderCharts();
}

function renderCharts() {
    // Destroy previous charts to avoid canvas reusable issues
    if (barChart) barChart.destroy();
    if (doughnutChart) doughnutChart.destroy();

    // Chart 1: Incoming vs Outgoing Qty per Product
    const barCtx = document.getElementById("bar-chart-inout");
    if (barCtx) {
        // Group by product name
        const productData = {};
        state.products.forEach(p => {
            productData[p.name] = { in: 0, out: 0 };
        });

        state.transactions.forEach(t => {
            if (productData[t.barang]) {
                if (t.status === "In") {
                    productData[t.barang].in += Number(t.qty);
                } else if (t.status === "Out") {
                    productData[t.barang].out += Number(t.qty);
                }
            }
        });

        const labels = Object.keys(productData);
        const dataIn = labels.map(l => productData[l].in);
        const dataOut = labels.map(l => productData[l].out);

        barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: labels.map(l => l.length > 20 ? l.substring(0, 18) + '...' : l),
                datasets: [
                    {
                        label: 'Barang Masuk (In)',
                        data: dataIn,
                        backgroundColor: 'rgba(16, 185, 129, 0.65)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Barang Keluar (Out)',
                        data: dataOut,
                        backgroundColor: 'rgba(239, 68, 68, 0.65)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8', font: { family: 'Outfit', size: 11 } }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 10 } },
                        grid: { color: 'rgba(255,255,255,0.03)' }
                    },
                    y: {
                        ticks: { color: '#94a3b8', font: { family: 'Outfit', size: 10 } },
                        grid: { color: 'rgba(255,255,255,0.03)' }
                    }
                }
            }
        });
    }

    // Chart 2: Occupied vs Empty BIN slots
    const pieCtx = document.getElementById("pie-chart-occupancy");
    if (pieCtx) {
        let emptyCount = 0;
        let partialCount = 0;
        let fullCount = 0;

        state.racks.forEach(rack => {
            const count = getPalletsInBin(state.activeWarehouse, rack).length;
            if (count === 0) emptyCount++;
            else if (count === 1) partialCount++;
            else if (count === 2) fullCount++;
        });

        doughnutChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kosong', 'Terisi 1 Palet', 'Terisi 2 Palet (Penuh)'],
                datasets: [{
                    data: [emptyCount, partialCount, fullCount],
                    backgroundColor: [
                        'rgba(148, 163, 184, 0.4)',
                        'rgba(56, 189, 248, 0.6)',
                        'rgba(245, 158, 11, 0.6)'
                    ],
                    borderColor: [
                        'rgba(148, 163, 184, 0.6)',
                        'rgba(56, 189, 248, 0.8)',
                        'rgba(245, 158, 11, 0.8)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', font: { family: 'Outfit', size: 11 } }
                    }
                }
            }
        });
    }
}

// ----------------------------------------------------
// MODUL: STOCK LOCATOR
// ----------------------------------------------------
function toggleLocatorView(viewType) {
    state.locatorView = viewType;
    saveState();

    const btnRekap = document.getElementById("rekap-tab-btn");
    const btnTarikan = document.getElementById("tarikan-tab-btn");
    const viewRekap = document.getElementById("locator-view-rekap");
    const viewTarikan = document.getElementById("locator-view-tarikan");

    if (viewType === "rekap") {
        btnRekap.classList.add("active");
        btnRekap.classList.remove("text-slate-400");
        btnRekap.style.borderBottom = "2px solid #38bdf8";
        btnTarikan.classList.remove("active");
        btnTarikan.classList.add("text-slate-400");
        btnTarikan.style.borderBottom = "none";

        viewRekap.classList.remove("hidden");
        viewTarikan.classList.add("hidden");
    } else {
        btnTarikan.classList.add("active");
        btnTarikan.classList.remove("text-slate-400");
        btnTarikan.style.borderBottom = "2px solid #38bdf8";
        btnRekap.classList.remove("active");
        btnRekap.classList.add("text-slate-400");
        btnRekap.style.borderBottom = "none";

        viewTarikan.classList.remove("hidden");
        viewRekap.classList.add("hidden");
    }
    renderStockLocator();
}

function renderStockLocator() {
    const searchVal = document.getElementById("locator-search-query").value.toLowerCase();
    const filterStatus = document.getElementById("locator-filter-status-rak").value;
    const filterPaletType = document.getElementById("locator-filter-jenis-palet").value;

    if (state.locatorView === "rekap") {
        renderRekapTable(searchVal, filterStatus, filterPaletType);
    } else {
        renderTarikanTable(searchVal, filterPaletType);
    }
}

// RENDER REKAP DATA BIN LOCATION (GAMBAR 3)
function renderRekapTable(searchQuery, filterStatus, filterPaletType) {
    const tableBody = document.getElementById("rekap-bin-table-body");
    if (!tableBody) return;

    // We need to gather all unique batches of (Product, Expired Date, Kode Mesin, Satuan, Jenis Palet)
    // present in the transaction database.
    const batches = [];
    const seen = new Set();

    state.transactions.forEach(t => {
        const key = `${t.barang}|${t.expired}|${t.mesin}|${t.satuan}|${t.paletJenis}`;
        if (!seen.has(key)) {
            seen.add(key);
            batches.push({
                barang: t.barang,
                expired: t.expired,
                mesin: t.mesin,
                satuan: t.satuan,
                paletJenis: t.paletJenis
            });
        }
    });

    let html = "";
    let totalRows = 0;

    // Loop through each product batch and cross-reference with ALL standard racks
    batches.forEach(batch => {
        state.racks.forEach(rackCode => {
            // Apply search query filter (matches product name or rack code)
            if (searchQuery && 
                !batch.barang.toLowerCase().includes(searchQuery) && 
                !rackCode.toLowerCase().includes(searchQuery)) {
                return;
            }

            // Apply Pallet Type filter
            if (filterPaletType && batch.paletJenis !== filterPaletType) {
                return;
            }

            // Sum up Quantities
            // In quantity: transactions of this batch/product, status 'In', at this specific rack
            const sumIn = state.transactions
                .filter(t => t.barang === batch.barang && t.expired === batch.expired && t.mesin === batch.mesin && t.rak === rackCode && t.status === "In")
                .reduce((sum, t) => sum + Number(t.qty), 0);

            // Out quantity: transactions of this batch/product, status 'Out', at this specific rack
            const sumOut = state.transactions
                .filter(t => t.barang === batch.barang && t.expired === batch.expired && t.mesin === batch.mesin && t.rak === rackCode && t.status === "Out")
                .reduce((sum, t) => sum + Number(t.qty), 0);

            const qtyAkhir = sumIn - sumOut;
            const statusRak = qtyAkhir > 0 ? "Terisi" : "Kosong";

            // Apply Status Rak filter
            if (filterStatus && statusRak !== filterStatus) {
                return;
            }

            totalRows++;

            // Styles for columns based on image reference
            const formattedExp = formatIndoDate(batch.expired);
            const qtyAkhirClass = qtyAkhir > 0 ? "text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20" : "text-slate-400";
            const badgeClass = qtyAkhir > 0 ? "badge-terisi" : "badge-kosong";
            
            // Text color coding matching Excel: yellow for Ucafe, cyan/blue for Uang Emas
            let nameBgClass = "text-sky-300 font-medium";
            if (batch.barang.toLowerCase().includes("ucafe")) {
                nameBgClass = "text-yellow-300 font-medium";
            }

            html += `
                <tr>
                    <td class="${nameBgClass}">${batch.barang}</td>
                    <td class="font-mono text-xs">${formattedExp}</td>
                    <td class="font-mono text-xs">${batch.mesin}</td>
                    <td><span class="badge badge-in btn-xs text-xxs">In</span></td>
                    <td class="font-mono font-semibold">${rackCode}</td>
                    <td class="text-center font-semibold text-emerald-400">${sumIn}</td>
                    <td><span class="badge badge-out btn-xs text-xxs">Out</span></td>
                    <td class="font-mono font-semibold">${rackCode}</td>
                    <td class="text-center font-semibold text-rose-400">${sumOut}</td>
                    <td class="text-center"><span class="${qtyAkhirClass}">${qtyAkhir}</span></td>
                    <td><span class="badge ${badgeClass} text-xxs">${statusRak}</span></td>
                </tr>
            `;
        });
    });

    if (html === "") {
        html = `<tr><td colspan="11" class="text-center py-4 text-slate-500">Tidak ada data rekapitulasi yang cocok dengan filter pencarian.</td></tr>`;
    }
    tableBody.innerHTML = html;
    document.getElementById("rekap-total-rows").textContent = `Menampilkan ${totalRows} baris rekap`;
}

// RENDER TARIKAN DATA LOG TRANSAKSI (GAMBAR 2)
function renderTarikanTable(searchQuery, filterPaletType) {
    const tableBody = document.getElementById("tarikan-bin-table-body");
    if (!tableBody) return;

    let html = "";
    let totalRows = 0;

    // Sorted descending by number or date
    const sortedTx = [...state.transactions].sort((a, b) => b.no - a.no);

    sortedTx.forEach(t => {
        // Search query check
        if (searchQuery && 
            !t.barang.toLowerCase().includes(searchQuery) && 
            !t.rak.toLowerCase().includes(searchQuery) &&
            !t.pic.toLowerCase().includes(searchQuery)) {
            return;
        }

        // Pallet type filter
        if (filterPaletType && t.paletJenis !== filterPaletType) {
            return;
        }

        totalRows++;
        const badgeStatus = t.status === "In" ? "badge-in" : "badge-out";
        const formattedExp = formatIndoDate(t.expired);

        html += `
            <tr class="font-mono">
                <td class="text-slate-400 font-semibold">${t.no}</td>
                <td class="text-white font-sans font-medium">${t.pic}</td>
                <td>${t.shift}</td>
                <td class="text-slate-400 text-xxs font-sans">${t.tanggal}</td>
                <td class="text-slate-200 font-sans text-xs">${t.barang}</td>
                <td class="text-center text-white font-bold">${t.qty}</td>
                <td><span class="bg-slate-800 text-slate-300 py-0.5 px-2 rounded-full border border-slate-700/50 text-xxs">${t.satuan}</span></td>
                <td class="text-xxs">${formattedExp}</td>
                <td>${t.mesin}</td>
                <td class="font-sans font-bold text-sky-400 text-xs">${t.rak}</td>
                <td><span class="badge ${badgeStatus} text-xxs">${t.status}</span></td>
                <td class="text-xxs font-sans">${t.paletJenis}</td>
                <td class="text-xxs font-sans text-slate-400">${t.gudang}</td>
            </tr>
        `;
    });

    if (html === "") {
        html = `<tr><td colspan="13" class="text-center py-4 text-slate-500">Tidak ada log transaksi penarikan data yang cocok.</td></tr>`;
    }
    tableBody.innerHTML = html;
    document.getElementById("tarikan-total-rows").textContent = `Menampilkan ${totalRows} baris transaksi`;
}

function resetLocatorFilters() {
    document.getElementById("locator-search-query").value = "";
    document.getElementById("locator-filter-status-rak").value = "";
    document.getElementById("locator-filter-jenis-palet").value = "";
    renderStockLocator();
}

function exportToExcelSimulated() {
    // Generate CSV representing the current view
    let csvContent = "data:text/csv;charset=utf-8,";
    
    if (state.locatorView === "rekap") {
        csvContent += "Nama Barang,Expired Date,Kode Mesin,Status In,Kode Rak In,Qty In,Status Out,Kode Rak Out,Qty Out,Qty Akhir,Status Rak\n";
        const batches = [];
        const seen = new Set();
        state.transactions.forEach(t => {
            const key = `${t.barang}|${t.expired}|${t.mesin}|${t.satuan}`;
            if (!seen.has(key)) {
                seen.add(key);
                batches.push({ barang: t.barang, expired: t.expired, mesin: t.mesin });
            }
        });
        batches.forEach(b => {
            state.racks.forEach(r => {
                const sumIn = state.transactions.filter(t => t.barang === b.barang && t.expired === b.expired && t.mesin === b.mesin && t.rak === r && t.status === "In").reduce((sum, t) => sum + Number(t.qty), 0);
                const sumOut = state.transactions.filter(t => t.barang === b.barang && t.expired === b.expired && t.mesin === b.mesin && t.rak === r && t.status === "Out").reduce((sum, t) => sum + Number(t.qty), 0);
                const qtyAkhir = sumIn - sumOut;
                const status = qtyAkhir > 0 ? "Terisi" : "Kosong";
                csvContent += `"${b.barang}","${b.expired}","${b.mesin}","In","${r}",${sumIn},"Out","${r}",${sumOut},${qtyAkhir},"${status}"\n`;
            });
        });
    } else {
        csvContent += "Nomor,PIC Input,Shift,Tanggal Input,Nama Barang,Qty,Satuan,Expired Date,Kode Mesin,Kode Rak,Status,Jenis Palet,Gudang\n";
        state.transactions.forEach(t => {
            csvContent += `${t.no},"${t.pic}",${t.shift},"${t.tanggal}","${t.barang}",${t.qty},"${t.satuan}","${t.expired}","${t.mesin}","${t.rak}","${t.status}","${t.paletJenis}","${t.gudang}"\n`;
        });
    }

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `wms_indraco_${state.locatorView}_${Date.now()}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast("File Rekapitulasi CSV diunduh dengan sukses!", "success");
    logAudit(state.currentUser, "SYSTEM", "READ", `Mengekspor rekapitulasi data ${state.locatorView} ke file Excel/CSV`);
}

// ----------------------------------------------------
// MODUL: BARANG MASUK (INBOUND)
// ----------------------------------------------------
function renderInbound() {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    document.getElementById("inbound-pic").value = activeUser.name;
    document.getElementById("inbound-shift").value = activeUser.shift;

    // Load Inbound Product Options
    const productSelect = document.getElementById("inbound-product-select");
    productSelect.innerHTML = state.products.map(p => `<option value="${p.name}">${p.name}</option>`).join('');

    // Load Available BIN Options (checks warehouse and highlights current pallet count)
    const binSelect = document.getElementById("inbound-bin-select");
    binSelect.innerHTML = state.racks.map(rack => {
        const pallets = getPalletsInBin(state.activeWarehouse, rack);
        const count = pallets.length;
        let badgeText = `${count}/2 Palet`;
        if (count === 2) badgeText += " (Penuh)";
        return `<option value="${rack}" ${count === 2 ? 'disabled' : ''}>${rack} [${badgeText}]</option>`;
    }).join('');

    // Event listener for BIN choice to display real-time warning
    binSelect.onchange = () => {
        const rack = binSelect.value;
        const pallets = getPalletsInBin(state.activeWarehouse, rack);
        const noteEl = document.getElementById("inbound-bin-occupancy-note");
        if (pallets.length === 2) {
            noteEl.textContent = `⚠️ BIN ${rack} sudah penuh! Maksimal 2 palet.`;
            noteEl.className = "text-xxs text-rose-400 mt-1 block font-semibold";
        } else {
            noteEl.textContent = `✔ BIN ${rack} memiliki kapasitas ${pallets.length}/2 palet.`;
            noteEl.className = "text-xxs text-emerald-400 mt-1 block";
        }
    };
    // Trigger initial state
    binSelect.onchange();

    // Render Inbound History for today
    const histBody = document.getElementById("inbound-history-body");
    const todayInbound = state.transactions.filter(t => t.status === "In");
    let html = "";
    todayInbound.forEach(t => {
        html += `
            <tr class="text-xs">
                <td>${t.tanggal}</td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-medium">${t.pic}</span>
                        <span class="text-xxs text-slate-400">Shift ${t.shift}</span>
                    </div>
                </td>
                <td class="font-semibold text-slate-200">${t.barang}</td>
                <td><strong class="text-white">${t.qty}</strong> <span class="text-xxs text-slate-400">(${t.satuan})</span></td>
                <td class="font-mono">${t.mesin}</td>
                <td class="font-mono text-sky-400 font-bold">${t.rak}</td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-semibold">${t.paletKode}</span>
                        <span class="text-xxs text-slate-400">${t.paletJenis}</span>
                    </div>
                </td>
                <td>
                    <button onclick="deleteTransaction(${t.no})" class="btn btn-outline-danger btn-xs py-0.5 px-2 border-slate-700 hover:bg-rose-500/10 text-rose-400 rounded"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
        `;
    });

    if (html === "") {
        html = `<tr><td colspan="8" class="text-center py-4 text-slate-500">Belum ada barang masuk hari ini.</td></tr>`;
    }
    histBody.innerHTML = html;
}

function handleInboundSubmit(event) {
    event.preventDefault();

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    // 1. RBAC Create Check
    if (!userPermissions.inbound.create) {
        showToast("Akses Ditolak: Anda tidak memiliki izin untuk menyimpan barang masuk!", "danger");
        return;
    }

    const barang = document.getElementById("inbound-product-select").value;
    const qty = Number(document.getElementById("inbound-qty").value);
    const satuan = document.getElementById("inbound-satuan").value;
    const expired = document.getElementById("inbound-expired").value;
    const mesin = document.getElementById("inbound-mesin").value;
    const rak = document.getElementById("inbound-bin-select").value;
    const paletKode = document.getElementById("inbound-pallet-code").value.trim().toUpperCase();
    const paletJenis = document.getElementById("inbound-pallet-type").value;
    const gudang = state.activeWarehouse;

    // 2. Kapasitas BIN Check (Kritis)
    const existingPallets = getPalletsInBin(gudang, rak);
    if (existingPallets.length >= 2) {
        showToast(`Gagal: BIN ${rak} sudah terisi maksimal 2 palet! Silakan pilih lokasi BIN lain.`, "danger");
        logAudit(activeUser.name, "INBOUND", "CREATE_FAIL", `Putaway gagal ke ${rak} karena BIN penuh.`);
        return;
    }

    // 3. Prevent duplicate pallet registration at different bins
    const activePallets = getActivePallets();
    const isPalletAlreadyInRack = activePallets.find(p => p.code === paletKode);
    if (isPalletAlreadyInRack) {
        showToast(`Gagal: Palet ${paletKode} saat ini aktif di Rak ${isPalletAlreadyInRack.rak}! Silakan gunakan barcode palet lain atau kosongkan palet tersebut terlebih dahulu.`, "danger");
        return;
    }

    // Success transaction receipt creation
    const nextNo = state.transactions.length > 0 ? Math.max(...state.transactions.map(t => t.no)) + 1 : 1;
    const today = new Date();
    const formattedToday = `${today.getDate()} ${formatIndoMonth(today.getMonth())} ${today.getFullYear()}`;

    const newTx = {
        no: nextNo,
        pic: activeUser.name,
        shift: activeUser.shift,
        tanggal: formattedToday,
        barang: barang,
        qty: qty,
        satuan: satuan,
        expired: expired,
        mesin: mesin,
        rak: rak,
        status: "In",
        paletJenis: paletJenis,
        gudang: gudang,
        paletKode: paletKode
    };

    state.transactions.push(newTx);
    
    // Register pallet to cache list if not exists
    if (!state.pallets.find(p => p.code === paletKode)) {
        state.pallets.push({ code: paletKode, type: paletJenis });
    }

    saveState();
    logAudit(activeUser.name, "INBOUND", "CREATE", `Receipt barang masuk: ${barang} (${qty} ${satuan}) di Palet ${paletKode} ke BIN ${rak}`);
    showToast(`Barang masuk disimpan ke BIN ${rak} menggunakan Palet ${paletKode}!`, "success");

    // Reset Form & Refresh
    document.getElementById("inbound-qty").value = 1;
    document.getElementById("inbound-pallet-code").value = "";
    renderInbound();
}

// ----------------------------------------------------
// MODUL: BARANG KELUAR (OUTBOUND)
// ----------------------------------------------------
function renderOutbound() {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    document.getElementById("outbound-pic").value = activeUser.name;
    document.getElementById("outbound-shift").value = activeUser.shift;

    // Load current active pallets in the warehouse to issue out
    const activePallets = getActivePallets().filter(p => p.gudang === state.activeWarehouse);
    const palletSelect = document.getElementById("outbound-pallet-select");
    
    if (activePallets.length === 0) {
        palletSelect.innerHTML = `<option value="">-- Tidak ada Palet Aktif di Gudang --</option>`;
        resetOutboundInfo();
    } else {
        palletSelect.innerHTML = `<option value="">-- Pilih Palet / Barcode --</option>` + 
            activePallets.map(p => `<option value="${p.code}">${p.code} (${p.barang} - Rak ${p.rak})</option>`).join('');
    }

    // Render Outbound history
    const histBody = document.getElementById("outbound-history-body");
    const todayOutbound = state.transactions.filter(t => t.status === "Out");
    let html = "";
    todayOutbound.forEach(t => {
        html += `
            <tr class="text-xs">
                <td>${t.tanggal}</td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-medium">${t.pic}</span>
                        <span class="text-xxs text-slate-400">Shift ${t.shift}</span>
                    </div>
                </td>
                <td class="font-semibold text-slate-200">${t.barang}</td>
                <td><strong class="text-white">${t.qty}</strong> <span class="text-xxs text-slate-400">(${t.satuan})</span></td>
                <td class="font-mono">${t.mesin}</td>
                <td class="font-mono text-rose-400 font-bold">${t.rak}</td>
                <td>
                    <div class="flex flex-col">
                        <span class="font-semibold">${t.paletKode}</span>
                        <span class="text-xxs text-slate-400">${t.paletJenis}</span>
                    </div>
                </td>
                <td>
                    <button onclick="deleteTransaction(${t.no})" class="btn btn-outline-danger btn-xs py-0.5 px-2 border-slate-700 hover:bg-rose-500/10 text-rose-400 rounded"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
        `;
    });

    if (html === "") {
        html = `<tr><td colspan="8" class="text-center py-4 text-slate-500">Belum ada barang keluar hari ini.</td></tr>`;
    }
    histBody.innerHTML = html;
}

function handleOutboundPalletChange(palletCode) {
    if (!palletCode) {
        resetOutboundInfo();
        return;
    }

    const activePallets = getActivePallets();
    const pallet = activePallets.find(p => p.code === palletCode);
    if (!pallet) return;

    document.getElementById("out-info-name").textContent = pallet.barang;
    document.getElementById("out-info-qty").textContent = pallet.qty;
    document.getElementById("out-info-exp").textContent = formatIndoDate(pallet.expired);
    document.getElementById("out-info-mesin").textContent = pallet.mesin;
    document.getElementById("out-info-location").textContent = `${pallet.rak} (${state.warehouses.find(w => w.code === pallet.gudang)?.name})`;

    const qtyInput = document.getElementById("outbound-qty-input");
    qtyInput.max = pallet.qty;
    qtyInput.value = pallet.qty; // default full release
    document.getElementById("outbound-satuan-display").value = pallet.satuan;
}

function resetOutboundInfo() {
    document.getElementById("out-info-name").textContent = "-";
    document.getElementById("out-info-qty").textContent = "-";
    document.getElementById("out-info-exp").textContent = "-";
    document.getElementById("out-info-mesin").textContent = "-";
    document.getElementById("out-info-location").textContent = "-";
    document.getElementById("outbound-qty-input").value = "";
    document.getElementById("outbound-qty-input").removeAttribute("max");
    document.getElementById("outbound-satuan-display").value = "";
}

function handleOutboundSubmit(event) {
    event.preventDefault();

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    // 1. RBAC Create Check
    if (!userPermissions.outbound.create) {
        showToast("Akses Ditolak: Anda tidak memiliki izin untuk menyimpan barang keluar!", "danger");
        return;
    }

    const palletCode = document.getElementById("outbound-pallet-select").value;
    if (!palletCode) {
        showToast("Gagal: Pilih palet barang terlebih dahulu!", "danger");
        return;
    }

    const activePallets = getActivePallets();
    const pallet = activePallets.find(p => p.code === palletCode);
    if (!pallet) return;

    const qtyOut = Number(document.getElementById("outbound-qty-input").value);
    if (qtyOut > pallet.qty) {
        showToast(`Gagal: Jumlah pengeluaran (${qtyOut}) melebihi stok yang tersedia (${pallet.qty})!`, "danger");
        return;
    }

    // Success transaction dispatch entry
    const nextNo = state.transactions.length > 0 ? Math.max(...state.transactions.map(t => t.no)) + 1 : 1;
    const today = new Date();
    const formattedToday = `${today.getDate()} ${formatIndoMonth(today.getMonth())} ${today.getFullYear()}`;

    const newTx = {
        no: nextNo,
        pic: activeUser.name,
        shift: activeUser.shift,
        tanggal: formattedToday,
        barang: pallet.barang,
        qty: qtyOut,
        satuan: pallet.satuan,
        expired: pallet.expired,
        mesin: pallet.mesin,
        rak: pallet.rak,
        status: "Out",
        paletJenis: pallet.jenis,
        gudang: pallet.gudang,
        paletKode: pallet.code
    };

    state.transactions.push(newTx);
    saveState();

    logAudit(activeUser.name, "OUTBOUND", "CREATE", `Dispatch barang keluar: ${pallet.barang} (${qtyOut} ${pallet.satuan}) dari Palet ${pallet.code} di Rak ${pallet.rak}`);
    showToast(`Barang keluar disimpan! Palet ${pallet.code} telah dikeluarkan dari ${pallet.rak}.`, "success");

    resetOutboundInfo();
    renderOutbound();
}

// ----------------------------------------------------
// MODUL: TRANSFER / MOVE
// ----------------------------------------------------
function renderTransfer() {
    const activePallets = getActivePallets().filter(p => p.gudang === state.activeWarehouse);
    const palletSelect = document.getElementById("transfer-pallet-select");
    
    if (activePallets.length === 0) {
        palletSelect.innerHTML = `<option value="">-- Tidak ada Palet Aktif untuk dipindahkan --</option>`;
        resetTransferInfo();
    } else {
        palletSelect.innerHTML = `<option value="">-- Pilih Palet --</option>` + 
            activePallets.map(p => `<option value="${p.code}">${p.code} (${p.barang} - Rak ${p.rak})</option>`).join('');
    }

    // Target BIN setup
    const binSelect = document.getElementById("transfer-bin-select");
    binSelect.innerHTML = state.racks.map(rack => {
        const count = getPalletsInBin(state.activeWarehouse, rack).length;
        let badgeText = `${count}/2 Palet`;
        if (count === 2) badgeText += " (Penuh)";
        return `<option value="${rack}">${rack} [${badgeText}]</option>`;
    }).join('');

    binSelect.onchange = () => {
        const rack = binSelect.value;
        const count = getPalletsInBin(state.activeWarehouse, rack).length;
        const note = document.getElementById("transfer-bin-occupancy-note");
        if (count === 2) {
            note.textContent = `⚠️ BIN ${rack} sudah penuh! Sistem akan menolak jika dijalankan.`;
            note.className = "text-xxs text-rose-400 mt-1 block font-semibold";
        } else {
            note.textContent = `✔ BIN ${rack} memiliki sisa slot (${count}/2 terisi).`;
            note.className = "text-xxs text-emerald-400 mt-1 block";
        }
    };
    binSelect.onchange();

    // Render Move History Logs
    const historyBody = document.getElementById("transfer-history-body");
    // Get audit logs related to MOVE actions
    const moveAudits = state.auditLogs.filter(l => l.action === "MOVE");
    let html = "";
    moveAudits.forEach(l => {
        html += `
            <tr class="text-xs">
                <td>${l.timestamp}</td>
                <td>${l.user}</td>
                <!-- Parse codes out of description dynamically for representation -->
                <td class="font-mono font-semibold">${extractPalletFromDesc(l.desc)}</td>
                <td>${extractProductFromDesc(l.desc)}</td>
                <td class="font-mono text-rose-400">${extractSourceFromDesc(l.desc)}</td>
                <td class="font-mono text-emerald-400 font-bold">${extractDestFromDesc(l.desc)}</td>
                <td class="text-slate-400">${l.desc}</td>
            </tr>
        `;
    });

    if (html === "") {
        html = `<tr><td colspan="7" class="text-center py-4 text-slate-500">Belum ada pemindahan lokasi hari ini.</td></tr>`;
    }
    historyBody.innerHTML = html;
}

function handleTransferPalletChange(palletCode) {
    if (!palletCode) {
        resetTransferInfo();
        return;
    }

    const activePallets = getActivePallets();
    const pallet = activePallets.find(p => p.code === palletCode);
    if (!pallet) return;

    document.getElementById("trans-info-name").textContent = pallet.barang;
    document.getElementById("trans-info-qty").textContent = `${pallet.qty} ${pallet.satuan}`;
    document.getElementById("trans-info-location").textContent = pallet.rak;
    document.getElementById("trans-info-pallet-type").textContent = pallet.jenis;
}

function resetTransferInfo() {
    document.getElementById("trans-info-name").textContent = "-";
    document.getElementById("trans-info-qty").textContent = "-";
    document.getElementById("trans-info-location").textContent = "-";
    document.getElementById("trans-info-pallet-type").textContent = "-";
}

function handleTransferSubmit(event) {
    event.preventDefault();

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    // 1. RBAC Access check
    if (!userPermissions.transfer.create) {
        showToast("Akses Ditolak: Peran Anda tidak memiliki izin memindahkan barang!", "danger");
        return;
    }

    const palletCode = document.getElementById("transfer-pallet-select").value;
    const targetRak = document.getElementById("transfer-bin-select").value;

    if (!palletCode) {
        showToast("Pilih palet yang akan dipindahkan!", "danger");
        return;
    }

    const activePallets = getActivePallets();
    const pallet = activePallets.find(p => p.code === palletCode);
    if (!pallet) return;

    const sourceRak = pallet.rak;

    if (sourceRak === targetRak) {
        showToast("Gagal: BIN tujuan pemindahan sama dengan BIN asal!", "danger");
        return;
    }

    // 2. Capacity target BIN check (Kritis)
    const targetBinPallets = getPalletsInBin(state.activeWarehouse, targetRak);
    if (targetBinPallets.length >= 2) {
        showToast(`Gagal: BIN ${targetRak} sudah penuh (2 Palet terdaftar). Pemindahan dibatalkan.`, "danger");
        logAudit(activeUser.name, "TRANSFER", "MOVE_FAIL", `Gagal memindahkan Palet ${palletCode} ke ${targetRak} karena overload.`);
        return;
    }

    // SUCCESS TRANSITION
    // We execute this by appending 2 transactions in the log:
    // 1. A release (Out) from the source Rak
    // 2. A placement (In) to the target Rak
    const today = new Date();
    const formattedToday = `${today.getDate()} ${formatIndoMonth(today.getMonth())} ${today.getFullYear()}`;
    const baseNo = Math.max(...state.transactions.map(t => t.no));

    // A. Out Log
    const txOut = {
        no: baseNo + 1,
        pic: activeUser.name,
        shift: activeUser.shift,
        tanggal: formattedToday,
        barang: pallet.barang,
        qty: pallet.qty,
        satuan: pallet.satuan,
        expired: pallet.expired,
        mesin: pallet.mesin,
        rak: sourceRak,
        status: "Out",
        paletJenis: pallet.jenis,
        gudang: pallet.gudang,
        paletKode: pallet.code
    };

    // B. In Log
    const txIn = {
        no: baseNo + 2,
        pic: activeUser.name,
        shift: activeUser.shift,
        tanggal: formattedToday,
        barang: pallet.barang,
        qty: pallet.qty,
        satuan: pallet.satuan,
        expired: pallet.expired,
        mesin: pallet.mesin,
        rak: targetRak,
        status: "In",
        paletJenis: pallet.jenis,
        gudang: pallet.gudang,
        paletKode: pallet.code
    };

    state.transactions.push(txOut, txIn);
    saveState();

    // Log the transaction in audit logs with a structural prefix for parsing in history
    logAudit(activeUser.name, "TRANSFER", "MOVE", `Palet ${pallet.code} berisi ${pallet.barang} dipindahkan dari BIN ${sourceRak} ke BIN ${targetRak}`);
    showToast(`Palet ${pallet.code} berhasil direlokasikan dari BIN ${sourceRak} ke ${targetRak}!`, "success");

    resetTransferInfo();
    renderTransfer();
}

// Helpers to extract variables from action text strings
function extractPalletFromDesc(desc) {
    const match = desc.match(/Palet\s(\S+)/);
    return match ? match[1] : "N/A";
}
function extractProductFromDesc(desc) {
    const match = desc.match(/berisi\s(.+?)\sdipindahkan/);
    return match ? match[1] : "Produk";
}
function extractSourceFromDesc(desc) {
    const match = desc.match(/dari BIN\s(\S+)/);
    return match ? match[1] : "N/A";
}
function extractDestFromDesc(desc) {
    const match = desc.match(/ke BIN\s(\S+)/);
    return match ? match[1] : "N/A";
}

// ----------------------------------------------------
// MODUL: HAK AKSES (RBAC)
// ----------------------------------------------------
function renderRBAC() {
    const roleSelect = document.getElementById("rbac-role-select");
    loadRolePermissionsMatrix(roleSelect.value);
    renderRBACUsers();
}

function loadRolePermissionsMatrix(roleName) {
    const tableBody = document.getElementById("rbac-permissions-matrix-body");
    if (!tableBody) return;

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const isOperator = activeUser.role === "Operator Gudang";

    const modules = [
        { code: "dashboard", name: "Dashboard Overview" },
        { code: "locator", name: "Stock Locator & Rekap" },
        { code: "inbound", name: "Inbound (Barang Masuk)" },
        { code: "outbound", name: "Outbound (Barang Keluar)" },
        { code: "transfer", name: "Transfer (Move)" },
        { code: "rbac", name: "Hak Akses & Users" },
        { code: "logs", name: "Log Riwayat Audit" }
    ];

    const permissions = state.roles[roleName] || {};

    let html = "";
    modules.forEach(m => {
        const p = permissions[m.code] || { read: false, create: false, update: false, delete: false };
        // Disable checkboxes if current user is Operator Gudang (RBAC Read-only) or if looking at Super Admin (cannot be changed easily)
        const disabledAttr = (isOperator || roleName === "Super Admin") ? "disabled" : "";

        html += `
            <tr class="align-middle">
                <td class="font-semibold text-slate-200">${m.name}</td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="perm-${m.code}-read" ${p.read ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="perm-${m.code}-create" ${p.create ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="perm-${m.code}-update" ${p.update ? 'checked' : ''} ${disabledAttr}>
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input bg-slate-800 border-slate-700" id="perm-${m.code}-delete" ${p.delete ? 'checked' : ''} ${disabledAttr}>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
}

function saveRolePermissions() {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    // 1. RBAC Update Permission Check
    if (!userPermissions.rbac.update) {
        showToast("Akses Ditolak: Peran Anda tidak memiliki izin untuk mengubah hak akses!", "danger");
        return;
    }

    const roleName = document.getElementById("rbac-role-select").value;
    if (roleName === "Super Admin") {
        showToast("Gagal: Role Super Admin tidak dapat dikurangi hak aksesnya demi alasan keamanan!", "danger");
        return;
    }

    const modules = ["dashboard", "locator", "inbound", "outbound", "transfer", "rbac", "logs"];
    
    modules.forEach(m => {
        state.roles[roleName][m] = {
            read: document.getElementById(`perm-${m}-read`).checked,
            create: document.getElementById(`perm-${m}-create`).checked,
            update: document.getElementById(`perm-${m}-update`).checked,
            delete: document.getElementById(`perm-${m}-delete`).checked
        };
    });

    saveState();
    logAudit(activeUser.name, "RBAC", "UPDATE", `Memperbarui hak akses role '${roleName}'`);
    showToast(`Hak akses untuk role '${roleName}' berhasil disimpan!`, "success");
    renderUI();
}

function renderRBACUsers() {
    const listEl = document.getElementById("rbac-users-list");
    if (!listEl) return;

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const isOperator = activeUser.role === "Operator Gudang";

    let html = "";
    state.users.forEach(u => {
        let badgeColor = "bg-slate-700 text-slate-300";
        if (u.role === "Super Admin") badgeColor = "bg-red-500/20 text-red-400 border border-red-500/30";
        else if (u.role === "Warehouse Supervisor") badgeColor = "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
        
        // Show delete button only if current user is not Operator Gudang
        const deleteButton = (isOperator || u.name === "Admin WMS") ? "" : `
            <button onclick="deleteUser('${u.name}')" class="btn btn-outline-danger btn-xs py-0.5 px-2 border-slate-700 text-rose-400 rounded"><i class="bi bi-trash"></i></button>
        `;

        html += `
            <div class="flex justify-between items-center p-3 bg-slate-900/40 rounded-lg border border-slate-700/30 text-xs">
                <div>
                    <div class="font-semibold text-white">${u.name}</div>
                    <div class="text-xxs text-slate-400 mt-0.5">Shift ${u.shift}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="badge ${badgeColor} text-xxs font-normal">${u.role}</span>
                    ${deleteButton}
                </div>
            </div>
        `;
    });

    listEl.innerHTML = html;
}

function handleAddUserSubmit(event) {
    event.preventDefault();

    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    if (!userPermissions.rbac.create) {
        showToast("Akses Ditolak: Peran Anda tidak memiliki izin menambah user!", "danger");
        return;
    }

    const name = document.getElementById("new-user-name").value.trim();
    const role = document.getElementById("new-user-role").value;
    const shift = Number(document.getElementById("new-user-shift").value);

    if (state.users.find(u => u.name.toLowerCase() === name.toLowerCase())) {
        showToast(`Gagal: User dengan nama '${name}' sudah terdaftar!`, "danger");
        return;
    }

    state.users.push({ name, role, shift });
    saveState();

    logAudit(activeUser.name, "RBAC", "CREATE", `Menambahkan user baru: ${name} (Role: ${role}, Shift: ${shift})`);
    showToast(`User '${name}' berhasil didaftarkan!`, "success");

    // Close Modal via bootstrap instance API
    const modalEl = document.getElementById("addUserModal");
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    // Reset Form
    document.getElementById("new-user-name").value = "";

    setupUserSimulator();
    renderRBAC();
}

function deleteUser(name) {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    if (!userPermissions.rbac.delete) {
        showToast("Akses Ditolak: Peran Anda tidak memiliki izin menghapus user!", "danger");
        return;
    }

    if (name === activeUser.name) {
        showToast("Gagal: Anda tidak dapat menghapus diri sendiri yang sedang aktif!", "danger");
        return;
    }

    state.users = state.users.filter(u => u.name !== name);
    saveState();

    logAudit(activeUser.name, "RBAC", "DELETE", `Menghapus user: ${name}`);
    showToast(`User '${name}' berhasil dihapus dari sistem.`, "success");

    setupUserSimulator();
    renderRBAC();
}

// ----------------------------------------------------
// MODUL: AUDIT LOGS (HISTORY)
// ----------------------------------------------------
function renderLogs() {
    filterLogs();
}

function filterLogs() {
    const tableBody = document.getElementById("logs-table-body");
    if (!tableBody) return;

    const moduleFilter = document.getElementById("log-filter-module").value;
    const actionFilter = document.getElementById("log-filter-action").value;

    let filtered = [...state.auditLogs].sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

    if (moduleFilter) {
        filtered = filtered.filter(l => l.module === moduleFilter);
    }
    if (actionFilter) {
        filtered = filtered.filter(l => l.action === actionFilter);
    }

    let html = "";
    filtered.forEach(l => {
        let badgeColor = "badge-in"; // default
        if (l.action === "DELETE" || l.action === "DENIED" || l.action === "MOVE_FAIL" || l.action === "CREATE_FAIL") badgeColor = "badge-out";
        else if (l.action === "UPDATE" || l.action === "MOVE") badgeColor = "badge-penuh text-warning-color";

        html += `
            <tr class="font-mono text-xs">
                <td class="text-slate-400 text-xxs font-sans">${l.timestamp}</td>
                <td class="font-sans font-medium text-white">${l.user}</td>
                <td><span class="bg-slate-800 text-slate-300 py-0.5 px-2 rounded-full border border-slate-700/50 text-xxs font-sans">${l.module}</span></td>
                <td><span class="badge ${badgeColor} text-xxs">${l.action}</span></td>
                <td class="text-slate-300 font-sans text-xs">${l.desc}</td>
            </tr>
        `;
    });

    if (html === "") {
        html = `<tr><td colspan="5" class="text-center py-4 text-slate-500">Tidak ada log aktivitas.</td></tr>`;
    }
    tableBody.innerHTML = html;
    document.getElementById("logs-count").textContent = `Total: ${filtered.length} Log Terdeteksi`;
}

function clearAllLogsSimulated() {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    if (!userPermissions.logs.delete) {
        showToast("Akses Ditolak: Peran Anda tidak memiliki izin untuk mengosongkan log history!", "danger");
        return;
    }

    state.auditLogs = [
        { timestamp: new Date().toISOString().replace('T', ' ').substring(0, 19), user: activeUser.name, module: "SYSTEM", action: "DELETE", desc: "Membersihkan seluruh riwayat audit log." }
    ];
    saveState();
    showToast("Audit log berhasil dibersihkan!", "success");
    renderLogs();
}

function deleteTransaction(no) {
    const activeUser = state.users.find(u => u.name === state.currentUser) || state.users[0];
    const userPermissions = state.roles[activeUser.role] || state.roles["Operator Gudang"];

    // Delete checks: Inbound delete permission or Outbound delete permission based on what tab is active
    if (state.currentTab === "inbound" && !userPermissions.inbound.delete) {
        showToast("Akses Ditolak: Anda tidak memiliki hak menghapus data inbound!", "danger");
        return;
    }
    if (state.currentTab === "outbound" && !userPermissions.outbound.delete) {
        showToast("Akses Ditolak: Anda tidak memiliki hak menghapus data outbound!", "danger");
        return;
    }

    const txIndex = state.transactions.findIndex(t => t.no === no);
    if (txIndex === -1) return;

    const tx = state.transactions[txIndex];
    state.transactions.splice(txIndex, 1);
    saveState();

    logAudit(activeUser.name, state.currentTab.toUpperCase(), "DELETE", `Menghapus transaksi #${tx.no}: ${tx.barang} (${tx.qty} ${tx.satuan}) di ${tx.rak}`);
    showToast(`Transaksi #${tx.no} berhasil dihapus!`, "success");
    
    renderUI();
}

// ----------------------------------------------------
// GLOBAL HELPERS & ALERTS SYSTEM
// ----------------------------------------------------
function logAudit(user, module, action, desc) {
    const now = new Date();
    const dateStr = now.getFullYear() + '-' + 
        String(now.getMonth() + 1).padStart(2, '0') + '-' + 
        String(now.getDate()).padStart(2, '0') + ' ' + 
        String(now.getHours()).padStart(2, '0') + ':' + 
        String(now.getMinutes()).padStart(2, '0') + ':' + 
        String(now.getSeconds()).padStart(2, '0');

    state.auditLogs.unshift({
        timestamp: dateStr,
        user: user,
        module: module,
        action: action,
        desc: desc
    });
    saveState();
}

function showToast(message, type = "info") {
    const container = document.getElementById("toast-container");
    if (!container) return;

    let icon = "bi-info-circle-fill text-sky-400";
    if (type === "success") icon = "bi-check-circle-fill text-emerald-400";
    else if (type === "danger") icon = "bi-x-circle-fill text-rose-400";
    else if (type === "warning") icon = "bi-exclamation-triangle-fill text-amber-400";

    const toast = document.createElement("div");
    toast.className = "wms-toast";
    toast.innerHTML = `
        <i class="bi ${icon} text-lg shrink-0"></i>
        <div class="text-xs font-semibold leading-normal flex-grow">${message}</div>
    `;

    container.appendChild(toast);

    // Auto-remove after 4.5 seconds
    setTimeout(() => {
        toast.style.animation = "slideIn 0.3s ease reverse forwards";
        setTimeout(() => {
            if (container.contains(toast)) container.removeChild(toast);
        }, 300);
    }, 4500);
}

// Date helpers
function formatIndoDate(isoDateStr) {
    // Expected format: YYYY-MM-DD
    if (!isoDateStr) return "-";
    const parts = isoDateStr.split('-');
    if (parts.length !== 3) return isoDateStr; // Fallback to raw string if already formatted

    const year = parts[0];
    const month = Number(parts[1]) - 1;
    const day = parts[2];

    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return `${Number(day)} ${months[month]} ${year}`;
}

function formatIndoMonth(monthIdx) {
    const months = ["Juli", "Agustus", "September", "Oktober", "November", "Desember", "Januari", "Februari", "Maret", "April", "Mei", "Juni"];
    // Offset standard months if shift relative
    const standardMonths = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return standardMonths[monthIdx];
}
