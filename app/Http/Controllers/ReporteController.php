<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RegistroAgua;
use App\Models\RegistroSueno;
use App\Models\RegistroActividad;
use App\Models\RegistroAlimento;

class ReporteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $fechaFin = Carbon::now();
        $fechaInicio = Carbon::now()->subWeek(); // Últimos 7 días

        // --- Obtener y procesar datos de Agua (para el resumen y para el gráfico) ---
        $registrosAguaSemana = RegistroAgua::where('user_id', $user->id)
                                            ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                                            ->get();

        $totalAguaMlSemana = 0;
        foreach ($registrosAguaSemana as $registro) {
            if ($registro->unidad === 'litros') {
                $totalAguaMlSemana += $registro->cantidad * 1000;
            } elseif ($registro->unidad === 'vasos') {
                $totalAguaMlSemana += $registro->cantidad * 250;
            } else { // ml
                $totalAguaMlSemana += $registro->cantidad;
            }
        }
        $promedioAguaDiarioMl = $registrosAguaSemana->count() > 0 ? $totalAguaMlSemana / 7 : 0;
        $promedioAguaDiarioVasos = round($promedioAguaDiarioMl / 250);

        // Datos para el gráfico de Agua
        $aguaChartLabels = []; // Días de la semana
        $aguaChartData = [];   // Cantidad de agua por día (en ml)
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i); // Empezar 6 días atrás hasta hoy
            $aguaChartLabels[] = $date->isoFormat('ddd D MMM'); // Ej: Mié 05 Jul
            $dailyWaterMl = $registrosAguaSemana->filter(function($registro) use ($date) {
                return Carbon::parse($registro->fecha_registro)->isSameDay($date);
            })->sum(function($registro) {
                if ($registro->unidad === 'litros') return $registro->cantidad * 1000;
                if ($registro->unidad === 'vasos') return $registro->cantidad * 250;
                return $registro->cantidad;
            });
            $aguaChartData[] = round($dailyWaterMl / 1000, 1); // Mostrar en Litros para el gráfico
        }


        // --- Obtener y procesar datos de Sueño ---
        $registrosSuenoSemana = RegistroSueno::where('user_id', $user->id)
                                             ->whereBetween('hora_inicio', [$fechaInicio, $fechaFin])
                                             ->get();

        $totalMinutosSuenoSemana = $registrosSuenoSemana->sum('duracion_minutos');
        $promedioHorasSuenoDiario = $registrosSuenoSemana->count() > 0 ? round(($totalMinutosSuenoSemana / $registrosSuenoSemana->count()) / 60, 1) : 0;

        // Datos para el gráfico de Sueño
        $suenoChartLabels = [];
        $suenoChartData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i);
            $suenoChartLabels[] = $date->isoFormat('ddd D MMM');
            $dailySleepMin = $registrosSuenoSemana->filter(function($registro) use ($date) {
                return Carbon::parse($registro->hora_inicio)->isSameDay($date);
            })->sum('duracion_minutos');
            $suenoChartData[] = round($dailySleepMin / 60, 1); // Horas de sueño
        }


        // --- Obtener y procesar datos de Actividad Física ---
        $registrosActividadSemana = RegistroActividad::where('user_id', $user->id)
                                                   ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                                                   ->get();

        $totalMinutosActividadSemana = $registrosActividadSemana->sum('duracion_minutos');
        $intensidadCounts = $registrosActividadSemana->groupBy('intensidad')->map->count();
        $intensidadMasComun = $intensidadCounts->isNotEmpty() ? $intensidadCounts->sortDesc()->keys()->first() : 'N/A';

        // Datos para el gráfico de Actividad (Tipo: Pie Chart por intensidad)
        $actividadChartLabels = ['Baja', 'Media', 'Alta', 'Sin Registrar'];
        $actividadChartData = [
            $intensidadCounts->get('baja', 0),
            $intensidadCounts->get('media', 0),
            $intensidadCounts->get('alta', 0),
            // Si hay registros, pero no tienen intensidad definida (no debería pasar si es 'required')
            // O si no hay registros en absoluto y queremos mostrar "Sin Registrar" si count es 0
            ($registrosActividadSemana->count() > 0 && $intensidadCounts->sum() == 0) ? $registrosActividadSemana->count() : 0
        ];


        // --- Obtener y procesar datos de Alimentación ---
        $registrosAlimentacionSemana = RegistroAlimento::where('user_id', $user->id)
                                                        ->whereBetween('fecha_registro', [$fechaInicio, $fechaFin])
                                                        ->get();

        $tiposComidaCount = $registrosAlimentacionSemana->groupBy('tipo_comida')->map->count();
        $patronAlimentacion = 'No hay suficientes datos';
        if ($tiposComidaCount->isNotEmpty()) {
            if ($tiposComidaCount->has('desayuno') && $tiposComidaCount->has('almuerzo') && $tiposComidaCount->has('cena')) {
                $patronAlimentacion = 'Patrón de comidas regular';
            } elseif ($tiposComidaCount->count() > 2) {
                $patronAlimentacion = 'Variedad en tipos de comida';
            } else {
                $patronAlimentacion = 'Poca variedad o datos insuficientes';
            }
        }
        // Datos para el gráfico de Alimentación (Tipo: Barras por tipo de comida)
        $alimentacionChartLabels = $tiposComidaCount->keys()->toArray();
        $alimentacionChartData = $tiposComidaCount->values()->toArray();


        return view('reportes.semanales', [
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y'),
            'promedioAguaDiarioVasos' => $promedioAguaDiarioVasos,
            'promedioHorasSuenoDiario' => $promedioHorasSuenoDiario,
            'totalMinutosActividadSemana' => $totalMinutosActividadSemana,
            'intensidadMasComun' => $intensidadMasComun,
            'patronAlimentacion' => $patronAlimentacion,

            // Datos para los gráficos
            'aguaChartLabels' => json_encode($aguaChartLabels),
            'aguaChartData' => json_encode($aguaChartData),

            'suenoChartLabels' => json_encode($suenoChartLabels),
            'suenoChartData' => json_encode($suenoChartData),

            'actividadChartLabels' => json_encode($actividadChartLabels),
            'actividadChartData' => json_encode($actividadChartData),

            'alimentacionChartLabels' => json_encode($alimentacionChartLabels),
            'alimentacionChartData' => json_encode($alimentacionChartData),
        ]);
    }
}