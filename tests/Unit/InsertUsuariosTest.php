<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    try {
        // Usuario admin
        $nombre1 = 'Administrador';
        $cedula1 = '10000001';
        $rol1 = 1;
        $correo1 = 'admin@empresa.com';
        $usuario1 = 'admin';
        $password1 = password_hash('123', PASSWORD_DEFAULT);

        // Usuario evaluador
        $nombre2 = 'Evaluador';
        $cedula2 = '20000002';
        $rol2 = 2;
        $correo2 = 'evaluador@empresa.com';
        $usuario2 = 'evaluador';
        $password2 = password_hash('0382646740ju*', PASSWORD_DEFAULT);

        $stmt = $db->prepare('INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nombre1, $cedula1, $rol1, $correo1, $usuario1, $password1]);
        $stmt->execute([$nombre2, $cedula2, $rol2, $correo2, $usuario2, $password2]);

        $mensaje = '<div class="alert alert-success">Usuarios insertados correctamente.</div>';
    } catch (PDOException $e) {
        $mensaje = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Insertar Usuarios de Prueba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Insertar Usuarios de Prueba</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <button type="submit" class="btn btn-success w-100">Insertar Usuarios</button>
                        </form>
                        <hr>
                        <ul class="list-group mt-3">
                            <li class="list-group-item"><strong>Usuario:</strong> admin | <strong>Clave:</strong> 123 | <strong>Rol:</strong> Administrativo</li>
                            <li class="list-group-item"><strong>Usuario:</strong> evaluador | <strong>Clave:</strong> 0382646740ju* | <strong>Rol:</strong> Evaluador</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    Ruta para probar: <code>tests/Unit/InsertUsuariosTest.php</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 