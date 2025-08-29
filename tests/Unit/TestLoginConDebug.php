<?php
// Script de prueba específico para LoginController con debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 PRUEBA ESPECÍFICA DEL LOGINCONTROLLER CON DEBUG</h2>";
echo "<hr>";

// Función para mostrar logs de debug
function showDebugLogs() {
    $debugFile = __DIR__ . '/../../logs/debug.log';
    if (file_exists($debugFile)) {
        echo "<h4>📋 Logs de Debug:</h4>";
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo "<pre style='margin: 0; font-family: monospace; font-size: 11px;'>";
        $lines = file($debugFile);
        $lastLines = array_slice($lines, -20); // Últimas 20 líneas
        foreach ($lastLines as $line) {
            $line = htmlspecialchars($line);
            if (strpos($line, 'ERROR') !== false) {
                echo "<span style='color: #dc3545; font-weight: bold;'>$line</span>";
            } elseif (strpos($line, 'AUTENTICACIÓN EXITOSA') !== false) {
                echo "<span style='color: #28a745; font-weight: bold;'>$line</span>";
            } elseif (strpos($line, 'INICIO AUTENTICACIÓN') !== false) {
                echo "<span style='color: #007bff; font-weight: bold;'>$line</span>";
            } else {
                echo $line;
            }
        }
        echo "</pre>";
        echo "</div>";
    } else {
        echo "<p>❌ No hay logs de debug disponibles</p>";
    }
}

// 1. Cargar autoloader
echo "<h3>1. Cargando Autoloader</h3>";
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    echo "<p>✅ Autoloader cargado correctamente</p>";
} else {
    echo "<p>❌ Autoloader no encontrado</p>";
    exit;
}

// 2. Verificar clases
echo "<h3>2. Verificando Clases</h3>";
$classes = [
    'App\Database\Database',
    'App\Services\LoggerService',
    'App\Controllers\LoginController'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "<p>✅ $class existe</p>";
    } else {
        echo "<p>❌ $class no existe</p>";
    }
}

// 3. Probar instanciación
echo "<h3>3. Instanciando Clases</h3>";
try {
    $db = App\Database\Database::getInstance();
    echo "<p>✅ Database instanciado</p>";
    
    $logger = new App\Services\LoggerService();
    echo "<p>✅ LoggerService instanciado</p>";
    
    $loginController = new App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar: " . $e->getMessage() . "</p>";
    exit;
}

// 4. Probar autenticación
echo "<h3>4. Prueba de Autenticación</h3>";

// Limpiar logs antes de la prueba
$debugFile = __DIR__ . '/../../logs/debug.log';
if (file_exists($debugFile)) {
    file_put_contents($debugFile, '');
}

echo "<p><strong>Probando login con usuario 'root' y contraseña 'root'...</strong></p>";

try {
    $result = $loginController->authenticate('root', 'root');
    
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    print_r($result);
    echo "</pre>";
    
    if (is_array($result)) {
        if ($result['success']) {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ AUTENTICACIÓN EXITOSA</p>";
            echo "<p><strong>Usuario:</strong> " . $result['data']['username'] . "</p>";
            echo "<p><strong>Rol:</strong> " . $result['data']['rol'] . "</p>";
            echo "<p><strong>Redirección:</strong> " . $result['data']['redirect_url'] . "</p>";
        } else {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ AUTENTICACIÓN FALLIDA</p>";
            echo "<p><strong>Error:</strong> " . $result['message'] . "</p>";
            echo "<p><strong>Código:</strong> " . $result['error_code'] . "</p>";
        }
    } else {
        echo "<p style='color: #fd7e14; font-weight: bold;'>⚠️ RESULTADO INESPERADO</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR EN AUTENTICACIÓN</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
}

// 5. Mostrar logs de debug
echo "<h3>5. Logs de Debug Generados</h3>";
showDebugLogs();

// 6. Probar casos adicionales
echo "<h3>6. Pruebas Adicionales</h3>";

// Probar con credenciales incorrectas
echo "<h4>Prueba con credenciales incorrectas:</h4>";
try {
    $result = $loginController->authenticate('root', 'password_incorrecta');
    echo "<p><strong>Resultado:</strong> " . ($result['success'] ? 'Éxito' : 'Falló') . "</p>";
    if (!$result['success']) {
        echo "<p><strong>Mensaje:</strong> " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Probar con usuario inexistente
echo "<h4>Prueba con usuario inexistente:</h4>";
try {
    $result = $loginController->authenticate('usuario_inexistente', 'password');
    echo "<p><strong>Resultado:</strong> " . ($result['success'] ? 'Éxito' : 'Falló') . "</p>";
    if (!$result['success']) {
        echo "<p><strong>Mensaje:</strong> " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Mostrar logs finales
echo "<h3>7. Logs Finales</h3>";
showDebugLogs();

echo "<hr>";
echo "<h3>🎯 RESUMEN</h3>";
echo "<p>Este script ha probado:</p>";
echo "<ul>";
echo "<li>✅ Carga de autoloader y clases</li>";
echo "<li>✅ Instanciación de componentes</li>";
echo "<li>✅ Autenticación con credenciales correctas</li>";
echo "<li>✅ Manejo de credenciales incorrectas</li>";
echo "<li>✅ Manejo de usuarios inexistentes</li>";
echo "<li>✅ Generación de logs de debug</li>";
echo "</ul>";

echo "<h3>🔗 Enlaces Útiles</h3>";
echo "<p>";
echo "<a href='VerLogsDebug.php' style='margin-right: 10px;'>🔍 Ver Logs Debug</a>";
echo "<a href='DiagnosticoCompleto.php' style='margin-right: 10px;'>🔍 Diagnóstico Completo</a>";
echo "<a href='TestBasico.php' style='margin-right: 10px;'>🧪 Prueba Básica</a>";
echo "<a href='CorregirHashUsuario.php'>🔧 Corregir Hash</a>";
echo "</p>";
?>
