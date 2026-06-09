@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Menú Lateral Izquierdo -->
            <div class="col-md-3 col-lg-2 px-0 mt-3">
                <div class="bg-custom-gradient p-3 m-2 rounded" id="accordionMenu">
                    <!-- Dashboard -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#dashboardMenu" role="button" aria-expanded="false">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <!-- Se agrega data-bs-parent para comportamiento acordeón -->
                        <div class="collapse mt-2" id="dashboardMenu" data-bs-parent="#accordionMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white text-decoration-none d-block mb-2">
                                    <i class="bi bi-box-seam me-2"></i> Inventario
                                </a>
                                <a href="#" class="text-white text-decoration-none d-block">
                                    <i class="bi bi-arrow-left-right me-2"></i> Movimientos
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#reportesMenu" role="button" aria-expanded="false">
                            <i class="bi bi-printer me-2"></i> Reportes
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse mt-2" id="reportesMenu" data-bs-parent="#accordionMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white text-decoration-none d-block mb-2">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 1
                                </a>
                                <a href="#" class="text-white text-decoration-none d-block mb-2">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 2
                                </a>
                                <a href="#" class="text-white text-decoration-none d-block">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 3
                                </a>
                            </div>
                        </div>
                    </div>

                    @if (Auth::user()->role != 3)
                        <!-- Historial -->
                        <div class="mb-3">
                            <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                                href="#historialMenu" role="button" aria-expanded="false">
                                <i class="bi bi-clock-history me-2"></i> Historial
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse mt-2" id="historialMenu" data-bs-parent="#accordionMenu">
                                <div class="ps-3">
                                    <a href="{{ route('log-access.index') }}"
                                        class="text-white text-decoration-none d-block mb-2">
                                        <i class="bi bi-door-open me-2"></i> Accesos
                                    </a>
                                    <a href="#" class="text-white text-decoration-none d-block">
                                        <i class="bi bi-pencil-square me-2"></i> Cambios
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Configuración -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#configMenu" role="button" aria-expanded="false">
                            <i class="bi bi-gear me-2"></i> Configuración
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse mt-2" id="configMenu" data-bs-parent="#accordionMenu">
                            <div class="ps-3">
                                @if (Auth::user()->role != 3)
                                    @if (Auth::user()->role == 1)
                                        <a href="#" class="text-white text-decoration-none d-block mb-2">
                                            <i class="bi bi-sliders2 me-2"></i> General
                                        </a>
                                    @endif
                                    <a href="{{ route('usuarios.index') }}"
                                        class="text-white text-decoration-none d-block mb-2">
                                        <i class="bi bi-people me-2"></i> Usuarios
                                    </a>
                                    <a href="#" class="text-white text-decoration-none d-block mb-2">
                                        <i class="bi bi-table me-2"></i> Tablas
                                    </a>
                                @endif
                                <a href="#" class="text-white text-decoration-none d-block">
                                    <i class="bi bi-key me-2"></i> Contraseña
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal (Métricas) -->
            {{--  --}}
            {{--  --}}
            <div class="col-md-9 col-lg-10 align-items-center">
                <div class="p-5 align-items-center">
                    <h2 class="mb-5 fw-bolder">Dashboard</h2>

                    <!-- Tarjetas de Métricas -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Equipos en Inventario</h6>
                                            <h3 class="mb-0">150</h3>
                                        </div>
                                        <i class="bi bi-hdd-stack fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Movimientos en el Mes</h6>
                                            <h3 class="mb-0">45</h3>
                                        </div>
                                        <i class="bi bi-arrow-repeat fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Usuarios Activos en el Sistema</h6>
                                            <h3 class="mb-0">8</h3>
                                        </div>
                                        <i class="bi bi-people fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segunda fila de métricas -->
                    <div class="row mt-3">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Equipos con Bajo Stock</h6>
                                            <h3 class="mb-0">12</h3>
                                        </div>
                                        <i class="bi bi-exclamation-triangle fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card bg-white text-dark border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total de Movimientos</h6>
                                            <h3 class="mb-0">1,234</h3>
                                        </div>
                                        <i class="bi bi-graph-up fs-1 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Almacenes Registrados</h6>
                                            <h3 class="mb-0">5</h3>
                                        </div>
                                        <i class="bi bi-building fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <footer class="footer text-center">
        <p class="text-muted fw-lighter text-sm-center small">
            &copy;
            2026
            -
            SIGPON
            -
            Desarrollado por Emmanuel Sierra
        </p>
    </footer>
@endsection

@push('styles')
    <style>
        #accordionMenu {
            height: calc(100vh - 160px);
            overflow-y: auto;
        }

        /* Estilo para los enlaces del menú principal usando el ID para mayor especificidad */
        #accordionMenu>.mb-3>a {
            transition: background-color 0.3s, padding-left 0.3s;
            padding: 8px 12px;
            border-radius: 4px;
        }

        #accordionMenu>.mb-3>a:hover {
            background-color: #134692;
            padding-left: 17px;
        }

        /* Estilo para los submenús */
        #accordionMenu .collapse .ps-3>a {
            transition: background-color 0.3s, padding-left 0.3s, color 0.3s;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 4px;
        }

        #accordionMenu .collapse .ps-3>a:hover {
            /* background-color: #0d6efd;
                    color: white !important; */
            background-color: #ffffff;
            color: #000000 !important;
            /* Necesario para sobreescribir text-white-50 */
            padding-left: 17px;
        }

        /* Animación para las tarjetas */
        .card {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Animación del chevron (flecha) */
        .bi-chevron-down {
            transition: transform 0.3s;
        }

        /* Bootstrap agrega aria-expanded="true" automáticamente al abrir */
        [aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        .bg-custom-gradient {
            background: #058fad;
            background: -webkit-linear-gradient(to right, #0b6b8b, #00B4DB);
            background: linear-gradient(to right, #0083B0, #00B4DB);
        }
    </style>
@endpush
