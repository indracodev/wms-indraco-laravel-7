# Dokumentasi Fitur & Skenario Optimasi Sistem WMS Indraco

Dokumen ini mencatat seluruh arsitektur optimasi, strategi performa, mekanisme teknis (Query, Memory, Caching, Frontend), serta **skenario pengujian optimasi** yang diimplementasikan pada aplikasi **WMS Indraco**, khususnya pada modul transaksi barang masuk (**Inbound V2**), pengeluaran barang (**Outbound V2**), dan antarmuka global aplikasi.

---

## 1. Ringkasan Eksekutif & Hasil Tolok Ukur (Benchmark)

Sebelum optimasi dilakukan, modul transaksi mengalami hambatan performa akibat pemuatan data ribuan slot BIN dan palet secara langsung ke dalam DOM HTML, query database tanpa seleksi kolom (*N+1 query problem*), dan alokasi memori berlebih.

### Tabel Perbandingan Performa

| Parameter Uji | Sebelum Optimasi | Sesudah Optimasi | Peningkatan |
| :--- | :---: | :---: | :---: |
| **Response Time Outbound Form** | 12.271 ms (12,2 detik) | **0,9 ms – 5,3 ms** (Ter-cache)<br>~246 ms (Query dingin) | **> 98% Lebih Cepat** |
| **Response Time Inbound Form** | ~4.800 ms (4,8 detik) | **219 ms – 240 ms** | **> 95% Lebih Cepat** |
| **API Inventory Rak/BIN Scan** | ~2.500 ms | **1,0 ms – 1,4 ms** | **Instan (< 2 ms)** |
| **API Pencarian Slot BIN Modal** | Belum ada (Load 7.714 option) | **1,4 ms – 60 ms** | **Instan** |
| **Ukuran Payload HTML Outbound** | ~1,5 MB (1.520 KB) | **~147 KB** | **Hemat Bandwidth 90,3%** |
| **Memory Footprint PHP** | Lonjakan tinggi (*O(N)*) | Konstan (*O(1)* via Stream Generator) | **Stabil, Bebas Memory Leak** |
| **UX Visual Loading** | Blank / browser freeze | **Top Loading Progress Bar** realtime | **Feedback Interaktif** |

---

## 2. Arsitektur & Pilar Optimasi

### A. Database & Query Optimization (Eloquent ORM)

1. **Selective Eager Loading (`with(...)` & Column Projection)**
   * **Masalah**: Default Eloquent memuat `SELECT *` untuk semua relasi, menghasilkan transfer data besar dari database MySQL ke PHP.
   * **Solusi**: Hanya memilih kolom yang benar-benar ditampilkan oleh Blade/View.
   * **Implementasi**:
     * *Pallet*: `select('id', 'pallet_type_id', 'bin_id', 'status', 'max_capacity', 'is_active')`
     * *Active Boxes*: `select('id', 'pallet_id', 'product_id', 'qty', 'uom_base', 'expired_date', 'machine_code', 'notes')`
     * *Product & UOM*: `with('product:id,name,brand,uom_inventory')` dan `with('product.conversions:id,product_id,uom_convert,qty_convert,uom_base')`
     * *Slot BIN*: `select('id', 'rack_id')` dengan `with(['rack:id,warehouse_id', 'rack.warehouse:id,name'])`

2. **Lazy Eager Loading (`loadMissing(...)`)**
   * **Tujuan**: Memastikan relasi terisi tanpa memicu query ganda jika relasi sudah ada di dalam memori instans model.
   * **Implementasi**: Digunakan pada `getPalletInfo()`, pemrosesan koleksi BIN pada `outboundForm()`, dan pencarian slot BIN `searchBins()`.

3. **Lazy Collection Streaming (`cursor()` / PHP Generators)**
   * **Masalah**: Memanggil `->get()->map(...)` pada ribuan baris membuat PHP menginstansiasi ribuan objek Eloquent sekaligus, yang memakan alokasi RAM server secara drastis.
   * **Solusi**: Mengganti pemanggilan koleksi menjadi `->cursor()`.
   * **Mekanisme**: Memanfaatkan PHP Generator (`yield`) dan kursor database PDO berulang per baris, menjaga kompleksitas memori tetap konstan $O(1)$.

4. **Aggregate Count Direct Subquery (`withCount(...)`)**
   * Menghitung jumlah muatan palet dan box menggunakan `withCount(['pallets' => ...])` dan `withCount('activeBoxes')` langsung di tingkat SQL tanpa menarik baris detail child.

---

### B. Multi-Tier Caching Layer (Cache In-Memory)

Sistem menggunakan `Illuminate\Support\Facades\Cache` dengan strategi masa aktif (TTL) berjenjang:

| Kategori Data | Kunci Cache (Key) | Masa Aktif (TTL) | Alasan & Karakteristik |
| :--- | :--- | :---: | :--- |
| **Master Data Tipe Palet** | `inbound_pallet_types_cache` | 3.600 detik (1 Jam) | Jarang berubah, dibaca berulang |
| **Master Data Produk & Satuan** | `inbound_products_cache` | 3.600 detik (1 Jam) | Katalog produk statis |
| **Master Data Gudang** | `inbound_warehouses_cache` | 3.600 detik (1 Jam) | Struktur gudang statis |
| **Master Data Rak & BIN** | `inbound_racks_cache` | 3.600 detik (1 Jam) | Layout rak statis |
| **Ringkasan BIN Outbound** | `outbound_bins_data` | 300 detik (5 Menit) | Sample awal slot gudang |
| **Slot BIN Belum Penuh (Inbound)** | `inbound_bins_cache` | 300 detik (5 Menit) | Slot siap tampung inbound |
| **Daftar Palet Terdaftar (Inbound)** | `inbound_initial_pallets` | 60 detik | Palet aktif & transit |
| **Palet Khusus Putaway** | `inbound_putaway_pallets` | 30 detik | Palet transit berisi box |
| **Inventory per Rak/BIN** | `rack_inventory_{id}` | 60 detik | Mempercepat scan barcode berulang |
| **Pencarian Slot BIN Dinamis** | `bins_search_{hash}` | 60 detik | Pencarian modal instan |
| **Pencarian Palet Dinamis** | `pallets_search_{hash}` | 60 detik | Autocomplete palet instan |

#### Mekanisme Cache Invalidation (Pembersihan Otomatis)
Setiap kali terjadi aksi mutasi data transaksi (Inbound V2, Outbound V2, Putaway Palet, Relokasi/Transfer), cache terkait langsung dihapus secara instan (`Cache::forget(...)`):
* `outbound_bins_data`
* `inbound_bins_cache`
* `inbound_initial_pallets`
* `inbound_putaway_pallets`
* `rack_inventory_{id}` (spesifik pada ID rak/BIN yang dimutasi)

---

### C. Frontend & UI/UX Optimizations

1. **Modal Pencarian Dinamis (Lazy Search) menggantikan 7.700+ Option HTML**
   * **Masalah**: Dropdown HTML `<select>` biasa yang menampung 7.700 slot BIN membuat browser crash, freeze, dan menghasilkan 1,5 MB HTML transfer.
   * **Solusi**: Form Outbound diganti menggunakan tombol **"Cari BIN"** yang membuka modal pencarian interaktif berbasis AJAX dengan *live debounce* (300 ms). Halaman awal hanya memuat inisial data ringan (~147 KB).

2. **Global Top Loading Progress Bar**
   * Komponen progress bar terpasang di layout utama `resources/views/layouts/app.blade.php`.
   * **Otomasi Pelacakan**:
     * Navigasi Link biasa (`click <a>` non-hash).
     * Pengiriman formulir (`submit <form>`).
     * Permintaan latar belakang AJAX / Fetch (`window.fetch` & `XMLHttpRequest` interceptor).
   * Memberikan respons visual instan pada saat operator melakukan aksi di gudang.

3. **Debounced Search Input (300ms)**
   * Menahan eksekusi request pencarian ke server hingga operator berhenti mengetik selama 300ms, memangkas beban query hingga 80%.

---

## 3. Skenario Pengujian Optimasi (Test Scenarios)

Berikut adalah skenario pengetesan standar untuk memverifikasi performa dan stabilitas fitur optimasi.

---

### Skenario 1: Verifikasi Response Time & Caching Halaman Transaksi
* **Kode Skenario**: `OPT-SCN-001`
* **Target Endpoint**:
  * `GET http://127.0.0.1:8000/transaction/inbound`
  * `GET http://127.0.0.1:8000/transaction/outbound`
* **Tujuan**: Memastikan response time halaman berada di bawah batas toleransi performa (< 500 ms pada cold cache, < 50 ms pada warm cache).
* **Langkah Uji**:
  1. Bersihkan cache aplikasi melalui terminal: `php artisan cache:clear`.
  2. Buka URL `/transaction/outbound` pertama kali (Cold Cache) dan catat waktu response di Network Inspector browser.
  3. Muat ulang (refresh) halaman `/transaction/outbound` (Warm Cache) dan catat waktu responsnya.
  4. Lakukan langkah yang sama untuk `/transaction/inbound`.
* **Kriteria Keberhasilan (Pass)**:
  * Cold Cache Outbound < 500 ms (aktual: ~246 ms).
  * Warm Cache Outbound < 20 ms (aktual: ~1 ms - 5 ms).
  * Ukuran transfer dokumen HTML Outbound < 250 KB (aktual: ~147 KB).

---

### Skenario 2: Modal Pencarian Slot BIN Outbound (Lazy Search)
* **Kode Skenario**: `OPT-SCN-002`
* **Target Endpoint**: `GET /transaction/outbound/search-bins?q={query}&tab={tab}`
* **Tujuan**: Memastikan modal pencarian BIN mampu mencari ribuan data slot tanpa lag browser dan mengembalikan hasil akurat.
* **Langkah Uji**:
  1. Buka halaman `/transaction/outbound`.
  2. Klik tombol **"Cari BIN"**.
  3. Ketik kata kunci slot rak (misal: `B.10`).
  4. Periksa respons daftar slot BIN beserta status palet & total box-nya.
  5. Klik tab filter: **"Ada Stok"** dan **"Kosong"**.
  6. Pilih salah satu BIN dengan mengklik tombol **"Pilih"**.
* **Kriteria Keberhasilan (Pass)**:
  * Modal terbuka seketika tanpa jeda lag.
  * Hasil filter keluar secara dinamis (< 100 ms).
  * Slot BIN yang dipilih langsung terisi otomatis pada form langkah 1 (input barcode rak dan badge lokasi terpilih).

---

### Skenario 3: Scan Barcode Cepat & Cache Inventory Rak/BIN
* **Kode Skenario**: `OPT-SCN-003`
* **Target Endpoint**: `GET /transaction/inventory/{id}`
* **Tujuan**: Menguji kecepatan scanning barcode fisik rak/BIN dalam mengambil daftar palet FEFO tanpa membebani database.
* **Langkah Uji**:
  1. Pada halaman Outbound, input atau scan barcode slot BIN (misal: `B.10.1`).
  2. Tekan Enter atau klik trigger scan.
  3. Lakukan scan ulang slot yang sama dalam interval < 60 detik.
* **Kriteria Keberhasilan (Pass)**:
  * Request pertama memuat daftar produk dan palet dengan sorting FEFO (*First Expired, First Out*).
  * Request kedua dan seterusnya merespons secara instan (< 5 ms) karena dilayani oleh cache `rack_inventory_{id}`.

---

### Skenario 4: Konsistensi & Cache Invalidation saat Transaksi Mutasi
* **Kode Skenario**: `OPT-SCN-004`
* **Target Endpoint**:
  * `POST /transaction/inbound`
  * `POST /transaction/outbound`
* **Tujuan**: Memastikan data yang telah dicache segera diperbarui (di-invalidate) secara otomatis saat ada barang masuk atau keluar.
* **Langkah Uji**:
  1. Buka Outbound, lakukan pengecekan stok pada slot BIN target (misal stok box = 10).
  2. Lakukan transaksi Outbound sebesar 2 box pada slot tersebut.
  3. Segera periksa kembali stok slot BIN target melalui modal pencarian maupun scan inventory.
* **Kriteria Keberhasilan (Pass)**:
  * Cache lama otomatis terhapus (`Cache::forget`).
  * Stok yang ditampilkan langsung terbarui menjadi 8 box tanpa perlu menunggu TTL cache habis.

---

### Skenario 5: Integritas Eager Loading Kolom Spesifik & Mencegah N+1 Query
* **Kode Skenario**: `OPT-SCN-005`
* **Target File**: `app/Http/Controllers/TransactionController.php`
* **Tujuan**: Memastikan seluruh query model tidak mengeksekusi query relasi berulang di dalam perulangan (*loop*).
* **Langkah Uji**:
  1. Aktifkan Laravel Query Listener / Debugbar.
  2. Eksekusi metode `inboundForm()`, `outboundForm()`, dan `getRackInventory()`.
  3. Periksa jumlah query SQL yang dihasilkan.
* **Kriteria Keberhasilan (Pass)**:
  * Jumlah query SQL tetap konstan (antara 3 hingga 6 query tunggal), tidak bertambah secara proporsional terhadap jumlah item/palet yang ada.
  * Query tidak memuat kolom yang tidak digunakan (*No unnecessary columns*).

---

### Skenario 6: Indikator Top Loading Progress Bar
* **Kode Skenario**: `OPT-SCN-006`
* **Target Elemen**: `#top-progress-bar` di `resources/views/layouts/app.blade.php`
* **Tujuan**: Memastikan indikator progres visual berjalan mulus saat operator menavigasi aplikasi atau mengeksekusi request.
* **Langkah Uji**:
  1. Buka halaman apa saja dalam aplikasi WMS Indraco.
  2. Klik salah satu menu navigasi (misal: Inbound -> Outbound).
  3. Lakukan submit formulir transaksi barang masuk.
  4. Lakukan pengetikan pencarian pada modal pencarian BIN (AJAX request).
* **Kriteria Keberhasilan (Pass)**:
  * Garis biru gradien muncul seketika di bagian paling atas viewport layar dengan transisi CSS halus.
  * Bar memanjang dari 0% hingga 100% dan memudar (*fade out*) saat halaman/request selesai dimuat.
  * Tidak ada error console pada JavaScript.

---

## 4. Panduan Pemeliharaan & Troubleshooting

1. **Membersihkan Cache Secara Manual**
   Jika terdapat perubahan data master langsung di basis data (di luar aplikasi), bersihkan cache via CLI:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

2. **Memverifikasi Versi Framework & Eloquent Generator**
   Proyek ini menggunakan **Laravel Framework 7.29**. Untuk fitur pemrosesan koleksi berbasis generator lazy streaming, selalu gunakan:
   ```php
   // Gunakan cursor() untuk streaming LazyCollection pada Laravel 7
   $collection = Model::select(...)->with(...)->cursor();
   ```

3. **Menambahkan Eager Loading Baru**
   Saat menambahkan relasi baru di controller, selalu sertakan foreign key dan primary key induk:
   ```php
   // Contoh yang benar:
   Pallet::with(['activeBoxes:id,pallet_id,product_id,qty']);
   // Jika 'pallet_id' tidak disertakan, relasi hasMany tidak dapat dipetakan oleh Eloquent!
   ```

---

*Dokumentasi ini dimutakhirkan secara berkala seiring dengan penambahan fitur dan penyempurnaan performa sistem WMS Indraco.*
