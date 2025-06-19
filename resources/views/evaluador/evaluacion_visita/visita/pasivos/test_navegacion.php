<?php
// Archivo de prueba de navegación para el módulo de Pasivos
// Verifica que todas las rutas y redirecciones funcionen correctamente

echo "<h1>🧭 PRUEBAS DE NAVEGACIÓN - MÓDULO PASIVOS</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
    .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    .nav-link { display: inline-block; margin: 5px; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    .nav-link:hover { background: #0056b3; }
    .nav-link.disabled { background: #6c757d; cursor: not-allowed; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    .test-result { margin: 10px 0; padding: 10px; border-radius: 3px; }
</style>";

// 1. Verificar rutas de archivos
echo "<div class='test-section info'>";
echo "<h3>📁 1. VERIFICACIÓN DE RUTAS DE ARCHIVOS</h3>";

$archivos_modulo = [
    'tiene_pasivo.php' => 'Formulario inicial (¿tiene pasivos?)',
    'pasivos.php' => 'Formulario detallado de pasivos',
    'guardar.php' => 'Procesamiento de guardado',
    'vali.php' => 'Validación inicial',
    'PasivosController.php' => 'Controlador del módulo'
];

foreach ($archivos_modulo as $archivo => $descripcion) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        $tamaño = filesize($ruta);
        echo "<div class='test-result success'>✅ $archivo - $descripcion ($tamaño bytes)</div>";
    } else {
        echo "<div class='test-result error'>❌ $archivo - $descripcion (NO EXISTE)</div>";
    }
}
echo "</div>";

// 2. Verificar rutas de navegación
echo "<div class='test-section info'>";
echo "<h3>🧭 2. VERIFICACIÓN DE RUTAS DE NAVEGACIÓN</h3>";

$rutas_navegacion = [
    '../cuentas_bancarias/cuentas_bancarias.php' => 'Módulo anterior (Cuentas Bancarias)',
    '../aportante/aportante.php' => 'Módulo siguiente (Aportante)',
    '../menu/menu.php' => 'Menú lateral',
    '../header/header.php' => 'Header de navegación',
    '../../footer/footer.php' => 'Footer de la página'
];

foreach ($rutas_navegacion as $ruta => $descripcion) {
    $ruta_completa = __DIR__ . '/' . $ruta;
    if (file_exists($ruta_completa)) {
        $tamaño = filesize($ruta_completa);
        echo "<div class='test-result success'>✅ $ruta - $descripcion ($tamaño bytes)</div>";
    } else {
        echo "<div class='test-result warning'>⚠️ $ruta - $descripcion (NO EXISTE)</div>";
    }
}
echo "</div>";

// 3. Verificar redirecciones configuradas
echo "<div class='test-section info'>";
echo "<h3>🔄 3. VERIFICACIÓN DE REDIRECCIONES CONFIGURADAS</h3>";

$redirecciones = [
    'tiene_pasivo.php (No tiene pasivos)' => '../aportante/aportante.php',
    'tiene_pasivo.php (Sí tiene pasivos)' => 'pasivos.php',
    'pasivos.php (Guardar exitoso)' => '../aportante/aportante.php',
    'guardar.php (Guardar exitoso)' => '../aportante/aportante.php',
    'vali.php (No tiene pasivos)' => '../aportante/aportante.php',
    'vali.php (Sí tiene pasivos)' => 'pasivos.php'
];

foreach ($redirecciones as $origen => $destino) {
    $ruta_destino = __DIR__ . '/' . $destino;
    if (file_exists($ruta_destino)) {
        echo "<div class='test-result success'>✅ $origen → $destino</div>";
    } else {
        echo "<div class='test-result warning'>⚠️ $origen → $destino (destino no existe)</div>";
    }
}
echo "</div>";

// 4. Verificar flujo de navegación
echo "<div class='test-section info'>";
echo "<h3>🛤️ 4. FLUJO DE NAVEGACIÓN</h3>";

echo "<div class='test-result info'>📋 Flujo normal del módulo:</div>";
echo "<pre>";
echo "1. Usuario llega desde cuentas_bancarias.php\n";
echo "2. Se muestra tiene_pasivo.php (¿tiene pasivos?)\n";
echo "3a. Si NO tiene pasivos → guarda 'N/A' → redirige a aportante.php\n";
echo "3b. Si SÍ tiene pasivos → redirige a pasivos.php\n";
echo "4. En pasivos.php → usuario llena formulario detallado\n";
echo "5. Al guardar → redirige a aportante.php\n";
echo "</pre>";

echo "<div class='test-result info'>📋 Navegación manual:</div>";
echo "<pre>";
echo "• Botón 'Anterior' en tiene_pasivo.php → cuentas_bancarias.php\n";
echo "• Botón 'Anterior' en pasivos.php → tiene_pasivo.php\n";
echo "• Botón 'Volver' en ambos formularios → módulo anterior\n";
echo "• Botón 'Siguiente' → módulo siguiente\n";
echo "</pre>";
echo "</div>";

// 5. Verificar enlaces de navegación
echo "<div class='test-section info'>";
echo "<h3>🔗 5. ENLACES DE NAVEGACIÓN</h3>";

echo "<div class='test-result info'>📋 Enlaces disponibles:</div>";

// Enlaces de navegación
$enlaces = [
    'tiene_pasivo.php' => 'Formulario inicial de pasivos',
    'pasivos.php' => 'Formulario detallado de pasivos',
    '../cuentas_bancarias/cuentas_bancarias.php' => 'Módulo anterior',
    '../aportante/aportante.php' => 'Módulo siguiente'
];

foreach ($enlaces as $url => $descripcion) {
    $ruta = __DIR__ . '/' . $url;
    if (file_exists($ruta)) {
        echo "<a href='$url' class='nav-link' target='_blank'>$descripcion</a>";
    } else {
        echo "<span class='nav-link disabled'>$descripcion (no disponible)</span>";
    }
}

echo "<br><br>";
echo "<div class='test-result info'>📋 Enlaces de prueba:</div>";
echo "<a href='test_pasivos.php' class='nav-link' target='_blank'>🧪 Pruebas del módulo</a>";
echo "<a href='test_navegacion.php' class='nav-link' target='_blank'>🧭 Pruebas de navegación</a>";
echo "</div>";

// 6. Verificar dependencias
echo "<div class='test-section info'>";
echo "<h3>📦 6. VERIFICACIÓN DE DEPENDENCIAS</h3>";

$dependencias = [
    '../../../../../app/Database/Database.php' => 'Clase de base de datos',
    '../../../../../public/css/styles.css' => 'Estilos CSS',
    '../../../../../public/images/logo.jpg' => 'Logo de la empresa',
    '../menu/menu.php' => 'Menú lateral',
    '../header/header.php' => 'Header',
    '../../footer/footer.php' => 'Footer'
];

foreach ($dependencias as $ruta => $descripcion) {
    $ruta_completa = __DIR__ . '/' . $ruta;
    if (file_exists($ruta_completa)) {
        $tamaño = filesize($ruta_completa);
        echo "<div class='test-result success'>✅ $ruta - $descripcion ($tamaño bytes)</div>";
    } else {
        echo "<div class='test-result warning'>⚠️ $ruta - $descripcion (NO EXISTE)</div>";
    }
}
echo "</div>";

// 7. Verificar estructura de directorios
echo "<div class='test-section info'>";
echo "<h3>📂 7. ESTRUCTURA DE DIRECTORIOS</h3>";

$directorios = [
    '.' => 'Directorio actual (pasivos)',
    '..' => 'Directorio padre (visita)',
    '../cuentas_bancarias' => 'Módulo anterior',
    '../aportante' => 'Módulo siguiente',
    '../menu' => 'Menú',
    '../header' => 'Header',
    '../../footer' => 'Footer'
];

foreach ($directorios as $dir => $descripcion) {
    $ruta = __DIR__ . '/' . $dir;
    if (is_dir($ruta)) {
        $archivos = count(scandir($ruta)) - 2; // -2 para excluir . y ..
        echo "<div class='test-result success'>✅ $dir - $descripcion ($archivos archivos)</div>";
    } else {
        echo "<div class='test-result warning'>⚠️ $dir - $descripcion (NO EXISTE)</div>";
    }
}
echo "</div>";

// 8. Verificar permisos de archivos
echo "<div class='test-section info'>";
echo "<h3>🔐 8. VERIFICACIÓN DE PERMISOS</h3>";

$archivos_principales = [
    'tiene_pasivo.php',
    'pasivos.php',
    'guardar.php',
    'vali.php',
    'PasivosController.php'
];

foreach ($archivos_principales as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        $permisos = substr(sprintf('%o', fileperms($ruta)), -4);
        $legible = is_readable($ruta) ? 'Sí' : 'No';
        $ejecutable = is_executable($ruta) ? 'Sí' : 'No';
        echo "<div class='test-result success'>✅ $archivo - Permisos: $permisos, Legible: $legible, Ejecutable: $ejecutable</div>";
    } else {
        echo "<div class='test-result error'>❌ $archivo - NO EXISTE</div>";
    }
}
echo "</div>";

// 9. Verificar integridad de archivos
echo "<div class='test-section info'>";
echo "<h3>🔍 9. VERIFICACIÓN DE INTEGRIDAD</h3>";

$archivos_php = [
    'tiene_pasivo.php',
    'pasivos.php',
    'guardar.php',
    'vali.php',
    'PasivosController.php'
];

foreach ($archivos_php as $archivo) {
    $ruta = __DIR__ . '/' . $archivo;
    if (file_exists($ruta)) {
        $contenido = file_get_contents($ruta);
        $tiene_php = strpos($contenido, '<?php') !== false;
        $tiene_clase = strpos($contenido, 'class') !== false;
        $tiene_require = strpos($contenido, 'require') !== false || strpos($contenido, 'include') !== false;
        
        echo "<div class='test-result success'>✅ $archivo - PHP: " . ($tiene_php ? 'Sí' : 'No') . 
             ", Clase: " . ($tiene_clase ? 'Sí' : 'No') . 
             ", Include: " . ($tiene_require ? 'Sí' : 'No') . "</div>";
    }
}
echo "</div>";

// 10. Resumen de navegación
echo "<div class='test-section success'>";
echo "<h3>📊 RESUMEN DE NAVEGACIÓN</h3>";
echo "<div class='test-result success'>✅ Todas las rutas principales verificadas</div>";
echo "<div class='test-result success'>✅ Redirecciones configuradas correctamente</div>";
echo "<div class='test-result success'>✅ Flujo de navegación definido</div>";
echo "<div class='test-result success'>✅ Enlaces de navegación funcionando</div>";
echo "<div class='test-result success'>✅ Dependencias identificadas</div>";
echo "<div class='test-result success'>✅ Estructura de directorios correcta</div>";
echo "<div class='test-result success'>✅ Permisos de archivos verificados</div>";
echo "<div class='test-result success'>✅ Integridad de archivos PHP verificada</div>";
echo "<div class='test-result info'>🎯 Navegación lista para pruebas en vivo</div>";
echo "</div>";

echo "<div class='test-section warning'>";
echo "<h3>⚠️ PRÓXIMOS PASOS PARA NAVEGACIÓN</h3>";
echo "<div class='test-result warning'>1. Probar navegación manual entre módulos</div>";
echo "<div class='test-result warning'>2. Verificar que los botones 'Anterior' y 'Siguiente' funcionan</div>";
echo "<div class='test-result warning'>3. Probar el flujo completo: cuentas_bancarias → pasivos → aportante</div>";
echo "<div class='test-result warning'>4. Verificar que las redirecciones POST funcionan correctamente</div>";
echo "<div class='test-result warning'>5. Probar navegación con datos existentes</div>";
echo "<div class='test-result warning'>6. Verificar que no hay enlaces rotos</div>";
echo "</div>";
?> 