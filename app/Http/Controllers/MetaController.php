<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MetaController extends Controller
{
    public function index()
    {
        $metas = Auth::user()->metas()->latest('fecha_inicio')->get();
        return view('metas.index', compact('metas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meta_tipo_habito' => 'required|string|in:agua,sueno,actividad,alimentacion',
            'meta_objetivo' => 'required|string|max:255',
            'meta_unidad' => 'nullable|string|max:50',
            'fecha_fin_meta' => 'nullable|date|after_or_equal:today',
        ], [
            'meta_tipo_habito.required' => 'El tipo de hábito para la meta es obligatorio.',
            'meta_objetivo.required' => 'El objetivo de la meta es obligatorio.',
            'fecha_fin_meta.date' => 'La fecha de fin debe ser una fecha válida.',
            'fecha_fin_meta.after_or_equal' => 'La fecha de fin no puede ser anterior al día de hoy.',
        ]);

        Auth::user()->metas()->create([
            'tipo_habito' => $request->meta_tipo_habito,
            'objetivo' => $request->meta_objetivo,
            'unidad' => $request->meta_unidad,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => $request->fecha_fin_meta,
            'completada' => false,
        ]);

        return redirect()->route('metas.index')->with('success', '¡Meta establecida exitosamente!');
    }
}