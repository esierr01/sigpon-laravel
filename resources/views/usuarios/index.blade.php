@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('home') }}" class="btn bg-custom-btn-on btn-sm"><i class="bi bi-box-arrow-left me-2"></i>
                        Regresar</a>

                    <a class="btn bg-custom-btn-on btn-sm" href="{{ route('usuarios.create') }}">
                        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                    </a>
                </div>

                <div class="card shadow">
                    <!-- Cabecera con Barra de Búsqueda -->
                    <div class="card-header bg-custom-gradient text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Gestión de Usuarios</h5>

                            <form action="{{ route('usuarios.index') }}" method="GET"
                                class="d-flex gap-2 align-items-center">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Buscar nombre, correo, rol, estado..." value="{{ request('search') }}"
                                        aria-label="Buscar">
                                    <button type="submit" class="btn btn-light" title="Buscar">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                                <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-light"
                                    title="Limpiar filtros">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </form>
                        </div>
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
                                            <td style="width: 280px">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role == 1)
                                                    <span class="text-primary fs-6 fw-bold">Admin</span>
                                                @elseif ($user->role == 2)
                                                    <span class="text-dark fs-6 fw-bold">Editor</span>
                                                @else
                                                    <span class="text-info fs-6 fw-bold">Visitante</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($user->active)
                                                    <span class="text-success fs-6 fw-bold">Activo</span>
                                                @else
                                                    <span class="text-danger fs-6 fw-bold">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td class="text-center d-flex gap-2 align-items-center justify-content-center">

                                                <a href="{{ route('usuarios.show', $user->id) }}"
                                                    class="btn bg-custom-btn-first btn-sm w-25"><i class="bi bi-eye"
                                                        title="Ver Detalle"></i></a>

                                                <a href="{{ route('usuarios.edit', $user->id) }}"
                                                    class="btn bg-custom-btn-second btn-sm w-25"><i class="bi bi-pencil"
                                                        title="Editar"></i></a>

                                                @if (Auth::id() !== $user->id)
                                                    @if ($user->active)
                                                        <button type="button"
                                                            class="btn bg-custom-btn-danger btn-sm btn-toggle-status w-25"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="inhabilitar" title="Inhabilitar">
                                                            <i class="bi bi-ban"></i>
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="btn bg-custom-btn-terciary btn-sm btn-toggle-status w-25"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="habilitar" title="Habilitar">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    @endif
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay usuarios registrados con los
                                                filtros aplicados</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $users->appends(request()->query())->links() }}
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
                <div class="modal-header bg-custom-gradient text-light">
                    <h5 class="modal-title" id="confirmToggleModalLabel">Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmToggleModalBody">
                    ¿Estás seguro de realizar esta acción?
                </div>
                <div class="modal-footer">
                    <!-- Botón NO -->
                    <button type="button" class="btn bg-custom-btn-off" data-bs-dismiss="modal">No</button>

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
                    const url = this.dataset.url;
                    const action = this.dataset.action;

                    if (action === 'inhabilitar') {
                        modalTitle.textContent = 'Confirmar Inhabilitación';
                        modalBody.textContent =
                            '¿Estás seguro de inhabilitar a este usuario? No podrá acceder al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Inhabilitar';
                        modalConfirmBtn.className = 'btn bg-custom-btn-on';
                    } else {
                        modalTitle.textContent = 'Confirmar Habilitación';
                        modalBody.textContent =
                            '¿Estás seguro de habilitar a este usuario? Volverá a tener acceso al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Habilitar';
                        modalConfirmBtn.className = 'btn bg-custom-btn-on';
                    }

                    modalForm.action = url;
                    bsModal.show();
                });
            });
        });
    </script>
@endpush
