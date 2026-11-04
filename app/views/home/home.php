<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Home - DeskFlow</title>
    <!-- Incluimos Bootstrap para que la tabla se vea bien -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar sencilla para el menú -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">DeskFlow</a>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Hola, <?= htmlspecialchars($_SESSION['USER']['name'] ?? 'Usuario') ?>!
                </span>
                <!-- El botón de salir llama a actionSalir de AccountController -->
                <a href="/DeskFlow/account/salir" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Inicio de Proyecto</h1>
        
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Lista de Usuarios Registrados</h5>
            </div>
            <div class="card-body">
                
                <!-- Verificamos si la variable $usuarios que mandó el Controller tiene datos -->
                <?php if (!empty($usuarios)): ?>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Rol ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Recorremos la lista de usuarios y generamos una fila por cada uno -->
                            <?php foreach ($usuarios as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user->id) ?></td>
                                    <td><?= htmlspecialchars($user->nombre) ?></td>
                                    <td><?= htmlspecialchars($user->apellido) ?></td>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                    <td>
                                        <?php 
                                            // Mostramos un texto amigable según el ID de rol
                                            if ($user->id_rol == 1) echo "Cliente";
                                            elseif ($user->id_rol == 2) echo "Agente";
                                            elseif ($user->id_rol == 3) echo "Administrador";
                                            else echo $user->id_rol;
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No hay usuarios registrados en la base de datos.</div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>
</html>