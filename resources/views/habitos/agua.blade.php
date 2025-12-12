@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #1B3C53;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Registrar Consumo de Agua') }}</h4>
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

                    <form action="{{ route('habitos.agua.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="cantidad_agua" class="form-label">Cantidad:</label>
                            <input type="number" class="form-control @error('cantidad_agua') is-invalid @enderror" id="cantidad_agua" name="cantidad_agua" value="{{ old('cantidad_agua') }}" required>
                            @error('cantidad_agua')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="unidad_agua" class="form-label">Unidad:</label>
                            <select class="form-select @error('unidad_agua') is-invalid @enderror" id="unidad_agua" name="unidad_agua" required>
                                <option value="" disabled {{ old('unidad_agua') == '' ? 'selected' : '' }}>Selecciona la unidad</option>
                                <option value="ml" {{ old('unidad_agua') == 'ml' ? 'selected' : '' }}>Mililitros (ml)</option>
                                <option value="litros" {{ old('unidad_agua') == 'litros' ? 'selected' : '' }}>Litros (L)</option>
                                <option value="vasos" {{ old('unidad_agua') == 'vasos' ? 'selected' : '' }}>Vasos</option>
                            </select>
                            @error('unidad_agua')
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
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($registrosAgua as $registro)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($registro->fecha_registro)->format('d/m/Y') }}</td>
                                        <td>{{ $registro->cantidad }}</td>
                                        <td>{{ $registro->unidad }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No hay registros de agua todavía.</td>
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