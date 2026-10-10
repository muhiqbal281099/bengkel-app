<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MontirTest extends TestCase
{
    use RefreshDatabase;

    private User $montir;

    private User $otherMontir;

    private Queue $queue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->montir = User::factory()->create(['role' => 'montor']);
        $this->otherMontir = User::factory()->create(['role' => 'montor']);

        $customer = User::factory()->create(['role' => 'pelanggan']);
        $kendaraan = Kendaraan::create([
            'user_id' => $customer->id,
            'no_polisi' => 'B 1111 MNT',
            'merk' => 'Yamaha',
            'model' => 'Aerox',
            'tahun' => 2022,
        ]);

        $service = Service::create([
            'nama_layanan' => 'Servis Injeksi',
            'biaya' => 80000,
            'durasi_estimasi' => 60,
        ]);

        $this->queue = Queue::create([
            'nomor_antrean' => 'A-010',
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $service->id,
            'montor_id' => $this->montir->id,
            'status' => 'waiting',
        ]);
    }

    public function test_guest_cannot_access_montir_dashboard(): void
    {
        $response = $this->get(route('montir.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_other_roles_cannot_access_montir_dashboard(): void
    {
        $kasir = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($kasir)->get(route('montir.dashboard'));
        $response->assertStatus(403);
    }

    public function test_montir_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->montir)->get(route('montir.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tugas Servis Saya');
        $response->assertSee('A-010');
    }

    public function test_montir_can_update_status_of_their_assigned_queue(): void
    {
        $response = $this->actingAs($this->montir)->patch(
            route('montir.antrean.update-status', $this->queue),
            [
                'status' => 'in_service',
                'catatan_servis' => 'Sedang membongkar CVT dan injector',
            ]
        );

        $response->assertRedirect(route('montir.dashboard'));
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue->id,
            'status' => 'in_service',
            'catatan_servis' => 'Sedang membongkar CVT dan injector',
        ]);
    }

    public function test_montir_cannot_update_queue_assigned_to_another_montir(): void
    {
        $response = $this->actingAs($this->otherMontir)->patch(
            route('montir.antrean.update-status', $this->queue),
            [
                'status' => 'completed',
            ]
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue->id,
            'status' => 'waiting',
        ]);
    }

    public function test_montir_can_mark_queue_as_completed(): void
    {
        $response = $this->actingAs($this->montir)->patch(
            route('montir.antrean.update-status', $this->queue),
            [
                'status' => 'completed',
                'catatan_servis' => 'Pembersihan injector & penggantian oli selesai',
            ]
        );

        $response->assertRedirect(route('montir.dashboard'));
        $this->assertDatabaseHas('queues', [
            'id' => $this->queue->id,
            'status' => 'completed',
        ]);
    }
}
