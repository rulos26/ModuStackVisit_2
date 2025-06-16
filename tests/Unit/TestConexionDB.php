<?php

require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$resultado = '';
$exito = false;

try {
    $db = Database::getInstance()->getConnection();
    $resultado = "Conexión exitosa a la base de datos 🎉";
    $exito = true;
} catch (PDOException $e) {
    $resultado = "Error de conexión: " . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Conexión a la Base de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .reporte-card {
            max-width: 500px;
            margin: 60px auto;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .reporte-header {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 12px 12px 0 0;
            padding: 1.5rem 1rem 1rem 1rem;
            text-align: center;
        }
        .reporte-body {
            padding: 2rem 1.5rem;
        }
        .icon-success {
            font-size: 3rem;
            color: #28a745;
        }
        .icon-error {
            font-size: 3rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="card reporte-card">
        <div class="reporte-header">
            <h2 class="mb-0">Reporte de Conexión a la Base de Datos</h2>
        </div>
        <div class="reporte-body text-center">
            <?php if ($exito): ?>
                <div class="icon-success mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="alert alert-success" role="alert">
                    <?php echo $resultado; ?>
                </div>
            <?php else: ?>
                <div class="icon-error mb-3">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="alert alert-danger" role="alert">
                    <?php echo $resultado; ?>
                </div>
            <?php endif; ?>
            <hr>
            <div class="text-muted small text-start mt-4">
                <strong>Parámetros de conexión:</strong><br>
                Servidor: <code>127.0.0.1</code><br>
                Usuario: <code>u130454517_root</code><br>
                Base de datos: <code>u130454517_modulo_vista</code><br>
                Charset: <code>utf8mb4</code>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</body>
</html> 