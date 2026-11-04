<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisión Global - DeskFlow</title>
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

        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Supervisión Global de Tickets</h2>
                <p class="text-muted small mb-0">Visión general de todas las incidencias registradas en el sistema.</p>
            </div>
        </div>

        <!-- Tarjeta con la Tabla de Tickets -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <?php if (!empty($tickets)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th># Ticket</th>
                                    <th>Cliente</th>
                                    <th>Categoría</th>
                                    <th>Asunto</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Agente Asignado</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $t): ?>
                                    <tr>
                                        <!-- ID del Ticket -->
                                        <td class="fw-bold text-secondary">#<?= htmlspecialchars($t->id) ?></td>
                                        
                                        <!-- Cliente -->
                                        <td class="text-muted small">
                                            <i class="fa-solid fa-user me-1 text-primary opacity-75"></i>
                                            <?= htmlspecialchars($t->cliente_nombre . ' ' . $t->cliente_apellido) ?>
                                        </td>
                                        
                                        <!-- Categoría -->
                                        <td class="text-muted small">
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($t->categoria_nombre) ?>
                                            </span>
                                        </td>
                                        
                                        <!-- Asunto -->
                                        <td class="fw-medium text-dark text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($t->titulo) ?>">
                                            <?= htmlspecialchars($t->titulo) ?>
                                        </td>
                                        
                                        <!-- Prioridad Badge -->
                                        <td>
                                            <?php if ($t->prioridad == 'Critica'): ?>
                                                <span class="badge bg-danger">Crítica</span>
                                            <?php elseif ($t->prioridad == 'Alta'): ?>
                                                <span class="badge bg-warning text-dark">Alta</span>
                                            <?php elseif ($t->prioridad == 'Media'): ?>
                                                <span class="badge bg-info text-dark">Media</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Baja</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Estado Badge -->
                                        <td>
                                            <?php if ($t->estado == 'Abierto'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success">Abierto</span>
                                            <?php elseif ($t->estado == 'En Proceso'): ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">En Proceso</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary">Cerrado</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Agente Asignado -->
                                        <td class="text-muted small">
                                            <?php if ($t->id_agente): ?>
                                                <i class="fa-solid fa-headset me-1 text-info"></i>
                                                <?= htmlspecialchars($t->agente_nombre . ' ' . $t->agente_apellido) ?>
                                            <?php else: ?>
                                                <span class="fst-italic text-warning"><i class="fa-solid fa-clock me-1"></i>Sin asignar</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="text-end">
                                            <!-- Botón Ver Detalle y Moderar Mensajes -->
                                            <a href="/DeskFlow/admin/verTicket/<?= $t->id ?>" class="btn btn-sm btn-outline-primary me-1" title="Ver Detalle y Moderar Chat">
                                                <i class="fa-solid fa-eye me-1"></i>Ver / Moderar
                                            </a>

                                            <!-- Botón Borrado Lógico del Ticket -->
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmarDesactivarTicket(<?= $t->id ?>)" title="Eliminar / Borrado lógico">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-inbox text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-semibold">No hay tickets registrados</h5>
                        <p class="text-muted small mb-0">El sistema de mesa de ayuda aún no ha recibido solicitudes.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- MODAL CONFIRMACIÓN DE ELIMINAR TICKET -->
    <div class="modal fade" id="modalDesactivarTicket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-triangle-exclamation fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar ticket?</h5>
                    <p class="text-muted small mb-4">
                        El ticket <strong id="idTicketDesactivar"></strong> se eliminará mediante borrado lógico, manteniendo la auditoría.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarEliminarTicket" class="btn btn-danger px-3 fw-semibold">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarDesactivarTicket(id) {
            document.getElementById('idTicketDesactivar').innerText = '#' + id;
            document.getElementById('btnConfirmarEliminarTicket').href = '/DeskFlow/admin/eliminarTicket/' + id;
            var modal = new bootstrap.Modal(document.getElementById('modalDesactivarTicket'));
            modal.show();
        }
    </script>
</body>
</html>