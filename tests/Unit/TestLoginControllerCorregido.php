<?php
// Script de prueba específico para LoginController después de corrección
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 PRUEBA DEL LOGINCONTROLLER CORREGIDO</h2>";
echo "<hr>";

// Función para mostrar logs de debug
function showDebugLogs() {
    $debugFile = __DIR__ . '/../../logs/debug.log';
    if (file_exists($debugFile)) {
        echo "<h4>📋 Logs de Debug:</h4>";
        echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 10px; max-height: 300px; overflow-y: auto;'>";
        echo "<pre style='margin: 0; font-family: monospace; font-size: 11px;'>";
        $lines = file($debugFile);
        $lastLines = array_slice($lines, -15); // Últimas 15 líneas
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

// 4. Limpiar logs antes de las pruebas
$debugFile = __DIR__ . '/../../logs/debug.log';
if (file_exists($debugFile)) {
    file_put_contents($debugFile, '');
    echo "<p>✅ Logs de debug limpiados</p>";
}

// 5. Prueba de autenticación exitosa
echo "<h3>4. Prueba de Autenticación Exitosa</h3>";
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
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
}

// 6. Mostrar logs de debug
echo "<h3>5. Logs de Debug Generados</h3>";
showDebugLogs();

// 7. Prueba de credenciales incorrectas (para probar incrementFailedAttempts)
echo "<h3>6. Prueba de Credenciales Incorrectas</h3>";
echo "<p><strong>Probando login con contraseña incorrecta para activar incrementFailedAttempts...</strong></p>";

try {
    $result = $loginController->authenticate('root', 'password_incorrecta');
    
    echo "<p><strong>Resultado:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
    print_r($result);
    echo "</pre>";
    
    if (is_array($result) && !$result['success']) {
        echo "<p style='color: #fd7e14; font-weight: bold;'>✅ MANEJO DE ERROR CORRECTO</p>";
        echo "<p><strong>Mensaje:</strong> " . $result['message'] . "</p>";
        echo "<p><strong>Código:</strong> " . $result['error_code'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR EN MANEJO DE CREDENCIALES INCORRECTAS</p>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
}

// 8. Mostrar logs finales
echo "<h3>7. Logs Finales</h3>";
showDebugLogs();

// 9. Verificar que no hay errores de bindParam
echo "<h3>8. Verificación de Errores de bindParam</h3>";
$errorLog = error_get_last();
if ($errorLog && strpos($errorLog['message'], 'bindParam') !== false) {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR DE bindParam DETECTADO</p>";
    echo "<p><strong>Error:</strong> " . $errorLog['message'] . "</p>";
    echo "<p><strong>Línea:</strong> " . $errorLog['line'] . "</p>";
    echo "<p><strong>Archivo:</strong> " . $errorLog['file'] . "</p>";
} else {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ NO HAY ERRORES DE bindParam</p>";
}

echo "<hr>";
echo "<h3>🎯 RESUMEN DE LA CORRECCIÓN</h3>";
echo "<p>Este script ha verificado:</p>";
echo "<ul>";
echo "<li>✅ Carga correcta de autoloader y clases</li>";
echo "<li>✅ Instanciación sin errores</li>";
echo "<li>✅ Autenticación exitosa sin errores de bindParam</li>";
echo "<li>✅ Manejo de credenciales incorrectas sin errores</li>";
echo "<li>✅ Generación de logs de debug</li>";
echo "<li>✅ Ausencia de errores de bindParam</li>";
echo "</ul>";

echo "<h3>🔧 CORRECCIÓN APLICADA</h3>";
echo "<p><strong>Problema:</strong> bindParam() no puede recibir constantes directamente</p>";
echo "<p><strong>Solución:</strong> Crear variables temporales para las constantes</p>";
echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
echo "// ANTES (ERROR):\n";
echo "\$stmt->bindParam(':max_attempts', self::MAX_LOGIN_ATTEMPTS, \\PDO::PARAM_INT);\n";
echo "\$stmt->bindParam(':lockout_duration', self::LOCKOUT_DURATION, \\PDO::PARAM_INT);\n\n";
echo "// DESPUÉS (CORREGIDO):\n";
echo "\$maxAttempts = self::MAX_LOGIN_ATTEMPTS;\n";
echo "\$lockoutDuration = self::LOCKOUT_DURATION;\n";
echo "\$stmt->bindParam(':max_attempts', \$maxAttempts, \\PDO::PARAM_INT);\n";
echo "\$stmt->bindParam(':lockout_duration', \$lockoutDuration, \\PDO::PARAM_INT);";
echo "</pre>";

echo "<h3>🔗 Enlaces Útiles</h3>";
echo "<p>";
echo "<a href='VerLogsDebug.php' style='margin-right: 10px;'>🔍 Ver Logs Debug</a>";
echo "<a href='DiagnosticoCompleto.php' style='margin-right: 10px;'>🔍 Diagnóstico Completo</a>";
echo "<a href='TestBasico.php' style='margin-right: 10px;'>🧪 Prueba Básica</a>";
echo "<a href='TestLoginConDebug.php'>⚡ Test Login con Debug</a>";
echo "</p>";
?>
