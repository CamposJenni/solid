<?php

namespace Database\Factories;

use App\Models\RegistroActividad;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistroActividadFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente al factory.
     */
    protected $model = RegistroActividad::class;

    /**
     * Define el estado por defecto del modelo.
     */
    public function definition(): array
    {
        return [
            // Creamos un usuario automáticamente si no se especifica uno
            'user_id' => User::factory(),
            
            // Datos aleatorios para simular actividades
            'tipo_actividad' => $this->faker->randomElement(['Correr', 'Nadar', 'Caminar', 'Gimnasio']),
            'duracion_minutos' => $this->faker->numberBetween(15, 90),
            'intensidad' => $this->faker->randomElement(['baja', 'media', 'alta']),
            'fecha_registro' => now(), // O $this->faker->date()
        ];
    }
}