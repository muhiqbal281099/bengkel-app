<?php

namespace Tests\Feature;

use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirPOSTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    private User $admin;

    private Queue $queue;

    private Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasir = User::factory()->create(['role' => 'kasir']);
        $this->admin = User::factory()->create(['role' => 'admin_gudang']);

        $customer = User::factory()->create(['role' => 'pelanggan']);
        $kendaraan = Kendaraan::create([
            'user_id' => $customer->id,
            'no_polisi' => 'B 9999 XYZ',
            'merk' => 'Honda',
            'model' => 'PCX 160',
            'tahun' => 2023,
        ]);

        $service = Service::create([
            'nama_layanan' => 'Tune Up Racing',
            'biaya' => 50000,
            'durasi_estimasi' => 30,
        ]);

        $this->queue = Queue::create([
            'nomor_antrean' => 'A-099',
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $service->id,
            'status' => 'in_service',
        ]);

        $this->sparepart = Sparepart::create([
            'kode_barang' => 'SP-TEST-01',
            'nama_barang' => 'Oli Mesin Test Super',
            'kategori' => 'Oli & Pelumas',
            'harga_jual' => 60000,
            'stok' => 10,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('kasir.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_kasir_panel(): void
    {
        $response = $this->actingAs($this->admin)->get(route('kasir.dashboard'));
        $response->assertStatus(403);
    }

    public function test_kasir_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('kasir.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Loket Pembayaran');
        $response->assertSee('B 9999 XYZ');
    }

    public function test_kasir_can_open_pos_form(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('kasir.pos', ['queue_id' => $this->queue->id]));
        $response->assertStatus(200);
        $response->assertSee('Point of Sales');
        $response->assertSee('B 9999 XYZ');
        $response->assertSee('Oli Mesin Test Super');
    }

    public function test_kasir_can_process_transaction_and_stock_is_reduced(): void
    {
        $payload = [
            'queue_id' => $this->queue->id,
            'metode_pembayaran' => 'cash',
            'nominal_bayar' => 200000,
            'items' => [
                [
                    'sparepart_id' => $this->sparepart->id,
                    'qty' => 2,
                ],
            ],
        ];

        // Biaya: Jasa 50.000 + Sparepart (60.000 * 2 = 120.000) = 170.000
        $response = $this->actingAs($this->kasir)->post(route('kasir.pos.store'), $payload);

        $transaction = Transaction::where('queue_id', $this->queue->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(170000, $transaction->total_pembayaran);

        $response->assertRedirect(route('kasir.transactions.receipt', $transaction));

        // Cek stok berkurang dari 10 menjadi 8
        $this->assertEquals(8, $this->sparepart->fresh()->stok);

        // Cek status antrean berubah menjadi 'completed'
        $this->assertEquals('completed', $this->queue->fresh()->status);
    }

    public function test_kasir_can_view_receipt(): void
    {
        $transaction = Transaction::create([
            'queue_id' => $this->queue->id,
            'kasir_id' => $this->kasir->id,
            'total_biaya_jasa' => 50000,
            'total_biaya_sparepart' => 0,
            'total_pembayaran' => 50000,
            'metode_pembayaran' => 'transfer',
        ]);

        $response = $this->actingAs($this->kasir)->get(route('kasir.transactions.receipt', $transaction));
        $response->assertStatus(200);
        $response->assertSee('NOTA-'.str_pad($transaction->id, 5, '0', STR_PAD_LEFT));
        $response->assertSee('B 9999 XYZ');
    }

    public function test_kasir_can_view_transaction_history(): void
    {
        Transaction::create([
            'queue_id' => $this->queue->id,
            'kasir_id' => $this->kasir->id,
            'total_biaya_jasa' => 50000,
            'total_biaya_sparepart' => 0,
            'total_pembayaran' => 50000,
            'metode_pembayaran' => 'qris',
        ]);

        $response = $this->actingAs($this->kasir)->get(route('kasir.transactions.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat & Laporan Transaksi Kasir', false);
    }
}
