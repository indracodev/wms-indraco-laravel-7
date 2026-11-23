# Test Scenario: TS-MST-003 - Manajemen Master Data Gudang (Warehouse)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-003
- **File Test**: `4_gudang_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Filter Plant, & Validasi Gudang
- **Tingkat Prioritas**: High (P1)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=warehouses`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun pengelola master data.
  2. Minimal satu Plant aktif sudah terdaftar di sistem (misal: `PLANT01` atau `PLANT-TEST-01`).
  3. Tab *"3. Gudang"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID Gudang: `GDG-TEST-01`
    - Plant Induk: `PLANT01` (atau ID plant yang valid)
    - Nama Gudang: `Gudang Bahan Baku Utama Test`
    - Alamat: `Kompleks Pergudangan Blok A-1, Surabaya`
  - **Update Data**:
    - Nama Baru: `Gudang Bahan Baku & Finished Goods Test`
    - Alamat Baru: `Kompleks Pergudangan Blok A-2, Surabaya`
  - **Filter Test**: Filter dropdown `branch_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **4.1** | Navigasi Tab Gudang | Klik tab *"3. Gudang"* atau akses `/master-data?tab=warehouses` | - | Tab Gudang terbuka, memuat daftar gudang, plant relasi, counter rak terpasang, kolom pencarian, dan filter plant. |
| **4.2** | Validasi Relasi Plant Wajib | Coba simpan form gudang tanpa memilih Plant induk | ID: `GDG-TEST-01`<br>Plant: `(kosong)`<br>Nama: `Gudang Test` | Form menolak submit, field Plant induk wajib diisi (*branch_id is required*). |
| **4.3** | Tambah Gudang Baru (Create Success) | Pilih Plant, isi ID, Nama, dan Alamat lalu submit form | ID: `GDG-TEST-01`<br>Branch ID: `PLANT01`<br>Nama: `Gudang Bahan Baku Utama Test`<br>Alamat: `Kompleks Blok A-1` | Data berhasil disimpan, notifikasi sukses *"Gudang Gudang Bahan Baku Utama Test berhasil ditambahkan"* muncul, tabel menampilkan baris baru, audit log mencatat aksi `CREATE`. |
| **4.4** | Validasi Duplikasi ID Gudang | Submit form kembali dengan ID `GDG-TEST-01` yang sama | ID: `GDG-TEST-01`<br>Branch ID: `PLANT01`<br>Nama: `Gudang Kloning` | Sistem menolak dengan validasi pesan duplikasi ID (*The id has already been taken*). |
| **4.5** | Filter Berdasarkan Plant | Pilih salah satu Plant pada dropdown filter | Filter Plant: `PLANT01` | Tabel hanya menampilkan gudang-gudang yang berada di bawah Plant terpilih. |
| **4.6** | Pencarian Gudang (Search) | Ketikkan ID atau nama gudang pada kolom pencarian | Keyword: `Bahan Baku Utama` | Tabel menampilkan hasil pencarian yang cocok dengan kata kunci. |
| **4.7** | Ubah Data Gudang (Update) | Klik tombol Edit pada baris `GDG-TEST-01`, ubah nama dan alamat lalu simpan | Nama: `Gudang Bahan Baku & Finished Goods Test`<br>Alamat: `Kompleks Blok A-2` | Data terupdate, modal tertutup, notifikasi sukses muncul, nama gudang di tabel berubah sesuai input baru. |
| **4.8** | Proteksi Hapus Gudang Berelasi Rak | Coba hapus gudang yang masih memiliki data Rak aktif terpasang di dalamnya | Klik Hapus pada gudang dengan jumlah Rak > 0 | Non-Superadmin dicegah dengan alert error *"Gagal menghapus: Gudang masih memiliki X Rak terdaftar"*. |
| **4.9** | Hapus Data Gudang Uji (Delete Success) | Klik Hapus pada baris `GDG-TEST-01` (yang tidak memiliki rak terkait), konfirmasi modal | Konfirmasi: `Ya, Hapus` | Gudang terhapus, notifikasi sukses muncul, data `GDG-TEST-01` terhapus dari tabel. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:40 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Verifikasi seluruh siklus CRUD Master Gudang dan perlindungan integritas relasi rak berjalan normal |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:40 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=warehouses`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **4.1** | 🟢 PASS | Tab Gudang aktif dan tabel data termuat | Endpoint `master.warehouse.store` & view `warehouses.blade.php` berfungsi |
| **4.2** | 🟢 PASS | Dropdown Plant tervalidasi required | Field `branch_id` wajib dipilih |
| **4.3** | 🟢 PASS | Gudang `GDG-TEST-01` tersimpan di database | Relasi foreign key ke tabel `branches` valid |
| **4.4** | 🟢 PASS | Sistem mencegah input ID gudang ganda | Constraint `unique:warehouses,id` aktif |
| **4.5** | 🟢 PASS | Filter `branch_filter` memfilter baris gudang dengan tepat | Query `where('branch_id', ...)` berjalan |
| **4.6** | 🟢 PASS | Pencarian nama gudang menampilkan hasil relevan | Filter search gudang akurat |
| **4.7** | 🟢 PASS | Update nama dan alamat gudang berhasil disimpan | Data tersimpan di DB & ter-audit |
| **4.8** | 🟢 PASS | Pencegahan hapus gudang berelasi rak aktif | Integritas hirarki lokasi terjaga |
| **4.9** | 🟢 PASS | Gudang uji `GDG-TEST-01` terhapus dengan sukses | Data terhapus dan ter-audit di tabel `audit_logs` |

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
| **4.1** | 🟢 PASS | | |
| **4.2** | 🟢 PASS | | |
| **4.3** | 🟢 PASS | | |
| **4.4** | 🟢 PASS | | |
| **4.5** | 🟢 PASS | | |
| **4.6** | 🟢 PASS | | |
| **4.7** | 🟢 PASS | | |
| **4.8** | 🟢 PASS | | |
| **4.9** | 🟢 PASS | | |
-->
