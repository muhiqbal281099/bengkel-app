<?php

namespace Database\Factories;

use App\Models\Sparepart;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sparepart>
 */
class SparepartFactory extends Factory
{
    protected $model = Sparepart::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_barang' => 'SP-'.strtoupper(fake()->unique()->bothify('???-###')),
            'nama_barang' => fake()->words(3, true),
            'kategori' => fake()->randomElement(['Oli & Pelumas', 'Pengereman', 'Kelistrikan', 'Transmisi', 'Ban & Roda']),
            'harga_jual' => fake()->numberBetween(15000, 350000),
            'stok' => fake()->numberBetween(0, 50),
        ];
    }
}
