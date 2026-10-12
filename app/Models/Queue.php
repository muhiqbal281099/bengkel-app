<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_antrean',
        'kendaraan_id',
        'service_id',
        'montor_id',
        'status',
        'catatan_servis',
    ];

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function montor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'montor_id');
    }

    public function montir(): BelongsTo
    {
        return $this->montor();
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
