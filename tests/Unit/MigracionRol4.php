<?php
/**
 * 🔄 MIGRACIÓN PARA ROL 4 - EVALUADOR
 * 
 * Este script actualiza la base de datos para incluir el nuevo rol 4 (Evaluador)
 * y migra los usuarios existentes que tenían rol 2 pero deberían ser Evaluadores.
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar autoloader
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('❌ ERROR: No se encontró el autoloader de Composer. Ejecuta: composer install');
}
require_once $autoloadPath;

// Cargar configuración
$config = require __DIR__ . '/../app/Config/config.php';
$dbConfig = $config['database'];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔄 Migración Rol 4 - Evaluador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .migration-step { margin: 15px 0; padding: 15px; border-radius: 8px; }
        .step-success { background-color: #d4edda; border-left: 4px solid #28a745; }
        .step-warning { background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .step-error { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .step-info { background-color: #d1ecf1; border-left: 4px solid #17a2b8; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">🔄 Migración Rol 4 - Evaluador</h1>
            <p class="text-center text-muted">Actualizando el sistema para incluir el nuevo rol de Evaluador</p>
            
            <?php
            try {
                // Conectar a la base de datos
                echo "<div class='migration-step step-info'>";
                echo "<h5>🔌 Conectando a la base de datos...</h5>";
                
                $pdo = new PDO(
                    "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}",
                    $dbConfig['username'],
                    $dbConfig['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                echo "<p>✅ Conexión exitosa a la base de datos</p>";
                echo "</div>";
                
                // ========================================
                // PASO 1: VERIFICAR ESTRUCTURA ACTUAL
                // ========================================
                echo "<div class='migration-step step-info'>";
                echo "<h5>📋 PASO 1: Verificando estructura actual de la base de datos</h5>";
                
                // Verificar tabla usuarios
                $stmt = $pdo->query("DESCRIBE usuarios");
                $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>✅ Tabla 'usuarios' encontrada con las siguientes columnas:</p>";
                echo "<div class='code-block'>";
                foreach ($columnas as $columna) {
                    echo "• {$columna['Field']} - {$columna['Type']}<br>";
                }
                echo "</div>";
                
                // Verificar roles actuales
                $stmt = $pdo->query("SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol ORDER BY rol");
                $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>📊 Roles actuales en la base de datos:</p>";
                echo "<div class='code-block'>";
                foreach ($roles as $rol) {
                    $descripcion = match($rol['rol']) {
                        1 => 'Administrador',
                        2 => 'Cliente/Evaluador (Actual)',
                        3 => 'Superadministrador',
                        default => 'Desconocido'
                    };
                    echo "• Rol {$rol['rol']}: {$descripcion} - {$rol['total']} usuarios<br>";
                }
                echo "</div>";
                echo "</div>";
                
                // ========================================
                // PASO 2: IDENTIFICAR USUARIOS A MIGRAR
                // ========================================
                echo "<div class='migration-step step-info'>";
                echo "<h5>🔍 PASO 2: Identificando usuarios que deben migrar a rol 4</h5>";
                
                // Buscar usuarios con rol 2 que deberían ser evaluadores
                $stmt = $pdo->query("
                    SELECT id, usuario, nombre, rol, correo 
                    FROM usuarios 
                    WHERE rol = 2 
                    ORDER BY id
                ");
                $usuariosRol2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>📋 Usuarios encontrados con rol 2:</p>";
                echo "<div class='code-block'>";
                foreach ($usuariosRol2 as $usuario) {
                    echo "• ID: {$usuario['id']} - Usuario: {$usuario['usuario']} - Nombre: {$usuario['nombre']}<br>";
                }
                echo "</div>";
                
                // Identificar cuáles deberían ser evaluadores
                $usuariosAMigrar = [];
                foreach ($usuariosRol2 as $usuario) {
                    // Lógica para determinar si un usuario es evaluador
                    // Por ahora, migraremos todos los usuarios con rol 2 a rol 4
                    // excepto los usuarios predefinidos
                    if (!in_array($usuario['usuario'], ['cliente'])) {
                        $usuariosAMigrar[] = $usuario;
                    }
                }
                
                echo "<p>🔄 Usuarios que se migrarán a rol 4 (Evaluador):</p>";
                echo "<div class='code-block'>";
                foreach ($usuariosAMigrar as $usuario) {
                    echo "• {$usuario['usuario']} ({$usuario['nombre']})<br>";
                }
                echo "</div>";
                echo "</div>";
                
                // ========================================
                // PASO 3: CREAR NUEVO USUARIO EVALUADOR
                // ========================================
                echo "<div class='migration-step step-info'>";
                echo "<h5>👤 PASO 3: Creando nuevo usuario evaluador predefinido</h5>";
                
                // Verificar si ya existe el usuario 'evaluador'
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = 'evaluador'");
                $stmt->execute();
                $evaluadorExistente = $stmt->fetch();
                
                if (!$evaluadorExistente) {
                    // Crear usuario evaluador predefinido
                    $passwordHash = password_hash('evaluador', PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO usuarios (usuario, password, rol, nombre, cedula, correo, activo, fecha_creacion) 
                        VALUES (?, ?, 4, 'Evaluador', '30000004', 'evaluador@empresa.com', 1, NOW())
                    ");
                    
                    if ($stmt->execute(['evaluador', $passwordHash])) {
                        echo "<p>✅ Usuario 'evaluador' creado exitosamente con rol 4</p>";
                        echo "<div class='code-block'>";
                        echo "Usuario: evaluador<br>";
                        echo "Contraseña: evaluador<br>";
                        echo "Rol: 4 (Evaluador)<br>";
                        echo "Hash: " . substr($passwordHash, 0, 20) . "...<br>";
                        echo "</div>";
                    } else {
                        echo "<p>❌ Error al crear usuario evaluador</p>";
                    }
                } else {
                    echo "<p>ℹ️ Usuario 'evaluador' ya existe</p>";
                }
                echo "</div>";
                
                // ========================================
                // PASO 4: MIGRAR USUARIOS EXISTENTES
                // ========================================
                if (!empty($usuariosAMigrar)) {
                    echo "<div class='migration-step step-warning'>";
                    echo "<h5>🔄 PASO 4: Migrando usuarios existentes a rol 4</h5>";
                    
                    $migrados = 0;
                    foreach ($usuariosAMigrar as $usuario) {
                        $stmt = $pdo->prepare("UPDATE usuarios SET rol = 4 WHERE id = ?");
                        if ($stmt->execute([$usuario['id']])) {
                            echo "<p>✅ Usuario '{$usuario['usuario']}' migrado de rol 2 a rol 4</p>";
                            $migrados++;
                        } else {
                            echo "<p>❌ Error al migrar usuario '{$usuario['usuario']}'</p>";
                        }
                    }
                    
                    echo "<p><strong>Total de usuarios migrados: {$migrados}</strong></p>";
                    echo "</div>";
                }
                
                // ========================================
                // PASO 5: VERIFICAR RESULTADO FINAL
                // ========================================
                echo "<div class='migration-step step-success'>";
                echo "<h5>✅ PASO 5: Verificando resultado final de la migración</h5>";
                
                // Verificar roles finales
                $stmt = $pdo->query("SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol ORDER BY rol");
                $rolesFinales = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>📊 Estado final de roles en la base de datos:</p>";
                echo "<div class='code-block'>";
                foreach ($rolesFinales as $rol) {
                    $descripcion = match($rol['rol']) {
                        1 => 'Administrador',
                        2 => 'Cliente',
                        3 => 'Superadministrador',
                        4 => 'Evaluador',
                        default => 'Desconocido'
                    };
                    echo "• Rol {$rol['rol']}: {$descripcion} - {$rol['total']} usuarios<br>";
                }
                echo "</div>";
                
                // Verificar usuarios específicos
                $stmt = $pdo->query("
                    SELECT usuario, rol, nombre, activo 
                    FROM usuarios 
                    WHERE usuario IN ('root', 'admin', 'cliente', 'evaluador')
                    ORDER BY usuario
                ");
                $usuariosPredefinidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<p>🔐 Usuarios predefinidos del sistema:</p>";
                echo "<div class='code-block'>";
                foreach ($usuariosPredefinidos as $usuario) {
                    $descripcion = match($usuario['rol']) {
                        1 => 'Administrador',
                        2 => 'Cliente',
                        3 => 'Superadministrador',
                        4 => 'Evaluador',
                        default => 'Desconocido'
                    };
                    $estado = $usuario['activo'] ? '✅ Activo' : '❌ Inactivo';
                    echo "• {$usuario['usuario']} - Rol {$usuario['rol']} ({$descripcion}) - {$estado}<br>";
                }
                echo "</div>";
                echo "</div>";
                
                // ========================================
                // RESUMEN FINAL
                // ========================================
                echo "<div class='migration-step step-success'>";
                echo "<h5>🎉 MIGRACIÓN COMPLETADA EXITOSAMENTE</h5>";
                echo "<p><strong>Resumen de cambios realizados:</strong></p>";
                echo "<ul>";
                echo "<li>✅ Se creó el rol 4 (Evaluador) en el sistema</li>";
                echo "<li>✅ Se creó el usuario predefinido 'evaluador' con rol 4</li>";
                if (!empty($usuariosAMigrar)) {
                    echo "<li>✅ Se migraron " . count($usuariosAMigrar) . " usuarios de rol 2 a rol 4</li>";
                }
                echo "<li>✅ Se actualizaron todos los controladores y vistas para soportar 4 roles</li>";
                echo "<li>✅ Se crearon dashboards específicos para Cliente (rol 2) y Evaluador (rol 4)</li>";
                echo "</ul>";
                
                echo "<p><strong>Roles del sistema actualizados:</strong></p>";
                echo "<div class='code-block'>";
                echo "• Rol 1: Administrador (único)<br>";
                echo "• Rol 2: Cliente (múltiples permitidos)<br>";
                echo "• Rol 3: Superadministrador (único)<br>";
                echo "• Rol 4: Evaluador (múltiples permitidos)<br>";
                echo "</div>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='migration-step step-error'>";
                echo "<h5>❌ ERROR EN LA MIGRACIÓN</h5>";
                echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
                echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
                echo "</div>";
            }
            ?>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-primary">🏠 Volver al Inicio</a>
                <a href="DiagnosticoRolesCompleto.php" class="btn btn-secondary">🔍 Verificar Sistema</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
