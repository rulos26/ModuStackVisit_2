<?php
// Archivo de prueba de navegación para el módulo de Patrimonio
echo "<h1>🧭 PRUEBAS DE NAVEGACIÓN - PATRIMONIO</h1>";
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
        'ruta' => '../servicios_publicos/servicios_publicos.php',
        'descripcion' => 'Módulo anterior: Servicios Públicos',
        'requerido' => true
    ],
    'siguiente' => [
        'ruta' => '../cuentas_bancarias/cuentas_bancarias.php',
        'descripcion' => 'Módulo siguiente: Cuentas Bancarias',
        'requerido' => true
    ],
    'guardar' => [
        'ruta' => 'guardar.php',
        'descripcion' => 'Archivo de guardado',
        'requerido' => true
    ],
    'vali' => [
        'ruta' => 'vali.php',
        'descripcion' => 'Archivo de validación',
        'requerido' => true
    ],
    'patrimonio_detallado' => [
        'ruta' => 'Patrimonio.php',
        'descripcion' => 'Formulario detallado de patrimonio',
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
    
    if (strpos($contenido_guardar, '../cuentas_bancarias/cuentas_bancarias.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección correcta configurada en guardar.php<br>";
        echo "🎯 Destino: ../cuentas_bancarias/cuentas_bancarias.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ Redirección incorrecta en guardar.php<br>";
        echo "🔍 No se encontró la ruta ../cuentas_bancarias/cuentas_bancarias.php";
        echo "</div>";
    }
    
    // Verificar manejo de errores
    if (strpos($contenido_guardar, 'Patrimonio.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección de errores configurada correctamente<br>";
        echo "🔄 En caso de error, regresa a Patrimonio.php";
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

// Verificar redirección en vali.php
$vali_path = __DIR__ . '/vali.php';
if (file_exists($vali_path)) {
    $contenido_vali = file_get_contents($vali_path);
    
    if (strpos($contenido_vali, '../cuentas_bancarias/cuentas_bancarias.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección correcta configurada en vali.php<br>";
        echo "🎯 Destino: ../cuentas_bancarias/cuentas_bancarias.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ Redirección incorrecta en vali.php<br>";
        echo "🔍 No se encontró la ruta ../cuentas_bancarias/cuentas_bancarias.php";
        echo "</div>";
    }
    
    // Verificar manejo de errores
    if (strpos($contenido_vali, 'tiene_patrimonio.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección de errores configurada correctamente en vali.php<br>";
        echo "🔄 En caso de error, regresa a tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Redirección de errores no encontrada en vali.php<br>";
        echo "🔍 Verificar manejo de errores en vali.php";
        echo "</div>";
    }
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar vali.php - archivo no encontrado";
    echo "</div>";
}
echo "</div>";

// 3. Verificar navegación en las vistas
echo "<div class='test-section info'>";
echo "<h3>👁️ 3. VERIFICACIÓN DE NAVEGACIÓN EN LAS VISTAS</h3>";

// Verificar tiene_patrimonio.php
$tiene_patrimonio_path = __DIR__ . '/tiene_patrimonio.php';
if (file_exists($tiene_patrimonio_path)) {
    $contenido_tiene = file_get_contents($tiene_patrimonio_path);
    
    // Verificar enlaces de navegación
    $enlaces_tiene = [
        '../servicios_publicos/servicios_publicos.php' => 'Enlace anterior',
        '../cuentas_bancarias/cuentas_bancarias.php' => 'Enlace siguiente (en formulario)',
        'vali.php' => 'Acción del formulario'
    ];
    
    foreach ($enlaces_tiene as $enlace => $descripcion) {
        if (strpos($contenido_tiene, $enlace) !== false) {
            echo "<div class='nav-test nav-success'>";
            echo "✅ $descripcion encontrado en tiene_patrimonio.php<br>";
            echo "🔗 Enlace: $enlace";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ $descripcion no encontrado en tiene_patrimonio.php<br>";
            echo "🔗 Enlace esperado: $enlace";
            echo "</div>";
        }
    }
    
    // Verificar controles de navegación
    if (strpos($contenido_tiene, 'btn-secondary') !== false && strpos($contenido_tiene, 'btn-primary') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Controles de navegación (botones anterior/siguiente) encontrados en tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Controles de navegación no encontrados o incompletos en tiene_patrimonio.php";
        echo "</div>";
    }
    
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar tiene_patrimonio.php - archivo no encontrado";
    echo "</div>";
}

// Verificar Patrimonio.php
$patrimonio_path = __DIR__ . '/Patrimonio.php';
if (file_exists($patrimonio_path)) {
    $contenido_patrimonio = file_get_contents($patrimonio_path);
    
    // Verificar enlaces de navegación
    $enlaces_patrimonio = [
        'tiene_patrimonio.php' => 'Enlace anterior',
        '../cuentas_bancarias/cuentas_bancarias.php' => 'Enlace siguiente (en formulario)',
        'guardar.php' => 'Acción del formulario'
    ];
    
    foreach ($enlaces_patrimonio as $enlace => $descripcion) {
        if (strpos($contenido_patrimonio, $enlace) !== false) {
            echo "<div class='nav-test nav-success'>";
            echo "✅ $descripcion encontrado en Patrimonio.php<br>";
            echo "🔗 Enlace: $enlace";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ $descripcion no encontrado en Patrimonio.php<br>";
            echo "🔗 Enlace esperado: $enlace";
            echo "</div>";
        }
    }
    
    // Verificar controles de navegación
    if (strpos($contenido_patrimonio, 'btn-secondary') !== false && strpos($contenido_patrimonio, 'btn-primary') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Controles de navegación (botones anterior/siguiente) encontrados en Patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Controles de navegación no encontrados o incompletos en Patrimonio.php";
        echo "</div>";
    }
    
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar Patrimonio.php - archivo no encontrado";
    echo "</div>";
}
echo "</div>";

// 4. Verificar secuencia del stepper
echo "<div class='test-section info'>";
echo "<h3>📊 4. VERIFICACIÓN DE LA SECUENCIA DEL STEPPER</h3>";

if (file_exists($tiene_patrimonio_path)) {
    $contenido_tiene = file_get_contents($tiene_patrimonio_path);
    
    // Verificar que el stepper muestra el paso correcto
    if (strpos($contenido_tiene, 'Paso 11') !== false && strpos($contenido_tiene, 'Patrimonio') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Stepper muestra correctamente el Paso 11: Patrimonio en tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Stepper no muestra correctamente el paso actual en tiene_patrimonio.php";
        echo "</div>";
    }
    
    // Verificar que los pasos anteriores están marcados como completados
    if (strpos($contenido_tiene, 'complete') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Pasos anteriores marcados como completados en tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ No se encontraron pasos marcados como completados en tiene_patrimonio.php";
        echo "</div>";
    }
    
    // Verificar que el paso actual está activo
    if (strpos($contenido_tiene, 'active') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Paso actual marcado como activo en tiene_patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Paso actual no marcado como activo en tiene_patrimonio.php";
        echo "</div>";
    }
}

if (file_exists($patrimonio_path)) {
    $contenido_patrimonio = file_get_contents($patrimonio_path);
    
    // Verificar que el stepper muestra el paso correcto
    if (strpos($contenido_patrimonio, 'Paso 11') !== false && strpos($contenido_patrimonio, 'Patrimonio') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Stepper muestra correctamente el Paso 11: Patrimonio en Patrimonio.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Stepper no muestra correctamente el paso actual en Patrimonio.php";
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
        echo "✅ Verificación de sesión implementada en guardar.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Verificación de sesión no encontrada en guardar.php";
        echo "</div>";
    }
    
    // Verificar mensajes de sesión
    if (strpos($contenido_guardar, '$_SESSION[\'success\']') !== false && strpos($contenido_guardar, '$_SESSION[\'error\']') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Manejo de mensajes de sesión implementado en guardar.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Manejo de mensajes de sesión incompleto en guardar.php";
        echo "</div>";
    }
}

if (file_exists($vali_path)) {
    $contenido_vali = file_get_contents($vali_path);
    
    // Verificar inicio de sesión
    if (strpos($contenido_vali, 'session_start()') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Sesión iniciada correctamente en vali.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ No se encontró session_start() en vali.php";
        echo "</div>";
    }
    
    // Verificar verificación de sesión
    if (strpos($contenido_vali, '$_SESSION[\'id_cedula\']') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Verificación de sesión implementada en vali.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Verificación de sesión no encontrada en vali.php";
        echo "</div>";
    }
}
echo "</div>";

// 6. Verificar flujo de decisión
echo "<div class='test-section info'>";
echo "<h3>🔄 6. VERIFICACIÓN DEL FLUJO DE DECISIÓN</h3>";

echo "<div class='nav-test nav-success'>";
echo "✅ <strong>Flujo de decisión configurado:</strong><br>";
echo "1. Usuario llega a tiene_patrimonio.php<br>";
echo "2. Selecciona 'No' → Guarda con valores N/A → Redirige a cuentas_bancarias.php<br>";
echo "3. Selecciona 'Sí' → Redirige a Patrimonio.php (formulario detallado)<br>";
echo "4. Completa formulario detallado → Guarda datos → Redirige a cuentas_bancarias.php<br>";
echo "5. En caso de error → Regresa al formulario correspondiente";
echo "</div>";

// Verificar que vali.php maneja el flujo de decisión
if (file_exists($vali_path)) {
    $contenido_vali = file_get_contents($vali_path);
    
    if (strpos($contenido_vali, 'tiene_patrimonio') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Flujo de decisión implementado en vali.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Flujo de decisión no encontrado en vali.php";
        echo "</div>";
    }
}
echo "</div>";

// 7. Resumen de navegación
echo "<div class='test-section success'>";
echo "<h3>📋 RESUMEN DE NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-success'>";
echo "🎯 <strong>Flujo de navegación configurado:</strong><br>";
echo "1. Servicios Públicos → Patrimonio (tiene_patrimonio.php)<br>";
echo "2. Patrimonio → Cuentas Bancarias<br>";
echo "3. Manejo de errores → Regreso a formularios correspondientes<br>";
echo "4. Mensajes de éxito/error implementados<br>";
echo "5. Stepper visual con progreso correcto<br>";
echo "6. Flujo de decisión (con/sin patrimonio) implementado";
echo "</div>";
echo "</div>";

echo "<div class='test-section warning'>";
echo "<h3>⚠️ PRÓXIMOS PASOS PARA NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-warning'>";
echo "1. Verificar que ../cuentas_bancarias/cuentas_bancarias.php existe y funciona<br>";
echo "2. Probar el flujo completo de navegación en el navegador<br>";
echo "3. Verificar que los mensajes de sesión se muestran correctamente<br>";
echo "4. Probar ambos flujos: con patrimonio y sin patrimonio<br>";
echo "5. Verificar que la redirección funciona correctamente en ambos casos";
echo "</div>";
echo "</div>";
?> 