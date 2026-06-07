@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Menú Lateral Izquierdo -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="bg-dark vh-100 p-3">
                    {{-- <h5 class="text-white text-center mb-4 border-bottom pb-2">Menú Principal</h5> --}}

                    <!-- Dashboard -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#dashboardMenu" role="button">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse show mt-2" id="dashboardMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-box-seam me-2"></i> Inventario
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block">
                                    <i class="bi bi-arrow-left-right me-2"></i> Movimientos
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#reportesMenu" role="button">
                            <i class="bi bi-file-text me-2"></i> Reportes
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse mt-2" id="reportesMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 1
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 2
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block">
                                    <i class="bi bi-file-bar-graph me-2"></i> Reporte 3
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Historial -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#historialMenu" role="button">
                            <i class="bi bi-clock-history me-2"></i> Historial
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse mt-2" id="historialMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-door-open me-2"></i> Accesos al Sistema
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block">
                                    <i class="bi bi-pencil-square me-2"></i> Cambios
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Configuración -->
                    <div class="mb-3">
                        <a class="text-white d-flex align-items-center text-decoration-none" data-bs-toggle="collapse"
                            href="#configMenu" role="button">
                            <i class="bi bi-gear me-2"></i> Configuración
                            <i class="bi bi-chevron-down ms-auto"></i>
                        </a>
                        <div class="collapse mt-2" id="configMenu">
                            <div class="ps-3">
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-sliders2 me-2"></i> General del Sistema
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block mb-2">
                                    <i class="bi bi-table me-2"></i> Tablas
                                </a>
                                <a href="#" class="text-white-50 text-decoration-none d-block">
                                    <i class="bi bi-key me-2"></i> Cambio Contraseña
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Principal (Métricas) -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <h2 class="mb-4">Dashboard</h2>

                    <!-- Tarjetas de Métricas -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Equipos</h6>
                                            <h3 class="mb-0">150</h3>
                                        </div>
                                        <i class="bi bi-hdd-stack fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Equipos Activos</h6>
                                            <h3 class="mb-0">120</h3>
                                        </div>
                                        <i class="bi bi-check-circle fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Movimientos Mes</h6>
                                            <h3 class="mb-0">45</h3>
                                        </div>
                                        <i class="bi bi-arrow-repeat fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Usuarios Activos</h6>
                                            <h3 class="mb-0">8</h3>
                                        </div>
                                        <i class="bi bi-people fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Segunda fila de métricas (opcional) -->
                    <div class="row mt-3">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Equipos en Garantía</h6>
                                            <h3 class="mb-0">25</h3>
                                        </div>
                                        <i class="bi bi-shield-check fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Equipos Dañados</h6>
                                            <h3 class="mb-0">12</h3>
                                        </div>
                                        <i class="bi bi-exclamation-triangle fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Sucursales</h6>
                                            <h3 class="mb-0">5</h3>
                                        </div>
                                        <i class="bi bi-building fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="card bg-white text-dark border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title">Total Movimientos</h6>
                                            <h3 class="mb-0">1,234</h3>
                                        </div>
                                        <i class="bi bi-graph-up fs-1 text-success"></i>
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

@push('styles')
    <style>
        /* Ajuste para que el menú lateral tenga altura completa */
        .vh-100 {
            min-height: calc(100vh - 70px);
        }

        /* Estilo para los enlaces del menú */
        .bg-dark a {
            transition: all 0.3s;
        }

        .bg-dark a:hover {
            color: white !important;
            padding-left: 5px;
        }

        /* Animación para las tarjetas */
        .card {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Iconos en el menú */
        .bi-chevron-down {
            transition: transform 0.3s;
        }

        [aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }
    </style>
@endpush
