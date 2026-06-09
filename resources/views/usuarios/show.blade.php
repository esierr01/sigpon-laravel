@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-8">

                <!-- Botón Regresar -->
                <a class="btn btn-info text-primary mb-4 fw-bold btn-regresar-hover" href="{{ route('usuarios.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-arrow-return-left text-primary" viewBox="0 0 16 16"
                        style="stroke: currentColor; stroke-width: 1.5; paint-order: stroke fill;">
                        <path fill-rule="evenodd"
                            d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5" />
                    </svg>
                    {{ __('Regresar') }}
                </a>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Detalles del Usuario</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="mb-3 fw-bold">{{ $user->name }}</h4>
                            </div>
                            <div class="col-md-4 text-end">
                                @if ($user->active)
                                    <span class="badge bg-success fs-6">Activo</span>
                                @else
                                    <span class="badge bg-danger fs-6">Inactivo</span>
                                @endif
                            </div>
                        </div>

                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="text-muted" style="width: 35%">ID:</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Correo Electrónico:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Rol Asignado:</th>
                                    <td>
                                        @if ($user->role == 1)
                                            <span class="badge bg-primary">Admin</span>
                                        @elseif ($user->role == 2)
                                            <span class="badge bg-warning text-dark">Editor</span>
                                        @else
                                            <span class="badge bg-secondary">Visitante</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Correo Verificado:</th>
                                    <td>
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-info">Sí
                                                ({{ $user->email_verified_at->format('d/m/Y H:i') }})</span>
                                        @else
                                            <span class="badge bg-secondary">No verificado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Último Inicio de Sesión:</th>
                                    <td>{{ $user->last_login ? $user->last_login->format('d/m/Y H:i:s') : 'Nunca' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Creado Por:</th>
                                    <td>{{ $user->creator ? $user->creator->name : 'Sistema / Root' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Fecha de Creación:</th>
                                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Última Actualización:</th>
                                    <td>{{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'No disponible' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
