<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Importa todos los controladores que hemos creado
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistroAguaController;
use App\Http\Controllers\RegistroSuenoController;
use App\Http\Controllers\RegistroActividadController;
use App\Http\Controllers\RegistroAlimentoController;
use App\Http\Controllers\MetaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RecomendacionController;
use App\Http\Controllers\PerfilController;


Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación generadas por Laravel UI
Auth::routes();

//panel de control principal después de iniciar sesión
Route::get('/home', [HomeController::class, 'index'])->name('home');

//Rutas Protegidas por Autenticación
// Todas las rutas dentro de este grupo requieren que el usuario esté autenticado.
Route::middleware(['auth'])->group(function () {

    // Rutas para el módulo de Registro de Consumo de Agua
    Route::get('/habitos/agua', [RegistroAguaController::class, 'index'])->name('habitos.agua');
    Route::post('/habitos/agua', [RegistroAguaController::class, 'store'])->name('habitos.agua.store');

    // Rutas para el módulo de Registro de Horas de Sueño
    Route::get('/habitos/sueno', [RegistroSuenoController::class, 'index'])->name('habitos.sueno');
    Route::post('/habitos/sueno', [RegistroSuenoController::class, 'store'])->name('habitos.sueno.store');

    // Rutas para el módulo de Registro de Actividad Física
    Route::get('/habitos/actividad', [RegistroActividadController::class, 'index'])->name('habitos.actividad');
    Route::post('/habitos/actividad', [RegistroActividadController::class, 'store'])->name('habitos.actividad.store');

    // Rutas para el módulo de Registro de Alimentación
    Route::get('/habitos/alimentacion', [RegistroAlimentoController::class, 'index'])->name('habitos.alimentacion');
    Route::post('/habitos/alimentacion', [RegistroAlimentoController::class, 'store'])->name('habitos.alimentacion.store');

    // Rutas para el módulo de Reportes Semanales
    Route::get('/reportes/semanales', [ReporteController::class, 'index'])->name('reportes.semanales');

    // Rutas para el módulo de Metas
    Route::get('/metas', [MetaController::class, 'index'])->name('metas.index');
    Route::post('/metas', [MetaController::class, 'store'])->name('metas.store');

    // Rutas para el módulo de Recomendaciones
    Route::get('/recomendaciones', [RecomendacionController::class, 'index'])->name('recomendaciones.index');

    // Rutas para el módulo de Perfil del Usuario
    Route::get('/perfil/editar', [PerfilController::class, 'editar'])->name('perfil.editar');
    Route::put('/perfil/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');

});