<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bandeja Personal - DeskFlow</title>
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
            <a class="navbar-brand fw-bold" href="/DeskFlow/agente">
                <i class=""></i>DeskFlow <span class="fw-light fs-6">| Portal Agente</span>
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
    <div class="container">
        
        <!-- Pestañas de Navegación del Agente -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link text-secondary" href="/DeskFlow/agente/global">
                    <i class="fa-solid fa-globe me-2"></i>Bandeja Global (Sin Asignar)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active fw-bold" href="/DeskFlow/agente/personal">
                    <i class="fa-solid fa-user-check me-2"></i>Bandeja Personal
                </a>
            </li>
        </ul>

        <!-- Encabezado de la Sección -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Mis Tickets Asignados</h2>
                <p class="text-muted small mb-0">Listado de casos bajo tu responsabilidad en estado "En Proceso"</p>
            </div>
        </div>

        <!-- Tarjeta con la Tabla de Tickets Personales -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <?php if (!empty($mis_tickets)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th># ID</th>
                                    <th>Cliente ID</th>
                                    <th>Título / Asunto</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mis_tickets as $ticket): ?>
                                    <tr>
                                        <td class="fw-bold text-secondary">#<?= htmlspecialchars($ticket->id) ?></td>
                                        <td><span class="badge bg-light text-dark border">Cliente #<?= htmlspecialchars($ticket->id_cliente) ?></span></td>
                                        <td class="fw-medium"><?= htmlspecialchars($ticket->titulo) ?></td>
                                        
                                        <!-- Prioridad -->
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

                                        <!-- Estado -->
                                        <td>
                                            <span class="badge bg-warning text-dark"><?= htmlspecialchars($ticket->estado) ?></span>
                                        </td>

                                        <td class="text-muted small">
                                            <?= date('d/m/Y H:i', strtotime($ticket->fecha_creacion)) ?>
                                        </td>

                                        <!-- Botón para gestionar / entrar al chat del ticket -->
                                        <td class="text-end">
                                            <a href="/DeskFlow/agente/ver/<?= $ticket->id ?>" class="btn btn-sm btn-outline-primary fw-semibold shadow-sm">
                                                <i class="fa-regular fa-comments me-1"></i>Gestionar / Chat
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- Estado vacío -->
                    <div class="text-center py-5">
                        <i class="fa-solid fa-folder-open text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h5 class="text-secondary fw-semibold">No tenés tickets asignados actualmente</h5>
                        <p class="text-muted small mb-3">Dirigite a la Bandeja Global para tomar un caso pendiente y comenzar a trabajar.</p>
                        <a href="/DeskFlow/agente/global" class="btn btn-outline-primary btn-sm">
                            Ir a la Bandeja Global
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