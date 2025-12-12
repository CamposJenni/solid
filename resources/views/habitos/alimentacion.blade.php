@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Registrar Alimentación') }}</h4>
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

                    <form action="{{ route('habitos.alimentacion.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tipo_comida" class="form-label">Tipo de Comida:</label>
                            <select class="form-select @error('tipo_comida') is-invalid @enderror" id="tipo_comida" name="tipo_comida" required>
                                <option value="" disabled {{ old('tipo_comida') == '' ? 'selected' : '' }}>Selecciona el tipo de comida</option>
                                <option value="desayuno" {{ old('tipo_comida') == 'desayuno' ? 'selected' : '' }}>Desayuno</option>
                                <option value="almuerzo" {{ old('tipo_comida') == 'almuerzo' ? 'selected' : '' }}>Almuerzo</option>
                                <option value="cena" {{ old('tipo_comida') == 'cena' ? 'selected' : '' }}>Cena</option>
                                <option value="snack" {{ old('tipo_comida') == 'snack' ? 'selected' : '' }}>Snack</option>
                            </select>
                            @error('tipo_comida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="detalles_comida" class="form-label">Detalles (Ej: pollo, arroz, brócoli):</label>
                            <textarea class="form-control @error('detalles_comida') is-invalid @enderror" id="detalles_comida" name="detalles_comida" rows="3" required>{{ old('detalles_comida') }}</textarea>
                            @error('detalles_comida')
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
                                    <th>Fecha</th>
                                    <th>Tipo de Comida</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registrosAlimento as $registro)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($registro->fecha_registro)->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($registro->tipo_comida) }}</td>
                                        <td>{{ $registro->detalles }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No hay registros de alimentación todavía.</td>
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