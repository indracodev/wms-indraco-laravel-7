# Modul Verifikasi Transaksi Inbound (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Verifikasi Inbound memberikan wewenang kepada Supervisor Gudang untuk mengawasi, menguji kelayakan, serta mengeksekusi transaksi penerimaan barang masuk dari pabrik (*Plant*). Supervisor memastikan bahwa seluruh barang yang masuk memenuhi standar kualitas, memiliki masa kedaluwarsa (*Expired Date*) yang valid, dikonversi ke satuan dasar secara akurat, serta ditempatkan pada slot rak (*Bin*) yang memiliki kapasitas memadai tanpa melanggar batas maksimal 2 palet per slot.

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`, `permission:inbound,read`, `permission:inbound,create`
- **Tanggung Jawab:** Validasi kepatuhan SOP logistik Indraco dan penyelesaian kendala penerimaan barang di lapangan.

---

## 3. Komponen Teknis & Ketentuan Bisnis

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `inboundForm()`, `inbound()`, `inboundPalletToBin()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `processInboundV2()`, `assignPalletToRack()`)
- Route:
  - Form: `GET /transaction/inbound` (`name('transaction.inbound.form')`)
  - Simpan Inbound: `POST /transaction/inbound` (`name('transaction.inbound')`)
  - Putaway Palet ke Bin: `POST /transaction/inbound/pallet-to-bin` (`name('transaction.inbound.pallet_to_bin')`)

### Poin Kritis Pengawasan Supervisor:
1. **Verifikasi Ketentuan 1:** Menolak penerimaan jika operator salah memindai palet yang sudah terisi SKU produk lain.
2. **Verifikasi Konversi Satuan (Ketentuan 3):** Memastikan faktor pengali satuan karton/ball sesuai dengan master data agar tidak terjadi selisih stok fisik (*variance*).
3. **Penyelesaian Slot Rak Penuh (Ketentuan 7):** Jika operator melaporkan bahwa slot rak yang dituju penuh (sudah ada 2 palet), supervisor berwenang mengarahkan penempatan ke slot bin alternatif atau menahan palet pada status `in_transit`.

---

## 4. Flowchart Alur Kerja Verifikasi Transaksi Inbound

```mermaid
flowchart TD
    Start([Barang Datang dari Lini Produksi / Pemasok]) --> SupervisorInspect[Supervisor Melakukan Pemeriksaan Fisik: Dokumen Surat Jalan, Kondisi Kemasan, dan Label ED]
    
    SupervisorInspect --> PhysicalCheck{Barang Lolos Uji Fisik?}
    PhysicalCheck -- Cacat / Rusak --> RejectGoods[Tolak Barang & Terbitkan Berita Acara Retur ke Pabrik]
    PhysicalCheck -- Sesuai Standar --> OpenInboundForm[Buka Form Inbound di Sistem WMS]
    
    OpenInboundForm --> InputTransactionData[Scan Barcode Palet, Pilih SKU Produk, Masukkan Qty & Expired Date]
    InputTransactionData --> ValidateSingleProduct{Ketentuan 1: Apakah Palet Sudah Terisi Produk Lain?}
    
    ValidateSingleProduct -- Terisi Produk Lain --> AlertSingleProduct[Sistem Tolak: Palet Pertama Kali Hanya Boleh 1 Produk]
    AlertSingleProduct --> ChangePallet[Ganti dengan Palet Kosong Baru atau Alihkan ke Transfer Antar Palet]
    
    ValidateSingleProduct -- Palet Kosong / Produk Sama --> SelectStorageOption{Tentukan Lokasi Penempatan}
    
    SelectStorageOption -- Simpan di Rak Fisik Langsung --> CheckTargetBinSlot{Slot Bin Memiliki Ruang (< 2 Palet)?}
    CheckTargetBinSlot -- Penuh (2 Palet) --> AlertBinFull[Sistem Tolak: Slot Rak Penuh]
    AlertBinFull --> RedirectAlternativeBin[Pilih Slot Bin Alternatif yang Masih Kosong]
    
    CheckTargetBinSlot -- Tersedia --> PutawayOnRack[Tetapkan status = 'on_rack' & bin_id Terpilih]
    SelectStorageOption -- Tahan Sementara di Lantai --> PutawayTransit[Tetapkan status = 'in_transit' & bin_id = NULL]
    
    PutawayOnRack & PutawayTransit --> ExecuteInboundDB[Commit DB Transaction Simpan Box, Palet, dan Log Transaksi]
    ExecuteInboundDB --> PrintConfirmation[Cetak / Konfirmasi Bukti Penerimaan Barang Masuk]
    PrintConfirmation --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Akses menu **Transaksi** > **Penerimaan Barang (Inbound)**.
2. **Pemeriksaan Palet:**
   - Masukkan ID Palet. Sistem secara otomatis memuat status palet terkini melalui panggilan API `/api/pallet/{id}/info`.
   - Pastikan palet belum pernah digunakan untuk produk yang berbeda.
3. **Penetapan Shift & Kode Mesin:**
   - Masukkan nomor shift operator yang bertugas (Shift 1, 2, atau 3) dan kode mesin produksi (misal: `MC-01`).
4. **Prosedur Put-away Palet Transit:**
   - Jika barang sebelumnya disimpan dengan status transit, gunakan formulir **Put-away Palet ke BIN** untuk meresmikan penempatannya ke slot rak fisik setelah operator forklift selesai menaikkan palet.
