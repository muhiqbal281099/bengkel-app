<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'biaya',
        'durasi_estimasi',
    ];

    /**
     * Get the queues for the service.
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class);
    }
}
