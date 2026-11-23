# Test Scenario: TS-INB-001 - Transaksi Penerimaan Barang Masuk (Inbound V2 & Putaway)

## 1. Informasi Umum
- **ID Skenario**: TS-INB-001
- **File Test**: `10_inbound_test.md`
- **Modul**: Inbound Transaction (`INB`)
- **Task Flow**: Wizard 4 Tahap Penerimaan Barang (Palet &rarr; Produk &rarr; Detail &rarr; Simpan Rak), Mode Mobile & Desktop, Select Search & Modal Cache, Multi-UOM, Multi-ED, dan Putaway Palet Transit ke Rak
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://127.0.0.1:8000/transaction/inbound` (atau `http://localhost/wms-indraco/public/transaction/inbound`)
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Operator Gudang / Receiver (`budi` / `andi`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun yang memiliki hak akses ke transaksi Inbound (`permission:transaction,inbound`).
  2. Master Data Palet Storage (Transit maupun On-Rack) telah tersedia di database (11.000+ palet).
  3. Master Produk SKU beserta varian konversi satuan (UOM) sudah terdaftar.
  4. Master Slot Rak & BIN lokasi gudang sudah aktif dan tersedia.
- **Test Data**:
  - **Palet Uji**:
    - Palet Kosong (Baru/Transit): `PLS00001` / `PLT-0001`
    - Palet Sudah Berisi Produk (Terkunci Ketentuan 1): Palet yang sudah memiliki box aktif
  - **Produk Uji**:
    - SKU: `FGD00001` (BaliCafé Arabica Gold 10 Capsules x 6g Box) / `FGD00002` (BaliCafé Arabica Gold Drip 5 Sachets x 10g Box)
    - Satuan UOM Transaksi: `Box` (x1) dan `Karton` (x20 atau x24)
  - **Kuantitas & ED**:
    - Qty Masuk: `2` Karton (Setara `40` Box)
    - Expired Date: `2027-11-23`
    - Catatan / No. PO: `PO-INB-2026/11-001`
  - **Lokasi Rak / BIN**:
    - BIN Tujuan: `I B.1.1` (atau BIN kosong yang valid)

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **10.1** | Akses Halaman Inbound V2 | Buka URL `/transaction/inbound` melalui menu sidebar atau direct link | - | Halaman Inbound V2 terbuka dengan stepper 4 tahap (Palet, Produk, Detail, Simpan Rak), tombol workflow switcher *"Alur Penerimaan Barang"* dan *"Putaway Palet Transit ke Rak"*, serta form Tahap 1 aktif. |
| **10.2** | Pemilihan Palet Desktop (Select Search) | Pada layar desktop, cari dan pilih palet terdaftar melalui dropdown select search | Keyword: `PLS00001` / `K125` | Dropdown menyaring secara instan dari cache database master palet, menampilkan kode palet dan status lokasi penempatannya. |
| **10.3** | Pemilihan Palet Mobile (Modal Search & Cache JS) | Pada tampilan mobile (resolusi ponsel), klik tombol *"Cari"* untuk membuka modal pencarian palet | Keyword: `PLS` / filter badge | Modal terbuka secara responsif, input pencarian real-time memfilter daftar palet tanpa reload (menggunakan cache JavaScript), memilih palet akan menutup modal dan mengisi form. |
| **10.4** | Validasi Palet Sudah Berisi Produk (Ketentuan 1) | Pilih palet yang telah memiliki muatan produk sebelumnya | Palet dengan muatan aktif | Muncul banner peringatan kuning dengan teks hitam tebal: *"Ketentuan 1: Palet Sudah Berisi Produk"* beserta SKU dan nama produk terkunci. Sistem menampilkan riwayat box multi-ED yang sudah ada di palet tersebut. |
| **10.5** | Navigasi Maju ke Tahap 2 | Klik tombol *"Next"* pada Tahap 1 | Form Tahap 1 valid | Sistem berpindah ke Tahap 2 (Produk), stepper menunjukkan progress 50%, banner informasi palet terpilih `#004b87` muncul dengan teks putih bersih. |
| **10.6** | Pindai / Pilih Produk SKU (Tahap 2) | Pilih produk SKU melalui scan barcode, simulator, atau dropdown pencarian | SKU: `FGD00001` | Rincian produk terpilih muncul pada kartu `#004b87` dengan teks putih bersih, menampilkan daftar varian konversi satuan (Pills UOM) dan satuan dasar terkecil (*Base UOM*). |
| **10.7** | Proteksi Produk Berbeda pada Palet Terkunci | Coba pilih SKU produk yang berbeda pada palet yang sudah terkunci Ketentuan 1 | SKU berbeda | Sistem memvalidasi dan mencegah pemilihan produk yang berbeda, memastikan 1 palet hanya berisi 1 SKU produk homogen. |
| **10.8** | Navigasi Mundur (Tombol Back) | Klik tombol *"Back"* pada Tahap 2 | Klik tombol Back | Sistem kembali ke Tahap 1 dengan data palet yang tetap terjaga (tidak hilang/reset). |
| **10.9** | Navigasi Maju ke Tahap 3 (Input Qty & ED) | Klik tombol *"Next"* dari Tahap 2 | Produk terpilih valid | Sistem berpindah ke Tahap 3, stepper menunjukkan progress 75%, menampilkan form input Qty, Satuan UOM, Expired Date, dan Catatan. |
| **10.10** | Konversi Satuan Dinamis & Kalkulasi Muatan | Masukkan Qty dan pilih satuan konversi yang lebih besar (misal: Karton) | Qty: `2`, Satuan: `Karton` | Sistem secara otomatis mengkalkulasi setara satuan dasar (*Base Qty*), mengupdate kalkulasi total muatan palet pada kartu ringkasan `#004b87` secara live, dan memvalidasi kapasitas maksimal palet. |
| **10.11** | Simpan Langsung Status In Transit (Opsi Langkah 3) | Klik tombol *"Simpan (Status: In Transit)"* pada Tahap 3 tanpa memilih rak fisik | Konfirmasi simpan modal | Dialog konfirmasi muncul, setelah dikonfirmasi data transaksi tersimpan resmi dengan status `in_transit` (berada di dock/staging), palet siap di-putaway kapan saja. |
| **10.12** | Navigasi ke Tahap 4 (Penempatan Rak Langsung) | Dari Tahap 3, klik tombol *"Next"* | Data Tahap 3 lengkap | Sistem berpindah ke Tahap 4 (Simpan Rak), stepper mencapai 100%, ringkasan review transaksi `#004b87` tampil lengkap dengan teks putih bersih. |
| **10.13** | Input Lokasi Rak & Simpan Penempatan | Pilih lokasi BIN rak tujuan atau scan barcode BIN, lalu klik *"Simpan & Tempatkan ke Rak"* | BIN: `I B.1.1` | Sistem memvalidasi kapasitas rak/BIN, menyimpan transaksi, mengupdate status palet menjadi `on_rack`, menambah stok box di BIN tersebut, dan mencatat riwayat audit log. |
| **10.14** | Workflow Putaway Palet Transit ke Rak | Beralih ke tab switcher *"Putaway Palet Transit ke Rak"* | Klik tab Putaway | Tampil tabel daftar palet yang berstatus `in_transit`. Operator dapat memilih palet transit dan menempatkannya ke rak fisik melalui form pemindahan lokasi BIN. |
| **10.15** | Verifikasi Konsistensi UI & Kontras Teks | Periksa tampilan visual pada mode desktop dan mobile | Light & Dark Mode | Seluruh teks pada banner/kartu `#004b87` dan tombol aksi indigo berwarna putih (`#ffffff`), kartu peringatan kuning bertuliskan hitam pekat (`#000000`), teks peringatan transit berwarna merah, tombol navigasi konsisten bertuliskan *"Back"* dan *"Next"*. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 13:50 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team / Developer | Pengujian menyeluruh alur Inbound V2 4 tahap (Palet, Produk, Detail, Simpan Rak), mode mobile modal cache, tombol Back/Next, dan kontras warna selesai dengan sukses. |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 13:50 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.2.29 / MySQL 8.0 / Laragon)
- **Base URL**: `http://127.0.0.1:8000/transaction/inbound`
- **Penguji / Runner**: QA Team / Developer
- **Browser / Tool**: Google Chrome (Desktop 1920x1080 & Mobile Emulation iPhone / Android 375x812)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **10.1** | 🟢 PASS | Halaman `/transaction/inbound` terbuka lancar dengan stepper 4 tahap yang responsif | Stepper header menampilkan tahapan Palet, Produk, Detail, Simpan Rak dengan teks putih bersih |
| **10.2** | 🟢 PASS | Select search desktop menyaring palet secara instan | Komponen TomSelect bekerja optimal dengan 11.000+ data palet |
| **10.3** | 🟢 PASS | Tombol Cari mobile membuka modal pencarian palet dengan cache JS | Pengambilan data cepat tanpa lag, pencarian real-time akurat |
| **10.4** | 🟢 PASS | Palet berstok menampilkan kotak peringatan kuning dengan teks hitam tebal | Ketentuan 1: 1 Palet 1 Produk terkunci sempurna, multi-ED list tampil |
| **10.5** | 🟢 PASS | Tombol *"Next"* berhasil memajukan stepper ke Langkah 2 | Progress bar bertambah menjadi 50% |
| **10.6** | 🟢 PASS | Pemilihan SKU memunculkan kartu `#004b87` dengan teks putih dan varian konversi satuan | Pills UOM interaktif dan satuan terkecil (Base) tampil jelas |
| **10.7** | 🟢 PASS | Validasi homogenitas produk menolak pemilihan SKU berbeda pada palet aktif | Integritas stok palet terjaga |
| **10.8** | 🟢 PASS | Tombol *"Back"* mengembalikan tampilan ke Langkah 1 dengan data form tetap utuh | Navigasi mundur berjalan mulus tanpa reload halaman |
| **10.9** | 🟢 PASS | Navigasi ke Langkah 3 berhasil dengan progress 75% | Form Qty, UOM, ED, dan catatan fisik siap diisi |
| **10.10** | 🟢 PASS | Pemilihan satuan Karton otomatis mengalikan Base Qty secara akurat | Live conversion card dan ringkasan muatan palet terupdate seketika |
| **10.11** | 🟢 PASS | Tombol *"Simpan (Status: In Transit)"* berhasil menyimpan transaksi tanpa penempatan rak | Palet tersimpan berstatus `in_transit` untuk diproses kemudian |
| **10.12** | 🟢 PASS | Tombol *"Next"* dari Langkah 3 membawa ke Langkah 4 (Simpan Rak) | Progress bar mencapai 100%, review transaksi lengkap |
| **10.13** | 🟢 PASS | Penempatan rak berhasil menyimpan transaksi dan mengupdate status palet menjadi `on_rack` | Relasi bin_id dan stock balance tercatat rapi |
| **10.14** | 🟢 PASS | Tab *"Putaway Palet Transit ke Rak"* dapat menampilkan palet in-transit untuk dialokasikan ke BIN | Workflow pemindahan transit ke rak fisik berfungsi penuh |
| **10.15** | 🟢 PASS | Kontras visual sempurna: teks putih pada `#004b87`, teks hitam pada kartu kuning, teks merah di atas tombol Back | Seluruh elemen UI memenuhi standar aksesibilitas dan kemudahan penggunaan |

---

<!-- TEMPLATE UNTUK RUN BERIKUTNYA (COPY & APPEND DI BAWAH INI):
### Run #[N] - YYYY-MM-DD HH:mm WIB - [STATUS]
- **Environment**: Localhost / Staging
- **Base URL**: http://127.0.0.1:8000/transaction/inbound
- **Penguji / Runner**: 
- **Browser / Tool**: 
- **Status Akhir**: 🟢 PASSED / 🔴 FAILED / 🟡 BLOCKED
- **Catatan Umum**: 

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **10.1** | 🟢 PASS | ... | - |
| **10.2** | 🟢 PASS | ... | - |
| **10.3** | 🟢 PASS | ... | - |
| **10.4** | 🟢 PASS | ... | - |
| **10.5** | 🟢 PASS | ... | - |
| **10.6** | 🟢 PASS | ... | - |
| **10.7** | 🟢 PASS | ... | - |
| **10.8** | 🟢 PASS | ... | - |
| **10.9** | 🟢 PASS | ... | - |
| **10.10** | 🟢 PASS | ... | - |
| **10.11** | 🟢 PASS | ... | - |
| **10.12** | 🟢 PASS | ... | - |
| **10.13** | 🟢 PASS | ... | - |
| **10.14** | 🟢 PASS | ... | - |
| **10.15** | 🟢 PASS | ... | - |
-->
