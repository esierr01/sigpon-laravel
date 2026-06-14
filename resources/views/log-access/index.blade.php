@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm mb-3"><i
                        class="bi bi-box-arrow-left me-2"></i>
                    Regresar</a>

                <div class="card shadow">
                    <!-- Cabecera con Barra de Búsqueda -->
                    <div class="card-header bg-custom-gradient text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Registro de Accesos al Sistema</h5>

                            <form action="{{ route('log-access.index') }}" method="GET"
                                class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Correo, resultado, obs..." value="{{ request('search') }}"
                                        aria-label="Buscar">
                                    <button type="submit" class="btn btn-light" title="Buscar">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <a href="{{ route('log-access.index') }}" class="btn btn-sm btn-outline-light"
                                    title="Limpiar filtros">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Correo</th>
                                        <th>Resultado</th>
                                        <th>Observación</th>
                                        <th>Fecha y Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ $log->mail }}</td>
                                            <td>
                                                @if ($log->result)
                                                    <span class="text-success fs-6 fw-bold">Exitoso</span>
                                                @else
                                                    <span class="text-danger fs-6 fw-bold">Fallido</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->obs ?? ' ' }}</td>
                                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay registros de acceso con los
                                                filtros aplicados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $logs->appends(request()->query())->links() }}
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

        /* From Uiverse.io by absent452 */
        /* From Uiverse.io by vinodjangid07 */
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
            margin-bottom: 10px
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
            font-size: 15px;
            opacity: 1;
            bottom: unset;
            transition-duration: 0.3s;
        }
    </style>
@endpush
