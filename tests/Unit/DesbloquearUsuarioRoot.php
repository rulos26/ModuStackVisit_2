<?php
// Script para desbloquear la cuenta del usuario 'root'
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔓 DESBLOQUEAR USUARIO ROOT</h2>";
echo "<hr>";

// Función para mostrar el estado actual del usuario
function mostrarEstadoUsuario($pdo, $usuario) {
    $stmt = $pdo->prepare('
        SELECT id, usuario, rol, activo, intentos_fallidos, bloqueado_hasta, ultimo_acceso
        FROM usuarios 
        WHERE usuario = :usuario
    ');
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para desbloquear usuario
function desbloquearUsuario($pdo, $usuario) {
    $stmt = $pdo->prepare('
        UPDATE usuarios 
        SET intentos_fallidos = 0, 
            bloqueado_hasta = NULL 
        WHERE usuario = :usuario
    ');
    $stmt->bindParam(':usuario', $usuario);
    return $stmt->execute();
}

// 1. Cargar configuración
echo "<h3>1. Cargando Configuración</h3>";
try {
    $configPath = __DIR__ . '/../../app/Config/config.php';
    if (file_exists($configPath)) {
        $config = require $configPath;
        echo "<p>✅ Configuración cargada</p>";
    } else {
        echo "<p>❌ Archivo de configuración no encontrado</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p>❌ Error al cargar configuración: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Conectar a la base de datos
echo "<h3>2. Conectando a Base de Datos</h3>";
try {
    $host = $config['database']['host'];
    $dbname = $config['database']['dbname'];
    $username = $config['database']['username'];
    $password = $config['database']['password'];
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Conexión exitosa</p>";
} catch (Exception $e) {
    echo "<p>❌ Error de conexión: " . $e->getMessage() . "</p>";
    exit;
}

// 3. Mostrar estado actual del usuario 'root'
echo "<h3>3. Estado Actual del Usuario 'root'</h3>";
$usuario = 'root';
$estadoActual = mostrarEstadoUsuario($pdo, $usuario);

if ($estadoActual) {
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>Información del Usuario:</h4>";
    echo "<p><strong>ID:</strong> " . $estadoActual['id'] . "</p>";
    echo "<p><strong>Usuario:</strong> " . $estadoActual['usuario'] . "</p>";
    echo "<p><strong>Rol:</strong> " . $estadoActual['rol'] . "</p>";
    echo "<p><strong>Activo:</strong> " . ($estadoActual['activo'] ? 'SÍ' : 'NO') . "</p>";
    echo "<p><strong>Intentos Fallidos:</strong> " . ($estadoActual['intentos_fallidos'] ?? '0') . "</p>";
    echo "<p><strong>Bloqueado Hasta:</strong> " . ($estadoActual['bloqueado_hasta'] ?? 'NO BLOQUEADO') . "</p>";
    echo "<p><strong>Último Acceso:</strong> " . ($estadoActual['ultimo_acceso'] ?? 'NUNCA') . "</p>";
    echo "</div>";
    
    // Verificar si está bloqueado
    $estaBloqueado = !empty($estadoActual['bloqueado_hasta']) && 
                     strtotime($estadoActual['bloqueado_hasta']) > time();
    
    if ($estaBloqueado) {
        echo "<p style='color: #dc3545; font-weight: bold;'>🔒 USUARIO BLOQUEADO</p>";
        echo "<p>El usuario está bloqueado hasta: <strong>" . $estadoActual['bloqueado_hasta'] . "</strong></p>";
    } else {
        echo "<p style='color: #28a745; font-weight: bold;'>✅ USUARIO NO BLOQUEADO</p>";
    }
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Usuario 'root' no encontrado</p>";
    exit;
}

// 4. Procesar desbloqueo si se solicita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['desbloquear'])) {
    echo "<h3>4. Procesando Desbloqueo</h3>";
    
    try {
        $resultado = desbloquearUsuario($pdo, $usuario);
        
        if ($resultado) {
            echo "<p style='color: #28a745; font-weight: bold;'>✅ USUARIO DESBLOQUEADO EXITOSAMENTE</p>";
            
            // Mostrar estado después del desbloqueo
            $estadoNuevo = mostrarEstadoUsuario($pdo, $usuario);
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>Estado Después del Desbloqueo:</h4>";
            echo "<p><strong>Intentos Fallidos:</strong> " . ($estadoNuevo['intentos_fallidos'] ?? '0') . "</p>";
            echo "<p><strong>Bloqueado Hasta:</strong> " . ($estadoNuevo['bloqueado_hasta'] ?? 'NO BLOQUEADO') . "</p>";
            echo "</div>";
        } else {
            echo "<p style='color: #dc3545; font-weight: bold;'>❌ Error al desbloquear usuario</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: #dc3545; font-weight: bold;'>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// 5. Formulario de desbloqueo
echo "<h3>5. Acción de Desbloqueo</h3>";
if ($estaBloqueado) {
    echo "<form method='POST' style='margin: 20px 0;'>";
    echo "<button type='submit' name='desbloquear' style='background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🔓 DESBLOQUEAR USUARIO ROOT";
    echo "</button>";
    echo "</form>";
    echo "<p><em>⚠️ Advertencia: Esta acción desbloqueará inmediatamente la cuenta del usuario 'root'.</em></p>";
} else {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ El usuario no está bloqueado. No es necesario desbloquear.</p>";
}

// 6. Información adicional
echo "<h3>6. Información Adicional</h3>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Configuración de Bloqueo:</h4>";
echo "<p><strong>Máximo Intentos:</strong> 5</p>";
echo "<p><strong>Duración del Bloqueo:</strong> 15 minutos (900 segundos)</p>";
echo "<p><strong>Usuario Afectado:</strong> root</p>";
echo "</div>";

// 7. Enlaces útiles
echo "<h3>7. Próximos Pasos</h3>";
echo "<p>Después de desbloquear el usuario, puedes:</p>";
echo "<ul>";
echo "<li><a href='TestLoginControllerCorregido.php' style='color: #007bff;'>🧪 Probar LoginController Corregido</a></li>";
echo "<li><a href='TestLoginConDebug.php' style='color: #007bff;'>⚡ Probar Login con Debug</a></li>";
echo "<li><a href='TestBasico.php' style='color: #007bff;'>🔍 Ejecutar Prueba Básica</a></li>";
echo "<li><a href='VerLogsDebug.php' style='color: #007bff;'>📋 Ver Logs de Debug</a></li>";
echo "</ul>";

echo "<hr>";
echo "<h3>🎯 RESUMEN</h3>";
echo "<p>Este script permite desbloquear la cuenta del usuario 'root' que está temporalmente bloqueada debido a múltiples intentos fallidos de login.</p>";
echo "<p><strong>Estado:</strong> " . ($estaBloqueado ? '🔒 BLOQUEADO' : '✅ NO BLOQUEADO') . "</p>";

if (!$estaBloqueado) {
    echo "<p style='color: #28a745; font-weight: bold;'>✅ El usuario 'root' ya está desbloqueado y listo para usar.</p>";
}
?>
