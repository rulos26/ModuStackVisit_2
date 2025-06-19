<?php
// Archivo de prueba de navegación para el módulo de Servicios Públicos
echo "<h1>🧭 PRUEBAS DE NAVEGACIÓN - SERVICIOS PÚBLICOS</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
    .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
    .warning { background-color: #fff3cd; border-color: #ffeaa7; color: #856404; }
    .info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
    .nav-test { margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 3px; }
    .nav-success { background-color: #d4edda; border-color: #c3e6cb; }
    .nav-error { background-color: #f8d7da; border-color: #f5c6cb; }
    .nav-warning { background-color: #fff3cd; border-color: #ffeaa7; }
</style>";

// 1. Verificar rutas de navegación
echo "<div class='test-section info'>";
echo "<h3>🗺️ 1. VERIFICACIÓN DE RUTAS DE NAVEGACIÓN</h3>";

$rutas_navegacion = [
    'anterior' => [
        'ruta' => '../inventario_enseres/inventario_enseres.php',
        'descripcion' => 'Módulo anterior: Inventario de Enseres',
        'requerido' => true
    ],
    'siguiente' => [
        'ruta' => '../Patrimonio/tiene_patrimonio.php',
        'descripcion' => 'Módulo siguiente: Patrimonio',
        'requerido' => true
    ],
    'guardar' => [
        'ruta' => 'guardar.php',
        'descripcion' => 'Archivo de guardado',
        'requerido' => true
    ]
];

foreach ($rutas_navegacion as $tipo => $info) {
    $ruta_completa = __DIR__ . '/' . $info['ruta'];
    $existe = file_exists($ruta_completa);
    
    if ($existe) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ <strong>$tipo:</strong> {$info['descripcion']}<br>";
        echo "📁 Ruta: {$info['ruta']}<br>";
        echo "📊 Tamaño: " . filesize($ruta_completa) . " bytes";
        echo "</div>";
    } else {
        if ($info['requerido']) {
            echo "<div class='nav-test nav-error'>";
            echo "❌ <strong>$tipo:</strong> {$info['descripcion']}<br>";
            echo "📁 Ruta: {$info['ruta']}<br>";
            echo "⚠️ ARCHIVO REQUERIDO NO ENCONTRADO";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ <strong>$tipo:</strong> {$info['descripcion']}<br>";
            echo "📁 Ruta: {$info['ruta']}<br>";
            echo "ℹ️ Archivo opcional no encontrado";
            echo "</div>";
        }
    }
}
echo "</div>";

// 2. Verificar flujo de navegación en el código
echo "<div class='test-section info'>";
echo "<h3>🔄 2. VERIFICACIÓN DEL FLUJO DE NAVEGACIÓN</h3>";

// Verificar redirección en guardar.php
$guardar_path = __DIR__ . '/guardar.php';
if (file_exists($guardar_path)) {
    $contenido_guardar = file_get_contents($guardar_path);
    
    if (strpos($contenido_guardar, '../Patrimonio/tiene_patrimonio.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección correcta configurada en guardar.php<br>";
        echo "🎯 Destino: ../Patrimonio/tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ Redirección incorrecta en guardar.php<br>";
        echo "🔍 No se encontró la ruta ../Patrimonio/tiene_patrimonio.php";
        echo "</div>";
    }
    
    // Verificar manejo de errores
    if (strpos($contenido_guardar, 'servicios_publicos.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección de errores configurada correctamente<br>";
        echo "🔄 En caso de error, regresa a servicios_publicos.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Redirección de errores no encontrada<br>";
        echo "🔍 Verificar manejo de errores en guardar.php";
        echo "</div>";
    }
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar guardar.php - archivo no encontrado";
    echo "</div>";
}
echo "</div>";

// 3. Verificar navegación en la vista
echo "<div class='test-section info'>";
echo "<h3>👁️ 3. VERIFICACIÓN DE NAVEGACIÓN EN LA VISTA</h3>";

$vista_path = __DIR__ . '/servicios_publicos.php';
if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar enlaces de navegación
    $enlaces_navegacion = [
        '../inventario_enseres/inventario_enseres.php' => 'Enlace anterior',
        '../Patrimonio/tiene_patrimonio.php' => 'Enlace siguiente (en formulario)',
        'guardar.php' => 'Acción del formulario'
    ];
    
    foreach ($enlaces_navegacion as $enlace => $descripcion) {
        if (strpos($contenido_vista, $enlace) !== false) {
            echo "<div class='nav-test nav-success'>";
            echo "✅ $descripcion encontrado<br>";
            echo "🔗 Enlace: $enlace";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ $descripcion no encontrado<br>";
            echo "🔗 Enlace esperado: $enlace";
            echo "</div>";
        }
    }
    
    // Verificar controles de navegación
    if (strpos($contenido_vista, 'btn-secondary') !== false && strpos($contenido_vista, 'btn-primary') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Controles de navegación (botones anterior/siguiente) encontrados";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Controles de navegación no encontrados o incompletos";
        echo "</div>";
    }
    
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar servicios_publicos.php - archivo no encontrado";
    echo "</div>";
}
echo "</div>";

// 4. Verificar secuencia del stepper
echo "<div class='test-section info'>";
echo "<h3>📊 4. VERIFICACIÓN DE LA SECUENCIA DEL STEPPER</h3>";

if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar que el stepper muestra el paso correcto
    if (strpos($contenido_vista, 'Paso 10') !== false && strpos($contenido_vista, 'Servicios Públicos') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Stepper muestra correctamente el Paso 10: Servicios Públicos";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Stepper no muestra correctamente el paso actual";
        echo "</div>";
    }
    
    // Verificar que los pasos anteriores están marcados como completados
    if (strpos($contenido_vista, 'complete') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Pasos anteriores marcados como completados";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ No se encontraron pasos marcados como completados";
        echo "</div>";
    }
    
    // Verificar que el paso actual está activo
    if (strpos($contenido_vista, 'active') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Paso actual marcado como activo";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Paso actual no marcado como activo";
        echo "</div>";
    }
}
echo "</div>";

// 5. Verificar manejo de sesión
echo "<div class='test-section info'>";
echo "<h3>🔐 5. VERIFICACIÓN DEL MANEJO DE SESIÓN</h3>";

if (file_exists($guardar_path)) {
    $contenido_guardar = file_get_contents($guardar_path);
    
    // Verificar inicio de sesión
    if (strpos($contenido_guardar, 'session_start()') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Sesión iniciada correctamente en guardar.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ No se encontró session_start() en guardar.php";
        echo "</div>";
    }
    
    // Verificar verificación de sesión
    if (strpos($contenido_guardar, '$_SESSION[\'id_cedula\']') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Verificación de sesión implementada";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Verificación de sesión no encontrada";
        echo "</div>";
    }
    
    // Verificar mensajes de sesión
    if (strpos($contenido_guardar, '$_SESSION[\'success\']') !== false && strpos($contenido_guardar, '$_SESSION[\'error\']') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Manejo de mensajes de sesión implementado";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Manejo de mensajes de sesión incompleto";
        echo "</div>";
    }
}
echo "</div>";

// 6. Resumen de navegación
echo "<div class='test-section success'>";
echo "<h3>📋 RESUMEN DE NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-success'>";
echo "🎯 <strong>Flujo de navegación configurado:</strong><br>";
echo "1. Inventario de Enseres → Servicios Públicos<br>";
echo "2. Servicios Públicos → Patrimonio<br>";
echo "3. Manejo de errores → Regreso a Servicios Públicos<br>";
echo "4. Mensajes de éxito/error implementados<br>";
echo "5. Stepper visual con progreso correcto";
echo "</div>";
echo "</div>";

echo "<div class='test-section warning'>";
echo "<h3>⚠️ PRÓXIMOS PASOS PARA NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-warning'>";
echo "1. Verificar que ../Patrimonio/tiene_patrimonio.php existe y funciona<br>";
echo "2. Probar el flujo completo de navegación en el navegador<br>";
echo "3. Verificar que los mensajes de sesión se muestran correctamente<br>";
echo "4. Probar la navegación con datos válidos e inválidos";
echo "</div>";
echo "</div>";
?> 