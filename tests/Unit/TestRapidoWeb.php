<?php
/**
 * Test Rápido para Verificar Funcionalidad del Test Web
 * 
 * Este script verifica que todos los componentes del test web estén funcionando correctamente
 * 
 * @version 1.0
 * @author Sistema ModuStack
 */

// Configuración básica
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔧 Test Rápido - Sistema Web</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".status { padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 5px solid; }";
echo ".success { background: #d4edda; color: #155724; border-left-color: #28a745; }";
echo ".error { background: #f8d7da; color: #721c24; border-left-color: #dc3545; }";
echo ".info { background: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔧 Test Rápido - Sistema Web de Usuarios Predefinidos</h1>";

echo "<div class='status info'>";
echo "<strong>📋 Objetivo:</strong> Verificar que todos los componentes del test web estén funcionando correctamente";
echo "</div>";

// Verificación 1: Archivos del sistema
echo "<h3>📁 Verificación de Archivos</h3>";

$filesToCheck = [
    'TestWebUsuariosPredefinidos.php' => 'Interfaz web principal',
    'TestWebUsuariosPredefinidosAPI.php' => 'API PHP del test',
    'README_TEST_WEB_USUARIOS_PREDEFINIDOS.md' => 'Documentación del sistema'
];

foreach ($filesToCheck as $file => $description) {
    $filePath = __DIR__ . '/' . $file;
    if (file_exists($filePath)) {
        $fileSize = filesize($filePath);
        echo "<div class='status success'>";
        echo "✅ <strong>$file</strong> - $description (Tamaño: " . number_format($fileSize) . " bytes)";
        echo "</div>";
    } else {
        echo "<div class='status error'>";
        echo "❌ <strong>$file</strong> - $description (NO ENCONTRADO)";
        echo "</div>";
    }
}

// Verificación 2: Autoloader
echo "<h3>🔧 Verificación de Autoloader</h3>";

$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<div class='status success'>";
    echo "✅ Autoloader encontrado en: vendor/autoload.php";
    echo "</div>";
    
    // Intentar cargar el autoloader
    try {
        require_once $autoloadPath;
        echo "<div class='status success'>";
        echo "✅ Autoloader cargado correctamente";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div class='status error'>";
        echo "❌ Error al cargar autoloader: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='status error'>";
    echo "❌ Autoloader NO encontrado en: vendor/autoload.php";
    echo "</div>";
}

// Verificación 3: Clases del sistema
echo "<h3>🏗️ Verificación de Clases</h3>";

$classesToCheck = [
    'App\Controllers\LoginController' => 'Controlador de login',
    'App\Controllers\SuperAdminController' => 'Controlador de superadmin',
    'App\Database\Database' => 'Clase de base de datos',
    'App\Services\LoggerService' => 'Servicio de logging'
];

foreach ($classesToCheck as $class => $description) {
    if (class_exists($class)) {
        echo "<div class='status success'>";
        echo "✅ <strong>$class</strong> - $description";
        echo "</div>";
    } else {
        echo "<div class='status error'>";
        echo "❌ <strong>$class</strong> - $description (NO ENCONTRADA)";
        echo "</div>";
    }
}

// Verificación 4: Configuración de base de datos
echo "<h3>🗄️ Verificación de Configuración</h3>";

$configPath = __DIR__ . '/../../app/Config/config.php';
if (file_exists($configPath)) {
    echo "<div class='status success'>";
    echo "✅ Archivo de configuración encontrado";
    echo "</div>";
    
    // Intentar cargar la configuración
    try {
        $config = require $configPath;
        if (isset($config['database'])) {
            echo "<div class='status success'>";
            echo "✅ Configuración de base de datos cargada correctamente";
            echo "</div>";
            
            // Mostrar información de la base de datos (sin contraseñas)
            $dbConfig = $config['database'];
            echo "<div class='status info'>";
            echo "📊 Host: " . $dbConfig['host'] . " | Base de datos: " . $dbConfig['dbname'] . " | Usuario: " . $dbConfig['username'];
            echo "</div>";
        } else {
            echo "<div class='status error'>";
            echo "❌ Configuración de base de datos no encontrada en el archivo";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='status error'>";
        echo "❌ Error al cargar configuración: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='status error'>";
    echo "❌ Archivo de configuración NO encontrado";
    echo "</div>";
}

// Verificación 5: Conexión a base de datos
echo "<h3>🔌 Verificación de Conexión a Base de Datos</h3>";

if (class_exists('App\Database\Database')) {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        echo "<div class='status success'>";
        echo "✅ Conexión a base de datos establecida";
        echo "</div>";
        
        // Probar query simple
        $stmt = $db->query('SELECT 1 as test');
        $result = $stmt->fetch();
        if ($result) {
            echo "<div class='status success'>";
            echo "✅ Query de prueba ejecutada correctamente";
            echo "</div>";
        } else {
            echo "<div class='status error'>";
            echo "❌ Query de prueba falló";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='status error'>";
        echo "❌ Error de conexión a base de datos: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='status error'>";
    echo "❌ Clase Database no disponible para verificar conexión";
    echo "</div>";
}

// Verificación 6: Estructura de tablas
echo "<h3>📊 Verificación de Estructura de Tablas</h3>";

if (class_exists('App\Database\Database')) {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        
        // Verificar tabla usuarios
        $stmt = $db->query("SHOW TABLES LIKE 'usuarios'");
        if ($stmt->fetch()) {
            echo "<div class='status success'>";
            echo "✅ Tabla 'usuarios' existe";
            echo "</div>";
            
            // Verificar columnas
            $stmt = $db->query("DESCRIBE usuarios");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns = ['id', 'usuario', 'password', 'rol', 'nombre', 'cedula', 'correo', 'activo'];
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (empty($missingColumns)) {
                echo "<div class='status success'>";
                echo "✅ Todas las columnas requeridas están presentes";
                echo "</div>";
            } else {
                echo "<div class='status error'>";
                echo "❌ Columnas faltantes: " . implode(', ', $missingColumns);
                echo "</div>";
            }
            
        } else {
            echo "<div class='status error'>";
            echo "❌ Tabla 'usuarios' NO existe";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='status error'>";
        echo "❌ Error al verificar estructura de tablas: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='status error'>";
    echo "❌ No se puede verificar estructura de tablas - Database no disponible";
    echo "</div>";
}

// Verificación 7: Usuarios existentes
echo "<h3>👥 Verificación de Usuarios Existentes</h3>";

if (class_exists('App\Database\Database')) {
    try {
        $db = \App\Database\Database::getInstance()->getConnection();
        
        $stmt = $db->query("SELECT usuario, rol, activo FROM usuarios ORDER BY rol, usuario");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($users)) {
            echo "<div class='status success'>";
            echo "✅ Se encontraron " . count($users) . " usuarios en el sistema";
            echo "</div>";
            
            foreach ($users as $user) {
                $status = $user['activo'] ? '🟢 Activo' : '🔴 Inactivo';
                $roleName = match($user['rol']) {
                    1 => 'Administrador',
                    2 => 'Cliente',
                    3 => 'Superadministrador',
                    4 => 'Evaluador',
                    default => 'Desconocido'
                };
                
                echo "<div class='status info'>";
                echo "👤 <strong>{$user['usuario']}</strong> - Rol: {$user['rol']} ({$roleName}) - Estado: {$status}";
                echo "</div>";
            }
        } else {
            echo "<div class='status error'>";
            echo "❌ No se encontraron usuarios en el sistema";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='status error'>";
        echo "❌ Error al verificar usuarios: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='status error'>";
    echo "❌ No se puede verificar usuarios - Database no disponible";
    echo "</div>";
}

// Resumen final
echo "<h3>📋 Resumen del Test Rápido</h3>";

echo "<div class='status info'>";
echo "<strong>🎯 Próximos Pasos:</strong>";
echo "<br>1. Si todas las verificaciones son exitosas, el test web está listo para usar";
echo "<br>2. Si hay errores, revisar la configuración antes de usar el test web";
echo "<br>3. Acceder a TestWebUsuariosPredefinidos.php para ejecutar el test completo";
echo "</div>";

echo "<div style='margin-top: 20px; text-align: center;'>";
echo "<a href='TestWebUsuariosPredefinidos.php' class='btn'>🚀 Ir al Test Web Completo</a>";
echo "<a href='../index.php' class='btn'>🏠 Ir al Login Principal</a>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
