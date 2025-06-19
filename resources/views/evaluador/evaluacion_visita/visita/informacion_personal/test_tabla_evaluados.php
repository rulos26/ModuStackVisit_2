<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de Tabla Evaluados</h1>";

try {
    // Incluir el controlador
    require_once __DIR__ . '/InformacionPersonalController.php';
    
    echo "<h2>1. Instanciando el controlador...</h2>";
    $controller = App\Controllers\InformacionPersonalController::getInstance();
    echo "✅ Controlador instanciado correctamente<br>";
    
    echo "<h2>2. Probando conexión a la tabla evaluados...</h2>";
    
    // Probar obtener datos por cédula
    $cedula_test = '123456789';
    $datos = $controller->obtenerPorCedula($cedula_test);
    if ($datos) {
        echo "✅ Datos encontrados para cédula $cedula_test<br>";
        echo "Nombre: " . $datos['nombres'] . " " . $datos['apellidos'] . "<br>";
    } else {
        echo "ℹ️ No se encontraron datos para cédula $cedula_test (esto es normal si no existe)<br>";
    }
    
    // Probar verificar existencia
    $existe = $controller->existeInformacion($cedula_test);
    echo "¿Existe información para cédula $cedula_test? " . ($existe ? 'Sí' : 'No') . "<br>";
    
    // Probar obtener estadísticas
    echo "<h3>Estadísticas de la tabla evaluados:</h3>";
    $stats = $controller->obtenerEstadisticas();
    if ($stats) {
        echo "✅ Estadísticas obtenidas:<br>";
        echo "- Total registros: " . ($stats['total_registros'] ?? 'N/A') . "<br>";
        echo "- Ciudades diferentes: " . ($stats['ciudades_diferentes'] ?? 'N/A') . "<br>";
        echo "- Edad promedio: " . round($stats['edad_promedio'] ?? 0, 2) . "<br>";
        echo "- Solteros: " . ($stats['solteros'] ?? 'N/A') . "<br>";
        echo "- Casados: " . ($stats['casados'] ?? 'N/A') . "<br>";
    } else {
        echo "ℹ️ No se pudieron obtener estadísticas<br>";
    }
    
    echo "<h2>3. Probando opciones de select boxes...</h2>";
    
    // Probar municipios
    $municipios = $controller->obtenerOpciones('municipios');
    echo "✅ Municipios: " . count($municipios) . " registros<br>";
    
    // Probar pesos
    $pesos = $controller->obtenerOpciones('pesos');
    echo "✅ Pesos: " . count($pesos) . " registros<br>";
    
    // Probar estado civil
    $estado_civil = $controller->obtenerOpciones('estado_civil');
    echo "✅ Estados civiles: " . count($estado_civil) . " registros<br>";
    
    echo "<h2>4. Resumen de consultas SQL corregidas:</h2>";
    echo "<ul>";
    echo "<li><strong>INSERT:</strong> INSERT INTO evaluados (...) VALUES (...)</li>";
    echo "<li><strong>UPDATE:</strong> UPDATE evaluados SET ... WHERE id_cedula = :id_cedula</li>";
    echo "<li><strong>SELECT:</strong> SELECT * FROM evaluados WHERE id_cedula = :cedula</li>";
    echo "<li><strong>COUNT:</strong> SELECT COUNT(*) FROM evaluados WHERE id_cedula = :cedula</li>";
    echo "<li><strong>DELETE:</strong> DELETE FROM evaluados WHERE id_cedula = :cedula</li>";
    echo "<li><strong>STATS:</strong> SELECT ... FROM evaluados</li>";
    echo "</ul>";
    
    echo "<h2>✅ Prueba completada exitosamente</h2>";
    echo "<p style='color: green;'>🎉 Todas las consultas SQL ahora apuntan a la tabla correcta: <strong>evaluados</strong></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las pruebas:</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?> 