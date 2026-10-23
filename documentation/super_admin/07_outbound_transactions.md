# Modul Transaksi Outbound / Pengeluaran Barang (Super Admin)

## 1. Deskripsi Fitur
Modul Transaksi Outbound mengatur proses pengeluaran stok barang dari sistem rak gudang untuk didistribusikan ke pelanggan (*Ekspedisi*) atau diturunkan sementara ke area staging/transit (*In Transit*). Super Admin memiliki kendali untuk mengawasi seluruh aktivitas pengeluaran, memvalidasi kepatuhan prinsip FIFO/FEFO berdasarkan tanggal kedaluwarsa, memastikan akurasi pemotongan stok kuantitas terkecil (*base unit*), serta mengelola pembebasan slot rak secara otomatis ketika palet telah kosong.

---

## 2. Hak Akses & Role Permission
- **Role:** `super_admin` (Akses Penuh: Eksekusi, Otorisasi, dan Pembatalan Log)
- **Middleware:** `auth`, `permission:outbound,read`, `permission:outbound,create`

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `outboundForm()`, `outbound()`, `outboundV2()`, `getRackInventory()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `processOutboundV2()`)
- Route:
  - Form: `GET /transaction/outbound` (`name('transaction.outbound.form')`)
  - Eksekusi Outbound: `POST /transaction/outbound` (`name('transaction.outbound')`)
  - API Stok Rak/Bin: `GET /api/rack/{id}/inventory` (`name('api.rack.inventory')`)

### Aturan Bisnis & Logika Dual Outbound (Ketentuan 4):
1. **Opsi 1: Keluar Ekspedisi (`outbound_type = ekspedisi`):**
   - Mengurangi kuantitas stok secara permanen dari box yang dipilih.
   - Jika sisa kuantitas box bernilai 0, status box diubah menjadi `empty`.
   - Jika seluruh box dalam palet tersebut sudah berstatus `empty`, status palet otomatis diubah menjadi `empty`, `is_active = false`, dan relasi `bin_id = NULL` (slot rak otomatis terbebas).
   - Tercatat di log transaksi dengan status `Out_Ekspedisi`.
2. **Opsi 2: Keluar In Transit (`outbound_type = in_transit`):**
   - Digunakan ketika palet utuh atau sebagian ingin dipindahkan dari rak ke area staging loading dock tanpa mengurangi kuantitas stok barang.
   - Mengubah status palet menjadi `in_transit` dan mengosongkan `bin_id = NULL`.
   - Tercatat di log transaksi dengan status `Out_Transit`.

---

## 4. Flowchart Alur Kerja Transaksi Outbound V2

```mermaid
flowchart TD
    Start([Super Admin Buka Form Outbound / Pengeluaran]) --> SelectBin[Scan / Pilih Slot Rak atau Bin Asal]
    SelectBin --> FetchInventory[Panggil API /api/rack/{id}/inventory]
    FetchInventory --> DisplayPallets[Tampilkan Daftar Palet, Box, dan ED Terurut FEFO]
    
    DisplayPallets --> PickItem[Pilih Palet, Box Spesifik, Masukkan Qty Keluar, dan Satuan]
    PickItem --> SelectType{Pilih Jenis Pengeluaran Barang}
    
    SelectType --> OptEkspedisi[1. Pengeluaran Ekspedisi - Pengiriman Luar]
    SelectType --> OptTransit[2. Pengeluaran In Transit - Turun Rak ke Staging]

    OptEkspedisi & OptTransit --> SubmitOutbound[Super Admin Klik Konfirmasi Pengeluaran]
    SubmitOutbound --> StartDBTx[Mulai DB::transaction dengan Row Locking]

    subgraph ServiceEngine [WarehouseTransactionService::processOutboundV2]
        StartDBTx --> LockRecords[Pallet & Box lockForUpdate]
        LockRecords --> CalcNeeded[Hitung Qty Base Unit Dibutuhkan = Qty Input * Faktor Konversi]
        
        CalcNeeded --> ValidateStock{Apakah Stok Tersedia di Box >= Qty Dibutuhkan?}
        ValidateStock -- Stok Tidak Cukup --> ThrowStockShortage[Rollback & Throw Exception: Qty Melebihi Stok Box]
        
        ValidateStock -- Stok Cukup --> BranchType{Jenis Outbound?}
        
        %% Cabang Ekspedisi
        BranchType -- Ekspedisi --> DeductStock[Kurangi Box qty -= Qty Dibutuhkan]
        DeductStock --> CheckBoxZero{Apakah Box qty <= 0?}
        CheckBoxZero -- Ya --> SetBoxEmpty[Set Box status = 'empty' & qty = 0]
        CheckBoxZero -- Tidak --> SaveBox[Simpan Sisa Qty Box]
        
        SetBoxEmpty & SaveBox --> CheckPalletActiveBoxes{Apakah Masih Ada Box Aktif di Palet Ini?}
        CheckPalletActiveBoxes -- Habis Seluruhnya --> FreePalletSlot[Set Pallet: status = 'empty', is_active = false, bin_id = NULL]
        CheckPalletActiveBoxes -- Masih Ada Sisa Box --> KeepPalletActive[Pertahankan Pallet Status on_rack]
        
        FreePalletSlot & KeepPalletActive --> SetEkspedisiLog[Set status log = 'Out_Ekspedisi']

        %% Cabang In Transit
        BranchType -- In Transit --> ReleaseBinSlot[Set Pallet: bin_id = NULL & status = 'in_transit']
        ReleaseBinSlot --> SetTransitLog[Set status log = 'Out_Transit']

        %% Pencatatan Log Terpadu
        SetEkspedisiLog & SetTransitLog --> CreateTransactionLog[TransactionLog::create Simpan PIC, Shift, Qty, Tanggal]
        CreateTransactionLog --> WriteAuditLog[AuditLogger::log Catat Jejak Pengeluaran Barang]
    end

    WriteAuditLog --> CommitDB[DB::commit Database Transaction Berhasil]
    ThrowStockShortage --> RollbackDB[DB::rollback Batalkan Perubahan]
    
    CommitDB --> FlashSuccess[Redirect dengan Pesan Sukses]
    RollbackDB --> FlashError[Redirect Back dengan Pesan Error Validasi]
    FlashSuccess & FlashError --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Operasional

1. Akses menu **Transaksi** > **Pengeluaran Barang (Outbound)**.
2. **Pilih Slot Rak/Bin:** Ketik atau scan kode Bin (cth: `B.10.1`). Antarmuka secara asinkron memanggil data palet dan box yang berada pada slot tersebut.
3. **Pilih Box / Batch:** Tabel menyajikan daftar box beserta tanggal expired (*Expired Date*). Sistem merekomendasikan pengambilan batch yang memiliki tanggal kedaluwarsa terdekat (*FEFO - First Expired First Out*).
4. **Masukkan Jumlah Pengambilan:** Ketik kuantitas dan pilih satuan (cth: `5 Karton`). Sistem memvalidasi secara instan terhadap stok sisa di box tersebut.
5. **Pilih Jenis Pengeluaran:**
   - **Keluar Ekspedisi:** Jika barang siap dimuat ke armada truk pengiriman.
   - **Keluar In Transit:** Jika barang hanya dipindahkan dari rak ke area transit lantai gudang.
6. Klik **Eksekusi Pengeluaran Barang**.
