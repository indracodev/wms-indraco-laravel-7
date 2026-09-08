# Rencana Implementasi Redesain Light Mode (Light Theme Optimization WMS Indraco)

Dokumen ini berisi analisis masalah kontras, spesifikasi *Design System Token* untuk **Light Mode**, serta **rencana eksekusi** penyesuaian CSS & Blade views agar tampilan Light Mode memiliki kontras tinggi, warna harmonis, keterbacaan tajam, dan tetap menjaga kerapian Dark Mode yang sudah ada.

---

## 1. Analisis Masalah Tampilan Light Mode Saat Ini

### A. Masalah Utama Kontras & Harmonik Warna
1. **Tulisan Putih Terjebak pada Latar Terang**:
   - Banyak teks menggunakan utility class `text-white`, `text-slate-100`, dan `text-slate-200`. Pada Light Mode dengan latar belakang putih/terang, teks ini menjadi sangat samar (low contrast) atau tidak terlihat.
2. **Latar Belakang Card & Container Tidak Konsisten**:
   - Komponen card menggunakan `bg-slate-800` / `glass-panel` yang memiliki warna dasar gelap. Ketika beralih ke Light Mode, card yang tetap gelap terlihat kontras mati (*mismatched*) dengan latar body yang terang.
3. **Badge Status & Border Form**:
   - Badge warna (seperti `bg-amber-950 text-amber-300`, `bg-emerald-950 text-emerald-300`) dibuat khusus untuk tema gelap. Pada Light Mode, badge ini terlihat kusam dan terlalu kontras negatif.
   - Input form (`bg-slate-900 text-white`) pada Light Mode menjadi kotak-kotak hitam di atas background putih yang merusak estetika antarmuka.
4. **Header, Sidebar, & Bottom Navigation**:
   - Header & Bottom Navigation Bar masih mempertahankan warna `bg-slate-900/95` atau `bg-slate-800` saat beralih ke Light Mode.

---

## 2. Spesifikasi Design System Light Mode (CSS Tokens & Rules)

Untuk mengatasi seluruh masalah di atas secara menyeluruh tanpa merusak Dark Mode (yang sudah dinilai *proper*), kita akan memperkuat penanganan CSS khusus `html.light` di [`resources/views/layouts/app.blade.php`](file:///c:/laragon/www/WMS%20Indraco/resources/views/layouts/app.blade.php) menggunakan CSS Variable Override & Selective Scoped Rules.

### A. Skema Warna Light Mode
| Elemen UI | Dark Mode (Eksisting) | Light Mode (Rencana Baru) |
|---|---|---|
| **Body Background** | `#0f172a` (Slate 900) | `#f8fafc` (Slate 50) |
| **Card & Panel Background** | `#1e293b` (Slate 800) | `#ffffff` (Pure White) + Shadow `0 4px 20px -2px rgba(0,0,0,0.05)` |
| **Header & Sidebar** | `#0f172a` / `#1e293b` | `#ffffff` (White) / `#f1f5f9` (Slate 100) + Border `#e2e8f0` |
| **Text Primary (Judul/Nama)** | `#ffffff` / `#f8fafc` | `#0f172a` (Slate 900 - Kontras Tinggi) |
| **Text Secondary (Sub-detail)**| `#cbd5e1` (Slate 300) | `#334155` (Slate 700) |
| **Text Muted (Label/Hint)** | `#94a3b8` (Slate 400) | `#64748b` (Slate 500) |
| **Border Line** | `#334155` (Slate 700) | `#e2e8f0` (Slate 200) |
| **Form Input Background** | `#0f172a` (Slate 900) | `#ffffff` (White) + Border `#cbd5e1` + Text `#0f172a` |
| **Hover Row Table / Card** | `rgba(30, 41, 59, 0.5)` | `#f1f5f9` (Slate 100) |
| **Bottom Navigation Bar** | `bg-slate-900/95` | `bg-white/95 text-slate-700 border-slate-200` |

---

## 3. Strategi Implementasi CSS (`app.blade.php`)

Kita akan menambahkan blok CSS khusus `html.light` yang secara instan mentransformasikan seluruh komponen ke skema warna Light Mode ber-kontras tinggi:

```css
/* LIGHT MODE GLOBAL OVERRIDES */
html.light body {
    background-color: #f8fafc !important;
    color: #0f172a !important;
}

html.light .glass-panel,
html.light aside,
html.light header,
html.light nav,
html.light .bg-slate-800,
html.light .bg-slate-800\/80,
html.light .bg-slate-800\/60,
html.light .bg-slate-800\/40,
html.light .bg-slate-900 {
    background-color: #ffffff !important;
    border-color: #e2e8f0 !important;
    color: #0f172a !important;
    box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05);
}

html.light header {
    background-color: rgba(255, 255, 255, 0.9) !important;
    border-bottom-color: #e2e8f0 !important;
}

html.light aside {
    background-color: #ffffff !important;
    border-right-color: #e2e8f0 !important;
}

/* TEXT CONTRAST OVERRIDES IN LIGHT MODE */
html.light .text-white,
html.light .text-slate-100,
html.light .text-slate-200 {
    color: #0f172a !important;
}

html.light .text-slate-300,
html.light .text-slate-400 {
    color: #334155 !important;
}

html.light .text-slate-500 {
    color: #64748b !important;
}

/* FORM INPUTS IN LIGHT MODE */
html.light input,
html.light select,
html.light textarea {
    background-color: #ffffff !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

html.light input::placeholder,
html.light textarea::placeholder {
    color: #94a3b8 !important;
}

/* BADGES & ACCENTS IN LIGHT MODE */
html.light .bg-emerald-950 {
    background-color: #d1fae5 !important;
    color: #065f46 !important;
    border-color: #a7f3d0 !important;
}

html.light .bg-amber-950 {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fde68a !important;
}

html.light .bg-rose-950 {
    background-color: #ffe4e6 !important;
    color: #9f1239 !important;
    border-color: #fecdd3 !important;
}

html.light .bg-purple-950 {
    background-color: #f3e8ff !important;
    color: #6b21a8 !important;
    border-color: #e9d5ff !important;
}

html.light .bg-sky-950 {
    background-color: #e0f2fe !important;
    color: #075985 !important;
    border-color: #bae6fd !important;
}

/* TABLE HOVER IN LIGHT MODE */
html.light tr:hover {
    background-color: #f1f5f9 !important;
}

/* MOBILE BOTTOM NAV IN LIGHT MODE */
html.light nav.fixed.bottom-0 {
    background-color: rgba(255, 255, 255, 0.95) !important;
    border-top-color: #e2e8f0 !important;
}
```

---

## 4. Tahapan Eksekusi Implementation Roadmap

```
           ROADMAP REDESAIN LIGHT MODE WMS INDRACO
┌──────────────────────────────────────────────────────────────┐
│ TAHAP 1: Penyusunan Rules CSS Global Light Mode (`app.blade`)│
├──────────────────────────────────────────────────────────────┤
│ TAHAP 2: Adaptasi Header, Sidebar, & Bottom Navigation       │
├──────────────────────────────────────────────────────────────┤
│ TAHAP 3: Adaptasi Dashboard & Expiring Stock Light Mode      │
├──────────────────────────────────────────────────────────────┤
│ TAHAP 4: Adaptasi Stock Locator & Form Filtering             │
├──────────────────────────────────────────────────────────────┤
│ TAHAP 5: Adaptasi Master Data, User Management, & RBAC       │
├──────────────────────────────────────────────────────────────┤
│ TAHAP 6: Adaptasi Impersonation Banner & Audit Logs          │
├──────────────────────────────────────────────────────────────┤
│ TAHAP 7: Visual Verification & Smoke Testing (Light & Dark)  │
└──────────────────────────────────────────────────────────────┘
```

### Rincian Langkah Eksekusi:

1. **Tahap 1: Penyusunan Rules CSS Global Light Mode**
   - Tambahkan CSS overrides khusus `html.light` di tag `<style>` pada [`resources/views/layouts/app.blade.php`](file:///c:/laragon/www/WMS%20Indraco/resources/views/layouts/app.blade.php).

2. **Tahap 2: Adaptasi Header, Sidebar, & Bottom Nav**
   - Perbarui warna icon toggle sun/moon pada `header.blade.php` & `sidebar.blade.php`.
   - Pastikan navigasi aktif pada Bottom Navigation Bar (`bottom_nav.blade.php`) kontras dan terbaca jelas di Light Mode.

3. **Tahap 3: Adaptasi Dashboard Light Mode**
   - Pastikan KPI Cards, Grafik Chart.js, dan Card List Expiring Stock memiliki kontras tajam antara teks hitam slate dan latar putih.

4. **Tahap 4: Adaptasi Stock Locator & Form Transaksi**
   - Pastikan input form pencarian, select gudang/produk, dan badge BIN `B.10.2` pada halaman Stock Locator & Form Transaksi (Inbound, Outbound, Transfer) terlihat bersih dan profesional.

5. **Tahap 5: Adaptasi Master Data, User Management, & RBAC**
   - Pastikan tab pills Master Data, Card User, tombol "Login As", serta Matriks RBAC checkbox terlihat kontras.

6. **Tahap 6: Adaptasi Impersonation Banner & Audit Logs**
   - Pastikan banner impersonasi (kuning/amber) dan timeline log terlihat jelas di Light Mode.

7. **Tahap 7: Verifikasi Visual Duo-Theme**
   - Menguji peralihan antara **Dark Mode** dan **Light Mode** secara bolak-balik untuk memastikan kedua mode tampil sempurna.

---

## 5. Rencana Verifikasi (Verification Plan)

### Visual & Interactive Verification
- [ ] **Uji Toggle Theme**: Menekan tombol Sun/Moon di Header. Memastikan peralihan dari Dark ke Light dan sebaliknya berjalan tanpa flickering.
- [ ] **Uji Kontras Teks Light Mode**: Memastikan tidak ada teks putih di atas background terang di seluruh halaman.
- [ ] **Uji Form & Input Field**: Memastikan kotak input di Light Mode berlatar putih dengan border slate terang dan teks hitam yang jelas.
- [ ] **Uji Kerapian Dark Mode**: Memastikan Dark Mode tidak mengalami regresi atau perubahan warna yang merusak estetika yang sudah bagus.
