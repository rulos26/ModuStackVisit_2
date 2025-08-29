<?php
// Script de prueba para debug de consola JavaScript del LoginController
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔍 Debug Console - LoginController</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".debug-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196f3; }";
echo ".console-output { background: #263238; color: #fff; padding: 20px; border-radius: 5px; font-family: 'Courier New', monospace; margin: 20px 0; max-height: 400px; overflow-y: auto; }";
echo ".test-section { background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 15px 0; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-danger { background: #dc3545; }";
echo ".btn-danger:hover { background: #c82333; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔍 Debug Console - LoginController</h1>";
echo "<p>Este script prueba el sistema de debug de consola JavaScript del LoginController.</p>";

echo "<div class='debug-info'>";
echo "<h3>📋 Instrucciones:</h3>";
echo "<ol>";
echo "<li>Abre las herramientas de desarrollador del navegador (F12)</li>";
echo "<li>Ve a la pestaña 'Console'</li>";
echo "<li>Ejecuta las pruebas a continuación</li>";
echo "<li>Observa los mensajes de debug en la consola</li>";
echo "</ol>";
echo "</div>";

// 1. Cargar autoloader
echo "<div class='test-section'>";
echo "<h3>1. Cargando Autoloader</h3>";
$autoloadPath = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    echo "<p>✅ Autoloader cargado correctamente</p>";
} else {
    echo "<p>❌ Autoloader no encontrado</p>";
    exit;
}
echo "</div>";

// 2. Verificar clases
echo "<div class='test-section'>";
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
echo "</div>";

// 3. Instanciar LoginController
echo "<div class='test-section'>";
echo "<h3>3. Instanciando LoginController</h3>";
try {
    $loginController = new App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// 4. Pruebas de autenticación
echo "<div class='test-section'>";
echo "<h3>4. Pruebas de Autenticación con Debug de Consola</h3>";
echo "<p>Ejecuta las siguientes pruebas para ver el debug en la consola:</p>";

// Formulario para pruebas
echo "<form method='POST' style='margin: 20px 0;'>";
echo "<div style='margin: 10px 0;'>";
echo "<label><strong>Usuario:</strong></label><br>";
echo "<input type='text' name='usuario' value='root' style='padding: 8px; width: 200px; margin: 5px 0;'>";
echo "</div>";
echo "<div style='margin: 10px 0;'>";
echo "<label><strong>Contraseña:</strong></label><br>";
echo "<input type='password' name='password' value='root' style='padding: 8px; width: 200px; margin: 5px 0;'>";
echo "</div>";
echo "<button type='submit' name='test_login' class='btn btn-success'>🧪 Probar Login con Debug</button>";
echo "<button type='submit' name='test_wrong_password' class='btn btn-danger'>❌ Probar Contraseña Incorrecta</button>";
echo "<button type='submit' name='test_nonexistent_user' class='btn btn-danger'>👤 Probar Usuario Inexistente</button>";
echo "</form>";
echo "</div>";

// 5. Procesar pruebas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<div class='test-section'>";
    echo "<h3>5. Resultados de las Pruebas</h3>";
    
    if (isset($_POST['test_login'])) {
        $usuario = $_POST['usuario'] ?? 'root';
        $password = $_POST['password'] ?? 'root';
        
        echo "<div class='console-output'>";
        echo "<h4>🔍 Ejecutando prueba de login con debug de consola...</h4>";
        echo "<p>Usuario: $usuario</p>";
        echo "<p>Contraseña: " . str_repeat('*', strlen($password)) . "</p>";
        echo "<p>📋 Abre la consola del navegador (F12) para ver los mensajes de debug</p>";
        echo "</div>";
        
        // Ejecutar autenticación con debug
        $result = $loginController->authenticate($usuario, $password);
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Resultado de la Autenticación:</h4>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        echo "</div>";
        
    } elseif (isset($_POST['test_wrong_password'])) {
        echo "<div class='console-output'>";
        echo "<h4>🔍 Ejecutando prueba con contraseña incorrecta...</h4>";
        echo "<p>📋 Abre la consola del navegador (F12) para ver los mensajes de debug</p>";
        echo "</div>";
        
        $result = $loginController->authenticate('root', 'password_incorrecta');
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Resultado de la Autenticación:</h4>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        echo "</div>";
        
    } elseif (isset($_POST['test_nonexistent_user'])) {
        echo "<div class='console-output'>";
        echo "<h4>🔍 Ejecutando prueba con usuario inexistente...</h4>";
        echo "<p>📋 Abre la consola del navegador (F12) para ver los mensajes de debug</p>";
        echo "</div>";
        
        $result = $loginController->authenticate('usuario_inexistente', 'password');
        
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>Resultado de la Autenticación:</h4>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        echo "</div>";
    }
    echo "</div>";
}

// 6. Información sobre el debug
echo "<div class='test-section'>";
echo "<h3>6. Información sobre el Debug de Consola</h3>";
echo "<div class='debug-info'>";
echo "<h4>🎯 Características del Debug:</h4>";
echo "<ul>";
echo "<li><strong>Grupos de Console:</strong> Cada paso del proceso se agrupa en la consola</li>";
echo "<li><strong>Timestamps:</strong> Cada mensaje incluye la fecha y hora exacta</li>";
echo "<li><strong>Datos Estructurados:</strong> Información detallada en formato JSON</li>";
echo "<li><strong>Stack Trace:</strong> Rastreo completo de la ejecución</li>";
echo "<li><strong>Emojis:</strong> Iconos para identificar rápidamente cada tipo de evento</li>";
echo "</ul>";
echo "</div>";

echo "<div class='debug-info'>";
echo "<h4>🔍 Tipos de Mensajes de Debug:</h4>";
echo "<ul>";
echo "<li><strong>🚀 INICIO AUTENTICACIÓN:</strong> Comienzo del proceso</li>";
echo "<li><strong>🔍 VALIDANDO ENTRADA:</strong> Validación de datos de entrada</li>";
echo "<li><strong>🔒 VERIFICANDO RATE LIMITING:</strong> Verificación de bloqueos</li>";
echo "<li><strong>🔍 BUSCANDO USUARIO EN BD:</strong> Búsqueda en base de datos</li>";
echo "<li><strong>🔐 VERIFICANDO CONTRASEÑA:</strong> Verificación de credenciales</li>";
echo "<li><strong>👤 VERIFICANDO ESTADO ACTIVO:</strong> Verificación de estado del usuario</li>";
echo "<li><strong>🔑 CREANDO SESIÓN:</strong> Creación de sesión de usuario</li>";
echo "<li><strong>🎉 AUTENTICACIÓN EXITOSA:</strong> Login exitoso</li>";
echo "<li><strong>❌ ERRORES:</strong> Diferentes tipos de errores</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

// 7. Enlaces útiles
echo "<div class='test-section'>";
echo "<h3>7. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='DesbloquearUsuarioRoot.php' class='btn btn-danger'>🔓 Desbloquear Usuario</a>";
echo "<a href='TestLoginControllerCorregido.php' class='btn'>🧪 Test LoginController</a>";
echo "<a href='TestLoginConDebug.php' class='btn'>⚡ Test con Debug</a>";
echo "<a href='VerLogsDebug.php' class='btn'>📋 Ver Logs</a>";
echo "</div>";
echo "</div>";

echo "<div class='debug-info'>";
echo "<h3>🎯 Resumen</h3>";
echo "<p>Este script permite probar el sistema de debug de consola JavaScript del LoginController. </p>";
echo "<p><strong>Para ver los resultados:</strong></p>";
echo "<ol>";
echo "<li>Ejecuta una de las pruebas de autenticación</li>";
echo "<li>Abre las herramientas de desarrollador (F12)</li>";
echo "<li>Ve a la pestaña 'Console'</li>";
echo "<li>Observa los mensajes de debug agrupados y detallados</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🔍 Debug Console - LoginController cargado');";
echo "console.log('📋 Abre las herramientas de desarrollador (F12) para ver los mensajes de debug');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
