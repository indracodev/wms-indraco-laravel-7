# Test Scenario: TS-MST-007 - Manajemen Master Data Katalog Produk SKU (Product)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-007
- **File Test**: `8_produk_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Konversi Multi-UOM, Dimensi/Berat, Filter, & Proteksi Stok Box Aktif
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=products`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun pengelola master data.
  2. Master Satuan UOM (misal: `PCS`, `KARTON`, `BOX`) sudah terdaftar di sistem.
  3. Tab *"7. Produk SKU"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - Kode SKU (`id`): `sku-kopi-test-01` (diuji dengan huruf kecil untuk verifikasi auto-uppercase menjadi `SKU-KOPI-TEST-01`)
    - Nama Produk: `Kopi Susu Indraco Test 250ml`
    - Brand: `Indraco Signature`
    - Item Group: `FINISHED GOODS (MANUFAKTUR)`
    - Group Level 2: `BEVERAGES`
    - Group Level 3: `READY TO DRINK`
    - UOM Inventory (Dasar): `PCS`
    - UOM Group: `KARTON`
    - Qty per UOM Group: `24`
    - Dimensi & Berat: Panjang `30 cm`, Lebar `20 cm`, Tinggi `15 cm`, Berat Kotor `6.5 kg`
    - Konversi UOM Tambahan: `1 KARTON = 24 PCS`
    - Status Aktif: `1 (Aktif)`
  - **Update Data**:
    - Nama Produk Baru: `Kopi Susu Indraco Test 250ml Extra Creamy`
    - Status Aktif Baru: `0 (Non-Aktif)`
  - **Filter Test**: Filter `brand_filter`, `group_filter`, dan `active_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **8.1** | Navigasi Tab Produk SKU | Klik tab *"7. Produk SKU"* atau buka `/master-data?tab=products` | - | Tab Produk SKU terbuka, menampilkan katalog kartu produk / tabel, ringkasan konversi UOM, filter brand/group/status, search bar, dan tombol tambah produk. |
| **8.2** | Buka Modal Form Tambah Produk | Klik tombol **"+ Tambah Produk SKU"** | Klik tombol tambah | Modal form tambah produk terbuka, memuat tab informasi dasar, spesifikasi logistik (dimensi/berat), dan tabel dinamis konversi satuan. |
| **8.3** | Validasi Field Wajib Produk | Kosongkan field Kode SKU dan Nama Produk lalu submit form | Kode SKU: `(kosong)`<br>Nama: `(kosong)` | Form menolak submit, sistem menandai field wajib dengan pesan error validasi. |
| **8.4** | Tambah Produk SKU & Konversi UOM (Create Success) | Isi seluruh parameter produk lengkap beserta konversi 1 Karton = 24 PCS lalu simpan | Data Create Valid (lihat Bagian 1) | Data tersimpan, notifikasi sukses *"Produk SKU Kopi Susu Indraco Test 250ml (SKU-KOPI-TEST-01) berhasil ditambahkan"* muncul, konversi dasar 1:1 dan konversi karton tersimpan di database, audit log mencatat aksi `CREATE`. |
| **8.5** | Validasi Duplikasi Kode SKU | Coba buat produk baru dengan Kode SKU `SKU-KOPI-TEST-01` yang sama | ID: `SKU-KOPI-TEST-01`<br>Nama: `Produk Kloning` | Sistem menolak dengan validasi error bahwa kode SKU sudah digunakan (*The id has already been taken*). |
| **8.6** | Filter Berdasarkan Brand & Status | Pilih Brand dan Status pada dropdown filter di atas katalog | Brand: `Indraco Signature`<br>Status: `Aktif` | Katalog hanya menampilkan daftar produk SKU dari brand dan status yang dipilih. |
| **8.7** | Pencarian Produk SKU (Search) | Ketik nama produk atau kode SKU pada input pencarian | Keyword: `Kopi Susu Indraco Test` | Sistem menampilkan produk SKU `SKU-KOPI-TEST-01` secara instan dan tepat. |
| **8.8** | Ubah Data Produk SKU (Update) | Klik tombol Edit pada produk `SKU-KOPI-TEST-01`, ubah nama dan ubah status aktif menjadi Non-Aktif, lalu simpan | Nama: `Kopi Susu Indraco Test 250ml Extra Creamy`<br>Status: `Non-Aktif (0)` | Data terupdate, notifikasi sukses muncul, nama produk berubah dan badge status berubah menjadi merah (Non-Aktif). |
| **8.9** | Proteksi Hapus Produk Berstok Box Aktif | Coba hapus produk SKU yang masih terdaftar pada stok box aktif di gudang (active boxes count > 0) | Klik Hapus pada SKU berstok aktif | Sistem menolak dengan error 422: *"Gagal menghapus: Produk SKU masih terdaftar pada stok box aktif di gudang!"*. |
| **8.10** | Hapus Produk SKU Uji (Delete Success) | Klik Hapus pada produk `SKU-KOPI-TEST-01` (yang tidak memiliki stok box aktif), konfirmasi dialog | Konfirmasi: `Ya, Hapus` | Produk dan tabel konversi relasinya terhapus bersih via database transaction, notifikasi sukses muncul, produk hilang dari katalog. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 09:00 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus lengkap CRUD Produk SKU, konversi multi-UOM, filter brand/kategori, dan proteksi stok box gudang sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 09:00 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=products`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **8.1** | 🟢 PASS | Tab Produk SKU terbuka dengan katalog kartu/tabel produk | Route `master.index` dengan parameter `tab=products` OK |
| **8.2** | 🟢 PASS | Modal form tambah produk terbuka lancar | Modal render form inputs lengkap |
| **8.3** | 🟢 PASS | Validasi input required SKU dan Nama berfungsi | HTML5 & Request validation berjalan |
| **8.4** | 🟢 PASS | Produk baru & konversi multi-UOM tersimpan di DB | Tabel `products` & `product_uom_conversions` terisi |
| **8.5** | 🟢 PASS | Sistem mencegah duplikasi Kode SKU | Constraint unique database berfungsi |
| **8.6** | 🟢 PASS | Filter brand dan status aktif menyaring produk dengan presisi | Query filtering backend berjalan akurat |
| **8.7** | 🟢 PASS | Pencarian nama produk menemukan item uji secara instan | Fitur search filter SKU berfungsi |
| **8.8** | 🟢 PASS | Update nama produk dan status aktif/non-aktif sukses | Kolom `name` & `is_active` terbarui di database |
| **8.9** | 🟢 PASS | Proteksi hapus produk yang masih memiliki box aktif berfungsi | Validasi integritas stok gudang aktif |
| **8.10** | 🟢 PASS | Produk SKU uji `SKU-KOPI-TEST-01` terhapus bersih | Transaction delete produk dan konversi berhasil |

---

<!-- TEMPLATE UNTUK RUN BERIKUTNYA (COPY & APPEND DI BAWAH INI):
### Run #[N] - YYYY-MM-DD HH:mm WIB - [STATUS]
- **Environment**: Localhost / Staging
- **Base URL**: 
- **Penguji / Runner**: 
- **Browser / Tool**: 
- **Status Akhir**: 🟢 PASSED / 🔴 FAILED / 🟡 BLOCKED
- **Catatan Umum**: 

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **8.1** | 🟢 PASS | | |
| **8.2** | 🟢 PASS | | |
| **8.3** | 🟢 PASS | | |
| **8.4** | 🟢 PASS | | |
| **8.5** | 🟢 PASS | | |
| **8.6** | 🟢 PASS | | |
| **8.7** | 🟢 PASS | | |
| **8.8** | 🟢 PASS | | |
| **8.9** | 🟢 PASS | | |
| **8.10** | 🟢 PASS | | |
-->
