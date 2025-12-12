<?php

namespace App\Http\Controllers;

use App\Models\RegistroAlimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegistroAlimentoController extends Controller
{
    public function index()
    {
        $registrosAlimento = Auth::user()->registrosAlimento()->latest('fecha_registro')->take(10)->get();
        return view('habitos.alimentacion', compact('registrosAlimento'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo_comida' => 'required|string|in:desayuno,almuerzo,cena,snack',
            'detalles_comida' => 'required|string|max:1000',
        ], [
            'tipo_comida.required' => 'El tipo de comida es obligatorio.',
            'tipo_comida.in' => 'El tipo de comida seleccionado no es válido.',
            'detalles_comida.required' => 'Los detalles de la comida son obligatorios.',
            'detalles_comida.max' => 'Los detalles de la comida no pueden exceder los 1000 caracteres.',
        ]);

        Auth::user()->registrosAlimento()->create([
            'tipo_comida' => $request->tipo_comida,
            'detalles' => $request->detalles_comida,
            'fecha_registro' => now()->toDateString(),
        ]);

        return redirect()->route('habitos.alimentacion')->with('success', '¡Registro de alimentación exitoso!');
    }
}