@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header text-white text-center rounded-top-4" style="background-color: #17a2b8;"> {{-- Estilo añadido para el header --}}
                    <h4 class="mb-0">{{ __('Actualizar Perfil') }}</h4>
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

                    <form method="POST" action="{{ route('perfil.actualizar') }}">
                        @csrf
                        @method('PUT') {{-- Importante para usar el método PUT en la ruta --}}

                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">{{ __('Nombre') }}</label>
                            <input id="nombre_usuario" type="text" class="form-control @error('nombre_usuario') is-invalid @enderror" name="nombre_usuario" value="{{ old('nombre_usuario', Auth::user()->name) }}" required autofocus>
                            @error('nombre_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="correo_electronico" class="form-label">{{ __('Correo Electrónico') }}</label>
                            <input id="correo_electronico" type="email" class="form-control @error('correo_electronico') is-invalid @enderror" name="correo_electronico" value="{{ old('correo_electronico', Auth::user()->email) }}" required>
                            @error('correo_electronico')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nueva_contrasena" class="form-label">{{ __('Nueva Contraseña (dejar en blanco para no cambiar)') }}</label>
                            <input id="nueva_contrasena" type="password" class="form-control @error('nueva_contrasena') is-invalid @enderror" name="nueva_contrasena">
                            @error('nueva_contrasena')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nueva_contrasena_confirmation" class="form-label">{{ __('Confirmar Nueva Contraseña') }}</label>
                            <input id="nueva_contrasena_confirmation" type="password" class="form-control" name="nueva_contrasena_confirmation">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save me-2"></i>
                                {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </form>
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