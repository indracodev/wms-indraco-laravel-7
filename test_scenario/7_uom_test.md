# Test Scenario: TS-MST-006 - Manajemen Master Data Satuan Inventaris (UOM)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-006
- **File Test**: `7_uom_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Auto-Uppercase ID, Sinkronisasi Nama, & Proteksi UOM
- **Tingkat Prioritas**: High (P1)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=uoms`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun yang memiliki hak akses master data.
  2. Tab *"6. UOM Inventory"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID UOM: `ctn_test` (input huruf kecil untuk verifikasi auto-uppercase menjadi `CTN_TEST`)
    - Nama Satuan: `KARTON TEST`
    - Deskripsi: `Satuan kardus pengujian otomatis WMS Indraco`
  - **Update Data**:
    - Nama Baru: `KARTON BESAR TEST`
    - Deskripsi Baru: `Satuan kardus besar pengujian`
  - **UOM Terpakai Produk**: UOM yang sudah terikat pada data produk (misal: `PCS` atau `BOX`)

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **7.1** | Navigasi Tab UOM | Klik tab *"6. UOM Inventory"* atau akses `/master-data?tab=uoms` | - | Tab UOM aktif, memuat daftar satuan, kode UOM, deskripsi, counter jumlah produk SKU yang menggunakan satuan tersebut, dan kolom pencarian. |
| **7.2** | Validasi Field Wajib UOM | Kosongkan field ID dan Nama Satuan pada form lalu klik Simpan | ID: `(kosong)`<br>Nama: `(kosong)` | Form menolak submit, browser/sistem menampilkan peringatan field wajib diisi. |
| **7.3** | Tambah UOM Baru & Verifikasi Auto-Uppercase | Masukkan ID dengan huruf kecil `ctn_test` dan Nama `KARTON TEST` lalu submit | ID: `ctn_test`<br>Nama: `KARTON TEST`<br>Deskripsi: `Satuan kardus pengujian` | Data tersimpan, pesan sukses *"Satuan UOM KARTON TEST (CTN_TEST) berhasil ditambahkan"* muncul, ID tersimpan sebagai huruf kapital `CTN_TEST`, audit log mencatat aksi `CREATE`. |
| **7.4** | Validasi Duplikasi ID UOM | Submit form kembali dengan ID `CTN_TEST` yang sama | ID: `CTN_TEST`<br>Nama: `Karton Duplikat` | Sistem menolak dengan pesan error validasi bahwa ID sudah terdaftar (*The id has already been taken*). |
| **7.5** | Pencarian Satuan UOM (Search) | Ketik nama satuan atau kode UOM pada kolom pencarian | Keyword: `KARTON TEST` | Tabel menampilkan baris UOM `CTN_TEST` yang sesuai dengan kata kunci pencarian. |
| **7.6** | Ubah Data UOM & Sinkronisasi Produk | Klik tombol Edit pada baris `CTN_TEST`, ubah nama menjadi `KARTON BESAR TEST` lalu simpan | Nama: `KARTON BESAR TEST`<br>Deskripsi: `Satuan kardus besar pengujian` | Data berhasil diperbarui, notifikasi sukses muncul, nama satuan di tabel berubah dan sistem secara otomatis menyinkronkan nama satuan baru ke tabel produk & konversi terkait. |
| **7.7** | Proteksi Hapus UOM yang Digunakan Produk | Coba hapus satuan UOM yang sedang digunakan oleh produk SKU aktif (misal UOM `PCS` dengan counter Produk > 0) | Klik Hapus pada UOM berelasi | Non-Superadmin dicegah dengan alert error 422: *"Gagal menghapus: Satuan UOM masih digunakan oleh X produk SKU!"*. |
| **7.8** | Hapus Data UOM Uji (Delete Success) | Klik Hapus pada baris `CTN_TEST` (yang belum digunakan produk apapun), konfirmasi modal | Konfirmasi: `Ya, Hapus` | Satuan UOM berhasil dihapus, notifikasi sukses muncul, data `CTN_TEST` terhapus dari tabel. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:55 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus CRUD Master UOM, konversi uppercase ID otomatis, sinkronisasi nama, dan proteksi foreign key sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:55 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=uoms`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **7.1** | 🟢 PASS | Tab UOM termuat dengan data satuan dan counter produk | Route `master.uom.store` & view `uoms.blade.php` siap |
| **7.2** | 🟢 PASS | Form menolak submit data kosong | Validasi HTML5 & Laravel berjalan |
| **7.3** | 🟢 PASS | Input `ctn_test` otomatis tersimpan sebagai `CTN_TEST` | Transformasi `strtoupper(trim($id))` bekerja |
| **7.4** | 🟢 PASS | Duplikasi ID UOM berhasil diblokir | Constraint unique `unique:uoms,id` aktif |
| **7.5** | 🟢 PASS | Filter pencarian UOM akurat | Query pencarian UOM berjalan lancar |
| **7.6** | 🟢 PASS | Update nama UOM berhasil dan disinkronkan via DB transaction | Tabel `uoms`, `products`, `product_uom_conversions` tersinkron |
| **7.7** | 🟢 PASS | Proteksi hapus UOM aktif berfungsi mencegah inkonsistensi | Validasi relasi produk aktif |
| **7.8** | 🟢 PASS | Satuan uji `CTN_TEST` berhasil dihapus dan diaudit | Terhapus dari DB & tercatat di `audit_logs` |

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
| **7.1** | 🟢 PASS | | |
| **7.2** | 🟢 PASS | | |
| **7.3** | 🟢 PASS | | |
| **7.4** | 🟢 PASS | | |
| **7.5** | 🟢 PASS | | |
| **7.6** | 🟢 PASS | | |
| **7.7** | 🟢 PASS | | |
| **7.8** | 🟢 PASS | | |
-->
