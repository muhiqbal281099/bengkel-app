<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasFrontdeskTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    private User $montir;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = User::factory()->create(['role' => 'petugas']);
        $this->montir = User::factory()->create(['role' => 'montor']);

        $this->service = Service::create([
            'nama_layanan' => 'Servis Berkala Standar',
            'biaya' => 65000,
            'durasi_estimasi' => 45,
        ]);
    }

    public function test_guest_cannot_access_petugas_dashboard(): void
    {
        $response = $this->get(route('petugas.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_other_roles_cannot_access_petugas_dashboard(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get(route('petugas.dashboard'));
        $response->assertStatus(403);
    }

    public function test_petugas_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Antrean Hari Ini');
    }

    public function test_petugas_can_view_create_queue_form(): void
    {
        $response = $this->actingAs($this->petugas)->get(route('petugas.antrean.create'));

        $response->assertStatus(200);
        $response->assertSee('Daftarkan Kendaraan ke Antrean');
    }

    public function test_petugas_can_register_queue_for_existing_kendaraan(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $kendaraan = Kendaraan::create([
            'user_id' => $customer->id,
            'no_polisi' => 'B 1234 ABC',
            'merk' => 'Yamaha',
            'model' => 'NMAX',
            'tahun' => 2022,
        ]);

        $payload = [
            'mode' => 'existing',
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $this->service->id,
            'montor_id' => $this->montir->id,
            'catatan_servis' => 'Ganti oli dan cek rem depan',
        ];

        $response = $this->actingAs($this->petugas)->post(route('petugas.antrean.store'), $payload);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertDatabaseHas('queues', [
            'nomor_antrean' => 'A-001',
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $this->service->id,
            'montor_id' => $this->montir->id,
            'status' => 'waiting',
        ]);
    }

    public function test_petugas_can_register_walkin_customer_and_kendaraan(): void
    {
        $payload = [
            'mode' => 'new',
            'pelanggan_nama' => 'Budi Santoso',
            'no_polisi' => 'D 5678 XYZ',
            'merk' => 'Honda',
            'model_kendaraan' => 'Vario 160',
            'tahun' => 2023,
            'service_id' => $this->service->id,
            'montor_id' => $this->montir->id,
            'catatan_servis' => 'Bunyi decit pada roda belakang',
        ];

        $response = $this->actingAs($this->petugas)->post(route('petugas.antrean.store'), $payload);

        $response->assertRedirect(route('petugas.dashboard'));

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'role' => 'pelanggan',
        ]);

        $this->assertDatabaseHas('kendaraans', [
            'no_polisi' => 'D 5678 XYZ',
            'merk' => 'Honda',
            'model' => 'Vario 160',
        ]);

        $this->assertDatabaseHas('queues', [
            'nomor_antrean' => 'A-001',
            'service_id' => $this->service->id,
            'status' => 'waiting',
        ]);
    }

    public function test_petugas_can_update_queue_status(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $kendaraan = Kendaraan::create([
            'user_id' => $customer->id,
            'no_polisi' => 'B 8888 TTT',
            'merk' => 'Suzuki',
            'model' => 'Address',
            'tahun' => 2021,
        ]);

        $queue = Queue::create([
            'nomor_antrean' => 'A-001',
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $this->service->id,
            'status' => 'waiting',
        ]);

        $response = $this->actingAs($this->petugas)->patch(route('petugas.antrean.update-status', $queue), [
            'status' => 'checking',
            'montor_id' => $this->montir->id,
            'catatan_servis' => 'Sedang dicek oleh frontdesk',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('queues', [
            'id' => $queue->id,
            'status' => 'checking',
            'montor_id' => $this->montir->id,
        ]);
    }
}
