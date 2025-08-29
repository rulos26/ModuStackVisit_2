<?php
// Script para probar login después de corregir hashes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🧪 Test Login Después de Corrección</title>";
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
echo ".btn-warning { background: #ffc107; color: #212529; }";
echo ".btn-warning:hover { background: #e0a800; }";
echo ".test-result { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🧪 Test Login Después de Corrección de Hashes</h1>";
echo "<p>Este script prueba el login con todos los usuarios predeterminados después de corregir los hashes.</p>";

// 1. Cargar autoloader
echo "<div class='info'>";
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

// 2. Instanciar LoginController
echo "<div class='info'>";
echo "<h3>2. Instanciando LoginController</h3>";
try {
    $loginController = new App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// 3. Definir usuarios para probar
$usuariosParaProbar = [
    [
        'usuario' => 'root',
        'password' => 'root',
        'rol' => 3,
        'descripcion' => 'Superadministrador'
    ],
    [
        'usuario' => 'admin',
        'password' => 'admin',
        'rol' => 1,
        'descripcion' => 'Administrador'
    ],
    [
        'usuario' => 'cliente',
        'password' => 'cliente',
        'rol' => 2,
        'descripcion' => 'Cliente/Evaluador'
    ]
];

// 4. Probar login con cada usuario
echo "<div class='info'>";
echo "<h3>3. Probando Login con Cada Usuario</h3>";

$loginsExitosos = 0;
$loginsFallidos = 0;

foreach ($usuariosParaProbar as $userInfo) {
    echo "<div class='test-result'>";
    echo "<h4>🔐 Probando Login: " . $userInfo['usuario'] . " (" . $userInfo['descripcion'] . ")</h4>";
    
    try {
        $startTime = microtime(true);
        $result = $loginController->authenticate($userInfo['usuario'], $userInfo['password']);
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        echo "<p><strong>Tiempo de ejecución:</strong> {$executionTime}ms</p>";
        
        if ($result['success']) {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ LOGIN EXITOSO</p>";
            echo "<p><strong>Usuario:</strong> " . $userInfo['usuario'] . "</p>";
            echo "<p><strong>Rol:</strong> " . $result['data']['rol'] . " - " . $userInfo['descripcion'] . "</p>";
            echo "<p><strong>Redirect URL:</strong> " . $result['data']['redirect_url'] . "</p>";
            echo "<p><strong>Session Token:</strong> " . substr($result['data']['session_token'], 0, 10) . "...</p>";
            $loginsExitosos++;
        } else {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ LOGIN FALLIDO</p>";
            echo "<p><strong>Error:</strong> " . $result['message'] . "</p>";
            echo "<p><strong>Código:</strong> " . $result['error_code'] . "</p>";
            $loginsFallidos++;
        }
        
    } catch (Exception $e) {
        echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR EN LOGIN</p>";
        echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        $loginsFallidos++;
    }
    
    echo "</div>";
}

echo "</div>";

// 5. Probar casos de error
echo "<div class='info'>";
echo "<h3>4. Probando Casos de Error</h3>";

$casosError = [
    [
        'usuario' => 'usuario_inexistente',
        'password' => 'password123',
        'descripcion' => 'Usuario inexistente'
    ],
    [
        'usuario' => 'root',
        'password' => 'password_incorrecta',
        'descripcion' => 'Contraseña incorrecta'
    ],
    [
        'usuario' => '',
        'password' => '',
        'descripcion' => 'Credenciales vacías'
    ]
];

foreach ($casosError as $caso) {
    echo "<div class='test-result'>";
    echo "<h4>🚫 Probando Error: " . $caso['descripcion'] . "</h4>";
    
    try {
        $result = $loginController->authenticate($caso['usuario'], $caso['password']);
        
        if (!$result['success']) {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ ERROR MANEJADO CORRECTAMENTE</p>";
            echo "<p><strong>Error esperado:</strong> " . $result['message'] . "</p>";
            echo "<p><strong>Código:</strong> " . $result['error_code'] . "</p>";
        } else {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR: Se aceptó credenciales inválidas</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: #dc3545; font-weight: bold;'>❌ ERROR INESPERADO</p>";
        echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
    
    echo "</div>";
}

echo "</div>";

// 6. Verificar hashes en base de datos
echo "<div class='info'>";
echo "<h3>5. Verificación Final de Hashes</h3>";

try {
    $db = App\Database\Database::getInstance()->getConnection();
    
    foreach ($usuariosParaProbar as $userInfo) {
        $stmt = $db->prepare('SELECT id, password, LENGTH(password) as hash_length FROM usuarios WHERE usuario = :usuario');
        $stmt->bindParam(':usuario', $userInfo['usuario']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $hashValido = password_verify($userInfo['password'], $user['password']);
            
            echo "<div class='test-result'>";
            echo "<h4>🔍 Verificación Hash: " . $userInfo['usuario'] . "</h4>";
            echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Hash:</strong> " . substr($user['password'], 0, 20) . "...</p>";
            echo "<p><strong>Longitud:</strong> " . $user['hash_length'] . " caracteres</p>";
            echo "<p><strong>Estado:</strong> " . ($hashValido ? '✅ VÁLIDO' : '❌ INVÁLIDO') . "</p>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error verificando hashes: " . $e->getMessage() . "</p>";
}

echo "</div>";

// 7. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen de Pruebas de Login</h3>";
echo "<p><strong>Usuarios probados:</strong> " . count($usuariosParaProbar) . "</p>";
echo "<p><strong>Logins exitosos:</strong> " . $loginsExitosos . "</p>";
echo "<p><strong>Logins fallidos:</strong> " . $loginsFallidos . "</p>";
echo "<p><strong>Casos de error probados:</strong> " . count($casosError) . "</p>";

if ($loginsExitosos === count($usuariosParaProbar)) {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ ¡TODOS LOS LOGINS FUNCIONAN CORRECTAMENTE!</p>";
    echo "<p style='color: #28a745; font-weight: bold;'>✅ Los hashes han sido corregidos exitosamente</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Algunos logins aún fallan - revisar hashes</p>";
}
echo "</div>";

// 8. Enlaces útiles
echo "<div class='info'>";
echo "<h3>6. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='CorregirTodosLosHashes.php' class='btn btn-warning'>🔧 Corregir Hashes</a>";
echo "<a href='CrearUsuariosPredeterminados.php' class='btn btn-success'>👥 Crear Usuarios</a>";
echo "<a href='TestCorreccionBindParam.php' class='btn'>🧪 Test BindParam</a>";
echo "<a href='TestLoginControllerDebugConsole.php' class='btn'>🔍 Debug Console</a>";
echo "</div>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>📋 Instrucciones de Uso</h3>";
echo "<ol>";
echo "<li>Este script prueba el login con todos los usuarios predeterminados</li>";
echo "<li>Verifica que los hashes sean válidos y funcionen correctamente</li>";
echo "<li>Prueba casos de error para asegurar seguridad</li>";
echo "<li>Proporciona un resumen completo de las pruebas</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🧪 Test de login completado');";
echo "console.log('✅ Logins exitosos: " . $loginsExitosos . "');";
echo "console.log('❌ Logins fallidos: " . $loginsFallidos . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
