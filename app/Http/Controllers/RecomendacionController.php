<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RegistroAgua;
use App\Models\RegistroSueno;
use App\Models\RegistroActividad;
use App\Models\RegistroAlimento;

class RecomendacionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recomendaciones = [];

        // Definir el rango para el análisis (ej. última semana o 30 días para patrones)
        $fechaHoy = Carbon::now();
        $fechaAnalisis = Carbon::now()->subDays(7); // Analizar la última semana

        // --- Análisis de Agua ---
        $registrosAgua = RegistroAgua::where('user_id', $user->id)
                                    ->whereBetween('fecha_registro', [$fechaAnalisis, $fechaHoy])
                                    ->get();
        $totalAguaMl = 0;
        foreach ($registrosAgua as $registro) {
            if ($registro->unidad === 'litros') {
                $totalAguaMl += $registro->cantidad * 1000;
            } elseif ($registro->unidad === 'vasos') {
                $totalAguaMl += $registro->cantidad * 250; // Asumiendo 250ml/vaso
            } else {
                $totalAguaMl += $registro->cantidad;
            }
        }
        $promedioAguaDiarioMl = $registrosAgua->count() > 0 ? $totalAguaMl / $fechaAnalisis->diffInDays($fechaHoy) : 0;
        $metaAguaMl = 2000; // Meta de ejemplo con 2 litros
        if ($promedioAguaDiarioMl < $metaAguaMl * 0.8) { // Si está por debajo del 80% de la meta
            $recomendaciones['agua'] = '¡Tu consumo de agua ha sido bajo! Intenta beber al menos 8 vasos (2 litros) al día. Lleva una botella reutilizable contigo y establece recordatorios.';
        } elseif ($promedioAguaDiarioMl >= $metaAguaMl * 0.8 && $promedioAguaDiarioMl < $metaAguaMl * 1.2) {
            $recomendaciones['agua'] = '¡Buen trabajo con tu hidratación! Mantén un consumo constante de agua a lo largo del día.';
        } else {
            $recomendaciones['agua'] = '¡Excelente consumo de agua! Continúa con este hábito saludable.';
        }


        // --- Análisis de Sueño ---
        $registrosSueno = RegistroSueno::where('user_id', $user->id)
                                      ->whereBetween('hora_inicio', [$fechaAnalisis, $fechaHoy])
                                      ->get();
        $promedioMinutosSuenoDiario = $registrosSueno->count() > 0 ? $registrosSueno->sum('duracion_minutos') / $registrosSueno->count() : 0;
        $promedioHorasSueno = round($promedioMinutosSuenoDiario / 60, 1);
        $metaSuenoHoras = 7;

        if ($promedioHorasSueno < $metaSuenoHoras - 1) {
            $recomendaciones['sueno'] = 'Parece que no estás durmiendo lo suficiente. Intenta establecer una rutina de sueño, ir a la cama y levantarte a la misma hora todos los días. Evita cafeína y pantallas antes de dormir.';
        } elseif ($promedioHorasSueno >= $metaSuenoHoras - 1 && $promedioHorasSueno <= $metaSuenoHoras + 1) {
            $recomendaciones['sueno'] = 'Tu patrón de sueño es bueno. ¡Sigue así! Un sueño reparador es clave para tu bienestar.';
        } else {
            $recomendaciones['sueno'] = 'Estás durmiendo más de lo recomendado, lo cual podría indicar fatiga. Revisa tus niveles de energía durante el día.';
        }

        // --- Análisis de Actividad Física ---
        $registrosActividad = RegistroActividad::where('user_id', $user->id)
                                              ->whereBetween('fecha_registro', [$fechaAnalisis, $fechaHoy])
                                              ->get();
        $totalMinutosActividad = $registrosActividad->sum('duracion_minutos');
        $diasConActividad = $registrosActividad->groupBy(function($date) {
            return Carbon::parse($date->fecha_registro)->format('Y-m-d');
        })->count();

        $metaMinutosSemanal = 150; // Meta de ejemplo: 150 minutos de actividad moderada a la semana
        if ($totalMinutosActividad < $metaMinutosSemanal * 0.7) {
            $recomendaciones['actividad'] = '¡Anímate a moverte más! Intenta añadir al menos 30 minutos de actividad moderada la mayoría de los días. Caminar, bailar o hacer jardinería son buenas opciones.';
        } elseif ($totalMinutosActividad >= $metaMinutosSemanal * 0.7 && $diasConActividad < 3) {
             $recomendaciones['actividad'] = 'Estás alcanzando tus minutos de actividad, pero intenta distribuirlos en más días de la semana para mayor beneficio.';
        }
        else {
            $recomendaciones['actividad'] = '¡Excelente nivel de actividad física! Mantén la consistencia y considera probar diferentes tipos de ejercicio para desafiar tu cuerpo.';
        }

        // --- Análisis de Alimentación ---
        $registrosAlimentacion = RegistroAlimento::where('user_id', $user->id)
                                                    ->whereBetween('fecha_registro', [$fechaAnalisis, $fechaHoy])
                                                    ->get();
        $tiposComidaRegistrados = $registrosAlimentacion->pluck('tipo_comida')->unique();

        if ($registrosAlimentacion->isEmpty()) {
            $recomendaciones['alimentacion'] = 'No has registrado alimentos esta semana. ¡Anímate a registrar tus comidas para obtener mejores recomendaciones!';
        } elseif ($tiposComidaRegistrados->count() < 3) { // Pocos tipos de comida
            $recomendaciones['alimentacion'] = 'Intenta variar más tus comidas. Asegúrate de incluir desayuno, almuerzo y cena. Prueba diferentes fuentes de proteínas y vegetales.';
        } else {
            $recomendaciones['alimentacion'] = 'Tu registro de alimentación muestra una buena variedad. ¡Sigue así! Recuerda incluir una buena porción de vegetales en cada comida.';
        }

        return view('recomendaciones.index', compact('recomendaciones'));
    }
}