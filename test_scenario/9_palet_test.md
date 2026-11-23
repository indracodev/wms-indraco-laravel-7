# Test Scenario: TS-MST-008 - Manajemen Master Data Palet Storage (Pallet)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-008
- **File Test**: `9_palet_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Filter Tipe & BIN, Validasi Kapasitas BIN, & Proteksi Palet Berstok
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=pallets` (atau `http://127.0.0.1:8000/master-data?tab=pallets`)
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun pengelola master data (`permission:master,read` dan `permission:master,create/update/delete`).
  2. Master Tipe Palet (misal: `loscam`, `kayu_125`, `plastik`) sudah terdaftar di sistem.
  3. Master Slot BIN sudah tersedia di sistem.
  4. Tab *"8. Palet"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid (Transit)**:
    - ID Palet: `plt-test-01` (diuji dengan huruf kecil untuk verifikasi auto-uppercase menjadi `PLT-TEST-01`)
    - Tipe Palet: `loscam` (Palet Loscam)
    - Lokasi BIN: `(Transit / Kosong)`
    - Kapasitas Max Box: `72`
    - Status: `1 (Aktif)`
  - **Create Valid (On Rack)**:
    - ID Palet: `PLT-TEST-02`
    - Tipe Palet: `kayu_125`
    - Lokasi BIN: `I B.1.10` (atau BIN valid yang masih memiliki kuota kapasitas)
    - Kapasitas Max Box: `50`
    - Status: `1 (Aktif)`
  - **Update Data**:
    - Tipe Baru: `plastik`
    - Kapasitas Max Baru: `80`
    - Status Baru: `0 (Non-Aktif)`
  - **Filter Test**: Filter `type_filter`, `bin_filter`, dan `status_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **9.1** | Navigasi Tab Master Palet | Klik tab *"8. Palet"* atau akses `/master-data?tab=pallets` | - | Tab Palet terbuka, menampilkan daftar palet terdaftar, badge tipe palet, lokasi penempatan (BIN / TRANSIT), kapasitas muatan box, status keaktifan, kolom pencarian, dan filter tipe/BIN/status. |
| **9.2** | Validasi Field Wajib Palet | Kosongkan field ID dan jangan pilih Tipe Palet lalu submit form | ID: `(kosong)`<br>Tipe: `(kosong)` | Form menolak submit, sistem memunculkan peringatan field wajib diisi. |
| **9.3** | Tambah Palet Baru Status Transit (Create Success) | Masukkan ID dengan huruf kecil `plt-test-01`, pilih tipe `loscam`, kosongkan lokasi BIN, lalu submit | ID: `plt-test-01`<br>Tipe: `loscam`<br>BIN: `(kosong)`<br>Kapasitas: `72` | Data tersimpan, pesan sukses *"Palet PLT-TEST-01 berhasil ditambahkan"* muncul, ID tersimpan sebagai huruf kapital `PLT-TEST-01`, lokasi berstatus `TRANSIT`, audit log mencatat aksi `CREATE`. |
| **9.4** | Validasi Duplikasi ID Palet | Coba simpan kembali form dengan ID `PLT-TEST-01` yang sama | ID: `PLT-TEST-01`<br>Tipe: `loscam` | Sistem menolak dengan validasi pesan duplikasi ID (*The id has already been taken*). |
| **9.5** | Validasi Kapasitas Maksimal BIN Penuh | Coba daftarkan palet baru ke dalam slot BIN yang kapasitas paletnya sudah penuh (`active_pallets_count >= max_pallet`) | ID: `PLT-TEST-FULL`<br>BIN: `(BIN yang sudah penuh)` | Sistem menolak dengan pesan error: *"Gagal: BIN sudah penuh (X Palet terdaftar). Maksimal Y Palet per BIN."*. |
| **9.6** | Filter Berdasarkan Tipe & Status | Pilih Tipe Palet dan Status pada filter dropdown | Tipe: `loscam`<br>Status: `In Transit` | Tabel menyaring dan hanya menampilkan palet tipe loscam yang berada dalam status transit. |
| **9.7** | Pencarian Palet (Search) | Ketikkan ID palet pada kolom pencarian | Keyword: `PLT-TEST-01` | Tabel menampilkan hasil pencarian palet `PLT-TEST-01` secara instan. |
| **9.8** | Lihat Detail Palet (Show Modal) | Klik tombol Detail (ikon mata biru) pada baris `PLT-TEST-01` | Klik tombol Detail | Modal popup detail palet terbuka, menampilkan kode barcode, jenis palet, lokasi BIN, kapasitas box, status keaktifan, dan tanggal registrasi. |
| **9.9** | Ubah Parameter Palet (Update) | Klik tombol Edit pada baris `PLT-TEST-01`, ubah tipe menjadi `plastik`, kapasitas `80`, status Non-Aktif, lalu simpan | Tipe: `plastik`<br>Kapasitas: `80`<br>Status: `Non-Aktif` | Data terupdate, modal tertutup, notifikasi sukses muncul, detail palet di tabel terbarui menjadi Tipe Plastik, Kapasitas 80 Box, dan Badge Non-Aktif. |
| **9.10** | Proteksi Hapus Palet Berisi Stok Box Aktif | Coba hapus palet yang masih memiliki box barang aktif di dalamnya (misal palet `PLT-0001` dengan active boxes > 0) | Klik Hapus pada palet berisi barang | Sistem menolak dengan alert error 422: *"Gagal menghapus: Palet masih terisi X box barang aktif di gudang!"*. |
| **9.11** | Hapus Palet Uji Kosong (Delete Success) | Klik Hapus pada baris palet uji kosong `PLT-TEST-01`, konfirmasi modal | Konfirmasi: `Ya, Hapus` | Palet berhasil dihapus dari sistem, notifikasi sukses muncul, data `PLT-TEST-01` hilang dari tabel data. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 09:10 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus lengkap CRUD Master Palet di tab /master-data?tab=pallets, validasi kapasitas BIN, dan proteksi palet berstok box sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 09:10 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.2 / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=pallets`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **9.1** | 🟢 PASS | Tab 8. Palet terbuka menampilkan tabel master palet storage | Parameter `tab=pallets` di-load via AJAX SPA |
| **9.2** | 🟢 PASS | Form menolak submit saat field wajib kosong | Validasi HTML5 & Laravel backend aktif |
| **9.3** | 🟢 PASS | Input `plt-test-01` otomatis terkapitalisasi menjadi `PLT-TEST-01` | Data tersimpan di tabel `pallets` dengan status `in_transit` |
| **9.4** | 🟢 PASS | Sistem mencegah duplikasi ID Palet | Error unique constraint tertangkap |
| **9.5** | 🟢 PASS | Kapasitas BIN divalidasi tidak melebihi kuota `max_pallet` | Validasi kuota BIN bekerja optimal |
| **9.6** | 🟢 PASS | Filter tipe palet dan status membatasi baris dengan akurat | Query filtering backend berjalan lancar |
| **9.7** | 🟢 PASS | Pencarian ID palet menampilkan hasil akurat | Search filter `search_pallet` berfungsi |
| **9.8** | 🟢 PASS | Modal detail palet terbuka menampilkan metadata lengkap | AlpineJS detail modal bekerja |
| **9.9** | 🟢 PASS | Update tipe, kapasitas, dan status palet berhasil | Perubahan tersimpan di database dan diaudit |
| **9.10** | 🟢 PASS | Palet yang masih terisi box aktif dicegah dari penghapusan | Integritas stok barang gudang terlindungi |
| **9.11** | 🟢 PASS | Palet uji kosong `PLT-TEST-01` berhasil dihapus | Data terhapus bersih dan tercatat di `audit_logs` |

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
| **9.1** | 🟢 PASS | | |
| **9.2** | 🟢 PASS | | |
| **9.3** | 🟢 PASS | | |
| **9.4** | 🟢 PASS | | |
| **9.5** | 🟢 PASS | | |
| **9.6** | 🟢 PASS | | |
| **9.7** | 🟢 PASS | | |
| **9.8** | 🟢 PASS | | |
| **9.9** | 🟢 PASS | | |
| **9.10** | 🟢 PASS | | |
| **9.11** | 🟢 PASS | | |
-->
