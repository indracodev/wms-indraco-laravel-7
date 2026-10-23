# Modul Eksekusi Transaksi Inbound (Operator Gudang)

## 1. Deskripsi Fitur
Modul Eksekusi Inbound merupakan panduan kerja teknis bagi Operator Gudang saat menerima barang dari bagian produksi atau pemasok luar. Operator memindai stiker barcode palet, memindai SKU produk, memasukkan jumlah kuantitas dan tanggal kedaluwarsa, serta meletakkan palet pada slot rak (*Put-away*) atau menyimpannya sementara di area transit lantai (*Staging Transit*).

---

## 2. Hak Akses & Role Permission
- **Role:** `operator` (Operator Gudang)
- **Middleware:** `auth`, `permission:inbound,read`, `permission:inbound,create`
- **Fokus Utama:** Kecepatan dan ketelitian pemindaian barcode serta pencegahan kesalahan penempatan barang.

---

## 3. Komponen Teknis & Ketentuan Kerja

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `inboundForm()`, `inbound()`, `inboundPalletToBin()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `processInboundV2()`)
- Route:
  - Form: `GET /transaction/inbound` (`name('transaction.inbound.form')`)
  - Simpan Inbound: `POST /transaction/inbound` (`name('transaction.inbound')`)
  - Putaway ke Rak: `POST /transaction/inbound/pallet-to-bin` (`name('transaction.inbound.pallet_to_bin')`)

### Aturan Wajib Diingat Operator:
1. **Satu Palet Satu Macam Produk (Ketentuan 1):** Saat menerima barang baru ke palet kosong, palet tersebut HANYA boleh diisi satu macam produk. Jangan mencampur produk berbeda pada form penerimaan pertama.
2. **Kapasitas Palet (Ketentuan 5):** Pastikan muatan palet tidak melebihi kapasitas standar yang dipilih (18, 72, atau 84 unit).
3. **Multi-Expired Date (Ketentuan 6):** Sistem mendukung beberapa tanggal kedaluwarsa berbeda pada palet yang sama. Pastikan operator teliti menginput tanggal yang tertera pada kemasan fisik kardus.
4. **Batas Muat Slot Rak (Ketentuan 7):** Satu slot Bin rak fisik HANYA dapat menampung **maksimal 2 palet**. Jika sistem menolak karena rak penuh, jangan paksakan menaruh palet secara fisik di rak tersebut; pilih slot bin lain atau simpan sebagai *Transit*.

---

## 4. Flowchart Alur Kerja Penerimaan Barang Inbound V2

```mermaid
flowchart TD
    Start([Operator Membuka Form Inbound /transaction/inbound]) --> Step1[Langkah 1: Scan / Input Barcode ID Palet]
    Step1 --> ApiPalletInfo[Sistem Panggil API /api/pallet/{id}/info]
    
    ApiPalletInfo --> CheckPalletState{Status Palet Saat Ini?}
    CheckPalletState -- Palet Baru / Kosong --> Step2[Langkah 2: Scan / Pilih Barcode SKU Produk]
    CheckPalletState -- Palet Berisi Produk A --> AlertActiveProduct[Layar Tampilkan: Palet Sudah Berisi Produk A]
    
    AlertActiveProduct --> CheckProductMatch{Apakah Barang yang Diterima Sama dengan Produk A?}
    CheckProductMatch -- Berbeda (Produk B) --> BlockSingleRule[STOP: Tolak Input! Palet Pertama Kali Hanya Boleh 1 Produk]
    CheckProductMatch -- Sama (Produk A) --> Step2
    
    Step2 --> ApiConversions[Sistem Panggil API /api/product/{id}/conversions Muat Satuan Valid]
    Step2 --> Step3[Langkah 3: Masukkan Kuantitas, Satuan cth: Karton, dan Expired Date]
    Step3 --> Step4[Langkah 4: Pilih Kode Mesin cth: MC-01 dan Shift Kerja cth: Shift 1]
    
    Step4 --> Step5{Langkah 5: Tentukan Lokasi Simpan Barang:}
    
    Step5 -- Langsung Naikkan ke Rak dengan Forklift --> InputBinId[Scan / Input Kode Slot BIN cth: B.10.1]
    InputBinId --> CheckBinSlotCount{Apakah Slot BIN Masih Ada Ruang (< 2 Palet)?}
    CheckBinSlotCount -- Penuh (Sudah 2 Palet) --> BlockBinOverload[STOP: Tolak! Slot BIN Sudah Penuh]
    BlockBinOverload --> ChangeAlternativeBin[Pilih Slot Rak Lain yang Masih Muat]
    CheckBinSlotCount -- Tersedia --> ConfirmOnRack[Tetapkan Status on_rack]

    Step5 -- Tahan Sementara di Lantai Gudang --> LeaveBinEmpty[Biarkan Kolom BIN Kosong atau Pilih TRANSIT]
    LeaveBinEmpty --> ConfirmInTransit[Tetapkan Status in_transit]

    ConfirmOnRack & ConfirmInTransit --> SubmitInboundForm[Operator Klik Tombol 'Simpan Inbound']
    SubmitInboundForm --> CommitTransaction[Sistem Simpan Box & Catat TransactionLog Status 'In']
    CommitTransaction --> ShowSuccessNotice[Notifikasi Hijau: Penerimaan Barang Berhasil Disimpan!]
    ShowSuccessNotice --> End([Selesai / Lanjut ke Palet Berikutnya])
```

---

## 5. Panduan Praktis Langkah demi Langkah

1. Ambil palet kayu/plastik yang bersih dan pastikan stiker barcode palet (misal: `PLT-0033`) terpasang jelas.
2. Arahkan *barcode scanner* ke barcode palet tersebut pada form WMS.
3. Pindai barcode produk pada kardus barang (misal: `PRD-KOPI-01`).
4. Hitung jumlah kardus yang disusun di atas palet. Masukkan kuantitas (misal: `72`) dan pilih satuan (`Box`).
5. Periksa tanggal expired date yang tertera pada kardus fisik dan pilih tanggal tersebut pada kalender sistem.
6. **Jika langsung dinaikkan ke rak:**
   - Scan barcode pada tiang slot rak (misal: `B.10.1`).
   - Jika sistem menampilkan pesan sukses, naikkan palet ke slot rak tersebut menggunakan forklift.
7. **Jika masih berada di lantai receiving:**
   - Kosongkan kolom Bin. Palet akan otomatis berstatus *In Transit*.
   - Saat palet nantinya dinaikkan ke rak, gunakan tombol **Putaway Palet ke BIN** untuk memperbarui lokasinya.
