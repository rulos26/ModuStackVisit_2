<?php
/**
 * PRUEBA DIRECTA DEL SISTEMA MODULARIZADO
 * Prueba el sistema sin pasar por el controlador
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

echo "<h1>🧪 Prueba Directa del Sistema Modularizado</h1>";

try {
    // Definir ruta base
    $basePath = $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2';
    
    // Verificar archivo de conexión
    $conexionPath = $basePath . '/conn/conexion.php';
    if (!file_exists($conexionPath)) {
        throw new Exception('Archivo de conexión no encontrado: ' . $conexionPath);
    }
    
    echo "<p>✅ Archivo de conexión encontrado</p>";
    
    // Incluir conexión
    require_once $conexionPath;
    
    if (!isset($mysqli) || !$mysqli) {
        throw new Exception('No se pudo establecer conexión con la base de datos');
    }
    
    echo "<p>✅ Conexión a base de datos establecida</p>";
    
    // Verificar archivo del sistema modularizado
    $informePath = $basePath . '/resources/views/evaluador/evaluacion_visita/visita/informe/InformeModular.php';
    if (!file_exists($informePath)) {
        throw new Exception('Archivo InformeModular.php no encontrado: ' . $informePath);
    }
    
    echo "<p>✅ Archivo InformeModular.php encontrado</p>";
    
    // Incluir el sistema modularizado
    require_once $informePath;
    
    echo "<p>✅ Sistema modularizado incluido correctamente</p>";
    
    // Verificar que la clase existe
    if (!class_exists('InformeVisitaDomiciliariaModular')) {
        throw new Exception('La clase InformeVisitaDomiciliariaModular no existe');
    }
    
    echo "<p>✅ Clase InformeVisitaDomiciliariaModular encontrada</p>";
    
    // Crear instancia
    $id_cedula = $_SESSION['id_cedula'];
    $informe = new InformeVisitaDomiciliariaModular($id_cedula, $mysqli);
    
    echo "<p>✅ Instancia del sistema modularizado creada</p>";
    
    // Verificar que el método existe
    if (!method_exists($informe, 'generarInforme')) {
        throw new Exception('El método generarInforme no existe');
    }
    
    echo "<p>✅ Método generarInforme encontrado</p>";
    
    echo "<p>🎉 ¡Sistema modularizado listo para usar!</p>";
    
    // Botón para generar informe
    echo "<form method='post'>";
    echo "<button type='submit' name='generar_informe' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>";
    echo "🚀 Generar Informe PDF";
    echo "</button>";
    echo "</form>";
    
    if (isset($_POST['generar_informe'])) {
        echo "<p>⏳ Generando informe...</p>";
        try {
            $informe->generarInforme();
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error al generar informe: " . $e->getMessage() . "</p>";
            echo "<p><strong>Detalles del error:</strong></p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Detalles del error:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='../resources/views/evaluador/evaluacion_visita/visita/informe/menu_principal.php' style='color: #007bff;'>🏠 Volver al Menú Principal</a></p>";
echo "<p><a href='test_controlador.php' style='color: #007bff;'>🔧 Probar Controlador</a></p>";
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
pre {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    font-size: 12px;
}
</style> 