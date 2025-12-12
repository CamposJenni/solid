<?php

namespace Database\Factories;

use App\Models\RegistroAlimento;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistroAlimentoFactory extends Factory
{
    protected $model = RegistroAlimento::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            // Elegimos uno de los valores permitidos en tu validación 'in:...'
            'tipo_comida' => $this->faker->randomElement(['desayuno', 'almuerzo', 'cena', 'snack']),
            // Generamos una frase aleatoria para los detalles
            'detalles' => $this->faker->sentence(6),
            'fecha_registro' => now(),
        ];
    }
}