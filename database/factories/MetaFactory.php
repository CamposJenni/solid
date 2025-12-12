<?php

namespace Database\Factories;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetaFactory extends Factory
{
    protected $model = Meta::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tipo_habito' => $this->faker->randomElement(['agua', 'sueno', 'actividad', 'alimentacion']),
            'objetivo' => $this->faker->sentence(3),
            'unidad' => $this->faker->word(), // Ej: "litros", "pasos"
            'fecha_inicio' => now(),
            'fecha_fin' => $this->faker->dateTimeBetween('now', '+1 month'),
            'completada' => false,
        ];
    }
}