<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Meta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MetaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    #[Test]
    public function usuarios_no_autenticados_no_pueden_ver_metas()
    {
        $this->get(route('metas.index'))
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_puede_ver_sus_metas()
    {
        $user = User::factory()->create();

        // Creamos 3 metas para el usuario
        Meta::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                         ->get(route('metas.index'));

        $response->assertOk();
        $response->assertViewIs('metas.index');
        $response->assertViewHas('metas');
    }

    #[Test]
    public function guarda_una_meta_correctamente()
    {
        $user = User::factory()->create();

        // Datos del FORMULARIO (con prefijo meta_)
        $datosFormulario = [
            'meta_tipo_habito' => 'actividad',
            'meta_objetivo' => 'Correr 5km diarios',
            'meta_unidad' => 'km',
            'fecha_fin_meta' => now()->addMonth()->toDateString(), // Fecha futura válida
        ];

        $this->actingAs($user)
             ->post(route('metas.store'), $datosFormulario)
             ->assertRedirect(route('metas.index'))
             ->assertSessionHas('success', '¡Meta establecida exitosamente!');

        // Verificamos en la BASE DE DATOS (sin prefijo meta_)
        $this->assertDatabaseHas('metas', [
            'user_id' => $user->id,
            'tipo_habito' => 'actividad',         // Mapeo correcto
            'objetivo' => 'Correr 5km diarios',   // Mapeo correcto
            'unidad' => 'km',                     // Mapeo correcto
            'fecha_inicio' => now()->toDateString(),
            'completada' => false,                // Verificar valor por defecto
        ]);
    }

    #[Test]
    public function validacion_impide_fechas_pasadas_y_datos_vacios()
    {
        $user = User::factory()->create();

        $datosInvalidos = [
            'meta_tipo_habito' => '', // Vacío
            'meta_objetivo' => '',    // Vacío
            // Intentamos poner una fecha de fin en el pasado (ayer)
            'fecha_fin_meta' => now()->subDay()->toDateString(), 
        ];

        $this->actingAs($user)
             ->post(route('metas.store'), $datosInvalidos)
             ->assertSessionHasErrors([
                 'meta_tipo_habito',
                 'meta_objetivo',
                 'fecha_fin_meta', // Debe fallar porque es ayer
             ]);
    }
}