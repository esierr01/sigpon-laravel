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
                        <form action="{{ route('general.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row mb-1 mt-0">
                                <div class="col-md-4">
                                    <div class="">
                                        <label class="form-label fw-bold text-primary">RIF</label>
                                        <input type="text" class="form-control" name="rif"
                                            value="{{ old('rif', $general->rif) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="">
                                        <label class="form-label fw-bold text-primary">Departamento / Dependencia</label>
                                        <input type="text" class="form-control" name="department"
                                            value="{{ old('department', $general->department) }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Reportes -->

                            <div class="row">
                                <div class="col-md-10 offset-md-2 align-items-center justify-content-center">
                                    <h6 class="text-primary text-center mb-3 fw-bold">Títulos y Subtítulos de Reportes</h6>
                                </div>
                            </div>

                            @for ($i = 1; $i <= 4; $i++)
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-2 text-start">
                                        @if ($i == 1)
                                            <span class="badge bg-primary fs-6">Stock por Almacén</span>
                                        @elseif ($i == 2)
                                            <span class="badge bg-primary fs-6">Stock Mínimo</span>
                                        @elseif ($i == 3)
                                            <span class="badge bg-primary fs-6">Movimientos</span>
                                        @elseif ($i == 4)
                                            <span class="badge bg-primary fs-6">Historial</span>
                                        @endif
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="title_report_{{ $i }}"
                                            value="{{ old('title_report_' . $i, $general->{'title_report_' . $i}) }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control"
                                            name="subtitle_report_{{ $i }}"
                                            value="{{ old('subtitle_report_' . $i, $general->{'subtitle_report_' . $i}) }}">
                                    </div>
                                </div>
                            @endfor

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-primary">Pie de Página (Footer)</label>
                                        <input type="text" class="form-control" name="footer"
                                            value="{{ old('footer', $general->footer) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn bg-custom-btn-on btn-sm">
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

    </style>
@endpush
