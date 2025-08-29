<?php
require_once __DIR__ . '/../../app/Database/Database.php';

use App\Database\Database;

$mensaje = '';
$resultados = [];

// Función para verificar si una columna existe
function columnExists($db, $table, $column) {
    $stmt = $db->prepare("SHOW COLUMNS FROM $table LIKE ?");
    $stmt->execute([$column]);
    return $stmt->fetch() !== false;
}

// Función para agregar columna si no existe
function addColumnIfNotExists($db, $table, $column, $definition) {
    if (!columnExists($db, $table, $column)) {
        $stmt = $db->prepare("ALTER TABLE $table ADD COLUMN $column $definition");
        $stmt->execute();
        return true;
    }
    return false;
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    switch ($accion) {
        case 'actualizar_tabla':
            $db = Database::getInstance()->getConnection();
            
            try {
                $db->beginTransaction();
                
                // Lista de columnas a agregar
                $columns = [
                    'activo' => 'TINYINT(1) DEFAULT 1 COMMENT "Estado activo del usuario"',
                    'ultimo_acceso' => 'TIMESTAMP NULL COMMENT "Último acceso del usuario"',
                    'intentos_fallidos' => 'INT DEFAULT 0 COMMENT "Contador de intentos fallidos"',
                    'bloqueado_hasta' => 'TIMESTAMP NULL COMMENT "Fecha hasta cuando está bloqueado"',
                    'fecha_creacion' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT "Fecha de creación del usuario"',
                    'fecha_actualizacion' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "Fecha de última actualización"'
                ];
                
                $addedColumns = [];
                $existingColumns = [];
                
                foreach ($columns as $column => $definition) {
                    if (addColumnIfNotExists($db, 'usuarios', $column, $definition)) {
                        $addedColumns[] = $column;
                    } else {
                        $existingColumns[] = $column;
                    }
                }
                
                // Crear índices para mejorar rendimiento
                $indexes = [
                    'idx_usuarios_activo' => 'activo',
                    'idx_usuarios_ultimo_acceso' => 'ultimo_acceso',
                    'idx_usuarios_intentos_fallidos' => 'intentos_fallidos',
                    'idx_usuarios_bloqueado_hasta' => 'bloqueado_hasta'
                ];
                
                $addedIndexes = [];
                foreach ($indexes as $indexName => $column) {
                    // Verificar si el índice existe
                    $stmt = $db->prepare("SHOW INDEX FROM usuarios WHERE Key_name = ?");
                    $stmt->execute([$indexName]);
                    
                    if (!$stmt->fetch()) {
                        $stmt = $db->prepare("CREATE INDEX $indexName ON usuarios ($column)");
                        $stmt->execute();
                        $addedIndexes[] = $indexName;
                    }
                }
                
                $db->commit();
                
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Tabla usuarios actualizada exitosamente',
                    'datos' => [
                        'columnas_agregadas' => $addedColumns,
                        'columnas_existentes' => $existingColumns,
                        'indices_agregados' => $addedIndexes
                    ]
                ];
                
            } catch (PDOException $e) {
                $db->rollBack();
                $resultados[] = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al actualizar la tabla: ' . $e->getMessage()
                ];
            }
            break;
            
        case 'verificar_estructura':
            $db = Database::getInstance()->getConnection();
            
            try {
                // Obtener estructura actual de la tabla
                $stmt = $db->prepare("DESCRIBE usuarios");
                $stmt->execute();
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Obtener índices
                $stmt = $db->prepare("SHOW INDEX FROM usuarios");
                $stmt->execute();
                $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $resultados[] = [
                    'tipo' => 'info',
                    'mensaje' => 'Estructura actual de la tabla usuarios',
                    'datos' => [
                        'columnas' => $columns,
                        'indices' => $indexes
                    ]
                ];
                
            } catch (PDOException $e) {
                $resultados[] = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al verificar estructura: ' . $e->getMessage()
                ];
            }
            break;
            
        case 'migrar_usuarios_existentes':
            $db = Database::getInstance()->getConnection();
            
            try {
                $db->beginTransaction();
                
                // Actualizar usuarios existentes
                $stmt = $db->prepare("
                    UPDATE usuarios 
                    SET activo = 1,
                        fecha_creacion = COALESCE(fecha_creacion, NOW()),
                        fecha_actualizacion = NOW()
                    WHERE activo IS NULL
                ");
                $stmt->execute();
                $updatedRows = $stmt->rowCount();
                
                $db->commit();
                
                $resultados[] = [
                    'tipo' => 'success',
                    'mensaje' => 'Usuarios existentes migrados exitosamente',
                    'datos' => [
                        'usuarios_actualizados' => $updatedRows
                    ]
                ];
                
            } catch (PDOException $e) {
                $db->rollBack();
                $resultados[] = [
                    'tipo' => 'error',
                    'mensaje' => 'Error al migrar usuarios: ' . $e->getMessage()
                ];
            }
            break;
    }
}

// Verificar estructura actual
$db = Database::getInstance()->getConnection();
$currentColumns = [];
try {
    $stmt = $db->prepare("DESCRIBE usuarios");
    $stmt->execute();
    $currentColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = 'Error al verificar estructura: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar Tabla Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .test-result {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .test-success { background-color: #d4edda; border: 1px solid #c3e6cb; }
        .test-error { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .test-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><i class="bi bi-database-gear me-2"></i>Actualizar Tabla Usuarios</h4>
                    </div>
                    <div class="card-body">
                        <!-- Estado actual -->
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Estado Actual:</h6>
                            <p class="mb-1"><strong>Tabla:</strong> usuarios</p>
                            <p class="mb-1"><strong>Columnas actuales:</strong> <?php echo count($currentColumns); ?></p>
                            <p class="mb-0"><strong>Acción:</strong> Agregar columnas de seguridad y auditoría</p>
                        </div>

                        <!-- Columnas a agregar -->
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-list-check me-2"></i>Columnas a Agregar:</h6>
                            <ul class="mb-0">
                                <li><code>activo</code> - Estado activo del usuario (TINYINT)</li>
                                <li><code>ultimo_acceso</code> - Último acceso del usuario (TIMESTAMP)</li>
                                <li><code>intentos_fallidos</code> - Contador de intentos fallidos (INT)</li>
                                <li><code>bloqueado_hasta</code> - Fecha de bloqueo (TIMESTAMP)</li>
                                <li><code>fecha_creacion</code> - Fecha de creación (TIMESTAMP)</li>
                                <li><code>fecha_actualizacion</code> - Fecha de actualización (TIMESTAMP)</li>
                            </ul>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="bi bi-tools me-2"></i>Acciones:</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="accion" value="verificar_estructura">
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100">
                                        <i class="bi bi-search me-1"></i>Verificar Estructura
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 mb-2">
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres actualizar la tabla usuarios?');">
                                    <input type="hidden" name="accion" value="actualizar_tabla">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-database-add me-2"></i>Actualizar Tabla
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4 mb-2">
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres migrar usuarios existentes?');">
                                    <input type="hidden" name="accion" value="migrar_usuarios_existentes">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-people me-2"></i>Migrar Usuarios
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Resultados -->
                        <?php if (!empty($resultados)): ?>
                            <div class="mb-4">
                                <h6><i class="bi bi-list-check me-2"></i>Resultados:</h6>
                                <?php foreach ($resultados as $resultado): ?>
                                    <div class="test-result test-<?php echo $resultado['tipo']; ?>">
                                        <strong><?php echo htmlspecialchars($resultado['mensaje']); ?></strong>
                                        <?php if (isset($resultado['datos'])): ?>
                                            <details class="mt-2">
                                                <summary>Ver detalles técnicos</summary>
                                                <pre class="mt-2 mb-0"><code><?php echo htmlspecialchars(json_encode($resultado['datos'], JSON_PRETTY_PRINT)); ?></code></pre>
                                            </details>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Información adicional -->
                        <div class="alert alert-success">
                            <h6><i class="bi bi-lightbulb me-2"></i>Información:</h6>
                            <ul class="mb-0">
                                <li>Este script agrega columnas necesarias para el sistema de seguridad mejorado</li>
                                <li>Las columnas incluyen control de intentos fallidos y bloqueo de cuentas</li>
                                <li>Se crean índices para mejorar el rendimiento de las consultas</li>
                                <li>Los usuarios existentes se marcan como activos por defecto</li>
                                <li>Después de la actualización, el LoginController optimizado funcionará correctamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="CorregirHashUsuario.php" class="btn btn-outline-primary me-2">
                        <i class="bi bi-wrench me-1"></i>Corregir Hash
                    </a>
                    <a href="TestLoginSuperAdmin.php" class="btn btn-outline-success me-2">
                        <i class="bi bi-shield-lock me-1"></i>Test Login
                    </a>
                    <a href="CrearSuperAdminTest.php" class="btn btn-outline-warning me-2">
                        <i class="bi bi-person-plus me-1"></i>Crear Superadmin
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
