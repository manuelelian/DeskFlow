<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Administrador | DeskFlow</title>
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
        .admin-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
    </style>
</head>
<body>

    <!-- Navbar Superior (Paleta unificada) -->
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
        
        <!-- Encabezado de la Sección -->
        <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
            <div>
                <h2 class="fw-bold text-dark mb-1">Panel de Control Maestro</h2>
                <p class="text-muted mb-0">Bienvenido. Desde aquí puedes gestionar los datos centrales y supervisar el sistema.</p>
            </div>
        </div>

        <!-- Tarjetas de Acceso a los 3 Módulos -->
        <div class="row g-4 justify-content-center">
            
            <!-- Módulo 1: Gestión de Usuarios -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 admin-card">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 mx-auto">
                            <i class="fa-solid fa-users fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Gestión de Usuarios</h4>
                        <p class="text-muted mb-4 flex-grow-1">Administra clientes y agentes. Da de alta nuevos miembros de soporte, cambia roles o desactiva cuentas.</p>
                        <a href="/DeskFlow/admin/usuarios" class="btn btn-primary w-100 fw-semibold py-2 rounded-3">
                            Administrar Usuarios <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo 2: Gestión de Categorías -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 admin-card">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 mx-auto">
                            <i class="fa-solid fa-tags fa-3x text-success"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Gestión de Categorías</h4>
                        <p class="text-muted mb-4 flex-grow-1">Crea, edita y organiza las categorías de incidencias disponibles para que los clientes clasifiquen sus tickets.</p>
                        <a href="/DeskFlow/admin/categorias" class="btn btn-success w-100 fw-semibold py-2 rounded-3">
                            Administrar Categorías <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo 3: Supervisión de Tickets -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 admin-card">
                    <div class="card-body text-center p-4 d-flex flex-column">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4 mx-auto">
                            <i class="fa-solid fa-clipboard-list fa-3x text-info"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-3">Supervisión Global</h4>
                        <p class="text-muted mb-4 flex-grow-1">Monitorea todos los tickets del sistema en tiempo real, revisa conversaciones y modera el contenido.</p>
                        <a href="/DeskFlow/admin/tickets" class="btn btn-info text-white w-100 fw-semibold py-2 rounded-3">
                            Supervisar Tickets <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>