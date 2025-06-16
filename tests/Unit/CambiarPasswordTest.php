<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $nuevaClave = $_POST['nueva_clave'] ?? '';
    if ($usuario && $nuevaClave) {
        $db = Database::getInstance()->getConnection();
        try {
            $hash = password_hash($nuevaClave, PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE usuarios SET password = ? WHERE usuario = ?');
            $stmt->execute([$hash, $usuario]);
            if ($stmt->rowCount() > 0) {
                $mensaje = '<div class="alert alert-success">Contraseña actualizada correctamente para el usuario <b>' . htmlspecialchars($usuario) . '</b>.</div>';
            } else {
                $mensaje = '<div class="alert alert-warning">No se encontró el usuario <b>' . htmlspecialchars($usuario) . '</b> o la contraseña es la misma.</div>';
            }
        } catch (PDOException $e) {
            $mensaje = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $mensaje = '<div class="alert alert-warning">Por favor, completa todos los campos.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4>Cambiar Contraseña de Usuario</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="nueva_clave" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="nueva_clave" name="nueva_clave" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    Ruta para probar: <code>tests/Unit/CambiarPasswordTest.php</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 