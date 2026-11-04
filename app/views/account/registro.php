<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - DeskFlow</title>
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
    .card-registro {
      max-width: 600px;
      width: 100%;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

  <div class="card shadow-sm border-0 rounded-4 p-4 card-registro">
    
    <!-- Branding / Logo Header -->
    <div class="text-center mb-4">
      <h2 class="fw-bold text-primary mb-2">DeskFlow</h2>
      <p class="text-secondary small mb-0">Completá tus datos para crear una cuenta</p>
    </div>

    <!-- Mensaje de Error Dinámico -->
    <?php if (isset($error_registro) && !empty($error_registro)): ?>
      <div class="alert alert-danger d-flex align-items-center px-3 py-2 border-0 shadow-sm mb-3 small" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2 fs-6"></i>
        <div class="fw-medium"><?= htmlspecialchars($error_registro) ?></div>
      </div>
    <?php endif; ?>

    <!-- Formulario de Registro -->
    <form action="/DeskFlow/account/registro" method="POST">
      <div class="row g-3">
        
        <!-- Nombre -->
        <div class="col-md-6">
          <label class="form-label small fw-bold text-secondary" for="nombre">NOMBRE</label>
          <input type="text" class="form-control bg-light" name="nombre" id="nombre" placeholder="Ej: Juan" value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required autofocus>
        </div>
        
        <!-- Apellido -->
        <div class="col-md-6">
          <label class="form-label small fw-bold text-secondary" for="apellido">APELLIDO</label>
          <input type="text" class="form-control bg-light" name="apellido" id="apellido" placeholder="Ej: Pérez" value="<?= htmlspecialchars($datos['apellido'] ?? '') ?>" required>
        </div>
        
        <!-- Email -->
        <div class="col-12">
          <label class="form-label small fw-bold text-secondary" for="email">EMAIL</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" class="form-control bg-light" name="email" id="email" placeholder="ejemplo@correo.com" value="<?= htmlspecialchars($datos['email'] ?? '') ?>" required>
          </div>
        </div>
        
        <!-- Contraseña -->
        <div class="col-12">
          <label class="form-label small fw-bold text-secondary" for="password">CONTRASEÑA</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control bg-light" id="password" name="password" placeholder="••••••••" required>
          </div>
          <div class="form-text text-muted" style="font-size: 0.75em;">
            Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.
          </div>
        </div>

        <!-- Repetir Contraseña -->
        <div class="col-12">
          <label class="form-label small fw-bold text-secondary" for="confirm_password">REPETIR CONTRASEÑA</label>
          <div class="input-group">
            <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
            <input type="password" class="form-control bg-light" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
          </div>
        </div>

        <!-- Feedback Dinámico de Contraseña -->
        <div class="col-12" id="password-feedback" style="display: none;">
          <div class="progress mb-2" style="height: 6px;">
            <div id="strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
          </div>
          <div class="text-start" style="font-size: 12px;">
            <span class="fw-bold">Requisitos de seguridad:</span>
            <ul class="mb-0 mt-1 list-unstyled text-muted">
              <li id="req-length"><i class="fa-solid fa-circle-xmark text-danger me-1"></i> Mínimo 8 caracteres</li>
              <li id="req-upper"><i class="fa-solid fa-circle-xmark text-danger me-1"></i> 1 letra mayúscula</li>
              <li id="req-number"><i class="fa-solid fa-circle-xmark text-danger me-1"></i> 1 número</li>
              <li id="req-special"><i class="fa-solid fa-circle-xmark text-danger me-1"></i> 1 carácter especial</li>
            </ul>
          </div>
        </div>

        <!-- Botón Registrarme -->
        <div class="col-12 mt-3">
          <button type="submit" class="btn btn-primary fw-semibold rounded-3 w-100 py-2">
             <i class="fa-solid fa-user-plus me-2"></i>Registrarme
          </button>
        </div>

      </div>
    </form>

    <!-- Enlace para volver al login -->
    <div class="text-center mt-4 small">
      <span class="text-muted">¿Ya tenés cuenta?</span>
      <a href="/DeskFlow/account/login" class="text-decoration-none text-primary fw-medium ms-1">Iniciá sesión</a>
    </div>

  </div>

  <!-- SCRIPT DE VALIDACIÓN DINÁMICA -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
        const passInput = document.getElementById('password');
        const feedbackBox = document.getElementById('password-feedback');
        const strengthBar = document.getElementById('strength-bar');

        const reqLength = document.getElementById('req-length');
        const reqUpper = document.getElementById('req-upper');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        if(passInput) {
            passInput.addEventListener('input', function() {
                const val = this.value;
                feedbackBox.style.display = val.length > 0 ? 'block' : 'none';

                let score = 0;
                const checkIcon = '<i class="fa-solid fa-circle-check text-success me-1"></i>';
                const crossIcon = '<i class="fa-solid fa-circle-xmark text-danger me-1"></i>';

                // Validación de longitud
                if(val.length >= 8) { score++; reqLength.innerHTML = checkIcon + '<span class="text-muted text-decoration-line-through">Mínimo 8 caracteres</span>'; } 
                else { reqLength.innerHTML = crossIcon + 'Mínimo 8 caracteres'; }

                // Validación de Mayúscula
                if(/[A-Z]/.test(val)) { score++; reqUpper.innerHTML = checkIcon + '<span class="text-muted text-decoration-line-through">1 letra mayúscula</span>'; } 
                else { reqUpper.innerHTML = crossIcon + '1 letra mayúscula'; }

                // Validación de Número
                if(/[0-9]/.test(val)) { score++; reqNumber.innerHTML = checkIcon + '<span class="text-muted text-decoration-line-through">1 número</span>'; } 
                else { reqNumber.innerHTML = crossIcon + '1 número'; }

                // Validación de Carácter Especial
                if(/[\W_]/.test(val)) { score++; reqSpecial.innerHTML = checkIcon + '<span class="text-muted text-decoration-line-through">1 carácter especial</span>'; } 
                else { reqSpecial.innerHTML = crossIcon + '1 carácter especial'; }

                // Actualizar barra de progreso
                let percent = (score / 4) * 100;
                strengthBar.style.width = percent + '%';

                if(score <= 1) { strengthBar.className = 'progress-bar bg-danger'; } 
                else if (score === 2 || score === 3) { strengthBar.className = 'progress-bar bg-warning'; } 
                else if (score === 4) { strengthBar.className = 'progress-bar bg-success'; }
            });
        }
    });
  </script>
</body>
</html>