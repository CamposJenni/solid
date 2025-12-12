@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Registrar Horas de Sueño') }}</h4>
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

                    <form action="{{ route('habitos.sueno.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="fecha_inicio_sueno" class="form-label">Fecha de Inicio:</label>
                            <input type="date" class="form-control @error('fecha_inicio_sueno') is-invalid @enderror" id="fecha_inicio_sueno" name="fecha_inicio_sueno" value="{{ old('fecha_inicio_sueno') }}" required>
                            @error('fecha_inicio_sueno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="hora_inicio_sueno" class="form-label">Hora de Inicio:</label>
                            <input type="time" class="form-control @error('hora_inicio_sueno') is-invalid @enderror" id="hora_inicio_sueno" name="hora_inicio_sueno" value="{{ old('hora_inicio_sueno') }}" required>
                            @error('hora_inicio_sueno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin_sueno" class="form-label">Fecha de Fin:</label>
                            <input type="date" class="form-control @error('fecha_fin_sueno') is-invalid @enderror" id="fecha_fin_sueno" name="fecha_fin_sueno" value="{{ old('fecha_fin_sueno') }}" required>
                            @error('fecha_fin_sueno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="hora_fin_sueno" class="form-label">Hora de Fin:</label>
                            <input type="time" class="form-control @error('hora_fin_sueno') is-invalid @enderror" id="hora_fin_sueno" name="hora_fin_sueno" value="{{ old('hora_fin_sueno') }}" required>
                            @error('hora_fin_sueno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                Registrar
                            </button>
                        </div>
                    </form>

                    <hr class="my-5">

                    <h5 class="mb-4 text-center text-secondary">Historial Reciente</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha Inicio</th>
                                    <th>Hora Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Hora Fin</th>
                                    <th>Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registrosSueno as $registro)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($registro->hora_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($registro->hora_inicio)->format('H:i') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($registro->hora_fin)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($registro->hora_fin)->format('H:i') }}</td>
                                        <td>{{ $registro->duracion_minutos }} minutos</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No hay registros de sueño todavía.</td>
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