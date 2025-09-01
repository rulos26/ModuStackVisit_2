<?php
// Script para probar la conexión entre index.php y LoginController
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test Conexión Index-LoginController</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }";
echo ".warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo ".test-result { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🧪 Test Conexión Index-LoginController</h1>";
echo "<p>Este script verifica que la conexión entre index.php y LoginController funcione correctamente.</p>";

// 1. Verificar autoloader
echo "<div class='info'>";
echo "<h3>1. Verificando Autoloader de Composer</h3>";

$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<p>✅ Autoloader encontrado en: $autoloadPath</p>";
    
    try {
        require_once $autoloadPath;
        echo "<p>✅ Autoloader cargado correctamente</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error al cargar autoloader: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Autoloader NO encontrado en: $autoloadPath</p>";
    echo "<p>💡 Ejecuta: <code>composer install</code> en la terminal</p>";
}
echo "</div>";

// 2. Verificar clases disponibles
echo "<div class='info'>";
echo "<h3>2. Verificando Clases Disponibles</h3>";

$clasesRequeridas = [
    'App\Controllers\LoginController',
    'App\Database\Database',
    'App\Services\LoggerService'
];

foreach ($clasesRequeridas as $clase) {
    if (class_exists($clase)) {
        echo "<p>✅ Clase disponible: <code>$clase</code></p>";
    } else {
        echo "<p>❌ Clase NO disponible: <code>$clase</code></p>";
    }
}
echo "</div>";

// 3. Probar instanciación de LoginController
echo "<div class='info'>";
echo "<h3>3. Probando Instanciación de LoginController</h3>";

try {
    $loginController = new \App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
    
    // Verificar métodos disponibles
    $metodosRequeridos = ['authenticate', 'validateInput', 'findUser', 'verifyPassword'];
    foreach ($metodosRequeridos as $metodo) {
        if (method_exists($loginController, $metodo)) {
            echo "<p>✅ Método disponible: <code>$metodo</code></p>";
        } else {
            echo "<p>❌ Método NO disponible: <code>$metodo</code></p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// 4. Probar método authenticate
echo "<div class='info'>";
echo "<h3>4. Probando Método Authenticate</h3>";

try {
    if (isset($loginController)) {
        // Probar con credenciales de prueba
        $result = $loginController->authenticate('test_user', 'test_password');
        
        if (is_array($result)) {
            echo "<p>✅ Método authenticate ejecutado correctamente</p>";
            echo "<p><strong>Resultado:</strong></p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
        } else {
            echo "<p>⚠️ Método authenticate retornó un tipo inesperado: " . gettype($result) . "</p>";
        }
    } else {
        echo "<p>❌ LoginController no disponible para probar authenticate</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al probar authenticate: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div>";

// 5. Verificar archivos de vista
echo "<div class='info'>";
echo "<h3>5. Verificando Archivos de Vista</h3>";

$archivosVista = [
    'index.php' => __DIR__ . '/../../index.php',
    'LoginController.php' => __DIR__ . '/../../app/Controllers/LoginController.php',
    'Database.php' => __DIR__ . '/../../app/Database/Database.php',
    'LoggerService.php' => __DIR__ . '/../../app/Services/LoggerService.php'
];

foreach ($archivosVista as $nombre => $ruta) {
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        echo "<p>✅ <strong>$nombre</strong> existe ($tamaño bytes)</p>";
    } else {
        echo "<p>❌ <strong>$nombre</strong> NO existe en: $ruta</p>";
    }
}
echo "</div>";

// 6. Simular flujo de login
echo "<div class='info'>";
echo "<h3>6. Simulando Flujo de Login</h3>";

try {
    if (isset($loginController)) {
        echo "<p>🔐 Simulando login con usuario 'root' y contraseña 'root'...</p>";
        
        $startTime = microtime(true);
        $result = $loginController->authenticate('root', 'root');
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        echo "<p><strong>Tiempo de ejecución:</strong> {$executionTime}ms</p>";
        
        if (is_array($result)) {
            if ($result['success']) {
                echo "<p style='color: #28a745; font-weight: bold;'>✅ LOGIN EXITOSO</p>";
                echo "<p><strong>Usuario:</strong> " . ($result['data']['username'] ?? 'N/A') . "</p>";
                echo "<p><strong>Rol:</strong> " . ($result['data']['rol'] ?? 'N/A') . "</p>";
                echo "<p><strong>Redirect URL:</strong> " . ($result['data']['redirect_url'] ?? 'N/A') . "</p>";
            } else {
                echo "<p style='color: #dc3545; font-weight: bold;'>❌ LOGIN FALLIDO</p>";
                echo "<p><strong>Error:</strong> " . ($result['message'] ?? 'N/A') . "</p>";
                echo "<p><strong>Código:</strong> " . ($result['error_code'] ?? 'N/A') . "</p>";
            }
        } else {
            echo "<p>⚠️ Resultado inesperado: " . gettype($result) . "</p>";
        }
    } else {
        echo "<p>❌ No se puede simular login - LoginController no disponible</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en simulación de login: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 7. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen de Pruebas</h3>";

$tests = [
    'Autoloader cargado' => file_exists($autoloadPath),
    'LoginController disponible' => class_exists('\App\Controllers\LoginController'),
    'Database disponible' => class_exists('\App\Database\Database'),
    'LoggerService disponible' => class_exists('\App\Services\LoggerService'),
    'index.php existe' => file_exists(__DIR__ . '/../../index.php'),
    'LoginController.php existe' => file_exists(__DIR__ . '/../../app/Controllers/LoginController.php')
];

$testsExitosos = 0;
$testsTotales = count($tests);

foreach ($tests as $test => $resultado) {
    $status = $resultado ? '✅' : '❌';
    echo "<p>$status <strong>$test:</strong> " . ($resultado ? 'SÍ' : 'NO') . "</p>";
    if ($resultado) $testsExitosos++;
}

echo "<p><strong>Resultado:</strong> $testsExitosos de $testsTotales pruebas exitosas</p>";

if ($testsExitosos === $testsTotales) {
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡TODAS LAS PRUEBAS EXITOSAS! La conexión está funcionando correctamente.</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>⚠️ Algunas pruebas fallaron. Revisa los errores arriba.</p>";
}
echo "</div>";

// 8. Enlaces útiles
echo "<div class='info'>";
echo "<h3>7. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='../index.php' class='btn btn-success'>🏠 Ir al Login</a>";
echo "<a href='VerificarRedireccionesPorRol.php' class='btn'>🔍 Verificar Redirecciones</a>";
echo "<a href='TestLoginDespuesCorreccion.php' class='btn'>🧪 Test Login</a>";
echo "<a href='CorregirTodosLosHashes.php' class='btn btn-warning'>🔧 Corregir Hashes</a>";
echo "</div>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🧪 Test de conexión completado');";
echo "console.log('✅ Tests exitosos: $testsExitosos');";
echo "console.log('❌ Tests fallidos: " . ($testsTotales - $testsExitosos) . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
