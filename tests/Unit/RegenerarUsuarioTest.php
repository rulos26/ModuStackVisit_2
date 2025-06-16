<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    try {
        $usuario = 'evaluador2';
        $clave = '0382646740ju*';
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        // Eliminar si existe
        $stmt = $db->prepare('DELETE FROM usuarios WHERE usuario = ?');
        $stmt->execute([$usuario]);
        // Insertar de nuevo
        $stmt = $db->prepare('INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(['Evaluador2', '20000002', 2, 'evaluador@empresa.com', $usuario, $hash]);
        $mensaje = '<div class="alert alert-success">Usuario evaluador2 regenerado correctamente con clave segura.</div>';
    } catch (PDOException $e) {
        $mensaje = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Regenerar Usuario evaluador2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Regenerar Usuario evaluador2</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <button type="submit" class="btn btn-success w-100">Regenerar Usuario</button>
                        </form>
                        <hr>
                        <ul class="list-group mt-3">
                            <li class="list-group-item"><strong>Usuario:</strong> evaluador2 | <strong>Clave:</strong> 0382646740ju* | <strong>Rol:</strong> Evaluador</li>
                        </ul>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    Ruta para probar: <code>tests/Unit/RegenerarUsuarioTest.php</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 