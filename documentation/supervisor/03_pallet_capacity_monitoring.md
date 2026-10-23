# Modul Monitoring Kapasitas & Status Palet (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Monitoring Palet memungkinkan Supervisor Gudang untuk mengawasi status seluruh palet di lantai pabrik dan gudang penyimpanan. Supervisor dapat memantau palet yang sedang aktif terisi persediaan (*active with stock*), palet yang tertahan di area transit (*in_transit*), palet kosong siap pakai (*empty*), serta memverifikasi bahwa muatan palet tidak melampaui batas kapasitas baku (18, 72, atau 84 unit) sesuai ketentuan logistik Indraco.

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`, `permission:pallet,read`
- **Fungsi Utama:** Audit fisik palet, pengawasan batas kapasitas muat, dan penugasan relokasi palet transit.

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [PalletController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/PalletController.php) (Metode `index()`, `show()`)
- Route:
  - `GET /pallets` (`name('pallets.index')`)
  - `GET /api/pallets/{id}` (`name('pallets.show')`)

### Panel Ringkasan Cepat (*Fast SQL Overview*):
Sistem menyajikan metrik statistik yang dihitung langsung melalui kueri SQL cepat:
- **Total Palet:** Jumlah seluruh palet terdaftar.
- **Palet Berisi Stok:** Palet aktif yang memiliki relasi box berstatus `active`.
- **Palet In Transit:** Palet yang belum ditempatkan pada slot Bin fisik (`bin_id IS NULL` atau status `in_transit`).
- **Palet Kosong:** Palet yang tidak memiliki persediaan aktif dan siap dialokasikan untuk penerimaan baru.

---

## 4. Flowchart Alur Monitoring Kapasitas Palet

```mermaid
flowchart TD
    Start([Supervisor Membuka Modul Palet /pallets]) --> QueryStats[PalletController Eksekusi Kueri Fast SQL Count]
    QueryStats --> RenderOverview[Tampilkan Kartu Statistik & Filter Status]
    
    RenderOverview --> SelectFilter{Pilih Filter Tampilan}
    SelectFilter --> FilterTransit[Filter: Palet In Transit]
    SelectFilter --> FilterActive[Filter: Palet Berisi Stok]
    SelectFilter --> FilterEmpty[Filter: Palet Kosong]

    FilterTransit & FilterActive & FilterEmpty --> RenderPalletTable[Render Tabel Daftar Palet Terfilter]
    
    RenderPalletTable --> InspectPallet[Supervisor Klik Detail Palet Tertentu]
    InspectPallet --> CallApiShow[Panggil Endpoint /api/pallets/{id}]
    
    CallApiShow --> CheckCapacityRule{Evaluasi Kapasitas Muatan:}
    CheckCapacityRule --> Cap18[Kapasitas Standar: 18 Unit]
    CheckCapacityRule --> Cap72[Kapasitas Standar: 72 Unit]
    CheckCapacityRule --> Cap84[Kapasitas Standar: 84 Unit]

    Cap18 & Cap72 & Cap84 --> CalcUtilization[Hitung Persentase Keterisian Palet = Total Qty Box / Max Capacity]
    CalcUtilization --> DisplayDetailModal[Tampilkan Modal Detail: Batch Box, Produk, Expired Date, dan Slot Bin]
    
    DisplayDetailModal --> AuditCheck{Kondisi Palet Memerlukan Tindakan?}
    AuditCheck -- Palet In Transit Menumpuk --> CreatePutawayTask[Terbitkan Tugas Put-away ke Operator]
    AuditCheck -- Palet Melebihi Kapasitas --> CreateSplitTask[Terbitkan Tugas Pemindahan Box Antar Palet]
    AuditCheck -- Sesuai Standar --> KeepMonitoring[Pertahankan Status Operasional Normal]

    CreatePutawayTask & CreateSplitTask & KeepMonitoring --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Buka menu **Manajemen Palet** pada bilah navigasi.
2. **Memeriksa Palet Tertahan di Area Transit:**
   - Klik filter **In Transit**. Supervisor dapat melihat daftar palet hasil penerimaan barang yang belum dinaikkan ke rak.
   - Periksa ketersediaan slot bin kosong untuk mengarahkan operator melakukan proses *put-away*.
3. **Inspeksi Muatan Multi-Expired Date:**
   - Klik tombol **Lihat Detail** pada nomor palet.
   - Jendela modal akan menampilkan seluruh box yang ada di atas palet tersebut, membedakan batch produksi dan tanggal kedaluwarsa masing-masing.
