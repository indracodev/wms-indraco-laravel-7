# Panduan Operasional Alur Transaksi Inbound (IN) & Outbound (OUT)
## Warehouse Management System (WMS) Indraco

Dokumen ini berisi panduan lengkap tata cara operasional pencatatan **Barang Masuk (Inbound / IN)** dan **Barang Keluar (Outbound / OUT)** berbasis Hirarki Gudang (BIN, Palet, Box, & Satuan Terkecil Produk) pada aplikasi WMS Indraco.

---

## 1. Konsep Utama Hirarki & Satuan Barang (Multi-UOM)

Aplikasi WMS Indraco menggunakan struktur hirarki penyimpanan 4 tingkat untuk menjamin ketepatan lokasi dan persediaan barang:

```
[ BIN / Lokasi Rak ] 
       └── [ PALET / Barcode Tag (PLT-xxxx) ]
                └── [ BOX / Kode Box (BOX-xxxx) ]
                         └── [ PRODUK SKU / Satuan Terkecil (uom_base: Sachet / PCS) ]
```

### Fitur Penunjang Utama:
1. **Multi-UOM Conversion**: Pencatatan transaksi dapat dilakukan dalam berbagai satuan (Karton, Box, Renceng, Pack), yang secara otomatis dihitung dan disimpan dalam **Satuan Terkecil (`uom_base`)** (contoh: *Sachet*, *PCS*).
2. **Granular Picking Outbound**: Pengeluaran barang dapat dilakukan hingga satuan terkecil dari box yang berada di atas palet (misal: mengambil 2 sachet dari box di atas palet tertentu).
3. **Select Search & Barcode Camera Scanner**: Seluruh elemen pilihan (`<select>`) menggunakan fitur pencarian cepat (TomSelect) dan mendukung pemindaian barcode fisik menggunakan kamera HP/Laptop.

---

## 2. Alur Barang Masuk (Inbound / IN Flow)

Aktivitas Inbound terbagi menjadi 2 alur utama pada halaman **[`http://127.0.0.1:8000/transaction/inbound`](http://127.0.0.1:8000/transaction/inbound)** atau langsung dari halaman **[`http://127.0.0.1:8000/pallets`](http://127.0.0.1:8000/pallets)**:

### Alur 1: Penempatan Palet ke BIN (Pallet Putaway)
*Tujuan: Memindahkan/menempatkan palet fisik ke lokasi BIN rak penyimpanan gudang.*

1. Buka menu **Inbound Putaway** (`/transaction/inbound`) dan pilih tab **"1. Penempatan Palet ke BIN"**.
2. **Step 1 - Scan / Input BIN Target**: Scan barcode BIN atau pilih dari dropdown Select Search (misal: `B.10.2`). Sistem akan menampilkan kapasitas sisa palet pada BIN tersebut (maksimal 2 palet per BIN).
3. **Step 2 - Scan / Input Palet Barcode**: Scan barcode fisik palet atau masukkan ID Palet (`PLT-xxxx`).
4. **Pilih Jenis Palet**: Pilih material palet (*Palet Loscam Wood*, *Palet Kayu Standar*, *Palet Plastik Heavy Duty*).
5. Klik **"Lanjutkan & Konfirmasikan"** -> Periksa detail pada modal konfirmasi -> Klik **"Ya, Simpan Putaway"**.

---

### Alur 2: Pendataan Box & Produk ke Palet (Box to Pallet Inbound)
*Tujuan: Mendaftarkan barang/box produksi baru ke atas palet tertentu.*

#### Metode A: Dari Halaman Inbound (`/transaction/inbound`)
1. Pilih tab **"2. Pendataan Box ke Palet"**.
2. **Step 1 - Palet Target**: Scan barcode palet atau pilih dari dropdown Select Search Palet Aktif.
3. **Step 2 - Detail Box & Produk**:
   - **Barcode Box ID**: Ketik ID box, scan barcode box, atau biarkan kosong untuk Auto-Generate (`BOX-xxxx`).
   - **Pilih Produk SKU**: Gunakan Select Search untuk mencari nama produk / SKU (tersedia 114 SKU).
   - **Jumlah Qty Transaksi & Satuan (UOM)**: Input jumlah (misal: `2`) dan pilih satuan transaksi (misal: `Karton`).
   - **Cek Live Calculator**: Sistem menampilkan hasil kalkulasi otomatis setara **Satuan Terkecil** (misal: `2 Karton = 288 Sachet`).
   - **Tanggal Expired, Kode Mesin, & Shift**: Masukkan tanggal kadaluarsa, kode mesin produksi (`MC-01`), dan shift operator.
4. Klik **"Lanjutkan & Konfirmasikan"** -> Periksa pada modal popup -> Klik **"Ya, Simpan Pendataan Box"**.

#### Metode B: Dari Halaman Manajemen Palet (`/pallets`)
1. Buka menu **Manajemen Palet** (`/pallets`).
2. Pada baris palet target, klik tombol hijau **"+ Box"** (`+ Tambah Box / Produk ke Palet`).
3. Form modal interaktif akan terbuka dengan `Pallet ID` terisi otomatis. Isi rincian produk SKU, qty, UOM, expired date, dan kode mesin, lalu klik **"Daftarkan Box Ke Palet"**.

---

## 3. Alur Barang Keluar (Outbound / OUT Flow)

Aktivitas Outbound dilaksanakan pada halaman **[`http://127.0.0.1:8000/transaction/outbound`](http://127.0.0.1:8000/transaction/outbound)** untuk mengeluarkan barang dari palet hingga tingkat satuan terkecil.

### Langkah-Langkah Operasional Outbound Picking:

1. Buka menu **Barang Keluar / Outbound** (`/transaction/outbound`).
2. **Step 1 - Scan / Input Palet Asal**: Scan barcode palet atau pilih dari dropdown Select Search (misal: `PLT-0001`).
3. **Step 2 - Pilih Box Spesifik pada Palet**:
   - Pilih Box target dari dropdown. Sistem menampilkan sisa stok box dalam satuan terkecil (misal: `BOX-0001 - Stok: 144 Sachet [Exp: 2026-12-31]`).
4. **Step 3 - Pilih SKU Produk & Satuan Pengambilan**:
   - Dropdown SKU Produk dan Satuan Pengambilan (UOM) akan terisi otomatis.
   - Operator dapat memilih pengeluaran dalam satuan koli (Box/Karton) atau **satuan terkecil (Sachet/PCS)**.
5. **Step 4 - Input Qty & Validasi Stok Real-Time**:
   - Masukkan jumlah yang diambil (misal: `2` Sachet).
   - **Indikator Live Check**: Sistem menampilkan sisa stok setelah pengeluaran. Jika qty yang diminta melebihi stok box, tombol konfirmasi akan dinonaktifkan secara otomatis.
6. **Step 5 - Konfirmasi Transaksi**:
   - Klik **"Lanjutkan & Konfirmasikan Outbound"**.
   - Modal Konfirmasi Popup akan muncul menampilkan ringkasan pengeluaran -> Klik **"Ya, Proses Outbound"**.
   - Stok fisik Box akan terpotong dan Log Mutasi **`OUT`** tercatat pada histori palet.

---

## 4. Manajemen Palet, Inspection, & Barcode Tracking

Fitur pendukung pengawasan palet tersedia di **[`http://127.0.0.1:8000/pallets`](http://127.0.0.1:8000/pallets)**:

1. **Check Isi & Akumulasi Stok (`Check Isi`)**:
   - Menampilkan akumulasi stok persediaan produk pada palet dalam satuan terkecil (`uom_base`), rincian seluruh Box, tanggal expired, dan lokasi BIN saat ini.
2. **Pendaftaran Palet Fisik (`Daftarkan Palet Baru`)**:
   - Mendaftarkan label barcode palet baru (`PLT-xxxx`) menggunakan pemindaian kamera HP/Laptop, tombol Auto-Generate, atau pengetikan manual.
3. **Pallet Tracking Timeline (`Track`)**:
   - Visualisasi histori mutasi palet secara kronologis, mencakup riwayat **IN** (Inbound putaway & pendataan box) serta **OUT** (Outbound picking), lengkap dengan timestamp & nama operator PIC.
4. **Cetak Tag Label Barcode (`Cetak Label`)**:
   - Mencetak label tag fisik palet (ukuran 10x15cm) dengan Barcode CODE128 & QR Code untuk dipasang pada fisik palet di lokasi gudang.

---

## 5. Ringkasan Matriks Transaksi (Audit Trail)

| Jenis Transaksi | Endpoint URL | Parameter Utama | Dampak Pada Stok | Status Log |
| :--- | :--- | :--- | :--- | :--- |
| **Putaway Palet** | `/transaction/inbound/pallet-to-bin` | `bin_id`, `pallet_id`, `pallet_type_id` | Menghubungkan Palet ke BIN Rak | `In` (Putaway) |
| **Inbound Box** | `/transaction/inbound/box-to-pallet` | `pallet_id`, `box_id`, `product_id`, `qty`, `unit`, `expired_date`, `machine_code` | Menambah Box & Stok Base (`uom_base`) pada Palet | `In` (Box Reg) |
| **Outbound Picking** | `/transaction/outbound` | `pallet_id`, `box_id`, `product_id`, `qty`, `unit` | Memotong Stok Box pada Palet hingga Satuan Terkecil | `Out` (Picking) |
| **Relokasi BIN** | `/pallets/{id}` (PUT) | `bin_id` | Memindahkan Lokasi BIN Palet | `Transfer` |

---
*Dokumen Panduan Operasional WMS Indraco — Terakhir Diperbarui: 19 Agustus 2026*
