<?php

namespace App\Http\Controllers;

use App\Models\RegistroAgua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistroAguaController extends Controller
{
    public function index()
    {
        $registrosAgua = Auth::user()->registrosAgua()->latest('fecha_registro')->take(10)->get();
        return view('habitos.agua', compact('registrosAgua'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cantidad_agua' => 'required|numeric|min:0',
            'unidad_agua' => 'required|string|in:ml,litros,vasos',
        ], [
            'cantidad_agua.required' => 'La cantidad de agua es obligatoria.',
            'cantidad_agua.numeric' => 'La cantidad de agua debe ser un número.',
            'cantidad_agua.min' => 'La cantidad de agua no puede ser negativa.',
            'unidad_agua.required' => 'La unidad es obligatoria.',
            'unidad_agua.in' => 'La unidad de agua seleccionada no es válida.',
        ]);

        Auth::user()->registrosAgua()->create([
            'cantidad' => $request->cantidad_agua,
            'unidad' => $request->unidad_agua,
            'fecha_registro' => now()->toDateString(),
        ]);

        return redirect()->route('habitos.agua')->with('success', '¡Consumo de agua registrado exitosamente!');
    }
}