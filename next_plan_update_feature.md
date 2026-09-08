# Implementation Plan - Rencana Pembaruan Fitur Master Data & Terminologi Sistem WMS

Dokumen ini berisi rencana pengembangan dan pembaruan fitur (*Next Update Plan*) untuk sistem Warehouse Management System (WMS) Indraco berdasarkan 4 kebutuhan utama pembaruan master data dan terminologi organisasi.

---

## Ringkasan Kebutuhan Pembaruan (Summary of Requirements)

1. **Perubahan Terminologi: "Cabang" diganti menjadi "Plant"**
   - Mengubah seluruh penyebutan istilah "Cabang" / "Branch" pada seluruh tampilan antarmuka (UI), sidebar navigasi, judul modul, tabel master data, modal form, filter dropdown, controller, dan dokumen acuan menjadi istilah **"Plant"**.

2. **Pengayaan Tabel Rak (Rack Master): Penambahan Kolom Kode Gudang**
   - Menambahkan kolom **Kode Gudang (`warehouse_id`)** secara eksplisit pada tabel Master Data Rak, berdampingan dengan Nama Gudang, untuk mempermudah identifikasi dan pemilahan rak fisik gudang.

3. **Master Data: Fitur Search, Filter, Pagination, & Sort**
   - Menambahkan dan menyempurnakan fitur pengelolaan data master secara komprehensif pada seluruh tabel Master Data (Plant, Gudang, Rak, BIN, Jenis Palet, Produk SKU, & User/Role):
     - **Pencarian (Search)**: Search bar kata kunci (Kode, Nama, Deskripsi).
     - **Penyaringan (Filter)**: Filter per Plant, per Gudang, per Rak, per Status Aktif/Non-Aktif.
     - **Halaman (Pagination)**: Pagination terstruktur (10 / 15 / 25 item per halaman) dengan mempertahankan query parameter URL.
     - **Pengurutan (Sort)**: Fitur clickable column headers untuk pengurutan ASC/DESC (berdasarkan Kode, Nama, Tanggal Buat).

4. **Input Kode Master Data: Manual Input & Auto-Generate Otomatis**
   - Menambahkan opsi **Auto-Generate Kode** pada seluruh Form Modal Pendaftaran Master Data (Plant Code, Kode Gudang, Kode Rak, Kode BIN, Kode Jenis Palet, & SKU Produk).
   - Operator tetap dapat memasukkan kode secara **manual**, atau menekan tombol **"Auto"** untuk menghasilkan kode standar sistem otomatis (contoh: `PLANT-xxx`, `WHS-xxx`, `RCK-xxx`, `BIN-xxx`, `SKU-xxx`).

---

## User Review Required

> [!IMPORTANT]
> - **Terminologi Model DB**: Perubahan dari "Cabang" ke "Plant" mencakup penggantian teks pada UI, label, dan pesan aplikasi. Nama tabel database underlying (`branches`) dan relasi Eloquent (`branch()`) dapat dipertahankan atau di-aliased sebagai `plant()` untuk menjaga backward-compatibility migrasi database existing.
> - **Format Auto-Generate Kode**: Kode auto-generate akan mengikuti konvensi standar (e.g. `PLANT-001`, `WHS-GSK-01`, `RCK-A.01`, `BIN-A.01.1`) namun tetap dapat diedit secara manual oleh operator sebelum disimpan.

---

## Proposed Changes

---

### Component 1: Terminologi & Rebranding (Cabang -> Plant)

#### [MODIFY] [sidebar.blade.php](file:///C:/laragon/www/WMS%20Indraco/resources/views/layouts/sidebar.blade.php)
- Mengubah label navigasi "Master Cabang / Branch" menjadi **"Master Plant"**.
- Mengubah icon dan sub-menu yang relevan dengan istilah Plant.

#### [MODIFY] [master/index.blade.php](file:///C:/laragon/www/WMS%20Indraco/resources/views/master/index.blade.php)
- Mengubah label Tab Header "Cabang" menjadi **"Plant"**.
- Mengubah label kolom tabel, form input modal (`Kode Plant`, `Nama Plant`, `Alamat Plant`), dan pesan notifikasi.

#### [MODIFY] [MasterDataController.php](file:///C:/laragon/www/WMS%20Indraco/app/Http/Controllers/MasterDataController.php)
- Mengubah pesan sukses notifikasi saat registrasi/update dari "Cabang berhasil disimpan" menjadi **"Plant berhasil disimpan"**.

---

### Component 2: Pengayaan Tabel Master Data Rak (Kode Gudang Column)

#### [MODIFY] [master/index.blade.php](file:///C:/laragon/www/WMS%20Indraco/resources/views/master/index.blade.php)
- Pada tabel **Master Data Rak (Rack Table)**, menambahkan th/td untuk **Kode Gudang (`warehouse_id`)** disamping **Nama Gudang (`warehouse.name`)**.
- Contoh Tampilan Kolom:
  - `ID Rak` | `Kode Gudang` | `Nama Gudang` | `Plant / Lokasi` | `Jumlah BIN` | `Aksi`
  - `RCK-A.01` | `GSK.678.I` | `Gudang Utama Gresik` | `Plant Gresik` | `12 BIN` | `Edit / Hapus`

---

### Component 3: Search, Filter, Pagination, & Sort pada Master Data

#### [MODIFY] [MasterDataController.php](file:///C:/laragon/www/WMS%20Indraco/app/Http/Controllers/MasterDataController.php)
- Mengimplementasikan query builder terpisah untuk masing-masing tab Master Data:
  - **Plant Query**: Filter search kode/nama + Sort + Paginate(10).
  - **Warehouse Query**: Filter per Plant + Search kode/nama + Sort + Paginate(10).
  - **Rack Query**: Filter per Gudang/Kode Gudang + Search kode/nama + Sort + Paginate(10).
  - **BIN Query**: Filter per Rak/Gudang + Search ID BIN + Sort + Paginate(15).
  - **Product Query**: Filter per Brand/Group/Status + Search SKU/Nama + Sort + Paginate(15).
  - **Pallet Type Query**: Filter status + Search + Sort + Paginate(10).

#### [MODIFY] [master/index.blade.php](file:///C:/laragon/www/WMS%20Indraco/resources/views/master/index.blade.php)
- Menambahkan toolbar Search Input Bar dan Dropdown Filter pada setiap Tab Master Data.
- Menambahkan Clickable Column Headers untuk Sorting (dengan indicator icon `fa-sort-asc` / `fa-sort-desc`).
- Menambahkan link pagination Laravel (`{{ $data->appends(request()->query())->links() }}`) pada footer setiap tabel master data.

---

### Component 4: Auto-Generate vs Manual Input Kode Master Data

#### [MODIFY] [master/index.blade.php](file:///C:/laragon/www/WMS%20Indraco/resources/views/master/index.blade.php)
- Menambahkan tombol **"Auto"** pada seluruh Form Modal Tambah Master Data:
  - **Modal Tambah Plant**: Input `id` / `code` + Tombol `Auto` -> Generates `PLANT-001`, `PLANT-002`.
  - **Modal Tambah Gudang**: Input `id` + Tombol `Auto` -> Generates `WHS-001`, `WHS-002`.
  - **Modal Tambah Rak**: Input `id` + Tombol `Auto` -> Generates `RCK-A.01`, `RCK-B.01`.
  - **Modal Tambah BIN**: Input `id` + Tombol `Auto` -> Generates `BIN-A.01.1`.
  - **Modal Tambah Jenis Palet**: Input `id` + Tombol `Auto` -> Generates `PLT-TYPE-01`.
  - **Modal Tambah Produk**: Input `id` (SKU) + Tombol `Auto` -> Generates `SKU-889911`.
- Memungkinkan operator mengetik kode secara manual ATAU menekan tombol **"Auto"** untuk mengisi kode secara otomatis.

---

## Verification Plan

### Automated Verification
- Membuat script pengujian `scratch/test_next_plan_features.php` untuk memverifikasi:
  1. Perubahan data Plant.
  2. Pengambilan data Rak dengan rincian Kode Gudang (`warehouse_id`).
  3. Filter, Search, Pagination, dan Sorting pada seluruh query MasterDataController.
  4. Generasi kode otomatis unik untuk setiap entitas master data.

### Manual Verification
1. Membuka `http://127.0.0.1:8000/master-data`.
2. Verifikasi penggantian seluruh istilah "Cabang" menjadi **"Plant"**.
3. Verifikasi kolom **Kode Gudang** pada tabel Rak.
4. Uji fitur Search, Filter, Pagination, dan Sorting pada setiap tab master data.
5. Uji tombol **"Auto"** pada setiap modal pendaftaran master data untuk memastikan kode terisi otomatis.
