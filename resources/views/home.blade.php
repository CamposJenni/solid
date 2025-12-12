@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Panel de Control') }}</h4>
                </div>

                <div class="card-body p-4">
                    {{-- Mensajes de estado (comúnmente después del login) --}}
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="lead text-center mb-4">¡Bienvenido/a de nuevo, {{ Auth::user()->name }}! Aquí puedes gestionar tus hábitos saludables:</p>

                    <div class="list-group list-group-flush">
                        <a href="{{ route('habitos.agua') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Registrar Consumo de Agua
                            <i class="bi bi-droplet-fill text-primary"></i>
                        </a>
                        <a href="{{ route('habitos.sueno') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Registrar Horas de Sueño
                            <i class="bi bi-moon-fill text-secondary"></i>
                        </a>
                        <a href="{{ route('habitos.actividad') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Registrar Actividad Física
                            <i class="bi bi-activity text-success"></i>
                        </a>
                        <a href="{{ route('habitos.alimentacion') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Registrar Alimentación
                            <i class="bi bi-egg-fried text-warning"></i>
                        </a>
                        <a href="{{ route('reportes.semanales') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Ver Reportes Semanales
                            <i class="bi bi-graph-up text-danger"></i>
                        </a>
                        <a href="{{ route('metas.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Establecer Metas
                            <i class="bi bi-trophy-fill text-info"></i>
                        </a>
                        <a href="{{ route('recomendaciones.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Ver Recomendaciones
                            <i class="bi bi-lightbulb-fill text-dark"></i>
                        </a>
                        <a href="{{ route('perfil.editar') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Actualizar Perfil
                            <i class="bi bi-person-circle text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection