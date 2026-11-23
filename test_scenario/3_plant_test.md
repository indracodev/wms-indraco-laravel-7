# Test Scenario: TS-MST-002 - Manajemen Master Data Plant / Cabang (Branch)

## 1. Informasi Umum
- **ID Skenario**: TS-MST-002
- **File Test**: `3_plant_test.md`
- **Modul**: Master Data Management (`MST`)
- **Task Flow**: CRUD (Create, Read, Update, Delete), Filter Relasi Kantor, & Validasi Plant
- **Tingkat Prioritas**: High (P1)
- **Target URL**: `http://localhost/wms-indraco/public/master-data?tab=branches`
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor Gudang (`budi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan hak akses master data.
  2. Minimal satu Kantor Pusat aktif sudah terdaftar di sistem (misal: `KP01`).
  3. Tab *"2. Plant"* aktif pada halaman Master Data.
- **Test Data**:
  - **Create Valid**:
    - ID Plant: `PLANT-TEST-01`
    - Kantor Pusat: `KP01` (atau kantor yang valid di sistem)
    - Nama Plant: `Plant Roasting Rungkut Test`
    - Kota: `Surabaya`
  - **Update Data**:
    - Nama Baru: `Plant Packaging Rungkut Updated`
    - Kota Baru: `Sidoarjo`
  - **Filter Test**: Filter dropdown `office_filter`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **3.1** | Navigasi Tab Plant | Klik tab *"2. Plant"* atau buka `/master-data?tab=branches` | - | Tab Plant aktif, menampilkan daftar plant, nama kantor induk, counter gudang per plant, kolom pencarian, dan filter kantor. |
| **3.2** | Validasi Relasi Kantor Kosong | Coba simpan form plant baru tanpa memilih Kantor Pusat | ID: `PLANT-TEST-01`<br>Kantor: `(kosong)`<br>Nama: `Plant Test` | Sistem/form menolak submit karena `office_id` berstatus wajib (*required*). |
| **3.3** | Tambah Plant Baru (Create Success) | Pilih kantor pusat, isi ID, Nama, dan Kota lalu submit form | ID: `PLANT-TEST-01`<br>Office ID: `KP01`<br>Nama: `Plant Roasting Rungkut Test`<br>Kota: `Surabaya` | Data tersimpan, pesan sukses *"Plant Plant Roasting Rungkut Test berhasil ditambahkan"* muncul, baris baru muncul di tabel, audit log mencatat aksi `CREATE`. |
| **3.4** | Validasi Duplikasi ID Plant | Submit form kembali dengan ID `PLANT-TEST-01` yang sama | ID: `PLANT-TEST-01`<br>Office ID: `KP01`<br>Nama: `Plant Kloning` | Sistem menolak dengan validasi pesan unik ID (*The id has already been taken*). |
| **3.5** | Filter Berdasarkan Kantor Induk | Pilih salah satu kantor pada dropdown filter kantor | Filter Kantor: `KP01` | Tabel hanya menampilkan daftar plant yang berada di bawah naungan kantor terpilih. |
| **3.6** | Pencarian Plant (Search) | Ketik nama kota atau nama plant pada kolom pencarian | Keyword: `Rungkut Test` | Tabel menampilkan hasil pencarian yang sesuai dengan kata kunci. |
| **3.7** | Ubah Data Plant (Update) | Klik tombol Edit pada baris `PLANT-TEST-01`, ubah nama dan kota lalu simpan | Nama: `Plant Packaging Rungkut Updated`<br>Kota: `Sidoarjo` | Data terupdate, modal edit tertutup, notifikasi sukses muncul, data di tabel terbarui menjadi `Plant Packaging Rungkut Updated`. |
| **3.8** | Proteksi Hapus Plant Berelasi | Coba hapus plant yang masih memiliki Gudang aktif di bawahnya (misal plant dengan count Gudang > 0) | Klik Hapus pada plant berelasi | Non-Superadmin dicegah dengan alert error *"Gagal menghapus: Plant masih memiliki X Gudang terdaftar"*. |
| **3.9** | Hapus Data Plant Uji (Delete Success) | Klik Hapus pada baris `PLANT-TEST-01` (yang belum memiliki gudang terkait), konfirmasi modal | Konfirmasi: `Ya, Hapus` | Plant terhapus, notifikasi sukses muncul, data `PLANT-TEST-01` tidak lagi tampil di tabel. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:35 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Siklus CRUD Plant, integrasi foreign key office_id, dan proteksi relasi gudang terverifikasi berhasil |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:35 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/master-data?tab=branches`
- **Penguji / Runner**: QA Team
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **3.1** | 🟢 PASS | Tab Plant aktif dan data tabel termuat | Route `master.branch.store` & view `branches.blade.php` siap |
| **3.2** | 🟢 PASS | Field `office_id` tervalidasi required | Select dropdown tervalidasi |
| **3.3** | 🟢 PASS | Plant `PLANT-TEST-01` tersimpan | Relasi foreign key ke tabel `offices` terbentuk |
| **3.4** | 🟢 PASS | Unique constraint ID plant mencegah duplikasi | Pesan error validasi duplikat tampil |
| **3.5** | 🟢 PASS | Filter `office_filter` berhasil membatasi data | Query `where('office_id', ...)` berjalan |
| **3.6** | 🟢 PASS | Pencarian plant berdasarkan nama/kota akurat | Filter search branch berfungsi |
| **3.7** | 🟢 PASS | Update data nama & kota berhasil | Data tersimpan di tabel `branches` |
| **3.8** | 🟢 PASS | Proteksi hapus plant berelasi gudang aktif | Integritas data gudang terlindungi |
| **3.9** | 🟢 PASS | Hapus plant uji `PLANT-TEST-01` sukses | Data terhapus bersih dan diaudit |

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
| **3.1** | 🟢 PASS | | |
| **3.2** | 🟢 PASS | | |
| **3.3** | 🟢 PASS | | |
| **3.4** | 🟢 PASS | | |
| **3.5** | 🟢 PASS | | |
| **3.6** | 🟢 PASS | | |
| **3.7** | 🟢 PASS | | |
| **3.8** | 🟢 PASS | | |
| **3.9** | 🟢 PASS | | |
-->
