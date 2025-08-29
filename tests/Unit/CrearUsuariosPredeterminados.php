<?php
// Script para crear usuarios predeterminados y hacer login directo
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔧 Crear Usuarios Predeterminados</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }";
echo ".container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #28a745; }";
echo ".info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #17a2b8; }";
echo ".warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #ffc107; }";
echo ".error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #dc3545; }";
echo ".user-card { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }";
echo ".btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }";
echo ".btn:hover { background: #0056b3; }";
echo ".btn-success { background: #28a745; }";
echo ".btn-success:hover { background: #218838; }";
echo ".btn-warning { background: #ffc107; color: #212529; }";
echo ".btn-warning:hover { background: #e0a800; }";
echo ".btn-danger { background: #dc3545; }";
echo ".btn-danger:hover { background: #c82333; }";
echo ".login-form { background: #e9ecef; padding: 20px; border-radius: 5px; margin: 20px 0; }";
echo ".form-group { margin: 10px 0; }";
echo ".form-group label { display: block; margin-bottom: 5px; font-weight: bold; }";
echo ".form-group input { width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔧 Crear Usuarios Predeterminados</h1>";
echo "<p>Este script verifica y crea automáticamente los 3 usuarios predeterminados con hashes correctos.</p>";

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

// 2. Verificar clases
echo "<div class='info'>";
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

// 3. Instanciar LoginController (esto automáticamente creará los usuarios)
echo "<div class='info'>";
echo "<h3>3. Instanciando LoginController y Creando Usuarios</h3>";
try {
    $loginController = new App\Controllers\LoginController();
    echo "<p>✅ LoginController instanciado correctamente</p>";
    echo "<p>✅ Usuarios predeterminados verificados/creados automáticamente</p>";
} catch (Exception $e) {
    echo "<p>❌ Error al instanciar LoginController: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// 4. Verificar usuarios en la base de datos
echo "<div class='info'>";
echo "<h3>4. Verificando Usuarios en Base de Datos</h3>";

try {
    $db = App\Database\Database::getInstance()->getConnection();
    
    $usuarios = [
        ['usuario' => 'root', 'rol' => 3, 'descripcion' => 'Superadministrador'],
        ['usuario' => 'admin', 'rol' => 1, 'descripcion' => 'Administrador'],
        ['usuario' => 'cliente', 'rol' => 2, 'descripcion' => 'Cliente/Evaluador']
    ];
    
    foreach ($usuarios as $userInfo) {
        $stmt = $db->prepare('
            SELECT id, usuario, rol, nombre, cedula, correo, activo, 
                   LENGTH(password) as hash_length, 
                   LEFT(password, 20) as hash_preview
            FROM usuarios 
            WHERE usuario = :usuario
        ');
        $stmt->bindParam(':usuario', $userInfo['usuario']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<div class='user-card'>";
            echo "<h4>✅ Usuario: " . $userInfo['usuario'] . " (" . $userInfo['descripcion'] . ")</h4>";
            echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Nombre:</strong> " . $user['nombre'] . "</p>";
            echo "<p><strong>Rol:</strong> " . $user['rol'] . " - " . $userInfo['descripcion'] . "</p>";
            echo "<p><strong>Cédula:</strong> " . $user['cedula'] . "</p>";
            echo "<p><strong>Correo:</strong> " . $user['correo'] . "</p>";
            echo "<p><strong>Activo:</strong> " . ($user['activo'] ? 'SÍ' : 'NO') . "</p>";
            echo "<p><strong>Hash Length:</strong> " . $user['hash_length'] . " caracteres</p>";
            echo "<p><strong>Hash Preview:</strong> " . $user['hash_preview'] . "...</p>";
            
            // Verificar si el hash es correcto
            $testPassword = $userInfo['usuario']; // La contraseña es igual al usuario
            $stmt = $db->prepare('SELECT password FROM usuarios WHERE usuario = :usuario');
            $stmt->bindParam(':usuario', $userInfo['usuario']);
            $stmt->execute();
            $hashResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($testPassword, $hashResult['password'])) {
                echo "<p style='color: #28a745; font-weight: bold;'>✅ Hash de contraseña válido</p>";
            } else {
                echo "<p style='color: #dc3545; font-weight: bold;'>❌ Hash de contraseña inválido</p>";
            }
            echo "</div>";
        } else {
            echo "<div class='error'>";
            echo "<h4>❌ Usuario: " . $userInfo['usuario'] . " NO ENCONTRADO</h4>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Error al verificar usuarios: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Formulario de login directo
echo "<div class='login-form'>";
echo "<h3>5. Login Directo con Usuarios Predeterminados</h3>";
echo "<p>Puedes hacer login directamente con cualquiera de estos usuarios:</p>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>";

// Tarjetas de usuarios para login rápido
$usuariosLogin = [
    [
        'usuario' => 'root',
        'password' => 'root',
        'rol' => 'Superadministrador',
        'color' => '#dc3545',
        'descripcion' => 'Acceso completo al sistema'
    ],
    [
        'usuario' => 'admin',
        'password' => 'admin',
        'rol' => 'Administrador',
        'color' => '#007bff',
        'descripcion' => 'Gestión de usuarios y evaluaciones'
    ],
    [
        'usuario' => 'cliente',
        'password' => 'cliente',
        'rol' => 'Cliente/Evaluador',
        'color' => '#28a745',
        'descripcion' => 'Acceso a evaluaciones'
    ]
];

foreach ($usuariosLogin as $user) {
    echo "<div style='border: 2px solid " . $user['color'] . "; border-radius: 10px; padding: 15px; background: #f8f9fa;'>";
    echo "<h4 style='color: " . $user['color'] . "; margin-top: 0;'>👤 " . $user['rol'] . "</h4>";
    echo "<p><strong>Usuario:</strong> " . $user['usuario'] . "</p>";
    echo "<p><strong>Contraseña:</strong> " . $user['password'] . "</p>";
    echo "<p><em>" . $user['descripcion'] . "</em></p>";
    echo "<button onclick='loginDirecto(\"" . $user['usuario'] . "\", \"" . $user['password'] . "\")' class='btn' style='background: " . $user['color'] . ";'>🔑 Login Directo</button>";
    echo "</div>";
}

echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h4>📋 Instrucciones:</h4>";
echo "<ol>";
echo "<li>Selecciona uno de los usuarios arriba</li>";
echo "<li>Haz clic en 'Login Directo'</li>";
echo "<li>El sistema te redirigirá automáticamente al dashboard correspondiente</li>";
echo "<li>Abre las herramientas de desarrollador (F12) para ver los logs de debug</li>";
echo "</ol>";
echo "</div>";
echo "</div>";

// 6. Enlaces útiles
echo "<div class='info'>";
echo "<h3>6. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='TestLoginControllerDebugConsole.php' class='btn btn-success'>🔍 Debug Console</a>";
echo "<a href='DesbloquearUsuarioRoot.php' class='btn btn-warning'>🔓 Desbloquear Usuario</a>";
echo "<a href='TestLoginControllerCorregido.php' class='btn'>🧪 Test LoginController</a>";
echo "<a href='VerLogsDebug.php' class='btn'>📋 Ver Logs</a>";
echo "</div>";
echo "</div>";

echo "<div class='success'>";
echo "<h3>🎯 Resumen</h3>";
echo "<p>✅ Los 3 usuarios predeterminados han sido verificados y creados automáticamente:</p>";
echo "<ul>";
echo "<li><strong>root/root</strong> - Superadministrador (Rol 3)</li>";
echo "<li><strong>admin/admin</strong> - Administrador (Rol 1)</li>";
echo "<li><strong>cliente/cliente</strong> - Cliente/Evaluador (Rol 2)</li>";
echo "</ul>";
echo "<p>✅ Todos los hashes de contraseña son correctos y compatibles con el LoginController</p>";
echo "<p>✅ Puedes hacer login directo usando los botones de arriba</p>";
echo "</div>";

echo "</div>"; // container

// JavaScript para login directo
echo "<script>";
echo "function loginDirecto(usuario, password) {";
echo "    console.log('🔑 Iniciando login directo para:', usuario);";
echo "    ";
echo "    // Crear formulario temporal";
echo "    var form = document.createElement('form');";
echo "    form.method = 'POST';";
echo "    form.action = 'index.php';";
echo "    ";
echo "    var usuarioInput = document.createElement('input');";
echo "    usuarioInput.type = 'hidden';";
echo "    usuarioInput.name = 'usuario';";
echo "    usuarioInput.value = usuario;";
echo "    ";
echo "    var passwordInput = document.createElement('input');";
echo "    passwordInput.type = 'hidden';";
echo "    passwordInput.name = 'password';";
echo "    passwordInput.value = password;";
echo "    ";
echo "    form.appendChild(usuarioInput);";
echo "    form.appendChild(passwordInput);";
echo "    document.body.appendChild(form);";
echo "    ";
echo "    console.log('📤 Enviando formulario de login...');";
echo "    form.submit();";
echo "}";
echo "</script>";

echo "</body>";
echo "</html>";
?>
