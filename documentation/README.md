# Dokumentasi Sistem Warehouse Management System (WMS) Indraco

Selamat datang di repositori dokumentasi teknis dan operasional **WMS Indraco** (*Warehouse Management System*). Dokumentasi ini disusun secara komprehensif berdasarkan peran pengguna (*role-based*), mencakup arsitektur sistem, aturan bisnis logistik, skema database, logika alur kerja controller, hingga flowchart mendalam menggunakan diagram Mermaid.

---

## 1. Ikhtisar Sistem & Arsitektur

WMS Indraco dirancang untuk mengelola tata letak pergudangan multi-tier, pelacakan inventaris berbasis unit terkecil (*Base Unit*), penempatan palet cerdas (*smart slotting*), serta integrasi pelacakan Multi-Expired Date (Multi-ED) dan FIFO/FEFO.

### Hierarki Tata Letak Gudang:
```
Office (Kantor Pusat)
  └── Branch / Plant (Pabrik / Cabang, contoh: INDRACO PLANT 1)
        └── Warehouse (Gudang Fisik, contoh: GSK.678.I)
              └── Rack (Baris Rak, contoh: RK-A, RK-B)
                    └── Bin (Slot Rak Level & Kolom, contoh: B.10.1, B.10.2)
                          └── Pallet (Kapasitas: 18, 72, 84 unit)
                                └── Box / SKU (Multi-ED, Batch Machine Code)
                                      └── Product (Konversi UOM Dinamis)
```

---

## 2. Matriks Hak Akses Berdasarkan Peran (Role-Based Access Control)

Sistem mengadopsi RBAC terpusat yang diinisialisasi melalui [RoleSeeder.php](file:///C:/laragon/www/%23Project2026/wms-indraco/database/seeds/RoleSeeder.php) dan dikelola via [RbacController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/RbacController.php):

| Modul Sistem | Super Admin (`super_admin`) | Supervisor Gudang (`supervisor`) | Operator Gudang (`operator`) |
| :--- | :---: | :---: | :---: |
| **Dashboard & KPI** | Akses Penuh (Eksekutif) | Akses Penuh (Operasional) | Akses Terbatas (Shift Task) |
| **User Management** | CRUD Penuh & Impersonasi | View Pengguna Gudang | Tidak Ada Akses |
| **RBAC Matrix** | Konfigurasi Matriks Izin | Tidak Ada Akses | Tidak Ada Akses |
| **Master Data Layout** | CRUD Penuh (Office, Plant, WH, Rack, Bin, UOM, Product) | Kelola Rack, Bin, Product, UOM (Read/Update) | Read Only (Katalog Produk) |
| **Pallet Management** | CRUD Penuh, Barcode Generator, Kapasitas | Monitoring, Status Palet, Alokasi Slot | View & Scan Palet |
| **Inbound (Penerimaan)** | Eksekusi & Monitoring Penuh | Verifikasi, Override & Monitoring | Eksekusi Penerimaan & Put-away |
| **Outbound (Pengeluaran)** | Eksekusi & Monitoring Penuh | Otorisasi Picking & Ekspedisi | Eksekusi Picking (Ekspedisi / In-Transit) |
| **Transfer Antar Palet / Bin** | Eksekusi & Monitoring Penuh | Otorisasi Relokasi | Eksekusi Pemindahan & Relokasi |
| **Stock Locator (Detail)** | Akses Penuh | Audit Stok & Pencarian Lokasi | Pencarian Lokasi Slot & Cek Bin Kosong |
| **Barcode Scanner** | Simulator Penuh | Simulator & Verifikasi Fisik | Alat Kerja Utama (Handheld Scanner) |
| **Audit & Transaction Logs** | Audit Trail Lengkap & Ekspor | Log Transaksi Operasional Gudang | Tidak Ada Akses |
| **Settings** | Konfigurasi Sistem | Tidak Ada Akses | Tidak Ada Akses |

---

## 3. Tujuh Ketentuan Inti Operasional WMS Indraco

Sistem menerapkan validasi ketat pada [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php):

1. **Ketentuan 1 (Single Product First Receive):** Saat palet pertama kali diterima pada proses Inbound, palet hanya boleh diisi oleh 1 macam SKU/produk.
2. **Ketentuan 2 (Mixed-Pallet Transfer):** Penggabungan produk berbeda pada satu palet hanya diizinkan melalui fitur **Transfer Antar Palet**.
3. **Ketentuan 3 (Dynamic Multi-UOM Conversion):** Setiap produk memiliki UOM dasar (*inventory unit*) dan faktor konversi fleksibel (cth: Karton, Pack, Sachet, Ball). Semua stok disimpan dalam kuantitas UOM terkecil.
4. **Ketentuan 4 (Dual Outbound Flow):** Pengeluaran barang mendukung 2 alur:
   - *Keluar Ekspedisi*: Pemotongan stok permanen untuk pengiriman pelanggan/distributor.
   - *Keluar In-Transit*: Penurunan palet dari slot rak ke area staging/transit tanpa menghapus stok.
5. **Ketentuan 5 (Pallet Capacity Rules):** Kapasitas palet terstandardisasi menjadi 18, 72, atau 84 unit dengan indikator visual keterisian.
6. **Ketentuan 6 (Multi-Expired Date per Pallet):** Dalam satu palet yang sama, sistem mendukung penyimpanan beberapa batch dengan tanggal kedaluwarsa (*Expired Date*) dan kode mesin (*Machine Code*) berbeda.
7. **Ketentuan 7 (Transit Staging & Slotting Limit):** Palet baru secara default berstatus `in_transit` hingga dipindahkan ke rak fisik. Setiap slot Bin dibatasi maksimal 2 palet (100% kapasitas).

---

## 4. Struktur Direktori Dokumentasi

Dokumentasi rinci per fitur disusun dalam folder masing-masing role:

```
documentation/
├── README.md                                          # Master Panduan & Arsitektur WMS
├── super_admin/                                       # Dokumentasi Role Super Admin
│   ├── 01_dashboard_and_kpi.md                       # Dashboard Eksekutif & KPI Gudang
│   ├── 02_user_management_and_impersonation.md       # Manajemen User & Fitur Impersonasi
│   ├── 03_rbac_permission_matrix.md                  # Matriks Hak Akses Dinamis RBAC
│   ├── 04_master_data_management.md                  # Manajemen Master Data Layout & Produk
│   ├── 05_pallet_management_system.md                # Sistem Registrasi & Manajemen Palet
│   ├── 06_inbound_transactions.md                    # Pengawasan Transaksi Inbound Put-away
│   ├── 07_outbound_transactions.md                   # Pengawasan Transaksi Outbound Picking
│   ├── 08_transfer_transactions.md                   # Pengawasan Relokasi & Transfer Antar Palet
│   ├── 09_stock_locator_and_detail_data.md           # Stock Locator & Grid Slotting Detail
│   ├── 10_barcode_scanner_simulator.md               # Simulator Handheld Scanner Barcode/QR
│   ├── 11_audit_trail_and_transaction_logs.md        # Audit Trail & Log Transaksi Komprehensif
│   └── 12_profile_and_system_settings.md             # Pengaturan Profil & Parameter Sistem
├── supervisor/                                        # Dokumentasi Role Supervisor Gudang
│   ├── 01_supervisor_dashboard.md                    # Dashboard Operasional & Warning Stok
│   ├── 02_warehouse_layout_and_catalog.md            # Katalog Layout Rak/Bin & Data Produk
│   ├── 03_pallet_capacity_monitoring.md              # Monitoring Kapasitas & Status Palet
│   ├── 04_inbound_verification.md                    # Verifikasi Transaksi Penerimaan Barang
│   ├── 05_outbound_authorization.md                  # Otorisasi & Kontrol Pengeluaran Barang
│   ├── 06_transfer_relocation_control.md             # Kontrol Pemindahan Barang & Antar Palet
│   ├── 07_stock_locator_and_audit.md                 # Stock Locator & Stock Opname Audit
│   ├── 08_barcode_scanner_simulator.md               # Alat Audit Fisik Scanner Barcode
│   └── 09_transaction_logs_monitoring.md             # Pemantauan Log Transaksi Harian
└── operator/                                          # Dokumentasi Role Operator Gudang
    ├── 01_operator_dashboard.md                      # Dashboard Kerja Shift & Quick Actions
    ├── 02_inbound_execution.md                       # Eksekusi Put-away Barang & Barcode Scan
    ├── 03_outbound_picking.md                        # Eksekusi Pengambilan Barang (Picking)
    ├── 04_transfer_operations.md                     # Eksekusi Pemindahan Bin & Antar Palet
    ├── 05_barcode_scanner_simulator.md               # Penggunaan Handheld Scanner Simulator
    └── 06_stock_locator_inquiry.md                   # Pencarian Cepat Lokasi Stok & Cek Bin Kosong
```

---

## 5. Panduan Navigasi Cepat

Untuk mempelajari fungsi spesifik masing-masing role, silakan buka dokumen terkait:
- [Dokumentasi Lengkap Super Admin](file:///C:/laragon/www/%23Project2026/wms-indraco/documentation/super_admin/01_dashboard_and_kpi.md)
- [Dokumentasi Lengkap Supervisor Gudang](file:///C:/laragon/www/%23Project2026/wms-indraco/documentation/supervisor/01_supervisor_dashboard.md)
- [Dokumentasi Lengkap Operator Gudang](file:///C:/laragon/www/%23Project2026/wms-indraco/documentation/operator/01_operator_dashboard.md)
