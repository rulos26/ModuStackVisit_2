<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    try {
        // Usuario superadministrador
        $nombre = 'Superadministrador';
        $cedula = '30000003';
        $rol = 3;
        $correo = 'root@empresa.com';
        $usuario = 'root';
        $password = password_hash('root', PASSWORD_DEFAULT);

        // Verificar si ya existe
        $stmt = $db->prepare('SELECT id FROM usuarios WHERE usuario = ? OR cedula = ?');
        $stmt->execute([$usuario, $cedula]);
        
        if ($stmt->fetch()) {
            $mensaje = '<div class="alert alert-warning">El usuario superadministrador ya existe.</div>';
        } else {
            $stmt = $db->prepare('INSERT INTO usuarios (nombre, cedula, rol, correo, usuario, password, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$nombre, $cedula, $rol, $correo, $usuario, $password]);

            $mensaje = '<div class="alert alert-success">Usuario superadministrador creado exitosamente.</div>';
        }
    } catch (PDOException $e) {
        $mensaje = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario Superadministrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4><i class="bi bi-shield-lock-fill me-2"></i>Crear Superadministrador</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-person-plus me-2"></i>
                                Crear Usuario Superadministrador
                            </button>
                        </form>
                        <hr>
                                                 <div class="alert alert-info">
                             <h6><i class="bi bi-info-circle me-2"></i>Credenciales del Superadministrador:</h6>
                             <ul class="mb-0">
                                 <li><strong>Usuario:</strong> root</li>
                                 <li><strong>Contraseña:</strong> root</li>
                                 <li><strong>Rol:</strong> Superadministrador (3)</li>
                                 <li><strong>Cédula:</strong> 30000003</li>
                                 <li><strong>Email:</strong> root@empresa.com</li>
                             </ul>
                         </div>
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle me-2"></i>Importante:</h6>
                            <ul class="mb-0">
                                <li>Este usuario tiene acceso completo al sistema</li>
                                <li>Puede gestionar todos los usuarios</li>
                                <li>Puede acceder a logs y auditoría</li>
                                <li>Puede crear respaldos del sistema</li>
                                <li>Cambie la contraseña después del primer acceso</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    Ruta para probar: <code>tests/Unit/CrearSuperAdminTest.php</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
