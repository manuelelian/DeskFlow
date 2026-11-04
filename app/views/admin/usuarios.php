<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - DeskFlow</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body>

    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/DeskFlow/admin">
                <i class="fa-solid fa-headset me-2"></i>DeskFlow <span class="fw-light fs-6">| Portal Administrador</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    <i class="fa-solid fa-crown me-1 text-warning"></i> Admin: <?= htmlspecialchars($_SESSION['USER']['name'] ?? 'Administrador') ?>
                </span>
                <a href="/DeskFlow/account/salir" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        
        <!-- Migas de pan / Volver -->
        <div class="mb-3">
            <a href="/DeskFlow/admin" class="text-decoration-none text-secondary small">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Panel Maestro
            </a>
        </div>

        <!-- Encabezado y Botón Crear Agente -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Gestión de Usuarios</h2>
                <p class="text-muted small mb-0">Listado, edición y administración de clientes y agentes de soporte</p>
            </div>
            <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoAgente">
                <i class="fa-solid fa-user-plus me-2"></i>Nuevo Agente
            </button>
        </div>

        <!-- Tarjeta con la Tabla de Usuarios -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <?php if (!empty($usuarios)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th># ID</th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha Registro</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td class="fw-bold text-secondary">#<?= htmlspecialchars($u->id) ?></td>
                                        <td class="fw-medium text-dark">
                                            <?= htmlspecialchars($u->nombre . ' ' . $u->apellido) ?>
                                        </td>
                                        <td class="text-muted"><?= htmlspecialchars($u->email) ?></td>
                                        
                                        <!-- Rol Badge -->
                                        <td>
                                            <?php if ($u->id_rol == 1): ?>
                                                <span class="badge bg-secondary">Cliente</span>
                                            <?php elseif ($u->id_rol == 2): ?>
                                                <span class="badge bg-info text-dark">Agente</span>
                                            <?php elseif ($u->id_rol == 3): ?>
                                                <span class="badge bg-primary">Administrador</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark">Otro</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-muted small">
                                            <?= !empty($u->fecha_registro) ? date('d/m/Y H:i', strtotime($u->fecha_registro)) : '-' ?>
                                        </td>
                                        <!-- Acciones -->
                                        <td class="text-end">
                                            <!-- Botón Editar -->
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                    onclick="cargarEditarUsuario(<?= $u->id ?>, '<?= htmlspecialchars(addslashes($u->nombre)) ?>', '<?= htmlspecialchars(addslashes($u->apellido)) ?>', '<?= htmlspecialchars(addslashes($u->email)) ?>', <?= $u->id_rol ?>)">
                                                <i class="fa-solid fa-pen-to-square me-1"></i>Editar
                                            </button>

                                            <?php 
                                                // Verificamos si el usuario está activo (si no tiene fecha de eliminación)
                                                $esActivo = empty($u->fecha_eliminacion); 
                                            ?>

                                            <?php if ($esActivo): ?>
                                                <?php if ($u->id != $_SESSION['USER']['id_usuario']): ?>
                                                    <!-- Botón Desactivar (Borrado Lógico) -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmarDesactivar(<?= $u->id ?>, '<?= htmlspecialchars(addslashes($u->nombre . ' ' . $u->apellido)) ?>')">
                                                        <i class="fa-solid fa-user-xmark me-1"></i>Desactivar
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border">Tu usuario</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- Botón Reactivar -->
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="confirmarReactivarUsuario(<?= $u->id ?>, '<?= htmlspecialchars(addslashes($u->nombre . ' ' . $u->apellido)) ?>')">
                                                    <i class="fa-solid fa-rotate-left me-1"></i>Reactivar
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-users text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-semibold">No se encontraron usuarios</h5>
                        <p class="text-muted small mb-0">No hay usuarios registrados o activos en este momento.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- MODAL 1: CREAR NUEVO AGENTE -->
    <div class="modal fade" id="modalNuevoAgente" tabindex="-1" aria-labelledby="modalNuevoAgenteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="/DeskFlow/admin/crearAgente" method="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalNuevoAgenteLabel">
                            <i class="fa-solid fa-user-plus text-primary me-2"></i>Dar de Alta Agente
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Lucas" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Apellido</label>
                                <input type="text" name="apellido" class="form-control" placeholder="Ej: Gómez" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" placeholder="agente@deskflow.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Contraseña Inicial</label>
                                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                                <div class="form-text small">Debe incluir mayúscula, número y símbolo especial.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="fa-solid fa-check me-1"></i>Crear Agente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDITAR USUARIO (ROL Y DATOS) -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="/DeskFlow/admin/editarUsuario" method="POST">
                    <!-- ID oculto del usuario a editar -->
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="modalEditarUsuarioLabel">
                            <i class="fa-solid fa-user-pen text-primary me-2"></i>Modificar Usuario
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre</label>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Apellido</label>
                                <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Correo Electrónico</label>
                                <input type="email" name="email" id="edit_email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Rol del Sistema</label>
                                <select name="id_rol" id="edit_rol" class="form-select" required>
                                    <option value="1">Cliente (Rol 1)</option>
                                    <option value="2">Agente de Soporte (Rol 2)</option>
                                    <option value="3">Administrador (Rol 3)</option>
                                </select>
                                <div class="form-text small">Cambiar el rol actualizará inmediatamente los permisos de acceso del usuario.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="fa-solid fa-floppy-disk me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: CONFIRMACIÓN DE DESACTIVACIÓN (BORRADO LÓGICO) -->
    <div class="modal fade" id="modalDesactivar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Desactivar usuario?</h5>
                    <p class="text-muted small mb-4">
                        El usuario <strong id="nombreUsuarioDesactivar"></strong> será dado de baja lógica del sistema.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarEliminar" class="btn btn-danger px-3 fw-semibold">Desactivar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: CONFIRMAR REACTIVACIÓN -->
    <div class="modal fade" id="modalReactivarUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-rotate-left fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Reactivar usuario?</h5>
                    <p class="text-muted small mb-4">
                        El usuario <strong id="nombreUsuarioReactivar"></strong> volverá a estar <strong>Activo</strong> y disponible.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarReactivarUsuario" class="btn btn-success px-3 fw-semibold">Reactivar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Carga los datos en el modal de edición
        function cargarEditarUsuario(id, nombre, apellido, email, rol) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_apellido').value = apellido;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_rol').value = rol;

            var modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
            modal.show();
        }

        // Carga los datos en el modal de confirmación de baja
        function confirmarDesactivar(id, nombre) {
            document.getElementById('nombreUsuarioDesactivar').innerText = nombre;
            document.getElementById('btnConfirmarEliminar').href = '/DeskFlow/admin/eliminarUsuario/' + id;
            var modal = new bootstrap.Modal(document.getElementById('modalDesactivar'));
            modal.show();
        }


        // Poblar modal de reactivación
        function confirmarReactivarUsuario(id, nombre) {
            document.getElementById('nombreUsuarioReactivar').innerText = nombre;
            document.getElementById('btnConfirmarReactivarUsuario').href = '/DeskFlow/admin/reactivarUsuario/' + id;
            var modal = new bootstrap.Modal(document.getElementById('modalReactivarUsuario'));
            modal.show();
        }
    </script>
</body>
</html>