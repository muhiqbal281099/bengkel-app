<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInventoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin_gudang',
        ]);

        $this->kasir = User::factory()->create([
            'role' => 'kasir',
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_non_admin_gets_forbidden(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        Sparepart::factory()->create([
            'nama_barang' => 'Oli Yamalube Test',
            'stok' => 3,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Gudang & Inventaris', false);
        $response->assertSee('Oli Yamalube Test');
    }

    public function test_admin_can_view_spareparts_index_and_filter(): void
    {
        Sparepart::factory()->create([
            'kode_barang' => 'TEST-001',
            'nama_barang' => 'Kampas Rem Depan Test',
            'kategori' => 'Pengereman',
            'stok' => 10,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.spareparts.index', ['q' => 'Kampas']));
        $response->assertStatus(200);
        $response->assertSee('Kampas Rem Depan Test');
    }

    public function test_admin_can_store_sparepart(): void
    {
        $payload = [
            'kode_barang' => 'SP-TEST-999',
            'nama_barang' => 'Busi Iridium Racing Test',
            'kategori' => 'Kelistrikan',
            'harga_jual' => 85000,
            'stok' => 15,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.spareparts.store'), $payload);

        $response->assertRedirect(route('admin.spareparts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('spareparts', [
            'kode_barang' => 'SP-TEST-999',
            'nama_barang' => 'Busi Iridium Racing Test',
        ]);
    }

    public function test_admin_can_update_sparepart(): void
    {
        $item = Sparepart::factory()->create([
            'kode_barang' => 'SP-OLD-01',
            'nama_barang' => 'Ban Lama',
            'kategori' => 'Ban & Roda',
            'harga_jual' => 100000,
            'stok' => 5,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.spareparts.update', $item), [
            'kode_barang' => 'SP-OLD-01',
            'nama_barang' => 'Ban Baru Diperbarui',
            'kategori' => 'Ban & Roda',
            'harga_jual' => 125000,
            'stok' => 8,
        ]);

        $response->assertRedirect(route('admin.spareparts.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $item->id,
            'nama_barang' => 'Ban Baru Diperbarui',
            'harga_jual' => 125000,
            'stok' => 8,
        ]);
    }

    public function test_admin_can_adjust_stock_via_quick_restock(): void
    {
        $item = Sparepart::factory()->create([
            'stok' => 4,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.spareparts.adjust-stock', $item), [
            'adjustment_type' => 'add',
            'amount' => 12,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(16, $item->fresh()->stok);
    }

    public function test_admin_can_delete_unused_sparepart(): void
    {
        $item = Sparepart::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.spareparts.destroy', $item));
        $response->assertRedirect(route('admin.spareparts.index'));
        $this->assertDatabaseMissing('spareparts', ['id' => $item->id]);
    }

    public function test_admin_can_manage_services(): void
    {
        // 1. Create
        $response = $this->actingAs($this->admin)->post(route('admin.services.store'), [
            'nama_layanan' => 'Uji Emisi Gas Buang',
            'biaya' => 60000,
            'durasi_estimasi' => 20,
            'deskripsi' => 'Pengujian standar emisi gas buang.',
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['nama_layanan' => 'Uji Emisi Gas Buang']);

        $service = Service::where('nama_layanan', 'Uji Emisi Gas Buang')->firstOrFail();

        // 2. Update
        $this->actingAs($this->admin)->put(route('admin.services.update', $service), [
            'nama_layanan' => 'Uji Emisi Resmi & Cetak Sertifikat',
            'biaya' => 75000,
            'durasi_estimasi' => 25,
            'deskripsi' => 'Pengujian plus cetak sertifikat.',
        ]);
        $this->assertDatabaseHas('services', ['nama_layanan' => 'Uji Emisi Resmi & Cetak Sertifikat']);

        // 3. Delete
        $this->actingAs($this->admin)->delete(route('admin.services.destroy', $service));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }
}
