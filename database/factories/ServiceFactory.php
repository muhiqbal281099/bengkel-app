<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_layanan' => fake()->words(3, true),
            'deskripsi' => fake()->sentence(),
            'biaya' => fake()->numberBetween(20000, 200000),
            'durasi_estimasi' => fake()->numberBetween(15, 120),
        ];
    }
}
