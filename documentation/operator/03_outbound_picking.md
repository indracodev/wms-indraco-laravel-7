# Modul Eksekusi Pengambilan Outbound (Operator Gudang)

## 1. Deskripsi Fitur
Modul Eksekusi Outbound memandu Operator Gudang saat mengambil barang (*Picking*) dari slot rak penyimpanan untuk memenuhi daftar muat truk ekspedisi atau menurunkan palet dari rak tinggi ke area staging lantai gudang. Operator dipandu oleh sistem untuk selalu memprioritaskan pengambilan batch barang dengan tanggal kedaluwarsa paling dekat (**FEFO - First Expired First Out**).

---

## 2. Hak Akses & Role Permission
- **Role:** `operator` (Operator Gudang)
- **Middleware:** `auth`, `permission:outbound,read`, `permission:outbound,create`
- **Fokus Pelaksanaan:** Ketepatan pengambilan batch FEFO, verifikasi kuantitas kardus fisik, dan ketertiban pelaporan barang keluar.

---

## 3. Komponen Teknis & Ketentuan Kerja

### File Terkait:
- Controller: [TransactionController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/TransactionController.php) (Metode `outboundForm()`, `outbound()`, `outboundV2()`, `getRackInventory()`)
- Service Inti: [WarehouseTransactionService.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Services/WarehouseTransactionService.php) (Metode `processOutboundV2()`)
- Route:
  - Form: `GET /transaction/outbound` (`name('transaction.outbound.form')`)
  - Simpan Outbound: `POST /transaction/outbound` (`name('transaction.outbound')`)
  - API Inventaris Rak: `GET /api/rack/{id}/inventory` (`name('api.rack.inventory')`)

### Memilih Jenis Pengeluaran yang Benar:
1. **Pilih 'Keluar Ekspedisi':**
   - Jika barang diambil dari rak untuk langsung dinaikkan ke dalam kontainer / truk ekspedisi pelanggan.
   - Mengurangi stok barang secara permanen di sistem.
2. **Pilih 'Keluar In Transit':**
   - Jika palet utuh hanya diturunkan dari rak ke lantai staging untuk persiapan bongkar muat nanti.
   - Tidak memotong jumlah stok, tetapi membebaskan slot rak tersebut agar bisa diisi palet lain.

---

## 4. Flowchart Alur Kerja Pengambilan Barang Outbound

```mermaid
flowchart TD
    Start([Operator Menerima Lembar Kerja Picking dari Supervisor]) --> OpenOutbound[Buka Menu Form Outbound /transaction/outbound]
    
    OpenOutbound --> ScanBin[Scan Barcode Slot Rak Asal cth: B.10.1]
    ScanBin --> ApiRackInventory[Sistem Panggil API /api/rack/{id}/inventory]
    
    ApiRackInventory --> RenderInventoryList[Layar Tampilkan Daftar Seluruh Palet & Box di Slot Tersebut]
    
    RenderInventoryList --> CheckFEFOGuidance[Operator Tinjau Urutan Expired Date Terdekat FEFO]
    CheckFEFOGuidance --> SelectBoxBatch[Pilih Box Batch Terlama Sesuai Instruksi Picking]
    
    SelectBoxBatch --> InputPickingQty[Masukkan Jumlah Kuantitas yang Diambil & Satuannya]
    InputPickingQty --> SelectOutboundType{Pilih Tujuan Pengeluaran:}
    
    SelectOutboundType -- Muat ke Truk Ekspedisi Pengiriman --> ChooseEkspedisi[Pilih Opsi: Keluar Ekspedisi]
    SelectOutboundType -- Diturunkan ke Staging Lantai Saja --> ChooseTransit[Pilih Opsi: Keluar In Transit]
    
    ChooseEkspedisi & ChooseTransit --> ClickSubmit[Operator Klik 'Konfirmasi Pengeluaran Barang']
    
    ClickSubmit --> ValidateStockSufficiency{Stok Tersedia di Box >= Qty Pengambilan?}
    ValidateStockSufficiency -- Kurang Dari Permintaan --> AlertStockError[Sistem Tolak: Kuantitas Melebihi Stok Box Tersedia]
    AlertStockError --> AdjustQty[Sesuaikan Kuantitas Sesuai Sisa Fisik yang Ada]
    
    ValidateStockSufficiency -- Cukup --> ExecuteDeduction[Sistem Potong Stok & Simpan TransactionLog Out]
    ExecuteDeduction --> CheckPalletStatus{Apakah Palet Kini Menjadi Kosong?}
    
    CheckPalletStatus -- Ya, Seluruh Box Habis --> AutoFreeSlot[Sistem Otomatis Ubah Status Palet = empty & Bebaskan Slot Rak]
    CheckPalletStatus -- Masih Ada Sisa Box --> KeepPalletSlot[Palet Tetap Tersimpan di Slot Rak Tersebut]
    
    AutoFreeSlot & KeepPalletSlot --> CompletePickingNotice[Muncul Notifikasi Hijau: Pengeluaran Barang Sukses!]
    CompletePickingNotice --> End([Barang Diangkut ke Truk / Staging])
```

---

## 5. Panduan Praktis Langkah demi Langkah

1. Dekati rak yang tertera pada lembar instruksi picking (contoh: Rak B, slot `B.10.1`).
2. Scan barcode slot rak tersebut menggunakan barcode scanner pada form WMS.
3. Tabel akan menampilkan daftar palet dan kardus yang ada di atasnya.
4. **Perhatikan Tanggal Kedaluwarsa (Expired Date):**
   - Pilih baris kardus yang memiliki tanggal kedaluwarsa paling dekat.
5. Masukkan jumlah kardus yang akan diambil (misal: `10 Karton`).
6. Pilih **Keluar Ekspedisi** jika kardus langsung dimasukkan ke armada truk pengiriman.
7. Klik tombol **Konfirmasi Pengeluaran Barang**.
8. Jika seluruh isi palet telah habis, turunkan palet kosong tersebut ke area penyimpanan palet kosong. Slot rak kini resmi kosong di sistem dan siap diisi barang baru.
