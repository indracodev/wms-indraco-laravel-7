# Test Scenario: TS-MST-005 - Manajemen Master Data Slot Lokasi (BIN)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-005
- **File Test**: `6_bin_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Filter Rak, & Validasi Slot BIN
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=bins`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun pengelola master data.
  2. Minimal satu Rak aktif sudah terdaftar di sistem (misal: `RAK01`).
  3. Tab *"5. BIN"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID BIN: `BIN-TEST-01-01`
    - Rak Induk: `RAK01` (atau ID rak yang valid)
    - Nomor Kolom (`column_no`): `1`
    - Nomor Level/Tingkat (`level_no`): `1`
    - Kapasitas Max Palet (`max_pallet`): `2` (rentang 1 s.d. 5)
  - **Update Data**:
    - Kolom Baru: `2`
    - Level Baru: `3`
    - Kapasitas Max Baru: `3`
  - **Filter Test**: Filter dropdown `rack_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **6.1** | Navigasi Tab BIN | Klik tab *"5. BIN"* atau akses `/master-data?tab=bins` | - | Tab BIN terbuka, memuat daftar BIN, relasi rak & gudang, kolom/level, kapasitas palet, status okupansi (terisi/kosong), dan filter rak. |
| **6.2** | Validasi Kapasitas Max Palet | Masukkan angka di luar rentang valid pada field kapasitas (misal angka 0 atau 6) | Kolom: `1`<br>Level: `1`<br>Max Palet: `6` | Sistem/form menolak submit karena `max_pallet` memiliki batasan validasi `min:1|max:5`. |
| **6.3** | Tambah Slot BIN Baru (Create Success) | Isi data BIN dengan nilai valid lalu submit form | ID: `BIN-TEST-01-01`<br>Rack ID: `RAK01`<br>Kolom: `1`<br>Level: `1`<br>Max Palet: `2` | Data tersimpan, notifikasi sukses *"BIN BIN-TEST-01-01 berhasil ditambahkan"* muncul, tabel menampilkan baris baru, audit log mencatat aksi `CREATE`. |
| **6.4** | Validasi Duplikasi ID BIN | Submit form kembali dengan ID `BIN-TEST-01-01` yang sama | ID: `BIN-TEST-01-01`<br>Rack ID: `RAK01` | Sistem menolak dengan validasi pesan duplikasi ID (*The id has already been taken*). |
| **6.5** | Filter Berdasarkan Rak | Pilih salah satu Rak pada dropdown filter rak | Filter Rak: `RAK01` | Tabel hanya menampilkan slot-slot BIN yang berada pada rak terpilih. |
| **6.6** | Pencarian Slot BIN (Search) | Ketik ID BIN pada kolom input pencarian | Keyword: `BIN-TEST-01` | Tabel menampilkan hasil pencarian BIN yang cocok secara instan. |
| **6.7** | Ubah Parameter BIN (Update) | Klik tombol Edit pada baris `BIN-TEST-01-01`, ubah kolom, level, dan kapasitas lalu simpan | Kolom: `2`<br>Level: `3`<br>Max Palet: `3` | Data terupdate, modal edit tertutup, notifikasi sukses muncul, detail BIN di tabel terbarui menjadi Kolom 2, Level 3, Max 3 Palet. |
| **6.8** | Proteksi Hapus BIN Terisi Palet | Coba hapus BIN yang statusnya sedang ditempati palet aktif (active pallets count > 0) | Klik Hapus pada BIN berstatus terisi | Sistem menolak dengan error 422: *"Gagal menghapus: BIN masih ditempati oleh X Palet aktif di gudang!"*. |
| **6.9** | Hapus Data BIN Uji (Delete Success) | Klik Hapus pada baris `BIN-TEST-01-01` (yang kosong tanpa palet aktif), konfirmasi modal | Konfirmasi: `Ya, Hapus` | Slot BIN berhasil dihapus, notifikasi sukses muncul, data `BIN-TEST-01-01` terhapus dari tabel. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :--- :--- | :--- | :--- |
| 1 | 2026-11-23 08:50 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus CRUD Slot BIN, validasi kapasitas palet 1-5, dan proteksi hapus slot terisi palet terverifikasi sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:50 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=bins`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **6.1** | 🟢 PASS | Tab BIN aktif dan data termuat lengkap dengan visual okupansi | Route `master.bin.store` & view `bins.blade.php` siap |
| **6.2** | 🟢 PASS | Validasi max_pallet (1-5) berfungsi optimal | Constraint validasi `min:1|max:5` bekerja |
| **6.3** | 🟢 PASS | BIN `BIN-TEST-01-01` tersimpan di database | Data tersimpan di tabel `bins` |
| **6.4** | 🟢 PASS | Duplikasi ID BIN berhasil dicegah | Error unik ID muncul |
| **6.5** | 🟢 PASS | Filter `rack_filter` menyaring BIN dengan akurat | Query `where('rack_id', ...)` berjalan |
| **6.6** | 🟢 PASS | Pencarian BIN akurat dan responsif | Filter pencarian berjalan lancar |
| **6.7** | 🟢 PASS | Update parameter kolom, level, dan kapasitas palet sukses | Nilai terbarui di tabel `bins` |
| **6.8** | 🟢 PASS | Proteksi hapus BIN yang sedang ditempati palet aktif | Pencegahan data loss & orphan stock bekerja |
| **6.9** | 🟢 PASS | BIN uji `BIN-TEST-01-01` terhapus bersih | Data terhapus dan ter-audit di `audit_logs` |

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
| **6.1** | 🟢 PASS | | |
| **6.2** | 🟢 PASS | | |
| **6.3** | 🟢 PASS | | |
| **6.4** | 🟢 PASS | | |
| **6.5** | 🟢 PASS | | |
| **6.6** | 🟢 PASS | | |
| **6.7** | 🟢 PASS | | |
| **6.8** | 🟢 PASS | | |
| **6.9** | 🟢 PASS | | |
-->
