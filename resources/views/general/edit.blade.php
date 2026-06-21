@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm mb-3">
                    <i class="bi bi-box-arrow-left me-2"></i>Regresar
                </a>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Configuración General del Sistema</h5>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="formConfig" action="{{ route('general.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-1 mt-0">
                                <div class="col-md-4">
                                    <div class="">
                                        <label class="form-label fw-bold text-primary">RIF</label>
                                        <input type="text" class="form-control bg-light" name="rif"
                                            value="{{ old('rif', $general->rif) }}" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="">
                                        <label class="form-label fw-bold text-primary">Departamento</label>
                                        <input type="text" class="form-control bg-light" name="department"
                                            value="{{ old('department', $general->department) }}" required readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Reportes -->
                            <div class="row mt-3">
                                <div class="col-md-10 offset-md-2 align-items-center justify-content-center">
                                    <h6 class="text-primary text-center mb-3 fw-bold">Títulos y Subtítulos de Reportes</h6>
                                </div>
                            </div>

                            @for ($i = 1; $i <= 4; $i++)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-2 text-start">
                                        @if ($i == 1)
                                            <span class="badge bg-info fs-6">Stock por Almacén</span>
                                        @elseif ($i == 2)
                                            <span class="badge bg-info fs-6">Stock Mínimo</span>
                                        @elseif ($i == 3)
                                            <span class="badge bg-info fs-6">Movimientos</span>
                                        @elseif ($i == 4)
                                            <span class="badge bg-info fs-6">Historial</span>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control bg-light"
                                            name="title_report_{{ $i }}"
                                            value="{{ old('title_report_' . $i, $general->{'title_report_' . $i}) }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control bg-light"
                                            name="subtitle_report_{{ $i }}"
                                            value="{{ old('subtitle_report_' . $i, $general->{'subtitle_report_' . $i}) }}"
                                            readonly>
                                    </div>
                                </div>
                            @endfor

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-primary">Pie de Página</label>
                                        <input type="text" class="form-control bg-light" name="footer"
                                            value="{{ old('footer', $general->footer) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2 gap-2">
                                <!-- Botón Editar (Visible inicialmente) -->
                                <button type="button" class="btn bg-custom-btn-second btn-sm" id="btn-editar">
                                    <i class="bi bi-pencil-square me-2"></i>Editar Configuración
                                </button>

                                <!-- Botones Guardar y Cancelar (Ocultos inicialmente) -->
                                <button type="button" class="btn bg-custom-btn-off btn-sm d-none" id="btn-cancelar">
                                    <i class="bi bi-x-circle me-2"></i>Cancelar
                                </button>
                                <button type="submit" class="btn bg-custom-btn-on btn-sm d-none" id="btn-guardar">
                                    <i class="bi bi-check-circle me-2"></i>Guardar Configuración
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
        /* Estilo para quitar el fondo gris claro cuando se activan para editar */
        .form-control:not([readonly]) {
            background-color: #fff !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnEditar = document.getElementById('btn-editar');
            const btnGuardar = document.getElementById('btn-guardar');
            const btnCancelar = document.getElementById('btn-cancelar');
            const inputs = document.querySelectorAll('#formConfig input[type="text"]');

            // Función para activar modo edición
            btnEditar.addEventListener('click', function() {
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                });
                btnEditar.classList.add('d-none');
                btnGuardar.classList.remove('d-none');
                btnCancelar.classList.remove('d-none');

                // Enfocar el primer input
                inputs[0].focus();
            });

            // Función para cancelar (recarga la página y descarta cambios)
            btnCancelar.addEventListener('click', function() {
                window.location.reload();
            });
        });
    </script>
@endpush
