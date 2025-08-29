<?php
// Script para visualizar logs de debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$debugFile = __DIR__ . '/../../logs/debug.log';
$appLogFile = __DIR__ . '/../../logs/app.log';

echo "<h2>🔍 VISUALIZADOR DE LOGS DE DEBUG</h2>";
echo "<hr>";

// Función para mostrar logs
function showLogs($filename, $title) {
    echo "<h3>$title</h3>";
    
    if (file_exists($filename)) {
        $lines = file($filename);
        $size = filesize($filename);
        $modified = date('Y-m-d H:i:s', filemtime($filename));
        
        echo "<p><strong>Archivo:</strong> $filename</p>";
        echo "<p><strong>Tamaño:</strong> " . number_format($size) . " bytes</p>";
        echo "<p><strong>Última modificación:</strong> $modified</p>";
        echo "<p><strong>Total líneas:</strong> " . count($lines) . "</p>";
        
        if (!empty($lines)) {
            echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 15px; max-height: 500px; overflow-y: auto;'>";
            echo "<pre style='margin: 0; font-family: monospace; font-size: 12px;'>";
            
            // Mostrar últimas 50 líneas
            $lastLines = array_slice($lines, -50);
            foreach ($lastLines as $line) {
                // Resaltar líneas importantes
                $line = htmlspecialchars($line);
                if (strpos($line, 'ERROR') !== false) {
                    echo "<span style='color: #dc3545; font-weight: bold;'>$line</span>";
                } elseif (strpos($line, 'CUENTA BLOQUEADA') !== false) {
                    echo "<span style='color: #fd7e14; font-weight: bold;'>$line</span>";
                } elseif (strpos($line, 'AUTENTICACIÓN EXITOSA') !== false) {
                    echo "<span style='color: #28a745; font-weight: bold;'>$line</span>";
                } elseif (strpos($line, 'INICIO AUTENTICACIÓN') !== false) {
                    echo "<span style='color: #007bff; font-weight: bold;'>$line</span>";
                } elseif (strpos($line, 'VERIFICACIÓN DE CONTRASEÑA') !== false) {
                    echo "<span style='color: #6f42c1; font-weight: bold;'>$line</span>";
                } else {
                    echo $line;
                }
            }
            echo "</pre>";
            echo "</div>";
            
            if (count($lines) > 50) {
                echo "<p><em>Mostrando las últimas 50 líneas de " . count($lines) . " totales</em></p>";
            }
        } else {
            echo "<p>El archivo está vacío</p>";
        }
    } else {
        echo "<p style='color: #dc3545;'>❌ Archivo no encontrado: $filename</p>";
    }
    
    echo "<hr>";
}

// Mostrar logs de debug
showLogs($debugFile, "📋 Logs de Debug del LoginController");

// Mostrar logs de aplicación
showLogs($appLogFile, "📋 Logs de Aplicación");

// Botones de acción
echo "<h3>🛠️ Acciones</h3>";
echo "<div style='margin: 20px 0;'>";
echo "<form method='POST' style='display: inline; margin-right: 10px;'>";
echo "<input type='hidden' name='action' value='clear_debug'>";
echo "<button type='submit' onclick='return confirm(\"¿Estás seguro de que quieres limpiar los logs de debug?\")' style='background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;'>";
echo "🗑️ Limpiar Logs Debug";
echo "</button>";
echo "</form>";

echo "<form method='POST' style='display: inline; margin-right: 10px;'>";
echo "<input type='hidden' name='action' value='clear_app'>";
echo "<button type='submit' onclick='return confirm(\"¿Estás seguro de que quieres limpiar los logs de aplicación?\")' style='background: #fd7e14; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;'>";
echo "🗑️ Limpiar Logs App";
echo "</button>";
echo "</form>";

echo "<form method='POST' style='display: inline;'>";
echo "<input type='hidden' name='action' => 'refresh'>";
echo "<button type='submit' style='background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;'>";
echo "🔄 Actualizar";
echo "</button>";
echo "</form>";
echo "</div>";

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'clear_debug':
            if (file_exists($debugFile)) {
                file_put_contents($debugFile, '');
                echo "<p style='color: #28a745;'>✅ Logs de debug limpiados</p>";
            }
            break;
            
        case 'clear_app':
            if (file_exists($appLogFile)) {
                file_put_contents($appLogFile, '');
                echo "<p style='color: #28a745;'>✅ Logs de aplicación limpiados</p>";
            }
            break;
            
        case 'refresh':
            echo "<p style='color: #007bff;'>🔄 Página actualizada</p>";
            break;
    }
}

// Información adicional
echo "<h3>💡 Información</h3>";
echo "<ul>";
echo "<li><strong>Logs de Debug:</strong> Contienen información detallada del flujo de autenticación</li>";
echo "<li><strong>Logs de Aplicación:</strong> Contienen logs generales del sistema</li>";
echo "<li><strong>Colores:</strong> Los logs importantes están resaltados con colores</li>";
echo "<li><strong>Actualización:</strong> Los logs se actualizan en tiempo real</li>";
echo "</ul>";

echo "<h3>🔗 Enlaces Útiles</h3>";
echo "<p>";
echo "<a href='TestBasico.php' style='margin-right: 10px;'>🧪 Prueba Básica</a>";
echo "<a href='DiagnosticoError500.php' style='margin-right: 10px;'>🔍 Diagnóstico Error 500</a>";
echo "<a href='ActualizarTablaUsuariosV2.php' style='margin-right: 10px;'>🗄️ Actualizar Tabla</a>";
echo "<a href='TestLoginControllerOptimizado.php'>⚡ Test LoginController</a>";
echo "</p>";
?>
