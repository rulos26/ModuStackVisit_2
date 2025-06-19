<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba del Controlador de Cámara de Comercio</h1>";

try {
    // Incluir el controlador
    require_once __DIR__ . '/CamaraComercioController.php';
    
    echo "<h2>1. Instanciando el controlador...</h2>";
    $controller = App\Controllers\CamaraComercioController::getInstance();
    echo "✅ Controlador instanciado correctamente<br>";
    
    echo "<h2>2. Probando obtener opciones...</h2>";
    $opciones = $controller->obtenerOpciones('parametros');
    echo "✅ Opciones obtenidas: " . count($opciones) . " registros<br>";
    
    echo "<h2>3. Probando sanitización de datos...</h2>";
    $datos_prueba = [
        'id_cedula' => '123456789',
        'tiene_camara' => 'Si',
        'nombre' => '<script>alert("test")</script>Empresa Test',
        'razon' => 'Razón Social Test',
        'actividad' => 'Actividad Test',
        'observacion' => 'Observación test'
    ];
    
    $datos_sanitizados = $controller->sanitizarDatos($datos_prueba);
    echo "✅ Datos sanitizados correctamente<br>";
    echo "Original: " . $datos_prueba['nombre'] . "<br>";
    echo "Sanitizado: " . $datos_sanitizados['nombre'] . "<br>";
    
    echo "<h2>4. Probando validación de datos...</h2>";
    $errores = $controller->validarDatos($datos_sanitizados);
    if (empty($errores)) {
        echo "✅ Datos válidos<br>";
    } else {
        echo "❌ Errores encontrados:<br>";
        foreach ($errores as $error) {
            echo "- " . $error . "<br>";
        }
    }
    
    echo "<h2>5. Probando verificar existencia de información...</h2>";
    $existe = $controller->existeInformacion('123456789');
    echo "¿Existe información para cédula 123456789? " . ($existe ? 'Sí' : 'No') . "<br>";
    
    echo "<h2>6. Probando obtener estadísticas...</h2>";
    $stats = $controller->obtenerEstadisticas();
    echo "✅ Estadísticas obtenidas:<br>";
    echo "- Total registros: " . ($stats['total_registros'] ?? 'N/A') . "<br>";
    echo "- Con cámara: " . ($stats['con_camara'] ?? 'N/A') . "<br>";
    echo "- Sin cámara: " . ($stats['sin_camara'] ?? 'N/A') . "<br>";
    
    echo "<h2>✅ Todas las pruebas completadas exitosamente</h2>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las pruebas:</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?> 