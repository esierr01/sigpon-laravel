@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-8">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Botón Cancelar -->
                    <a class="btn btn-danger fw-bold" href="{{ route('usuarios.index') }}">
                        <i class="bi bi-x-circle me-2"></i> Cancelar
                    </a>
                    <h4 class="mb-0 text-muted">Nuevo Usuario</h4>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Registrar Usuario en el Sistema</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('usuarios.store') }}" method="POST">
                            @csrf

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Rol -->
                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold">Rol del Sistema</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role"
                                    name="role" required>
                                    <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Admin</option>
                                    <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Editor</option>
                                    <option value="3" {{ old('role') == 3 ? 'selected' : '' }}>Visitante</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado (Checkbox) - Por defecto se crea activo -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="active" name="active"
                                    {{ old('active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">Usuario Activo</label>
                            </div>

                            <hr>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Contraseña</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <!-- Botón Guardar -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="submit" class="btn btn-success fw-bold">
                                    <i class="bi bi-person-plus-fill me-2"></i> Crear Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-custom-gradient {
            background: #058fad;
            background: -webkit-linear-gradient(to right, #0b6b8b, #00B4DB);
            background: linear-gradient(to right, #0083B0, #00B4DB);
        }
    </style>
@endpush
