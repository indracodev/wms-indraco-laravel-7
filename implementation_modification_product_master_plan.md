# Implementation Plan - Modifikasi Master Data Produk (Product Master)

Dokumen ini berisi perencanaan komprehensif untuk memperbarui dan menyesuaikan Master Data Produk pada aplikasi **WMS Indraco** sesuai dengan struktur field pada gambar referensi dan berkas Excel `C:\laragon\www\WMS Indraco\Master Item WMS.xlsx`.

---

## 1. Analisis Struktur Data & Field Mapping

Berdasarkan analisis terhadap berkas [`Master Item WMS.xlsx`](file:///C:/laragon/www/WMS%20Indraco/Master%20Item%20WMS.xlsx) (total 302 baris data, 114 produk unik) dan tangkapan layar UI, berikut adalah pemetaan field:

| Kolom Gambar / Excel | Nama Field Database | Tipe Data | Keterangan & Batasan |
| :--- | :--- | :--- | :--- |
| **Kode Item** | `id` / `item_code` | `VARCHAR(50)` | Primary Key unik (contoh: `FGD00001`, `FGD00002`) |
| **Nama Item** | `name` | `VARCHAR(255)` | Nama lengkap produk |
| **Group Item** | `item_group` | `VARCHAR(100)` | Kategori produk (`FINISHED GOODS (MANUFAKTUR)`, `PROMOTION (PROMOSI)`) |
| **UOM Inventory** | `uom_inventory` | `VARCHAR(50)` | Satuan persediaan utama (`Box`, `Bag`, `Can`, `Sachet`, `PCS`, `KG`) |
| **Grup UOM** | `uom_group` | `VARCHAR(50)` | Kode kelompok UOM (`BOX24`, `BAG10`, `SACHET300R`, dll) |
| **Isi Per Group UOM** | `qty_per_uom_group` | `DECIMAL(10,2)` | Jumlah isi dalam 1 grup UOM (contoh: `24`, `10`, `1`) |
| **Brand** | `brand` | `VARCHAR(100)` | Merek/Brand produk (`BaliCafé`, `Supresso`, `Kopi Bro`, dll) |
| **Active** | `is_active` | `BOOLEAN` | Status keaktifan item (`Yes` -> `1`, `No` -> `0`) |
| **UOM Convert** | `uom_convert` | `VARCHAR(50)` | Satuan konversi per baris (`BOX`, `BOXBNS`, `KARTON`, `SACHET`) |
| **Qty Convert** | `qty_convert` | `DECIMAL(10,2)` | Faktor/Jumlah konversi terhadap base unit (`1`, `2`, `24`) |
| **Uom Base** | `uom_base` | `VARCHAR(50)` | Satuan dasar acuan konversi (`BOX`, `BAG`, `CAN`) |

---

## 2. Rancangan Skema Database (Database Schema Design)

Dikarenakan 1 produk (`Kode Item`) dapat memiliki **beberapa aturan konversi UOM** (relasi 1-ke-banyak / *1-to-many*), maka skema database dirancang secara ternormalisasi menjadi 2 tabel:

### A. Tabel Utama: `products`
Memuat master informasi produk yang bersifat unik per `Kode Item`.

```sql
CREATE TABLE products (
    id VARCHAR(50) PRIMARY KEY,                  -- Kode Item (e.g. FGD00001)
    name VARCHAR(255) NOT NULL,                  -- Nama Item
    item_group VARCHAR(100) NULL,                -- Group Item
    uom_inventory VARCHAR(50) NOT NULL,          -- UOM Inventory
    uom_group VARCHAR(50) NULL,                  -- Grup UOM
    qty_per_uom_group DECIMAL(10,2) DEFAULT 1,  -- Isi Per Group UOM
    brand VARCHAR(100) NULL,                     -- Brand
    is_active BOOLEAN DEFAULT TRUE,              -- Status Active (1 = Yes, 0 = No)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

> **Kompatibilitas Backward**: Properti `$product->default_unit` pada Eloquent Model `Product` akan dikonfigurasi sebagai *accessor* yang mengembalikan `$this->uom_inventory`, sehingga modul lain (seperti `Box` dan `TransactionLog`) tetap berjalan tanpa breaking changes.

### B. Tabel Anak: `product_uom_conversions`
Memuat rincian konversi UOM per produk.

```sql
CREATE TABLE product_uom_conversions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(50) NOT NULL,            -- FK ke products.id
    uom_convert VARCHAR(50) NOT NULL,           -- UOM Convert (e.g. KARTON)
    qty_convert DECIMAL(10,2) NOT NULL,         -- Qty Convert (e.g. 24.00)
    uom_base VARCHAR(50) NOT NULL,              -- Uom Base (e.g. BOX)
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

---

## 3. Rencana Migrasi Database (Migration Plan)

1. **Modifikasi Migration `products`**:
   - Path: [`database/migrations/2026_01_01_000008_create_products_table.php`](file:///C:/laragon/www/WMS%20Indraco/database/migrations/2026_01_01_000008_create_products_table.php)
   - Menambahkan kolom `item_group`, `uom_inventory`, `uom_group`, `qty_per_uom_group`, `brand`, `is_active`.

2. **Buat Migration Baru `product_uom_conversions`**:
   - Path: `database/migrations/2026_01_01_000008_sub_create_product_uom_conversions_table.php`
   - Menyiapkan struktur tabel konversi UOM dengan *foreign key constraint* ke `products(id)`.

---

## 4. Rencana Seeder (Data Seeding Strategy)

Membuat Seeder `ProductMasterSeeder.php` yang membaca data secara otomatis dari berkas [`Master Item WMS.xlsx`](file:///C:/laragon/www/WMS%20Indraco/Master%20Item%20WMS.xlsx):

1. **Path File Seeder**: `database/seeds/ProductMasterSeeder.php`
2. **Mekanisme Import**:
   - Membaca dan melakukan grouping data berdasarkan `Kode Item`.
   - Mengisi tabel `products` (114 data unik).
   - Mengisi tabel `product_uom_conversions` (302 baris data konversi).
3. **Integrasi**:
   - Dipanggil di dalam `MasterDataSeeder.php` dan `DatabaseSeeder.php`.

---

## 5. Rencana Fungsi CRUD & UI (CRUD Functionality & UI Plan)

### A. Model Layer
- Modifikasi [`app/Models/Product.php`](file:///C:/laragon/www/WMS%20Indraco/app/Models/Product.php): relasi `conversions()`, fillable, dan accessor `default_unit`.
- Buat Model `app/Models/ProductUomConversion.php`: relasi `product()`.

### B. Controller Layer
- Modifikasi [`app/Http/Controllers/MasterDataController.php`](file:///C:/laragon/www/WMS%20Indraco/app/Http/Controllers/MasterDataController.php):
  - **`index()`**: Menampilkan daftar produk beserta rincian konversi UOM + fitur pencarian & filter (Brand, Group, Active).
  - **`storeProduct()`**: Menyimpan produk baru dan array baris konversi UOM.
  - **`updateProduct()`**: Mengedit produk dan menyinkronkan data konversi UOM.
  - **`destroyProduct()`**: Menghapus produk (dengan audit log & proteksi integritas transaksi).
  - Integrasi dengan `AuditLogger::log()`.

### C. Routes
- Modifikasi [`routes/web.php`](file:///C:/laragon/www/WMS%20Indraco/routes/web.php): Menambahkan rute PUT (`/master-data/product/{id}`) dan DELETE (`/master-data/product/{id}`).

### D. UI Layout & View
- Pembaruan Tab Produk SKU di [`resources/views/master/index.blade.php`](file:///C:/laragon/www/WMS%20Indraco/resources/views/master/index.blade.php):
  - Tabel desktop responsif & mobile flat cards.
  - Form modal input produk lengkap dengan fitur tambah/hapus baris konversi UOM secara realtime menggunakan Alpine.js.

---

## 6. Rencana Pengujian & Verifikasi (Verification Plan)

1. **Database & Seeder Verification**:
   - Jalankan `php artisan migrate:fresh --seed`.
   - Verifikasi jumlah record: 114 produk unik dan 302 baris konversi UOM.
2. **UI & CRUD Manual Testing**:
   - Uji coba Tambah Produk baru dengan 2 baris konversi UOM.
   - Uji coba Pencarian & Filter Brand/Group/Active status.
   - Uji coba Edit dan Hapus Produk.
