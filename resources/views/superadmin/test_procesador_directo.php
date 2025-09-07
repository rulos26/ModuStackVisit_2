<?php
// Test directo del procesador simple
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    die('Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.');
}

echo "<h2>Test Directo del Procesador Simple</h2>";

// Simular la llamada POST
$_POST['accion'] = 'obtener_usuarios_evaluados';

echo "<h3>Simulando llamada POST con accion: obtener_usuarios_evaluados</h3>";

// Capturar la salida
ob_start();

try {
    // Incluir el procesador simple
    include 'procesar_simple.php';
} catch (Exception $e) {
    echo "❌ Error al incluir procesar_simple.php: " . $e->getMessage() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
}

$output = ob_get_clean();

echo "<h3>Salida del procesador:</h3>";
echo "<pre>" . htmlspecialchars($output) . "</pre>";

// Intentar parsear como JSON
if (!empty($output)) {
    echo "<h3>Análisis de la salida:</h3>";
    
    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Salida válida JSON<br>";
        echo "Tipo de datos: " . gettype($json) . "<br>";
        if (is_array($json)) {
            echo "Elementos: " . count($json) . "<br>";
            if (isset($json['error'])) {
                echo "❌ Error: " . $json['error'] . "<br>";
            } else {
                echo "✅ Datos válidos<br>";
            }
        }
    } else {
        echo "❌ Error JSON: " . json_last_error_msg() . "<br>";
        echo "Primeros 200 caracteres: " . htmlspecialchars(substr($output, 0, 200)) . "<br>";
    }
} else {
    echo "❌ No se generó salida<br>";
}

echo "<br><a href='gestion_tablas_principales.php'>Volver a Tablas Principales</a>";
?>
