# Test Scenario: TS-MST-004 - Manajemen Master Data Rak (Rack)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-004
- **File Test**: `5_rak_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Filter Gudang, & Validasi Rak
- **Tingkat Prioritas**: High (P1)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=racks`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun pengelola master data.
  2. Minimal satu Gudang aktif sudah terdaftar di sistem (misal: `GDG01`).
  3. Tab *"4. Rak"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID Rak: `RAK-TEST-01`
    - Gudang Induk: `GDG01` (atau ID gudang yang valid)
    - Nama Rak: `Rak Zona Kering A-01 Test`
  - **Update Data**:
    - Nama Rak Baru: `Rak Zona Kering A-01 Heavy Duty`
  - **Filter Test**: Filter dropdown `wh_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **5.1** | Navigasi Tab Rak | Klik tab *"4. Rak"* atau akses `/master-data?tab=racks` | - | Tab Rak terbuka, memuat daftar rak, gudang relasi, counter BIN terpasang pada tiap rak, kolom pencarian, dan filter gudang. |
| **5.2** | Validasi Relasi Gudang Wajib | Coba simpan form rak baru tanpa memilih Gudang induk | ID: `RAK-TEST-01`<br>Gudang: `(kosong)`<br>Nama: `Rak A-01` | Form menolak submit karena `warehouse_id` berstatus wajib (*required*). |
| **5.3** | Tambah Rak Baru (Create Success) | Pilih Gudang, isi ID Rak dan Nama Rak, lalu submit form | ID: `RAK-TEST-01`<br>Warehouse ID: `GDG01`<br>Nama: `Rak Zona Kering A-01 Test` | Data tersimpan, notifikasi sukses *"Rak Rak Zona Kering A-01 Test berhasil ditambahkan"* muncul, tabel menampilkan baris baru, audit log mencatat aksi `CREATE`. |
| **5.4** | Validasi Duplikasi ID Rak | Submit form kembali dengan ID `RAK-TEST-01` yang sama | ID: `RAK-TEST-01`<br>Warehouse ID: `GDG01`<br>Nama: `Rak Duplikat` | Sistem menolak dengan validasi pesan duplikasi ID (*The id has already been taken*). |
| **5.5** | Filter Berdasarkan Gudang | Pilih salah satu Gudang pada dropdown filter gudang | Filter Gudang: `GDG01` | Tabel hanya menampilkan rak-rak yang berlokasi di gudang terpilih. |
| **5.6** | Pencarian Rak (Search) | Ketikkan ID atau nama rak pada kolom input pencarian | Keyword: `Zona Kering` | Tabel menampilkan data rak yang relevan dengan keyword pencarian. |
| **5.7** | Ubah Data Rak (Update) | Klik tombol Edit pada baris `RAK-TEST-01`, ubah nama rak lalu simpan | Nama Rak: `Rak Zona Kering A-01 Heavy Duty` | Data terupdate, modal edit tertutup, notifikasi sukses muncul, nama rak di tabel terbarui. |
| **5.8** | Proteksi Hapus Rak Berelasi BIN | Coba hapus rak yang masih memiliki Slot BIN terdaftar di dalamnya (counter BIN > 0) | Klik Hapus pada rak berelasi | Non-Superadmin dicegah dengan alert error *"Gagal menghapus: Rak masih memiliki X Slot BIN terdaftar"*. |
| **5.9** | Hapus Data Rak Uji (Delete Success) | Klik Hapus pada baris `RAK-TEST-01` (yang belum memiliki BIN), konfirmasi modal | Konfirmasi: `Ya, Hapus` | Rak terhapus, notifikasi sukses muncul, data `RAK-TEST-01` hilang dari tabel. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :--- :--- | :--- | :--- |
| 1 | 2026-11-23 08:45 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus CRUD Master Rak, relasi warehouse_id, dan proteksi hapus berelasi BIN teruji sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:45 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=racks`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **5.1** | 🟢 PASS | Tab Rak aktif dan daftar rak termuat sempurna | Route `master.rack.store` & view `racks.blade.php` siap |
| **5.2** | 🟢 PASS | Validasi dropdown gudang wajib berfungsi | Field `warehouse_id` tervalidasi |
| **5.3** | 🟢 PASS | Rak `RAK-TEST-01` berhasil dibuat | Data tersimpan di tabel `racks` |
| **5.4** | 🟢 PASS | Duplikasi ID rak dicegah | Constraint unique database berfungsi |
| **5.5** | 🟢 PASS | Filter `wh_filter` menyaring data rak dengan akurat | Query `where('warehouse_id', ...)` berjalan |
| **5.6** | 🟢 PASS | Pencarian rak akurat | Query pencarian rak berjalan lancar |
| **5.7** | 🟢 PASS | Update nama rak berhasil disimpan | Kolom `rack_name` terupdate dan diaudit |
| **5.8** | 🟢 PASS | Proteksi hapus rak berelasi BIN aktif | Integritas slot penyimpanan terjaga |
| **5.9** | 🟢 PASS | Rak uji `RAK-TEST-01` berhasil dihapus | Data terhapus bersih dari sistem |

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
| **5.1** | 🟢 PASS | | |
| **5.2** | 🟢 PASS | | |
| **5.3** | 🟢 PASS | | |
| **5.4** | 🟢 PASS | | |
| **5.5** | 🟢 PASS | | |
| **5.6** | 🟢 PASS | | |
| **5.7** | 🟢 PASS | | |
| **5.8** | 🟢 PASS | | |
| **5.9** | 🟢 PASS | | |
-->
