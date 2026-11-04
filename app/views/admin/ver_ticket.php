<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisión Ticket #<?= htmlspecialchars($ticket->id) ?> - DeskFlow</title>
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
        .chat-box {
            max-height: 500px;
            overflow-y: auto;
            background-color: #f1f5f9;
        }
        .message-card {
            border-radius: 0.75rem;
            transition: background-color 0.2s;
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
        
        <!-- Cabecera y Botón Volver -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="/DeskFlow/admin/tickets" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>Volver a Supervisión Global
            </a>
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2">
                <i class="fa-solid fa-shield-halved me-1"></i>Modo Supervisión y Moderación
            </span>
        </div>

        <!-- Alerta de éxito al moderar un mensaje -->
        <?php if (isset($_GET['moderado']) && $_GET['moderado'] === 'exito'): ?>
            <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4 d-flex align-items-center" role="alert">
                <i class="fa-solid fa-circle-check me-2 fs-5"></i>
                <div>El mensaje seleccionado fue eliminado lógicamente del hilo de conversación.</div>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Columna Izquierda: Detalles del Ticket -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Ticket #<?= htmlspecialchars($ticket->id) ?></h5>
                            <?php if ($ticket->estado === 'Abierto'): ?>
                                <span class="badge bg-success">Abierto</span>
                            <?php elseif ($ticket->estado === 'En Proceso'): ?>
                                <span class="badge bg-primary">En Proceso</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Cerrado</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <span class="text-muted small d-block">Asunto / Título</span>
                            <span class="fw-bold text-primary fs-5"><?= htmlspecialchars($ticket->titulo) ?></span>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted small d-block mb-1">Descripción Inicial</span>
                            <p class="bg-light p-3 rounded-3 small text-secondary mb-0"><?= nl2br(htmlspecialchars($ticket->descripcion)) ?></p>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Prioridad:</span>
                            <?php
                                $badgePrioridad = 'bg-secondary';
                                if ($ticket->prioridad === 'Baja') $badgePrioridad = 'bg-success';
                                elseif ($ticket->prioridad === 'Media') $badgePrioridad = 'bg-info text-dark';
                                elseif ($ticket->prioridad === 'Alta') $badgePrioridad = 'bg-warning text-dark';
                                elseif ($ticket->prioridad === 'Critica') $badgePrioridad = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgePrioridad ?>"><?= htmlspecialchars($ticket->prioridad) ?></span>
                        </div>

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">ID Cliente:</span>
                            <span class="small fw-semibold text-dark">#<?= htmlspecialchars($ticket->id_cliente) ?></span>
                        </div>

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">ID Agente Asignado:</span>
                            <span class="small fw-semibold text-dark">
                                <?= $ticket->id_agente ? '#' . htmlspecialchars($ticket->id_agente) : '<span class="text-warning fst-italic">Sin asignar</span>' ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Fecha Creación:</span>
                            <span class="small text-secondary"><?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Hilo de Conversación y Moderación -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-regular fa-comments me-2 text-primary"></i>Auditoría de Mensajes del Hilo
                        </h5>
                        <span class="text-muted small"><?= is_array($mensajes) ? count($mensajes) : 0 ?> mensaje(s) activos</span>
                    </div>
                    
                    <!-- Contenedor de Mensajes -->
                    <div class="card-body chat-box p-4 rounded-3 m-3">
                        <?php if (!empty($mensajes)): ?>
                            <?php foreach ($mensajes as $msj): ?>
                                <?php 
                                    $esCliente = ($msj->id_rol == 1);
                                ?>
                                <div class="card border-0 shadow-sm mb-3 message-card <?= $esCliente ? 'border-start border-4 border-primary' : 'border-start border-4 border-info' ?>">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="fw-bold text-dark small">
                                                    <?= htmlspecialchars(($msj->nombre ?? 'Usuario') . ' ' . ($msj->apellido ?? '')) ?>
                                                </span>
                                                <?php if ($msj->id_rol == 1): ?>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary ms-1" style="font-size: 0.7rem;">Cliente</span>
                                                <?php elseif ($msj->id_rol == 2): ?>
                                                    <span class="badge bg-info bg-opacity-10 text-info border border-info ms-1" style="font-size: 0.7rem;">Agente Soporte</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary ms-1" style="font-size: 0.7rem;">Admin</span>
                                                <?php endif; ?>
                                                <span class="text-muted ms-2" style="font-size: 0.75rem;">
                                                    <i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($msj->fecha_envio)) ?>
                                                </span>
                                            </div>

                                            <!-- Botón Moderar / Borrar Mensaje -->
                                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" 
                                                    onclick="confirmarEliminarMensaje(<?= $msj->id ?>)" title="Borrar mensaje por moderación">
                                                <i class="fa-solid fa-trash-can me-1"></i><span class="small">Moderar</span>
                                            </button>
                                        </div>

                                        <div class="text-secondary small">
                                            <?= nl2br(htmlspecialchars($msj->contenido)) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-regular fa-comment-dots fa-2x mb-2 opacity-50"></i>
                                <p class="small mb-0">No hay mensajes activos registrados en este ticket.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL CONFIRMACIÓN DE MODERACIÓN DE MENSAJE -->
    <div class="modal fade" id="modalEliminarMensaje" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow rounded-4 text-center p-3">
                <div class="modal-body">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-3 mb-3">
                        <i class="fa-solid fa-comment-slash fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">¿Eliminar mensaje?</h5>
                    <p class="text-muted small mb-4">
                        Este mensaje dejará de ser visible en el chat para el cliente y el agente (Borrado Lógico de Moderación).
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancelar</button>
                        <a href="#" id="btnConfirmarEliminarMsj" class="btn btn-danger px-3 fw-semibold">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarEliminarMensaje(idMensaje) {
            document.getElementById('btnConfirmarEliminarMsj').href = '/DeskFlow/admin/eliminarMensaje/' + idMensaje;
            var modal = new bootstrap.Modal(document.getElementById('modalEliminarMensaje'));
            modal.show();
        }
    </script>
</body>
</html>