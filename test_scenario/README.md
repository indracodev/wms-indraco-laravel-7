# Panduan Pembuatan & Pengelolaan Skenario Pengetesan Web (Test Scenario)

Dokumen ini merupakan panduan standar untuk menyusun dan mendokumentasikan skenario pengetesan web (manual maupun otomatis) di lingkungan project **WMS Indraco**.

---

## 1. Konsep Utama

1. **1 Task Flow = 1 File Test (`.md`)**  
   Setiap alur tugas/proses bisnis spesifik memiliki satu file skenario pengujian mandiri dalam format Markdown (`.md`).
2. **Riwayat Eksekusi Berkelanjutan (Appended Log)**  
   Setiap kali pengujian dijalankan, file skenario yang bersangkutan **wajib ditambahkan (di-append)** riwayat pengujian terbarunya di bagian bawah dokumen tanpa menghapus riwayat pengujian sebelumnya. Dengan demikian, histori pengujian, perubahan status, dan tanggal pengetesan selalu tercatat rapi.

---

## 2. Struktur Folder

File skenario disimpan di dalam direktori `test_scenario/` dan dikelompokkan berdasarkan modul aplikasi:

```text
test_scenario/
├── README.md                      # Panduan standar dokumentasi & eksekusi pengujian
├── TEMPLATE_SCENARIO.md           # Template master untuk dicopy setiap membuat flow baru
├── 1_login_test.md                # [TS-AUTH-001] Otentikasi Login & Sesi
├── 2_kantor_pusat_test.md         # [TS-MST-001] Master Data Kantor Pusat (CRUD)
├── 3_plant_test.md                # [TS-MST-002] Master Data Plant / Cabang (CRUD)
├── 4_gudang_test.md               # [TS-MST-003] Master Data Gudang (CRUD)
├── 5_rak_test.md                  # [TS-MST-004] Master Data Rak (CRUD)
├── 6_bin_test.md                  # [TS-MST-005] Master Data Slot BIN (CRUD)
├── 7_uom_test.md                  # [TS-MST-006] Master Data Satuan UOM (CRUD)
├── 8_produk_test.md               # [TS-MST-007] Master Data Katalog Produk SKU (CRUD)
├── 9_palet_test.md                # [TS-MST-008] Master Data Palet Storage (CRUD)
└── 10_inbound_test.md             # [TS-INB-001] Transaksi Penerimaan Barang Masuk (Inbound V2)
```

> **Catatan:** Jika ada modul baru, buat folder baru dengan nama slug huruf kecil (`kebab-case`).

---

## 3. Aturan Penamaan File (Naming Convention)

Format nama file pengujian adalah:
```text
TS-[MODUL]-[NOMOR_FLOW]-[slug-nama-flow].md
```

| Bagian | Keterangan | Contoh |
| :--- | :--- | :--- |
| `TS` | Prefix singkatan dari *Test Scenario* | `TS` |
| `[MODUL]` | Kode singkatan modul (3–4 huruf kapital) | `AUTH`, `MST`, `INB`, `OUT`, `INV` |
| `[NOMOR_FLOW]` | 3 digit nomor urutan alur tugas | `001`, `002`, `010` |
| `[slug-nama-flow]` | Deskripsi singkat alur tugas (huruf kecil & tanda minus) | `login-valid`, `terima-barang-po` |

**Contoh Nama File:**
- `TS-AUTH-001-login-valid.md`
- `TS-INB-001-terima-barang-po.md`
- `TS-OUT-002-verifikasi-packing.md`

---

## 4. Anatomi Isi File Skenario Pengujian

Setiap file `.md` terdiri dari 4 bagian utama:

```
┌─────────────────────────────────────────────────────────┐
│ 1. Metadata Skenario & Prasyarat (Statis)               │
│    - ID, Modul, Flow Name, URL, Pre-requisite, Test Data│
├─────────────────────────────────────────────────────────┤
│ 2. Langkah-Langkah Pengujian Baku (Statis)              │
│    - Langkah kerja spesifik & hasil yang diharapkan     │
├─────────────────────────────────────────────────────────┤
│ 3. Tabel Ringkasan Riwayat Pengujian (Dinamis - Update) │
│    - Quick summary status & tanggal setiap run          │
├─────────────────────────────────────────────────────────┤
│ 4. Log Detail Riwayat Eksekusi (Dinamis - Append)       │
│    - Detail eksekusi Run #1, Run #2, dst di bagian akhir│
└─────────────────────────────────────────────────────────┘
```

---

## 5. Standar Nilai Status Pengujian

Saat menjalankan skenario, gunakan kode status berikut:

| Status | Label Markdown | Definisi |
| :--- | :--- | :--- |
| **PASSED** | `🟢 PASSED` | Seluruh langkah berhasil dijalankan dan hasil aktual sesuai dengan hasil yang diharapkan. |
| **FAILED** | `🔴 FAILED` | Ditemukan kesalahan, error sistem, atau hasil aktual tidak sesuai ekspektasi. |
| **BLOCKED** | `🟡 BLOCKED` | Pengujian tidak dapat dilanjutkan karena kendala teknis/prasyarat belum siap (misal server down, data belum ada). |
| **SKIPPED** | `⚪ SKIPPED` | Langkah atau skenario sengaja dilewati berdasarkan instruksi/alasan tertentu. |

---

## 6. Standar Format Tanggal & Waktu

Gunakan format standar berikut untuk menghindari kebingungan zona waktu:
- **Format:** `YYYY-MM-DD HH:mm:ss` (Contoh: `2026-11-23 08:30:00 WIB`)

---

## 7. Prosedur Eksekusi & Penambahan Status

Setiap kali Anda atau Agent melakukan pengetesan terhadap alur tugas tersebut:

1. **Buka file skenario** yang sesuai (misal `TS-AUTH-001-login.md`).
2. **Jalankan langkah-langkah pengetesan** dari Step 1 sampai selesai pada web browser / automation runner.
3. **Tambahkan baris ringkasan** pada tabel `## 3. Ringkasan Eksekusi`:
   ```markdown
   | 2 | 2026-11-23 09:15 | Staging | 🟢 PASSED | Tester B | Verifikasi setelah fix bug token |
   ```
4. **Append (tambahkan di paling bawah file)** detail eksekusi lengkap menggunakan format blok `### Run #[N] - [Tanggal] [Waktu] - [Status]`:
   - Catat status tiap step (`PASS` / `FAIL`).
   - Sertakan catatan atau URL screenshot/error log bila terjadi kegagalan (`FAIL`).

---

## 8. Template Standar Skenario (Contoh Lengkap)

Gunakan file [`TEMPLATE_SCENARIO.md`](file:///c:/laragon/www/#Project2026/wms-indraco/test_scenario/TEMPLATE_SCENARIO.md) sebagai referensi langsung, atau gunakan format di bawah ini saat membuat file baru:

```markdown
# Test Scenario: TS-[MODUL]-[NO] - [Nama Flow]

## 1. Informasi Umum
- **ID Skenario**: TS-[MODUL]-[NO]
- **Modul**: [Nama Modul, contoh: Inbound / Authentication]
- **Task Flow**: [Nama Alur Tugas, contoh: Penerimaan Barang dari PO Supplier]
- **Tingkat Prioritas**: High / Medium / Low
- **Target URL**: `http://localhost/wms-indraco/public/...`
- **Role Akun Penguji**: [Contoh: Admin Gudang / Operator]
- **Prasyarat (Pre-requisites)**:
  1. Akun penguji aktif dan memiliki hak akses ke modul terkait.
  2. Data PO sudah berstatus "Approved" di sistem.
- **Test Data**:
  - Username: `admin_gudang`
  - Nomor PO: `PO-202611-001`
  - SKU Barang: `BRG-001`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Tindakan (Action) | Input / Data | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- |
| 1 | Buka halaman modul | Klik menu Inbound > Penerimaan | Halaman daftar penerimaan terbuka dengan benar |
| 2 | Cari dokumen PO | Input nomor PO pada kolom pencarian | Dokumen PO ditemukan dan status sesuai |
| 3 | Input jumlah barang diterima | Masukkan Qty = 100 | Sistem memvalidasi Qty tidak melebihi pesanan |
| 4 | Klik tombol Simpan | Klik "Simpan Penerimaan" | Notifikasi sukses muncul dan status PO berubah |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:30 WIB | Localhost | 🔴 FAILED | Budi | Tombol simpan tidak merespon saat diklik |
| 2 | 2026-11-23 10:15 WIB | Localhost | 🟢 PASSED | Budi | Re-test setelah fix controller, penerimaan sukses |

---

## 4. Detail Riwayat Eksekusi

<!-- SETIAP KALI MENJALANKAN TEST, TAMBAHKAN BLOK RUN BARU DI BAWAH INI -->

### Run #1 - 2026-11-23 08:30 WIB - 🔴 FAILED
- **Environment**: Local (PHP 8.x / MySQL)
- **Penguji / Runner**: Budi (QA)
- **Browser / Tool**: Chrome v130 (Desktop)
- **Status Akhir**: 🔴 FAILED

#### Hasil Per Langkah:
| Step | Status | Hasil Aktual (Actual Result) | Catatan / Bukti |
| :---: | :---: | :--- | :--- |
| 1 | 🟢 PASS | Halaman terbuka sesuai URL | - |
| 2 | 🟢 PASS | PO ditemukan | - |
| 3 | 🟢 PASS | Input Qty valid | - |
| 4 | 🔴 FAIL | Tombol simpan unclickable, error console 500 | `POST /inbound/store 500 Internal Server Error` |

---

### Run #2 - 2026-11-23 10:15 WIB - 🟢 PASSED
- **Environment**: Local (PHP 8.x / MySQL)
- **Penguji / Runner**: Budi (QA)
- **Browser / Tool**: Chrome v130 (Desktop)
- **Status Akhir**: 🟢 PASSED

#### Hasil Per Langkah:
| Step | Status | Hasil Aktual (Actual Result) | Catatan / Bukti |
| :---: | :---: | :--- | :--- |
| 1 | 🟢 PASS | Halaman terbuka lancar | - |
| 2 | 🟢 PASS | PO ditemukan | - |
| 3 | 🟢 PASS | Input Qty diterima | - |
| 4 | 🟢 PASS | Notifikasi "Penerimaan Berhasil Disimpan" muncul | Data tersimpan di DB tabel `inbound_receipts` |
```
