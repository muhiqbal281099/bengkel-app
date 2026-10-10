<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@bengkel.com'],
            [
                'name' => 'Admin Gudang',
                'password' => bcrypt('password'),
                'role' => 'admin_gudang',
            ]
        );

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@bengkel.com'],
            [
                'name' => 'Kasir Utama',
                'password' => bcrypt('password'),
                'role' => 'kasir',
            ]
        );

        $petugas = User::firstOrCreate(
            ['email' => 'petugas@bengkel.com'],
            [
                'name' => 'Rina (Frontdesk)',
                'password' => bcrypt('password'),
                'role' => 'petugas',
            ]
        );

        $montir = User::firstOrCreate(
            ['email' => 'montir@bengkel.com'],
            [
                'name' => 'Budi Santoso (Mekanik)',
                'password' => bcrypt('password'),
                'role' => 'montor',
            ]
        );

        $pelanggan1 = User::firstOrCreate(
            ['email' => 'ahmad@gmail.com'],
            ['name' => 'Ahmad Fauzi', 'password' => bcrypt('password'), 'role' => 'pelanggan']
        );

        $pelanggan2 = User::firstOrCreate(
            ['email' => 'siti@gmail.com'],
            ['name' => 'Siti Rahma', 'password' => bcrypt('password'), 'role' => 'pelanggan']
        );

        $pelanggan3 = User::firstOrCreate(
            ['email' => 'doni@gmail.com'],
            ['name' => 'Doni Pratama', 'password' => bcrypt('password'), 'role' => 'pelanggan']
        );

        // 2. Kendaraan Pelanggan
        $k1 = Kendaraan::firstOrCreate(
            ['no_polisi' => 'B 3456 KAA'],
            ['user_id' => $pelanggan1->id, 'merk' => 'Honda', 'model' => 'Vario 160 CBS', 'tahun' => 2022]
        );
        $k2 = Kendaraan::firstOrCreate(
            ['no_polisi' => 'B 6789 TGH'],
            ['user_id' => $pelanggan2->id, 'merk' => 'Yamaha', 'model' => 'NMAX 155 Connected', 'tahun' => 2023]
        );
        $k3 = Kendaraan::firstOrCreate(
            ['no_polisi' => 'B 1234 BCD'],
            ['user_id' => $pelanggan3->id, 'merk' => 'Honda', 'model' => 'Beat ESP Street', 'tahun' => 2021]
        );

        // 3. Services
        $services = [
            ['nama_layanan' => 'Ganti Oli Rutin', 'deskripsi' => 'Kuras oli mesin dan penggantian oli baru + cek tekanan angin ban.', 'biaya' => 15000, 'durasi_estimasi' => 15],
            ['nama_layanan' => 'Servis Ringan (Tune Up)', 'deskripsi' => 'Pembersihan filter udara, cek busi, pembersihan throttle body/karbu, dan setel gas.', 'biaya' => 45000, 'durasi_estimasi' => 30],
            ['nama_layanan' => 'Servis CVT Matic Lengkap', 'deskripsi' => 'Bongkar transmisi CVT, bersihkan pulley & mangkok ganda, ganti grease khusus.', 'biaya' => 40000, 'durasi_estimasi' => 35],
            ['nama_layanan' => 'Ganti Kampas Rem', 'deskripsi' => 'Pemasangan kampas rem depan/belakang serta bleeding minyak rem.', 'biaya' => 20000, 'durasi_estimasi' => 20],
            ['nama_layanan' => 'Overhaul / Turun Mesin', 'deskripsi' => 'Bongkar blok mesin, skir klep, ganti ring piston, dan kalibrasi kompresi.', 'biaya' => 250000, 'durasi_estimasi' => 180],
        ];

        foreach ($services as $srv) {
            Service::firstOrCreate(['nama_layanan' => $srv['nama_layanan']], $srv);
        }

        // 4. Spareparts
        $spareparts = [
            ['kode_barang' => 'SP-OLI-001', 'nama_barang' => 'Oli Mesin Yamalube Silver 0.8L', 'kategori' => 'Oli & Pelumas', 'harga_jual' => 48000, 'stok' => 24],
            ['kode_barang' => 'SP-OLI-002', 'nama_barang' => 'Oli Mesin AHM MPX 2 Matic 0.8L', 'kategori' => 'Oli & Pelumas', 'harga_jual' => 52000, 'stok' => 30],
            ['kode_barang' => 'SP-OLI-003', 'nama_barang' => 'Oli Gardan Yamalube Matic 100ml', 'kategori' => 'Oli & Pelumas', 'harga_jual' => 18000, 'stok' => 15],
            ['kode_barang' => 'SP-OLI-004', 'nama_barang' => 'Oli Mesin Shell Advance AX7 10W-40', 'kategori' => 'Oli & Pelumas', 'harga_jual' => 65000, 'stok' => 4],
            ['kode_barang' => 'SP-REM-001', 'nama_barang' => 'Kampas Rem Cakram Depan Honda Beat/Vario', 'kategori' => 'Pengereman', 'harga_jual' => 38000, 'stok' => 12],
            ['kode_barang' => 'SP-REM-002', 'nama_barang' => 'Kampas Rem Belakang Tromol Matic', 'kategori' => 'Pengereman', 'harga_jual' => 35000, 'stok' => 8],
            ['kode_barang' => 'SP-LIS-001', 'nama_barang' => 'Busi NGK CPR9EA-9 Standar', 'kategori' => 'Kelistrikan', 'harga_jual' => 25000, 'stok' => 18],
            ['kode_barang' => 'SP-LIS-002', 'nama_barang' => 'Aki Kering GS Astra GTZ5S Maintenance Free', 'kategori' => 'Kelistrikan', 'harga_jual' => 235000, 'stok' => 3],
            ['kode_barang' => 'SP-TRN-001', 'nama_barang' => 'Drive Belt / V-Belt Kit Scoopy ESP', 'kategori' => 'Transmisi', 'harga_jual' => 145000, 'stok' => 6],
            ['kode_barang' => 'SP-TRN-002', 'nama_barang' => 'Paket Rantai & Gir Supra X 125', 'kategori' => 'Transmisi', 'harga_jual' => 185000, 'stok' => 2],
            ['kode_barang' => 'SP-TRN-003', 'nama_barang' => 'Roller CVT Standar Beat FI (Set)', 'kategori' => 'Transmisi', 'harga_jual' => 45000, 'stok' => 0],
            ['kode_barang' => 'SP-BAN-001', 'nama_barang' => 'Ban Luar Tubeless IRC 90/90-14', 'kategori' => 'Ban & Roda', 'harga_jual' => 210000, 'stok' => 7],
        ];

        foreach ($spareparts as $sp) {
            Sparepart::firstOrCreate(['kode_barang' => $sp['kode_barang']], $sp);
        }

        // 5. Antrean Hari Ini
        $srvOli = Service::where('nama_layanan', 'Ganti Oli Rutin')->first();
        $srvCvt = Service::where('nama_layanan', 'Servis CVT Matic Lengkap')->first();
        $srvRem = Service::where('nama_layanan', 'Ganti Kampas Rem')->first();

        Queue::firstOrCreate(
            ['nomor_antrean' => 'A-001'],
            [
                'kendaraan_id' => $k1->id,
                'service_id' => $srvOli->id,
                'montor_id' => $montir->id,
                'status' => 'in_service',
                'catatan_servis' => 'Keluhan: Tarikan mesin agak berat, ganti oli mesin dan gardan.',
            ]
        );

        Queue::firstOrCreate(
            ['nomor_antrean' => 'A-002'],
            [
                'kendaraan_id' => $k2->id,
                'service_id' => $srvCvt->id,
                'montor_id' => $montir->id,
                'status' => 'checking',
                'catatan_servis' => 'Keluhan: Gredek di tarikan awal kecepatan rendah.',
            ]
        );

        Queue::firstOrCreate(
            ['nomor_antrean' => 'A-003'],
            [
                'kendaraan_id' => $k3->id,
                'service_id' => $srvRem->id,
                'montor_id' => $montir->id,
                'status' => 'waiting',
                'catatan_servis' => 'Keluhan: Rem depan bunyi mendecit saat ditekan.',
            ]
        );
    }
}
