@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm"><i
                            class="bi bi-box-arrow-left me-2"></i>Regresar</a>

                    <a href="{{ route('movements.index', ['from' => 'inventario']) }}"
                        class="btn bg-custom-btn-second btn-sm"><i class="bi bi-clock-history me-2"></i>Historial
                        Movimientos</a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Inventario Actual del Sistema</h5>
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
                                <strong>Por favor corrige los siguientes errores:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Botones: Refrescar y Nuevo Movimiento -->
                        <div class="d-flex justify-content-end mb-3 gap-2">
                            <!-- Botón Refrescar -->
                            <a href="{{ route('inventory.index') }}" class="btn bg-custom-btn-second btn-sm"
                                title="Refrescar datos del inventario">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refrescar
                            </a>
                            <!-- Botón Nuevo Movimiento -->
                            <button type="button" class="btn bg-custom-btn-on btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalTipoMovimiento">
                                <i class="bi bi-plus-circle me-2"></i> Nuevo Movimiento
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Categoría</th>
                                        <th>Unidad</th>
                                        <th>Almacén</th>
                                        <th class="text-center">Stock Actual</th>
                                        <th class="text-center">Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($inventories as $inv)
                                        <tr>
                                            <td>{{ $inv->equipment->name }}</td>
                                            <td>{{ $inv->equipment->category->name }}</td>
                                            <td>{{ $inv->equipment->unit->name }}</td>
                                            <td>{{ $inv->store->name }}</td>
                                            <td
                                                class="text-center fw-bold {{ $inv->stock <= $inv->equipment->umbral ? 'text-danger' : 'text-success' }}">
                                                {{ $inv->stock }}
                                            </td>
                                            <!-- NUEVOS BOTONES DETALLE -->
                                            <td class="text-center d-flex gap-2 align-items-center justify-content-center">
                                                <button type="button"
                                                    class="btn bg-custom-btn-second btn-sm btn-ver-equipo"
                                                    data-id="{{ $inv->equipment_id }}">
                                                    Equipamiento
                                                </button>
                                                <button type="button"
                                                    class="btn bg-custom-btn-on btn-sm btn-ver-inventario"
                                                    data-id="{{ $inv->id }}" data-store="{{ $inv->store->name }}"
                                                    data-stock="{{ $inv->stock }}"
                                                    data-umbral="{{ $inv->equipment->umbral }}"
                                                    data-last="{{ $inv->last_change ? $inv->last_change->format('d/m/Y H:i') : '-' }}">
                                                    Inventario
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay registros de inventario</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $inventories->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Seleccionar Tipo de Movimiento -->
    <div class="modal fade" id="modalTipoMovimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title">Tipo de Movimiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-grid gap-3">
                        <button type="button" class="btn bg-custom-btn-on py-3 btn-tipo-movimiento" data-tipo="1">1.
                            COMPRA EQUIPAMIENTO</button>
                        <button type="button" class="btn bg-custom-btn-second py-3 btn-tipo-movimiento" data-tipo="2">2.
                            SALIDA PARA INSTALACIÓN</button>
                        <button type="button" class="btn bg-custom-btn-terciary py-3 btn-tipo-movimiento" data-tipo="3">3.
                            TRASLADO ENTRE ALMACENES</button>
                        <button type="button" class="btn bg-custom-btn-danger py-3 btn-tipo-movimiento" data-tipo="4">4.
                            AJUSTE INVENTARIO (±)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Formularios de Movimiento -->
    <div class="modal fade" id="modalFormularioMovimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formMovimiento" action="{{ route('movements.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="movement_type" id="input_movement_type">

                    <div class="modal-header bg-custom-gradient text-white">
                        <h5 class="modal-title" id="tituloModalMovimiento">Movimiento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="bodyModalMovimiento">
                    </div>
                    <div class="modal-footer bg-footer">
                        <button type="button" class="btn bg-custom-btn-off btn-sm"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-custom-btn-on btn-sm">Procesar Movimiento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Detalle Equipamiento (Cambiado a modal-lg) -->
    <div class="modal fade" id="modalDetalleEquipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title">Detalle Equipamiento</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bodyDetalleEquipo">
                </div>
                <div class="modal-footer d-flex justify-content-center align-items-center gap-2 bg-footer">
                    <button type="button" class="btn bg-custom-btn-second btn-sm" id="btnAbrirEditarEquipo">Editar
                        Equipamiento</button>
                    <button type="button" class="btn bg-custom-btn-off btn-sm" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Detalle Inventario -->
    <div class="modal fade" id="modalDetalleInventario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-white">
                    <h5 class="modal-title">Detalle Inventario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="bodyDetalleInventario">
                </div>
                <div class="modal-footer bg-footer">
                    <button type="button" class="btn bg-custom-btn-off" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: Editar Equipamiento -->
    <div class="modal fade" id="modalEditarEquipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditarEquipo" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="modal-header bg-custom-gradient text-white">
                        <h5 class="modal-title">Editar Equipamiento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="bodyEditarEquipo"></div>
                    <div class="modal-footer bg-footer">
                        <button type="button" class="btn bg-custom-btn-off btn-sm"
                            id="btnCancelarEditarEquipo">Cancelar</button>
                        <button type="submit" class="btn bg-custom-btn-on btn-sm">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Contenedor de tamaño fijo con borde */
        .img-container-detalle {
            width: 100%;
            height: 160px;
            /* Tamaño fijo del contenedor */
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* Recorta lo que sobresalga */
        }

        /* La imagen se adapta al contenedor sin deformarse */
        .img-container-detalle img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            /* Mantiene la proporción y recorta si es necesario */
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap === 'undefined') {
                console.error("Error: Bootstrap no está definido.");
                return;
            }

            // Cargar equipos con sus relaciones para el modal de detalle
            const equipments = @json(App\Models\Equipment::with(['unit', 'category', 'brandModel'])->get());
            const stores = @json($stores);
            const suppliers = @json($suppliers);
            const categories = @json($categories);
            const brands = @json($brands);
            const units = @json($units);

            // NUEVA VARIABLE: Stocks agrupados por equipo
            const stocksData = @json($stocks);

            let modalTipo = new bootstrap.Modal(document.getElementById('modalTipoMovimiento'));
            let modalForm = new bootstrap.Modal(document.getElementById('modalFormularioMovimiento'));
            let modalDetalleEquipo = new bootstrap.Modal(document.getElementById('modalDetalleEquipo'));
            let modalDetalleInventario = new bootstrap.Modal(document.getElementById('modalDetalleInventario'));

            // Variables para el modal de edición
            let modalEditarEquipo = new bootstrap.Modal(document.getElementById('modalEditarEquipo'));
            let currentEqId = null;

            // Función para actualizar los almacenes según el equipo seleccionado (Salidas)
            window.actualizarAlmacenesSalida = function() {
                const eqId = document.getElementById('select_equipment_salida').value;
                const selectOrigin = document.getElementById('select_origin_salida');
                const stockInfo = document.getElementById('stock_info_salida');
                const amountInput = document.getElementById('input_amount_salida');

                // Resetear
                selectOrigin.innerHTML = '<option value="">Seleccione...</option>';
                stockInfo.textContent = '';
                amountInput.value = '';
                amountInput.removeAttribute('max');

                if (eqId && stocksData[eqId]) {
                    stocksData[eqId].forEach(inv => {
                        selectOrigin.innerHTML +=
                            `<option value="${inv.store_id}" data-stock="${inv.stock}">${inv.store.name} - existencia: ${inv.stock}</option>`;
                    });
                }
            };

            // Función para limitar la cantidad a retirar según el almacén seleccionado
            window.validarMaximoSalida = function() {
                const selectOrigin = document.getElementById('select_origin_salida');
                const selectedOption = selectOrigin.options[selectOrigin.selectedIndex];
                const amountInput = document.getElementById('input_amount_salida');
                const stockInfo = document.getElementById('stock_info_salida');

                if (selectedOption && selectedOption.dataset.stock) {
                    const maxStock = parseInt(selectedOption.dataset.stock);
                    amountInput.setAttribute('max', maxStock);
                    stockInfo.textContent = `Máximo a retirar: ${maxStock}`;

                    // Si el usuario escribe un número mayor, lo corrige automáticamente
                    if (parseInt(amountInput.value) > maxStock) {
                        amountInput.value = maxStock;
                    }
                } else {
                    amountInput.removeAttribute('max');
                    stockInfo.textContent = '';
                }
            };

            // Función para actualizar los almacenes según el equipo seleccionado (Ajuste)
            window.actualizarAlmacenesAjuste = function() {
                const eqId = document.getElementById('select_equipment_ajuste').value;
                const selectOrigin = document.getElementById('select_origin_ajuste');
                const stockInfo = document.getElementById('stock_info_ajuste');
                const amountInput = document.getElementById('input_amount_ajuste');

                // Resetear
                selectOrigin.innerHTML = '<option value="">Seleccione...</option>';
                stockInfo.textContent = '';
                amountInput.value = '';
                amountInput.removeAttribute('min');

                if (eqId && stocksData[eqId]) {
                    stocksData[eqId].forEach(inv => {
                        selectOrigin.innerHTML +=
                            `<option value="${inv.store_id}" data-stock="${inv.stock}">${inv.store.name} - existencia: ${inv.stock}</option>`;
                    });
                }
            };

            // Función para limitar el ajuste negativo según el almacén seleccionado
            window.validarMaximoAjuste = function() {
                const selectOrigin = document.getElementById('select_origin_ajuste');
                const selectedOption = selectOrigin.options[selectOrigin.selectedIndex];
                const amountInput = document.getElementById('input_amount_ajuste');
                const stockInfo = document.getElementById('stock_info_ajuste');

                if (selectedOption && selectedOption.dataset.stock) {
                    const maxStock = parseInt(selectedOption.dataset.stock);
                    stockInfo.textContent = `Existencia actual: ${maxStock}. (Mínimo permitido: -${maxStock})`;

                    // Establecer el mínimo negativo permitido (no puede bajar de -maxStock)
                    amountInput.setAttribute('min', -maxStock);
                } else {
                    amountInput.removeAttribute('min');
                    stockInfo.textContent = '';
                }
            };

            // Función para actualizar los almacenes según el equipo seleccionado (Traslados)
            window.actualizarAlmacenesTraslado = function() {
                const eqId = document.getElementById('select_equipment_traslado').value;
                const selectOrigin = document.getElementById('select_origin_traslado');
                const stockInfo = document.getElementById('stock_info_traslado');
                const amountInput = document.getElementById('input_amount_traslado');
                const selectDestination = document.getElementById('select_destination_traslado');

                // Resetear
                selectOrigin.innerHTML = '<option value="">Seleccione...</option>';
                selectDestination.innerHTML = '<option value="">Seleccione origen primero...</option>';
                stockInfo.textContent = '';
                amountInput.value = '';
                amountInput.removeAttribute('max');

                if (eqId && stocksData[eqId]) {
                    stocksData[eqId].forEach(inv => {
                        selectOrigin.innerHTML +=
                            `<option value="${inv.store_id}" data-stock="${inv.stock}">${inv.store.name} - existencia: ${inv.stock}</option>`;
                    });
                }
            };

            // Función para limitar la cantidad a trasladar y filtrar destino
            window.validarMaximoTraslado = function() {
                const selectOrigin = document.getElementById('select_origin_traslado');
                const selectedOption = selectOrigin.options[selectOrigin.selectedIndex];
                const amountInput = document.getElementById('input_amount_traslado');
                const stockInfo = document.getElementById('stock_info_traslado');
                const selectDestination = document.getElementById('select_destination_traslado');
                const originId = selectOrigin.value;

                if (selectedOption && selectedOption.dataset.stock) {
                    const maxStock = parseInt(selectedOption.dataset.stock);
                    amountInput.setAttribute('max', maxStock);
                    stockInfo.textContent = `Máximo a trasladar: ${maxStock}`;

                    if (parseInt(amountInput.value) > maxStock) {
                        amountInput.value = maxStock;
                    }
                } else {
                    amountInput.removeAttribute('max');
                    stockInfo.textContent = '';
                }

                // Llenar destino con todos los almacenes EXCEPTO el de origen
                selectDestination.innerHTML = '<option value="">Seleccione...</option>' +
                    stores.filter(s => s.id != originId).map(s => `<option value="${s.id}">${s.name}</option>`)
                    .join('');
            };

            // Función reutilizable para dibujar el detalle del equipo
            function renderDetalleEquipo(eq) {
                const img1Src = eq.img_url_one ? `/storage/${eq.img_url_one}` : '';
                const img2Src = eq.img_url_two ? `/storage/${eq.img_url_two}` : '';
                return `
                    <div class="row">
                        <div class="col-md-7">
                            <ul class="list-group mt-5">
                                <li class="list-group-item"><strong>SKU:</strong> ${eq.sku}</li>
                                <li class="list-group-item"><strong>Nombre:</strong> ${eq.name}</li>
                                <li class="list-group-item"><strong>Categoría:</strong> ${eq.category?.name ?? 'N/A'}</li>
                                <li class="list-group-item"><strong>Marca/Modelo:</strong> ${eq.brand_model ? eq.brand_model.brand + ' - ' + eq.brand_model.model : 'N/A'}</li>
                                <li class="list-group-item"><strong>Unidad:</strong> ${eq.unit?.name ?? 'N/A'}</li>
                                <li class="list-group-item"><strong>Umbral Mínimo:</strong> ${eq.umbral}</li>
                                <li class="list-group-item"><strong>Estado:</strong> ${eq.active ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex flex-column gap-3 mt-3 mt-md-0">
                                <div>
                                    <label class="form-label fw-bold mb-1">Imagen 1</label>
                                    <div class="img-container-detalle">
                                        ${img1Src ? `<a href="${img1Src}" target="_blank"><img src="${img1Src}" alt="Imagen 1"></a>` : '<span class="text-muted small">Sin imagen</span>'}
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label fw-bold mb-1">Imagen 2</label>
                                    <div class="img-container-detalle">
                                        ${img2Src ? `<a href="${img2Src}" target="_blank"><img src="${img2Src}" alt="Imagen 2"></a>` : '<span class="text-muted small">Sin imagen</span>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // LÓGICA: ABRIR DETALLE EQUIPAMIENTO
            document.querySelectorAll('.btn-ver-equipo').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentEqId = parseInt(this.getAttribute('data-id'));
                    const eq = equipments.find(e => e.id === currentEqId);
                    if (!eq) return;
                    document.getElementById('bodyDetalleEquipo').innerHTML = renderDetalleEquipo(
                        eq);
                    modalDetalleEquipo.show();
                });
            });

            // LÓGICA: ABRIR EDITAR EQUIPAMIENTO DESDE EL DETALLE
            document.getElementById('btnAbrirEditarEquipo').addEventListener('click', function() {
                const eq = equipments.find(e => e.id === currentEqId);
                if (!eq) return;

                let html = `
                    <div class="row mb-3">
                        <div class="col-md-4"><label class="form-label fw-bold">SKU</label><input type="text" name="sku" class="form-control" value="${eq.sku}" required></div>
                        <div class="col-md-8"><label class="form-label fw-bold">Nombre</label><input type="text" name="name" class="form-control" value="${eq.name}" required></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Categoría</label>
                            <select name="category_id" class="form-select" required>
                                ${categories.map(c => `<option value="${c.id}" ${eq.category_id == c.id ? 'selected' : ''}>${c.name}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Marca/Modelo</label>
                            <select name="brand_model_id" class="form-select" required>
                                ${brands.map(b => `<option value="${b.id}" ${eq.brand_model_id == b.id ? 'selected' : ''}>${b.brand} - ${b.model}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Unidad</label>
                            <select name="unit_id" class="form-select" required>
                                ${units.map(u => `<option value="${u.id}" ${eq.unit_id == u.id ? 'selected' : ''}>${u.name}</option>`).join('')}
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Umbral Mínimo</label>
                            <input type="number" name="umbral" class="form-control" min="0" value="${eq.umbral}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estado</label>
                            <select name="active" class="form-select" required>
                                <option value="1" ${eq.active ? 'selected' : ''}>Activo</option>
                                <option value="0" ${!eq.active ? 'selected' : ''}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Reemplazar Imagen 1</label>
                            <input type="file" name="img_url_one" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Reemplazar Imagen 2</label>
                            <input type="file" name="img_url_two" class="form-control" accept="image/*">
                        </div>
                    </div>
                `;
                document.getElementById('bodyEditarEquipo').innerHTML = html;
                document.getElementById('formEditarEquipo').action = `/equipments/${currentEqId}`;

                modalDetalleEquipo.hide();
                modalEditarEquipo.show();
            });

            // LÓGICA: CANCELAR EDICIÓN (Volver a detalle)
            document.getElementById('btnCancelarEditarEquipo').addEventListener('click', function() {
                modalEditarEquipo.hide();
                modalDetalleEquipo.show();
            });

            // LÓGICA: GUARDAR CAMBIOS EQUIPO (AJAX)
            document.getElementById('formEditarEquipo').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Actualizar el arreglo local de equipos
                            const index = equipments.findIndex(e => e.id === data.equipment.id);
                            if (index !== -1) {
                                equipments[index] = data.equipment;
                            }
                            // Re-dibujar el detalle con los nuevos datos
                            document.getElementById('bodyDetalleEquipo').innerHTML =
                                renderDetalleEquipo(data.equipment);
                            // Volver al modal de detalle
                            modalEditarEquipo.hide();
                            modalDetalleEquipo.show();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // LÓGICA: DETALLE INVENTARIO
            document.querySelectorAll('.btn-ver-inventario').forEach(btn => {
                btn.addEventListener('click', function() {
                    const store = this.getAttribute('data-store');
                    const stock = this.getAttribute('data-stock');
                    const umbral = this.getAttribute('data-umbral');
                    const last = this.getAttribute('data-last');

                    let html = `
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Almacén:</strong> ${store}</li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Stock Actual:</strong> 
                                <span class="fw-bold ${parseInt(stock) <= parseInt(umbral) ? 'text-danger' : 'text-success'}">${stock}</span>
                            </li>
                            <li class="list-group-item"><strong>Umbral Configurado:</strong> ${umbral}</li>
                            <li class="list-group-item"><strong>Último Cambio:</strong> ${last}</li>
                        </ul>
                    `;
                    document.getElementById('bodyDetalleInventario').innerHTML = html;
                    modalDetalleInventario.show();
                });
            });

            // =======================================================
            // LÓGICA: MOVIMIENTOS (Existente)
            // =======================================================
            document.querySelectorAll('.btn-tipo-movimiento').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tipo = this.getAttribute('data-tipo');
                    abrirModalMovimiento(tipo);
                });
            });

            function abrirModalMovimiento(tipo) {
                modalTipo.hide();
                document.getElementById('input_movement_type').value = tipo;

                let html = '';
                const titulo = document.getElementById('tituloModalMovimiento');

                if (tipo == 1) {
                    titulo.textContent = '1. Compra de Equipamiento';
                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Equipo Existente (seleccione: ➕ CREAR NUEVO EQUIPO si es un nuevo equipo)</label>
                            <select name="equipment_id" class="form-select" onchange="document.getElementById('nuevoEquipoSection').style.display = this.value === '' ? 'block' : 'none'">                               

                                <optgroup label="─── ACCIONES ───">
                                    <option value="" style="font-weight: bold; color: #0d6efd;">➕ CREAR NUEVO EQUIPO</option>
                                </optgroup>
                                <optgroup label="─── EQUIPOS EXISTENTES ───">
                                    ${equipments.map(e => `<option value="${e.id}">${e.sku} - ${e.name}</option>`).join('')}
                                </optgroup>

                            </select>
                        </div>
                        
                        <!-- DIV QUE SE OCULTA/MUESTRA -->
                        <div id="nuevoEquipoSection">
                            <hr style="border: 2px solid #1e3660; opacity: 1; margin: 5px 0;">
                            <h6>Datos Nuevo Equipo (Solo si seleccionó CREAR NUEVO)</h6>
                            <div class="row mb-2">
                                <div class="col-md-4"><input type="text" name="new_sku" class="form-control" placeholder="SKU"></div>
                                <div class="col-md-8"><input type="text" name="new_name" class="form-control" placeholder="Nombre Equipo"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <select name="category_id" class="form-select"><option value="">Categoría</option>${categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}</select>
                                </div>
                                <div class="col-md-4">
                                    <select name="brand_model_id" class="form-select"><option value="">Marca/Modelo</option>${brands.map(b => `<option value="${b.id}">${b.brand} - ${b.model}</option>`).join('')}</select>
                                </div>
                                <div class="col-md-4">
                                    <select name="unit_id" class="form-select"><option value="">Seleccione Unidad</option>${units.map(u => `<option value="${u.id}">${u.name}</option>`).join('')}</select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Umbral Mínimo</label>
                                    <input type="number" name="umbral" class="form-control" min="0" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Imagen 1</label>
                                    <input type="file" name="img_url_one" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Imagen 2</label>
                                    <input type="file" name="img_url_two" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <!-- FIN DIV -->

                        <hr style="border: 2px solid #1e3660; opacity: 1; margin: 5px 0;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Proveedor</label>
                                <select name="supplier_id" class="form-select" required><option value="">Seleccione...</option>${suppliers.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}</select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Almacén Destino</label>
                                <select name="destination_id" class="form-select" required><option value="">Seleccione...</option>${stores.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}</select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label class="form-label fw-bold">Cantidad</label><input type="number" name="amount" class="form-control" min="1" required></div>
                            <div class="col-md-8"><label class="form-label fw-bold">Observación</label><input type="text" name="obs" class="form-control"></div>
                        </div>`;
                } else if (tipo == 2) {
                    titulo.textContent = '2. Salida para Instalación';

                    // Filtrar solo equipos que tienen stock > 0 en stocksData
                    let equiposConStock = Object.keys(stocksData).map(eqId => {
                        const eq = equipments.find(e => e.id == eqId);
                        if (!eq) return '';
                        return `<option value="${eq.id}">${eq.name}</option>`;
                    }).join('');

                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Equipo (Solo con existencia)</label>
                            <select name="equipment_id" id="select_equipment_salida" class="form-select" required onchange="actualizarAlmacenesSalida()">
                                <option value="">Seleccione...</option>
                                ${equiposConStock}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Almacén Origen</label>
                            <select name="origin_id" id="select_origin_salida" class="form-select" required onchange="validarMaximoSalida()">
                                <option value="">Seleccione un equipo primero...</option>
                            </select>
                            <small id="stock_info_salida" class="form-text text-muted fw-bold"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label class="form-label fw-bold">Cantidad a Retirar</label><input type="number" name="amount" id="input_amount_salida" class="form-control" min="1" required></div>
                            <div class="col-md-8"><label class="form-label fw-bold">Observación (Opcional)</label><input type="text" name="obs" class="form-control"></div>
                        </div>`;
                } else if (tipo == 3) {
                    titulo.textContent = '3. Traslado entre Almacenes';

                    // Filtrar solo equipos que tienen stock > 0 en stocksData
                    let equiposConStock = Object.keys(stocksData).map(eqId => {
                        const eq = equipments.find(e => e.id == eqId);
                        if (!eq) return '';
                        return `<option value="${eq.id}">${eq.name}</option>`;
                    }).join('');

                    html += `
                        <div class="mb-3">
                            <label class="form-label fw-bold">Equipo (Solo con existencia)</label>
                            <select name="equipment_id" id="select_equipment_traslado" class="form-select" required onchange="actualizarAlmacenesTraslado()">
                                <option value="">Seleccione...</option>
                                ${equiposConStock}
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Almacén Origen</label>
                                <select name="origin_id" id="select_origin_traslado" class="form-select" required onchange="validarMaximoTraslado()">
                                    <option value="">Seleccione un equipo primero...</option>
                                </select>
                                <small id="stock_info_traslado" class="form-text text-muted fw-bold"></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Almacén Destino</label>
                                <select name="destination_id" id="select_destination_traslado" class="form-select" required>
                                    <option value="">Seleccione origen primero...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label class="form-label fw-bold">Cantidad a Trasladar</label><input type="number" name="amount" id="input_amount_traslado" class="form-control" min="1" required></div>
                            <div class="col-md-8"><label class="form-label fw-bold">Observación</label><input type="text" name="obs" class="form-control"></div>
                        </div>`;
                } else if (tipo == 4) {
                    titulo.textContent = '4. Ajuste de Inventario (±)';

                    // Filtrar solo equipos que tienen stock > 0 en stocksData
                    let equiposConStock = Object.keys(stocksData).map(eqId => {
                        const eq = equipments.find(e => e.id == eqId);
                        if (!eq) return '';
                        return `<option value="${eq.id}">${eq.sku} - ${eq.name}</option>`;
                    }).join('');

                    html += `
                        <div class="alert alert-warning">Para ajustar, ingrese cantidad positiva para sumar o negativa para restar.</div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Equipo (Solo con existencia)</label>
                            <select name="equipment_id" id="select_equipment_ajuste" class="form-select" required onchange="actualizarAlmacenesAjuste()">
                                <option value="">Seleccione...</option>
                                ${equiposConStock}
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Almacén</label>
                            <select name="origin_id" id="select_origin_ajuste" class="form-select" required onchange="validarMaximoAjuste()">
                                <option value="">Seleccione un equipo primero...</option>
                            </select>
                            <small id="stock_info_ajuste" class="form-text text-muted fw-bold"></small>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><label class="form-label fw-bold">Cantidad (+/-)</label><input type="number" name="amount" id="input_amount_ajuste" class="form-control" required></div>
                            <div class="col-md-8"><label class="form-label fw-bold">Motivo del Ajuste</label><input type="text" name="obs" class="form-control" required></div>
                        </div>`;
                }

                document.getElementById('bodyModalMovimiento').innerHTML = html;
                setTimeout(() => modalForm.show(), 300);
            }
        });
    </script>
@endpush
