# Modul Operasional Transfer & Relokasi (Operator Gudang)

## 1. Deskripsi Fitur
Modul Operasional Transfer memandu Operator Gudang saat memindahkan palet fisik antar slot rak atau memindahkan kardus/box dari satu palet ke palet lain. Operator menggunakan fitur ini untuk menata kembali tata letak gudang, mengisi slot rak yang kosong dengan palet transit, atau menyiapkan palet campuran (*Mixed-Pallet*) untuk pengiriman parsial sesuai arahan supervisor.

---

## 2. Hak Akses & Role Permission
- **Role:** `operator` (Operator Gudang)
- **Middleware:** `auth`, `permission:transfer,read`, `permission:transfer,create`
- **Fokus Kerja:** Memastikan lokasi rak di sistem selalu sama persis dengan posisi fisik barang di gudang nyata.

---

## 3. Komponen Teknis & Ketentuan Kerja

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `transferForm()`, `transfer()`, `transferPalletToPallet()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `assignPalletToRack()`, `transferProductBetweenPallets()`)
- Route:
  - Form: `GET /transaction/transfer` (`name('transaction.transfer.form')`)
  - Pindah Palet ke Bin: `POST /transaction/transfer` (`name('transaction.transfer')`)
  - Pindah Box Antar Palet: `POST /transaction/transfer/pallet-to-pallet` (`name('transaction.transfer.pallet_to_pallet')`)

### Aturan Operasional Transfer:
1. **Pengecualian Palet Campuran (Ketentuan 2):** Pada form *Transfer Antar Palet*, operator DIIZINKAN memindahkan produk A ke palet yang sudah berisi produk B. Ini adalah satu-satunya cara sah di sistem Indraco untuk membuat palet *mixed-product*.
2. **Kapasitas Slot Bin Target:** Pastikan slot Bin yang dituju belum memiliki 2 palet. Jika slot tujuan sudah terisi 2 palet, sistem akan menolak pemindahan.
3. **Pembersihan Palet:** Jika seluruh isi palet asal dipindahkan hingga habis, sistem otomatis menonaktifkan palet asal tersebut.

---

## 4. Flowchart Alur Kerja Operasional Transfer

```mermaid
flowchart TD
    Start([Operator Buka Form Transfer /transaction/transfer]) --> ChooseAction{Pilih Bentuk Pemindahan Barang:}
    
    %% Alur 1: Pindahkan Palet Utuh ke Bin Lain
    ChooseAction -- Pindahkan Palet Utuh ke Rak Lain --> SelectPallet[1. Pilih / Scan Barcode Palet yang Dipindah]
    SelectPallet --> SelectTargetBin[2. Pilih / Scan Barcode Slot Bin Tujuan cth: A.02.1]
    SelectTargetBin --> SubmitRelocation[3. Klik Tombol 'Pindahkan Palet ke BIN']
    
    SubmitRelocation --> CheckBinSpace{Apakah Slot Bin Tujuan Penuh (>= 2 Palet)?}
    CheckBinSpace -- Penuh --> ErrorBinSpace[Sistem Tolak: Slot Rak Tujuan Penuh!]
    ErrorBinSpace --> ChooseOtherBin[Pilih Slot Rak Lain yang Masih Kosong]
    CheckBinSpace -- Ada Ruang --> SaveRelocation[Sistem Pindahkan Palet & Cetak Notifikasi Sukses]
    SaveRelocation --> MoveForklift[Operator Forklift Naikkan Palet ke Slot Rak Tujuan]

    %% Alur 2: Pindahkan Kardus Antar Palet (Mixed Pallet)
    ChooseAction -- Pindahkan Kardus dari Palet A ke Palet B --> SelectSourcePallet[1. Scan Barcode Palet Asal]
    SelectSourcePallet --> SelectBoxBatch[2. Pilih Kardus / Batch yang Mau Dipindah]
    SelectBoxBatch --> InputTransferQty[3. Masukkan Jumlah Kardus yang Dipindahkan]
    SelectBoxBatch --> SelectTargetPallet[4. Scan Barcode Palet Tujuan]
    
    SelectTargetPallet --> SubmitBoxTransfer[5. Klik Tombol 'Transfer Antar Palet']
    SubmitBoxTransfer --> ValidateQty{Stok Palet Asal Cukup?}
    ValidateQty -- Kurang --> ErrorQty[Sistem Tolak: Jumlah Melebihi Isi Palet Asal]
    ValidateQty -- Cukup --> CommitBoxTransfer[Sistem Pindahkan Stok ke Palet Tujuan Tanpa Batas Produk]
    
    CommitBoxTransfer --> CheckSourceLeft{Apakah Isi Palet Asal Habis Total?}
    CheckSourceLeft -- Habis --> DeactivatePallet[Palet Asal Berubah Menjadi Kosong & Bebas dari Rak]
    CheckSourceLeft -- Masih Ada Sisa --> RetainPallet[Sisa Kardus Tetap Berada di Palet Asal]

    MoveForklift & DeactivatePallet & RetainPallet --> End([Selesai])
```

---

## 5. Panduan Praktis Langkah demi Langkah

### A. Memindahkan Palet dari Staging ke Rak:
1. Dekati palet yang berada di area transit.
2. Buka menu **Transfer / Relokasi Barang**.
3. Pada bagian **Relokasi Palet ke BIN**, masukkan ID Palet (misal: `PLT-0012`).
4. Pilih nomor slot rak yang kosong (misal: `B.05.2`).
5. Klik **Pindahkan Palet ke BIN**.
6. Gunakan forklift untuk menaikkan palet fisik ke posisi rak tersebut.

### B. Menggabungkan Kardus ke Palet Lain:
1. Buka form **Transfer Antar Palet**.
2. Masukkan ID Palet Asal (misal: `PLT-0001`).
3. Pilih batch kardus yang akan diambil.
4. Masukkan jumlah kardus yang akan dipindahkan.
5. Masukkan ID Palet Tujuan (misal: `PLT-0002`).
6. Klik **Transfer Antar Palet**.
7. Pindahkan kardus fisik dari palet 1 ke palet 2 secara rapi.
