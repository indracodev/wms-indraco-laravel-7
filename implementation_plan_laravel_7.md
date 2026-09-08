# Rencana Implementasi WMS Indraco berbasis Laravel 7
## Warehouse Management System (WMS) - Laravel 7 Production Ready Architecture

Dokumen ini berisi panduan komprehensif, arsitektur teknis, perancangan skema database Laravel, struktur Controller/Service, otorisasi RBAC, middleware impersonasi, serta **tahapan implementasi bertahap** untuk memindahkan prototype WMS Indraco ke dalam framework **Laravel 7**.

---

## 1. Ringkasan Eksekutif & Arsitektur Teknologi

### Stack Teknologi & Spesifikasi System
- **Framework Backend**: Laravel 7.x (Persyaratan PHP 7.2.5 - 7.4)
- **Database Engine**: MySQL / MariaDB (Support InnoDB, Foreign Key Strict, `lockForUpdate` Transaction)
- **Environment Server**: Laragon (Apache / Nginx + PHP 7.4 + MySQL)
- **Frontend Stack**:
  - **Blade Templating Engine** (Server-side rendering & komponen modular)
  - **Laravel Mix (Webpack)** untuk kompilasi asset
  - **Bootstrap 5.3** (Layout grid, struktur form, & komponen modal)
  - **TailwindCSS** (Utility class untuk styling *Glassmorphism*, theme toggle Light/Dark mode, & micro-animations)
  - **Alpine.js / Vanilla JavaScript** (Reactive state modal, pemindai barcode AJAX, & chart interaktif)
  - **Chart.js** (Visualisasi grafik okupansi gudang & statistik mutasi)
- **Security & Authorization**:
  - Custom RBAC Middleware & Blade Directives (`@canAccessModule`)
  - Impersonation Engine (`Session`-based Admin Login-As)
  - Audit Trail Eloquent Observer

---

## 2. Skema Database Laravel (Migrations & Eloquent Models)

Skema database disesuaikan dengan kebutuhan struktur hierarki lokasi 7-tingkat, pelacakan palet & box, serta log transaksi.

### A. Daftar Tabel & Migrasi (`database/migrations`)

```php
// 1. offices
Schema::create('offices', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., KTR-SBY
    $table->string('name', 100);
    $table->text('address')->nullable();
    $table->timestamps();
});

// 2. branches
Schema::create('branches', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., CBG-GSK
    $table->string('office_id', 50);
    $table->string('name', 100);
    $table->string('city', 100)->nullable();
    $table->timestamps();

    $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
});

// 3. warehouses
Schema::create('warehouses', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., GSK.678.I
    $table->string('branch_id', 50);
    $table->string('name', 100);
    $table->text('address')->nullable();
    $table->timestamps();

    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
});

// 4. racks
Schema::create('racks', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // Barcode Rak e.g., RK-A
    $table->string('warehouse_id', 50);
    $table->string('rack_name', 50); // e.g., B
    $table->timestamps();

    $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
});

// 5. bins (Slot Penyimpanan Rak)
Schema::create('bins', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., B.10.2 (Format: [Rak].[Kolom].[Tingkat/SAP])
    $table->string('rack_id', 50);
    $table->integer('column_no');
    $table->integer('level_no');
    $table->integer('max_pallet')->default(2); // Aturan Bisnis Maksimal 2 Palet per BIN
    $table->timestamps();

    $table->foreign('rack_id')->references('id')->on('racks')->onDelete('cascade');
});

// 6. pallet_types
Schema::create('pallet_types', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., loscam, kayu_125, plastik
    $table->string('name', 50);
    $table->timestamps();
});

// 7. pallets
Schema::create('pallets', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // Barcode Palet e.g., PLT-0001
    $table->string('pallet_type_id', 50);
    $table->string('bin_id', 50)->nullable(); // Nullable jika palet di area transit
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('pallet_type_id')->references('id')->on('pallet_types');
    $table->foreign('bin_id')->references('id')->on('bins')->onDelete('set null');
});

// 8. products
Schema::create('products', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // SKU e.g., PRD-99812
    $table->string('name', 150);
    $table->string('default_unit', 20); // Karton, Bag, Box, Pcs, Kg
    $table->timestamps();
});

// 9. boxes
Schema::create('boxes', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // Barcode Box e.g., BOX-0023
    $table->string('pallet_id', 50);
    $table->string('product_id', 50);
    $table->integer('qty');
    $table->date('expired_date');
    $table->string('machine_code', 50);
    $table->timestamps();

    $table->foreign('pallet_id')->references('id')->on('pallets')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('products');
});

// 10. roles
Schema::create('roles', function (Blueprint $table) {
    $table->string('id', 50)->primary(); // e.g., super_admin, supervisor, operator
    $table->string('name', 50);
    $table->text('description')->nullable();
    $table->timestamps();
});

// 11. role_permissions
Schema::create('role_permissions', function (Blueprint $table) {
    $table->id();
    $table->string('role_id', 50);
    $table->string('module', 50); // inbound, outbound, transfer, rbac, settings, logs, detail, locator
    $table->boolean('can_read')->default(false);
    $table->boolean('can_create')->default(false);
    $table->boolean('can_update')->default(false);
    $table->boolean('can_delete')->default(false);
    $table->timestamps();

    $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
    $table->unique(['role_id', 'module']);
});

// 12. users
Schema::create('users', function (Blueprint $table) {
    $table->string('id', 50)->primary();
    $table->string('name', 100);
    $table->string('username', 50)->unique();
    $table->string('password', 255);
    $table->string('role_id', 50);
    $table->integer('shift')->default(1);
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();

    $table->foreign('role_id')->references('id')->on('roles');
});

// 13. transaction_logs
Schema::create('transaction_logs', function (Blueprint $table) {
    $table->id();
    $table->string('pic_user_id', 50);
    $table->integer('shift');
    $table->timestamp('transaction_date')->useCurrent();
    $table->string('product_id', 50);
    $table->integer('qty');
    $table->string('unit', 20);
    $table->date('expired_date')->nullable();
    $table->string('machine_code', 50)->nullable();
    $table->string('bin_id', 50);
    $table->enum('status', ['In', 'Out']);
    $table->string('pallet_id', 50);
    $table->string('pallet_type_id', 50);
    $table->string('warehouse_id', 50);
    $table->timestamps();

    $table->foreign('pic_user_id')->references('id')->on('users');
    $table->foreign('product_id')->references('id')->on('products');
    $table->foreign('bin_id')->references('id')->on('bins');
    $table->foreign('pallet_id')->references('id')->on('pallets');
    $table->foreign('warehouse_id')->references('id')->on('warehouses');
});

// 14. impersonation_logs
Schema::create('impersonation_logs', function (Blueprint $table) {
    $table->id();
    $table->string('admin_user_id', 50);
    $table->string('target_user_id', 50);
    $table->timestamp('started_at')->useCurrent();
    $table->timestamp('ended_at')->nullable();

    $table->foreign('admin_user_id')->references('id')->on('users');
    $table->foreign('target_user_id')->references('id')->on('users');
});

// 15. audit_logs
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->timestamp('timestamp')->useCurrent();
    $table->string('user_id', 50);
    $table->string('module', 50);
    $table->string('action_type', 20); // CREATE, UPDATE, DELETE, MOVE, IMPERSONATE, LOGIN
    $table->text('description');
    $table->timestamps();
});
```

---

### B. Relasi Eloquent Models (`app/Models`)

1. **`Bin` Model**:
   ```php
   class Bin extends Model {
       public $incrementing = false; // Primary Key string (e.g., B.10.2)
       protected $keyType = 'string';

       public function rack() {
           return $this->belongsTo(Rack::class, 'rack_id');
       }

       public function pallets() {
           return $this->hasMany(Pallet::class, 'bin_id')->where('is_active', true);
       }

       public function isFull(): bool {
           return $this->pallets()->count() >= $this->max_pallet;
       }
   }
   ```

2. **`Pallet` Model**:
   ```php
   class Pallet extends Model {
       public $incrementing = false;
       protected $keyType = 'string';

       public function bin() {
           return $this->belongsTo(Bin::class, 'bin_id');
       }

       public function type() {
           return $this->belongsTo(PalletType::class, 'pallet_type_id');
       }

       public function boxes() {
           return $this->hasMany(Box::class, 'pallet_id');
       }
   }
   ```

3. **`User` Model & RBAC Integration**:
   ```php
   class User extends Authenticatable {
       public function role() {
           return $this->belongsTo(Role::class, 'role_id');
       }

       public function hasPermission(string $module, string $action): bool {
           if ($this->role_id === 'super_admin') return true;

           $perm = RolePermission::where('role_id', $this->role_id)
                       ->where('module', $module)
                       ->first();

           if (!$perm) return false;
           return (bool) $perm->{"can_{$action}"};
       }
   }
   ```

---

## 3. Logika Bisnis & Core Services Laravel

### A. Validation Kapasitas BIN (Maksimal 2 Palet per BIN)

Penguncian transaksi (*Database Lock*) dan validasi kapasitas 2 palet diimplementasikan pada Layer Service: `app/Services/WarehouseTransactionService.php`.

```php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Bin;
use App\Models\Pallet;
use App\Models\TransactionLog;
use Exception;

class WarehouseTransactionService
{
    /**
     * Memproses Barang Masuk (Inbound Putaway) dengan validasi kapasitas BIN
     */
    public function processInbound(array $data, string $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // Lock record BIN untuk mencegah race condition
            $bin = Bin::where('id', $data['bin_id'])->lockForUpdate()->firstOrFail();

            // Hitung palet aktif saat ini di BIN tersebut
            $currentPalletCount = Pallet::where('bin_id', $bin->id)
                                        ->where('is_active', true)
                                        ->count();

            // Cek apakah penambahan palet baru melampaui limit 2 palet
            $existingPallet = Pallet::find($data['pallet_id']);
            $isNewPalletInBin = !$existingPallet || $existingPallet->bin_id !== $bin->id;

            if ($isNewPalletInBin && $currentPalletCount >= $bin->max_pallet) {
                throw new Exception("Gagal: BIN {$bin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Maksimal {$bin->max_pallet} Palet.");
            }

            // Upsert Palet
            $pallet = Pallet::updateOrCreate(
                ['id' => $data['pallet_id']],
                [
                    'pallet_type_id' => $data['pallet_type_id'],
                    'bin_id' => $bin->id,
                    'is_active' => true
                ]
            );

            // Buat Box
            $box = $pallet->boxes()->create([
                'id' => 'BOX-' . strtoupper(uniqid()),
                'product_id' => $data['product_id'],
                'qty' => $data['qty'],
                'expired_date' => $data['expired_date'],
                'machine_code' => $data['machine_code'],
            ]);

            // Catat Log Transaksi Inbound
            TransactionLog::create([
                'pic_user_id' => $userId,
                'shift' => $data['shift'],
                'product_id' => $data['product_id'],
                'qty' => $data['qty'],
                'unit' => $data['unit'],
                'expired_date' => $data['expired_date'],
                'machine_code' => $data['machine_code'],
                'bin_id' => $bin->id,
                'status' => 'In',
                'pallet_id' => $pallet->id,
                'pallet_type_id' => $data['pallet_type_id'],
                'warehouse_id' => $data['warehouse_id'],
            ]);

            // Audit Log
            AuditLogger::log($userId, 'inbound', 'CREATE', "Barang Masuk: {$data['qty']} {$data['unit']} Produk {$data['product_id']} di BIN {$bin->id}");

            return $box;
        });
    }

    /**
     * Memproses Transfer / Pemindahan Palet antar BIN
     */
    public function processTransfer(string $palletId, string $targetBinId, string $userId)
    {
        return DB::transaction(function () use ($palletId, $targetBinId, $userId) {
            $targetBin = Bin::where('id', $targetBinId)->lockForUpdate()->firstOrFail();

            // Cek Kapasitas BIN Tujuan
            $currentPalletCount = Pallet::where('bin_id', $targetBin->id)
                                        ->where('is_active', true)
                                        ->count();

            if ($currentPalletCount >= $targetBin->max_pallet) {
                throw new Exception("Gagal: BIN Tujuan {$targetBin->id} sudah penuh ({$currentPalletCount} Palet terdaftar). Pemindahan dibatalkan.");
            }

            $pallet = Pallet::findOrFail($palletId);
            $originBinId = $pallet->bin_id;

            // Update Lokasi Palet
            $pallet->bin_id = $targetBin->id;
            $pallet->save();

            // Audit Log
            AuditLogger::log($userId, 'transfer', 'MOVE', "Memindahkan Palet {$palletId} dari BIN {$originBinId} ke BIN {$targetBin->id}");

            return $pallet;
        });
    }
}
```

---

### B. Middleware Otorisasi RBAC Granular (`app/Http/Middleware/CheckModulePermission.php`)

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'read')
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermission($module, $action)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Akses Ditolak: Role Anda ({$user->role->name}) tidak memiliki izin {$action} modul {$module}!"
                ], 403);
            }

            return redirect()->back()->with('error', "Akses Ditolak: Role Anda ({$user->role->name}) tidak memiliki izin {$action} modul {$module}!");
        }

        return $next($request);
    }
}
```

---

### C. Engine Impersonasi User (Login As & Revert)

Menggunakan Session Engine bawaan Laravel untuk menyimpan identitas Admin asli saat berpindah ke akun target.

`app/Services/ImpersonationService.php`:
```php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\ImpersonationLog;

class ImpersonationService
{
    public function impersonate(User $targetUser)
    {
        $admin = Auth::user();

        if ($admin->role_id !== 'super_admin') {
            throw new \Exception("Hanya Super Admin yang dapat menggunakan fitur Login As.");
        }

        // Simpan Admin ID asli di Session
        Session::put('impersonator_id', $admin->id);

        // Catat Log Impersonasi
        ImpersonationLog::create([
            'admin_user_id' => $admin->id,
            'target_user_id' => $targetUser->id,
            'started_at' => now(),
        ]);

        // Switch Sesi Login ke Target User
        Auth::login($targetUser);
    }

    public function revert()
    {
        if (!Session::has('impersonator_id')) {
            return;
        }

        $adminId = Session::get('impersonator_id');
        $admin = User::findOrFail($adminId);

        // Update Log Impersonasi Terakhir
        ImpersonationLog::where('admin_user_id', $adminId)
            ->whereNull('ended_at')
            ->update(['ended_at' => now()]);

        Session::forget('impersonator_id');
        Auth::login($admin);
    }
}
```

---

## 4. Arsitektur Routing & Controller Laravel 7

File `routes/web.php` mengelompokkan rute berdasarkan otorisasi middleware:

```php
Route::middleware(['auth'])->group(function () {
    // Dashboard (Multi-Role Render)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Impersonation Routes
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::post('/impersonate/revert', [ImpersonationController::class, 'revert'])->name('impersonate.revert');

    // Master Data Routes (Protected by RBAC)
    Route::middleware(['permission:master,read'])->group(function () {
        Route::get('/master-data', [MasterDataController::class, 'index'])->name('master.index');
        Route::post('/master-data/office', [OfficeController::class, 'store'])->middleware('permission:master,create');
        Route::post('/master-data/warehouse', [WarehouseController::class, 'store'])->middleware('permission:master,create');
        Route::post('/master-data/bin', [BinController::class, 'store'])->middleware('permission:master,create');
        Route::post('/master-data/product', [ProductController::class, 'store'])->middleware('permission:master,create');
    });

    // Detail Data & Inventory Locator
    Route::get('/detail-data', [DetailDataController::class, 'index'])->middleware('permission:detail,read')->name('detail.index');

    // Modul Transaksi (Inbound, Outbound, Transfer)
    Route::post('/transaction/inbound', [TransactionController::class, 'inbound'])->middleware('permission:inbound,create')->name('transaction.inbound');
    Route::post('/transaction/outbound', [TransactionController::class, 'outbound'])->middleware('permission:outbound,create')->name('transaction.outbound');
    Route::post('/transaction/transfer', [TransactionController::class, 'transfer'])->middleware('permission:transfer,create')->name('transaction.transfer');

    // User Management & RBAC
    Route::resource('users', UserController::class)->middleware('permission:users,read');
    Route::get('/rbac', [RbacController::class, 'index'])->middleware('permission:rbac,read')->name('rbac.index');
    Route::post('/rbac/update', [RbacController::class, 'update'])->middleware('permission:rbac,update')->name('rbac.update');

    // Scanner Simulator & Logs
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner/scan', [ScannerController::class, 'scan'])->name('scanner.scan');
    Route::get('/logs', [LogController::class, 'index'])->middleware('permission:logs,read')->name('logs.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings,read')->name('settings.index');
});
```

---

## 5. Pemetaan View Blade dari `desain_prototype`

Seluruh halaman prototype HTML pada folder `desain_prototype/` dikonversi menjadi template Blade bermodular tinggi:

```
resources/views/
├── layouts/
│   ├── app.blade.php                 # Core Layout Wrapper (Theme Toggle, Scripts)
│   ├── header.blade.php              # Header Navbar + User Switcher + Theme Switcher
│   ├── sidebar.blade.php             # Dynamic Sidebar Navigation (RBAC filtered)
│   └── impersonate_banner.blade.php  # Banner Melayang Login As (Conditional Render)
├── auth/
│   └── login.blade.php               # Halaman Login Glassmorphism
├── dashboard/
│   ├── index.blade.php               # Switch Dashboard berdasarkan Role
│   ├── admin.blade.php               # KPI cards, Global Occupancy Chart, Log Hub
│   ├── supervisor.blade.php          # Critical Bin Alert, Expired Alert Chart
│   └── operator.blade.php            # Mobile-optimized putaway & picking cards
├── master/
│   └── index.blade.php               # Tabs Kantor, Cabang, Gudang, Rak, BIN, Produk
├── detail/
│   └── index.blade.php               # Detail Stok per Palet & Box + Dynamic Locator
├── transactions/
│   ├── inbound.blade.php             # Form Barang Masuk + Validation Toast
│   ├── outbound.blade.php            # Form Barang Keluar
│   └── transfer.blade.php            # Form Pindah BIN
├── users/
│   └── index.blade.php               # User CRUD Table & Modals
├── rbac/
│   └── index.blade.php               # Matriks Permission Grid Checkboxes
├── scanner/
│   └── index.blade.php               # Barcode Scanner Simulator Panel
├── logs/
│   └── index.blade.php               # Datatable Audit Trail Global
└── profile/
    └── index.blade.php               # Personal Info & Activity History
```

---

## 6. Rencana Tahapan Eksekusi (Roadmap Implementation)

```
        ROADMAP IMPLEMENTASI WMS INDRACO LARAVEL 7
┌────────────────────────────────────────────────────────┐
│ TAHAP 1: Setup Framework & Laragon Environment         │
├────────────────────────────────────────────────────────┤
│ TAHAP 2: Database Migration, Seeders & Eloquent Models │
├────────────────────────────────────────────────────────┤
│ TAHAP 3: Autentikasi, RBAC & Impersonation Engine      │
├────────────────────────────────────────────────────────┤
│ TAHAP 4: Layouting Blade, Light/Dark Mode & Master Data│
├────────────────────────────────────────────────────────┤
│ TAHAP 5: Core Services Transaksi & Validation BIN (2)  │
├────────────────────────────────────────────────────────┤
│ TAHAP 6: Stock Locator & Barcode Scanner AJAX Engine   │
├────────────────────────────────────────────────────────┤
│ TAHAP 7: Dashboard Multi-Role & Audit Log Integration  │
├────────────────────────────────────────────────────────┤
│ TAHAP 8: End-to-End Testing & Verification Skenario    │
└────────────────────────────────────────────────────────┘
```

### rincian Tahap demi Tahap:

#### **Tahap 1: Setup Environment & Project Skeleton**
1. Install Laravel 7 via Composer di Laragon (`composer create-project --prefer-dist laravel/laravel:^7.0 wms-indraco`).
2. Konfigurasi `.env` untuk koneksi MySQL database `wms_indraco`.
3. Install frontend asset bundler via NPM (`laravel-mix`, `tailwindcss`, `bootstrap`, `alpinejs`, `chart.js`).
4. Konfigurasi `webpack.mix.js` untuk mengompilasi file `styles.css` dan `app.js`.

#### **Tahap 2: Database Layer (Migrations, Models, & Seeders)**
1. Buat seluruh file migrasi 15 tabel sesuai spesifikasi Bagian 2.
2. Buat Eloquent Models (`Office`, `Branch`, `Warehouse`, `Rack`, `Bin`, `PalletType`, `Pallet`, `Product`, `Box`, `Role`, `RolePermission`, `User`, `TransactionLog`, `ImpersonationLog`, `AuditLog`).
3. Buat Database Seeder (`DatabaseSeeder.php`, `RoleSeeder.php`, `UserSeeder.php`, `InitialMasterSeeder.php`) yang mengisi data awal persis seperti dataset pada `desain_prototype/app.js`.

#### **Tahap 3: Autentikasi, RBAC System, & Impersonation**
1. Implementasi `Auth::routes()`.
2. Buat Custom Blade Directives `@canAccessModule($module, $action)` pada `AppServiceProvider`.
3. Pasang middleware `CheckModulePermission` pada grup rute web.
4. Buat `ImpersonationService` dan komponen banner melayang `layouts/impersonate_banner.blade.php`.

#### **Tahap 4: Modularisasi Blade UI Layout & Master Data CRUD**
1. Integrasi layout `app.blade.php` dengan fitur Light/Dark Mode toggle (simpan preference di `localStorage`).
2. Pindahkan tampilan HTML dari `desain_prototype/master-data.html` ke `resources/views/master/index.blade.php`.
3. Buat AJAX DataTables / Livewire / Blade Partial untuk filter hierarkis (Kantor -> Cabang -> Gudang -> Rak -> BIN).

#### **Tahap 5: Core Services Transaksi & Proteksi Batas 2 Palet**
1. Implementasi `WarehouseTransactionService` dengan DB Transaction & `lockForUpdate()`.
2. Buat Form Inbound (`Barang Masuk`), Outbound (`Barang Keluar`), dan Transfer (`Move`).
3. Hubungkan validasi real-time via AJAX/Blade Request Validation: Tampilkan warning `⚠️ BIN X.Y.Z sudah penuh!` jika kapasitas BIN mencapai 2 palet.

#### **Tahap 6: Stock Locator, Rekap BIN Location & Scanner AJAX Engine**
1. Buat Query Agregasi Stok untuk menghitung `Qty In` - `Qty Out` = `Qty Akhir` per BIN.
2. Implementasi logika status BIN otomatis (`Kosong` vs `Terisi`).
3. Buat `ScannerController` yang menerima input barcode regex, mencocokkan pattern (`PLT-`, `PRD-`, `B.10.2`), dan mengembalikan response JSON lokasi barang.

#### **Tahap 7: Dashboard Multi-Role, Chart.js & Audit Trail**
1. Buat tampilan dashboard adaptif berdasarkan role user yang sedang login (`Super Admin`, `Supervisor`, `Operator Gudang`).
2. Hubungkan grafik Chart.js dengan data riil dari database (Okupansi Gudang & Sisa Masa Kedaluwarsa Produk).
3. Buat Eloquent Observer untuk mencatat setiap aktivitas penciptaan, perubahan, dan pemindahan data ke tabel `audit_logs`.

#### **Tahap 8: Smoke Testing, Verifikasi Bisnis, & Deployment**
1. Jalankan pengujian skenario bisnis 1 (Validasi Kapasitas 2 Palet per BIN).
2. Jalankan pengujian skenario bisnis 2 (Uji Hak Akses RBAC Granular per User).
3. Jalankan pengujian skenario bisnis 3 (Siklus Mutasi Barang Inbound -> Outbound -> Move).
4. Buat file dokumentasi petunjuk penggunaan aplikasi untuk pengguna akhir.

---

## 7. Rencana Verifikasi & Pembuktian Bisnis (Testing Plan)

Pengujian dilakukan untuk memastikan sistem Laravel 7 memenuhi 100% aturan bisnis yang tertera pada `README.md` dan `analisis.md`:

### A. Uji Kapasitas Maksimal BIN (2 Palet)
- **Tindakan**: Melakukan Inbound / Transfer palet ketiga ke BIN `C.1.1` (yang sudah terisi 2 palet).
- **Hasil yang Diharapkan**:
  - Controller melempar `ValidationException` / error JSON.
  - Tampilan UI memunculkan Toast Notifikasi Merah: `Gagal: BIN C.1.1 sudah penuh (2 Palet terdaftar). Pemindahan dibatalkan.`
  - Database menolak perubahan (Rollback Transaction) dan mencatat aktivitas kegagalan di `audit_logs`.

### B. Uji Otorisasi RBAC & Impersonasi
- **Tindakan**: Login sebagai `Gilang` (Operator Gudang), mencoba membuka URL `/rbac` atau `/logs`.
- **Hasil yang Diharapkan**: Middleware memblokir request, meredirect ke dashboard dengan notifikasi `Akses Ditolak`.
- **Tindakan 2**: Login sebagai Super Admin, mengeklik `Login As` ke akun `Gilang`.
- **Hasil yang Diharapkan**: Sistem menampilkan banner melayang oranye, mengubah view ke dashboard Operator, dan menyediakan tombol `Kembali ke Admin` yang dapat memulihkan sesi Admin secara instan.

### C. Uji Siklus Mutasi Barang (Inbound -> Transfer -> Outbound)
- **Tindakan**:
  1. Input Barang Masuk 10 Karton di BIN `B.10.2` (Palet `PLT-099`).
  2. Pindahkan `PLT-099` dari `B.10.2` ke `L.5.1`.
  3. Input Barang Keluar 10 Karton dari `PLT-099` di `L.5.1`.
- **Hasil yang Diharapkan**:
  - Stok akhir `PLT-099` menjadi 0.
  - Status BIN `L.5.1` otomatis kembali menjadi `Kosong`.
  - Seluruh rangkaian kronologis tercatat sempurna pada `transaction_logs` dan `audit_logs`.
