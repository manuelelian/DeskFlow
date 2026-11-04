<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías - DeskFlow</title>
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
    <div class="container pb-5">
        
        <!-- Migas de pan / Volver -->
        <div class="mb-3">
            <a href="/DeskFlow/admin" class="text-decoration-none text-secondary small">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al Panel Maestro
            </a>
        </div>

        <!-- Encabezado y Botón Crear -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Gestión de Categorías</h2>
                <p class="text-muted small mb-0">Administra las áreas de soporte disponibles para los tickets</p>
            </div>
            <button type="button" class="btn btn-primary fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaCategoria">
                <i class="fa-solid fa-plus me-2"></i>Nueva Categoría
            </button>
        </div>

        <!-- Tarjeta con la Tabla de Categorías -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <?php if (!empty($categorias)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 8%;"># ID</th>
                                    <th style="width: 25%;">Nombre de la Categoría</th>
                                    <th style="width: 35%;">Descripción</th>
                                    <th style="width: 12%;">Estado</th>
                                    <th class="text-end" style="width: 20%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categorias as $c): ?>
                                    <?php $esActiva = ($c->estado === 'Activa'); ?>
                                    <tr class="<?= !$esActiva ? 'table-light opacity-75' : '' ?>">
                                        <td class="fw-bold text-secondary">#<?= htmlspecialchars($c->id) ?></td>
                                        <td class="fw-medium <?= $esActiva ? 'text-dark' : 'text-muted text-decoration-line-through' ?>">
                                            <i class="fa-solid fa-tag <?= $esActiva ? 'text-primary' : 'text-secondary' ?> opacity-50 me-2"></i><?= htmlspecialchars($c->nombre) ?>
                                        </td>
                                        <td class="text-muted small"><?= htmlspecialchars($c->descripcion) ?></td>
                                        
                                        <!-- Badge de Estado -->
                                        <td>
                                            <?php if ($esActiva): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">
                                                    <i class="fa-solid fa-circle-check me-1"></i>Activa
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                                                    <i class="fa-solid fa-ban me-1"></i>Inactiva
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="text-end">
                                            <!-- Botón Editar -->
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                                    onclick="cargarEditarCategoria(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->nombre)) ?>', '<?= htmlspecialchars(addslashes($c->descripcion)) ?>')">
                                                <i class="fa-solid fa-pen-to-square me-1"></i>Editar
                                            </button>

                                            <?php if ($esActiva): ?>
                                                <!-- Botón Desactivar -->
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmarDesactivarCategoria(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->nombre)) ?>')">
                                                    <i class="fa-solid fa-trash-can me-1"></i>Desactivar
                                                </button>
                                            <?php else: ?>
                                                <!-- Botón Reactivar -->
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="confirmarReactivarCategoria(<?= $c->id ?>, '<?= htmlspecialchars(addslashes($c->nombre)) ?>')">
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
                        <i class="fa-solid fa-tags text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-semibold">No se encontraron categorías</h5>
                        <p class="text-muted small mb-0">Aún no hay categorías registradas en el sistema.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- MODAL 1: NUEVA CATEGORÍA -->
    <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="/DeskFlow/admin/crearCategoria" method="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-plus text-primary me-2"></i>Crear Categoría
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre de la Categoría</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Soporte Técnico" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Descripción Breve</label>
                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Define qué tipo de problemas incluye esta categoría..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="fa-solid fa-check me-1"></i>Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: EDITAR CATEGORÍA -->
    <div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="/DeskFlow/admin/editarCategoria" method="POST">
                    <input type="hidden" name="id" id="edit_cat_id">

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">
                            <i class="fa-solid fa-pen text-primary me-2"></i>Modificar Categoría
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input type="text" name="nombre" id="edit_cat_nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Descripción</label>
                            <textarea name="descripcion" id="edit_cat_descripcion" class="form-control" rows="3"></textarea>
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

    <!-- MODAL 3: CONFIRMAR DESACTIVACIÓN -->
    <div class="modal fade" id="modalDesactivarCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-trash fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Desactivar categoría?</h5>
                    <p class="text-muted small mb-4">
                        La categoría <strong id="nombreCatDesactivar"></strong> pasará a estado <strong>Inactiva</strong> y no podrá elegirse en nuevos tickets.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarEliminarCat" class="btn btn-danger px-3 fw-semibold">Desactivar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: CONFIRMAR REACTIVACIÓN -->
    <div class="modal fade" id="modalReactivarCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-rotate-left fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Reactivar categoría?</h5>
                    <p class="text-muted small mb-4">
                        La categoría <strong id="nombreCatReactivar"></strong> volverá a estar <strong>Activa</strong> y disponible para los clientes.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarReactivarCat" class="btn btn-success px-3 fw-semibold">Reactivar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Poblar modal de edición
        function cargarEditarCategoria(id, nombre, descripcion) {
            document.getElementById('edit_cat_id').value = id;
            document.getElementById('edit_cat_nombre').value = nombre;
            document.getElementById('edit_cat_descripcion').value = descripcion;
            var modal = new bootstrap.Modal(document.getElementById('modalEditarCategoria'));
            modal.show();
        }

        // Poblar modal de desactivación
        function confirmarDesactivarCategoria(id, nombre) {
            document.getElementById('nombreCatDesactivar').innerText = nombre;
            document.getElementById('btnConfirmarEliminarCat').href = '/DeskFlow/admin/eliminarCategoria/' + id;
            var modal = new bootstrap.Modal(document.getElementById('modalDesactivarCategoria'));
            modal.show();
        }

        // Poblar modal de reactivación
        function confirmarReactivarCategoria(id, nombre) {
            document.getElementById('nombreCatReactivar').innerText = nombre;
            document.getElementById('btnConfirmarReactivarCat').href = '/DeskFlow/admin/reactivarCategoria/' + id;
            var modal = new bootstrap.Modal(document.getElementById('modalReactivarCategoria'));
            modal.show();
        }
    </script>
</body>
</html>