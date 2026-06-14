@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-8">

                <!-- Botón Regresar -->
                <a href="{{ route('usuarios.index') }}" class="btn bg-custom-btn-on btn-sm"><i
                        class="bi bi-box-arrow-left me-2"></i>
                    Regresar</a>

                <div class="card shadow mt-3">
                    <div class="card-header bg-custom-gradient text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detalles del Usuario</h5>
                        @if ($user->active)
                            <span class="badge bg-success fs-6">Activo</span>
                        @else
                            <span class="badge bg-danger fs-6">Inactivo</span>
                        @endif
                    </div>

                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th class="text-muted">Nombre Usuario:</th>
                                    <td>{{ $user->name }}</td>

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

        .button {
            width: 50px;
            height: 35px;
            border-radius: 20%;
            background-color: #0093BE;
            border: none;
            font-weight: 300;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px double #e9e9e9;
            border-radius: 15px;
            cursor: pointer;
            transition-duration: 0.3s;
            overflow: hidden;
            position: relative;
        }

        .svgIcon {
            width: 18px;
            transition-duration: 0.3s;
        }

        .svgIcon path {
            fill: #ffffff;
        }

        .button:hover {
            width: 140px;
            border-radius: 15px;
            transition-duration: 0.3s;
            background-color: #0084B1;
            align-items: center;
        }

        .button:hover .svgIcon {
            transition-duration: 0.3s;
            transform: translateY(-200%);
        }

        .button::before {
            position: absolute;
            bottom: -20px;
            content: "Regresar";
            color: #e9e9e9;
            font-size: 0px;
        }

        .button:hover::before {
            /* font-family: Garet; */
            font-size: 15px;
            opacity: 1;
            bottom: unset;
            transition-duration: 0.3s;
        }
    </style>
@endpush
