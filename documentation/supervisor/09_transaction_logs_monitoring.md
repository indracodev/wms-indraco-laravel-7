# Modul Pemantauan Log Transaksi Operasional (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Pemantauan Log Transaksi memungkinkan Supervisor Gudang untuk mengaudit seluruh arus mutasi barang masuk, keluar, dan pemindahan antar rak yang dieksekusi selama jam operasional gudang. Supervisor dapat memfilter transaksi berdasarkan nomor shift (Shift 1, 2, atau 3), nama staf pelaksana (PIC Operator), jenis transaksi, maupun rentang tanggal tertentu guna mengevaluasi produktivitas shift dan ketertiban pelaporan mutasi fisik barang.

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`, `permission:logs,read`
- **Fokus Pengawasan:** Memastikan tidak ada pergerakan barang fisik yang tidak tercatat di dalam sistem (*unauthorized stock movement*).

---

## 3. Komponen Teknis & Skema Database

### File Terkait:
- Controller: [LogController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/LogController.php) (Metode `index()`)
- Model: [TransactionLog](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Models/TransactionLog.php)
- Route: `GET /logs` (`name('logs.index')`)

### Parameter Mutasi yang Dipantau:
| Field Log | Makna Pengawasan |
| :--- | :--- |
| `transaction_date` | Waktu pencatatan mutasi di sistem |
| `pic_user_id` | Operator yang bertanggung jawab atas transaksi |
| `shift` | Shift operasional (Shift 1 / 2 / 3) |
| `product_id` | SKU barang yang dipindahkan |
| `qty_input` & `unit` | Kuantitas dan satuan yang diinput operator |
| `qty_base` & `uom_base` | Kuantitas baku dalam satuan terkecil |
| `status` | Jenis transaksi: `In`, `Out_Ekspedisi`, `Out_Transit`, `Transfer_Pallet` |
| `bin_id` & `pallet_id` | Slot rak dan palet yang terlibat |

---

## 4. Flowchart Alur Pemantauan Log Transaksi oleh Supervisor

```mermaid
flowchart TD
    Start([Supervisor Buka Menu Log Transaksi /logs]) --> LoadLogFilter[Tampilkan Halaman Log Transaksi dengan Filter Parameter]
    
    LoadLogFilter --> ApplySupervisorFilters[Supervisor Tentukan Kriteria Pemantauan:]
    ApplySupervisorFilters --> SetDate[Rentang Tanggal cth: Hari Ini / Minggu Ini]
    ApplySupervisorFilters --> SetShift[Filter Shift Kerja: Shift 1 / 2 / 3]
    ApplySupervisorFilters --> SetStatus[Filter Jenis Transaksi: In / Out_Ekspedisi / Out_Transit / Transfer_Pallet]
    ApplySupervisorFilters --> SetPIC[Filter Berdasarkan Operator Tertentu]

    SetDate & SetShift & SetStatus & SetPIC --> SubmitFilter[Kirim Request Pencarian ke LogController::index]
    
    SubmitFilter --> BuildQuery[Kueri TransactionLog dengan Eager Loading: user, product, bin.rack, pallet]
    BuildQuery --> RenderLogTable[Tampilkan Daftar Mutasi Terurut Tanggal Terbaru]
    
    RenderLogTable --> ReviewThroughput[Supervisor Analisis Produktivitas & Kepatuhan:]
    ReviewThroughput --> EvalInbound[Periksa Total Penerimaan Barang Masuk Hari Ini]
    ReviewThroughput --> EvalOutbound[Periksa Akurasi Pemotongan Stok Ekspedisi]
    ReviewThroughput --> EvalTransfer[Periksa Kewajaran Pemindahan Slot Bin]

    EvalInbound & EvalOutbound & EvalTransfer --> CheckAnomalies{Apakah Ditemukan Anomali / Jam Transaksi Ganjil?}
    CheckAnomalies -- Ada Kejanggalan --> QuestionOperator[Panggil Operator PIC Terkait untuk Konfirmasi Lapangan]
    CheckAnomalies -- Normal --> SignShiftHandover[Sahkan Laporan Serah Terima Shift Kerja]
    
    QuestionOperator & SignShiftHandover --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Akses menu **Audit & Laporan** > **Pusat Log Sistem**.
2. **Evaluasi Serah Terima Shift (Shift Handover):**
   - Sebelum pergantian shift, pilih nomor shift yang sedang berakhir pada filter **Shift**.
   - Periksa total transaksi barang masuk (`In`) dan barang keluar (`Out_Ekspedisi`).
   - Cocokkan jumlah surat jalan fisik dengan jumlah baris transaksi di sistem.
3. **Pemeriksaan Catatan Khusus (Notes):**
   - Periksa kolom **Catatan Transaksi** untuk melihat keterangan tambahan yang ditulis operator saat bertugas (seperti nomor batch, kondisi kemasan, atau alasan relokasi).
