@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @php
                        // Determinar a dónde regresar basado en el parámetro 'from' de la URL
                        $backUrl = request('from') === 'inventario' ? route('inventory.index') : route('home');
                    @endphp
                    <a href="{{ $backUrl }}" class="btn bg-custom-btn-on btn-sm"><i
                            class="bi bi-box-arrow-left me-2"></i>Regresar</a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Historial de Movimientos</h5>

                            <!-- Formulario de búsqueda -->
                            <form action="{{ route('movements.index') }}" method="GET"
                                class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width: 350px;">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Equipo, tipo, proveedor, almacén..." value="{{ request('search') }}"
                                        aria-label="Buscar movimientos">
                                    <button type="submit" class="btn btn-light" title="Buscar">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                @if (request('search'))
                                    <a href="{{ route('movements.index') }}" class="btn btn-sm btn-outline-light"
                                        title="Limpiar filtros">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Equipo</th>
                                        <th>Cantidad</th>
                                        <th>Proveedor</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $mov)
                                        <tr>
                                            <td style="width: 110px;">{{ $mov->created_at->format('d/m/Y') }}</td>
                                            <td style="width: 90px;">
                                                @php
                                                    $tipoId = $mov->movement_type;
                                                    $tipoNombre = $movementTypes[$tipoId] ?? 'Desconocido';
                                                    $badgeClass = match ($tipoId) {
                                                        1 => 'bg-success',
                                                        2 => 'bg-danger',
                                                        3 => 'bg-warning text-dark',
                                                        4 => 'bg-secondary',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $tipoNombre }}
                                                </span>
                                            </td>
                                            <td style="width: 300px;">{{ $mov->equipment->name ?? 'N/A' }}</td>
                                            <td style="width: 80px; text-align: center;" class="fw-bold">
                                                {{ $mov->amount }}</td>
                                            <td style="width: 130px; font-size: 0.9rem;">{{ $mov->supplier->name ?? '-' }}
                                            </td>
                                            <td style="width: 130px; font-size: 0.9rem;">{{ $mov->origin->name ?? '-' }}
                                            <td style="width: 130px; font-size: 0.9rem;">
                                                {{ $mov->destination->name ?? '-' }}
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn bg-custom-btn-second btn-sm btn-ver-detalle"
                                                    data-id="{{ $mov->id }}">
                                                    Detalle
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Sin movimientos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $movements->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalle Movimiento -->
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title">Detalle del Movimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bodyDetalleMovimiento">
                    <div class="text-center">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-custom-btn-off" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>

    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));

            document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('bodyDetalleMovimiento').innerHTML =
                        '<div class="text-center"><div class="spinner-border text-primary"></div></div>';
                    modalDetalle.show();

                    fetch(`/movimientos/${id}`)
                        .then(res => res.json())
                        .then(data => {
                            let html = `<ul class="list-group">
                                <li class="list-group-item"><strong>Fecha:</strong> ${data.created_at ? new Date(data.created_at).toLocaleString('es-ES', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:false}) : 'N/A'}</li>

                                <li class="list-group-item">
                                    <strong>Tipo:</strong>
                                    ${(() => {
                                        const tipos = {
                                            1: { name: 'Compra', class: 'bg-success' },
                                            2: { name: 'Salida', class: 'bg-danger' },
                                            3: { name: 'Traslado', class: 'bg-warning text-dark' },
                                            4: { name: 'Ajuste', class: 'bg-secondary' }
                                        };
                                        const tipo = tipos[data.movement_type] || { name: 'N/A', class: 'bg-secondary' };
                                        return `<span class="badge ${tipo.class}">${tipo.name}</span>`;
                                    })()}
                                </li>

                                <li class="list-group-item"><strong>Equipo:</strong> ${data.equipment?.name ?? 'N/A'}</li>
                                
                                ${data.supplier?.name != null ? `<li class="list-group-item"><strong>Proveedor:</strong> ${data.supplier?.name ?? 'N/A'}</li>` : ''}
                                
                                ${data.origin?.name != null ? `<li class="list-group-item"><strong>Origen:</strong> ${data.origin?.name ?? 'N/A'}</li>` : ''}
                                
                                ${data.destination?.name != null ? `<li class="list-group-item"><strong>Destino:</strong> ${data.destination?.name ?? 'N/A'}</li>` : ''}
                                
                                <li class="list-group-item"><strong>Cantidad:</strong> ${data.amount}</li>
                                <li class="list-group-item"><strong>Observación:</strong> ${data.obs ?? 'Sin obs'}</li>
                                <li class="list-group-item"><strong>Realizado por:</strong> ${data.user?.name ?? 'N/A'}</li>
                            </ul>`;
                            document.getElementById('bodyDetalleMovimiento').innerHTML = html;
                        });
                });
            });
        });
    </script>
@endpush
