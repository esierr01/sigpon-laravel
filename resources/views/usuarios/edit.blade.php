@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Editando Usuario: {{ $user->name }}</h5>
                    </div>

                    <div class="card-body">
                        <!-- Importante: method="POST" y @method('PUT') para que Laravel entienda que es un update -->
                        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fila para Rol y Estado (Activo) -->
                            <div class="row mb-3 align-items-end justify-content-between">
                                <!-- Rol (Izquierda) -->
                                <div class="col-md-7">
                                    <label for="role" class="form-label fw-bold">Rol del Sistema</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role"
                                        name="role" required>
                                        <option value="1" {{ old('role', $user->role) == 1 ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="2" {{ old('role', $user->role) == 2 ? 'selected' : '' }}>Editor
                                        </option>
                                        <option value="3" {{ old('role', $user->role) == 3 ? 'selected' : '' }}>
                                            Visitante</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Estado (Derecha) -->
                                <div class="col-auto">
                                    <div class="form-check mb-1">
                                        <input type="checkbox"
                                            class="form-check-input @error('active') is-invalid @enderror" id="active"
                                            name="active" {{ old('active', $user->active) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="active">Usuario Activo</label>
                                        @error('active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Fila para Contraseñas -->
                            <div class="row mb-3">
                                <!-- Contraseña (Izquierda) -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold">Nueva Contraseña</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Dejar vacío para no cambiar">
                                    <div class="form-text">Solo complete si desea cambiar la contraseña actual.</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirmar Contraseña (Derecha) -->
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-bold">Confirmar
                                        Contraseña</label>
                                    <input type="password"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a class="btn bg-custom-btn-off btn-sm" href="{{ route('usuarios.index') }}">
                                    Cancelar
                                </a>

                                <button type="submit" class="btn bg-custom-btn-on btn-sm">
                                    Guardar Datos
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
