# Modul Alat Audit Barcode Scanner (Supervisor Gudang)

## 1. Deskripsi Fitur
Modul Alat Audit Barcode Scanner digunakan oleh Supervisor Gudang saat melakukan inspeksi keliling (*floor walk rounds*) menggunakan perangkat genggam (*tablet* atau *handheld mobile computer*). Dengan memindai stiker barcode fisik di lapangan, supervisor dapat secara instan melihat isi muatan palet, status kapasitas slot rak, atau persebaran stok SKU produk tertentu tanpa perlu membuka komputer desktop.

---

## 2. Hak Akses & Role Permission
- **Role:** `supervisor` (Supervisor Gudang)
- **Middleware:** `auth`
- **Fungsi Utama:** Verifikasi barcode fisik di lantai gudang, validasi label palet, dan inspeksi langsung saat proses loading/unloading.

---

## 3. Komponen Teknis & Logika Pemindaian

### File Terkait:
- Controller: [ScannerController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/ScannerController.php) (Metode `index()`, `scan()`)
- Route:
  - `GET /scanner` (`name('scanner.index')`)
  - `POST /scanner/scan` (`name('scanner.scan')`)

### Logika Verifikasi Tiga Objek Fisik:
1. **Inspeksi Palet (`PLT-`):** Menampilkan rincian slot bin tempat palet seharusnya berada, tipe palet, dan seluruh daftar box di atasnya beserta tanggal kedaluwarsa.
2. **Inspeksi Slot Bin (Format Rak, cth: `B.10.1`):** Menampilkan status apakah slot kosong, terisi 1 palet, atau penuh 2 palet (`is_full`), memvalidasi bahwa tidak ada palet liar yang diletakkan tanpa tercatat.
3. **Inspeksi SKU Produk (`PRD-`):** Menampilkan total stok pabrik dan peta posisi palet serta bin di mana produk tersebut tersimpan.

---

## 4. Flowchart Alur Kerja Inspeksi Barcode oleh Supervisor

```mermaid
flowchart TD
    Start([Supervisor Memulai Inspeksi Keliling Gudang]) --> OpenScanner[Buka Modul Scanner di Tablet / Handheld /scanner]
    OpenScanner --> ScanPhysicalTag[Pindai Barcode pada Fisik Palet / Rak / Box Produk]
    
    ScanPhysicalTag --> SendPostScan[Kirim Kode String ke Controller /scanner/scan]
    SendPostScan --> IdentifyPattern{Identifikasi Pola Barcode:}
    
    %% Kasus Pemindaian Palet
    IdentifyPattern -- Awalan 'PLT-' (Palet) --> FetchPalletData[Ambil Data Palet & Seluruh Box Aktif]
    FetchPalletData --> DisplayPalletCard[Tampilkan Kartu Palet: Lokasi Bin Terdaftar vs Lokasi Fisik Saat Ini]
    DisplayPalletCard --> CheckLocationMatch{Apakah Lokasi Fisik Cocok dengan Bin Terdaftar?}
    CheckLocationMatch -- Berbeda / Nyasar --> AlertMisplaced[Peringatan: Palet Berada di Luar Slot Rak Terdaftar]
    CheckLocationMatch -- Cocok --> ConfirmPalletValid[Status Valid: Palet Berada pada Posisi yang Tepat]

    %% Kasus Pemindaian Bin Rak
    IdentifyPattern -- Format Kode Bin (cth: B.10.1) --> FetchBinData[Ambil Data Bin, Max Pallet = 2, dan Palet Terdaftar]
    FetchBinData --> DisplayBinCard[Tampilkan Kartu Bin: Daftar Palet Aktif & Indikator Full]
    DisplayBinCard --> ComparePhysicalPallets{Jumlah Palet Fisik Cocok dengan Sistem?}
    ComparePhysicalPallets -- Selisih Fisik vs Sistem --> AlertBinDiscrepancy[Peringatan: Terjadi Ketidaksesuaian Jumlah Palet di Rak]
    ComparePhysicalPallets -- Cocok --> ConfirmBinValid[Status Valid: Slot Rak Akurat]

    %% Kasus Pemindaian Produk
    IdentifyPattern -- Awalan 'PRD-' (Produk) --> FetchProductData[Ambil Total Stok & Seluruh Titik Simpan Produk]
    FetchProductData --> DisplayProductCard[Tampilkan Kartu Produk: Stok Unit Dasar & Daftar Bin Tersedia]

    AlertMisplaced & AlertBinDiscrepancy --> TakeCorrectiveAction[Supervisor Terbitkan Perintah Koreksi ke Operator]
    ConfirmPalletValid & ConfirmBinValid & DisplayProductCard --> ContinueRounds[Lanjutkan Inspeksi ke Lorong Rak Berikutnya]
    
    TakeCorrectiveAction & ContinueRounds --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Supervisi

1. Buka menu **Peralatan** > **Simulator Barcode Scanner** pada perangkat genggam supervisor.
2. Arahkan sinar scanner ke label barcode palet (misal: `PLT-0015`).
3. Periksa informasi yang muncul di layar:
   - Jika kolom **Slot Bin** bertuliskan `B.10.2`, pastikan palet tersebut memang diletakkan di rak B level 10 kolom 2.
   - Periksa tanggal expired date box di layar dengan stiker label pada fisik kardus.
4. Jika ditemukan palet yang berada di slot yang salah, supervisor dapat langsung membuka menu Transfer untuk mengoreksi penempatan bin di sistem.
