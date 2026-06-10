@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('home') }}" class="button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="svgIcon" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                        </svg>
                    </a>

                    <!-- Botón Crear Nuevo Usuario -->
                    <a class="btn btn-success text-light fw-bold" href="{{ route('usuarios.create') }}">
                        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                    </a>
                </div>

                <div class="card shadow">
                    <div class="card-header bg-custom-gradient text-white">
                        <h5 class="mb-0">Gestión de Usuarios</h5>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role == 1)
                                                    <span class="badge bg-primary">Admin</span>
                                                @elseif ($user->role == 2)
                                                    <span class="badge bg-warning text-dark">Editor</span>
                                                @else
                                                    <span class="badge bg-secondary">Visitante</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-center">
                                                <!-- Botón Ver Detalle -->
                                                <a href="{{ route('usuarios.show', $user->id) }}"
                                                    class="btn btn-sm btn-info me-1" title="Ver Detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <!-- Botón Editar -->
                                                <a href="{{ route('usuarios.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning me-1" title="Editar">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <!-- Botones para Conmutar Estado (Sin formulario, usan el modal) -->
                                                @if (Auth::id() !== $user->id)
                                                    @if ($user->active)
                                                        <button type="button"
                                                            class="btn btn-sm btn-danger btn-toggle-status"
                                                            title="Inhabilitar"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="inhabilitar">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="btn btn-sm btn-success btn-toggle-status"
                                                            title="Habilitar"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="habilitar">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay usuarios registrados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación Universal -->
    <div class="modal fade" id="confirmToggleModal" tabindex="-1" aria-labelledby="confirmToggleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-custom-gradient text-dark">
                    <h5 class="modal-title" id="confirmToggleModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmToggleModalBody">
                    ¿Estás seguro de realizar esta acción?
                </div>
                <div class="modal-footer">
                    <!-- Botón NO -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>

                    <!-- Formulario oculto que se enviará si dicen que SÍ -->
                    <form id="modalToggleForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" id="modalConfirmBtn">Sí</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-custom-gradient {
            background: #058fad;
            background: -webkit-linear-gradient(to right, #0b6b8b, #00B4DB);
            background: linear-gradient(to right, #0083B0, #00B4DB);
        }

        /* From Uiverse.io by absent452 */
        /* From Uiverse.io by vinodjangid07 */
        .button {
            width: 50px;
            height: 35px;
            border-radius: 20%;
            background-color: #0093BE;
            border: none;
            font-weight: 300;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px double #e9e9e9;
            border-radius: 15px;
            cursor: pointer;
            transition-duration: 0.3s;
            overflow: hidden;
            position: relative;
        }

        .svgIcon {
            width: 18px;
            transition-duration: 0.3s;
        }

        .svgIcon path {
            fill: #ffffff;
        }

        .button:hover {
            width: 140px;
            border-radius: 15px;
            transition-duration: 0.3s;
            background-color: #0084B1;
            align-items: center;
        }

        .button:hover .svgIcon {
            transition-duration: 0.3s;
            transform: translateY(-200%);
        }

        .button::before {
            position: absolute;
            bottom: -20px;
            content: "Regresar";
            color: #e9e9e9;
            font-size: 0px;
        }

        .button:hover::before {
            font-size: 15px;
            opacity: 1;
            bottom: unset;
            transition-duration: 0.3s;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar todos los botones que activan el modal
            const toggleButtons = document.querySelectorAll('.btn-toggle-status');
            const confirmModal = document.getElementById('confirmToggleModal');
            const modalTitle = document.getElementById('confirmToggleModalLabel');
            const modalBody = document.getElementById('confirmToggleModalBody');
            const modalForm = document.getElementById('modalToggleForm');
            const modalConfirmBtn = document.getElementById('modalConfirmBtn');

            // Crear instancia del modal de Bootstrap
            let bsModal = new bootstrap.Modal(confirmModal);

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Leer los datos del botón clickeado
                    const url = this.dataset.url;
                    const action = this.dataset.action; // 'inhabilitar' o 'habilitar'

                    // Cambiar el contenido del modal según la acción
                    if (action === 'inhabilitar') {
                        modalTitle.textContent = 'Confirmar Inhabilitación';
                        modalBody.textContent =
                            '¿Estás seguro de inhabilitar a este usuario? No podrá acceder al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Inhabilitar';
                        modalConfirmBtn.className = 'btn btn-danger'; // Botón rojo
                    } else {
                        modalTitle.textContent = 'Confirmar Habilitación';
                        modalBody.textContent =
                            '¿Estás seguro de habilitar a este usuario? Volverá a tener acceso al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Habilitar';
                        modalConfirmBtn.className = 'btn btn-success'; // Botón verde
                    }

                    // Asignar la URL al formulario del modal
                    modalForm.action = url;

                    // Mostrar el modal
                    bsModal.show();
                });
            });
        });
    </script>
@endpush
