<?php
// Script para verificar el tiempo restante del bloqueo automático
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>⏰ ESPERAR DESBLOQUEO AUTOMÁTICO</h2>";
echo "<hr>";

// Función para mostrar el estado del bloqueo
function mostrarEstadoBloqueo($pdo, $usuario) {
    $stmt = $pdo->prepare('
        SELECT intentos_fallidos, bloqueado_hasta
        FROM usuarios 
        WHERE usuario = :usuario
    ');
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
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

// 3. Verificar estado del bloqueo
echo "<h3>3. Estado del Bloqueo</h3>";
$usuario = 'root';
$estado = mostrarEstadoBloqueo($pdo, $usuario);

if ($estado) {
    $intentosFallidos = $estado['intentos_fallidos'] ?? 0;
    $bloqueadoHasta = $estado['bloqueado_hasta'];
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>Información del Bloqueo:</h4>";
    echo "<p><strong>Usuario:</strong> $usuario</p>";
    echo "<p><strong>Intentos Fallidos:</strong> $intentosFallidos</p>";
    echo "<p><strong>Bloqueado Hasta:</strong> " . ($bloqueadoHasta ?? 'NO BLOQUEADO') . "</p>";
    echo "</div>";
    
    // Calcular tiempo restante
    if ($bloqueadoHasta) {
        $tiempoBloqueo = strtotime($bloqueadoHasta);
        $tiempoActual = time();
        $tiempoRestante = $tiempoBloqueo - $tiempoActual;
        
        if ($tiempoRestante > 0) {
            $minutos = floor($tiempoRestante / 60);
            $segundos = $tiempoRestante % 60;
            
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>⏰ Tiempo Restante del Bloqueo:</h4>";
            echo "<p style='font-size: 18px; font-weight: bold; color: #856404;'>";
            echo "$minutos minutos y $segundos segundos";
            echo "</p>";
            echo "<p><strong>Se desbloqueará automáticamente el:</strong> " . date('Y-m-d H:i:s', $tiempoBloqueo) . "</p>";
            echo "</div>";
            
            // Mostrar barra de progreso
            $duracionTotal = 900; // 15 minutos en segundos
            $progreso = (($duracionTotal - $tiempoRestante) / $duracionTotal) * 100;
            
            echo "<div style='background: #e9ecef; border-radius: 10px; padding: 3px; margin: 10px 0;'>";
            echo "<div style='background: #007bff; height: 20px; border-radius: 8px; width: $progreso%; transition: width 0.3s;'></div>";
            echo "</div>";
            echo "<p style='text-align: center; color: #6c757d;'>Progreso del bloqueo: " . round($progreso, 1) . "%</p>";
            
        } else {
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h4>✅ Bloqueo Expirado</h4>";
            echo "<p style='color: #155724; font-weight: bold;'>El bloqueo ya ha expirado. El usuario puede intentar login nuevamente.</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h4>✅ Usuario No Bloqueado</h4>";
        echo "<p style='color: #155724; font-weight: bold;'>El usuario no está bloqueado.</p>";
        echo "</div>";
    }
} else {
    echo "<p style='color: #dc3545; font-weight: bold;'>❌ Usuario no encontrado</p>";
}

// 4. Opciones disponibles
echo "<h3>4. Opciones Disponibles</h3>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>¿Qué puedes hacer?</h4>";
echo "<ul>";
echo "<li><strong>Esperar:</strong> El bloqueo se desbloqueará automáticamente en el tiempo mostrado arriba</li>";
echo "<li><strong>Desbloquear manualmente:</strong> Usar el script de desbloqueo inmediato</li>";
echo "<li><strong>Probar login:</strong> Verificar si ya se desbloqueó automáticamente</li>";
echo "</ul>";
echo "</div>";

// 5. Enlaces de acción
echo "<h3>5. Acciones Disponibles</h3>";
echo "<div style='display: flex; gap: 10px; flex-wrap: wrap; margin: 20px 0;'>";

echo "<a href='DesbloquearUsuarioRoot.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
echo "🔓 Desbloquear Manualmente";
echo "</a>";

echo "<a href='TestLoginControllerCorregido.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
echo "🧪 Probar Login";
echo "</a>";

echo "<a href='TestLoginConDebug.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
echo "⚡ Test con Debug";
echo "</a>";

echo "<a href='VerLogsDebug.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>";
echo "📋 Ver Logs";
echo "</a>";

echo "</div>";

// 6. Auto-refresh si está bloqueado
if ($bloqueadoHasta && $tiempoRestante > 0) {
    echo "<script>";
    echo "setTimeout(function() {";
    echo "  location.reload();";
    echo "}, 30000);"; // Recargar cada 30 segundos
    echo "</script>";
    echo "<p style='text-align: center; color: #6c757d; font-style: italic;'>";
    echo "🔄 Esta página se actualizará automáticamente cada 30 segundos para mostrar el progreso del bloqueo.";
    echo "</p>";
}

// 7. Información técnica
echo "<h3>6. Información Técnica</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h4>Configuración del Sistema:</h4>";
echo "<p><strong>Máximo Intentos:</strong> 5</p>";
echo "<p><strong>Duración del Bloqueo:</strong> 15 minutos (900 segundos)</p>";
echo "<p><strong>Desbloqueo Automático:</strong> Sí</p>";
echo "<p><strong>Desbloqueo Manual:</strong> Disponible</p>";
echo "</div>";

echo "<hr>";
echo "<h3>🎯 RESUMEN</h3>";
if ($bloqueadoHasta && $tiempoRestante > 0) {
    echo "<p>El usuario 'root' está bloqueado temporalmente. Puedes esperar a que expire automáticamente o desbloquearlo manualmente.</p>";
} else {
    echo "<p>El usuario 'root' no está bloqueado y está listo para usar.</p>";
}
?>
