@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm mb-3">
                    <i class="bi bi-box-arrow-left me-2"></i>Regresar
                </a>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white text-center">
                        <h5 class="mb-0 fw-bold">Reporte Historial</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('reports.history') }}" method="GET" class="mb-4" id="formHistory">
                            <div class="row align-items-end g-3">
                                <div class="col-md-3">
                                    <label for="report_type" class="form-label fw-bold">Tipo de Reporte</label>
                                    <select name="report_type" id="report_type" class="form-select" required
                                        onchange="toggleDates()">
                                        <option value="">Seleccione...</option>
                                        <option value="access" {{ $reportType == 'access' ? 'selected' : '' }}>
                                            Accesos (ordenado por fecha)
                                        </option>
                                        <option value="changes" {{ $reportType == 'changes' ? 'selected' : '' }}>
                                            Cambios (Ordenado por Fecha)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3 date-field {{ $reportType ? '' : 'd-none' }}">
                                    <label for="date_from" class="form-label fw-bold">Fecha Inicio</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control"
                                        value="{{ $dateFrom ?? '' }}" required>
                                </div>

                                <div class="col-md-3 date-field {{ $reportType ? '' : 'd-none' }}">
                                    <label for="date_to" class="form-label fw-bold">Fecha Fin</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control"
                                        value="{{ $dateTo ?? '' }}" required>
                                </div>

                                <div class="col-md-3">
                                    <button type="submit" name="generate" value="1"
                                        class="btn bg-custom-btn-on btn-sm w-100">
                                        <i class="bi bi-file-earmark-text me-2"></i>Procesar Datos
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Por favor corrige los errores:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($data->isNotEmpty())
                            <div class="table-wrapper">
                                <div class="table-responsive table-report">
                                    @if ($reportType === 'access')
                                        <table class="table table-striped table-hover table-bordered mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha y Hora</th>
                                                    <th>Correo</th>
                                                    <th>Resultado</th>
                                                    <th>Observación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>{{ $item->mail }}</td>
                                                        <td>
                                                            @if ($item->result)
                                                                <span class="text-success fw-bold">Exitoso</span>
                                                            @else
                                                                <span class="text-danger fw-bold">Fallido</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->obs ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <table class="table table-striped table-hover table-bordered mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Fecha y Hora</th>
                                                    <th>Usuario</th>
                                                    <th>Tabla</th>
                                                    <th>Dirección IP</th>
                                                    <th>Observación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i:s') }}
                                                        </td>
                                                        <td>
                                                            @if ($item->user)
                                                                <span class="fw-bold">{{ $item->user->name }}</span>
                                                            @else
                                                                <span class="text-muted">Sistema / Eliminado</span>
                                                            @endif
                                                        </td>
                                                        <td><span class="text-success fw-bold">{{ $item->table }}</span>
                                                        </td>
                                                        <td><code>{{ $item->ip }}</code></td>
                                                        <td>{{ $item->obs }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <form action="{{ route('reports.history.pdf') }}" method="GET" target="_blank"
                                    class="d-inline">
                                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                                    <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                                    <input type="hidden" name="date_to" value="{{ $dateTo }}">
                                    <button type="submit" class="btn bg-custom-btn-danger btn-sm"
                                        {{ empty($reportType) ? 'disabled' : '' }}>
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Generar PDF
                                    </button>
                                </form>
                                <form action="{{ route('reports.history.excel') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                                    <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                                    <input type="hidden" name="date_to" value="{{ $dateTo }}">
                                    <button type="submit" class="btn bg-custom-btn-second btn-sm"
                                        {{ empty($reportType) ? 'disabled' : '' }}>
                                        <i class="bi bi-file-earmark-excel me-2"></i>Generar XLS
                                    </button>
                                </form>
                            </div>
                        @elseif(request()->has('generate'))
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                No se encontraron registros en el rango de fechas seleccionado.
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table-wrapper {
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }

        .table-report {
            max-height: 55vh;
            overflow: auto;
        }

        .table-report thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #212529;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }

        .table-report td,
        .table-report th {
            white-space: nowrap;
            vertical-align: middle;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleDates() {
            const select = document.getElementById('report_type');
            const fields = document.querySelectorAll('.date-field');
            fields.forEach(f => {
                if (select.value) {
                    f.classList.remove('d-none');
                } else {
                    f.classList.add('d-none');
                }
            });
        }

        // Ejecutar al cargar por si hay valores previos
        document.addEventListener('DOMContentLoaded', toggleDates);
    </script>
@endpush
