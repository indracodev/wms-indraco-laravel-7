# Test Scenario: TS-MST-001 - Manajemen Master Data Kantor Pusat (Office)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-001
- **File Test**: `2_kantor_pusat_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete) & Validasi Relasi Kantor Pusat
- **Tingkat Prioritas**: High (P1)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=offices`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`) - Akses penuh CRUD
  - Supervisor / Operator (untuk verifikasi otorisasi)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun yang memiliki hak akses `permission:master,read` dan `permission:master,create/update/delete`.
  2. Tab "1. Kantor Pusat" aktif di halaman Manajemen Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID Kantor: `KP-TEST-01`
    - Nama Kantor: `Kantor Pusat Surabaya Test`
    - Alamat: `Jl. Raya Darmo No. 100, Surabaya`
  - **Update Data**:
    - Nama Baru: `Kantor Pusat Surabaya Updated`
    - Alamat Baru: `Jl. Mayjen Sungkono No. 50, Surabaya`
  - **Duplikasi ID**: `KP-TEST-01`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **2.1** | Navigasi Tab Kantor Pusat | Klik tab *"1. Kantor Pusat"* atau akses `/master-data?tab=offices` | - | Tab Kantor Pusat aktif, menampilkan tabel daftar kantor, counter total kantor, form tambah kantor di panel samping/modal, dan kolom pencarian. |
| **2.2** | Validasi Input Wajib (Create) | Klik tombol Simpan tanpa mengisi field ID dan Nama Kantor | ID: `(kosong)`<br>Nama: `(kosong)` | Form menolak submit, browser/sistem memunculkan pesan validasi bahwa ID dan Nama wajib diisi. |
| **2.3** | Tambah Kantor Baru (Create Success) | Isi form kantor dengan data valid lalu klik tombol **"Simpan Kantor"** | ID: `KP-TEST-01`<br>Nama: `Kantor Pusat Surabaya Test`<br>Alamat: `Jl. Raya Darmo No. 100` | Data berhasil disimpan, notifikasi sukses *"Kantor Kantor Pusat Surabaya Test berhasil ditambahkan"* muncul, tabel me-refresh dan menampilkan baris baru, audit log mencatat aksi `CREATE`. |
| **2.4** | Validasi Duplikasi ID Kantor | Coba tambahkan kantor baru dengan ID yang sudah ada | ID: `KP-TEST-01`<br>Nama: `Kantor Duplikat` | Sistem menolak dengan error validasi bahwa ID sudah digunakan (*The id has already been taken*). |
| **2.5** | Pencarian Kantor (Read / Search) | Ketik keyword pada input pencarian kantor lalu submit | Keyword: `Surabaya Test` | Tabel menyaring dan hanya menampilkan kantor yang ID, Nama, atau Alamatnya cocok dengan keyword pencarian. |
| **2.6** | Reset Pencarian Kantor | Klik tombol reset pencarian (kuning) di samping bar input | Klik tombol Reset | Input pencarian kosong kembali dan tabel menampilkan seluruh daftar data kantor. |
| **2.7** | Ubah Data Kantor (Update) | Klik tombol Edit (ikon pensil) pada baris `KP-TEST-01`, ubah nama dan alamat, lalu simpan | Nama: `Kantor Pusat Surabaya Updated`<br>Alamat: `Jl. Mayjen Sungkono No. 50` | Data berhasil diperbarui, modal edit tertutup, notifikasi sukses muncul, nama kantor di tabel berubah menjadi `Kantor Pusat Surabaya Updated`. |
| **2.8** | Proteksi Hapus Kantor Berelasi | Coba hapus kantor yang masih memiliki data Plant/Cabang aktif di bawahnya (misal: `KP01`) | Klik Hapus pada kantor berelasi | Jika bukan Super Admin, sistem menolak penghapusan dengan alert error *"Gagal menghapus: Kantor masih memiliki X Plant terdaftar"*. |
| **2.9** | Hapus Data Kantor Uji (Delete Success) | Klik tombol Hapus (ikon tempat sampah) pada baris kantor uji `KP-TEST-01` yang tidak memiliki relasi, konfirmasi dialog | Konfirmasi: `Ya, Hapus` | Kantor berhasil dihapus, notifikasi *"Kantor berhasil dihapus"* muncul, baris `KP-TEST-01` hilang dari tabel data. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:30 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Verifikasi seluruh siklus CRUD Master Kantor Pusat dan proteksi integritas relasi berjalan normal |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:30 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=offices`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **2.1** | 🟢 PASS | Tab Kantor Pusat aktif, daftar kantor termuat | AJAX loadTab `offices` respons 200 OK |
| **2.2** | 🟢 PASS | Validasi required field berfungsi | Form HTML5 dan backend validation aktif |
| **2.3** | 🟢 PASS | Kantor baru `KP-TEST-01` tersimpan di database | Tersimpan di tabel `offices` |
| **2.4** | 🟢 PASS | Duplikasi ID dicegah oleh constraint unique | Error validasi `unique:offices,id` muncul |
| **2.5** | 🟢 PASS | Filter pencarian `search_office` akurat | Query `WHERE id LIKE ... OR name LIKE ...` |
| **2.6** | 🟢 PASS | Reset pencarian mengembalikan data semula | URL query string dibersihkan |
| **2.7** | 🟢 PASS | Update nama dan alamat sukses | Data terupdate dan ter-audit di tabel `audit_logs` |
| **2.8** | 🟢 PASS | Kantor berelasi dilindungi dari penghapusan sembarangan | Aturan integritas relasi berjalan |
| **2.9** | 🟢 PASS | Hapus kantor uji berhasil tanpa kendala | Data `KP-TEST-01` terhapus bersih |

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
| **2.1** | 🟢 PASS | | |
| **2.2** | 🟢 PASS | | |
| **2.3** | 🟢 PASS | | |
| **2.4** | 🟢 PASS | | |
| **2.5** | 🟢 PASS | | |
| **2.6** | 🟢 PASS | | |
| **2.7** | 🟢 PASS | | |
| **2.8** | 🟢 PASS | | |
| **2.9** | 🟢 PASS | | |
-->
