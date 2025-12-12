<?php

namespace App\Http\Controllers;

use App\Models\RegistroActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistroActividadController extends Controller
{
    public function index()
    {
        $registrosActividad = Auth::user()->registrosActividad()->latest('fecha_registro')->take(10)->get();
        return view('habitos.actividad', compact('registrosActividad'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_actividad' => 'required|string|max:255',
            'duracion_actividad' => 'required|integer|min:1',
            'intensidad_actividad' => 'required|string|in:baja,media,alta',
        ], [
            'tipo_actividad.required' => 'El tipo de actividad es obligatorio.',
            'duracion_actividad.required' => 'La duración de la actividad es obligatoria.',
            'duracion_actividad.integer' => 'La duración debe ser un número entero.',
            'duracion_actividad.min' => 'La duración debe ser al menos 1 minuto.',
            'intensidad_actividad.required' => 'La intensidad es obligatoria.',
            'intensidad_actividad.in' => 'La intensidad seleccionada no es válida.',
        ]);

        Auth::user()->registrosActividad()->create([
            'tipo_actividad' => $request->tipo_actividad,
            'duracion_minutos' => $request->duracion_actividad,
            'intensidad' => $request->intensidad_actividad,
            'fecha_registro' => now()->toDateString(),
        ]);

        return redirect()->route('habitos.actividad')->with('success', '¡Actividad física registrada exitosamente!');
    }
}