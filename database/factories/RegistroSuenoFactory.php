<?php

namespace Database\Factories;

use App\Models\RegistroSueno;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class RegistroSuenoFactory extends Factory
{
    protected $model = RegistroSueno::class;

    public function definition(): array
    {
        // Generamos una fecha de inicio aleatoria reciente
        $inicio = Carbon::instance($this->faker->dateTimeBetween('-1 month', 'now'));
        
        // Clonamos la fecha y le sumamos entre 4 y 10 horas para el fin
        $fin = (clone $inicio)->addMinutes($this->faker->numberBetween(240, 600));

        return [
            'user_id' => User::factory(),
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
            'duracion_minutos' => $fin->diffInMinutes($inicio),
        ];
    }
}