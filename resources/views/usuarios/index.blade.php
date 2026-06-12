@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-md-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-left me-2"></i>
                        Regresar</a>

                    <a class="btn btn-success btn-sm" href="{{ route('usuarios.create') }}">
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

                                                <a href="{{ route('usuarios.show', $user->id) }}"
                                                    class="btn btn-outline-primary btn-sm">Detalle</a>

                                                <a href="{{ route('usuarios.edit', $user->id) }}"
                                                    class="btn btn-outline-info btn-sm">Editar</a>

                                                @if (Auth::id() !== $user->id)
                                                    @if ($user->active)
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-sm btn-toggle-status"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="inhabilitar">
                                                            Inhabilitar
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="btn btn-outline-success btn-sm btn-toggle-status"
                                                            data-url="{{ route('usuarios.destroy', $user->id) }}"
                                                            data-action="habilitar">
                                                            Habilitar
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
                    const url = this.dataset.url;
                    const action = this.dataset.action;

                    if (action === 'inhabilitar') {
                        modalTitle.textContent = 'Confirmar Inhabilitación';
                        modalBody.textContent =
                            '¿Estás seguro de inhabilitar a este usuario? No podrá acceder al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Inhabilitar';
                        modalConfirmBtn.className = 'btn btn-danger';
                    } else {
                        modalTitle.textContent = 'Confirmar Habilitación';
                        modalBody.textContent =
                            '¿Estás seguro de habilitar a este usuario? Volverá a tener acceso al sistema.';
                        modalConfirmBtn.textContent = 'Sí, Habilitar';
                        modalConfirmBtn.className = 'btn btn-success';
                    }

                    modalForm.action = url;
                    bsModal.show();
                });
            });
        });
    </script>
@endpush
