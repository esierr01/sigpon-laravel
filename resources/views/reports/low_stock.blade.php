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
                        <h5 class="mb-0 fw-bold">Reporte Inventario (Bajo Stock)</h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('reports.low_stock') }}" method="GET" class="mb-4">
                            <div class="row align-items-end g-3">
                                <div class="col-md-6">
                                    <label for="report_type" class="form-label fw-bold">Tipo de Reporte</label>
                                    <select name="report_type" id="report_type" class="form-select" required>
                                        <option value="">Seleccione una opción...</option>
                                        <option value="low_by_equipment"
                                            {{ $reportType == 'low_by_equipment' ? 'selected' : '' }}>
                                            Equipamiento Bajo Stock (ordenado por equipo)
                                        </option>
                                        <option value="low_by_store" {{ $reportType == 'low_by_store' ? 'selected' : '' }}>
                                            Equipamiento Bajo Stock (Ordenado por Almacén)
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
                                                <th>Equipo</th>
                                                <th>SKU</th>
                                                <th>Categoría</th>
                                                <th>Marca/Modelo</th>
                                                <th>Unidad</th>
                                                <th class="text-center">Stock</th>
                                                <th>Almacén</th>
                                                <th class="text-center">Umbral Mínimo</th>
                                                <th>Fecha Último Cambio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $item)
                                                <tr>
                                                    <td>{{ $item->equipment_name }}</td>
                                                    <td>{{ $item->sku }}</td>
                                                    <td>{{ $item->category_name ?? 'N/A' }}</td>
                                                    <td>{{ $item->brand ?? 'N/A' }} - {{ $item->model ?? 'N/A' }}</td>
                                                    <td>{{ $item->unit_name ?? 'N/A' }}</td>
                                                    <td class="text-center fw-bold text-danger">
                                                        {{ $item->stock }}
                                                    </td>
                                                    <td>{{ $item->store_name ?? 'N/A' }}</td>
                                                    <td class="text-center">{{ $item->umbral }}</td>
                                                    <td>
                                                        {{ $item->last_change ? \Carbon\Carbon::parse($item->last_change)->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <form action="{{ route('reports.low_stock.pdf') }}" method="GET" target="_blank"
                                    class="d-inline">
                                    <input type="hidden" name="report_type" value="{{ $reportType }}">
                                    <button type="submit" class="btn bg-custom-btn-danger btn-sm"
                                        {{ empty($reportType) ? 'disabled' : '' }}>
                                        <i class="bi bi-file-earmark-pdf me-2"></i>Generar PDF
                                    </button>
                                </form>
                                <form action="{{ route('reports.low_stock.excel') }}" method="GET" class="d-inline">
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
                                No se encontraron registros con bajo stock para el tipo de reporte seleccionado.
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
