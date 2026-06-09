@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- Botón Regresar -->
                    <a class="btn btn-info text-primary fw-bold btn-regresar-hover" href="{{ route('home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-return-left text-primary" viewBox="0 0 16 16"
                            style="stroke: currentColor; stroke-width: 1.5; paint-order: stroke fill;">
                            <path fill-rule="evenodd"
                                d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5" />
                        </svg>
                        {{ __('Regresar') }}
                    </a>

                    <!-- Botón Crear Nuevo Usuario -->
                    <a class="btn btn-success text-light fw-bold" href="{{ route('usuarios.create') }}">
                        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                    </a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Gestión de Usuarios</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role == 1)
                                                    <span class="badge bg-primary">Admin</span>
                                                @elseif ($user->role == 2)
                                                    <span class="badge bg-warning text-dark">Editor</span>
                                                @else
                                                    <span class="badge bg-secondary">Visitante</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-center">
                                                <!-- Botón Ver Detalle -->
                                                <a href="{{ route('usuarios.show', $user->id) }}"
                                                    class="btn btn-sm btn-info me-1" title="Ver Detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- Botón Editar -->
                                                <a href="{{ route('usuarios.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <!-- Formulario para Conmutar Estado (Oculto si es tu propio usuario) -->
                                                @if (Auth::id() !== $user->id)
                                                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')

                                                        @if ($user->active)
                                                            <!-- Si está activo, muestra botón para Inhabilitar -->
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Inhabilitar"
                                                                onclick="return confirm('¿Estás seguro de inhabilitar a este usuario?')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        @else
                                                            <!-- Si está inactivo, muestra botón para Habilitar -->
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Habilitar"
                                                                onclick="return confirm('¿Estás seguro de habilitar a este usuario?')">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        @endif

                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No hay usuarios registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Estilo hover para el botón regresar */
        .btn-regresar-hover:hover {
            background-color: var(--bs-primary) !important;
            color: var(--bs-light) !important;
            border-color: var(--bs-primary) !important;
        }

        /* Aseguramos que el SVG también se ponga blanco/light al hacer hover */
        .btn-regresar-hover:hover svg {
            color: var(--bs-light) !important;
        }

        .bg-custom-gradient {
            background: #058fad;
            background: -webkit-linear-gradient(to right, #0b6b8b, #00B4DB);
            background: linear-gradient(to right, #0083B0, #00B4DB);
        }
    </style>
@endpush
