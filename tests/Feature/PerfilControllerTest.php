<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerfilControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    #[Test]
    public function usuarios_no_autenticados_no_pueden_editar_perfil()
    {
        // Intentar entrar a la vista de editar
        $this->get(route('perfil.editar'))
             ->assertRedirect(route('login'));
             
        // Intentar enviar datos para actualizar
        $this->put(route('perfil.actualizar'), [])
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_puede_ver_su_formulario_de_perfil()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(route('perfil.editar'));

        $response->assertOk();
        $response->assertViewIs('perfil.editar');
    }

    #[Test]
    public function usuario_puede_actualizar_su_nombre_y_correo()
    {
        $user = User::factory()->create([
            'name' => 'Nombre Viejo',
            'email' => 'viejo@correo.com',
        ]);

        $datosNuevos = [
            'nombre_usuario' => 'Juan Nuevo',
            'correo_electronico' => 'nuevo@correo.com',
            // No enviamos contraseña para probar que no se borre ni cambie
        ];

        $this->actingAs($user)
             ->put(route('perfil.actualizar'), $datosNuevos)
             ->assertRedirect(route('perfil.editar'))
             ->assertSessionHas('success', '¡Perfil actualizado exitosamente!');

        // Verificamos que en la BD se hayan mapeado bien los campos
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Juan Nuevo',       // Mapeado desde 'nombre_usuario'
            'email' => 'nuevo@correo.com', // Mapeado desde 'correo_electronico'
        ]);
    }

    #[Test]
    public function usuario_puede_cambiar_su_contrasena()
    {
        $user = User::factory()->create([
            'password' => Hash::make('clave_anterior'),
        ]);

        $datosCambioPassword = [
            'nombre_usuario' => $user->name,
            'correo_electronico' => $user->email,
            'nueva_contrasena' => 'nueva_secreta_123',
            'nueva_contrasena_confirmation' => 'nueva_secreta_123', // Requerido por la regla 'confirmed'
        ];

        $this->actingAs($user)
             ->put(route('perfil.actualizar'), $datosCambioPassword);

        // Obtenemos al usuario fresco de la BD para verificar su password
        $user->refresh();

        // Verificamos que la nueva contraseña funcione (coincida con el Hash)
        $this->assertTrue(Hash::check('nueva_secreta_123', $user->password));
    }

    #[Test]
    public function no_se_puede_usar_el_correo_de_otro_usuario()
    {
        // Creamos al usuario A (nosotros)
        $userA = User::factory()->create(['email' => 'mi_correo@test.com']);
        
        // Creamos al usuario B (el dueño del correo que queremos robar)
        $userB = User::factory()->create(['email' => 'correo_ocupado@test.com']);

        $datosIntentoRobo = [
            'nombre_usuario' => 'Intento Hack',
            'correo_electronico' => 'correo_ocupado@test.com', // Este email ya existe en UserB
        ];

        $this->actingAs($userA)
             ->put(route('perfil.actualizar'), $datosIntentoRobo)
             ->assertSessionHasErrors(['correo_electronico']); // Debe dar error de "ya está en uso"
    }

    #[Test]
    public function se_permite_mantener_el_mismo_correo_propio()
    {
        // Este test prueba la regla: Rule::unique(...)->ignore($usuario->id)
        $user = User::factory()->create(['email' => 'mi_mismo_correo@test.com']);

        $datosMismoCorreo = [
            'nombre_usuario' => 'Nuevo Nombre',
            'correo_electronico' => 'mi_mismo_correo@test.com', // El mismo que ya tengo
        ];

        $this->actingAs($user)
             ->put(route('perfil.actualizar'), $datosMismoCorreo)
             ->assertSessionHasNoErrors(); // No debería fallar
             
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nuevo Nombre',
        ]);
    }
}