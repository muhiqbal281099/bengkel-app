# 📘 Buku Panduan Pengguna (Manual Book)
## Sistem Informasi Manajemen Bengkel Terpadu — Bengkel Pro

---

## Daftar Isi
1. [Bab 1: Pendahuluan & Gambaran Umum Sistem](#bab-1-pendahuluan--gambaran-umum-sistem)
2. [Bab 2: Kebutuhan Sistem & Akses Aplikasi](#bab-2-kebutuhan-sistem--akses-aplikasi)
3. [Bab 3: Layar Monitor Publik (Display Ruang Tunggu)](#bab-3-layar-monitor-publik-display-ruang-tunggu)
4. [Bab 4: Panduan Petugas Frontdesk (Loket Pendaftaran)](#bab-4-panduan-petugas-frontdesk-loket-pendaftaran)
5. [Bab 5: Panduan Montir / Mekanik (Area Pengerjaan Servis)](#bab-5-panduan-montir--mekanik-area-pengerjaan-servis)
6. [Bab 6: Panduan Kasir (Point of Sales & Cetak Nota)](#bab-6-panduan-kasir-point-of-sales--cetak-nota)
7. [Bab 7: Panduan Admin Gudang & Profil Bengkel](#bab-7-panduan-admin-gudang--profil-bengkel)
8. [Bab 8: Skenario Praktik Alur Penuh (Simulasi Lengkap)](#bab-8-skenario-praktik-alur-penuh-simulasi-lengkap)
9. [Bab 9: Tanya Jawab & Pemecahan Masalah (FAQ)](#bab-9-tanya-jawab--pemecahan-masalah-faq)

---

## Bab 1: Pendahuluan & Gambaran Umum Sistem

**Bengkel Pro** adalah platform aplikasi web yang dirancang untuk mengotomatisasi seluruh alur kerja operasional bengkel kendaraan bermotor (roda dua maupun roda empat). Aplikasi ini mengintegrasikan komunikasi antara meja depan, ruang mekanik, gudang sparepart, hingga loket pembayaran secara *real-time*.

### Diagram Alur Kerja Terpadu
```text
[Pelanggan Datang]
        │
        ▼
[1. Frontdesk / Petugas] ─────────────► [Monitor Publik Ruang Tunggu]
  • Input Data Kendaraan                  (Nomor Antrean Tampil Live)
  • Pilih Servis & Tugaskan Montir
        │
        ▼
[2. Montir / Mekanik]
  • Pengecekan Kendaraan
  • Pengerjaan Fisik Servis
  • Konfirmasi Selesai & Catatan Teknis
        │
        ▼
[3. Kasir / Point of Sales]
  • Hitung Biaya Jasa Servis
  • Tambah Suku Cadang (Stok Gudang Otomatis Berkurang)
  • Proses Pembayaran (Cash / Transfer / QRIS)
  • Cetak Nota / Struk Pembayaran Resmi
        │
        ▼
[Pelanggan Pulang Membawa Kendaraan & Nota]
```

---

## Bab 2: Kebutuhan Sistem & Akses Aplikasi

### Kebutuhan Server Lokal
- **Lingkungan**: Laragon (Web Server Apache + MySQL).
- **Versi PHP**: Minimal PHP 8.3 (tersedia di folder `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64`).
- **Web Browser**: Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari versi terkini.

### Cara Menjalankan Aplikasi
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** hingga Apache dan MySQL berstatus *Running*.
3. Buka browser web Anda dan gunakan salah satu tautan berikut:
   - **Tampilan Antrean Publik**: `http://localhost/bengkel-app/public` atau `http://bengkel-app.test`
   - **Pintu Masuk Login Staf**: `http://localhost/bengkel-app/public/login` atau `http://bengkel-app.test/login`

### Akun Bawaan (Default Credentials)
Semua akun menggunakan kata sandi yang sama: **`password`**

| Peran | Alamat Email | Halaman Dituju |
|---|---|---|
| **Admin Gudang** | `admin@bengkel.com` | Dashboard Manajemen Gudang & Pengaturan Bengkel |
| **Kasir** | `kasir@bengkel.com` | Dashboard Transaksi POS & Kasir |
| **Petugas Frontdesk** | `petugas@bengkel.com` | Dashboard Antrean & Pendaftaran Servis |
| **Montir / Mekanik** | `montir@bengkel.com` | Panel Tugas Servis Montir |

---

## Bab 3: Layar Monitor Publik (Display Ruang Tunggu)

Layar ini ditujukan untuk ditayangkan pada televisi atau layar monitor di ruang tunggu pelanggan tanpa memerlukan login.

### Fitur-Fitur Utama Monitor
1. **Identitas Bengkel**:
   - Menampilkan Logo Resmi (jika belum diupload, sistem menyediakan logo default dinamis).
   - Menampilkan Nama Bengkel, Alamat Fisik, dan Nomor Layanan Pelanggan.
2. **Jam Digital & Kalender**: Menampilkan waktu hari ini secara *real-time*.
3. **Pengubah Tema (Theme Toggle)**:
   - Tombol `☀️ Mode Terang` dan `🌙 Mode Gelap` di pojok kanan atas.
   - Pilihan tema tersimpan otomatis di browser (*localStorage*).
4. **Kartu Nomor Antrean Berjalan**:
   - **Menunggu Giliran (Kuning/Orange)**: Kendaraan telah terdaftar dan menunggu giliran.
   - **Pengecekan (Orange)**: Sedang dilakukan pengecekan awal oleh teknisi.
   - **Sedang Diservis (Biru)**: Kendaraan sedang aktif dibongkar/diperbaiki mekanik.
   - **Selesai Servis (Hijau)**: Servis telah rampung dan siap diproses di meja kasir.
5. **Running Text Pengumuman**: Teks berjalan di bagian bawah layar yang dapat diatur teks pesannya melalui menu Admin Bengkel.
6. **Auto Refresh**: Halaman akan memuat ulang data secara otomatis setiap 30 detik.

---

## Bab 4: Panduan Petugas Frontdesk (Loket Pendaftaran)

Petugas meja depan bertugas menyambut pelanggan dan menginput kendaraan ke dalam antrean harian.

### Langkah demi Langkah Pendaftaran Servis:
1. Masuk ke halaman login dengan akun `petugas@bengkel.com`.
2. Anda akan diarahkan ke **Dashboard Manajemen Antrean**.
3. Klik tombol hijau **➕ Daftarkan Kendaraan Baru** di kanan atas.
4. Pilih metode pendaftaran:
   - **Metode A: Kendaraan Terdaftar**:
     Jika pelanggan sudah pernah servis sebelumnya, pilih tab **🔍 Cari Kendaraan Terdaftar**, lalu pilih nomor polisi dari daftar *dropdown*.
   - **Metode B: Pelanggan Baru / Walk-in**:
     Jika pelanggan baru pertama kali datang, klik tab **🆕 Kendaraan Baru / Walk-in**, lalu lengkapi formulir:
     - Nama Pelanggan
     - Nomor Polisi (Contoh: `B 1234 ABC`)
     - Merek (Contoh: Honda, Yamaha, Toyota)
     - Model Kendaraan (Contoh: Vario 160, Avanza)
     - Tahun Pembuatan
5. Pilih **Jenis Layanan Servis** (misal: Servis Rutin, Tune Up, Ganti Oli).
6. Pilih **Penugasan Mekanik** (pilih montir yang sedang luang, atau kosongkan jika ingin ditugaskan nanti).
7. Tuliskan **Keluhan / Catatan Servis** dari pelanggan (misal: "Rem belakang berdecit, tarikan bawah berat").
8. Klik tombol **Daftarkan ke Antrean**.
9. Sistem akan otomatis menerbitkan **Nomor Antrean** resmi (misal: `A-001`) dan langsung muncul di Monitor Publik.

---

## Bab 5: Panduan Montir / Mekanik (Area Pengerjaan Servis)

Panel ini dirancang khusus untuk teknisi bengkel dengan tombol-tombol aksi cepat (*quick action button*) yang ramah layar sentuh maupun desktop.

### Langkah demi Langkah Pengerjaan Montir:
1. Masuk ke halaman login dengan akun `montir@bengkel.com`.
2. Di halaman **Tugas Servis Saya**, montir akan melihat kartu-kartu kendaraan yang ditugaskan kepadanya.
3. **Mulai Pengecekan**:
   - Klik tombol **🔍 Mulai Pengecekan**. Status antrean berubah menjadi `Pengecekan (Checking)`.
4. **Mulai Servis**:
   - Setelah pengecekan selesai dan mulai membongkar, klik tombol **🔧 Mulai Pengerjaan**. Status berubah menjadi `Sedang Diservis (In Service)`.
5. **Menyelesaikan Servis**:
   - Bila perbaikan telah rampung, klik tombol **✅ Tandai Selesai**.
   - Muncul jendela konfirmasi (modal). Tuliskan catatan ringkas perbaikan jika ada (misal: "Ganti busi dan filter udara, kampas rem masih tebal").
   - Klik **Konfirmasi Selesai**.
6. Antrean akan otomatis berpindah ke tab riwayat montir dan langsung masuk ke radar meja Kasir untuk pembayaran.

---

## Bab 6: Panduan Kasir (Point of Sales & Cetak Nota)

Kasir bertanggung jawab atas penerimaan pembayaran jasa dan suku cadang serta mencetak struk transaksi.

### Langkah demi Langkah Pembayaran & Cetak Nota:
1. Masuk ke halaman login dengan akun `kasir@bengkel.com`.
2. Di **Dashboard Kasir**, antrean yang berstatus **Selesai Servis** akan berada di posisi paling atas tabel antrean belum bayar.
3. Klik tombol **💳 Proses Pembayaran** pada kendaraan yang dituju, atau masuk ke menu **Kasir (POS)**.
4. **Rincian Biaya Servis**:
   - Biaya jasa servis otomatis tertera sesuai tarif layanan.
5. **Menambah Suku Cadang / Sparepart (Opsional)**:
   - Jika ada penggantian oli, busi, atau kampas rem, cari nama barang pada bagian *Tambah Suku Cadang*.
   - Masukkan jumlah (*Qty*). Klik tombol **Tambah ke Tagihan**.
   - Sistem akan memeriksa ketersediaan stok gudang secara otomatis dan menghitung subtotal.
6. **Pembayaran**:
   - Pilih metode bayar: **Tunai (Cash)**, **Transfer Bank**, atau **QRIS**.
   - Jika memilih *Tunai*, masukkan jumlah nominal uang yang diterima pelanggan. Sistem akan otomatis menghitung kembalian.
7. Klik tombol **Simpan Transaksi & Cetak Nota**.
8. **Struk Pembayaran Resmi**:
   - Nota akan terbuka dengan format rapi dilengkapi kop bengkel (logo, alamat, kontak), rincian jasa, rincian suku cadang, dan kolom tanda tangan.
   - Klik tombol **🖨️ Cetak Nota Pembayaran** untuk mencetak langsung ke printer fisik (mendukung printer thermal kasir 80mm maupun kertas biasa A4).

---

## Bab 7: Panduan Admin Gudang & Profil Bengkel

Admin Gudang memiliki wewenang mengontrol barang masuk/keluar, layanan bengkel, dan identitas perusahaan.

### 1. Manajemen Suku Cadang (Sparepart)
- Akses melalui menu **Master Suku Cadang**.
- **Tambah Barang Baru**: Masukkan kode unik, nama suku cadang, kategori, harga beli, harga jual, dan jumlah stok awal.
- **Peringatan Stok Menipis (*Low Stock Alert*)**: Barang yang stoknya di bawah 5 unit otomatis memiliki label merah mencolok untuk segera dilakukan pemesanan ulang (*restock*).
- **Penyesuaian Stok Cepat (*Adjust Stock*)**: Klik tombol *Penyesuaian*, lalu pilih jenis penyesuaian (Barang Masuk / Barang Rusak / Koreksi) untuk menambah atau mengurangi stok tanpa mengubah harga.

### 2. Manajemen Jasa Servis
- Akses melalui menu **Layanan Servis**.
- Daftarkan jenis pekerjaan servis, tarif dasar biaya jasa (Rp), dan estimasi waktu pengerjaan dalam satuan menit.

### 3. Pengaturan Identitas Bengkel (Branding)
- Akses melalui menu **Pengaturan Bengkel**.
- Di sini Admin dapat mengubah:
  - **Nama Bengkel**: Nama yang akan tampil di header aplikasi dan kop nota.
  - **Alamat Bengkel**: Alamat lengkap untuk informasi publik dan nota.
  - **Nomor Telepon**: Kontak layanan pelanggan bengkel.
  - **Teks Berjalan (*Running Text*)**: Pesan pengumuman atau promo yang melintas di bagian bawah Monitor Publik.
  - **Unggah Logo**: Unggah file gambar logo bengkel (PNG/JPG/WEBP). Jika belum mengunggah gambar, sistem otomatis menampilkan logo default bengkel yang bersih dan proporsional.

---

## Bab 8: Skenario Praktik Alur Penuh (Simulasi Lengkap)

Untuk menguji kelancaran sistem dari awal hingga akhir, Anda dapat menjalankan simulasi berikut:

1. **Buka Monitor Publik**:
   Buka jendela browser baru di `http://localhost/bengkel-app/public` (posisikan berdampingan di layar).
2. **Langkah 1 (Frontdesk)**:
   - Login dengan `petugas@bengkel.com` / `password`.
   - Buka menu **Daftarkan Kendaraan**.
   - Pilih tab *Walk-in*, isi Plat: `B 8888 BOS`, Merek: `Honda`, Model: `PCX 160`, Jasa: `Tune Up`, Montir: `Budi Santoso (Mekanik)`.
   - Klik Simpan. Nomor antrean terbit (misal `A-004`).
   - Perhatikan Monitor Publik: Kendaraan langsung muncul dengan status *Menunggu*.
3. **Langkah 2 (Montir)**:
   - Login di tab terpisah dengan `montir@bengkel.com` / `password`.
   - Di dashboard tugas, klik **Mulai Pengecekan**, lalu klik **Mulai Servis**.
   - Klik **Tandai Selesai**, isi catatan: "Tune up beres, CVT bersih". Konfirmasi selesai.
   - Perhatikan Monitor Publik: Status berubah hijau *Selesai Servis*.
4. **Langkah 3 (Kasir)**:
   - Login dengan `kasir@bengkel.com` / `password`.
   - Klik menu **Kasir (POS)**, pilih antrean `B 8888 BOS`.
   - Tambahkan 1 botol *Oli Mesin*, pilih metode *Tunai*, masukkan nominal pembayaran.
   - Klik Simpan Transaksi.
   - Halaman nota resmi langsung terbuka dan siap dicetak.
   - Cek menu Gudang: Stok oli mesin berkurang otomatis sejumlah 1 botol.

---

## Bab 9: Tanya Jawab & Pemecahan Masalah (FAQ)

### Q1: Halaman menampilkan "404 Not Found" atau "403 Forbidden" saat membuka menu tertentu?
**Jawaban**:
Aplikasi ini dilengkapi pengaman *Role-Based Middleware*. Setiap peran hanya diizinkan membuka halamannya sendiri. Misalnya, akun kasir tidak diizinkan membuka menu gudang admin, dan sebaliknya. Pastikan login dengan akun yang sesuai peran menu yang dituju.

### Q2: Logo bengkel yang diunggah tidak muncul di nota atau layar monitor?
**Jawaban**:
Pastikan symbolic link penyimpanan Laravel telah aktif. Jika gambar belum muncul, buka Terminal Laragon lalu jalankan:
```powershell
php artisan storage:link
```

### Q3: Tampilan website berantakan atau tidak memuat gaya visual?
**Jawaban**:
Semua desain pada aplikasi ini menggunakan CSS Vanilla mandiri tanpa ketergantungan CDN eksternal yang lambat. Pastikan browser Anda tidak dalam mode "Block Stylesheet" dan bersihkan cache browser dengan menekan `Ctrl + F5`.

### Q4: Bagaimana cara mereset seluruh data kembali ke kondisi awal?
**Jawaban**:
Buka Terminal Laragon, arahkan ke folder proyek, lalu jalankan:
```powershell
php artisan migrate:fresh --seed
```
Seluruh tabel akan diatur ulang dan diisi data contoh baru yang siap dipakai.

---

*Dokumen ini merupakan buku panduan resmi operasional Bengkel Pro.*
