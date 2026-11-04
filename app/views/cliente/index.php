<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Tickets - DeskFlow</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para íconos -->
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
    <div class="container">
        
        <!-- Encabezado y Botón de Nuevo Ticket -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Mis Solicitudes de Soporte</h2>
                <p class="text-muted small mb-0">Consultá el estado de tus reclamos o abrí un nuevo ticket</p>
            </div>
            <a href="/DeskFlow/cliente/crear" class="btn btn-primary fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-2"></i>Nuevo Ticket
            </a>
        </div>

        <!-- Tarjeta con la Tabla de Tickets -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <?php if (!empty($mis_tickets)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th># ID</th>
                                    <th>Asunto / Título</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mis_tickets as $ticket): ?>
                                    <tr>
                                        <td class="fw-bold text-secondary">#<?= htmlspecialchars($ticket->id) ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($ticket->titulo) ?></td>
                                        
                                        <!-- Etiqueta dinámica según la Prioridad -->
                                        <td>
                                            <?php
                                                $badgePrioridad = 'bg-secondary';
                                                if ($ticket->prioridad === 'Baja') $badgePrioridad = 'bg-info text-dark';
                                                elseif ($ticket->prioridad === 'Media') $badgePrioridad = 'bg-primary';
                                                elseif ($ticket->prioridad === 'Alta') $badgePrioridad = 'bg-warning text-dark';
                                                elseif ($ticket->prioridad === 'Critica') $badgePrioridad = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgePrioridad ?>"><?= htmlspecialchars($ticket->prioridad) ?></span>
                                        </td>

                                        <!-- Etiqueta dinámica según el Estado -->
                                        <td>
                                            <?php
                                                $badgeEstado = 'bg-secondary';
                                                if ($ticket->estado === 'Abierto') $badgeEstado = 'bg-success';
                                                elseif ($ticket->estado === 'En Proceso') $badgeEstado = 'bg-warning text-dark';
                                                elseif ($ticket->estado === 'Cerrado') $badgeEstado = 'bg-secondary';
                                            ?>
                                            <span class="badge rounded-pill <?= $badgeEstado ?>"><?= htmlspecialchars($ticket->estado) ?></span>
                                        </td>

                                        <td class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                                        </td>

                                        <!-- Botón para ir al Chat/Detalle del Ticket -->
                                        <td class="text-end">
                                            <a href="/DeskFlow/cliente/ver/<?= $ticket->id ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-regular fa-comments me-1"></i>Ver Hilo
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Estado vacío (cuando el cliente aún no creó ningún ticket) -->
                    <div class="text-center py-5">
                        <i class="fa-solid fa-ticket text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-semibold">Todavía no tenés tickets abiertos</h5>
                        <p class="text-muted small mb-3">Si tenés algún inconveniente o consulta, creá una solicitud y un agente te ayudará.</p>
                        <a href="/DeskFlow/cliente/crear" class="btn btn-outline-primary btn-sm">
                            Crear mi primer ticket
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>