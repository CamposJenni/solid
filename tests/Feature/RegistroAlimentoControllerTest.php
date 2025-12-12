<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistroAlimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistroAlimentoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // Para evitar errores de frontend
    }

    #[Test]
    public function usuarios_no_autenticados_no_pueden_ver_alimentacion()
    {
        $this->get(route('habitos.alimentacion'))
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_ve_su_historial_de_alimentacion()
    {
        $user = User::factory()->create();

        // Creamos 3 registros falsos
        RegistroAlimento::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
                         ->get(route('habitos.alimentacion'));

        $response->assertOk();
        $response->assertViewIs('habitos.alimentacion');
        $response->assertViewHas('registrosAlimento');
    }

    #[Test]
    public function guarda_un_registro_de_alimento_correctamente()
    {
        $user = User::factory()->create();

        // Datos simulan el formulario (nombres de inputs HTML)
        $datosFormulario = [
            'tipo_comida' => 'almuerzo',
            'detalles_comida' => 'Pollo a la brasa con ensalada', // Input se llama 'detalles_comida'
        ];

        $this->actingAs($user)
             ->post(route('habitos.alimentacion.store'), $datosFormulario)
             ->assertRedirect(route('habitos.alimentacion'))
             ->assertSessionHas('success', '¡Registro de alimentación exitoso!');

        // Verificamos en la BD (nombres de columnas reales)
        $this->assertDatabaseHas('registros_alimento', [
            'user_id' => $user->id,
            'tipo_comida' => 'almuerzo',
            'detalles' => 'Pollo a la brasa con ensalada', // Controller mapeó esto correctamente
            'fecha_registro' => now()->toDateString(),
        ]);
    }

    #[Test]
    public function validacion_rechaza_datos_incorrectos()
    {
        $user = User::factory()->create();

        $datosMalos = [
            'tipo_comida' => 'banquete', // Valor no permitido (solo desayuno, almuerzo, cena, snack)
            'detalles_comida' => '',     // Campo obligatorio vacío
        ];

        $this->actingAs($user)
             ->post(route('habitos.alimentacion.store'), $datosMalos)
             ->assertSessionHasErrors([
                 'tipo_comida', 
                 'detalles_comida'
             ]);
    }
}