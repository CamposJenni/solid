<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistroSueno;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Carbon\Carbon;

class RegistroSuenoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    #[Test]
    public function usuarios_no_autenticados_no_pueden_ver_sueno()
    {
        $this->get(route('habitos.sueno'))
             ->assertRedirect(route('login'));
    }

    #[Test]
    public function usuario_ve_historial_de_sueno()
    {
        $user = User::factory()->create();
        
        RegistroSueno::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                         ->get(route('habitos.sueno'));

        $response->assertOk(); // Es lo mismo que assertStatus(200)
        $response->assertViewIs('habitos.sueno');
        $response->assertViewHas('registrosSueno');
    }

    #[Test]
    public function guarda_un_registro_de_sueno_normal()
    {
        $user = User::factory()->create();

        // Escenario: Duerme siesta de 2pm a 4pm el mismo día
        $datos = [
            'fecha_inicio_sueno' => '2025-05-10',
            'hora_inicio_sueno' => '14:00',
            'fecha_fin_sueno' => '2025-05-10',
            'hora_fin_sueno' => '16:00',
        ];

        $this->actingAs($user)
             ->post(route('habitos.sueno.store'), $datos)
             ->assertRedirect(route('habitos.sueno'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('registros_sueno', [
            'user_id' => $user->id,
            'duracion_minutos' => 120, // 2 horas exactas
        ]);
    }

    #[Test]
    public function detecta_correctamente_el_cambio_de_dia()
    {
        // ESTA ES LA PRUEBA DE TU LÓGICA INTELIGENTE
        $user = User::factory()->create();

        // Escenario: El usuario pone que durmió a las 23:00 y despertó a las 07:00.
        // AUNQUE ponga la misma fecha en ambos campos (error común de usuario),
        // tu controlador debería sumar un día a la fecha final.
        $datos = [
            'fecha_inicio_sueno' => '2025-05-10',
            'hora_inicio_sueno' => '23:00',
            'fecha_fin_sueno' => '2025-05-10', // El usuario olvidó poner día 11
            'hora_fin_sueno' => '07:00',
        ];

        $this->actingAs($user)
             ->post(route('habitos.sueno.store'), $datos);

        // Verificamos que tu lógica calculó 8 horas (480 mins) y no un número negativo
        $this->assertDatabaseHas('registros_sueno', [
            'user_id' => $user->id,
            'duracion_minutos' => 480, 
        ]);
        
        // Opcional: Verificar que la fecha guardada en BD sea realmente el día 11
        $registro = RegistroSueno::latest()->first();
        // createFromFormat es necesario porque la BD devuelve string
        $fechaGuardada = Carbon::parse($registro->hora_fin); 
        
        $this->assertEquals(11, $fechaGuardada->day);
    }

    #[Test]
    public function validacion_falla_con_formatos_incorrectos()
    {
        $user = User::factory()->create();

        $datosMalos = [
            'fecha_inicio_sueno' => 'no-es-fecha',
            'hora_inicio_sueno' => '25:99', // Hora imposible
            'fecha_fin_sueno' => '',
            'hora_fin_sueno' => 'texto',
        ];

        $this->actingAs($user)
             ->post(route('habitos.sueno.store'), $datosMalos)
             ->assertSessionHasErrors([
                 'fecha_inicio_sueno', 
                 'hora_inicio_sueno', 
                 'fecha_fin_sueno', 
                 'hora_fin_sueno'
             ]);
    }
}