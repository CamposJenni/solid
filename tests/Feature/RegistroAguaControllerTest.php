<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistroAgua;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistroAguaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // Previene errores de frontend
    }

    #[Test]
    public function usuarios_no_autenticados_son_redirigidos_al_login()
    {
        $response = $this->get(route('habitos.agua'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function un_usuario_puede_ver_su_historial_de_agua()
    {
        $user = User::factory()->create();

        // Creamos 3 registros de agua para este usuario
        RegistroAgua::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)
                         ->get(route('habitos.agua'));

        $response->assertStatus(200);
        $response->assertViewIs('habitos.agua');
        // Verificamos que la variable llegue a la vista
        $response->assertViewHas('registrosAgua');
    }

    #[Test]
    public function un_usuario_puede_registrar_consumo_de_agua()
    {
        $user = User::factory()->create();

        // Datos simulan el formulario HTML (nombres con guion bajo)
        $datosFormulario = [
            'cantidad_agua' => 500,
            'unidad_agua' => 'ml',
        ];

        $response = $this->actingAs($user)
                         ->post(route('habitos.agua.store'), $datosFormulario);

        $response->assertRedirect(route('habitos.agua'));
        $response->assertSessionHas('success', '¡Consumo de agua registrado exitosamente!');

        // Verificamos en la BD (nombres de columnas reales de tu tabla)
        $this->assertDatabaseHas('registros_agua', [
            'user_id' => $user->id,
            'cantidad' => 500,    // El controller mapeó cantidad_agua -> cantidad
            'unidad' => 'ml',     // El controller mapeó unidad_agua -> unidad
            'fecha_registro' => now()->toDateString(),
        ]);
    }

    #[Test]
    public function validacion_impide_datos_incorrectos()
    {
        $user = User::factory()->create();

        $datosInvalidos = [
            'cantidad_agua' => -50,       // No puede ser negativo
            'unidad_agua' => 'botellas',  // No está en la lista (ml, litros, vasos)
        ];
        #ACCION
        $response = $this->actingAs($user)
                         ->post(route('habitos.agua.store'), $datosInvalidos);

        $response->assertSessionHasErrors([
            'cantidad_agua',
            'unidad_agua'
        ]);
    }
}