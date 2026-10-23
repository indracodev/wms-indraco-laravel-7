# Modul Dashboard Shift & Panduan Kerja (Operator Gudang)

## 1. Deskripsi Fitur
Dashboard Operator Gudang dirancang khusus untuk memfasilitasi kebutuhan kerja harian operator di lantai gudang. Antarmuka menyajikan pintasan cepat (*quick action shortcuts*) untuk memulai tugas pemindaian barang masuk (*Inbound*), pengambilan barang keluar (*Outbound Picking*), pemindahan antar rak (*Transfer*), serta alat pemindai barcode (*Scanner Simulator*), dilengkapi pemantauan transaksi terkini yang telah diselesaikan pada shift yang bersangkutan.

---

## 2. Hak Akses & Role Permission
- **Role:** `operator` (Operator Gudang)
- **Middleware:** `auth`
- **Fokus Pengguna:** Kemudahan navigasi layar sentuh (*touchscreen friendly*), kecepatan eksekusi tugas fisik, dan kepatuhan terhadap nomor shift kerja.

---

## 3. Komponen Teknis & Tampilan Antarmuka

### File Terkait:
- Controller: [DashboardController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/DashboardController.php)
- View: `resources/views/dashboard/index.blade.php`
- Route: `GET /` (`name('dashboard')`)

### Elemen Dashboard Operator:
1. **Identitas Pengguna & Shift Aktif:** Menampilkan nama operator yang sedang login beserta nomor shift kerja (Shift 1, 2, atau 3).
2. **Pintasan Cepat Operasional (Action Cards):**
   - **Tombol Penerimaan (Inbound):** Akses langsung ke formulir penerimaan barang masuk.
   - **Tombol Pengeluaran (Outbound):** Akses langsung ke proses picking pengiriman.
   - **Tombol Relokasi (Transfer):** Akses langsung ke pemindahan palet dan bin.
   - **Tombol Barcode Scanner:** Membuka simulator pemindai genggam.
3. **Ringkasan Riwayat Kerja Pribadi:** Menampilkan 10 transaksi terakhir yang dicatat dengan ID operator yang sedang login.

---

## 4. Flowchart Alur Kerja Shift Operator Gudang

```mermaid
flowchart TD
    Start([Operator Datang & Login ke WMS]) --> DisplayShiftInfo[Dashboard Menampilkan Identitas Staf & Shift Aktif]
    
    DisplayShiftInfo --> SelectShiftTask{Operator Memilih Tugas Kerja Lapangan:}
    
    SelectShiftTask -- 1. Truk Bongkar / Barang Datang --> ClickInbound[Klik Pintasan 'Penerimaan Inbound' /transaction/inbound]
    SelectShiftTask -- 2. Instruksi Muat Truk / Picking --> ClickOutbound[Klik Pintasan 'Pengeluaran Outbound' /transaction/outbound]
    SelectShiftTask -- 3. Penataan / Pemindahan Rak --> ClickTransfer[Klik Pintasan 'Transfer Relokasi' /transaction/transfer]
    SelectShiftTask -- 4. Pengecekan Barcode Lapangan --> ClickScanner[Klik Pintasan 'Barcode Scanner' /scanner]

    ClickInbound --> ExecInboundTask[Eksekusi Scan Palet, Scan SKU, dan Simpan ke Rak / Transit]
    ClickOutbound --> ExecOutboundTask[Eksekusi Scan Rak, Pilih Batch FEFO, dan Kurangi Stok]
    ClickTransfer --> ExecTransferTask[Eksekusi Pindah Palet ke Bin Kosong / Transfer Antar Palet]
    ClickScanner --> ExecScannerTask[Arahkan Pemindai ke Barcode untuk Cek Informasi]

    ExecInboundTask & ExecOutboundTask & ExecTransferTask & ExecScannerTask --> TaskComplete[Transaksi Berhasil Disimpan ke Sistem WMS]
    TaskComplete --> ReturnDashboard[Kembali ke Dashboard & Riwayat Transaksi Bertambah]
    ReturnDashboard --> End([Siap Melakukan Tugas Berikutnya])
```

---

## 5. Panduan Praktis Operator Lapangan

1. **Awal Pergantian Shift:**
   - Masuk menggunakan akun operator Anda.
   - Pastikan nomor shift yang tampil di sudut atas antarmuka sesuai dengan jadwal kerja Anda (Shift 1: Pagi, Shift 2: Siang/Sore, Shift 3: Malam).
2. **Menjalankan Pekerjaan:**
   - Gunakan tombol kartu besar (*Quick Action Cards*) di tengah layar untuk langsung menuju formulir kerja tanpa harus mencari menu di bilah navigasi samping.
3. **Memeriksa Riwayat Pekerjaan:**
   - Gulir ke bawah pada tabel riwayat transaksi untuk memastikan transaksi yang baru saja Anda simpan telah tercatat dengan status *Success*.
