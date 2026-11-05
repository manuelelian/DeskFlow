<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña - DeskFlow</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    body {
      background-color: #f8fafc;
      color: #1e293b;
      font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

  <div class="card shadow-sm border-0 rounded-4 p-4" style="max-width: 420px; width: 100%;">

    <!-- Branding / Logo Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary mb-2">DeskFlow</h2>
      <h5 class="fw-semibold text-dark mb-1">Recuperar Contraseña</h5>
      <p class="text-secondary small mb-0">Ingresá tu correo electrónico y te enviaremos las instrucciones para restablecer tu cuenta.</p>
    </div>

    <!-- Mensaje de Error -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-3" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2 fs-6"></i>
        <div class="small fw-medium"><?= htmlspecialchars($error) ?></div>
      </div>
    <?php endif; ?>

    <!-- Mensaje de Éxito -->
    <?php if (!empty($exito)): ?>
      <div class="alert alert-success d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-3" role="alert">
        <i class="fa-solid fa-circle-check me-2 fs-6"></i>
        <div class="small fw-medium"><?= htmlspecialchars($exito) ?></div>
      </div>
      <?php if (!empty($link_directo)): ?>
        <div class="card bg-light border-0 p-3 mb-3 rounded-3 small">
          <span class="text-muted d-block mb-1"><strong>Simulación de Correo (Modo Desarrollo):</strong></span>
          <a href="<?= htmlspecialchars($link_directo) ?>" class="text-break small fw-bold text-primary">
            Restablecer Contraseña Ahora
          </a>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <!-- Formulario de Solicitud -->
    <form action="/DeskFlow/account/recuperar_contrasena" method="POST">

      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="form-label small fw-bold text-secondary">EMAIL REGISTRADO</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
          <input type="email" class="form-control bg-light" name="email" id="email"
                 placeholder="ejemplo@correo.com" value="<?= htmlspecialchars($email ?? '') ?>" required autofocus>
        </div>
      </div>

      <!-- Botón Enviar -->
      <button type="submit" class="btn btn-primary fw-semibold rounded-3 w-100 py-2">
        <i class="fa-solid fa-paper-plane me-2"></i>Enviar Enlace
      </button>

    </form>

    <!-- Enlaces Volver -->
    <div class="d-flex flex-column align-items-center mt-4 text-center small gap-2">
      <a href="/DeskFlow/account/login" class="text-decoration-none text-primary fw-medium">
        <i class="fa-solid fa-arrow-left me-1"></i>Volver al Inicio de Sesión
      </a>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
