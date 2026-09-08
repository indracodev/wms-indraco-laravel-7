# Warehouse Management System (WMS) - PT Indraco Global Indonesia

![Laravel Version](https://img.shields.io/badge/Laravel-7.30.7-ff2d20?style=for-the-badge&logo=laravel)
![PHP Version](https://img.shields.io/badge/PHP-7.4%20%7C%208.2-777bb4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479a1?style=for-the-badge&logo=mysql)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8?style=for-the-badge&logo=tailwindcss)
![Status](https://img.shields.io/badge/Status-Production%20Ready-emerald?style=for-the-badge)

System Manajemen Gudang (WMS) berbasis **Laravel 7** yang dirancang untuk mengelola hierarki lokasi penyimpanan 7-tingkat, pelacakan stok palet & box karton, mutasi barang (Inbound, Outbound, Transfer), proteksi fisik kapasitas BIN, otorisasi RBAC granular, impersonasi user, serta simulator barcode scanner.

---

## 📌 1. Aturan Bisnis Utama (Business Rules)

1. **Struktur Hierarki Lokasi 7-Tingkat**:
   - `Offices` (Kantor Pusat / Perwakilan)
   - `Branches` (Cabang)
   - `Warehouses` (Gudang)
   - `Racks` (Rak Penyimpanan)
   - `Bins` (Slot BIN Rak)
   - `Pallets` (Palet Kayu/Loscam/Plastik)
   - `Boxes` (Karton Box Produk)

2. **Proteksi Kapasitas Fisik BIN (Max 2 Palet)**:
   - Setiap slot BIN memiliki batas maksimal **2 Palet** (`max_pallet = 2`).
   - Layer transaksi mengunci record BIN (`lockForUpdate()`). Transaksi Inbound / Transfer ke BIN yang sudah terisi 2 palet otomatis **ditolak sistem** dengan pesan error/warning.

3. **Multi-Role & Otorisasi RBAC**:
   - **Super Admin**: Akses penuh seluruh modul, manajemen user, matriks permission, dan fitur *Login As* (Impersonasi).
   - **Supervisor Gudang**: Manajemen master data, otorisasi transaksi, dan laporan stok.
   - **Operator Gudang**: Eksekusi transaksi Inbound Putaway, Outbound Picking, dan Transfer BIN.

4. **Sistem Impersonasi Admin (Login As & Revert)**:
   - Super Admin dapat beralih ke akun user lain secara langsung untuk troubleshooting/pengujian tanpa memerlukan password user target, dan dapat kembali ke akun Admin asli secara instan.

5. **Audit Trail & Logging Dual-Layer**:
   - Log Mutasi Transaksi (`transaction_logs`): Catatan riwayat masuk/keluar barang.
   - Log Audit System (`audit_logs`): Catatan aktivitas penciptaan, pengubahan, pemindahan data, dan autentikasi.

---

## 🚀 2. Stack Teknologi

- **Backend Framework**: Laravel `v7.30.7`
- **PHP Version**: PHP `7.4.32` / PHP `8.2.x` (Disertai patch error suppression untuk kompatibilitas PHP 8.2)
- **Database**: MySQL `8.0` / MariaDB (InnoDB, Foreign Keys, Transaction Lock)
- **Frontend Stack**:
  - Blade Templating Engine (Server-side rendering modular)
  - TailwindCSS & Glassmorphism Design Token
  - Alpine.js (Reactive UI state & modal)
  - Chart.js (Visualisasi statistik okupansi & expired date)
  - FontAwesome 6 (Ikon sistem)
- **Testing Engine**: PHPUnit 9.6

---

## 💻 3. Fitur Utama Sistem

| Modul | Deskripsi Fitur |
|---|---|
| **Dashboard Adaptive** | KPI Okupansi BIN, Peringatan Critical BIN 2 Palet (FULL), Grafik Expired Stock (<90 hari), & Log Mutasi Terakhir. |
| **Master Data** | Management CRUD Kantor, Cabang, Gudang, Rak, Slot BIN, Jenis Palet, & SKU Produk. |
| **Stock Locator** | Pencarian lokasi fisik stok barang per BIN, Palet, SKU Produk, & Tanggal Kedaluwarsa. |
| **Inbound Putaway** | Form registrasi barang masuk ke palet & BIN dengan penguncian validasi batas 2 palet. |
| **Outbound Picking** | Form pengambilan barang dari palet aktif berdasarkan SKU & Qty. |
| **Transfer BIN** | Pemindahan palet antar BIN dengan validasi kapasitas BIN tujuan. |
| **Scanner Simulator** | Panel simulasi scan barcode fisik (`PLT-`, `PRD-`, `B.x.x`, `BOX-`) via AJAX API. |
| **Manajemen User** | Pengelolaan akun, penetapan shift kerja, dan tombol *Login As* (Impersonasi). |
| **Matriks RBAC** | Grid pengatur hak akses modul granular (`can_read`, `can_create`, `can_update`, `can_delete`). |
| **Audit Logs** | Rekapitulasi log mutasi transaksi, audit aktivitas system, dan histori impersonasi. |

---

## ⚙️ 4. Petunjuk Instalasi & Cara Menjalankan

### A. Persyaratan Environment
- Laragon (Apache/Nginx + PHP 7.4 / PHP 8.2 + MySQL 8.0)
- Composer 2.x

### B. Langkah Instalasi

1. **Clone / Buka Directory Project**:
   ```bash
   cd "C:\laragon\www\WMS Indraco"
   ```

2. **Konfigurasi File Environment (`.env`)**:
   Pastikan konfigurasi database pada `.env` sudah sesuai:
   ```env
   APP_NAME="WMS Indraco"
   APP_ENV=local
   APP_KEY=base64:3jnj8p1RUl/+MSFFJZV8FkRTnPj5oYGLju2tQtP48yQ=
   APP_DEBUG=true
   APP_URL=http://localhost

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=wms_indraco
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Jalankan Server Aplikasi**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di: **`http://127.0.0.1:8000`**

---

## 🔑 5. Kredensial Login Default (Seeder Data)

Password default untuk seluruh akun demo adalah: **`[username]123`**

| Role | Username | Password | Deskripsi Akses |
|---|---|---|---|
| **Super Admin** | `admin` | `admin123` | Akses Penuh Seluruh Modul + Impersonasi |
| **Supervisor** | `budi` | `budi123` | Master Data, Transaksi, Laporan, & Monitoring |
| **Operator Gudang** | `gilang` | `gilang123` | Transaksi Inbound, Outbound, Transfer BIN (Shift 1) |
| **Operator Gudang** | `eko` | `eko123` | Transaksi Inbound, Outbound, Transfer BIN (Shift 2) |

---

## 🧪 6. Pengujian Bisnis (Testing Suite)

Proyek ini dilengkapi dengan skenario pengujian otomatis menggunakan **PHPUnit** untuk memastikan aturan bisnis terpenuhi 100%:

### Perintah Pengujian:
```bash
vendor/bin/phpunit tests/Feature/WMSBusinessRulesTest.php
```

### Skenario Test yang Dicakup:
1. `test_bin_capacity_limit_max_2_pallets`: Memastikan penambahan palet ke-3 pada BIN yang terisi 2 palet ditolak sistem.
2. `test_rbac_user_permissions`: Memastikan perizinan RBAC membatasi hak akses Operator dan memberikan akses penuh ke Admin.
3. `test_admin_impersonation_flow`: Memastikan aliran Impersonasi (Login As) dan Revert berjalan aman tanpa kehilangan sesi.
4. `test_full_inventory_mutation_cycle`: Memastikan siklus lengkap Inbound &rarr; Transfer BIN &rarr; Outbound berjalan konsisten.

---

## 📁 7. Struktur File Utama Project

```
WMS Indraco/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Auth, Dashboard, MasterData, Transaction, UserController, dll
│   │   └── Middleware/        # CheckModulePermission.php (RBAC Middleware)
│   ├── Models/                # Office, Branch, Warehouse, Rack, Bin, Pallet, Box, Product, User, Log models
│   ├── Providers/             # AppServiceProvider.php (@canAccessModule Blade directive)
│   └── Services/              # WarehouseTransactionService.php, ImpersonationService.php, AuditLogger.php
├── database/
│   ├── migrations/            # 15 file migrasi skema tabel
│   └── seeds/                 # RoleSeeder, UserSeeder, MasterDataSeeder
├── resources/
│   └── views/                 # Template Blade UI (layouts, dashboard, master, detail, transactions, rbac, scanner, logs)
├── routes/
│   └── web.php                # Definisi rute aplikasi & middleware RBAC
└── tests/
    └── Feature/               # WMSBusinessRulesTest.php (Pengujian integrasi bisnis)
```

---

## 📄 License & Ownership

Hak Cipta &copy; 2026 **PT Indraco Global Indonesia**. All rights reserved.
