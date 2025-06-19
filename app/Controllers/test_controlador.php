<?php
/**
 * ARCHIVO DE PRUEBA DEL CONTROLADOR
 * Para verificar que el controlador funciona correctamente
 */

// Configurar manejo de errores
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simular una sesión de usuario para pruebas
if (!isset($_SESSION['id_cedula'])) {
    $_SESSION['id_cedula'] = '12345678'; // Cédula de prueba
    echo "<p>⚠️ Sesión simulada con cédula: 12345678</p>";
}

echo "<h1>🧪 Prueba del Controlador InformeFinalPdfController</h1>";

try {
    // Incluir el controlador
    require_once 'InformeFinalPdfController.php';
    
    echo "<p>✅ Controlador incluido correctamente</p>";
    
    // Crear instancia del controlador
    $controlador = new InformeFinalPdfController();
    
    echo "<p>✅ Instancia del controlador creada</p>";
    
    // Probar validación de permisos
    $tienePermisos = $controlador->validarPermisos();
    echo "<p>🔐 Validación de permisos: " . ($tienePermisos ? '✅ Aprobado' : '❌ Denegado') . "</p>";
    
    // Probar obtención de estadísticas
    $estadisticas = $controlador->obtenerEstadisticas();
    echo "<p>📊 Estadísticas obtenidas:</p>";
    echo "<ul>";
    echo "<li>Total módulos: " . $estadisticas['total_modulos'] . "</li>";
    echo "<li>Módulos con datos: " . $estadisticas['modulos_con_datos'] . "</li>";
    echo "<li>Módulos vacíos: " . $estadisticas['modulos_vacios'] . "</li>";
    echo "<li>Fecha: " . $estadisticas['fecha_generacion'] . "</li>";
    echo "<li>Cédula: " . $estadisticas['cedula'] . "</li>";
    echo "</ul>";
    
    echo "<p>🎉 ¡Todas las pruebas pasaron exitosamente!</p>";
    
    // Botón para generar informe (solo si hay permisos)
    if ($tienePermisos) {
        echo "<form method='post'>";
        echo "<button type='submit' name='generar_informe' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
        echo "🚀 Generar Informe PDF";
        echo "</button>";
        echo "</form>";
        
        if (isset($_POST['generar_informe'])) {
            echo "<p>⏳ Generando informe...</p>";
            try {
                // Usar el método modular que es más confiable
                $controlador->generarInformeModular();
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error al generar informe: " . $e->getMessage() . "</p>";
                echo "<p><strong>Detalles del error:</strong></p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Detalles del error:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='../resources/views/evaluador/evaluacion_visita/visita/informe/menu_principal.php' style='color: #007bff;'>🏠 Volver al Menú Principal</a></p>";
echo "<p><a href='ejemplo_uso_informe.php' style='color: #007bff;'>📖 Ver Ejemplo Completo</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}
h1 {
    color: #333;
    text-align: center;
}
p {
    margin: 10px 0;
    padding: 10px;
    background: white;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}
ul {
    background: white;
    padding: 20px;
    border-radius: 5px;
    margin: 10px 0;
}
li {
    margin: 5px 0;
}
pre {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    font-size: 12px;
}
</style> 