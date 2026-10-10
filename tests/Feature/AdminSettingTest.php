<?php

namespace Tests\Feature;

use App\Models\BengkelSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $kasir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin_gudang']);
        $this->kasir = User::factory()->create(['role' => 'kasir']);
    }

    public function test_guest_cannot_access_settings(): void
    {
        $response = $this->get(route('admin.settings.edit'));
        $response->assertRedirect('/login');
    }

    public function test_kasir_cannot_access_settings(): void
    {
        $response = $this->actingAs($this->kasir)->get(route('admin.settings.edit'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_settings_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil & Identitas Bengkel', false);
    }

    public function test_admin_can_update_workshop_profile(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'nama_bengkel' => 'Bengkel Jaya Abadi Motor',
            'alamat_bengkel' => 'Jl. Pahlawan No. 45, Bandung',
            'telepon_bengkel' => '0812-9988-7766',
            'running_text' => 'Promo Diskon Servis 20% Hari Ini!',
        ]);

        $response->assertRedirect(route('admin.settings.edit'));
        $response->assertSessionHas('success');

        $setting = BengkelSetting::getSettings();
        $this->assertEquals('Bengkel Jaya Abadi Motor', $setting->nama_bengkel);
        $this->assertEquals('Jl. Pahlawan No. 45, Bandung', $setting->alamat_bengkel);
    }

    public function test_public_queue_displays_workshop_name_and_address(): void
    {
        BengkelSetting::getSettings()->update([
            'nama_bengkel' => 'Bengkel Sumber Rezeki Official',
            'alamat_bengkel' => 'Jl. Sudirman Kav. 10',
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Bengkel Sumber Rezeki Official');
        $response->assertSee('Jl. Sudirman Kav. 10');
    }
}
