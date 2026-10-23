# Modul Transaksi Transfer & Relokasi Barang (Super Admin)

## 1. Deskripsi Fitur
Modul Transfer & Relokasi mengelola mutasi internal stok di dalam fasilitas gudang. Super Admin memiliki kendali penuh atas 2 macam transaksi transfer:
1. **Relokasi Palet Fisik Antar Bin (Put-away / Relocation):** Memindahkan satu unit palet utuh dari satu slot Bin (atau area Transit) ke slot Bin target lain dengan tetap menjaga batas kapasitas maksimal slot rak (maksimal 2 palet per bin).
2. **Transfer Produk Antar Palet (Mixed-Pallet Transfer - Ketentuan 2):** Memindahkan sejumlah kuantitas box/produk spesifik dari Palet Asal ke Palet Tujuan. Fitur ini merupakan satu-satunya metode resmi yang diizinkan sistem WMS Indraco untuk mencampur lebih dari satu macam produk (*mixed-pallet*) dalam satu palet yang sama.

---

## 2. Hak Akses & Role Permission
- **Role:** `super_admin` (Akses Penuh: Eksekusi, Otorisasi, dan Pembatalan Log)
- **Middleware:** `auth`, `permission:transfer,read`, `permission:transfer,create`

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `transferForm()`, `transfer()`, `transferPalletToPallet()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `assignPalletToRack()`, `transferProductBetweenPallets()`)
- Route:
  - Form: `GET /transaction/transfer` (`name('transaction.transfer.form')`)
  - Relokasi Palet ke Bin: `POST /transaction/transfer` (`name('transaction.transfer')`)
  - Transfer Box Antar Palet: `POST /transaction/transfer/pallet-to-pallet` (`name('transaction.transfer.pallet_to_pallet')`)

### Aturan Bisnis & Ketentuan Khusus:
1. **Ketentuan 2 (Pengecualian Mixed-Pallet):** Berbeda dengan proses Inbound pertama kali yang mewajibkan 1 macam SKU, pada proses *Transfer Antar Palet*, sistem **TIDAK** membatasi produk yang masuk ke palet tujuan. Palet tujuan dapat menampung produk A, produk B, dan produk C secara bersamaan.
2. **Kapasitas Bin Target:** Pada relokasi palet antar slot bin, sistem memverifikasi bahwa jumlah palet aktif di slot target saat ini belum mencapai 2 unit.
3. **Pembersihan Otomatis Palet Asal:** Jika seluruh box pada palet asal dipindahkan ke palet lain, palet asal otomatis diubah menjadi `status = 'empty'`, `is_active = false`, dan dilepaskan dari bin (`bin_id = NULL`).

---

## 4. Flowchart Alur Kerja Transaksi Transfer & Relokasi

```mermaid
flowchart TD
    Start([Super Admin Buka Halaman Transfer /transaction/transfer]) --> SelectTransferType{Pilih Metode Transfer}
    
    %% Alur 1: Relokasi Palet ke Bin Lain
    SelectTransferType -- Relokasi Palet Fisik ke BIN --> InputRelocation[Pilih ID Palet & Tentukan BIN Target]
    InputRelocation --> StartRelocationTx[Mulai DB::transaction assignPalletToRack]
    
    StartRelocationTx --> LockTargetBin[Lock Target Bin Record FOR UPDATE]
    LockTargetBin --> CheckBinSlots{Jumlah Palet Aktif di Bin Target >= 2?}
    CheckBinSlots -- Penuh --> ThrowBinFull[Rollback & Throw Exception: Slot BIN Target Penuh]
    CheckBinSlots -- Tersedia --> UpdatePalletLocation[Update Pallet: bin_id = Target Bin & status = 'on_rack']
    UpdatePalletLocation --> LogRelocation[AuditLogger & TransactionLog Catat Relokasi Palet]
    LogRelocation --> CommitRelocation[Commit DB & Tampilkan Pesan Sukses]

    %% Alur 2: Transfer Box Antar Palet (Mixed-Pallet)
    SelectTransferType -- Transfer Box Antar Palet --> InputBoxTransfer[Pilih Palet Asal, Box Asal, Qty, Satuan, dan Palet Tujuan]
    InputBoxTransfer --> StartBoxTransferTx[Mulai DB::transaction transferProductBetweenPallets]
    
    StartBoxTransferTx --> LockSourceAndTarget[Pallet Asal, Pallet Tujuan, & Box Asal lockForUpdate]
    LockSourceAndTarget --> CalcBaseNeeded[Hitung Kebutuhan Qty Base Unit = Qty Input * Faktor Konversi]
    
    CalcBaseNeeded --> ValidateSourceStock{Stok Box Asal >= Qty Base Dibutuhkan?}
    ValidateSourceStock -- Kurang --> ThrowShortage[Rollback & Throw Exception: Qty Melebihi Stok Palet Asal]
    
    ValidateSourceStock -- Cukup --> DeductSourceBox[Kurangi Box Asal qty -= Qty Base]
    DeductSourceBox --> CheckSourceZero{Sisa Qty Box Asal <= 0?}
    CheckSourceZero -- Ya --> SetSourceBoxEmpty[Set Box Asal: qty = 0 & status = 'empty']
    CheckSourceZero -- Tidak --> SaveSourceBox[Simpan Sisa Qty Box Asal]
    
    SetSourceBoxEmpty & SaveSourceBox --> TargetBoxCheck{Box Produk & ED Sama Sudah Ada di Palet Tujuan?}
    TargetBoxCheck -- Ada --> AddTargetBoxQty[Tambahkan Qty ke Box Eksisting di Palet Tujuan]
    TargetBoxCheck -- Belum Ada --> CreateTargetBox[Buat Box Baru di Palet Tujuan (Mixed-Pallet Diizinkan)]

    AddTargetBoxQty & CreateTargetBox --> CheckSourcePalletEmpty{Apakah Seluruh Box di Palet Asal Habis?}
    CheckSourcePalletEmpty -- Habis Seluruhnya --> DeactivateSourcePallet[Set Palet Asal: status = 'empty', is_active = false, bin_id = NULL]
    CheckSourcePalletEmpty -- Masih Ada Stok Lain --> KeepSourcePallet[Pertahankan Status Palet Asal]

    DeactivateSourcePallet & KeepSourcePallet --> EnsureTargetActive[Set Palet Tujuan is_active = true]
    EnsureTargetActive --> WriteTransferLogs[TransactionLog Status = 'Transfer_Pallet' & AuditLogger Log Jejak]
    WriteTransferLogs --> CommitBoxTransfer[Commit DB & Tampilkan Notifikasi Sukses]

    ThrowBinFull & ThrowShortage --> RollbackError[Rollback Transaksi & Tampilkan Pesan Gagal]
    CommitRelocation & CommitBoxTransfer & RollbackError --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Operasional

### A. Prosedur Relokasi Palet ke Bin:
1. Pilih **Form Relokasi Palet ke BIN**.
2. Pilih kode Palet yang akan dipindahkan dari dropdown atau ketik ID palet.
3. Pilih slot Bin tujuan baru (misal: memindahkan palet dari staging transit ke `RK-A` slot `A.01.1`).
4. Klik **Pindahkan Palet ke BIN**. Sistem seketika memverifikasi daya tampung bin tujuan dan memperbarui status posisi rak.

### B. Prosedur Transfer Produk Antar Palet:
1. Pilih **Form Transfer Antar Palet**.
2. Pilih **Palet Asal** dan tentukan **Box / Batch** spesifik yang ingin dipindahkan.
3. Masukkan kuantitas yang ingin ditransfer beserta satuannya.
4. Masukkan kode **Palet Tujuan**.
5. Klik **Konfirmasi Transfer Antar Palet**. Sistem secara otomatis memotong stok dari palet asal dan menambahkannya ke palet tujuan tanpa batasan kesamaan produk.
