<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Ticket - DeskFlow</title>
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
            <div class="col-lg-8">

                <!-- Encabezado y botón volver -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Abrir Nuevo Ticket</h2>
                        <p class="text-muted small mb-0">Completá los detalles de tu incidente para que un agente pueda ayudarte</p>
                    </div>
                    <a href="/DeskFlow/cliente" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Volver a mis tickets
                    </a>
                </div>

                <!-- Tarjeta del Formulario -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">

                        <!-- Mensaje de Error (si falla la validación) -->
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-4" role="alert">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                <div class="small fw-medium"><?= htmlspecialchars($error) ?></div>
                            </div>
                        <?php endif; ?>

                        <form action="/DeskFlow/cliente/crear" method="POST">
                            
                            <!-- Asunto / Título -->
                            <div class="mb-3">
                                <label for="titulo" class="form-label small fw-bold text-secondary">ASUNTO / TÍTULO DEL PROBLEMA *</label>
                                <input type="text" class="form-control bg-light" id="titulo" name="titulo" 
                                       placeholder="Ej: No puedo acceder al sistema de facturación" 
                                       maxlength="150" value="<?= htmlspecialchars($datos['titulo'] ?? '') ?>" required autofocus>
                            </div>

                            <div class="row">
                                <!-- Categoría (Dinámica desde BD) -->
                                <div class="col-md-6 mb-3">
                                    <label for="id_categoria" class="form-label small fw-bold text-secondary">CATEGORÍA *</label>
                                    <select class="form-select bg-light" id="id_categoria" name="id_categoria" required>
                                        <option value="" disabled <?= empty($datos['id_categoria']) ? 'selected' : '' ?>>Seleccioná una categoría...</option>
                                        <?php if (!empty($categorias)): ?>
                                            <?php foreach ($categorias as $cat): ?>
                                                <option value="<?= $cat->id ?>" <?= (isset($datos['id_categoria']) && $datos['id_categoria'] == $cat->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat->nombre) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <!-- Prioridad (Según el ENUM de la tabla tickets) -->
                                <div class="col-md-6 mb-3">
                                    <label for="prioridad" class="form-label small fw-bold text-secondary">PRIORIDAD *</label>
                                    <select class="form-select bg-light" id="prioridad" name="prioridad" required>
                                        <option value="" disabled <?= empty($datos['prioridad']) ? 'selected' : '' ?>>Seleccioná la urgencia...</option>
                                        <option value="Baja" <?= (isset($datos['prioridad']) && $datos['prioridad'] === 'Baja') ? 'selected' : '' ?>>Baja (Consulta general)</option>
                                        <option value="Media" <?= (isset($datos['prioridad']) && $datos['prioridad'] === 'Media') ? 'selected' : '' ?>>Media (Inconveniente menor)</option>
                                        <option value="Alta" <?= (isset($datos['prioridad']) && $datos['prioridad'] === 'Alta') ? 'selected' : '' ?>>Alta (Afecta mi trabajo)</option>
                                        <option value="Critica" <?= (isset($datos['prioridad']) && $datos['prioridad'] === 'Critica') ? 'selected' : '' ?>>Crítica (Sistema caído / Urgente)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Descripción detallada -->
                            <div class="mb-4">
                                <label for="descripcion" class="form-label small fw-bold text-secondary">DESCRIPCIÓN DETALLADA *</label>
                                <textarea class="form-control bg-light" id="descripcion" name="descripcion" rows="5" 
                                          placeholder="Describí con el mayor detalle posible qué estabas haciendo cuando ocurrió el problema..." required><?= htmlspecialchars($datos['descripcion'] ?? '') ?></textarea>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/DeskFlow/cliente" class="btn btn-light border px-4">Cancelar</a>
                                <button type="submit" class="btn btn-primary fw-semibold px-4">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Enviar Solicitud
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>