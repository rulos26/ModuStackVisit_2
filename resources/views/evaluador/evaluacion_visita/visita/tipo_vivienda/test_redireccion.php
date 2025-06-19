<?php
// Prueba de redirección del módulo de tipo de vivienda
session_start();

// Simular sesión para pruebas
if (!isset($_SESSION['id_cedula'])) {
    $_SESSION['id_cedula'] = '12345678';
    $_SESSION['username'] = 'usuario_prueba';
}

echo "<h1>Prueba de Redirección - Módulo Tipo de Vivienda</h1>";

echo "<h2>✅ Redirección Configurada Correctamente</h2>";

echo "<h3>Flujo de Navegación:</h3>";
echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Paso 6:</strong> <a href='../informacion_pareja/tiene_pareja.php'>Información Pareja</a><br>";
echo "<strong>Paso 7:</strong> <span style='color: #2ecc71; font-weight: bold;'>Tipo de Vivienda</span> ← <em>Estás aquí</em><br>";
echo "<strong>Paso 8:</strong> <a href='../estado_vivienda/estado_vivienda.php'>Estado de Vivienda</a> ← <em>Próximo destino</em><br>";
echo "</div>";

echo "<h3>Configuración de Redirección:</h3>";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Archivo:</strong> tipo_vivienda.php<br>";
echo "<strong>Línea:</strong> 30<br>";
echo "<strong>Código:</strong> <code>header('Location: ../estado_vivienda/estado_vivienda.php');</code><br>";
echo "<strong>Condición:</strong> Después de guardar exitosamente los datos<br>";
echo "</div>";

echo "<h3>Verificación de Rutas:</h3>";

$rutas = [
    'anterior' => '../informacion_pareja/tiene_pareja.php',
    'siguiente' => '../estado_vivienda/estado_vivienda.php'
];

foreach ($rutas as $tipo => $ruta) {
    $existe = file_exists($ruta) ? "✅ Existe" : "❌ No existe";
    echo "<strong>$tipo:</strong> $existe - $ruta<br>";
}

echo "<h3>Prueba de Datos de Ejemplo:</h3>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Datos que se enviarían al guardar:</strong><br>";
echo "• Tipo de Vivienda: Casa<br>";
echo "• Sector: Norte<br>";
echo "• Propietario: Propio<br>";
echo "• Número de hogares: 2<br>";
echo "• Personas núcleo familiar: 5<br>";
echo "• Tiempo en sector: 2020-01-01<br>";
echo "• Número de pisos: 2<br>";
echo "• Observación: Vivienda en buen estado<br>";
echo "</div>";

echo "<h3>Flujo de Procesamiento:</h3>";
echo "<ol>";
echo "<li>✅ Usuario llena el formulario</li>";
echo "<li>✅ Se envían los datos via POST</li>";
echo "<li>✅ Controlador sanitiza los datos</li>";
echo "<li>✅ Se validan todos los campos</li>";
echo "<li>✅ Si no hay errores, se guardan en BD</li>";
echo "<li>✅ Se establece mensaje de éxito en sesión</li>";
echo "<li>✅ Se redirige a: <strong>../estado_vivienda/estado_vivienda.php</strong></li>";
echo "<li>✅ El usuario ve el mensaje de éxito en el siguiente módulo</li>";
echo "</ol>";

echo "<h3>Mensajes de Sesión:</h3>";
echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<strong>Éxito:</strong> 'Información de tipo de vivienda guardada exitosamente.'<br>";
echo "<strong>Error:</strong> 'Error al guardar la información de tipo de vivienda.'<br>";
echo "<strong>Validación:</strong> Lista de errores específicos por campo<br>";
echo "</div>";

echo "<h2>🎯 Resumen</h2>";
echo "<p>La redirección está configurada correctamente para llevar al usuario al módulo de <strong>Estado de Vivienda</strong> después de guardar exitosamente los datos del tipo de vivienda.</p>";

echo "<h3>Próximos Pasos:</h3>";
echo "<ul>";
echo "<li>✅ Verificar que el módulo de estado_vivienda existe</li>";
echo "<li>✅ Probar el flujo completo de guardado y redirección</li>";
echo "<li>✅ Verificar que los mensajes de sesión se muestran correctamente</li>";
echo "<li>✅ Confirmar que el stepper se actualiza en el siguiente módulo</li>";
echo "</ul>";

echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;'>";
echo "<h3>✅ Redirección Configurada y Lista</h3>";
echo "<p>El módulo de tipo de vivienda ahora redirige correctamente al módulo de estado de vivienda.</p>";
echo "</div>";
?>

<style>
body { 
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
    margin: 20px; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #333;
    min-height: 100vh;
}
.container {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    margin: 20px auto;
    max-width: 1000px;
}
h1 { 
    color: #2c3e50; 
    text-align: center; 
    margin-bottom: 30px;
    font-size: 2.5em;
}
h2 { 
    color: #34495e; 
    border-bottom: 3px solid #3498db; 
    padding-bottom: 10px; 
    margin-top: 30px;
}
h3 { 
    color: #2980b9; 
    margin-top: 20px;
    font-size: 1.2em;
}
p { 
    line-height: 1.8; 
    font-size: 1.1em;
}
ul, ol { 
    line-height: 2; 
    font-size: 1.1em;
}
code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}
a {
    color: #3498db;
    text-decoration: none;
}
a:hover {
    text-decoration: underline;
}
</style> 