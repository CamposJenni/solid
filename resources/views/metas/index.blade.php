@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Mis Metas') }}</h4>
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

                    <form action="{{ route('metas.store') }}" method="POST" class="mb-5 p-4 border rounded shadow-sm bg-light">
                        @csrf
                        <h5 class="mb-4 text-primary text-center">Establecer Nueva Meta</h5>
                        <div class="mb-3">
                            <label for="meta_tipo_habito" class="form-label fw-semibold">Hábito:</label>
                            <select class="form-select @error('meta_tipo_habito') is-invalid @enderror" id="meta_tipo_habito" name="meta_tipo_habito" required>
                                <option value="" disabled {{ old('meta_tipo_habito') == '' ? 'selected' : '' }}>Selecciona un hábito</option>
                                <option value="agua" {{ old('meta_tipo_habito') == 'agua' ? 'selected' : '' }}>Agua</option>
                                <option value="sueno" {{ old('meta_tipo_habito') == 'sueno' ? 'selected' : '' }}>Sueño</option>
                                <option value="actividad" {{ old('meta_tipo_habito') == 'actividad' ? 'selected' : '' }}>Actividad Física</option>
                                <option value="alimentacion" {{ old('meta_tipo_habito') == 'alimentacion' ? 'selected' : '' }}>Alimentación</option>
                            </select>
                            @error('meta_tipo_habito')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="meta_objetivo" class="form-label fw-semibold">Objetivo:</label>
                            <input type="text" class="form-control @error('meta_objetivo') is-invalid @enderror" id="meta_objetivo" name="meta_objetivo" placeholder="Ej: Beber 8 vasos de agua al día" value="{{ old('meta_objetivo') }}" required>
                            @error('meta_objetivo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="meta_unidad" class="form-label fw-semibold">Unidad (Opcional):</label>
                            <input type="text" class="form-control @error('meta_unidad') is-invalid @enderror" id="meta_unidad" name="meta_unidad" placeholder="Ej: vasos, horas, minutos" value="{{ old('meta_unidad') }}">
                            @error('meta_unidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="fecha_fin_meta" class="form-label fw-semibold">Fecha de Finalización (Opcional):</label>
                            <input type="date" class="form-control @error('fecha_fin_meta') is-invalid @enderror" id="fecha_fin_meta" name="fecha_fin_meta" value="{{ old('fecha_fin_meta') }}">
                            @error('fecha_fin_meta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i> Guardar Meta
                            </button>
                        </div>
                    </form>

                    <h5 class="mt-4 mb-4 text-center text-primary">Metas Actuales</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Hábito</th>
                                    <th>Objetivo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($metas as $meta)
                                    <tr>
                                        <td>{{ ucfirst($meta->tipo_habito) }}</td>
                                        <td>{{ $meta->objetivo }} {{ $meta->unidad ? '(' . $meta->unidad . ')' : '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($meta->fecha_inicio)->format('d/m/Y') }}</td>
                                        <td>{{ $meta->fecha_fin ? \Carbon\Carbon::parse($meta->fecha_fin)->format('d/m/Y') : 'N/A' }}</td>
                                        <td>
                                            @if ($meta->completada)
                                                <span class="badge bg-success">Completada</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Aquí podrías añadir botones para editar/marcar como completada/eliminar --}}
                                            <a href="#" class="btn btn-sm btn-info text-white" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                            <a href="#" class="btn btn-sm btn-success" title="Marcar como Completada"><i class="bi bi-check-lg"></i></a>
                                            <a href="#" class="btn btn-sm btn-danger" title="Eliminar"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No has establecido ninguna meta todavía.</td>
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