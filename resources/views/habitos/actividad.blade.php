@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                {{-- Header de la tarjeta con color de la paleta (mantenido como en tu ejemplo) --}}
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;">
                    <h4 class="mb-0">{{ __('Registrar Actividad Física') }}</h4>
                </div>
                <div class="card-body p-4">
                    {{-- Mensajes de éxito y error --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('habitos.actividad.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tipo_actividad" class="form-label fw-semibold">Tipo de Actividad:</label>
                            <input type="text" class="form-control @error('tipo_actividad') is-invalid @enderror" id="tipo_actividad" name="tipo_actividad" placeholder="Ej: Correr, Ciclismo, Yoga" value="{{ old('tipo_actividad') }}" required>
                            @error('tipo_actividad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="duracion_actividad" class="form-label fw-semibold">Duración (minutos):</label>
                            <input type="number" class="form-control @error('duracion_actividad') is-invalid @enderror" id="duracion_actividad" name="duracion_actividad" required min="1" value="{{ old('duracion_actividad') }}">
                            @error('duracion_actividad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="intensidad_actividad" class="form-label fw-semibold">Intensidad:</label>
                            <select class="form-select @error('intensidad_actividad') is-invalid @enderror" id="intensidad_actividad" name="intensidad_actividad">
                                <option value="" disabled {{ old('intensidad_actividad') == '' ? 'selected' : '' }}>Selecciona la intensidad</option>
                                <option value="baja" {{ old('intensidad_actividad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('intensidad_actividad') == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('intensidad_actividad') == 'alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                            @error('intensidad_actividad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Registrar Actividad
                            </button>
                        </div>
                    </form>

                    <hr class="my-5">

                    <h5 class="mb-4 text-center text-secondary">Historial Reciente</h5>
                    <div class="table-responsive ">
                        <table class="table table-hover table-bordered text-center ">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Actividad</th>
                                    <th scope="col">Duración</th>
                                    <th scope="col">Intensidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registrosActividad as $registro)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($registro->fecha_registro)->format('d/m/Y') }}</td>
                                        <td>{{ $registro->tipo_actividad }}</td>
                                        <td>{{ $registro->duracion_minutos }} min</td>
                                        <td>
                                            @php
                                                $badgeClass = '';
                                                switch ($registro->intensidad) {
                                                    case 'baja': $badgeClass = 'bg-info text-dark'; break;
                                                    case 'media': $badgeClass = 'bg-warning text-dark'; break;
                                                    case 'alta': $badgeClass = 'bg-danger'; break;
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($registro->intensidad) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No hay actividades registradas todavía.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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