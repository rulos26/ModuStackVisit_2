<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance()->getConnection();
    try {
        // Verificar si la columna ya existe
        $stmt = $db->prepare("SHOW COLUMNS FROM usuarios LIKE 'fecha_creacion'");
        $stmt->execute();
        
        if ($stmt->fetch()) {
            $mensaje = '<div class="alert alert-info">La columna fecha_creacion ya existe en la tabla usuarios.</div>';
        } else {
            // Agregar la columna fecha_creacion
            $stmt = $db->prepare("ALTER TABLE usuarios ADD COLUMN fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
            $stmt->execute();
            
            $mensaje = '<div class="alert alert-success">Columna fecha_creacion agregada exitosamente a la tabla usuarios.</div>';
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
    <title>Agregar Columna fecha_creacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="bi bi-database-add me-2"></i>Agregar Columna fecha_creacion</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje) echo $mensaje; ?>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-database-add me-2"></i>
                                Agregar Columna fecha_creacion
                            </button>
                        </form>
                        <hr>
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Información:</h6>
                            <ul class="mb-0">
                                <li>Este script agrega la columna <code>fecha_creacion</code> a la tabla <code>usuarios</code></li>
                                <li>La columna será de tipo <code>TIMESTAMP</code> con valor por defecto <code>CURRENT_TIMESTAMP</code></li>
                                <li>Esta columna es necesaria para el funcionamiento del rol Superadministrador</li>
                                <li>Si la columna ya existe, no se realizará ningún cambio</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="CrearSuperAdminTest.php" class="btn btn-success">
                        <i class="bi bi-arrow-right me-2"></i>
                        Continuar a Crear Superadministrador
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
