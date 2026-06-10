@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <a href="{{ route('home') }}" class="button">
                    <svg xmlns="http://www.w3.org/2000/svg" class="svgIcon" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>


                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Registro de Accesos al Sistema</h5>
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
                                                    <span class="badge bg-success">Exitoso</span>
                                                @else
                                                    <span class="badge bg-danger">Fallido</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->obs ?? ' ' }}</td>
                                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay registros de acceso</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $logs->links() }}
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
            /* Fondo azul primary */
            color: var(--bs-light) !important;
            /* Letras blancas/light */
            border-color: var(--bs-primary) !important;
            /* Borde del mismo color para que no resalte */
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
            /* font-family: Garet; */
            font-size: 15px;
            opacity: 1;
            bottom: unset;
            transition-duration: 0.3s;
        }
    </style>
@endpush
