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
                        <h5 class="mb-0 fw-bold">Reporte Movimientos</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('reports.movements') }}" method="GET" class="mb-4">
                            <div class="row align-items-end g-3">
                                <div class="col-md-6">
                                    <label for="report_type" class="form-label fw-bold">Tipo de Reporte</label>
                                    <select name="report_type" id="report_type" class="form-select" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="movements_by_date"
                                            {{ $reportType == 'movements_by_date' ? 'selected' : '' }}>
                                            Movimientos (ordenado por fecha)
                                        </option>
                                        <option value="movements_by_type"
                                            {{ $reportType == 'movements_by_type' ? 'selected' : '' }}>
                                            Movimientos (Ordenado por Tipo de Movimiento)
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" name="generate" value="1"
                                        class="btn bg-custom-btn-on btn-sm w-100">
                                        <i class="bi bi-file-earmark-text me-2"></i>Procesar Datos
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if ($data->isNotEmpty())
                            <div class="table-wrapper">
                                <div class="table-responsive table-report">
                                    <table class="table table-striped table-hover table-bordered mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Fecha Movimiento</th>
                                                <th>Tipo Movimiento</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Cantidad</th>
                                                <th>Observación</th>
                                                <th>Realizado por</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $item)
                                                @php
                                                    $tipoId = $item->movement_type;
                                                    $tipoNombre = match ($tipoId) {
                                                        1 => 'Compra',
                                                        2 => 'Salida',
                                                        3 => 'Traslado',
                                                        4 => 'Ajuste',
                                                        default => 'Desconocido',
                                                    };
                                                    $badgeClass = match ($tipoId) {
                                                        1 => 'bg-success',
                                                        2 => 'bg-danger',
                                                        3 => 'bg-warning text-dark',
                                                        4 => 'bg-secondary',
                                                        default => 'bg-secondary',
                                                    };

                                                    $descripcion = match ($tipoId) {
                                                        1 => 'Proveedor: ' .
                                                            ($item->supplier_name ?? 'N/A') .
                                                            ' - Almacén Destino: ' .
                                                            ($item->destination_name ?? 'N/A'),
                                                        2 => 'Almacén Origen: ' . ($item->origin_name ?? 'N/A'),
                                                        3 => 'Origen: ' .
                                                            ($item->origin_name ?? 'N/A') .
                                                            ' - Destino: ' .
                                                            ($item->destination_name ?? 'N/A'),
                                                        4 => 'En Almacén: ' . ($item->origin_name ?? 'N/A'),
                                                        default => '-',
                                                    };
                                                @endphp
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td><span class="badge {{ $badgeClass }}">{{ $tipoNombre }}</span>
                                                    </td>
                                                    <td>{{ $descripcion }}</td>
                                                    <td class="text-center fw-bold">{{ $item->amount }}</td>
                                                    <td>{{ $item->obs ?? '-' }}</td>
                                                    <td>{{ $item->user_name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <form action="{{ route('reports.movements.pdf') }}" method="GET" target="_blank"
                                    class="d-inline">
                                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                                    <button type="submit" class="btn bg-custom-btn-danger btn-sm"
                                        {{ empty($reportType) ? 'disabled' : '' }}>
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Generar PDF
                                    </button>
                                </form>
                                <form action="{{ route('reports.movements.excel') }}" method="GET" class="d-inline">
                                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                                    <button type="submit" class="btn bg-custom-btn-second btn-sm"
                                        {{ empty($reportType) ? 'disabled' : '' }}>
                                        <i class="bi bi-file-earmark-excel me-2"></i>Generar XLS
                                    </button>
                                </form>
                            </div>
                        @elseif(request()->has('generate'))
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle me-2"></i>
                                No se encontraron registros de movimientos para el tipo de reporte seleccionado.
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
