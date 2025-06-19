<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Prueba de Opciones Corregidas</h1>";

try {
    // Incluir el controlador
    require_once __DIR__ . '/InformacionPersonalController.php';
    
    echo "<h2>1. Instanciando el controlador...</h2>";
    $controller = App\Controllers\InformacionPersonalController::getInstance();
    echo "✅ Controlador instanciado correctamente<br>";
    
    echo "<h2>2. Probando todas las opciones...</h2>";
    
    // Probar municipios
    echo "<h3>Municipios:</h3>";
    $municipios = $controller->obtenerOpciones('municipios');
    echo "✅ Municipios obtenidos: " . count($municipios) . " registros<br>";
    if (count($municipios) > 0) {
        echo "Primer municipio: ID=" . $municipios[0]['id_municipio'] . ", Nombre=" . $municipios[0]['municipio'] . "<br>";
    }
    
    // Probar tipos de documentos
    echo "<h3>Tipos de Documentos:</h3>";
    $tipos_documentos = $controller->obtenerOpciones('tipo_documentos');
    echo "✅ Tipos de documentos obtenidos: " . count($tipos_documentos) . " registros<br>";
    if (count($tipos_documentos) > 0) {
        echo "Primer tipo: ID=" . $tipos_documentos[0]['id'] . ", Nombre=" . $tipos_documentos[0]['nombre'] . "<br>";
    }
    
    // Probar RH
    echo "<h3>Tipos de RH:</h3>";
    $rh = $controller->obtenerOpciones('rh');
    echo "✅ Tipos de RH obtenidos: " . count($rh) . " registros<br>";
    if (count($rh) > 0) {
        echo "Primer RH: ID=" . $rh[0]['id'] . ", Nombre=" . $rh[0]['nombre'] . "<br>";
    }
    
    // Probar estaturas
    echo "<h3>Estaturas:</h3>";
    $estaturas = $controller->obtenerOpciones('estaturas');
    echo "✅ Estaturas obtenidas: " . count($estaturas) . " registros<br>";
    if (count($estaturas) > 0) {
        echo "Primera estatura: ID=" . $estaturas[0]['id'] . ", Nombre=" . $estaturas[0]['nombre'] . "<br>";
    }
    
    // Probar pesos (CORREGIDO)
    echo "<h3>Pesos (tabla opc_peso):</h3>";
    $pesos = $controller->obtenerOpciones('pesos');
    echo "✅ Pesos obtenidos: " . count($pesos) . " registros<br>";
    if (count($pesos) > 0) {
        echo "Primer peso: ID=" . $pesos[0]['id'] . ", Nombre=" . $pesos[0]['nombre'] . "<br>";
    }
    
    // Probar estado civil (CORREGIDO)
    echo "<h3>Estados Civiles (tabla opc_estado_civiles):</h3>";
    $estado_civil = $controller->obtenerOpciones('estado_civil');
    echo "✅ Estados civiles obtenidos: " . count($estado_civil) . " registros<br>";
    if (count($estado_civil) > 0) {
        echo "Primer estado civil: ID=" . $estado_civil[0]['id'] . ", Nombre=" . $estado_civil[0]['nombre'] . "<br>";
    }
    
    // Probar estratos
    echo "<h3>Estratos:</h3>";
    $estratos = $controller->obtenerOpciones('estratos');
    echo "✅ Estratos obtenidos: " . count($estratos) . " registros<br>";
    if (count($estratos) > 0) {
        echo "Primer estrato: ID=" . $estratos[0]['id'] . ", Nombre=" . $estratos[0]['nombre'] . "<br>";
    }
    
    echo "<h2>✅ Todas las pruebas completadas exitosamente</h2>";
    
    // Mostrar resumen
    echo "<h3>Resumen de consultas SQL:</h3>";
    echo "<ul>";
    echo "<li><strong>Municipios:</strong> SELECT id_municipio, municipio FROM municipios ORDER BY municipio</li>";
    echo "<li><strong>Tipos de Documentos:</strong> SELECT id, nombre FROM opc_tipo_documentos ORDER BY nombre</li>";
    echo "<li><strong>RH:</strong> SELECT id, nombre FROM opc_rh ORDER BY nombre</li>";
    echo "<li><strong>Estaturas:</strong> SELECT id, nombre FROM opc_estaturas ORDER BY nombre</li>";
    echo "<li><strong>Pesos:</strong> SELECT id, nombre FROM opc_peso ORDER BY nombre</li>";
    echo "<li><strong>Estados Civiles:</strong> SELECT id, nombre FROM opc_estado_civiles ORDER BY nombre</li>";
    echo "<li><strong>Estratos:</strong> SELECT id, nombre FROM opc_estratos ORDER BY nombre</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las pruebas:</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?> 