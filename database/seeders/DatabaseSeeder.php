<?php

namespace Database\Seeders;

use App\Models\BengkelSetting;
use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Profil & Pengaturan Bengkel
        BengkelSetting::updateOrCreate(
            ['id' => 1],
            [
                'nama_bengkel' => 'Bengkel Pro Motor & Mobil',
                'alamat_bengkel' => 'Jl. Raya Otomotif No. 88, Pusat Servis Kendaraan',
                'telepon_bengkel' => '0812-3456-7890 / (021) 555-8989',
                'logo_path' => null,
                'running_text' => 'Selamat datang di Bengkel Pro! Servis profesional, transparan, dan bergaransi resmi.',
            ]
        );

        // 2. Akun Staf (Iqbal, Wahyu, Amin, Adimas)
        // Petugas Frontdesk: Iqbal
        $petugas = User::create([
            'name' => 'Iqbal',
            'email' => 'petugas@bengkel.com',
            'password' => $defaultPassword,
            'role' => 'petugas',
        ]);

        // Montir: Wahyu
        $montir = User::create([
            'name' => 'Wahyu',
            'email' => 'montir@bengkel.com',
            'password' => $defaultPassword,
            'role' => 'montor',
        ]);

        // Kasir: Amin
        $kasir = User::create([
            'name' => 'Amin',
            'email' => 'kasir@bengkel.com',
            'password' => $defaultPassword,
            'role' => 'kasir',
        ]);

        // Admin Gudang: Adimas
        $admin = User::create([
            'name' => 'Adimas',
            'email' => 'admin@bengkel.com',
            'password' => $defaultPassword,
            'role' => 'admin_gudang',
        ]);

        // 3. Akun Pelanggan (Dummy)
        $pelanggan1 = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@gmail.com',
            'password' => $defaultPassword,
            'role' => 'pelanggan',
        ]);
        $pelanggan2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@gmail.com',
            'password' => $defaultPassword,
            'role' => 'pelanggan',
        ]);
        $pelanggan3 = User::create([
            'name' => 'Doni Pratama',
            'email' => 'doni@gmail.com',
            'password' => $defaultPassword,
            'role' => 'pelanggan',
        ]);
        $pelanggan4 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@gmail.com',
            'password' => $defaultPassword,
            'role' => 'pelanggan',
        ]);
        $pelanggan5 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi.lestari@gmail.com',
            'password' => $defaultPassword,
            'role' => 'pelanggan',
        ]);

        // 4. Data Kendaraan Pelanggan
        $k1 = Kendaraan::create([
            'user_id' => $pelanggan1->id,
            'no_polisi' => 'B 3456 KAA',
            'merk' => 'Honda',
            'model' => 'Vario 160 CBS',
            'tahun' => 2022,
        ]);
        $k2 = Kendaraan::create([
            'user_id' => $pelanggan2->id,
            'no_polisi' => 'B 6789 TGH',
            'merk' => 'Yamaha',
            'model' => 'NMAX 155 Connected',
            'tahun' => 2023,
        ]);
        $k3 = Kendaraan::create([
            'user_id' => $pelanggan3->id,
            'no_polisi' => 'B 1234 BCD',
            'merk' => 'Honda',
            'model' => 'Beat ESP Street',
            'tahun' => 2021,
        ]);
        $k4 = Kendaraan::create([
            'user_id' => $pelanggan4->id,
            'no_polisi' => 'D 5678 EFG',
            'merk' => 'Yamaha',
            'model' => 'Aerox 155 CyberCity',
            'tahun' => 2023,
        ]);
        $k5 = Kendaraan::create([
            'user_id' => $pelanggan5->id,
            'no_polisi' => 'F 9012 HIJ',
            'merk' => 'Honda',
            'model' => 'Scoopy Prestige',
            'tahun' => 2022,
        ]);

        // 5. Layanan Servis Bengkel
        $srvOli = Service::create([
            'nama_layanan' => 'Ganti Oli Rutin',
            'deskripsi' => 'Kuras oli mesin dan penggantian oli baru + cek tekanan angin ban.',
            'biaya' => 15000,
            'durasi_estimasi' => 15,
        ]);
        $srvTuneUp = Service::create([
            'nama_layanan' => 'Servis Ringan (Tune Up)',
            'deskripsi' => 'Pembersihan filter udara, cek busi, semprot throttle body/karbu, dan setel rem.',
            'biaya' => 45000,
            'durasi_estimasi' => 30,
        ]);
        $srvCvt = Service::create([
            'nama_layanan' => 'Servis CVT Matic Lengkap',
            'deskripsi' => 'Bongkar transmisi CVT, bersihkan pulley & mangkok ganda, dan ganti grease CVT.',
            'biaya' => 40000,
            'durasi_estimasi' => 35,
        ]);
        $srvRem = Service::create([
            'nama_layanan' => 'Ganti Kampas Rem',
            'deskripsi' => 'Pemasangan kampas rem depan/belakang serta bleeding minyak rem.',
            'biaya' => 20000,
            'durasi_estimasi' => 20,
        ]);
        $srvOverhaul = Service::create([
            'nama_layanan' => 'Overhaul / Turun Mesin',
            'deskripsi' => 'Bongkar blok silinder, skir klep, ganti ring piston, dan kalibrasi kompresi.',
            'biaya' => 250000,
            'durasi_estimasi' => 180,
        ]);
        $srvBan = Service::create([
            'nama_layanan' => 'Pasang Ban & Tubeless',
            'deskripsi' => 'Bongkar pasang ban menggunakan mesin tire changer + isi angin nitrogen.',
            'biaya' => 25000,
            'durasi_estimasi' => 20,
        ]);

        // 6. Spareparts / Suku Cadang
        $spOliYamalube = Sparepart::create([
            'kode_barang' => 'SP-OLI-001',
            'nama_barang' => 'Oli Mesin Yamalube Silver 0.8L',
            'kategori' => 'Oli & Pelumas',
            'harga_jual' => 48000,
            'stok' => 25,
        ]);
        $spOliMpx2 = Sparepart::create([
            'kode_barang' => 'SP-OLI-002',
            'nama_barang' => 'Oli Mesin AHM MPX 2 Matic 0.8L',
            'kategori' => 'Oli & Pelumas',
            'harga_jual' => 52000,
            'stok' => 30,
        ]);
        $spOliGardan = Sparepart::create([
            'kode_barang' => 'SP-OLI-003',
            'nama_barang' => 'Oli Gardan Yamalube Matic 100ml',
            'kategori' => 'Oli & Pelumas',
            'harga_jual' => 18000,
            'stok' => 15,
        ]);
        $spOliShell = Sparepart::create([
            'kode_barang' => 'SP-OLI-004',
            'nama_barang' => 'Oli Mesin Shell Advance AX7 10W-40',
            'kategori' => 'Oli & Pelumas',
            'harga_jual' => 65000,
            'stok' => 4, // Stok menipis
        ]);
        $spRemDepan = Sparepart::create([
            'kode_barang' => 'SP-REM-001',
            'nama_barang' => 'Kampas Rem Depan Honda Beat/Vario',
            'kategori' => 'Pengereman',
            'harga_jual' => 38000,
            'stok' => 12,
        ]);
        $spRemBelakang = Sparepart::create([
            'kode_barang' => 'SP-REM-002',
            'nama_barang' => 'Kampas Rem Belakang Tromol Matic',
            'kategori' => 'Pengereman',
            'harga_jual' => 35000,
            'stok' => 8,
        ]);
        $spBusi = Sparepart::create([
            'kode_barang' => 'SP-LIS-001',
            'nama_barang' => 'Busi NGK CPR9EA-9 Standar',
            'kategori' => 'Kelistrikan',
            'harga_jual' => 25000,
            'stok' => 18,
        ]);
        $spAki = Sparepart::create([
            'kode_barang' => 'SP-LIS-002',
            'nama_barang' => 'Aki Kering GS Astra GTZ5S MF',
            'kategori' => 'Kelistrikan',
            'harga_jual' => 235000,
            'stok' => 3, // Stok menipis
        ]);
        $spVbelt = Sparepart::create([
            'kode_barang' => 'SP-TRN-001',
            'nama_barang' => 'Drive Belt / V-Belt Kit Scoopy ESP',
            'kategori' => 'Transmisi',
            'harga_jual' => 145000,
            'stok' => 6,
        ]);
        $spRantai = Sparepart::create([
            'kode_barang' => 'SP-TRN-002',
            'nama_barang' => 'Paket Rantai & Gir Supra X 125',
            'kategori' => 'Transmisi',
            'harga_jual' => 185000,
            'stok' => 2, // Stok menipis
        ]);
        $spRoller = Sparepart::create([
            'kode_barang' => 'SP-TRN-003',
            'nama_barang' => 'Roller CVT Standar Beat FI (Set)',
            'kategori' => 'Transmisi',
            'harga_jual' => 45000,
            'stok' => 0, // Stok habis
        ]);
        $spBanIrc = Sparepart::create([
            'kode_barang' => 'SP-BAN-001',
            'nama_barang' => 'Ban Luar Tubeless IRC 90/90-14',
            'kategori' => 'Ban & Roda',
            'harga_jual' => 210000,
            'stok' => 7,
        ]);

        // 7. Antrean & Transaksi Hari Ini
        // Antrean 1: A-001 (Completed & Sudah Ada Transaksi di Kasir)
        $q1 = Queue::create([
            'nomor_antrean' => 'A-001',
            'kendaraan_id' => $k1->id,
            'service_id' => $srvOli->id,
            'montor_id' => $montir->id,
            'status' => 'completed',
            'catatan_servis' => 'Ganti oli MPX 2 dan oli gardan. Tarikan mesin kembali responsif.',
        ]);

        $trx1 = Transaction::create([
            'queue_id' => $q1->id,
            'kasir_id' => $kasir->id,
            'total_biaya_jasa' => $srvOli->biaya,
            'total_biaya_sparepart' => $spOliMpx2->harga_jual + $spOliGardan->harga_jual,
            'total_pembayaran' => $srvOli->biaya + $spOliMpx2->harga_jual + $spOliGardan->harga_jual,
            'metode_pembayaran' => 'qris',
        ]);

        TransactionDetail::create([
            'transaction_id' => $trx1->id,
            'sparepart_id' => $spOliMpx2->id,
            'jumlah' => 1,
            'subtotal' => $spOliMpx2->harga_jual,
        ]);
        TransactionDetail::create([
            'transaction_id' => $trx1->id,
            'sparepart_id' => $spOliGardan->id,
            'jumlah' => 1,
            'subtotal' => $spOliGardan->harga_jual,
        ]);

        // Antrean 2: A-002 (Completed, Siap Bayar di Kasir)
        Queue::create([
            'nomor_antrean' => 'A-002',
            'kendaraan_id' => $k2->id,
            'service_id' => $srvCvt->id,
            'montor_id' => $montir->id,
            'status' => 'completed',
            'catatan_servis' => 'CVT dibersihkan dan dilumasi ulang grease high-temp. Gredek awal hilang.',
        ]);

        // Antrean 3: A-003 (Sedang Dikerjakan Montir Wahyu - in_service)
        Queue::create([
            'nomor_antrean' => 'A-003',
            'kendaraan_id' => $k3->id,
            'service_id' => $srvRem->id,
            'montor_id' => $montir->id,
            'status' => 'in_service',
            'catatan_servis' => 'Pemasangan kampas rem depan baru + pembersihan kaliper rem.',
        ]);

        // Antrean 4: A-004 (Sedang Diinspeksi - checking)
        Queue::create([
            'nomor_antrean' => 'A-004',
            'kendaraan_id' => $k4->id,
            'service_id' => $srvTuneUp->id,
            'montor_id' => $montir->id,
            'status' => 'checking',
            'catatan_servis' => 'Keluhan: Mesin agak tersendat saat langsam, cek throttle body dan busi.',
        ]);

        // Antrean 5: A-005 (Menunggu Giliran - waiting)
        Queue::create([
            'nomor_antrean' => 'A-005',
            'kendaraan_id' => $k5->id,
            'service_id' => $srvBan->id,
            'montor_id' => null,
            'status' => 'waiting',
            'catatan_servis' => 'Ganti ban belakang tubeless IRC dan pasang pentil baru.',
        ]);
    }
}
