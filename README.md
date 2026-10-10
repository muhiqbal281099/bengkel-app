# 🛠️ Bengkel Pro - Sistem Terpadu Manajemen & POS

Selamat datang di aplikasi **Bengkel Pro**. Aplikasi ini adalah Sistem Informasi Manajemen Bengkel Terpadu berbasis web yang mencakup **Monitor Antrean Publik (Public Display)**, **Frontdesk Pendaftaran Servis**, **Panel Kerja Montir/Mekanik**, **Manajemen Inventaris Gudang & Suku Cadang**, serta **Kasir & Point of Sales (POS)** dengan cetak nota resmi.

---

## 🚀 Persiapan dan Menjalankan Aplikasi (Lokal Laragon)

Aplikasi ini dibangun menggunakan framework **Laravel 11** dan berjalan optimal pada lingkungan **Laragon** dengan versi **PHP 8.3 / 8.4**.

### 1. Mengaktifkan Laragon & Database
1. Buka aplikasi **Laragon** di komputer Anda.
2. Klik tombol **Start All** untuk menyalakan Apache dan MySQL.
3. Database aplikasi bernama `db_bengkel`. Semua tabel utama (pengguna, kendaraan, layanan jasa, suku cadang, antrean, dan transaksi) sudah siap dan saling terhubung.
4. Jika ingin menyegarkan data awal (seeding) ke database, jalankan perintah ini melalui terminal Laragon:
   ```powershell
   php artisan migrate:fresh --seed
   ```
   *(Catatan: Pastikan PHP versi 8.3/8.4 yang digunakan pada path CLI Laragon).*

### 2. Alamat Akses Website
Anda dapat mengakses sistem melalui web browser (Google Chrome, Microsoft Edge, Mozilla Firefox) melalui salah satu link berikut:

- **Layar Monitor Antrean Publik**:
  👉 [http://localhost/bengkel-app/public](http://localhost/bengkel-app/public) atau [http://bengkel-app.test](http://bengkel-app.test)
- **Halaman Login Staf Bengkel**:
  👉 [http://localhost/bengkel-app/public/login](http://localhost/bengkel-app/public/login) atau [http://bengkel-app.test/login](http://bengkel-app.test/login)
- **Manual Book / Panduan Lengkap**:
  👉 Buka berkas [PANDUAN.md](file:///c:/laragon/www/bengkel-app/PANDUAN.md) untuk panduan operasional langkah demi langkah.

---

## 🔐 Akun Pengujian & Hak Akses (Role-Based Access)

Sistem menggunakan pembagian peran (*Role-Based Access Control* / RBAC) dengan pemisahan antarmuka, hak akses menu, dan warna aksen tema. Setiap peran akan otomatis dialihkan (*redirect*) ke panel masing-masing setelah berhasil login.

Semua akun default menggunakan kata sandi: **`password`**

| Peran (Role) | Alamat Email | Halaman Dashboard | Tugas Utama |
|---|---|---|---|
| **Admin Gudang** | `admin@bengkel.com` | `/admin/dashboard` | Mengelola stok suku cadang, katalog jasa servis, dan pengaturan profil bengkel (nama, logo, alamat, telp, running text). |
| **Kasir** | `kasir@bengkel.com` | `/kasir/dashboard` | Memproses pembayaran antrean selesai, menambah suku cadang transaksi, dan cetak nota resmi ber-kop bengkel. |
| **Petugas Frontdesk** | `petugas@bengkel.com` | `/petugas/dashboard` | Menerima kendaraan datang, mendaftarkan pelanggan walk-in, membuat nomor antrean (`A-001`), dan menugaskan mekanik. |
| **Montir / Mekanik** | `montir@bengkel.com` | `/montir/dashboard` | Menerima kartu antrean kendaraan, memperbarui status pengerjaan (*checking* $\rightarrow$ *in service* $\rightarrow$ *completed*), dan mencatat keluhan teknis. |
| **Pelanggan** | `ahmad@gmail.com` | `/dashboard` | Pemilik kendaraan terdaftar dalam basis data sistem. |

---

## 🌟 Fitur Utama Sistem

1. **Layar Monitor Publik (*Public Display Screen*)**:
   - Menampilkan nomor antrean berjalan hari ini secara live dengan indikator warna status resmi.
   - Dilengkapi identitas bengkel dinamis (Logo, Nama Bengkel, Alamat, Nomor Kontak).
   - Jam digital *real-time* dan *running text* pengumuman / promosi bergerak.
   - Tombol pengubah tema **Mode Terang / Mode Gelap** (*default: Mode Terang*).
   - Auto-refresh setiap 30 detik untuk pembaharuan data otomatis.

2. **Panel Petugas Frontdesk**:
   - Pendaftaran antrean berbasis tab: pencarian kendaraan yang sudah ada atau registrasi langsung pelanggan baru (*walk-in*).
   - Penomoran antrean otomatis per hari (`A-001`, `A-002`, dst).
   - Pelimpahan langsung ke montir yang tersedia.

3. **Panel Montir / Mekanik**:
   - Kartu tugas servis aktif dengan akses pembaruan status 1-klik (*Mulai Pengecekan*, *Mulai Servis*, *Konfirmasi Selesai*).
   - Validasi keamanan: mekanik hanya dapat memperbarui antrean yang ditugaskan kepada dirinya.
   - Modal konfirmasi catatan akhir sebelum diserahkan ke kasir.

4. **Panel Kasir & Point of Sales (POS)**:
   - Antrean yang baru saja diselesaikan montir otomatis tampil di prioritas utama pembayaran.
   - Pilihan suku cadang tambahan dengan kalkulasi harga subtotal dinamis dan pengecekan stok otomatis.
   - Pilihan metode pembayaran (*Tunai/Cash*, *Transfer Bank*, *QRIS*).
   - Fitur cetak nota pembayaran ramah cetak thermal/A4 (*print-ready CSS*) dengan logo dan kop resmi bengkel.

5. **Panel Admin Gudang & Pengaturan Bengkel**:
   - Manajemen katalog suku cadang: tambah, edit, hapus, dan penyesuaian stok manual (*in/out*).
   - Indikator peringatan stok menipis (*low stock alert*).
   - Manajemen katalog jenis layanan jasa servis & estimasi durasi kerja.
   - Halaman **Pengaturan Bengkel**: ubah nama, alamat, nomor telepon, running text, dan unggah logo (dilengkapi logo cadangan/default otomatis jika belum ada gambar yang diunggah).

---

## 📂 Struktur Penting Arsitektur Kode

1. **Model & Basis Data**:
   - `app/Models/`: Berisi model Eloquent `Queue`, `Kendaraan`, `Service`, `Sparepart`, `Transaction`, `TransactionDetail`, `BengkelSetting`, dan `User`.
   - `database/migrations/`: Skema relasi tabel dari kendaraan hingga detail struk transaksi.
2. **Controllers**:
   - `QueueController.php`: Pengendali monitor antrean publik.
   - `FrontdeskController.php`: Registrasi antrean dan alur loket meja depan.
   - `MontirController.php`: Alur kerja servis teknisi bengkel.
   - `POSController.php`: Manajemen transaksi kasir, potong stok, dan cetak nota.
   - `InventoryController.php`: Manajemen suku cadang gudang.
   - `ServiceController.php`: Manajemen tarif dan jenis jasa servis.
   - `SettingController.php`: Pengaturan identitas bengkel & unggah logo.
3. **Desain Tampilan (Blade & CSS)**:
   - `resources/views/layouts/`: Tata letak khusus tiap peran (`admin.blade.php`, `kasir.blade.php`, `petugas.blade.php`, `montir.blade.php`) dengan sistem tema Terang/Gelap dan tipografi modern *Outfit*.
4. **Keamanan & Otorisasi**:
   - `app/Http/Middleware/RoleMiddleware.php`: Menjamin isolasi ketat antar peran pengguna (*Error 403 jika mengakses rute peran lain*).

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi telah dilengkapi rangkaian uji fitur otomatis (*Feature Test Suite*) dengan 100% tingkat kelulusan:

Jalankan perintah pengujian via terminal:
```powershell
php artisan test --compact
```
*(Hasil: 36 Tests Passed, 92 Assertions).*
