<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BengkelSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bengkel',
        'alamat_bengkel',
        'telepon_bengkel',
        'logo_path',
        'running_text',
    ];

    private static ?self $cachedInstance = null;

    /**
     * Dapatkan instance konfigurasi bengkel tunggal (singleton record).
     */
    public static function getSettings(): self
    {
        if (static::$cachedInstance !== null) {
            return static::$cachedInstance;
        }

        $attributes = Cache::remember('bengkel_settings_data', 3600, function () {
            $record = self::firstOrCreate(
                ['id' => 1],
                [
                    'nama_bengkel' => 'Bengkel Pro Motor & Mobil',
                    'alamat_bengkel' => 'Jl. Raya Otomotif No. 88, Pusat Servis Kendaraan',
                    'telepon_bengkel' => '0812-3456-7890 / (021) 555-8989',
                    'logo_path' => null,
                    'running_text' => 'Selamat datang di Bengkel Pro! Servis profesional, transparan, dan bergaransi.',
                ]
            );

            return $record->getAttributes();
        });

        $model = new self;
        $model->setRawAttributes($attributes, true);
        $model->exists = true;

        return static::$cachedInstance = $model;
    }

    /**
     * Hapus cache setiap kali settings disimpan.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            static::$cachedInstance = null;
            Cache::forget('bengkel_settings_data');
        });
    }

    /**
     * Dapatkan URL logo atau null jika belum diupload.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }

        return null;
    }
}
