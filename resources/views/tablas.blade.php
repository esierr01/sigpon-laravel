@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm mb-3"><i
                        class="bi bi-box-arrow-left me-2"></i>Regresar</a>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5>
                            Gestión de Tablas del Sistema (Tabla de
                            @if ($activeTab == 'categories')
                                Categorías
                            @elseif ($activeTab == 'units')
                                Unidades
                            @elseif ($activeTab == 'brand_models')
                                Marcas/Modelos
                            @elseif ($activeTab == 'suppliers')
                                Proveedores
                            @elseif ($activeTab == 'stores')
                                Almacenes
                            @endif
                            )
                        </h5>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error:</strong> {{ $errors->first('error_general') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Pestañas de Navegación -->
                        <ul class="nav nav-tabs mb-1" id="myTab" role="tablist">
                            <li class="nav-item"><a class="nav-link {{ $activeTab == 'categories' ? 'active' : '' }}"
                                    href="{{ route('tablas.index', ['tab' => 'categories']) }}">Categorías</a></li>
                            <li class="nav-item"><a class="nav-link {{ $activeTab == 'units' ? 'active' : '' }}"
                                    href="{{ route('tablas.index', ['tab' => 'units']) }}">Unidades</a></li>
                            <li class="nav-item"><a class="nav-link {{ $activeTab == 'brand_models' ? 'active' : '' }}"
                                    href="{{ route('tablas.index', ['tab' => 'brand_models']) }}">Marcas/Modelos</a>
                            </li>
                            <li class="nav-item"><a class="nav-link {{ $activeTab == 'suppliers' ? 'active' : '' }}"
                                    href="{{ route('tablas.index', ['tab' => 'suppliers']) }}">Proveedores</a></li>
                            <li class="nav-item"><a class="nav-link {{ $activeTab == 'stores' ? 'active' : '' }}"
                                    href="{{ route('tablas.index', ['tab' => 'stores']) }}">Almacenes</a></li>
                        </ul>

                        <!-- Botón Agregar según pestaña -->
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn bg-custom-btn-first btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalCrear">
                                <i class="bi bi-plus-circle me-2"></i> Nuevo Registro
                            </button>
                        </div>

                        <!-- Contenido de la tabla dinámica -->
                        <div class="table-responsive">
                            @php
                                // Definir columnas según la pestaña
                                $columns = match ($activeTab) {
                                    'categories' => ['Nombre'],
                                    'units' => ['Nombre'],
                                    'brand_models' => ['Marca', 'Modelo'],
                                    'suppliers' => ['Nombre', 'RIF', 'Teléfono', 'Contacto'],
                                    'stores' => ['Nombre', 'Teléfono', 'Contacto'],
                                    default => [],
                                };
                            @endphp

                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        @foreach ($columns as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                        <th>Fecha Creación</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $item)
                                        <tr>
                                            <!-- Columnas dinámicas -->
                                            @if ($activeTab == 'categories' || $activeTab == 'units')
                                                <td style="width: 600px">{{ $item->name }}</td>
                                            @elseif($activeTab == 'brand_models')
                                                <td style="width: 350px">{{ $item->brand }}</td>
                                                <td style="width: 350px">{{ $item->model }}</td>
                                            @elseif($activeTab == 'suppliers')
                                                <td style="width: 300px">{{ $item->name }}</td>
                                                <td>{{ $item->rif }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->contact }}</td>
                                            @elseif($activeTab == 'stores')
                                                <td style="width: 400px">{{ $item->name }}</td>
                                                <td>{{ $item->phone }}</td>
                                                <td>{{ $item->contact }}</td>
                                            @endif

                                            <td>{{ $item->created_at->format('d/m/Y H:i:s') }}</td>

                                            <td class="text-center d-flex gap-3 align-items-center justify-content-center">

                                                @if ($activeTab == 'suppliers' || $activeTab == 'stores')
                                                    <!-- Botón Detalle (Solo Proveedores y Almacenes) -->
                                                    <button type="button"
                                                        class="btn bg-custom-btn-on btn-sm btn-ver-detalle"
                                                        data-item="{{ json_encode($item) }}"
                                                        data-tabla="{{ $activeTab }}">
                                                        Detalle
                                                    </button>
                                                @endif

                                                <!-- Botón Editar (Abre modal) -->
                                                <button type="button" class="btn bg-custom-btn-second btn-sm btn-editar"
                                                    data-id="{{ $item->id }}" data-tabla="{{ $activeTab }}"
                                                    data-item="{{ json_encode($item) }}">
                                                    Editar
                                                </button>

                                                <!-- Botón Eliminar (Usa el modal existente) -->
                                                <button type="button"
                                                    class="btn bg-custom-btn-danger btn-sm btn-toggle-status"
                                                    data-url="{{ route('tablas.destroy', ['tabla' => $activeTab, 'id' => $item->id]) }}"
                                                    data-action="eliminar">
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No hay registros</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">{{ $data->appends(['tab' => $activeTab])->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Registro -->
    <div class="modal fade" id="modalCrear" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formCrear" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-custom-gradient text-white">
                        <h5 class="modal-title">Nuevo Registro</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="bodyCrear">
                        <!-- Los inputs se inyectarán por JavaScript según la pestaña -->
                    </div>
                    <div class="modal-footer bg-footer">
                        <button type="button" class="btn bg-custom-btn-off btn-sm"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-custom-btn-on btn-sm">Guardar Datos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Registro -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formEditar" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-custom-gradient text-white">
                        <h5 class="modal-title">Editar Registro</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="bodyEditar">
                        <!-- Los inputs se inyectarán por JavaScript -->
                    </div>
                    <div class="modal-footer bg-footer">
                        <button type="button" class="btn bg-custom-btn-off btn-sm"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-custom-btn-on btn-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Universal (Reutilizado) -->
    <div class="modal fade" id="confirmToggleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title" id="confirmToggleModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="confirmToggleModalBody">¿Estás seguro de realizar esta acción?</div>
                <div class="modal-footer bg-footer">
                    <button type="button" class="btn bg-custom-btn-off btn-sm" data-bs-dismiss="modal">No</button>
                    <form id="modalToggleForm" method="POST" action="">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn bg-custom-btn-danger btn-sm" id="modalConfirmBtn">Sí,
                            Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: Detalle Registro (Proveedores/Almacenes) -->
    <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title">Detalle del Registro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bodyDetalle">
                    <!-- Contenido inyectado por JS -->
                </div>
                <div class="modal-footer bg-footer">
                    <button type="button" class="btn bg-custom-btn-off btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .nav-tabs {
            border-bottom: none;
            gap: 8px;
        }

        .nav-tabs .nav-link {
            border-radius: 8px;
            padding: 2px 20px;
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
            color: #0093BE;
        }

        .nav-tabs .nav-link.active {
            background-color: #0093BE;
            color: white;
            border-color: #0093BE;
            box-shadow: 0 2px 8px rgba(0, 147, 190, 0.3);
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = '{{ $activeTab }}';

            // --- LÓGICA PARA MODAL ELIMINAR (Reutilizada) ---
            const toggleButtons = document.querySelectorAll('.btn-toggle-status');
            const confirmModal = document.getElementById('confirmToggleModal');
            const modalForm = document.getElementById('modalToggleForm');
            let bsModalConfirm = new bootstrap.Modal(confirmModal);

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    document.getElementById('confirmToggleModalBody').textContent =
                        '¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.';
                    modalForm.action = this.dataset.url;
                    bsModalConfirm.show();
                });
            });

            // --- LÓGICA PARA MODAL CREAR ---
            const modalCrear = new bootstrap.Modal(document.getElementById('modalCrear'));
            const formCrear = document.getElementById('formCrear');
            const bodyCrear = document.getElementById('bodyCrear');

            document.querySelector('[data-bs-target="#modalCrear"]').addEventListener('click', function() {
                formCrear.action = '{{ route('tablas.store', ['tabla' => $activeTab]) }}';
                bodyCrear.innerHTML = getFormFields(activeTab);
                modalCrear.show();
            });

            // --- LÓGICA PARA MODAL DETALLE ---
            const modalDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));
            const bodyDetalle = document.getElementById('bodyDetalle');

            document.querySelectorAll('.btn-ver-detalle').forEach(btn => {
                btn.addEventListener('click', function() {
                    const item = JSON.parse(this.dataset.item);
                    const tabla = this.dataset.tabla;

                    let html = '<ul class="list-group list-group-flush">';

                    if (tabla === 'suppliers') {
                        html +=
                            `<li class="list-group-item"><strong>Nombre / Razón Social:</strong> ${item.name}</li>`;
                        html +=
                        `<li class="list-group-item"><strong>RIF:</strong> ${item.rif}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Dirección:</strong> ${item.address}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Teléfono:</strong> ${item.phone}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Persona de Contacto:</strong> ${item.contact}</li>`;
                    } else if (tabla === 'stores') {
                        html +=
                            `<li class="list-group-item"><strong>Nombre del Almacén:</strong> ${item.name}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Dirección:</strong> ${item.address}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Teléfono:</strong> ${item.phone}</li>`;
                        html +=
                            `<li class="list-group-item"><strong>Persona de Contacto:</strong> ${item.contact}</li>`;
                    }

                    // Formatear fecha
                    const fecha = new Date(item.created_at).toLocaleString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    html +=
                        `<li class="list-group-item"><strong>Fecha de Creación:</strong> ${fecha}</li>`;

                    html += '</ul>';
                    bodyDetalle.innerHTML = html;
                    modalDetalle.show();
                });
            });

            // --- LÓGICA PARA MODAL EDITAR ---
            const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
            const formEditar = document.getElementById('formEditar');
            const bodyEditar = document.getElementById('bodyEditar');

            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = JSON.parse(this.dataset.item);

                    formEditar.action = '/tablas/' + activeTab + '/' + id;
                    bodyEditar.innerHTML = getFormFields(activeTab, item);
                    modalEditar.show();
                });
            });

            // --- FUNCIONES AUXILIARES PARA GENERAR HTML DEL FORMULARIO ---
            function fieldHTML(label, name, value = '', type = 'text') {
                return `<div class="mb-3">
                    <label class="form-label fw-bold">${label}</label>
                    <input type="${type}" class="form-control" name="${name}" value="${value}" required>
                </div>`;
            }

            function getFormFields(tab, data = {}) {
                let html = '';
                switch (tab) {
                    case 'categories':
                    case 'units':
                        html += fieldHTML('Nombre', 'name', data.name || '');
                        break;
                    case 'brand_models':
                        html += fieldHTML('Marca', 'brand', data.brand || '');
                        html += fieldHTML('Modelo', 'model', data.model || '');
                        break;
                    case 'suppliers':
                        html += fieldHTML('Nombre / Razón Social', 'name', data.name || '');
                        html += fieldHTML('RIF', 'rif', data.rif || '');
                        html += fieldHTML('Dirección', 'address', data.address || '');
                        html += fieldHTML('Teléfono', 'phone', data.phone || '');
                        html += fieldHTML('Persona de Contacto', 'contact', data.contact || '');
                        break;
                    case 'stores':
                        html += fieldHTML('Nombre del Almacén', 'name', data.name || '');
                        html += fieldHTML('Dirección', 'address', data.address || '');
                        html += fieldHTML('Teléfono', 'phone', data.phone || '');
                        html += fieldHTML('Persona de Contacto', 'contact', data.contact || '');
                        break;
                }
                return html;
            }
        });
    </script>
@endpush
