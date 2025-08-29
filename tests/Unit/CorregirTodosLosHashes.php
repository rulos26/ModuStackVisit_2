<?php
// Script para corregir todos los hashes de contraseña
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>🔧 Corregir Todos los Hashes</title>";
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
echo ".user-card { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0; border: 1px solid #dee2e6; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>🔧 Corregir Todos los Hashes de Contraseña</h1>";
echo "<p>Este script corrige los hashes de contraseña de todos los usuarios predeterminados.</p>";

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

// 2. Conectar a la base de datos
echo "<div class='info'>";
echo "<h3>2. Conectando a Base de Datos</h3>";
try {
    $db = App\Database\Database::getInstance()->getConnection();
    echo "<p>✅ Conexión a base de datos exitosa</p>";
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// 3. Definir usuarios a corregir
$usuariosACorregir = [
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

// 4. Procesar cada usuario
echo "<div class='info'>";
echo "<h3>3. Corrigiendo Hashes de Contraseña</h3>";

$usuariosCorregidos = 0;
$errores = 0;

foreach ($usuariosACorregir as $userInfo) {
    echo "<div class='user-card'>";
    echo "<h4>🔧 Procesando: " . $userInfo['usuario'] . " (" . $userInfo['descripcion'] . ")</h4>";
    
    try {
        // Verificar si el usuario existe
        $stmt = $db->prepare('SELECT id, password, LENGTH(password) as hash_length FROM usuarios WHERE usuario = :usuario');
        $stmt->bindParam(':usuario', $userInfo['usuario']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "<p>❌ Usuario no encontrado en la base de datos</p>";
            $errores++;
            echo "</div>";
            continue;
        }
        
        echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
        echo "<p><strong>Hash actual:</strong> " . substr($user['password'], 0, 20) . "...</p>";
        echo "<p><strong>Longitud actual:</strong> " . $user['hash_length'] . " caracteres</p>";
        
        // Verificar si el hash actual es válido
        $hashActualValido = password_verify($userInfo['password'], $user['password']);
        echo "<p><strong>Hash actual válido:</strong> " . ($hashActualValido ? '✅ SÍ' : '❌ NO') . "</p>";
        
        if ($hashActualValido) {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ Hash ya es válido - no necesita corrección</p>";
        } else {
            // Crear nuevo hash correcto
            $nuevoHash = password_hash($userInfo['password'], PASSWORD_DEFAULT);
            $nuevaLongitud = strlen($nuevoHash);
            
            echo "<p><strong>Nuevo hash:</strong> " . substr($nuevoHash, 0, 20) . "...</p>";
            echo "<p><strong>Nueva longitud:</strong> " . $nuevaLongitud . " caracteres</p>";
            
            // Verificar que el nuevo hash sea válido
            $nuevoHashValido = password_verify($userInfo['password'], $nuevoHash);
            echo "<p><strong>Nuevo hash válido:</strong> " . ($nuevoHashValido ? '✅ SÍ' : '❌ NO') . "</p>";
            
            if ($nuevoHashValido) {
                // Actualizar el hash en la base de datos
                $stmt = $db->prepare('UPDATE usuarios SET password = :password WHERE usuario = :usuario');
                $stmt->bindParam(':password', $nuevoHash);
                $stmt->bindParam(':usuario', $userInfo['usuario']);
                
                if ($stmt->execute()) {
                    echo "<p style='color: #28a745; font-weight: bold;'>✅ Hash actualizado exitosamente en la base de datos</p>";
                    $usuariosCorregidos++;
                } else {
                    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Error al actualizar hash en la base de datos</p>";
                    $errores++;
                }
            } else {
                echo "<p style='color: #dc3545; font-weight: bold;'>❌ Error: El nuevo hash generado no es válido</p>";
                $errores++;
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: #dc3545; font-weight: bold;'>❌ Error procesando usuario: " . $e->getMessage() . "</p>";
        $errores++;
    }
    
    echo "</div>";
}

echo "</div>";

// 5. Verificar resultados finales
echo "<div class='info'>";
echo "<h3>4. Verificación Final de Hashes</h3>";

$usuariosVerificados = 0;
$hashesValidos = 0;

foreach ($usuariosACorregir as $userInfo) {
    try {
        $stmt = $db->prepare('SELECT id, password, LENGTH(password) as hash_length FROM usuarios WHERE usuario = :usuario');
        $stmt->bindParam(':usuario', $userInfo['usuario']);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $usuariosVerificados++;
            $hashValido = password_verify($userInfo['password'], $user['password']);
            
            echo "<div class='user-card'>";
            echo "<h4>🔍 Verificación: " . $userInfo['usuario'] . "</h4>";
            echo "<p><strong>ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Hash:</strong> " . substr($user['password'], 0, 20) . "...</p>";
            echo "<p><strong>Longitud:</strong> " . $user['hash_length'] . " caracteres</p>";
            echo "<p><strong>Estado:</strong> " . ($hashValido ? '✅ VÁLIDO' : '❌ INVÁLIDO') . "</p>";
            echo "</div>";
            
            if ($hashValido) {
                $hashesValidos++;
            }
        }
    } catch (Exception $e) {
        echo "<p>❌ Error verificando usuario " . $userInfo['usuario'] . ": " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

// 6. Resumen final
echo "<div class='success'>";
echo "<h3>🎯 Resumen de Corrección</h3>";
echo "<p><strong>Usuarios procesados:</strong> " . count($usuariosACorregir) . "</p>";
echo "<p><strong>Usuarios corregidos:</strong> " . $usuariosCorregidos . "</p>";
echo "<p><strong>Errores encontrados:</strong> " . $errores . "</p>";
echo "<p><strong>Usuarios verificados:</strong> " . $usuariosVerificados . "</p>";
echo "<p><strong>Hashes válidos:</strong> " . $hashesValidos . " de " . $usuariosVerificados . "</p>";

if ($hashesValidos === $usuariosVerificados && $usuariosVerificados > 0) {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ ¡TODOS LOS HASHES HAN SIDO CORREGIDOS EXITOSAMENTE!</p>";
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Algunos hashes aún necesitan corrección</p>";
}
echo "</div>";

// 7. Enlaces útiles
echo "<div class='info'>";
echo "<h3>5. Enlaces Útiles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap;'>";
echo "<a href='CrearUsuariosPredeterminados.php' class='btn btn-success'>🔧 Crear Usuarios</a>";
echo "<a href='TestCorreccionBindParam.php' class='btn'>🧪 Test BindParam</a>";
echo "<a href='TestLoginControllerDebugConsole.php' class='btn'>🔍 Debug Console</a>";
echo "<a href='VerLogsDebug.php' class='btn'>📋 Ver Logs</a>";
echo "</div>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>📋 Instrucciones de Uso</h3>";
echo "<ol>";
echo "<li>Este script corrige automáticamente los hashes de contraseña</li>";
echo "<li>Verifica que cada hash sea válido antes de actualizar</li>";
echo "<li>Muestra un resumen completo de la corrección</li>";
echo "<li>Proporciona enlaces para verificar el funcionamiento</li>";
echo "</ol>";
echo "</div>";

echo "</div>"; // container

echo "<script>";
echo "console.log('🔧 Script de corrección de hashes completado');";
echo "console.log('✅ Hashes corregidos: " . $usuariosCorregidos . "');";
echo "console.log('❌ Errores: " . $errores . "');";
echo "</script>";

echo "</body>";
echo "</html>";
?>
