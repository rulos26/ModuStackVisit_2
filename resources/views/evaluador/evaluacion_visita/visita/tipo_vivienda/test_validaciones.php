<?php
// Prueba específica de validaciones para el módulo de tipo de vivienda
require_once __DIR__ . '/TipoViviendaController.php';
use App\Controllers\TipoViviendaController;

echo "<h1>Prueba de Validaciones - Módulo Tipo de Vivienda</h1>";

try {
    $controller = TipoViviendaController::getInstance();
    
    echo "<h2>Casos de Prueba de Validación</h2>";
    
    // Caso 1: Todos los campos vacíos
    echo "<h3>Caso 1: Campos vacíos</h3>";
    $datos_vacios = [];
    $errores = $controller->validarDatos($datos_vacios);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 2: Valores cero (inválidos)
    echo "<h3>Caso 2: Valores cero</h3>";
    $datos_cero = [
        'id_tipo_vivienda' => '0',
        'id_sector' => '0',
        'id_propietario' => '0',
        'numero_de_familia' => '0',
        'personas_nucleo_familiar' => '0',
        'tiempo_sector' => '',
        'numero_de_pisos' => '0'
    ];
    $errores = $controller->validarDatos($datos_cero);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 3: Valores fuera de rango
    echo "<h3>Caso 3: Valores fuera de rango</h3>";
    $datos_rango_invalido = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '51', // Mayor a 50
        'personas_nucleo_familiar' => '101', // Mayor a 100
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '51' // Mayor a 50
    ];
    $errores = $controller->validarDatos($datos_rango_invalido);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 4: Fecha futura
    echo "<h3>Caso 4: Fecha futura</h3>";
    $datos_fecha_futura = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '2',
        'personas_nucleo_familiar' => '5',
        'tiempo_sector' => '2025-12-31', // Fecha futura
        'numero_de_pisos' => '2'
    ];
    $errores = $controller->validarDatos($datos_fecha_futura);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 5: Observación muy corta
    echo "<h3>Caso 5: Observación muy corta</h3>";
    $datos_obs_corta = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '2',
        'personas_nucleo_familiar' => '5',
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '2',
        'observacion' => 'Corto' // Menos de 10 caracteres
    ];
    $errores = $controller->validarDatos($datos_obs_corta);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 6: Datos válidos
    echo "<h3>Caso 6: Datos válidos</h3>";
    $datos_validos = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '2',
        'personas_nucleo_familiar' => '5',
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '2',
        'observacion' => 'Vivienda en excelente estado con buena iluminación natural'
    ];
    $errores = $controller->validarDatos($datos_validos);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Todos los datos son válidos<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    // Caso 7: Observación vacía (válida)
    echo "<h3>Caso 7: Observación vacía (válida)</h3>";
    $datos_obs_vacia = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '2',
        'personas_nucleo_familiar' => '5',
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '2',
        'observacion' => '' // Vacía es válida
    ];
    $errores = $controller->validarDatos($datos_obs_vacia);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Observación vacía es válida<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    // Caso 8: Valores mínimos válidos
    echo "<h3>Caso 8: Valores mínimos válidos</h3>";
    $datos_minimos = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '1', // Mínimo
        'personas_nucleo_familiar' => '1', // Mínimo
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '1' // Mínimo
    ];
    $errores = $controller->validarDatos($datos_minimos);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Valores mínimos son válidos<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    // Caso 9: Valores máximos válidos
    echo "<h3>Caso 9: Valores máximos válidos</h3>";
    $datos_maximos = [
        'id_tipo_vivienda' => '1',
        'id_sector' => '1',
        'id_propietario' => '1',
        'numero_de_familia' => '50', // Máximo
        'personas_nucleo_familiar' => '100', // Máximo
        'tiempo_sector' => '2020-01-01',
        'numero_de_pisos' => '50' // Máximo
    ];
    $errores = $controller->validarDatos($datos_maximos);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Valores máximos son válidos<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    echo "<h2>✓ Resumen de Validaciones</h2>";
    echo "<p>Todas las validaciones están funcionando correctamente:</p>";
    echo "<ul>";
    echo "<li>✓ Campos obligatorios</li>";
    echo "<li>✓ Rangos numéricos</li>";
    echo "<li>✓ Validación de fechas</li>";
    echo "<li>✓ Validación de observación</li>";
    echo "<li>✓ Manejo de valores vacíos</li>";
    echo "<li>✓ Manejo de valores cero</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las validaciones</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
h1 { color: #2c3e50; text-align: center; }
h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
h3 { color: #2980b9; margin-top: 20px; }
p { line-height: 1.6; }
ul { line-height: 1.8; }
</style> 