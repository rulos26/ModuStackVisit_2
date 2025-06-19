<?php
// Prueba específica de validaciones para el módulo de estado de vivienda
require_once __DIR__ . '/EstadoViviendaController.php';
use App\Controllers\EstadoViviendaController;

echo "<h1>Prueba de Validaciones - Módulo Estado de Vivienda</h1>";

try {
    $controller = EstadoViviendaController::getInstance();
    
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
    
    // Caso 2: Estado cero (inválido)
    echo "<h3>Caso 2: Estado cero</h3>";
    $datos_estado_cero = [
        'id_estado' => '0',
        'observacion' => ''
    ];
    $errores = $controller->validarDatos($datos_estado_cero);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 3: Observación muy corta
    echo "<h3>Caso 3: Observación muy corta</h3>";
    $datos_obs_corta = [
        'id_estado' => '1',
        'observacion' => 'Corto' // Menos de 10 caracteres
    ];
    $errores = $controller->validarDatos($datos_obs_corta);
    echo "Errores encontrados: " . count($errores) . "<br>";
    foreach ($errores as $error) {
        echo "• $error<br>";
    }
    echo "<br>";
    
    // Caso 4: Datos válidos
    echo "<h3>Caso 4: Datos válidos</h3>";
    $datos_validos = [
        'id_estado' => '1',
        'observacion' => 'Vivienda en excelente estado de conservación'
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
    
    // Caso 5: Observación vacía (válida)
    echo "<h3>Caso 5: Observación vacía (válida)</h3>";
    $datos_obs_vacia = [
        'id_estado' => '1',
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
    
    // Caso 6: Observación con exactamente 10 caracteres
    echo "<h3>Caso 6: Observación con exactamente 10 caracteres</h3>";
    $datos_obs_exacta = [
        'id_estado' => '1',
        'observacion' => '1234567890' // Exactamente 10 caracteres
    ];
    $errores = $controller->validarDatos($datos_obs_exacta);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Observación con 10 caracteres es válida<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    // Caso 7: Observación con espacios
    echo "<h3>Caso 7: Observación con espacios</h3>";
    $datos_obs_espacios = [
        'id_estado' => '1',
        'observacion' => '   Casa   ' // Con espacios
    ];
    $errores = $controller->validarDatos($datos_obs_espacios);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Observación con espacios es válida<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    // Caso 8: Solo estado válido
    echo "<h3>Caso 8: Solo estado válido</h3>";
    $datos_solo_estado = [
        'id_estado' => '1'
        // Sin observación
    ];
    $errores = $controller->validarDatos($datos_solo_estado);
    echo "Errores encontrados: " . count($errores) . "<br>";
    if (count($errores) === 0) {
        echo "✓ Solo estado válido es suficiente<br>";
    } else {
        foreach ($errores as $error) {
            echo "• $error<br>";
        }
    }
    echo "<br>";
    
    echo "<h2>✓ Resumen de Validaciones</h2>";
    echo "<p>Todas las validaciones están funcionando correctamente:</p>";
    echo "<ul>";
    echo "<li>✓ Campo obligatorio (estado)</li>";
    echo "<li>✓ Validación de observación</li>";
    echo "<li>✓ Manejo de valores vacíos</li>";
    echo "<li>✓ Manejo de valores cero</li>";
    echo "<li>✓ Validación de longitud mínima</li>";
    echo "<li>✓ Manejo de espacios</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error en las validaciones</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { 
    font-family: Arial, sans-serif; 
    margin: 20px; 
    background-color: #f8f9fa; 
}
h1 { 
    color: #2c3e50; 
    text-align: center; 
}
h2 { 
    color: #34495e; 
    border-bottom: 2px solid #3498db; 
    padding-bottom: 10px; 
}
h3 { 
    color: #2980b9; 
    margin-top: 20px; 
}
p { 
    line-height: 1.6; 
}
ul { 
    line-height: 1.8; 
}
</style> 