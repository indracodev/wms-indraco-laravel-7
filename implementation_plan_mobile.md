# Rencana Implementasi Adaptasi Layout Mobile (Mobile-Responsive WMS Indraco)

Dokumen ini berisi panduan komprehensif, arsitektur antarmuka pengguna (UI/UX), spesifikasi komponen responsive, serta **tahapan eksekusi** untuk mengadaptasi seluruh tampilan aplikasi WMS Indraco agar optimal dan nyaman digunakan pada perangkat *mobile* (smartphone & handheld barcode scanner PDA).

---

## 1. Ringkasan Kebutuhan Adaptasi Mobile

### A. Tujuan Utama
1. **Pengalaman Pengguna Handheld / Mobile (PDA Ready)**:
   - Memastikan Operator dan Supervisor Gudang dapat melakukan scan, transaksi Inbound/Outbound/Transfer, dan pengecekan lokasi stok dengan nyaman menggunakan satu tangan (one-thumb operation).
2. **Transformasi Tabel Data menjadi Mobile Card List (Flat Single-Level Card)**:
   - Seluruh tabel data (`<table>...</table>`) pada tampilan desktop dikonversi menjadi **Card List** yang responsif pada layar perangkat mobile (`md:hidden`), dengan aturan ketat: **TIDAK ADA CARD DI DALAM CARD (Flat Card Structure)**.
3. **Bottom Navigation Bar Terpaku (Fixed Bottom Nav)**:
   - Menambahkan navigasi bawah terikat (*fixed bottom navigation*) untuk perangkat layar kecil (`md:hidden`) agar akses ke menu utama (Dashboard, Locator, Inbound, Outbound, Transfer, Scanner) selalu berada di jangkauan ibu jari.

---

## 2. Spesifikasi Arsitektur Layout Mobile

### A. Layout Global & Container Adjustment (`resources/views/layouts/app.blade.php`)
- **Bottom Padding Safe Area**:
  Main container diberi margin/padding bawah (`pb-24 md:pb-8`) agar konten halaman bagian paling bawah tidak tertutup oleh Fixed Bottom Navigation Bar.
- **Top Bar & Drawer Adaptation**:
  Sidebar desktop dapat diciptakan sebagai drawer tersembunyi (*slide-over*) atau dinavigasikan sepenuhnya via Bottom Bar & Header Menu.

### B. Fixed Bottom Navigation Bar (`resources/views/layouts/bottom_nav.blade.php`)
Navigasi melayang di bagian bawah layar (`fixed bottom-0 inset-x-0 z-50 bg-slate-900/95 backdrop-blur-md border-t border-slate-800 md:hidden`):

```html
<nav class="fixed bottom-0 inset-x-0 bg-slate-900/95 backdrop-blur-lg border-t border-slate-800/80 z-50 md:hidden px-2 py-1.5 flex items-center justify-around shadow-2xl">
    <!-- Home / Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('dashboard') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-chart-pie text-lg mb-0.5"></i>
        <span>Home</span>
    </a>

    <!-- Stock Locator -->
    @canAccessModule('detail', 'read')
    <a href="{{ route('detail.index') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('detail.*') ? 'text-indigo-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-magnifying-glass-location text-lg mb-0.5"></i>
        <span>Locator</span>
    </a>
    @endcanAccessModule

    <!-- Inbound Putaway -->
    @canAccessModule('inbound', 'read')
    <a href="{{ route('transaction.inbound.form') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.inbound*') ? 'text-emerald-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-truck-ramp-box text-lg mb-0.5"></i>
        <span>Inbound</span>
    </a>
    @endcanAccessModule

    <!-- Outbound Picking -->
    @canAccessModule('outbound', 'read')
    <a href="{{ route('transaction.outbound.form') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.outbound*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-dolly text-lg mb-0.5"></i>
        <span>Outbound</span>
    </a>
    @endcanAccessModule

    <!-- Transfer BIN -->
    @canAccessModule('transfer', 'read')
    <a href="{{ route('transaction.transfer.form') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('transaction.transfer*') ? 'text-sky-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-arrows-up-down-left-right text-lg mb-0.5"></i>
        <span>Transfer</span>
    </a>
    @endcanAccessModule

    <!-- Scanner Simulator -->
    <a href="{{ route('scanner.index') }}" class="flex flex-col items-center py-1 px-2 rounded-xl text-[10px] font-medium transition {{ request()->routeIs('scanner.*') ? 'text-purple-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
        <i class="fa-solid fa-barcode text-lg mb-0.5"></i>
        <span>Scan</span>
    </a>
</nav>
```

---

## 3. Strategi Konversi Tabel Data ke Mobile Card List

### Aturan Utama Struktur Card List:
> [!IMPORTANT]
> **TIDAK ADA CARD DI DALAM CARD (Flat Card Structure)**
> - Elemen pembungkus luar pada tampilan mobile tidak berupa card tebal bertingkat, melainkan daftar item vertikal sederhana (`space-y-3`).
> - Setiap baris data direpresentasikan oleh **satu buah Card Tunggal** berbatas jelas (`bg-slate-800/80 border border-slate-700/70 p-4 rounded-xl`), di mana isi di dalamnya menggunakan teks berlapis (*key-value pairs* & *badge*) tanpa pembungkus card tambahan.

---

### A. Pemetaan Komponen View yang Disesuaikan

#### 1. **Dashboard Page** (`resources/views/dashboard/index.blade.php`)
- **Desktop**: Tabel "Produk Mendekati Expired" dan List Log Transaksi.
- **Mobile Adaption**:
  - Tabel Expired dikonversi menjadi Card List produk expired tunggal (SKU, Nama, Qty, BIN, Badge Sisa Hari).
  - KPI summary diubah menjadi 2-kolom grid yang ringkas.

#### 2. **Stock Locator Page** (`resources/views/detail/index.blade.php`)
- **Desktop**: Datatable Stok Box (`<table>`).
- **Mobile Adaption**:
  - Filter form diubah menjadi panel collapsible atau stacked input.
  - Setiap record Box ditampilkan sebagai Card Stok Tunggal:
    - **Header Card**: Barcode Box ID (font mono bold) + Status BIN Badge (misal: `B.10.2`).
    - **Body**: Nama Produk, SKU, Qty Karton (font besar terperinci), Tanggal Expired, & Kode Mesin.
    - **Footer**: Quick action button jika dibutuhkan.

#### 3. **Master Data Page** (`resources/views/master/index.blade.php`)
- **Desktop**: Tabel Kantor, Cabang, Gudang, Rak, BIN, dan Produk SKU.
- **Mobile Adaption**:
  - Navigasi tab dibuat berupa *horizontal scrollable pills*.
  - Setiap data Master (Kantor/Cabang/Gudang/Rak/BIN/Produk) direpresentasikan oleh Card List datar dengan ID menonjol, nama, dan detail atribut.

#### 4. **User Management Page** (`resources/views/users/index.blade.php`)
- **Desktop**: Datatable User (`<table>`).
- **Mobile Adaption**:
  - Setiap User direpresentasikan oleh Card User Tunggal:
    - Avatar inisial, Nama Lengkap & Username (`@username`).
    - Badge Role (`Super Admin` / `Supervisor` / `Operator`) & Shift.
    - Tombol "Login As" (Impersonasi) ukuran besar yang mudah ditekan jempol (*touch target* minimum 44px).

#### 5. **Matriks RBAC Page** (`resources/views/rbac/index.blade.php`)
- **Desktop**: Grid Checkbox Matrix Tabel.
- **Mobile Adaption**:
  - Dikonversi menjadi *Accordion per Role* atau *Card Modul per Role*.
  - Setiap modul berisi pill checkbox toggle (`Read`, `Create`, `Update`, `Delete`) yang luas dan responsif terhadap sentuhan jari.

#### 6. **Audit Logs Page** (`resources/views/logs/index.blade.php`)
- **Desktop**: Datatable Logs (`<table>`).
- **Mobile Adaption**:
  - Dikonversi menjadi *Timeline Card List* vertikal dengan garis/badge status mutasi (`IN` / `OUT` / `MOVE`), PIC User, dan timestamp relatif.

#### 7. **Form Transaksi** (Inbound, Outbound, Transfer)
- Input field dibuat full width dengan ukuran font 16px (mencegah *auto-zoom* browser pada iOS/Android).
- Tombol submit ditempatkan di posisi bawah yang mudah dijangkau.

---

## 4. Tahapan Eksekusi Implementation Roadmap

```
      ROADMAP IMPLEMENTASI MOBILE-RESPONSIVE WMS INDRACO
┌────────────────────────────────────────────────────────┐
│ TAHAP 1: Inisialisasi Bottom Navigation & Layout Base  │
├────────────────────────────────────────────────────────┤
│ TAHAP 2: Adaptasi Dashboard & Expiring Stock Cards     │
├────────────────────────────────────────────────────────┤
│ TAHAP 3: Adaptasi Stock Locator Card List              │
├────────────────────────────────────────────────────────┤
│ TAHAP 4: Adaptasi Master Data Cards                    │
├────────────────────────────────────────────────────────┤
│ TAHAP 5: Adaptasi User Management & Impersonation Cards│
├────────────────────────────────────────────────────────┤
│ TAHAP 6: Adaptasi RBAC Permission Mobile View          │
├────────────────────────────────────────────────────────┤
│ TAHAP 7: Adaptasi Audit Log Timeline Cards             │
├────────────────────────────────────────────────────────┤
│ TAHAP 8: Mobile Touch Optimization & Verification      │
└────────────────────────────────────────────────────────┘
```

### Rincian Langkah Eksekusi:

1. **Tahap 1: Inisialisasi Bottom Navigation & Layout Base**
   - Buat `resources/views/layouts/bottom_nav.blade.php`.
   - Sertakan `bottom_nav` pada `resources/views/layouts/app.blade.php`.
   - Tambahkan padding bawah `pb-24 md:pb-8` pada `<main>`.

2. **Tahap 2: Adaptasi Dashboard Mobile**
   - Tambahkan blok `md:hidden` Card List untuk stok mendekati expired di `resources/views/dashboard/index.blade.php`.
   - Sembunyikan tabel desktop di mobile dengan kelas `hidden md:table`.

3. **Tahap 3: Adaptasi Stock Locator Card List**
   - Tambahkan blok Card List untuk data stok box di `resources/views/detail/index.blade.php`.
   - Pastikan struktur card datar (tidak ada card di dalam card).

4. **Tahap 4: Adaptasi Master Data Cards**
   - Tambahkan Card List untuk 6 tab Master Data di `resources/views/master/index.blade.php`.

5. **Tahap 5: Adaptasi User Management & Impersonate Cards**
   - Tambahkan Card List User dengan tombol "Login As" berukuran touch-friendly di `resources/views/users/index.blade.php`.

6. **Tahap 6: Adaptasi RBAC Mobile View**
   - Tambahkan perizinan per modul berformat card/pill checkbox di `resources/views/rbac/index.blade.php`.

7. **Tahap 7: Adaptasi Audit Log Timeline Cards**
   - Tambahkan Card List Timeline Log di `resources/views/logs/index.blade.php`.

8. **Tahap 8: Verifikasi & Smoke Testing View Perangkat Mobile**
   - Lakukan uji tampilan pada resolusi layar mobile (360px - 430px).

---

## 5. Rencana Verifikasi (Verification Plan)

### Manual & Responsive Verification
- [ ] **Uji Tampilan Layout Mobile (Viewport <= 768px)**:
  Buka aplikasi di layar mobile/PDA. Memastikan Fixed Bottom Navigation muncul dan tidak menutupi tombol atau konten bawah.
- [ ] **Uji Flat Card Structure**:
  Memastikan seluruh baris data di halaman Stock Locator, Master Data, Users, dan Logs tampil dalam bentuk Flat Card tanpa ada pembungkus card ganda di dalamnya.
- [ ] **Uji Navigasi Bottom Bar**:
  Menekan item menu Home, Locator, Inbound, Outbound, Transfer, dan Scan pada Bottom Navigation. Memastikan item aktif ter-highlight dan route berhasil dimuat.
- [ ] **Uji Form Transaksi Mobile**:
  Mengisi form Inbound, Outbound, dan Transfer pada viewport mobile. Memastikan tombol submit mudah ditekan dan input select berjalan lancar.
