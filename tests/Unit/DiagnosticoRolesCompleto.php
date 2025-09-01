<?php
/**
 * 🔍 DIAGNÓSTICO COMPLETO DE ROLES DEL SISTEMA
 * 
 * Este script revisa todo el proyecto para asegurar que los cuatro roles
 * estén correctamente implementados en todo el sistema.
 * 
 * Roles definidos:
 * - Rol 1: Administrador
 * - Rol 2: Cliente  
 * - Rol 3: Superadministrador
 * - Rol 4: Evaluador (NUEVO)
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Función para mostrar resultados
function mostrarResultado($titulo, $contenido, $tipo = 'info') {
    $clases = [
        'info' => 'alert-info',
        'success' => 'alert-success', 
        'warning' => 'alert-warning',
        'danger' => 'alert-danger'
    ];
    
    echo "<div class='alert {$clases[$tipo]}' role='alert'>";
    echo "<h5>{$titulo}</h5>";
    echo "<pre>" . htmlspecialchars($contenido) . "</pre>";
    echo "</div>";
}

// Función para verificar archivo
function verificarArchivo($ruta, $descripcion) {
    if (file_exists($ruta)) {
        return "✅ {$descripcion} - Existe";
    } else {
        return "❌ {$descripcion} - NO EXISTE";
    }
}

// Función para buscar patrones en archivos
function buscarPatrones($directorio, $patrones) {
    $resultados = [];
    
    if (!is_dir($directorio)) {
        return $resultados;
    }
    
    $archivos = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directorio, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($archivos as $archivo) {
        if ($archivo->isFile() && in_array($archivo->getExtension(), ['php', 'js', 'html'])) {
            $contenido = file_get_contents($archivo->getPathname());
            $rutaRelativa = str_replace(__DIR__ . '/../', '', $archivo->getPathname());
            
            foreach ($patrones as $patron => $descripcion) {
                if (preg_match($patron, $contenido)) {
                    $resultados[] = [
                        'archivo' => $rutaRelativa,
                        'patron' => $patron,
                        'descripcion' => $descripcion
                    ];
                }
            }
        }
    }
    
    return $resultados;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔍 Diagnóstico Completo de Roles del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .resultado-archivo { margin: 10px 0; padding: 10px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .resultado-rol { margin: 5px 0; padding: 5px; border-radius: 4px; }
        .rol-1 { background: #e3f2fd; border-left: 4px solid #2196f3; }
        .rol-2 { background: #e8f5e8; border-left: 4px solid #4caf50; }
        .rol-3 { background: #fff3e0; border-left: 4px solid #ff9800; }
        .rol-4 { background: #f3e5f5; border-left: 4px solid #9c27b0; }
        .problema { background: #ffebee; border-left: 4px solid #f44336; }
    </style>
</head>
<body class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">🔍 Diagnóstico Completo de Roles del Sistema</h1>
            <p class="text-center text-muted">Revisión exhaustiva de la implementación de los cuatro roles en todo el proyecto</p>
            
            <div class="alert alert-info">
                <h5>📋 Roles Definidos en el Sistema:</h5>
                <ul>
                    <li><strong>Rol 1:</strong> Administrador</li>
                    <li><strong>Rol 2:</strong> Cliente</li>
                    <li><strong>Rol 3:</strong> Superadministrador</li>
                    <li><strong>Rol 4:</strong> Evaluador (NUEVO)</li>
                </ul>
            </div>
            
            <?php
            // ========================================
            // 1. VERIFICACIÓN DE ARCHIVOS PRINCIPALES
            // ========================================
            echo "<h2 class='mt-4'>📁 Verificación de Archivos Principales</h2>";
            
            $archivosPrincipales = [
                'index.php' => 'Archivo principal de login',
                'dashboard.php' => 'Router de dashboard',
                'app/Controllers/LoginController.php' => 'Controlador de login',
                'app/Controllers/SuperAdminController.php' => 'Controlador de superadmin',
                'resources/views/admin/dashboardAdmin.php' => 'Dashboard de administrador',
                'resources/views/superadmin/dashboardSuperAdmin.php' => 'Dashboard de superadmin',
                'resources/views/evaluador/dashboardEavaluador.php' => 'Dashboard de evaluador',
                'resources/views/layout/menu.php' => 'Menú de navegación'
            ];
            
            foreach ($archivosPrincipales as $archivo => $descripcion) {
                echo "<div class='resultado-archivo'>";
                echo verificarArchivo($archivo, $descripcion);
                echo "</div>";
            }
            
            // ========================================
            // 2. ANÁLISIS DE CONTROLADORES
            // ========================================
            echo "<h2 class='mt-4'>🎮 Análisis de Controladores</h2>";
            
            // Verificar LoginController
            if (file_exists('app/Controllers/LoginController.php')) {
                $contenido = file_get_contents('app/Controllers/LoginController.php');
                
                // Buscar método getRedirectUrl
                if (preg_match('/private function getRedirectUrl\\(\$rol\\)\\s*\\{[^}]*\\}/s', $contenido, $matches)) {
                    echo "<div class='alert alert-success'>";
                    echo "<h5>✅ Método getRedirectUrl encontrado en LoginController</h5>";
                    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-danger'>";
                    echo "<h5>❌ Método getRedirectUrl NO encontrado en LoginController</h5>";
                    echo "</div>";
                }
                
                // Verificar roles en getRedirectUrl
                if (preg_match('/case 1:/', $contenido) && 
                    preg_match('/case 2:/', $contenido) && 
                    preg_match('/case 3:/', $contenido)) {
                    echo "<div class='alert alert-success'>";
                    echo "<h5>✅ Roles 1, 2 y 3 encontrados en getRedirectUrl</h5>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-warning'>";
                    echo "<h5>⚠️ Faltan algunos roles en getRedirectUrl</h5>";
                    echo "</div>";
                }
            }
            
            // ========================================
            // 3. ANÁLISIS DE VISTAS
            // ========================================
            echo "<h2 class='mt-4'>👁️ Análisis de Vistas</h2>";
            
            // Verificar dashboards por rol
            $dashboards = [
                1 => ['ruta' => 'resources/views/admin/dashboardAdmin.php', 'rol' => 'Administrador'],
                2 => ['ruta' => 'resources/views/cliente/dashboardCliente.php', 'rol' => 'Cliente'],
                3 => ['ruta' => 'resources/views/superadmin/dashboardSuperAdmin.php', 'rol' => 'Superadministrador'],
                4 => ['ruta' => 'resources/views/evaluador/dashboardEvaluador.php', 'rol' => 'Evaluador']
            ];
            
            foreach ($dashboards as $rol => $dashboard) {
                echo "<div class='resultado-archivo resultado-rol rol-{$rol}'>";
                echo verificarArchivo($dashboard['ruta'], "Dashboard de {$dashboard['rol']} (Rol {$rol})");
                echo "</div>";
            }
            
            // ========================================
            // 4. ANÁLISIS DE MENÚS Y NAVEGACIÓN
            // ========================================
            echo "<h2 class='mt-4'>🧭 Análisis de Menús y Navegación</h2>";
            
            if (file_exists('resources/views/layout/menu.php')) {
                $contenido = file_get_contents('resources/views/layout/menu.php');
                
                // Verificar comentarios sobre roles
                if (preg_match('/Rol 1.*Administrador/', $contenido)) {
                    echo "<div class='alert alert-success'>";
                    echo "<h5>✅ Comentarios de roles encontrados en menu.php</h5>";
                    echo "</div>";
                }
                
                // Verificar lógica de roles
                if (preg_match('/\\$_SESSION\[\'rol\'\]\s*===\s*1/', $contenido) ||
                    preg_match('/\\$_SESSION\[\'rol\'\]\s*==\s*1/', $contenido)) {
                    echo "<div class='alert alert-success'>";
                    echo "<h5>✅ Lógica de roles encontrada en menu.php</h5>";
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-warning'>";
                    echo "<h5>⚠️ Lógica de roles no encontrada en menu.php</h5>";
                    echo "</div>";
                }
            }
            
            // ========================================
            // 5. BÚSQUEDA DE PATRONES EN TODO EL PROYECTO
            // ========================================
            echo "<h2 class='mt-4'>🔍 Búsqueda de Patrones en Todo el Proyecto</h2>";
            
            $patrones = [
                '/rol\s*==\s*1/' => 'Comparación de rol 1 (Administrador)',
                '/rol\s*==\s*2/' => 'Comparación de rol 2 (Cliente)',
                '/rol\s*==\s*3/' => 'Comparación de rol 3 (Superadministrador)',
                '/rol\s*==\s*4/' => 'Comparación de rol 4 (Evaluador)',
                '/case 1:/' => 'Switch case para rol 1',
                '/case 2:/' => 'Switch case para rol 2',
                '/case 3:/' => 'Switch case para rol 3',
                '/case 4:/' => 'Switch case para rol 4',
                '/Administrador/' => 'Referencia a Administrador',
                '/Cliente/' => 'Referencia a Cliente',
                '/Superadministrador/' => 'Referencia a Superadministrador',
                '/Evaluador/' => 'Referencia a Evaluador'
            ];
            
            $resultados = buscarPatrones(__DIR__ . '/..', $patrones);
            
            if (!empty($resultados)) {
                echo "<div class='alert alert-info'>";
                echo "<h5>📊 Patrones Encontrados:</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm'>";
                echo "<thead><tr><th>Archivo</th><th>Patrón</th><th>Descripción</th></tr></thead>";
                echo "<tbody>";
                
                foreach ($resultados as $resultado) {
                    echo "<tr>";
                    echo "<td><code>{$resultado['archivo']}</code></td>";
                    echo "<td><code>{$resultado['patron']}</code></td>";
                    echo "<td>{$resultado['descripcion']}</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
                echo "</div>";
                echo "</div>";
            }
            
            // ========================================
            // 6. VERIFICACIÓN DE BASE DE DATOS
            // ========================================
            echo "<h2 class='mt-4'>🗄️ Verificación de Base de Datos</h2>";
            
            try {
                // Intentar conectar a la base de datos
                $config = require __DIR__ . '/../app/Config/config.php';
                $dbConfig = $config['database'];
                
                $pdo = new PDO(
                    "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}",
                    $dbConfig['username'],
                    $dbConfig['password']
                );
                
                echo "<div class='alert alert-success'>";
                echo "<h5>✅ Conexión a base de datos exitosa</h5>";
                echo "</div>";
                
                // Verificar estructura de tabla usuarios
                $stmt = $pdo->query("DESCRIBE usuarios");
                $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<div class='alert alert-info'>";
                echo "<h5>📋 Estructura de la tabla 'usuarios':</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm'>";
                echo "<thead><tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr></thead>";
                echo "<tbody>";
                
                foreach ($columnas as $columna) {
                    echo "<tr>";
                    echo "<td><code>{$columna['Field']}</code></td>";
                    echo "<td>{$columna['Type']}</td>";
                    echo "<td>{$columna['Null']}</td>";
                    echo "<td>{$columna['Key']}</td>";
                    echo "<td>{$columna['Default']}</td>";
                    echo "<td>{$columna['Extra']}</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
                echo "</div>";
                echo "</div>";
                
                // Verificar roles existentes
                $stmt = $pdo->query("SELECT rol, COUNT(*) as total FROM usuarios GROUP BY rol ORDER BY rol");
                $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<div class='alert alert-info'>";
                echo "<h5>👥 Usuarios por Rol en la Base de Datos:</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm'>";
                echo "<thead><tr><th>Rol</th><th>Descripción</th><th>Total Usuarios</th></tr></thead>";
                echo "<tbody>";
                
                foreach ($roles as $rol) {
                    $descripcion = match($rol['rol']) {
                        1 => 'Administrador',
                        2 => 'Cliente',
                        3 => 'Superadministrador',
                        4 => 'Evaluador',
                        default => 'Desconocido'
                    };
                    
                    echo "<tr>";
                    echo "<td><strong>{$rol['rol']}</strong></td>";
                    echo "<td>{$descripcion}</td>";
                    echo "<td>{$rol['total']}</td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table>";
                echo "</div>";
                echo "</div>";
                
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>";
                echo "<h5>❌ Error al conectar con la base de datos:</h5>";
                echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
                echo "</div>";
            }
            
            // ========================================
            // 7. RESUMEN Y RECOMENDACIONES
            // ========================================
            echo "<h2 class='mt-4'>📋 Resumen y Recomendaciones</h2>";
            
            echo "<div class='alert alert-warning'>";
            echo "<h5>⚠️ Problemas Identificados:</h5>";
            echo "<ul>";
            echo "<li><strong>Rol 4 (Evaluador):</strong> No está implementado en el sistema principal</li>";
            echo "<li><strong>Dashboard de Cliente:</strong> No existe archivo específico para rol 2</li>";
            echo "<li><strong>Dashboard de Evaluador:</strong> No existe archivo específico para rol 4</li>";
            echo "<li><strong>Inconsistencias:</strong> Algunos archivos usan 'evaluador' para rol 2 en lugar de 'cliente'</li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<div class='alert alert-info'>";
            echo "<h5>🔧 Acciones Recomendadas:</h5>";
            echo "<ol>";
            echo "<li><strong>Crear Rol 4:</strong> Implementar el rol de Evaluador en todo el sistema</li>";
            echo "<li><strong>Separar Cliente y Evaluador:</strong> Diferenciar claramente los roles 2 y 4</li>";
            echo "<li><strong>Crear Dashboards:</strong> Crear archivos específicos para cada rol</li>";
            echo "<li><strong>Actualizar Controladores:</strong> Modificar LoginController y otros para incluir rol 4</li>";
            echo "<li><strong>Actualizar Menús:</strong> Modificar la lógica de navegación para los cuatro roles</li>";
            echo "<li><strong>Actualizar Base de Datos:</strong> Asegurar que la tabla usuarios soporte el rol 4</li>";
            echo "</ol>";
            echo "</div>";
            
            ?>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">🏠 Volver al Inicio</a>
                <a href="TestSistemaCompleto.php" class="btn btn-secondary">🧪 Test del Sistema</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
