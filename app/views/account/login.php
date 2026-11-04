<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - DeskFlow</title>
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
      <p class="text-secondary small mb-0">Ingresá tus credenciales para acceder</p>
    </div>

    <!-- Mensaje de Error (Enviado desde SessionController) -->
    <?php if (isset($error_login) && !empty($error_login)): ?>
      <div class="alert alert-danger d-flex align-items-center rounded-3 px-3 py-2 border-0 shadow-sm mb-3" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2 fs-6"></i>
        <div class="small fw-medium"><?= htmlspecialchars($error_login) ?></div>
      </div>
    <?php endif; ?>

    <!-- Formulario de Login -->
    <!-- El action apunta a la ruta que procesará el login -->
    <form action="/DeskFlow/account/login" method="POST">
      
      <!-- Usuario / Email -->
      <div class="mb-3">
        <label for="email" class="form-label small fw-bold text-secondary">EMAIL</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
          <!-- Mantenemos el email ingresado si hay un error -->
          <input type="email" class="form-control bg-light" name="email" id="email" placeholder="ejemplo@correo.com" value="<?= htmlspecialchars($email_ingresado ?? '') ?>" required autofocus>
        </div>
      </div>

      <!-- Contraseña -->
      <div class="mb-4">
        <label for="password" class="form-label small fw-bold text-secondary">CONTRASEÑA</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
          <input type="password" class="form-control bg-light" name="password" id="password" placeholder="••••••••" required>
        </div>
      </div>

      <!-- Botón Ingresar -->
      <button type="submit" class="btn btn-primary fw-semibold rounded-3 w-100 py-2">
        <i class="fa-solid fa-right-to-bracket me-2"></i>Iniciar Sesión
      </button>

    </form>

    <!-- Enlaces de Registro y Recuperación (Rutas relativas) -->
    <div class="d-flex flex-column align-items-center mt-4 text-center small gap-3">
      <a href="/DeskFlow/account/recuperar_contrasena" class="text-decoration-none text-primary fw-medium">¿Olvidaste tu contraseña?</a>
      <a href="/DeskFlow/account/registro" class="text-decoration-none text-primary fw-medium">Crear una cuenta</a>
    </div>
  </div>

  <!-- Bootstrap 5 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>