<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Ticket #<?= htmlspecialchars($ticket->id) ?> - DeskFlow</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-light">

    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/DeskFlow/cliente">
                <i class="fa-solid fa-headset me-2"></i>DeskFlow <span class="fw-light fs-6">| Portal Cliente</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    <i class="fa-regular fa-user me-1"></i> Hola, <?= htmlspecialchars($_SESSION['USER']['name'] ?? 'Cliente') ?>
                </span>
                <a href="/DeskFlow/account/salir" class="btn btn-outline-light btn-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">

                <!-- Botón Volver y Estado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="/DeskFlow/cliente" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Volver a mis solicitudes
                    </a>
                    <div>
                        <?php
                            $badgeEstado = 'bg-secondary';
                            if ($ticket->estado === 'Abierto') $badgeEstado = 'bg-success';
                            elseif ($ticket->estado === 'En Proceso') $badgeEstado = 'bg-warning text-dark';
                            elseif ($ticket->estado === 'Cerrado') $badgeEstado = 'bg-secondary';
                        ?>
                        <span class="badge <?= $badgeEstado ?> px-3 py-2 fs-6 rounded-pill">Estado: <?= htmlspecialchars($ticket->estado) ?></span>
                        
                        <?php if ($ticket->estado !== 'Cerrado'): ?>
                        <button type="button" class="badge bg-success border-0 px-3 py-2 fs-6 rounded-pill" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalResolverTicket">
                        <i class="fa-solid fa-check-circle me-1"></i> Marcar como Resuelto
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tarjeta de Información del Ticket -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h3 class="fw-bold text-dark mb-0">#<?= htmlspecialchars($ticket->id) ?> - <?= htmlspecialchars($ticket->titulo) ?></h3>
                            <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i> <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?></span>
                        </div>
                        <p class="text-secondary mb-3"><?= nl2br(htmlspecialchars($ticket->descripcion)) ?></p>
                        
                        <div class="d-flex gap-3 text-muted small border-top pt-3">
                            <span><i class="fa-solid fa-tag me-1 text-primary"></i> Categoría ID: <?= htmlspecialchars($ticket->id_categoria) ?></span>
                            <span><i class="fa-solid fa-flag me-1 text-primary"></i> Prioridad: <strong><?= htmlspecialchars($ticket->prioridad) ?></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Sección del Chat / Hilo de Mensajes -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0"><i class="fa-regular fa-comments me-2 text-primary"></i>Historial de Conversación</h5>
                    </div>
                    <div class="card-body px-4 py-3">

                        <!-- Mensajes de Error en el chat -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-3" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                <div class="small fw-medium"><?= htmlspecialchars($error) ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Listado de Mensajes -->
                        <div class="chat-thread my-3">
                            <?php if (!empty($mensajes)): ?>
                                <?php foreach ($mensajes as $msg): ?>
                                    <?php 
                                        // Determinamos si el mensaje fue enviado por el usuario actual o por soporte/agente
                                        $esMio = ($msg->id_usuario == $_SESSION['USER']['id_usuario']);
                                    ?>
                                    <div class="d-flex mb-3 <?= $esMio ? 'justify-content-end' : 'justify-content-start' ?>">
                                        <div class="card border-0 shadow-sm p-3 rounded-4 <?= $esMio ? 'bg-primary text-white' : 'bg-white text-dark' ?>" style="max-width: 75%;">
                                            <div class="d-flex justify-content-between align-items-center mb-1 small <?= $esMio ? 'text-white-50' : 'text-muted' ?>" style="font-size: 0.75rem;">
                                                <span class="fw-bold me-3"><?= $esMio ? 'Vos' : 'Soporte / Agente' ?></span>
                                                <span><?= date('d/m/Y H:i', strtotime($msg->fecha_envio)) ?></span>
                                            </div>
                                            <p class="mb-0 text-break"><?= nl2br(htmlspecialchars($msg->contenido)) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted small">
                                    No hay mensajes en este hilo todavía. Escribí tu consulta abajo.
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

                <!-- Caja de Envío de Mensajes (Bloqueada si el ticket está Cerrado - RN03) -->
                <?php if ($ticket->estado === 'Cerrado'): ?>
                    <div class="alert alert-secondary text-center rounded-4 py-3 shadow-sm border-0" role="alert">
                        <i class="fa-solid fa-lock me-2"></i>Este ticket se encuentra <strong>Cerrado</strong>. Ya no admite nuevos mensajes (RN03).
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <form action="/DeskFlow/cliente/ver/<?= $ticket->id ?>" method="POST">
                                <div class="mb-3">
                                    <label for="contenido" class="form-label small fw-bold text-secondary">RESPONDER AL TICKET</label>
                                    <textarea class="form-control bg-light" id="contenido" name="contenido" rows="3" 
                                              placeholder="Escribí tu mensaje o aclaración aquí..." required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary fw-semibold px-4">
                                        <i class="fa-solid fa-paper-plane me-2"></i>Enviar Mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Modal para Resolver Ticket -->
    <div class="modal fade" id="modalResolverTicket" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/DeskFlow/cliente/resolverTicket" method="POST">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Dar por Resuelto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas cerrar este ticket? Por favor, dejanos un mensaje final explicando el motivo.</p>
                        <input type="hidden" name="id_ticket" value="<?= $ticket->id ?>">

                        <div class="mb-3">
                            <label for="mensaje_resolucion" class="form-label">Motivo de la resolución <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="mensaje_resolucion" name="mensaje_resolucion" rows="3" required placeholder="Ej: Pude solucionarlo reiniciando el equipo, muchas gracias."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Cerrar Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>