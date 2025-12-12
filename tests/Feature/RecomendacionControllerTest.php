<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RegistroAgua;
use App\Models\RegistroSueno;
use App\Models\RegistroActividad;
use App\Models\RegistroAlimento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecomendacionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Borramos el setup de Vite porque ya tienes los archivos compilados
    }

    #[Test]
    public function genera_recomendaciones_para_habitos_deficientes()
    {
        $user = User::factory()->create();

        // ESCENARIO:
        // - Agua: 0 litros (No creamos registros) -> Debería pedir beber más.
        // - Sueño: 5 horas (menos de 6) -> Debería pedir rutina.
        // - Actividad: 0 minutos -> Debería pedir moverse más.
        // - Comida: 0 registros -> Debería pedir registrar.

        RegistroSueno::factory()->create([
            'user_id' => $user->id,
            'duracion_minutos' => 300, // 5 horas
            'hora_inicio' => now(),
        ]);

        $response = $this->actingAs($user)
                         ->get(route('recomendaciones.index'));

        $response->assertOk();
        
        // Verificamos el CONTENIDO de los mensajes
        // Accedemos a la variable $recomendaciones que se pasó a la vista
        $recomendaciones = $response->viewData('recomendaciones');

        $this->assertStringContainsString('consumo de agua ha sido bajo', $recomendaciones['agua']);
        $this->assertStringContainsString('no estás durmiendo lo suficiente', $recomendaciones['sueno']);
        $this->assertStringContainsString('Anímate a moverte más', $recomendaciones['actividad']);
        $this->assertStringContainsString('No has registrado alimentos', $recomendaciones['alimentacion']);
    }

#[Test]
    public function genera_felicitaciones_para_habitos_saludables()
    {
        $user = User::factory()->create();

        // 1. AGUA: 2 Litros diarios
        RegistroAgua::factory()->count(7)->create([
            'user_id' => $user->id,
            'cantidad' => 2,
            'unidad' => 'litros',
            'fecha_registro' => now(),
        ]);

        // 2. SUEÑO: 7.5 horas (450 min)
        RegistroSueno::factory()->count(5)->create([
            'user_id' => $user->id,
            'duracion_minutos' => 450,
            'hora_inicio' => now(),
        ]);

        // 3. ACTIVIDAD: (AQUÍ ESTABA EL ERROR)
        // Creamos registros en 4 DÍAS DISTINTOS explícitamente
        // Total: 160 minutos (meta 150) en 4 días (meta 3) -> ¡Excelente!
        RegistroActividad::factory()->create(['user_id' => $user->id, 'duracion_minutos' => 40, 'fecha_registro' => now()]);
        RegistroActividad::factory()->create(['user_id' => $user->id, 'duracion_minutos' => 40, 'fecha_registro' => now()->subDay()]);
        RegistroActividad::factory()->create(['user_id' => $user->id, 'duracion_minutos' => 40, 'fecha_registro' => now()->subDays(2)]);
        RegistroActividad::factory()->create(['user_id' => $user->id, 'duracion_minutos' => 40, 'fecha_registro' => now()->subDays(3)]);

        // 4. ALIMENTACIÓN: Variedad
        RegistroAlimento::factory()->create(['user_id' => $user->id, 'tipo_comida' => 'desayuno']);
        RegistroAlimento::factory()->create(['user_id' => $user->id, 'tipo_comida' => 'almuerzo']);
        RegistroAlimento::factory()->create(['user_id' => $user->id, 'tipo_comida' => 'cena']);

        $response = $this->actingAs($user)
                         ->get(route('recomendaciones.index'));

        $recomendaciones = $response->viewData('recomendaciones');

        $this->assertStringContainsString('Buen trabajo con tu hidratación', $recomendaciones['agua']);
        $this->assertStringContainsString('patrón de sueño es bueno', $recomendaciones['sueno']);
        
        // Ahora sí debe salir "Excelente" porque hay actividad en 4 días distintos
        $this->assertStringContainsString('Excelente nivel de actividad', $recomendaciones['actividad']);
        
        $this->assertStringContainsString('buena variedad', $recomendaciones['alimentacion']);
    }

    #[Test]
    public function detecta_actividad_suficiente_pero_poca_frecuencia()
    {
        $user = User::factory()->create();

        // ESCENARIO:
        // Hace 200 minutos de ejercicio (cumple la meta de 150)
        // PERO lo hace todo en UN solo día (fecha_registro igual)
        RegistroActividad::factory()->create([
            'user_id' => $user->id,
            'duracion_minutos' => 200,
            'fecha_registro' => now(),
        ]);

        $response = $this->actingAs($user)
                         ->get(route('recomendaciones.index'));

        $recomendaciones = $response->viewData('recomendaciones');

        // Debería sugerir distribuir el ejercicio
        $this->assertStringContainsString('intenta distribuirlos en más días', $recomendaciones['actividad']);
    }

    #[Test]
    public function detecta_exceso_de_sueno()
    {
        $user = User::factory()->create();

        // ESCENARIO: Duerme 10 horas (600 minutos)
        // Meta es 7. Rango aceptable hasta 8. 10 es exceso.
        RegistroSueno::factory()->count(3)->create([
            'user_id' => $user->id,
            'duracion_minutos' => 600,
            'hora_inicio' => now(),
        ]);

        $response = $this->actingAs($user)
                         ->get(route('recomendaciones.index'));

        $recomendaciones = $response->viewData('recomendaciones');

        $this->assertStringContainsString('durmiendo más de lo recomendado', $recomendaciones['sueno']);
    }
}