<?php
// Script de prueba simple para verificar el sistema
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test Simple del Sistema</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🧪 Test Simple del Sistema</h1>";
echo "<p>Este es un test básico para verificar que el sistema funcione correctamente.</p>";

// 1. Información básica del sistema
echo "<div class='info'>";
echo "<h3>1. Información del Sistema</h3>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>Directorio actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Directorio raíz del proyecto:</strong> " . dirname(__DIR__, 2) . "</p>";
echo "<p><strong>URL actual:</strong> " . $_SERVER['REQUEST_URI'] ?? 'N/A' . "</p>";
echo "<p><strong>Host:</strong> " . $_SERVER['HTTP_HOST'] ?? 'N/A' . "</p>";
echo "</div>";

// 2. Verificar archivos principales
echo "<div class='info'>";
echo "<h3>2. Verificando Archivos Principales</h3>";

$archivosPrincipales = [
    'index.php' => dirname(__DIR__, 2) . '/index.php',
    'dashboard.php' => dirname(__DIR__, 2) . '/dashboard.php',
    'logout.php' => dirname(__DIR__, 2) . '/logout.php',
    'vendor/autoload.php' => dirname(__DIR__, 2) . '/vendor/autoload.php'
];

foreach ($archivosPrincipales as $nombre => $ruta) {
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        echo "<p>✅ <strong>$nombre</strong> existe ($tamaño bytes)</p>";
    } else {
        echo "<p>❌ <strong>$nombre</strong> NO existe en: $ruta</p>";
    }
}
echo "</div>";

// 3. Verificar autoloader
echo "<div class='info'>";
echo "<h3>3. Verificando Autoloader</h3>";

$autoloadPath = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    echo "<p>✅ Autoloader encontrado</p>";
    
    try {
        require_once $autoloadPath;
        echo "<p>✅ Autoloader cargado correctamente</p>";
        
        // Verificar clases básicas
        $clases = [
            'App\Controllers\LoginController',
            'App\Database\Database'
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

// 4. Test básico de LoginController
echo "<div class='info'>";
echo "<h3>4. Test Básico de LoginController</h3>";

try {
    if (class_exists('\App\Controllers\LoginController')) {
        $loginController = new \App\Controllers\LoginController();
        echo "<p>✅ LoginController instanciado correctamente</p>";
        
        // Verificar métodos básicos
        $metodos = ['authenticate', 'validateInput'];
        foreach ($metodos as $metodo) {
            if (method_exists($loginController, $metodo)) {
                echo "<p>✅ Método disponible: <code>$metodo</code></p>";
            } else {
                echo "<p>❌ Método NO disponible: <code>$metodo</code></p>";
            }
        }
        
    } else {
        echo "<p>❌ LoginController no está disponible</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen del Test Simple</h3>";

$tests = [
    'Archivos principales existen' => file_exists(dirname(__DIR__, 2) . '/index.php'),
    'Autoloader funcional' => file_exists($autoloadPath),
    'LoginController disponible' => class_exists('\App\Controllers\LoginController')
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
    echo "<p style='color: #28a745; font-weight: bold;'>🎉 ¡SISTEMA BÁSICO FUNCIONANDO!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>⚠️ Algunas verificaciones fallaron.</p>";
}
echo "</div>";

// 6. Enlaces útiles
echo "<div class='info'>";
echo "<h3>5. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='../index.php' class='btn btn-success'>🏠 Ir al Login</a>";
echo "<a href='../dashboard.php' class='btn'>🎯 Test Dashboard</a>";
echo "<a href='TestSistemaCompleto.php' class='btn'>🧪 Test Completo</a>";
echo "<a href='TestSistemaRedireccion.php' class='btn'>🔍 Test Redirección</a>";
echo "</div>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🧪 Test simple del sistema finalizado');";
echo "console.log('✅ Verificaciones exitosas: $testsExitosos');";
echo "console.log('❌ Verificaciones fallidas: " . ($testsTotales - $testsExitosos) . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
