<?php

namespace Database\Factories;

use App\Models\RegistroAgua;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistroAguaFactory extends Factory
{
    protected $model = RegistroAgua::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            // Usamos valores que pasen tu validación (numeric y > 0)
            'cantidad' => $this->faker->numberBetween(100, 2000), 
            // Usamos una de las unidades permitidas en tu validación
            'unidad' => $this->faker->randomElement(['ml', 'litros', 'vasos']),
            'fecha_registro' => now(),
        ];
    }
}