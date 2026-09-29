# PANDUAN & PROMPT IMPLEMENTASI POLA MOBILE UX MASTER DATA

Dokumen ini berisi arsitektur, kode template acuan, serta **Prompt Siap Pakai** untuk mereplikasi pola antarmuka mobile modern (Swipe Gestures, FAB `+`, Modal Tambah Baru, dan Layering Z-Index) ke tab master data lainnya (misal: `offices`, `bins`, `uoms`, `products`).

---

## 1. Arsitektur Pola Mobile UX Master Data

Pola ini dibangun dengan 5 pilar utama:
1. **Swipeable Card Gestures**:
   - **Geser Kanan (Swipe Right)**: Memunculkan tray merah `bg-rose-600` berisi tombol **Hapus** (maksimal snap: `96px`).
   - **Geser Kiri (Swipe Left)**: Memunculkan tray biru & kuning berisi tombol **Detail** (`bg-sky-600`) dan **Edit** (`bg-amber-600`) (maksimal snap: `-144px`).
   - Dilengkapi proteksi deteksi scroll vertikal (`Math.abs(diffY) > Math.abs(diffX)`) agar scroll halaman tidak macet.
   - Tap kartu otomatis menutup kembali tray (`closeTray()`).
   - Teks panduan: `» Geser kanan: Hapus | Geser kiri: Detail & Edit «`.
2. **Form Desktop Disembunyikan di Mobile (`hidden lg:block`)**:
   - Form penambahan data di desktop (`lg:col-span-1`) diberi class `hidden lg:block` agar tidak memadati layar smartphone.
3. **Floating Action Button (FAB) `+`**:
   - Berada di posisi `fixed bottom-[114px] right-4 z-40 lg:hidden`.
   - **KRUSIAL**: Diletakkan **di luar `.glass-panel`** agar `position: fixed` terikat langsung ke viewport browser (tidak terjebak CSS stacking context panel).
4. **Modal Form "Tambah Baru" Modern & Ergonomis**:
   - Menggunakan header berikon visual, deskripsi singkat, tombol close `x`.
   - Input dengan icon-prefix visual dan tombol generator kode otomatis (`fa-wand-magic-sparkles`).
   - Select dropdown menggunakan custom native styling (`appearance-none` + chevron). **JANGAN** menggunakan TomSelect untuk modal agar tidak memicu bug double-border.
   - Grid 50:50 tombol aksi **Batal** dan **Simpan** yang besar dan ramah ibu jari.
5. **Layering Z-Index & Backdrop Viewport**:
   - Semua modal (Detail, Edit, Add) wajib menggunakan:
     `fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md`
   - **Alasan**:
     - `bg-black/75` memastikan latar belakang gelap pekat di light mode maupun dark mode (menghindari bug CSS override `bg-slate-950/80` yang menjadi putih di light mode).
     - Layer berada di `z-40` dengan padding `pt-16 pb-16`, sehingga **Header** (`sticky top-0 z-50`) dan **Bottom Navigation** (`fixed bottom-0 z-50`) tetap 100% tampak, tidak tertimpa dialog modal, dan konten halaman di bawahnya tertutup sempurna.

---

## 2. PROMPT SIAP PAKAI (Copy-Paste ke AI)

Gunakan prompt di bawah ini setiap kali ingin mengimplementasikan pola ke tab lain:

```markdown
Tolong implementasikan pola mobile UX yang sudah diterapkan pada tab `branches`, `warehouses`, dan `racks` ke tab berikut:
http://127.0.0.1:8000/master-data?tab=[NAMA_TAB]

Poin-poin spesifikasi yang harus diterapkan:
1. Mobile Card Gestures:
   - Ganti tampilan card mobile lama dengan swipeable card list menggunakan AlpineJS `swipeableCard()`.
   - Swipe kanan memunculkan tombol Hapus (bg-rose-600, limit 96px).
   - Swipe kiri memunculkan tombol Detail (bg-sky-600) dan Edit (bg-amber-600) (limit -144px).
   - Tap kartu menutup tray; sertakan deteksi scroll vertikal dan teks panduan:
     "» Geser kanan: Hapus | Geser kiri: Detail & Edit «".
   - Warna kontras tinggi (light/dark mode aware).

2. Hide Desktop Form on Mobile:
   - Tambahkan class `hidden lg:block` pada form penambahan data di sisi kanan tab.

3. Floating Action Button (FAB) `+` Mobile:
   - Tambahkan tombol melayang di posisi `fixed bottom-[114px] right-4 z-40 lg:hidden` (DI LUAR elemen .glass-panel).
   - Tombol memicu modal Tambah Baru `openAdd[NamaEntitas]Modal()`.

4. Modal Dialog Tambah Baru:
   - Buat modal `showAdd[NamaEntitas]Modal` di `resources/views/master/modals/[NAMA_TAB].blade.php`.
   - Backdrop wajib: `fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md`.
   - Card dialog: `w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-full overflow-y-auto`.
   - Field input dilengkapi icon prefix, tombol generator kode otomatis jika ada kode/ID, dropdown native (tanpa TomSelect), dan grid 50:50 tombol Batal & Simpan.
   - Pastikan modal Detail dan Edit juga menggunakan backdrop `fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md`.

5. Integrasi di master/index.blade.php:
   - Tambahkan state `showAdd[NamaEntitas]Modal: false` dan method `openAdd[NamaEntitas]Modal()` ke dalam `masterDataForm()`.
   - Daftarkan `window.openAdd[NamaEntitas]Modal`.
   - Pastikan modal baru ditutup otomatis pada saat `submitCreateForm()` berhasil.
   - JANGAN mendaftarkan select modal ke dalam `masterSelectIds` TomSelect.
```

---

## 3. Template Standar Komponen & Kode Acuan

### A. Template File Tab (`resources/views/master/tabs/{tab_name}.blade.php`)

```html
<!-- Kontainer Tab Utama -->
<div class="glass-panel p-4 sm:p-6 rounded-2xl space-y-6 bg-white dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
            <i class="fa-solid [ICON_TAB] text-indigo-600 dark:text-indigo-400"></i>
            <span>Daftar [NAMA_ENTITAS]</span>
        </h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            <!-- Filter Form ... -->

            <!-- Desktop Table View (hidden md:block) ... -->

            <!-- Mobile Swipeable Interactive Card List -->
            <div class="space-y-3 md:hidden">
                @forelse($items as $item)
                    @php
                        $itemData = [
                            'id' => $item->id,
                            'name' => $item->name,
                            // data lainnya...
                        ];
                    @endphp
                    
                    <div x-data="swipeableCard()" 
                         @touchstart="touchStart($event)"
                         @touchmove="touchMove($event)"
                         @touchend="touchEnd($event)"
                         @mousedown="mouseDown($event)"
                         @mousemove="mouseMove($event)"
                         @mouseup="mouseUp($event)"
                         @mouseleave="mouseUp($event)"
                         class="relative overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700/80 shadow-sm select-none">
                        
                        <!-- LEFT ACTION TRAY (Swipe Right: DELETE) -->
                        <div class="absolute inset-y-0 left-0 w-24 bg-rose-600 flex items-stretch z-0">
                            @canAccessModule('master', 'delete')
                            <form action="{{ route('master.[entity].destroy', $item->id) }}" method="POST" onsubmit="MasterDataApp.deleteItem(event, this)" class="w-full flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex flex-col items-center justify-center p-2 text-white hover:bg-rose-700 active:bg-rose-800 transition cursor-pointer" title="Hapus">
                                    <i class="fa-solid fa-trash-can text-lg mb-1"></i>
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase">Hapus</span>
                                </button>
                            </form>
                            @else
                            <div class="w-full flex flex-col items-center justify-center text-white/60 p-2">
                                <i class="fa-solid fa-lock text-base mb-1"></i>
                                <span class="text-[10px] font-bold">No Akses</span>
                            </div>
                            @endcanAccessModule
                        </div>

                        <!-- RIGHT ACTION TRAY (Swipe Left: DETAIL & EDIT) -->
                        <div class="absolute inset-y-0 right-0 w-36 flex items-stretch z-0">
                            <!-- Detail Button -->
                            <button type="button" 
                                    data-[entity]="{{ json_encode($itemData) }}" 
                                    onclick="window.openDetail[Entity]Modal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white transition p-2 cursor-pointer"
                                    title="Detail">
                                <i class="fa-solid fa-eye text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Detail</span>
                            </button>

                            <!-- Edit Button -->
                            @canAccessModule('master', 'update')
                            <button type="button" 
                                    data-[entity]="{{ json_encode($itemData) }}" 
                                    onclick="window.openEdit[Entity]Modal(this)" 
                                    @click="closeTray()"
                                    class="flex-1 flex flex-col items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white transition p-2 cursor-pointer"
                                    title="Edit">
                                <i class="fa-solid fa-pen-to-square text-base mb-1"></i>
                                <span class="text-[10px] font-extrabold tracking-wider uppercase">Edit</span>
                            </button>
                            @endcanAccessModule
                        </div>

                        <!-- FOREGROUND CARD -->
                        <div class="relative z-10 bg-white dark:bg-slate-800/95 p-4 transition-transform ease-out cursor-grab active:cursor-grabbing space-y-2.5 text-xs"
                             :class="{ 'duration-200': !isDragging }"
                             :style="'transform: translateX(' + offset + 'px)'"
                             @click="if (offset !== 0) { closeTray(); $event.stopPropagation(); }">
                            
                            <!-- Badges Header -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/70 border border-indigo-200 dark:border-indigo-800/70 text-indigo-700 dark:text-indigo-400 font-mono font-extrabold rounded-lg text-xs flex items-center gap-1.5 shadow-xs">
                                    <i class="fa-solid [ICON_BADGE] text-[11px]"></i>
                                    <span>{{ $item->id }}</span>
                                </span>
                            </div>

                            <!-- Title -->
                            <h4 class="text-base font-extrabold text-slate-900 dark:text-white tracking-tight">{{ $item->name }}</h4>

                            <!-- Swipe Hints -->
                            <div class="flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 pt-1.5 border-t border-slate-100 dark:border-slate-700/60">
                                <span class="flex items-center gap-1 font-semibold text-rose-600/80 dark:text-rose-400/80">
                                    <i class="fa-solid fa-angles-right text-[9px]"></i> Geser kanan: Hapus
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-sky-600/80 dark:text-sky-400/80">
                                    Geser kiri: Detail & Edit <i class="fa-solid fa-angles-left text-[9px]"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-800">Tidak ada data.</div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        </div>

        <!-- Form Tambah Desktop (hidden lg:block) -->
        @canAccessModule('master', 'create')
        <div class="hidden lg:block bg-white dark:bg-slate-800/60 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 h-fit space-y-4 shadow-sm">
            <!-- Form Desktop Content -->
        </div>
        @endcanAccessModule
    </div>
</div>

<!-- FLOATING ACTION BUTTON (Diletakkan DI LUAR .glass-panel) -->
@canAccessModule('master', 'create')
<div class="fixed bottom-[114px] right-4 z-40 lg:hidden">
    <button type="button" 
            @click="openAdd[Entity]Modal()" 
            onclick="window.openAdd[Entity]Modal()"
            class="w-13 h-13 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-2xl shadow-indigo-600/50 flex items-center justify-center text-2xl active:scale-90 transition-transform cursor-pointer border-2 border-white dark:border-slate-800"
            title="Tambah [Entity] Baru">
        <i class="fa-solid fa-plus"></i>
    </button>
</div>
@endcanAccessModule

<!-- Helper Script swipeableCard (Guarded) -->
<script>
    if (typeof window.swipeableCard !== 'function') {
        window.swipeableCard = function() {
            return {
                startX: 0,
                startY: 0,
                offset: 0,
                isDragging: false,
                isScrolling: false,
                maxLeftSwipe: -144,
                maxRightSwipe: 96,
                touchStart(e) {
                    this.startX = e.touches[0].clientX;
                    this.startY = e.touches[0].clientY;
                    this.isDragging = true;
                    this.isScrolling = false;
                },
                touchMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.touches[0].clientX - this.startX;
                    const diffY = e.touches[0].clientY - this.startY;
                    if (!this.isScrolling && Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > 8) {
                        this.isScrolling = true;
                        this.isDragging = false;
                        return;
                    }
                    if (this.isScrolling) return;
                    if (Math.abs(diffX) > 10 && e.cancelable) {
                        e.preventDefault();
                    }
                    if (diffX > 0) {
                        this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                    } else {
                        this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                    }
                },
                touchEnd(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                mouseDown(e) {
                    if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
                    this.startX = e.clientX;
                    this.isDragging = true;
                },
                mouseMove(e) {
                    if (!this.isDragging) return;
                    const diffX = e.clientX - this.startX;
                    if (Math.abs(diffX) > 5) {
                        if (diffX > 0) {
                            this.offset = Math.min(diffX, this.maxRightSwipe + 15);
                        } else {
                            this.offset = Math.max(diffX, this.maxLeftSwipe - 15);
                        }
                    }
                },
                mouseUp(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;
                    if (this.offset > 40) {
                        this.offset = this.maxRightSwipe;
                    } else if (this.offset < -45) {
                        this.offset = this.maxLeftSwipe;
                    } else {
                        this.offset = 0;
                    }
                },
                closeTray() {
                    this.offset = 0;
                }
            };
        };
    }
</script>
```

---

### B. Template File Modal (`resources/views/master/modals/{tab_name}.blade.php`)

```html
<!-- MODAL TAMBAH BARU -->
<div x-show="showAdd[Entity]Modal" 
     class="fixed inset-0 z-40 pt-16 pb-16 px-4 flex items-center justify-center bg-black/75 backdrop-blur-md" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="showAdd[Entity]Modal = false"
     x-cloak>
    <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl p-5 sm:p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4 max-h-full overflow-y-auto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="showAdd[Entity]Modal = false">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-200 dark:border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg border border-indigo-100 dark:border-indigo-800/40 shadow-sm">
                    <i class="fa-solid [ICON_ENTITAS]"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">Tambah [Entity] Baru</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Daftarkan [entity] baru ke dalam sistem</p>
                </div>
            </div>
            <button type="button" @click="showAdd[Entity]Modal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('master.[entity].store') }}" method="POST" onsubmit="MasterDataApp.submitCreateForm(event, this)" class="space-y-4 text-xs">
            @csrf
            
            <!-- Contoh Field ID dengan Auto Generator -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold flex items-center justify-between">
                    <span>Kode [Entity] (ID) <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Format: CODE-XXX</span>
                </label>
                <div class="flex items-center space-x-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </div>
                        <input type="text" name="id" x-model="forms.[entity]_id" required 
                               class="w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold tracking-wider uppercase focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                               placeholder="misal: EX-001">
                    </div>
                    <button type="button" @click="auto[Entity]Code()" 
                            class="px-4 py-3 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-300 font-mono font-bold text-xs rounded-xl border border-indigo-200 dark:border-indigo-800/50 transition shadow-sm cursor-pointer active:scale-95 flex items-center space-x-1.5 shrink-0"
                            title="Generate Kode Otomatis">
                        <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                        <span>Auto</span>
                    </button>
                </div>
            </div>

            <!-- Contoh Field Nama -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 mb-1.5 font-bold">
                    Nama [Entity] <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-500">
                        <i class="fa-solid fa-tag text-sm"></i>
                    </div>
                    <input type="text" name="name" required 
                           class="w-full pl-10 pr-3 py-3 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-semibold focus:ring-2 focus:ring-indigo-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none shadow-sm transition" 
                           placeholder="Nama entitas">
                </div>
            </div>

            <!-- Action Buttons Grid (50:50) -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" @click="showAdd[Entity]Modal = false" 
                        class="w-full py-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer active:scale-95 text-center">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center space-x-1.5 active:scale-95 text-center">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span class="btn-text">Simpan [Entity]</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

---

### C. Integrasi di `resources/views/master/index.blade.php`

1. **State & Method AlpineJS**:
   ```javascript
   function masterDataForm(initialTab) {
       return {
           // ...
           showAdd[Entity]Modal: false,
           openAdd[Entity]Modal() {
               this.forms.[entity]_id = '';
               this.auto[Entity]Code(); // jika ada generator
               this.showAdd[Entity]Modal = true;
           },
           // ...
       }
   }
   ```

2. **Window Triggers**:
   ```javascript
   window.openAdd[Entity]Modal = function() {
       const alpine = getMasterAlpine();
       if (alpine) {
           alpine.openAdd[Entity]Modal();
       }
   };
   ```

3. **Auto Close pada `submitCreateForm`**:
   ```javascript
   if (masterAlpine) {
       masterAlpine.showAdd[Entity]Modal = false;
       // ...
   }
   ```

---

## 4. Daftar Periksa (Checklist) Sebelum Selesai

- [ ] Sintaks PHP file Blade valid (`php -l resources/views/...`).
- [ ] Tombol FAB berada **di luar `.glass-panel`** (`fixed bottom-[114px] right-4 z-40 lg:hidden`).
- [ ] Form desktop memiliki class `hidden lg:block`.
- [ ] Seluruh backdrop modal menggunakan `bg-black/75 backdrop-blur-md` dengan padding `pt-16 pb-16` dan z-index `z-40`.
- [ ] Select pada modal **TIDAK** didaftarkan ke TomSelect `masterSelectIds`.
- [ ] Menjalankan `php artisan view:clear` setelah perubahan.
