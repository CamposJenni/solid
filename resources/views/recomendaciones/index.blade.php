@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #2e0b1e;">
                    <h4 class="mb-0">{{ __('Recomendaciones Personalizadas') }}</h4>
                </div>
                <div class="card-body p-4">
                    <p class="lead text-center mb-4">
                        Aquí encontrarás recomendaciones personalizadas basadas en tus hábitos recientes.
                    </p>

                    <hr class="my-4">

                    <h5 class="mb-4 text-center text-primary">Tus Recomendaciones</h5>

                    @if (!empty($recomendaciones['agua']))
                    <div class="alert alert-info border-info shadow-sm mb-3" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-droplet-fill me-2"></i> Recomendación de Agua:</h6>
                        <p class="mb-0">{{ $recomendaciones['agua'] }}</p>
                    </div>
                    @endif

                    @if (!empty($recomendaciones['sueno']))
                    <div class="alert alert-warning border-warning shadow-sm mb-3" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-moon-fill me-2"></i> Recomendación de Sueño:</h6>
                        <p class="mb-0">{{ $recomendaciones['sueno'] }}</p>
                    </div>
                    @endif

                    @if (!empty($recomendaciones['actividad']))
                    <div class="alert alert-success border-success shadow-sm mb-3" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-activity me-2"></i> Recomendación de Actividad Física:</h6>
                        <p class="mb-0">{{ $recomendaciones['actividad'] }}</p>
                    </div>
                    @endif

                    @if (!empty($recomendaciones['alimentacion']))
                    <div class="alert alert-danger border-danger shadow-sm mb-3" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-egg-fried me-2"></i> Recomendación de Alimentación:</h6>
                        <p class="mb-0">{{ $recomendaciones['alimentacion'] }}</p>
                    </div>
                    @endif

                    @if (empty($recomendaciones))
                        <div class="alert alert-secondary text-center" role="alert">
                            No hay recomendaciones disponibles en este momento. ¡Sigue registrando tus hábitos para obtenerlas!
                        </div>
                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection