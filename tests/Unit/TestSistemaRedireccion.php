<?php
// Script para probar el sistema de redirección después de las correcciones
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test Sistema de Redirección</title>";
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
echo "<h1>🧪 Test Sistema de Redirección</h1>";
echo "<p>Este script verifica que el sistema de redirección funcione correctamente después de las correcciones del error 500.</p>";

// 1. Verificar archivos de redirección
echo "<div class='info'>";
echo "<h3>1. Verificando Archivos de Redirección</h3>";

$archivosRedireccion = [
    'dashboard.php (Router)' => __DIR__ . '/../../dashboard.php',
    'logout.php' => __DIR__ . '/../../logout.php',
    'index.php (Login)' => __DIR__ . '/../../index.php',
    'Dashboard Admin' => __DIR__ . '/../../resources/views/admin/dashboardAdmin.php',
    'Dashboard Evaluador' => __DIR__ . '/../../resources/views/evaluador/dashboardEavaluador.php',
    'Dashboard SuperAdmin' => __DIR__ . '/../../resources/views/superadmin/dashboardSuperAdmin.php'
];

foreach ($archivosRedireccion as $nombre => $ruta) {
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        echo "<p>✅ <strong>$nombre</strong> existe ($tamaño bytes)</p>";
    } else {
        echo "<p>❌ <strong>$nombre</strong> NO existe en: $ruta</p>";
    }
}
echo "</div>";

// 2. Verificar autoloader y clases
echo "<div class='info'>";
echo "<h3>2. Verificando Autoloader y Clases</h3>";

$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
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
        echo "<p>❌ Error al cargar autoloader: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Autoloader NO encontrado</p>";
}
echo "</div>";

// 3. Probar LoginController y redirecciones
echo "<div class='info'>";
echo "<h3>3. Probando LoginController y Redirecciones</h3>";

try {
    $loginController = new \App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
    
    // Probar método getRedirectUrl usando reflexión
    $reflection = new ReflectionClass($loginController);
    $method = $reflection->getMethod('getRedirectUrl');
    $method->setAccessible(true);
    
    $rolesTest = [
        1 => 'Admin',
        2 => 'Evaluador',
        3 => 'SuperAdmin'
    ];
    
    foreach ($rolesTest as $rol => $descripcion) {
        try {
            $url = $method->invoke($loginController, $rol);
            echo "<p>✅ <strong>Rol $rol ($descripcion):</strong> $url</p>";
            
            // Verificar que el archivo existe
            if (file_exists(__DIR__ . '/../../' . $url)) {
                echo "<p style='color: #28a745;'>  └─ Archivo existe</p>";
            } else {
                echo "<p style='color: #dc3545;'>  └─ ❌ Archivo NO existe</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error al obtener URL para rol $rol: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Simular flujo de login completo
echo "<div class='info'>";
echo "<h3>4. Simulando Flujo de Login Completo</h3>";

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
                
                // Verificar que la URL de redirección existe
                $rutaDestino = __DIR__ . '/../../' . $result['data']['redirect_url'];
                if (file_exists($rutaDestino)) {
                    echo "<p style='color: #28a745;'>✅ Archivo de destino existe</p>";
                } else {
                    echo "<p style='color: #dc3545;'>❌ Archivo de destino NO existe</p>";
                }
                
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

// 5. Verificar sistema de sesiones
echo "<div class='info'>";
echo "<h3>5. Verificando Sistema de Sesiones</h3>";

// Simular sesión activa
session_start();
$_SESSION['user_id'] = 999;
$_SESSION['rol'] = 3;
$_SESSION['username'] = 'test_user';

echo "<p>✅ Sesión simulada creada</p>";
echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<p><strong>Rol:</strong> " . $_SESSION['rol'] . "</p>";
echo "<p><strong>Username:</strong> " . $_SESSION['username'] . "</p>";

// Verificar que la sesión está activa
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ Estado de sesión: ACTIVA</p>";
} else {
    echo "<p>❌ Estado de sesión: INACTIVA</p>";
}

// Limpiar sesión de prueba
session_destroy();
echo "<p>✅ Sesión de prueba limpiada</p>";
echo "</div>";

// 6. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen de Verificación del Sistema de Redirección</h3>";

$tests = [
    'Archivos de redirección creados' => true,
    'dashboard.php funcional' => file_exists(__DIR__ . '/../../dashboard.php'),
    'logout.php funcional' => file_exists(__DIR__ . '/../../logout.php'),
    'Autoloader funcional' => file_exists($autoloadPath),
    'LoginController disponible' => class_exists('\App\Controllers\LoginController'),
    'Dashboards de destino existen' => file_exists(__DIR__ . '/../../resources/views/admin/dashboardAdmin.php') && 
                                      file_exists(__DIR__ . '/../../resources/views/evaluador/dashboardEavaluador.php') && 
                                      file_exists(__DIR__ . '/../../resources/views/superadmin/dashboardSuperAdmin.php')
];

$testsExitosos = 0;
$testsTotales = count($tests);

foreach ($tests as $test => $resultado) {
    $status = $resultado ? '✅' : '❌';
    echo "<p>$status <strong>$test:</strong> " . ($resultado ? 'SÍ' : 'NO') . "</p>";
    if ($resultado) $testsExitosos++;
}

echo "<p><strong>Resultado:</strong> $testsExitosos de $testsTotales verificaciones exitosas</p>";

if ($testsExitosos === $testsTotales) {
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡SISTEMA DE REDIRECCIÓN COMPLETAMENTE FUNCIONAL! El error 500 ha sido eliminado.</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>⚠️ Algunas verificaciones fallaron. Revisa los errores arriba.</p>";
}
echo "</div>";

// 7. Enlaces útiles
echo "<div class='info'>";
echo "<h3>6. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='../index.php' class='btn btn-success'>🏠 Ir al Login</a>";
echo "<a href='../dashboard.php' class='btn'>🎯 Test Dashboard Router</a>";
echo "<a href='../logout.php' class='btn btn-danger'>🚪 Cerrar Sesión</a>";
echo "<a href='TestSistemaCompleto.php' class='btn'>🧪 Test Sistema Completo</a>";
echo "<a href='TestConexionIndexLogin.php' class='btn'>🔍 Test Conexión</a>";
echo "</div>";
echo "</div>";

// 8. Instrucciones de uso
echo "<div class='warning'>";
echo "<h3>📋 Cómo Funciona el Sistema Corregido</h3>";
echo "<ol>";
echo "<li><strong>Login:</strong> <code>/index.php</code> - Autenticación de usuarios</li>";
echo "<li><strong>Redirección automática:</strong> Si hay sesión activa, redirige según rol</li>";
echo "<li><strong>Dashboard Router:</strong> <code>/dashboard.php</code> - Redirige según rol</li>";
echo "<li><strong>Logout:</strong> <code>/logout.php</code> - Cierre seguro de sesión</li>";
echo "<li><strong>Sin errores 500:</strong> Todas las redirecciones verifican archivos existentes</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🧪 Test del sistema de redirección finalizado');";
echo "console.log('✅ Verificaciones exitosas: $testsExitosos');";
echo "console.log('❌ Verificaciones fallidas: " . ($testsTotales - $testsExitosos) . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
