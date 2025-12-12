<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistroActividad;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test; // Importante para la nueva sintaxis
use Tests\TestCase;

class RegistroActividadControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usuarios_no_autenticados_no_pueden_ver_el_registro_de_actividad()
    {
        $response = $this->get(route('habitos.actividad'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function un_usuario_puede_ver_la_pagina_con_su_historial()
    {
        $user = User::factory()->create();

        RegistroActividad::factory()->count(3)->create([
            'user_id' => $user->id,
            'fecha_registro' => now()
        ]);

        $response = $this->actingAs($user)
                         ->get(route('habitos.actividad'));

        $response->assertStatus(200);
        $response->assertViewIs('habitos.actividad');
        $response->assertViewHas('registrosActividad');
    }

    #[Test]
    public function un_usuario_puede_guardar_una_nueva_actividad()
    {
        $user = User::factory()->create();

        $datosFormulario = [
            'tipo_actividad' => 'Crossfit',
            'duracion_actividad' => 45,
            'intensidad_actividad' => 'alta',
        ];

        $response = $this->actingAs($user)
                         ->post(route('habitos.actividad.store'), $datosFormulario);

        $response->assertRedirect(route('habitos.actividad'));
        $response->assertSessionHas('success', '¡Actividad física registrada exitosamente!');

        $this->assertDatabaseHas('registros_actividad', [
            'user_id' => $user->id,
            'tipo_actividad' => 'Crossfit',
            'duracion_minutos' => 45,
            'intensidad' => 'alta',
            'fecha_registro' => now()->toDateString(),
        ]);
    }

    #[Test]
    public function validacion_impide_datos_incorrectos()
    {
        $user = User::factory()->create();

        $datosInvalidos = [
            'tipo_actividad' => '',
            'duracion_actividad' => 'texto_no_numero',
            'intensidad_actividad' => 'extrema',
        ];

        $response = $this->actingAs($user)
                         ->post(route('habitos.actividad.store'), $datosInvalidos);

        $response->assertSessionHasErrors([
            'tipo_actividad',
            'duracion_actividad',
            'intensidad_actividad'
        ]);
    }
}