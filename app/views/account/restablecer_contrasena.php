<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restablecer Contraseña - DeskFlow</title>
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

  <div class="card shadow-sm border-0 rounded-4 p-4" style="max-width: 440px; width: 100%;">

    <!-- Branding / Logo Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary mb-2">DeskFlow</h2>
      <h5 class="fw-semibold text-dark mb-1">Nueva Contraseña</h5>
      <p class="text-secondary small mb-0">Ingresá tu nueva clave segura para actualizar tu cuenta.</p>
    </div>

    <!-- Mensaje de Error -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-3" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2 fs-6"></i>
        <div class="small fw-medium"><?= htmlspecialchars($error) ?></div>
      </div>
    <?php endif; ?>

    <!-- Formulario de Nueva Contraseña -->
    <form action="/DeskFlow/account/restablecer_contrasena?token=<?= htmlspecialchars($token ?? '') ?>" method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

      <!-- Nueva Contraseña -->
      <div class="mb-3">
        <label for="password" class="form-label small fw-bold text-secondary">NUEVA CONTRASEÑA</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
          <input type="password" class="form-control bg-light" name="password" id="password"
                 placeholder="Mínimo 8 caracteres" required autofocus>
        </div>
        <div class="form-text" style="font-size: 0.75rem;">
          Debe tener al menos 8 caracteres, una mayúscula, un número y un caracter especial.
        </div>
      </div>

      <!-- Confirmar Contraseña -->
      <div class="mb-4">
        <label for="confirm_password" class="form-label small fw-bold text-secondary">CONFIRMAR NUEVA CONTRASEÑA</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
          <input type="password" class="form-control bg-light" name="confirm_password" id="confirm_password"
                 placeholder="Repetí la contraseña" required>
        </div>
      </div>

      <!-- Botón Guardar -->
      <button type="submit" class="btn btn-primary fw-semibold rounded-3 w-100 py-2">
        <i class="fa-solid fa-key me-2"></i>Guardar Nueva Contraseña
      </button>

    </form>

    <!-- Enlaces Volver -->
    <div class="d-flex flex-column align-items-center mt-4 text-center small">
      <a href="/DeskFlow/account/login" class="text-decoration-none text-primary fw-medium">
        <i class="fa-solid fa-arrow-left me-1"></i>Volver al Inicio de Sesión
      </a>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
