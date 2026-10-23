# Modul Kontrol Transfer & Relokasi (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Kontrol Transfer & Relokasi memberikan instrumen bagi Supervisor Gudang untuk mengatur lalu lintas pergerakan barang antar slot rak dan antar palet di dalam gudang. Supervisor menggunakan modul ini untuk:
1. Mengoptimalkan kepadatan gudang (*warehouse space consolidation*) dengan memindahkan palet dari area transit ke slot rak kosong yang strategis.
2. Mengotorisasi penggabungan beberapa macam produk pada satu palet (*Mixed-Pallet Transfer* - Ketentuan 2) untuk efisiensi pengiriman parsial ke distributor.
3. Memastikan relokasi palet tidak melebihi batas beban dan daya tampung rak (maksimal 2 palet per slot Bin).

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`, `permission:transfer,read`, `permission:transfer,create`
- **Fokus Kontrol:** Mencegah terjadinya kelebihan muatan rak (*rack overload*) dan mengawasi konsolidasi stok parsial.

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `transferForm()`, `transfer()`, `transferPalletToPallet()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `assignPalletToRack()`, `transferProductBetweenPallets()`)
- Route:
  - Form: `GET /transaction/transfer` (`name('transaction.transfer.form')`)
  - Relokasi ke Bin: `POST /transaction/transfer` (`name('transaction.transfer')`)
  - Transfer Box Antar Palet: `POST /transaction/transfer/pallet-to-pallet` (`name('transaction.transfer.pallet_to_pallet')`)

---

## 4. Flowchart Alur Kontrol Transfer & Relokasi

```mermaid
flowchart TD
    Start([Supervisor Mengidentifikasi Kebutuhan Relokasi]) --> ChooseOperation{Tentukan Jenis Operasi Transfer:}
    
    %% Alur Relokasi Palet ke Bin Fisik
    ChooseOperation -- 1. Relokasi Palet Utuh ke Slot Rak --> SelectPalletAndBin[Pilih ID Palet & Tentukan Slot Bin Target]
    SelectPalletAndBin --> CheckTargetCapacity{Apakah Slot Bin Target Masih Memiliki Ruang (< 2 Palet)?}
    CheckTargetCapacity -- Penuh (Sudah 2 Palet) --> RejectRelocation[Sistem Tolak: Bin Target Melebihi Batas Kapasitas]
    RejectRelocation --> ChooseAnotherBin[Supervisor Tentukan Slot Bin Target Alternatif]
    
    CheckTargetCapacity -- Tersedia Ruang --> CommitRelocation[Commit assignPalletToRack: Update Posisi Bin & Status on_rack]
    CommitRelocation --> LogRelocationTx[Catat Mutasi Relokasi pada TransactionLog]

    %% Alur Transfer Parsial Box Antar Palet (Mixed-Pallet)
    ChooseOperation -- 2. Transfer Box Antar Palet --> SelectPalletsAndBatch[Pilih Palet Asal, Box Batch Spesifik, dan Palet Tujuan]
    SelectPalletsAndBatch --> CheckSourceStock{Apakah Qty Tersedia Cukup di Palet Asal?}
    CheckSourceStock -- Kurang --> RejectTransfer[Sistem Tolak: Kuantitas Melebihi Stok Sisa Box Asal]
    
    CheckSourceStock -- Cukup --> ApplyMixedRule[Ketentuan 2: Sistem Izinkan Pencampuran Produk pada Palet Tujuan]
    ApplyMixedRule --> ExecuteBoxTransfer[Commit transferProductBetweenPallets: Potong Palet Asal & Tambah ke Palet Tujuan]
    ExecuteBoxTransfer --> CheckSourceEmpty{Apakah Palet Asal Kini Menjadi Kosong?}
    CheckSourceEmpty -- Ya --> MarkEmptyPallet[Set Palet Asal: status = 'empty', is_active = false, bin_id = NULL]
    CheckSourceEmpty -- Tidak --> MaintainSourcePallet[Pertahankan Palet Asal Aktif]

    LogRelocationTx & MarkEmptyPallet & MaintainSourcePallet --> NotifySupervisorSuccess[Tampilkan Notifikasi Berhasil & Update Status Fisik]
    NotifySupervisorSuccess --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Buka menu **Transaksi** > **Transfer / Relokasi Barang**.
2. **Prosedur Penggabungan Muatan (Mixed-Pallet):**
   - Pilih tab **Transfer Antar Palet**.
   - Masukkan ID Palet Asal dan tentukan nomor box yang ingin digabungkan.
   - Masukkan ID Palet Tujuan (meskipun palet tujuan sudah berisi SKU yang berbeda).
   - Masukkan kuantitas pemindahan dan klik **Proses Transfer**.
3. **Pemeriksaan Palet Kosong:**
   - Jika palet asal habis seluruh isinya, sistem otomatis melepaskan nomor slot raknya sehingga supervisor dapat langsung mengarahkan palet baru untuk mengisi slot tersebut.
