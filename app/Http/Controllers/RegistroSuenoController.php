<?php

namespace App\Http\Controllers;

use App\Models\RegistroSueno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegistroSuenoController extends Controller
{
    public function index()
    {
        $registrosSueno = Auth::user()->registrosSueno()->latest('hora_inicio')->take(10)->get();
        return view('habitos.sueno', compact('registrosSueno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_inicio_sueno' => 'required|date',
            'hora_inicio_sueno' => 'required|date_format:H:i',
            'fecha_fin_sueno' => 'required|date|after_or_equal:fecha_inicio_sueno',
            'hora_fin_sueno' => 'required|date_format:H:i',
        ], [
            'fecha_inicio_sueno.required' => 'La fecha de inicio de sueño es obligatoria.',
            'hora_inicio_sueno.required' => 'La hora de inicio de sueño es obligatoria.',
            'fecha_fin_sueno.required' => 'La fecha de fin de sueño es obligatoria.',
            'hora_fin_sueno.required' => 'La hora de fin de sueño es obligatoria.',
            'fecha_fin_sueno.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        $horaInicio = Carbon::parse($request->fecha_inicio_sueno . ' ' . $request->hora_inicio_sueno);
        $horaFin = Carbon::parse($request->fecha_fin_sueno . ' ' . $request->hora_fin_sueno);

        if ($horaFin->lessThan($horaInicio)) {
            $horaFin->addDay();
        }

        //$duracionMinutos = $horaFin->diffInMinutes($horaInicio);
        // AHORA (calcula de Inicio a Fin):
        $duracionMinutos = $horaInicio->diffInMinutes($horaFin);

        Auth::user()->registrosSueno()->create([
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'duracion_minutos' => $duracionMinutos,
        ]);

        return redirect()->route('habitos.sueno')->with('success', '¡Horas de sueño registradas exitosamente!');
    }
}