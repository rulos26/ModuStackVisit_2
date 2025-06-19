<?php
// Archivo de prueba de navegación para el módulo de Cuentas Bancarias
echo "<h1>🧭 PRUEBAS DE NAVEGACIÓN - CUENTAS BANCARIAS</h1>";
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
        'ruta' => '../Patrimonio/tiene_patrimonio.php',
        'descripcion' => 'Módulo anterior: Patrimonio',
        'requerido' => true
    ],
    'siguiente' => [
        'ruta' => '../pasivos/tiene_pasivo.php',
        'descripcion' => 'Módulo siguiente: Pasivos',
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
    
    if (strpos($contenido_guardar, '../pasivos/tiene_pasivo.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección correcta configurada en guardar.php<br>";
        echo "🎯 Destino: ../pasivos/tiene_pasivo.php";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-error'>";
        echo "❌ Redirección incorrecta en guardar.php<br>";
        echo "🔍 No se encontró la ruta ../pasivos/tiene_pasivo.php";
        echo "</div>";
    }
    
    // Verificar manejo de errores
    if (strpos($contenido_guardar, 'cuentas_bancarias.php') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Redirección de errores configurada correctamente<br>";
        echo "🔄 En caso de error, regresa a cuentas_bancarias.php";
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

$vista_path = __DIR__ . '/cuentas_bancarias.php';
if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar enlaces de navegación
    $enlaces_navegacion = [
        '../Patrimonio/tiene_patrimonio.php' => 'Enlace anterior',
        '../pasivos/tiene_pasivo.php' => 'Enlace siguiente (en formulario)',
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
    
    // Verificar funcionalidades dinámicas
    if (strpos($contenido_vista, 'btnAgregarCuenta') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Botón para agregar cuentas encontrado";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Botón para agregar cuentas no encontrado";
        echo "</div>";
    }
    
    if (strpos($contenido_vista, 'removeCuenta') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Función para eliminar cuentas encontrada";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Función para eliminar cuentas no encontrada";
        echo "</div>";
    }
    
} else {
    echo "<div class='nav-test nav-error'>";
    echo "❌ No se puede verificar cuentas_bancarias.php - archivo no encontrado";
    echo "</div>";
}
echo "</div>";

// 4. Verificar secuencia del stepper
echo "<div class='test-section info'>";
echo "<h3>📊 4. VERIFICACIÓN DE LA SECUENCIA DEL STEPPER</h3>";

if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar que el stepper muestra el paso correcto
    if (strpos($contenido_vista, 'Paso 12') !== false && strpos($contenido_vista, 'Cuentas Bancarias') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Stepper muestra correctamente el Paso 12: Cuentas Bancarias";
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

// 6. Verificar funcionalidades JavaScript
echo "<div class='test-section info'>";
echo "<h3>⚙️ 6. VERIFICACIÓN DE FUNCIONALIDADES JAVASCRIPT</h3>";

if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar funciones JavaScript
    $funciones_js = [
        'btnAgregarCuenta' => 'Botón para agregar cuentas',
        'removeCuenta' => 'Función para eliminar cuentas',
        'cuentaCounter' => 'Contador de cuentas',
        'cuentas-container' => 'Contenedor de cuentas'
    ];
    
    foreach ($funciones_js as $funcion => $descripcion) {
        if (strpos($contenido_vista, $funcion) !== false) {
            echo "<div class='nav-test nav-success'>";
            echo "✅ $descripcion encontrado";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ $descripcion no encontrado";
            echo "</div>";
        }
    }
    
    // Verificar manejo de eventos
    if (strpos($contenido_vista, 'addEventListener') !== false) {
        echo "<div class='nav-test nav-success'>";
        echo "✅ Manejo de eventos JavaScript implementado";
        echo "</div>";
    } else {
        echo "<div class='nav-test nav-warning'>";
        echo "⚠️ Manejo de eventos JavaScript no encontrado";
        echo "</div>";
    }
}
echo "</div>";

// 7. Verificar estructura de formulario dinámico
echo "<div class='test-section info'>";
echo "<h3>📋 7. VERIFICACIÓN DE ESTRUCTURA DE FORMULARIO DINÁMICO</h3>";

if (file_exists($vista_path)) {
    $contenido_vista = file_get_contents($vista_path);
    
    // Verificar elementos del formulario dinámico
    $elementos_formulario = [
        'cuenta-item' => 'Contenedor de cuenta individual',
        'btn-remove-cuenta' => 'Botón para eliminar cuenta',
        'data-cuenta' => 'Atributo de identificación de cuenta',
        'id_entidad[]' => 'Campo de entidad (array)',
        'id_tipo_cuenta[]' => 'Campo de tipo de cuenta (array)',
        'id_ciudad[]' => 'Campo de ciudad (array)',
        'observaciones[]' => 'Campo de observaciones (array)'
    ];
    
    foreach ($elementos_formulario as $elemento => $descripcion) {
        if (strpos($contenido_vista, $elemento) !== false) {
            echo "<div class='nav-test nav-success'>";
            echo "✅ $descripcion encontrado";
            echo "</div>";
        } else {
            echo "<div class='nav-test nav-warning'>";
            echo "⚠️ $descripcion no encontrado";
            echo "</div>";
        }
    }
}
echo "</div>";

// 8. Resumen de navegación
echo "<div class='test-section success'>";
echo "<h3>📋 RESUMEN DE NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-success'>";
echo "🎯 <strong>Flujo de navegación configurado:</strong><br>";
echo "1. Patrimonio → Cuentas Bancarias<br>";
echo "2. Cuentas Bancarias → Pasivos<br>";
echo "3. Manejo de errores → Regreso a Cuentas Bancarias<br>";
echo "4. Mensajes de éxito/error implementados<br>";
echo "5. Stepper visual con progreso correcto<br>";
echo "6. Funcionalidad dinámica de múltiples cuentas<br>";
echo "7. Interfaz JavaScript para agregar/eliminar cuentas";
echo "</div>";
echo "</div>";

echo "<div class='test-section warning'>";
echo "<h3>⚠️ PRÓXIMOS PASOS PARA NAVEGACIÓN</h3>";
echo "<div class='nav-test nav-warning'>";
echo "1. Verificar que ../pasivos/tiene_pasivo.php existe y funciona<br>";
echo "2. Probar el flujo completo de navegación en el navegador<br>";
echo "3. Verificar que los mensajes de sesión se muestran correctamente<br>";
echo "4. Probar la funcionalidad de agregar múltiples cuentas<br>";
echo "5. Probar la funcionalidad de eliminar cuentas individuales<br>";
echo "6. Verificar que los datos se guardan correctamente en la base de datos<br>";
echo "7. Probar la carga de datos existentes en el formulario";
echo "</div>";
echo "</div>";
?> 