<?php
// Script de diagnóstico del servidor para identificar problemas
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔍 Diagnóstico del Servidor</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }";
echo ".warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo ".critical { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; font-weight: bold; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo ".btn-danger { background: #dc3545; }";
echo ".btn-danger:hover { background: #c82333; }";
echo "pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Diagnóstico del Servidor</h1>";
echo "<p>Este script identifica problemas específicos del servidor que pueden estar causando errores 500.</p>";

// 1. Información del servidor
echo "<div class='info'>";
echo "<h3>1. Información del Servidor</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "<p><strong>Server Protocol:</strong> " . ($_SERVER['SERVER_PROTOCOL'] ?? 'N/A') . "</p>";
echo "<p><strong>Server Name:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>Server Port:</strong> " . ($_SERVER['SERVER_PORT'] ?? 'N/A') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>Request URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
echo "<p><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>";
echo "</div>";

// 2. Información del sistema
echo "<div class='info'>";
echo "<h3>2. Información del Sistema</h3>";
echo "<p><strong>OS:</strong> " . PHP_OS . "</p>";
echo "<p><strong>Architecture:</strong> " . (PHP_INT_SIZE * 8) . " bits</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " segundos</p>";
echo "<p><strong>Upload Max Filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>Post Max Size:</strong> " . ini_get('post_max_size') . "</p>";
echo "</div>";

// 3. Verificar módulos de Apache
echo "<div class='info'>";
echo "<h3>3. Módulos de Apache</h3>";

$modulosApache = [
    'mod_rewrite' => 'Rewrite Engine',
    'mod_headers' => 'Headers HTTP',
    'mod_expires' => 'Cache Control',
    'mod_deflate' => 'Compresión GZIP'
];

foreach ($modulosApache as $modulo => $descripcion) {
    if (function_exists('apache_get_modules')) {
        $modulos = apache_get_modules();
        $disponible = in_array($modulo, $modulos);
        $status = $disponible ? '✅' : '❌';
        echo "<p>$status <strong>$descripcion ($modulo):</strong> " . ($disponible ? 'Disponible' : 'NO disponible') . "</p>";
    } else {
        echo "<p>⚠️ <strong>$descripcion ($modulo):</strong> No se puede verificar (función no disponible)</p>";
    }
}
echo "</div>";

// 4. Verificar archivos críticos
echo "<div class='info'>";
echo "<h3>4. Verificación de Archivos Críticos</h3>";

$archivosCriticos = [
    'index.php (raíz)' => dirname(__DIR__, 2) . '/index.php',
    'dashboard.php' => dirname(__DIR__, 2) . '/dashboard.php',
    'logout.php' => dirname(__DIR__, 2) . '/logout.php',
    'public/index.php' => dirname(__DIR__, 2) . '/public/index.php',
    '.htaccess' => dirname(__DIR__, 2) . '/.htaccess',
    'vendor/autoload.php' => dirname(__DIR__, 2) . '/vendor/autoload.php',
    'app/Config/config.php' => dirname(__DIR__, 2) . '/app/Config/config.php',
    'app/Controllers/LoginController.php' => dirname(__DIR__, 2) . '/app/Controllers/LoginController.php'
];

foreach ($archivosCriticos as $nombre => $ruta) {
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        $permisos = substr(sprintf('%o', fileperms($ruta)), -4);
        echo "<p>✅ <strong>$nombre</strong> existe ($tamaño bytes, permisos: $permisos)</p>";
    } else {
        echo "<p>❌ <strong>$nombre</strong> NO existe en: $ruta</p>";
    }
}
echo "</div>";

// 5. Verificar permisos de directorios
echo "<div class='info'>";
echo "<h3>5. Verificación de Permisos de Directorios</h3>";

$directorios = [
    'Raíz del proyecto' => dirname(__DIR__, 2),
    'tests/Unit' => __DIR__,
    'public' => dirname(__DIR__, 2) . '/public',
    'app' => dirname(__DIR__, 2) . '/app',
    'vendor' => dirname(__DIR__, 2) . '/vendor'
];

foreach ($directorios as $nombre => $ruta) {
    if (is_dir($ruta)) {
        $permisos = substr(sprintf('%o', fileperms($ruta)), -4);
        $escribible = is_writable($ruta);
        $legible = is_readable($ruta);
        $status = ($escribible && $legible) ? '✅' : '⚠️';
        echo "<p>$status <strong>$nombre:</strong> Permisos: $permisos, Escritura: " . ($escribible ? 'SÍ' : 'NO') . ", Lectura: " . ($legible ? 'SÍ' : 'NO') . "</p>";
    } else {
        echo "<p>❌ <strong>$nombre:</strong> NO es un directorio</p>";
    }
}
echo "</div>";

// 6. Test de autoloader
echo "<div class='info'>";
echo "<h3>6. Test del Autoloader</h3>";

$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<p>✅ Autoloader encontrado</p>";
    
    try {
        require_once $autoloadPath;
        echo "<p>✅ Autoloader cargado correctamente</p>";
        
        // Verificar clases
        $clases = [
            'App\Controllers\LoginController',
            'App\Database\Database',
            'App\Services\LoggerService'
        ];
        
        foreach ($clases as $clase) {
            if (class_exists($clase)) {
                echo "<p>✅ Clase disponible: <code>$clase</code></p>";
            } else {
                echo "<p>❌ Clase NO disponible: <code>$clase</code></p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p class='critical'>❌ ERROR CRÍTICO al cargar autoloader: " . $e->getMessage() . "</p>";
        echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<p class='critical'>❌ ERROR CRÍTICO: Autoloader NO encontrado</p>";
}
echo "</div>";

// 7. Test de LoginController
echo "<div class='info'>";
echo "<h3>7. Test de LoginController</h3>";

try {
    if (class_exists('\App\Controllers\LoginController')) {
        $loginController = new \App\Controllers\LoginController();
        echo "<p>✅ LoginController instanciado correctamente</p>";
        
        // Verificar métodos críticos
        $metodosCriticos = ['authenticate', 'validateInput', 'findUser'];
        foreach ($metodosCriticos as $metodo) {
            if (method_exists($loginController, $metodo)) {
                echo "<p>✅ Método disponible: <code>$metodo</code></p>";
            } else {
                echo "<p>❌ Método NO disponible: <code>$metodo</code></p>";
            }
        }
        
    } else {
        echo "<p class='critical'>❌ ERROR CRÍTICO: LoginController no está disponible</p>";
    }
} catch (Exception $e) {
    echo "<p class='critical'>❌ ERROR CRÍTICO al instanciar LoginController: " . $e->getMessage() . "</p>";
    echo "<pre>Stack trace:\n" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// 8. Test de base de datos
echo "<div class='info'>";
echo "<h3>8. Test de Base de Datos</h3>";

try {
    if (class_exists('\App\Database\Database')) {
        $database = \App\Database\Database::getInstance();
        $connection = $database->getConnection();
        echo "<p>✅ Conexión a base de datos establecida</p>";
        
        // Test simple de consulta
        $stmt = $connection->query('SELECT 1 as test');
        $result = $stmt->fetch();
        if ($result && $result['test'] == 1) {
            echo "<p>✅ Consulta de prueba exitosa</p>";
        } else {
            echo "<p>⚠️ Consulta de prueba falló</p>";
        }
        
    } else {
        echo "<p class='critical'>❌ ERROR CRÍTICO: Clase Database no está disponible</p>";
    }
} catch (Exception $e) {
    echo "<p class='critical'>❌ ERROR CRÍTICO en base de datos: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 9. Análisis de errores
echo "<div class='info'>";
echo "<h3>9. Análisis de Errores del Servidor</h3>";

// Verificar logs de error
$logFiles = [
    'Error log del sistema' => '/var/log/apache2/error.log',
    'Error log de PHP' => '/var/log/php_errors.log',
    'Error log personalizado' => dirname(__DIR__, 2) . '/logs/error.log'
];

foreach ($logFiles as $nombre => $ruta) {
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        $ultimaModificacion = date('Y-m-d H:i:s', filemtime($ruta));
        echo "<p>✅ <strong>$nombre:</strong> Existe ($tamaño bytes, última modificación: $ultimaModificacion)</p>";
        
        // Mostrar últimas líneas del log si es pequeño
        if ($tamaño < 10000) {
            $contenido = file_get_contents($ruta);
            $lineas = explode("\n", $contenido);
            $ultimasLineas = array_slice($lineas, -5);
            echo "<pre>Últimas 5 líneas:\n" . implode("\n", $ultimasLineas) . "</pre>";
        }
    } else {
        echo "<p>⚠️ <strong>$nombre:</strong> No encontrado</p>";
    }
}
echo "</div>";

// 10. Resumen y recomendaciones
echo "<div class='success'>";
echo "<h3>🎯 Resumen del Diagnóstico</h3>";

$problemas = [];
$warnings = [];

// Verificar problemas críticos
if (!file_exists($autoloadPath)) {
    $problemas[] = "Autoloader no encontrado";
}

if (!class_exists('\App\Controllers\LoginController')) {
    $problemas[] = "LoginController no disponible";
}

if (!is_dir(dirname(__DIR__, 2) . '/vendor')) {
    $problemas[] = "Directorio vendor no existe";
}

if (!is_writable(dirname(__DIR__, 2) . '/logs')) {
    $warnings[] = "Directorio logs no es escribible";
}

if (empty($problemas)) {
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡SISTEMA FUNCIONANDO CORRECTAMENTE!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ PROBLEMAS CRÍTICOS DETECTADOS:</p>";
    foreach ($problemas as $problema) {
        echo "<p>• $problema</p>";
    }
}

if (!empty($warnings)) {
    echo "<p style='color: #856404; font-weight: bold;'>⚠️ ADVERTENCIAS:</p>";
    foreach ($warnings as $warning) {
        echo "<p>• $warning</p>";
    }
}
echo "</div>";

// 11. Enlaces útiles
echo "<div class='info'>";
echo "<h3>10. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='../index.php' class='btn btn-success'>🏠 Ir al Login</a>";
echo "<a href='TestSimple.php' class='btn'>🧪 Test Simple</a>";
echo "<a href='TestSistemaCompleto.php' class='btn'>🔍 Test Completo</a>";
echo "<a href='../dashboard.php' class='btn'>🎯 Test Dashboard</a>";
echo "</div>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🔍 Diagnóstico del servidor completado');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
