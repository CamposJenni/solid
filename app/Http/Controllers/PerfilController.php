<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function editar()
    {
        return view('perfil.editar');
    }

    public function actualizar(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'nombre_usuario' => ['required', 'string', 'max:255'],
            'correo_electronico' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'nueva_contrasena' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'nombre_usuario.required' => 'El nombre es obligatorio.',
            'correo_electronico.required' => 'El correo electrónico es obligatorio.',
            'correo_electronico.email' => 'El correo electrónico debe ser una dirección válida.',
            'correo_electronico.unique' => 'Este correo electrónico ya está en uso.',
            'nueva_contrasena.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'nueva_contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $usuario->name = $request->nombre_usuario;
        $usuario->email = $request->correo_electronico;

        if ($request->filled('nueva_contrasena')) {
            $usuario->password = Hash::make($request->nueva_contrasena);
        }

        $usuario->save();

        return redirect()->route('perfil.editar')->with('success', '¡Perfil actualizado exitosamente!');
    }
}