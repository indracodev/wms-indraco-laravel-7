# Test Scenario: TS-AUTH-001 - Autentikasi Login Pengguna

## 1. Informasi Umum
- **ID Skenario**: TS-AUTH-001
- **File Test**: `1_login_test.md`
- **Modul**: Authentication (`AUTH`)
- **Task Flow**: Login Akun & Autentikasi Sesi (Happy Path, Validasi Error, Fitur Demo Kredensial, & Toggle Tema)
- **Tingkat Prioritas**: Critical (P0)
- **Target URL**: `http://localhost/wms-indraco/public/login` (atau `http://wms-indraco.test/login`)
- **Role Pengguna yang Diuji**:
  - Super Admin (`admin`)
  - Supervisor (`budi`)
  - Operator (`gilang`)
- **Prasyarat (Pre-requisites)**:
  1. Web server Apache & database MySQL pada Laragon sudah berjalan.
  2. Database `wms_indraco` sudah ter-migrate dan ter-seed (`php artisan db:seed`).
  3. Akun user uji berstatus aktif (`is_active = 1`).
  4. Penguji belum dalam keadaan login (jika sudah login, lakukan logout atau gunakan mode *Incognito / Private Window*).
- **Test Data**:
  - **Akun Valid 1 (Super Admin)**: `admin` / `admin123`
  - **Akun Valid 2 (Supervisor)**: `budi` / `budi123`
  - **Akun Valid 3 (Operator)**: `gilang` / `gilang123`
  - **Akun Invalid (Password Salah)**: `admin` / `wrongpassword`
  - **Akun Invalid (Username Tidak Ada)**: `user_fiktif` / `rahasia123`

---

## 2. Langkah-Langkah Pengujian Baku

| Step | Kasus Pengujian (Test Case) | Tindakan Pengujian (Action) | Data Input | Hasil yang Diharapkan (Expected Result) |
| :---: | :--- | :--- | :--- | :--- |
| **1.1** | Akses Halaman Login | Buka browser dan arahkan ke URL `/login` | - | Halaman login WMS Indraco berhasil dimuat, menampilkan logo, input form (Username, Password, Ingat saya), tombol demo kredensial, dan toggle tema. |
| **1.2** | Toggle Mode Tampilan | Klik tombol toggle tema (Light/Dark mode) di pojok kanan atas | Klik tombol | Tema halaman berubah seketika antara tema Dark (Deep Navy) dan Light (Soft Sky) tanpa reload halaman. |
| **1.3** | Validasi Input Kosong | Kosongkan field `username` & `password`, lalu klik tombol **"Masuk ke Dashboard"** | Username: `(kosong)`<br>Password: `(kosong)` | Form tidak ter-submit, browser memunculkan tooltip peringatan validasi *HTML5 required* pada field pertama yang kosong. |
| **1.4** | Login Gagal - Password Salah | Masukkan username terdaftar dengan password salah, lalu submit | Username: `admin`<br>Password: `wrongpassword` | Sistem menolak login, halaman reload/kembali dengan pesan error: *"Username atau password yang dimasukkan salah, atau akun Anda non-aktif."* Nilai username tetap terisi (old input). |
| **1.5** | Login Gagal - User Tidak Terdaftar | Masukkan username fiktif dengan sembarang password, lalu submit | Username: `user_fiktif`<br>Password: `rahasia123` | Sistem menampilkan pesan error validasi yang sama. |
| **1.6** | Auto-fill Demo Kredensial | Klik salah satu tombol Demo Credentials (misal: tombol `admin` Super Admin) | Klik kartu demo `admin` | Field `username` otomatis terisi `admin` dan field `password` terisi `admin123`. |
| **1.7** | Login Berhasil (Happy Path) & Remember Me | Centang checkbox *"Ingat saya di perangkat ini"*, lalu klik **"Masuk ke Dashboard"** | Username: `admin`<br>Password: `admin123`<br>Remember: `Checked` | Sistem berhasil mengautentikasi, session di-regenerate, audit log mencatat event `LOGIN`, dan user dialihkan (*redirect*) ke halaman Dashboard (`/`). |
| **1.8** | Proteksi Guest Route | Saat sudah login, coba akses kembali URL `/login` | Akses langsung `/login` | Sistem langsung me-redirect kembali ke halaman Dashboard (`/`) tanpa menampilkan form login. |

---

## 3. Ringkasan Eksekusi

| Run # | Tanggal & Waktu | Environment | Status Akhir | Penguji (Tester) | Catatan Ringkas |
| :---: | :--- | :--- | :---: | :--- | :--- |
| 1 | 2026-11-23 08:15 WIB | Localhost (Laragon) | 🟢 PASSED | QA Team | Inisialisasi pengujian awal flow login web, seluruh step 1.1 - 1.8 valid |

---

## 4. Detail Riwayat Eksekusi

<!--
PETUNJUK PENAMBAHAN RIWAYAT (APPEND-ONLY):
Setiap kali menjalankan scenario test ini:
1. Update tabel ringkasan pada bagian "## 3. Ringkasan Eksekusi" di atas.
2. Tambahkan blok baru di paling bawah file ini menggunakan template:
   ### Run #[N] - YYYY-MM-DD HH:mm WIB - [STATUS]
-->

### Run #1 - 2026-11-23 08:15 WIB - 🟢 PASSED
- **Environment**: Localhost (PHP 8.x / MySQL 8 / Laragon)
- **Base URL**: `http://localhost/wms-indraco/public/login`
- **Penguji / Runner**: QA Team / Automated Runner
- **Browser / Tool**: Google Chrome (Desktop 1920x1080)
- **Status Akhir**: `🟢 PASSED`
- **Durasi Eksekusi**: ~2 menit
- **Catatan Umum**: Semua fungsionalitas login, auto-fill demo kredensial, toggle dark mode, penanganan error input salah, dan pengalihan ke dashboard berjalan dengan baik.

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **1.1** | 🟢 PASS | Halaman login terbuka dengan styling Glassmorphism dan background animasi partikel. | Element `#username`, `#password`, dan tombol submit ditemukan. |
| **1.2** | 🟢 PASS | Toggle tema berfungsi bergantian antara Dark Mode dan Light Mode secara realtime. | Class `dark` pada tag `<html>` berhasil di-toggle dan state tersimpan di localStorage. |
| **1.3** | 🟢 PASS | Tooltip validasi *"Please fill out this field"* muncul saat submit form kosong. | Form HTML5 validation bekerja sesuai ekspektasi. |
| **1.4** | 🟢 PASS | Muncul alert merah berisi *"Username atau password yang dimasukkan salah, atau akun Anda non-aktif."* | Input username tetap terisi `admin`, password direset. |
| **1.5** | 🟢 PASS | Alert validasi gagal muncul untuk username yang tidak ada di database. | Pesan error konsisten dan aman (tidak mengekspos eksistensi user). |
| **1.6** | 🟢 PASS | Tombol demo kredensial mengisikan `admin` & `admin123` secara instan. | Fungsi JS `setDemo()` bekerja normal. |
| **1.7** | 🟢 PASS | Berhasil login dan diarahkan ke URL `/` (Dashboard). Cookie remember me aktif. | Database audit log mencatat event `LOGIN` untuk user ID `admin`. |
| **1.8** | 🟢 PASS | Mengakses `/login` saat status terautentikasi otomatis me-redirect ke Dashboard. | Route middleware `guest` / auth redirect berjalan sempurna. |

---

<!-- TEMPLATE UNTUK RUN BERIKUTNYA (COPY & APPEND DI BAWAH INI):
### Run #[N] - YYYY-MM-DD HH:mm WIB - [STATUS]
- **Environment**: Localhost / Staging
- **Base URL**: 
- **Penguji / Runner**: 
- **Browser / Tool**: 
- **Status Akhir**: 🟢 PASSED / 🔴 FAILED / 🟡 BLOCKED
- **Catatan Umum**: 

#### Hasil Per Langkah Pengujian:
| Step | Status | Hasil Aktual (Actual Result) | Bukti / Catatan |
| :---: | :---: | :--- | :--- |
| **1.1** | 🟢 PASS | | |
| **1.2** | 🟢 PASS | | |
| **1.3** | 🟢 PASS | | |
| **1.4** | 🟢 PASS | | |
| **1.5** | 🟢 PASS | | |
| **1.6** | 🟢 PASS | | |
| **1.7** | 🟢 PASS | | |
| **1.8** | 🟢 PASS | | |
-->
