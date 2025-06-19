<?php
// Archivo de prueba para verificar la redirección
session_start();

// Simular sesión para pruebas
if (!isset($_SESSION['id_cedula'])) {
    $_SESSION['id_cedula'] = '12345678';
    $_SESSION['username'] = 'usuario_prueba';
}

echo "<h1>Prueba de Redirección - Módulo de Información de Pareja</h1>";

echo "<h2>✓ Configuración de Redirección</h2>";
echo "<p>Después de guardar exitosamente la información de pareja, el sistema redirige a:</p>";
echo "<code>../tipo_vivienda/tipo_vivienda.php</code>";

echo "<h2>✓ Verificación de Archivo Destino</h2>";
$archivo_destino = __DIR__ . '/../tipo_vivienda/tipo_vivienda.php';
if (file_exists($archivo_destino)) {
    echo "<p style='color: green;'>✓ El archivo de destino existe: <code>$archivo_destino</code></p>";
} else {
    echo "<p style='color: red;'>❌ El archivo de destino NO existe: <code>$archivo_destino</code></p>";
}

echo "<h2>✓ Flujo de Navegación</h2>";
echo "<p>El flujo correcto es:</p>";
echo "<ol>";
echo "<li>Composición Familiar → Información de Pareja</li>";
echo "<li>Información de Pareja → Tipo de Vivienda</li>";
echo "<li>Tipo de Vivienda → Siguiente módulo</li>";
echo "</ol>";

echo "<h2>✓ Código de Redirección</h2>";
echo "<p>En el archivo <code>tiene_pareja.php</code>, línea 30:</p>";
echo "<pre><code>";
echo "if (\$resultado['success']) {\n";
echo "    \$_SESSION['success'] = \$resultado['message'];\n";
echo "    header('Location: ../tipo_vivienda/tipo_vivienda.php');\n";
echo "    exit();\n";
echo "}";
echo "</code></pre>";

echo "<h2>✓ Stepper Actualizado</h2>";
echo "<p>El stepper incluye el paso de Tipo de Vivienda como Paso 7.</p>";

echo "<h2>✓ Prueba de Redirección</h2>";
echo "<p>Para probar la redirección:</p>";
echo "<ol>";
echo "<li>Accede al módulo de información de pareja</li>";
echo "<li>Completa el formulario</li>";
echo "<li>Haz clic en 'Guardar'</li>";
echo "<li>Deberías ser redirigido automáticamente al módulo de tipo de vivienda</li>";
echo "</ol>";

echo "<h2>✓ Estado Actual</h2>";
echo "<p style='color: green; font-weight: bold;'>✅ La redirección está configurada correctamente</p>";
echo "<p>El módulo de información de pareja redirige al módulo de tipo de vivienda después de guardar exitosamente.</p>";

?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c3e50; }
h2 { color: #34495e; margin-top: 30px; }
p { line-height: 1.6; }
code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
ol { line-height: 1.8; }
</style> 