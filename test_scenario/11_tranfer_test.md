# Test Scenario: TS-TRF-001 - Transaksi Transfer Antar Palet & Relokasi BIN

## 1. Informasi Umum
- **ID Skenario**: TS-TRF-001
- **File Test**: `11_tranfer_test.md`
- **Modul**: Transfer & Relocation (`TRF`)
- **Task Flow**: 
  1. **Transfer Produk Antar Palet (Mixed-Pallet / Konsolidasi)**: Pindah sebagian atau seluruh box dari Palet Asal ke Palet Tujuan dengan dukungan multi-produk (campur produk) dalam satu palet penerima.
  2. **Relokasi Palet ke BIN Lain**: Pemindahan 1 unit palet fisik secara utuh beserta seluruh muatan box di dalamnya ke slot rak/BIN tujuan baru.
  3. **UX & Responsive Optimization**: Antarmuka modal pencarian responsif (Mobile & Desktop) dengan virtualisasi data, integrasi Barcode Camera Scanner, skema warna korporat `#004b87` (Teks Putih & Kartu Putih Teks Dark Blue `#004b87`/`#00274d`), serta eliminasi icon dekoratif yang tidak perlu.
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://127.0.0.1:8000/transaction/transfer` (atau `http://localhost/wms-indraco/public/transaction/transfer`)
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor (`budi`)
  - Operator Gudang (`gilang`)
- **Prasyarat (Pre-requisites)**:
  1. Pengguna telah login dengan akun yang memiliki hak akses transaksi Transfer (`permission:transaction,transfer`).
  2. Data Master Palet Storage aktif dan terdaftar di database (palet berisi stok box aktif maupun palet kosong/baru).
  3. Data Master Slot BIN dan Rak aktif dan terdaftar di gudang.
  4. Tersedia minimal satu palet yang memiliki stok box/produk aktif untuk diuji sebagai Palet Asal.
- **Test Data**:
  - **Data Transfer Antar Palet (Tab 1)**:
    - Palet Asal: `PLT-0001` (atau palet aktif yang memiliki box di rak)
    - Box Asal: Box terpilih dengan kuantitas > 0
    - Qty Transfer: `1` Box (atau parsial unit)
    - Palet Tujuan: `PLT-0002` / `PLT-0003` / Palet Baru
  - **Data Relokasi Palet ke BIN (Tab 2)**:
    - Palet Relokasi: `PLT-0001` (atau palet yang berada di rak)
    - Slot BIN Tujuan: Slot BIN kosong/valid (contoh: `Rak I A.1.1` atau `I B.1.10`)
  - **Kamera Scanner**: Kamera ponsel / webcam aktif untuk pengujian barcode scan

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **11.1** | Akses Halaman Transfer Stok | Buka URL `/transaction/transfer` melalui sidebar menu atau navigasi langsung | - | Halaman Transfer Stok terbuka sempurna dengan latar belakang `#004b87`, teks putih, Tab Switcher workflow *"1. Transfer Antar Palet"* & *"2. Relokasi Palet ke BIN Lain"*, serta form Tab 1 aktif secara default. |
| **11.2** | Workflow Tab Switcher | Klik tombol tab *"2. Relokasi Palet ke BIN Lain"*, kemudian klik kembali tab *"1. Transfer Antar Palet"* | Klik tab | Tampilan form berganti secara reaktif tanpa reload halaman (Alpine.js). Tab yang aktif mendapat highlight latar putih dengan teks `#004b87`. |
| **11.3** | Input Barcode Palet Asal (Manual / Scanner Fisik) | Masukkan kode palet pada kolom *"Scan Barcode Palet Asal"* dan tekan Enter atau trigger `@change` | Kode Palet (misal: `PLT-0001`) | Sistem melakukan request AJAX ke backend, memuat detail muatan palet asal, dan menampilkan daftar box yang tersedia di bawahnya. |
| **11.4** | Pemilihan Palet Asal via Modal Pencarian (Mobile & Desktop) | Klik kartu trigger *"Atau Pilih Palet Asal Aktif (Modal Cari)"* | Klik tombol *"Cari Palet"* | Modal pencarian palet terbuka responsif di tengah layar (tanpa bug dropdown overflow). Menampilkan filter *"Semua Palet"*, *"Ada Muatan Box"*, *"Kosong / In Transit"*, serta input pencarian real-time dengan virtualisasi chunking. Memilih palet otomatis menutup modal dan mengisi data form. |
| **11.5** | Pemuatan Otomatis Data Box & Batch Produk Asal | Periksa dropdown *"Pilih Box / Batch Produk yang Dipindahkan"* setelah Palet Asal terpilih | Palet Asal terisi | Dropdown menampilkan daftar box yang ada di palet asal beserta informasi Nama Produk, Stok Tersedia, Satuan UOM, dan Expired Date (ED). |
| **11.6** | Pemilihan Box Produk & Validasi Kuantitas | Pilih salah satu box produk dan masukkan jumlah pada input *"Qty Dipindahkan"* | Qty valid (misal: `1` Box) | Sistem otomatis mengisi kolom *"Satuan"* (readonly), mengunci nilai maksimum input sesuai stok aktual box, dan mencegah input Qty <= 0 atau melebihi stok yang tersedia. |
| **11.7** | Pemilihan Palet Tujuan (Mixed-Pallet) | Scan barcode palet tujuan atau gunakan *"Pilih Palet Tujuan Terdaftar (Modal Cari)"* | Kode Palet Tujuan (misal: `PLT-0002`) | Sistem memvalidasi palet tujuan, mendeteksi apakah palet tersebut berstatus baru, berada di rak tertentu, atau in transit. |
| **11.8** | Verifikasi Indikator & Status Palet Penerima | Periksa kartu informasi status palet tujuan di sisi kanan | - | Menampilkan status palet tujuan, muatan unit saat ini, serta badge informasi: *"Mengizinkan penerimaan produk berbeda (campur produk)"*. |
| **11.9** | Eksekusi Transfer Antar Palet | Klik tombol *"Eksekusi Transfer Antar Palet"* | Data form lengkap & valid | Sistem memproses transaksi via `POST /transaction/transfer/pallet_to_pallet`, mengurangi stok box dari palet asal, memindahkan/membuat data box di palet tujuan, mencatat mutasi stok, dan memberikan alert notifikasi sukses. |
| **11.10** | Akses Form Relokasi Palet ke BIN (Tab 2) | Beralih ke tab *"2. Relokasi Palet ke BIN Lain"* | Klik Tab 2 | Tampil dua kartu putih: *"1. Palet yang Dipindahkan"* dan *"2. Slot BIN Tujuan"*. |
| **11.11** | Pemilihan Palet Relokasi (Tab 2) | Scan barcode palet atau pilih palet yang ingin dipindahkan via modal pencarian | Palet terdaftar di rak | Data palet terinput pada form dan kartu status menampilkan informasi posisi awal palet saat ini. |
| **11.12** | Pemilihan Slot BIN Tujuan via Modal Pencarian (Tab 2) | Klik tombol *"Cari BIN"* pada kartu *"Slot BIN Tujuan"* | Klik trigger modal BIN | Modal pencarian BIN terbuka secara responsif. Menampilkan filter *"Semua BIN"*, *"Kosong / Belum Penuh"*, *"Ada Stok"*, serta kolom pencarian real-time dengan virtualisasi 40 data per page. Memilih salah satu BIN langsung menutup modal dan mengisi kode BIN tujuan. |
| **11.13** | Eksekusi Relokasi BIN Fisik | Klik tombol *"Eksekusi Relokasi BIN"* | Palet & BIN tujuan valid | Sistem memproses transaksi via `POST /transaction/transfer`, memperbarui lokasi fisik palet ke slot BIN baru, memindahkan relasi seluruh box di dalamnya, dan menampilkan notifikasi sukses. |
| **11.14** | Integrasi Barcode Scanner Kamera | Klik tombol *"Kamera"* pada salah satu input scan palet atau BIN | Klik tombol Kamera | Modal kamera scanner muncul, meminta izin akses kamera, mampu memindai barcode fisik, dan otomatis mengisikan hasil pembacaan ke input field target. |
| **11.15** | Uji Responsivitas Tampilan Mobile | Buka halaman menggunakan resolusi perangkat mobile (lebar layar <= 480px / 375px) | Viewport Mobile | Tampilan tidak mengalami horizontal scroll / overflow. Dropdown TomSelect yang sebelumnya overflow kini digantikan oleh modal pencarian responsif yang pas dengan layar ponsel. Tombol kamera dan aksi tetap ergonomis digunakan. |
| **11.16** | Verifikasi Desain Visual, Kontras Warna & Icon Bersih | Periksa seluruh elemen UI pada layar | Desktop & Mobile | Background container `#004b87` memiliki teks berwarna putih bersih (`#ffffff`), kartu berlatar putih memiliki teks dark blue (`#004b87` / `#00274d`). Icon dekoratif yang tidak perlu telah dibersihkan, hanya menyisakan icon fungsional (Kamera, Kaca Pembesar, Tombol Close, Paginasi). |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | 2026-11-24 14:15 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team / Developer | Pengujian menyeluruh alur Transfer Antar Palet (Mixed-Pallet), Relokasi Palet ke BIN, modal pencarian responsif mobile/desktop, kontras warna (#004b87), dan kebersihan icon berhasil dengan sempurna. |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-24 14:15 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.2.29 / MySQL 8.0 / Laragon / Windows)
- **Base URL**: `http://127.0.0.1:8000/transaction/transfer`
- **Penguji / Runner**: QA Team / Developer
- **Browser / Tool**: Google Chrome Desktop (1920x1080) & Mobile Emulation (Android / iOS 375x812)
- **Status Akhir**: `🟢 PASSED`

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **11.1** | 🟢 PASS | Halaman `/transaction/transfer` terbuka lancar dengan tema korporat `#004b87` dan teks putih | Latar belakang kontras tinggi dengan typography jelas |
| **11.2** | 🟢 PASS | Tab Switcher berganti mulus antara mode Transfer Antar Palet dan Relokasi BIN | Alpine.js reactive state `activeMode` berjalan responsif tanpa flicker |
| **11.3** | 🟢 PASS | Input barcode palet asal berhasil memanggil API dan me-load data box | Data box dan batch produk langsung termuat di dropdown |
| **11.4** | 🟢 PASS | Modal pencarian palet terbuka dengan cepat dan memfilter data real-time | Menggantikan select dropdown biasa, bebas dari masalah overflow di mobile |
| **11.5** | 🟢 PASS | Dropdown box asal menampilkan rincian produk, stok, UOM, dan expired date secara informatif | Informasi detail box memudahkan operator memilih batch yang tepat |
| **11.6** | 🟢 PASS | Input Qty tervalidasi dengan baik (min 0.01 dan max sesuai stok box) | Satuan UOM otomatis terisi dan terkunci (readonly) |
| **11.7** | 🟢 PASS | Palet tujuan terdeteksi dengan benar (palet baru maupun palet aktif) | Modal pencarian palet tujuan bekerja optimal |
| **11.8** | 🟢 PASS | Indikator Mixed Pallet tampil jelas mengizinkan konsolidasi produk berbeda | Menampilkan total muatan saat ini pada palet penerima |
| **11.9** | 🟢 PASS | Eksekusi transfer antar palet berhasil memindahkan box dan mencatat mutasi stok | Form submit ke route `transaction.transfer.pallet_to_pallet` sukses |
| **11.10** | 🟢 PASS | Tab 2 Relokasi Palet ke BIN aktif dengan form bersih dan terpisah | Kartu input Palet dan BIN tersusun rapi dalam 2 kolom grid |
| **11.11** | 🟢 PASS | Palet yang akan direlokasi dapat dipilih via scan maupun modal pencarian | Status dan lokasi asal palet langsung teridentifikasi |
| **11.12** | 🟢 PASS | Modal pencarian BIN menyaring slot gudang dengan filter status dan virtualisasi 40 data | Kinerja pencarian sangat cepat dan tidak membebani memori browser |
| **11.13** | 🟢 PASS | Eksekusi relokasi berhasil memindahkan lokasi fisik palet ke slot BIN baru | Form submit ke route `transaction.transfer` berhasil memperbarui data di database |
| **11.14** | 🟢 PASS | Tombol Kamera scanner aktif dan memicu modal pemindai barcode | Integrasi QuaggaJS/HTML5 barcode scanner berfungsi normal |
| **11.15** | 🟢 PASS | Pengujian pada viewport mobile (375px) tidak ada elemen yang terpotong atau overflow | Modal pencarian mengisi layar dengan proporsional dan ramah sentuhan |
| **11.16** | 🟢 PASS | Seluruh icon dekoratif telah bersih, hanya icon fungsional yang tampil; teks pada kartu putih berwarna dark blue `#004b87`/`#00274d` | Estetika visual minimalis, profesional, dan sangat mudah dibaca |

---

<!-- TEMPLATE UNTUK RUN BERIKUTNYA (COPY & APPEND DI BAWAH INI):
### Run #[N] - YYYY-MM-DD HH:mm WIB - [STATUS]
- **Environment**: Localhost / Staging / Production
- **Base URL**: http://127.0.0.1:8000/transaction/transfer
- **Penguji / Runner**: 
- **Browser / Tool**: 
- **Status Akhir**: 🟢 PASSED / 🔴 FAILED / 🟡 BLOCKED
- **Catatan Umum**: 

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **11.1** | 🟢 PASS | ... | - |
| **11.2** | 🟢 PASS | ... | - |
| **11.3** | 🟢 PASS | ... | - |
| **11.4** | 🟢 PASS | ... | - |
| **11.5** | 🟢 PASS | ... | - |
| **11.6** | 🟢 PASS | ... | - |
| **11.7** | 🟢 PASS | ... | - |
| **11.8** | 🟢 PASS | ... | - |
| **11.9** | 🟢 PASS | ... | - |
| **11.10** | 🟢 PASS | ... | - |
| **11.11** | 🟢 PASS | ... | - |
| **11.12** | 🟢 PASS | ... | - |
| **11.13** | 🟢 PASS | ... | - |
| **11.14** | 🟢 PASS | ... | - |
| **11.15** | 🟢 PASS | ... | - |
| **11.16** | 🟢 PASS | ... | - |
-->
