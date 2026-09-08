# Rencana Implementasi Alur Transaksi Inbound (Putaway & Registration Flow)

Dokumen ini berisi spesifikasi kebutuhan, alur kerja interaktif (*interactive step-by-step scan & confirm workflow*), perancangan antarmuka pengguna (UI/UX), serta **rencana eksekusi** untuk memperbarui halaman **Inbound Putaway** (`http://127.0.0.1:8000/transaction/inbound`).

---

## 1. Ringkasan Alur Kerja Baru Inbound (Putaway & Box Registration)

Halaman Inbound dipisah menjadi **2 Tab Mode Utama** yang intuitif bagi operator gudang dan PDA handheld scanner:

```
            ALUR TRANSAKSI INBOUND PUTAWAY & REGISTRASI BOX
┌─────────────────────────────────────────────────────────────────────────┐
│ TAB 1: PENEMPATAN PALET KE BIN (Pallet to BIN Putaway)                  │
│   Step 1: Scan Barcode BIN ──> Step 2: Scan Barcode Palet               │
│                            └──> Step 3: Modal Konfirmasi (Ya / Batal)   │
├─────────────────────────────────────────────────────────────────────────┤
│ TAB 2: PENDATAAN BOX KE PALET (Box to Pallet Registration)             │
│   Step 1: Scan Barcode Palet ──> Step 2: Scan Barcode Box & Detail     │
│                              └──> Step 3: Modal Konfirmasi (Ya / Batal) │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Rincian Skenario Alur Kerja (Workflow Specifications)

### 🔹 ALUR 1: Penempatan Data Barang / Palet pada Slot BIN

1. **Langkah 1: Scan Barcode BIN (`bin_id`)**:
   - Operator melakukan scan/input barcode BIN (misal: `B.10.2`).
   - System melakukan validasi otomatis:
     - Memeriksa keberadaan slot BIN di database.
     - Memeriksa kapasitas terisi (`max_pallet = 2`).
     - **Pencegahan Error**: Jika BIN sudah penuh (2/2 Palet), sistem langsung menampilkan pesan peringatan: `"BIN B.10.2 SUDAH PENUH (2/2 Palet). Silakan pilih BIN lain!"` dan memblokir langkah berikutnya.
     - Jika BIN valid dan belum penuh, sistem mengunci identitas BIN dan otomatis memindahkan kursor/fokus ke **Langkah 2**.

2. **Langkah 2: Scan Barcode Palet (`pallet_id`)**:
   - Operator melakukan scan/input barcode Palet (misal: `PLT-001`).
   - Operator memilih jenis palet (*Wooden*, *Loscam*, *Plastic*).
   - System secara otomatis memicu **Modal Konfirmasi Interaktif**.

3. **Langkah 3: Dialog Konfirmasi (Modal Ya / Batal)**:
   - System menampilkan modal popup konfirmasi:
     > 📦 **Konfirmasi Penempatan Palet ke BIN**
     > Apakah Anda yakin ingin memasukkan **Palet PLT-001** ke dalam **BIN B.10.2**?
     > *(Informasi BIN B.10.2 saat ini terisi: 1/2 Palet)*
     >
     > **[ Ya, Masukkan Palet ]** &nbsp;&nbsp;&nbsp;&nbsp; **[ Batal ]**

   - **Tindakan User**:
     - **Jika Pilih "Ya"**: Sistem mengirim data ke backend, mendaftarkan `pallet.bin_id = B.10.2`, mencatat log mutasi `status = 'In'`, dan menampilkan alert sukses: `"Palet PLT-001 Berhasil Didaftarkan ke BIN B.10.2!"`.
     - **Jika Pilih "Batal"**: Proses dibatalkan, form dibersihkan, dan tidak ada data yang diubah di database.

---

### 🔹 ALUR 2: Pendataan Box Karton pada Palet

1. **Langkah 1: Scan Barcode Palet (`pallet_id`)**:
   - Operator melakukan scan/input barcode Palet target (misal: `PLT-001`).
   - System memverifikasi keberadaan palet dan menampilkan status lokasi BIN palet tersebut (misal: `Berada di BIN B.10.2`).
   - Kursor/fokus otomatis berpindah ke **Langkah 2**.

2. **Langkah 2: Scan Barcode Box & Isi Detail (`box_id` & Produk)**:
   - Operator melakukan scan/input barcode Box (misal: `BOX-202607-001`).
   - Operator mengisi/memilih SKU Produk (misal: `PRD-99815`), Qty Karton (misal: `20`), Tanggal Expired, dan Kode Mesin.
   - Tombol / pemicu konfirmasi ditekan.

3. **Langkah 3: Dialog Konfirmasi (Modal Ya / Batal)**:
   - System menampilkan modal popup konfirmasi:
     > 🏷️ **Konfirmasi Pendataan Box ke Palet**
     > Apakah Anda yakin ingin mendaftarkan **Box BOX-202607-001** (Produk: *Kopi Kapal Api 25g*, Qty: *20 Karton*) ke dalam **Palet PLT-001** (BIN: *B.10.2*)?
     >
     > **[ Ya, Daftarkan Box ]** &nbsp;&nbsp;&nbsp;&nbsp; **[ Batal ]**

   - **Tindakan User**:
     - **Jika Pilih "Ya"**: Sistem menyimpan record Box baru terikat pada Palet `PLT-001`, mencatat log mutasi transaksi, dan menampilkan alert sukses: `"Box BOX-202607-001 Berhasil Didaftarkan ke Palet PLT-001!"`.
     - **Jika Pilih "Batal"**: Proses dibatalkan, form input box dibersihkan, dan tidak ada data yang disimpan.

---

## 3. Rincian Perubahan Kode & File Project

### A. Routing & Controller (`app/Http/Controllers/TransactionController.php`)
- Tambahkan handler endpoint backend terpisah untuk 2 skenario transaksi:
  1. `inboundPalletToBin(Request $request)` &rarr; Mendaftarkan Palet ke BIN.
  2. `inboundBoxToPallet(Request $request)` &rarr; Mendaftarkan Box ke Palet.

### B. Warehouse Transaction Service (`app/Services/WarehouseTransactionService.php`)
- Tambahkan metode `assignPalletToBin($binId, $palletId, $palletTypeId, $userId)` dengan validasi kapasitas BIN max 2 palet & penguncian transaksi (`lockForUpdate()`).
- Tambahkan metode `attachBoxToPallet($palletId, $boxData, $userId)` untuk mencatat stok box pada palet terdaftar.

### C. Tampilan Blade UI (`resources/views/transactions/inbound.blade.php`)
- Redesain penuh tampilan Inbound menggunakan **Alpine.js Stepper & Tab Mode**:
  - Tab 1: Penempatan Palet ke BIN (Scan BIN &rarr; Scan Palet &rarr; Modal Konfirmasi Ya/Batal).
  - Tab 2: Pendataan Box ke Palet (Scan Palet &rarr; Scan Box & Detail &rarr; Modal Konfirmasi Ya/Batal).
  - Modal Pop-up Konfirmasi Ya/Batal yang jernih, kontras, dan ramah sentuhan layar mobile/PDA scanner.

---

## 4. Tahapan Eksekusi Implementation Roadmap

```
           ROADMAP IMPLEMENTASI ALUR INBOUND SCAN & CONFIRM
┌──────────────────────────────────────────────────────────────────┐
│ TAHAP 1: Update Service Layer (`WarehouseTransactionService.php`)│
├──────────────────────────────────────────────────────────────────┤
│ TAHAP 2: Update Controller Layer (`TransactionController.php`)   │
├──────────────────────────────────────────────────────────────────┤
│ TAHAP 3: Update Routing (`routes/web.php`)                        │
├──────────────────────────────────────────────────────────────────┤
│ TAHAP 4: Redesain View Inbound (`transactions/inbound.blade.php`)│
├──────────────────────────────────────────────────────────────────┤
│ TAHAP 5: Automated Testing (PHPUnit Feature Tests)               │
├──────────────────────────────────────────────────────────────────┤
│ TAHAP 6: Verification & Manual Smoke Testing                     │
└──────────────────────────────────────────────────────────────────┘
```

---

## 5. Rencana Verifikasi (Verification Plan)

### Automated Tests (PHPUnit)
- [ ] `test_inbound_pallet_to_bin_confirmation_flow`: Menguji pendaftaran palet ke BIN via endpoint baru.
- [ ] `test_inbound_box_to_pallet_confirmation_flow`: Menguji pendaftaran box ke palet terdaftar.

### Manual Verification
- [ ] Buka `http://127.0.0.1:8000/transaction/inbound`.
- [ ] **Uji Alur 1 (Palet ke BIN)**:
  - Input/scan BIN `B.10.2` &rarr; Input/scan Palet `PLT-TEST-1`.
  - Pastikan modal konfirmasi muncul menanyakan "Apakah Anda yakin ingin memasukkan Palet PLT-TEST-1 ke BIN B.10.2?".
  - Tekan **Batal**: Pastikan data tidak berubah dan form reset.
  - Ulangi dan tekan **Ya**: Pastikan Palet terdaftar di BIN `B.10.2`.
- [ ] **Uji Alur 2 (Box ke Palet)**:
  - Input/scan Palet `PLT-TEST-1` &rarr; Input/scan Box `BOX-TEST-1`, SKU, Qty.
  - Pastikan modal konfirmasi muncul.
  - Tekan **Batal**: Pastikan data tidak tersimpan.
  - Ulangi dan tekan **Ya**: Pastikan Box terdaftar di Palet `PLT-TEST-1`.
