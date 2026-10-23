# Modul Otorisasi & Kontrol Outbound (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Otorisasi Outbound memberikan kewenangan kepada Supervisor Gudang untuk mengontrol proses pengeluaran stok persediaan dari rak penyimpanan guna memenuhi pesanan penjualan (*Sales Order* / *Delivery Order*). Supervisor bertanggung jawab memastikan kepatuhan prinsip **FEFO (First Expired First Out)**, mengotorisasi pemotongan kuantitas stok untuk pengiriman armada ekspedisi (*Keluar Ekspedisi*), atau menyetujui penurunan palet dari rak ke staging lantai gudang (*Keluar In Transit*) untuk konsolidasi muatan.

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`, `permission:outbound,read`, `permission:outbound,create`
- **Tanggung Jawab:** Mencegah terjadinya pengeluaran barang yang kedaluwarsa (*spoilage risk*) dan menjamin validitas pemotongan stok pada inventaris sistem.

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `outboundForm()`, `outbound()`, `outboundV2()`, `getRackInventory()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `processOutboundV2()`)
- Route:
  - `GET /transaction/outbound` (`name('transaction.outbound.form')`)
  - `POST /transaction/outbound` (`name('transaction.outbound')`)
  - `GET /api/rack/{id}/inventory` (`name('api.rack.inventory')`)

### Dua Jalur Otorisasi Pengeluaran (Ketentuan 4):
1. **Pengeluaran Ekspedisi:**
   - Memotong kuantitas persediaan secara permanen.
   - Jika box telah habis, status diubah menjadi `empty`. Jika seluruh isi palet kosong, sistem secara otomatis melepaskan slot rak (`bin_id = NULL`) sehingga siap diisi palet baru.
2. **Pengeluaran In Transit:**
   - Menurunkan palet dari rak penyimpanan ke area staging/konsolidasi. Kuantitas stok tidak berkurang, namun status palet berubah menjadi `in_transit` dan membebaskan slot rak asalnya.

---

## 4. Flowchart Alur Otorisasi Outbound oleh Supervisor

```mermaid
flowchart TD
    Start([Dokumen Delivery Order / Permintaan Pengiriman Masuk]) --> ReviewOrder[Supervisor Meninjau SKU dan Volume Pesanan]
    ReviewOrder --> OpenOutbound[Buka Menu Transaksi Outbound di WMS]
    
    OpenOutbound --> SelectSourceBin[Pilih Slot Rak / Bin Penyimpanan]
    SelectSourceBin --> CallRackInventory[Panggil API /api/rack/{id}/inventory]
    CallRackInventory --> DisplayBatches[Tampilkan Batch Box Terurut FEFO Tanggal Kedaluwarsa Terdekat]
    
    DisplayBatches --> ValidateFEFO{Apakah Batch yang Dipilih Adalah ED Paling Awal?}
    ValidateFEFO -- Tidak Sesuai FEFO --> WarnFEFO[Peringatan: Terdapat Batch Lain dengan ED Lebih Awal di Rak Ini]
    WarnFEFO --> OverrideOrAdjust{Supervisor Putuskan Pengambilan:}
    OverrideOrAdjust -- Ikuti FEFO --> SelectOldestBatch[Pilih Batch Terlama / ED Paling Dekat]
    OverrideOrAdjust -- Kebutuhan Khusus --> ForceSelectBatch[Lanjutkan dengan Alasan Otorisasi Tertulis]

    ValidateFEFO -- Sesuai FEFO --> SelectOldestBatch
    SelectOldestBatch & ForceSelectBatch --> DetermineOutboundType{Tentukan Jenis Pengeluaran:}
    
    DetermineOutboundType -- 1. Keluar Ekspedisi --> ExecEkspedisi[Pilih Tipe 'ekspedisi': Potong Stok Permanen & Status Log Out_Ekspedisi]
    DetermineOutboundType -- 2. Keluar In Transit --> ExecTransit[Pilih Tipe 'in_transit': Bebaskan Slot Rak & Status Log Out_Transit]
    
    ExecEkspedisi & ExecTransit --> SubmitToDB[Commit DB Transaction Pemotongan Stok / Pelepasan Rak]
    SubmitToDB --> NotifyForklift[Serahkan Lembar Kerja Picking ke Operator Forklift]
    NotifyForklift --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Akses menu **Transaksi** > **Pengeluaran Barang (Outbound)**.
2. **Pilih Lokasi Rak Asal:**
   - Masukkan ID Bin (misal: `B.10.1`).
   - Sistem seketika menyajikan rincian seluruh palet dan box yang tersedia pada slot tersebut.
3. **Pemeriksaan FEFO:**
   - Periksa tanggal kedaluwarsa pada kolom *Expired Date*. Pastikan operator diarahkan untuk mengambil box dengan tanggal expired paling dekat terlebih dahulu.
4. **Eksekusi Pengeluaran:**
   - Masukkan kuantitas yang dikeluarkan beserta satuannya.
   - Pilih jenis pengeluaran (**Keluar Ekspedisi** untuk pemotongan stok permanen, atau **Keluar In Transit** untuk penurunan palet utuh ke staging).
   - Klik tombol konfirmasi untuk mencatat mutasi pada log transaksi dan audit sistem.
