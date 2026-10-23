# Modul Simulator Barcode Scanner (Super Admin)

## 1. Deskripsi Fitur
Modul Barcode Scanner Simulator menyediakan antarmuka emulasi perangkat keras pemindai (*handheld barcode/QR scanner*) berbasis web. Super Admin dapat memanfaatkan modul ini untuk menguji format barcode, memverifikasi keterbacaan kode inventaris lapangan, serta melakukan inspeksi instan (*instant barcode lookup*) terhadap tiga tipe objek pergudangan: **Palet**, **Produk/SKU**, dan **Slot Bin**.

---

## 2. Hak Akses & Role Permission
- **Role:** `super_admin` (Akses Penuh: Pengujian & Pemindaian Global)
- **Middleware:** `auth`
- **Route:**
  - `GET /scanner` (`name('scanner.index')`)
  - `POST /scanner/scan` (`name('scanner.scan')`)

---

## 3. Komponen Teknis & Logika Pattern Matching

### File Terkait:
- Controller: [ScannerController.php](file:///C:/laragon/www/%23Project2026/wms-indraco/app/Http/Controllers/ScannerController.php) (Metode `index()`, `scan()`)
- View: `resources/views/scanner/index.blade.php`

### Aturan Pola Pemindaian (Pattern Matching):
1. **Pola Palet (`PLT-` / `PLT`):**
   - Mendeteksi awalan kode string `PLT-`.
   - Mengambil relasi: `bin`, `type`, `boxes.product`.
   - Payload JSON mengembalikan: ID Palet, nama tipe, posisi Bin (atau Transit), status keaktifan, dan rincian box di dalamnya.
2. **Pola Produk / SKU (`PRD-` / `PRD`):**
   - Mendeteksi awalan kode string `PRD-`.
   - Menghitung akumulasi total stok seluruh gudang (`sum('qty')`).
   - Payload JSON mengembalikan: ID Produk, Nama, Satuan Dasar, Total Stok, dan peta lokasi seluruh palet & bin tempat produk tersebut tersimpan.
3. **Pola Slot Bin (Format Kode Rak, misal: `B.10.1`, `RK-A`, `C.1.2`):**
   - Mencari kecocokan ID pada tabel `bins`.
   - Payload JSON mengembalikan: Nama Gedung Gudang, Kapasitas Maksimal (`max_pallet`), Jumlah Palet Terisi, Status Keterisian Penuh (`is_full`), dan daftar palet aktif di slot tersebut.

---

## 4. Flowchart Alur Kerja Barcode Scanner Simulator

```mermaid
flowchart TD
    Start([Super Admin Buka Halaman Simulator /scanner]) --> DisplayInput[Tampilkan Bidang Input Pemindai Fokus Otomatis]
    DisplayInput --> ScanEvent[Super Admin Scan Barcode Fisik / Input Teks Manual]
    ScanEvent --> SendScanPost[Kirim POST Request ke /scanner/scan dengan Parameter 'code']
    
    SendScanPost --> ValidateEmpty{Apakah Kode String Kosong?}
    ValidateEmpty -- Ya --> ReturnErrorEmpty[Kembalikan Error HTTP 400: Kode Barcode Tidak Boleh Kosong]
    
    ValidateEmpty -- Ada Nilai --> PatternMatch{Deteksi Awalan Kode (Pattern Matching)}
    
    %% Cabang Pola Palet
    PatternMatch -- Awalan 'PLT-' --> QueryPallet[Kueri Pallet::with bin, type, boxes.product]
    QueryPallet --> CheckPalletFound{Palet Ditemukan di DB?}
    CheckPalletFound -- Ya --> ReturnPalletJson[Kembalikan JSON Type = 'pallet' dengan Info Posisi Bin & Daftar Box]
    CheckPalletFound -- Tidak --> PatternBinFallback

    %% Cabang Pola Produk
    PatternMatch -- Awalan 'PRD-' --> QueryProduct[Kueri Product::with boxes.pallet.bin]
    QueryProduct --> CheckProductFound{Produk Ditemukan di DB?}
    CheckProductFound -- Ya --> ReturnProductJson[Kembalikan JSON Type = 'product' dengan Total Stok & Peta Lokasi]
    CheckProductFound -- Tidak --> PatternBinFallback

    %% Cabang Pola Bin (Default Fallback)
    PatternMatch -- Format Lain / Bin Code --> PatternBinFallback[Kueri Bin::with rack.warehouse, pallets.boxes.product]
    PatternBinFallback --> CheckBinFound{Bin Ditemukan di DB?}
    CheckBinFound -- Ya --> ReturnBinJson[Kembalikan JSON Type = 'bin' dengan Status Full & Daftar Palet]
    CheckBinFound -- Tidak --> ReturnNotFound[Kembalikan JSON: Barcode Tidak Dikenali di Sistem WMS]

    ReturnPalletJson & ReturnProductJson & ReturnBinJson --> RenderScannerCard[Frontend Render Kartu Hasil Inspeksi Interaktif]
    ReturnErrorEmpty & ReturnNotFound --> RenderScannerAlert[Frontend Render Notifikasi Peringatan Merah]
    
    RenderScannerCard & RenderScannerAlert --> ResetInputFocus[Reset Bidang Input & Siap Pemindaian Berikutnya]
    ResetInputFocus --> End([Selesai])
```

---

## 5. Rincian Antarmuka & Langkah Operasional

1. Buka menu **Peralatan** > **Simulator Barcode Scanner**.
2. Pastikan kursor berada pada kolom teks pemindai (kolom memiliki fokus otomatis *autofocus*).
3. Arahkan *barcode scanner gun* fisik ke stiker barcode, atau ketikkan kode secara manual (contoh: `PLT-0005`, `PRD-KOPI-01`, atau `B.10.1`) lalu tekan **Enter**.
4. Antarmuka langsung menampilkan panel hasil inspeksi secara visual:
   - Jika memindai **Palet**: Menampilkan badge posisi bin, status kapasitas, dan tabel box batch yang sedang dibawanya.
   - Jika memindai **Produk**: Menampilkan rincian total stok di seluruh pabrik dan daftar nomor bin tempat stok dapat diambil.
   - Jika memindai **Slot Bin**: Menampilkan status keterisian (apakah slot kosong, terisi 1 palet, atau penuh 2 palet).
