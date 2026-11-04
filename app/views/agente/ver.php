<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ticket #<?= htmlspecialchars($ticket->id) ?> - DeskFlow</title>
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
            max-height: 450px;
            overflow-y: auto;
            background-color: #f1f5f9;
        }
        .message-bubble {
            max-width: 75%;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>

    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/DeskFlow/agente">
                <i class="fa-solid fa-headset me-2"></i>DeskFlow <span class="fw-light fs-6">| Portal Agente</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    <i class="fa-regular fa-user me-1"></i> Agente: <?= htmlspecialchars($_SESSION['USER']['name'] ?? 'Soporte') ?>
                </span>
                <a href="/DeskFlow/account/salir" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container pb-5">
        
        <!-- Botón Volver y Acciones de Cabecera -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="/DeskFlow/agente/personal" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i>Volver a mi Bandeja
            </a>

            <!-- Si el ticket NO está cerrado, el botón abre el Modal de Confirmación de Cierre -->
            <?php if ($ticket->estado !== 'Cerrado'): ?>
                <button type="button" class="btn btn-danger btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCerrarTicket">
                    <i class="fa-solid fa-check-circle me-1"></i>Resolver y Cerrar Ticket
                </button>
            <?php else: ?>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary px-3 py-2 fs-6">Ticket Cerrado (Solo Lectura)</span>
                    <!-- Botón para Reabrir Ticket Cerrado -->
                    <button type="button" class="btn btn-warning btn-sm fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalReabrirTicket">
                        <i class="fa-solid fa-rotate-left me-1"></i>Reabrir Ticket
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Alertas de Error o Éxito -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['cerrado']) && $_GET['cerrado'] === 'exito'): ?>
            <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>El ticket ha sido cerrado con éxito.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['cambio']) && $_GET['cambio'] === 'prioridad'): ?>
            <div class="alert alert-info shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-flag me-2"></i>La prioridad del ticket fue actualizada correctamente.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['cambio']) && $_GET['cambio'] === 'reabierto'): ?>
            <div class="alert alert-warning shadow-sm border-0 rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-rotate-left me-2"></i>El ticket ha sido reabierto y vuelve a estar <strong>En Proceso</strong>.
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Columna Izquierda: Detalles del Ticket y Gestión Operativa -->
            <div class="col-lg-4">
                <!-- Tarjeta 1: Información del Caso -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-3">Información del Caso</h5>
                        
                        <div class="mb-3">
                            <span class="text-muted small d-block">Asunto / Título</span>
                            <span class="fw-bold text-primary fs-5"><?= htmlspecialchars($ticket->titulo) ?></span>
                        </div>

                        <div class="mb-3">
                            <span class="text-muted small d-block">Descripción Inicial del Cliente</span>
                            <p class="bg-light p-3 rounded-3 small text-secondary mb-0"><?= nl2br(htmlspecialchars($ticket->descripcion)) ?></p>
                        </div>

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Estado Actual:</span>
                            <?php if ($ticket->estado === 'Cerrado'): ?>
                                <span class="badge bg-secondary">Cerrado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($ticket->estado) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Prioridad:</span>
                            <?php
                                $badgePrioridad = 'bg-secondary';
                                if ($ticket->prioridad === 'Baja') $badgePrioridad = 'bg-info text-dark';
                                elseif ($ticket->prioridad === 'Media') $badgePrioridad = 'bg-primary';
                                elseif ($ticket->prioridad === 'Alta') $badgePrioridad = 'bg-warning text-dark';
                                elseif ($ticket->prioridad === 'Critica') $badgePrioridad = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgePrioridad ?>"><?= htmlspecialchars($ticket->prioridad) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Fecha Creación:</span>
                            <span class="small text-secondary"><?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta 2: Gestión Operativa (Prioridad y Reasignación - Solo si no está cerrado) -->
                <?php if ($ticket->estado !== 'Cerrado'): ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-sliders me-2 text-primary"></i>Gestión Operativa</h6>
                            
                            <!-- Formulario 1: Cambiar Prioridad -->
                            <form action="/DeskFlow/agente/cambiarPrioridad" method="POST" class="mb-4">
                                <input type="hidden" name="id_ticket" value="<?= $ticket->id ?>">
                                <label class="form-label small fw-semibold text-secondary mb-1">Cambiar Prioridad</label>
                                <div class="input-group input-group-sm">
                                    <select name="prioridad" class="form-select" required>
                                        <option value="Baja" <?= $ticket->prioridad === 'Baja' ? 'selected' : '' ?>>Baja</option>
                                        <option value="Media" <?= $ticket->prioridad === 'Media' ? 'selected' : '' ?>>Media</option>
                                        <option value="Alta" <?= $ticket->prioridad === 'Alta' ? 'selected' : '' ?>>Alta</option>
                                        <option value="Critica" <?= $ticket->prioridad === 'Critica' ? 'selected' : '' ?>>Crítica</option>
                                    </select>
                                    <button type="submit" class="btn btn-outline-primary fw-semibold">Actualizar</button>
                                </div>
                            </form>

                            <!-- Formulario 2: Reasignar / Derivar a otro Agente -->
                            <form action="/DeskFlow/agente/reasignarTicket" method="POST">
                                <input type="hidden" name="id_ticket" value="<?= $ticket->id ?>">
                                <label class="form-label small fw-semibold text-secondary mb-1">Derivar a otro Agente</label>
                                <div class="input-group input-group-sm">
                                    <select name="id_agente" class="form-select" required>
                                        <option value="" disabled selected>Seleccionar compañero...</option>
                                        <?php if (!empty($agentes)): ?>
                                            <?php foreach ($agentes as $ag): ?>
                                                <?php if ($ag->id != $_SESSION['USER']['id_usuario']): ?>
                                                    <option value="<?= $ag->id ?>">
                                                        <?= htmlspecialchars($ag->nombre . ' ' . $ag->apellido) ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button type="submit" class="btn btn-outline-secondary fw-semibold">Derivar</button>
                                </div>
                                <div class="form-text" style="font-size: 0.72rem;">Al derivarlo, el ticket pasará a la bandeja de tu compañero.</div>
                            </form>

                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Columna Derecha: Hilo de Conversación (Chat) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 px-4 border-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-regular fa-comments me-2 text-primary"></i>Historial de Mensajes</h5>
                    </div>
                    
                    <!-- Contenedor del Chat -->
                    <div class="card-body chat-box p-4 rounded-3 m-3">
                        <?php if (!empty($mensajes)): ?>
                            <?php foreach ($mensajes as $msj): ?>
                                <?php 
                                    $esMio = ($msj->id_usuario == $_SESSION['USER']['id_usuario']);
                                ?>
                                <div class="d-flex flex-column mb-3 <?= $esMio ? 'align-items-end' : 'align-items-start' ?>">
                                    <div class="small text-muted mb-1">
                                        <?= $esMio ? 'Vos (Soporte)' : 'Cliente' ?> • <span style="font-size: 0.75rem;"><?= date('d/m/Y H:i', strtotime($msj->fecha_envio)) ?></span>
                                    </div>
                                    <div class="message-bubble <?= $esMio ? 'bg-primary text-white' : 'bg-white border text-dark shadow-sm' ?>">
                                        <?= nl2br(htmlspecialchars($msj->contenido)) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted small">
                                No hay mensajes en este hilo todavía. Escribí tu primera respuesta abajo.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Formulario de Respuesta (Solo si el ticket NO está cerrado - RN03) -->
                    <div class="card-footer bg-white p-4 border-0">
                        <?php if ($ticket->estado !== 'Cerrado'): ?>
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="contenido" class="form-label small fw-bold text-secondary">Escribir respuesta al cliente:</label>
                                    <textarea class="form-control" name="contenido" rows="3" placeholder="Escribí tu mensaje aquí..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm">
                                        <i class="fa-solid fa-paper-plane me-1"></i>Enviar Respuesta
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-secondary text-center small mb-0 py-3" role="alert">
                                <i class="fa-solid fa-lock me-2"></i>Este ticket se encuentra cerrado. Si necesitás retomar el caso, usá el botón <strong>Reabrir Ticket</strong> arriba a la derecha.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL 1: CONFIRMACIÓN PARA CERRAR EL TICKET -->
    <div class="modal fade" id="modalCerrarTicket" tabindex="-1" aria-labelledby="modalCerrarTicketLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-warning">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" id="modalCerrarTicketLabel">¿Estás seguro de que querés cerrar el Ticket?</h4>
                    <p class="text-secondary small mb-4">
                        Una vez cerrado, el ticket pasará a ser de <strong>solo lectura</strong> (Regla RN03).
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancelar</button>
                        <a href="/DeskFlow/agente/cerrar/<?= $ticket->id ?>" class="btn btn-danger btn-sm px-4 fw-semibold">
                            Sí, cerrar ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: CONFIRMACIÓN PARA REABRIR EL TICKET -->
    <div class="modal fade" id="modalReabrirTicket" tabindex="-1" aria-labelledby="modalReabrirTicketLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="/DeskFlow/agente/reabrirTicket" method="POST">
                    <input type="hidden" name="id_ticket" value="<?= $ticket->id ?>">
                    <div class="modal-body text-center p-4">
                        <div class="mb-3 text-warning">
                            <i class="fa-solid fa-rotate-left" style="font-size: 3.5rem;"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2" id="modalReabrirTicketLabel">¿Reabrir este Ticket?</h4>
                        <p class="text-secondary small mb-4">
                            El ticket volverá al estado <strong>En Proceso</strong> y se habilitará nuevamente el envío de mensajes en el chat.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning btn-sm px-4 fw-semibold">
                                Sí, reabrir ticket
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>