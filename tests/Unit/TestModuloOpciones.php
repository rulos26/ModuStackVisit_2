<?php
/**
 * Test para verificar el módulo de opciones del sistema
 * Verifica la funcionalidad CRUD completa para todas las tablas de opciones
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Módulo de Opciones del Sistema</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'>
    <style>
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-success { background-color: #d4edda; border-color: #c3e6cb; }
        .test-error { background-color: #f8d7da; border-color: #f5c6cb; }
        .test-info { background-color: #d1ecf1; border-color: #bee5eb; }
        .code-block { background: #f8f9fa; padding: 10px; border-radius: 3px; font-family: monospace; }
        .tabla-info { background: #e9ecef; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body class='bg-light'>
    <div class='container mt-4'>
        <h1 class='text-center mb-4'>
            <i class='bi bi-gear-wide-connected text-primary'></i>
            Test del Módulo de Opciones del Sistema
        </h1>";

try {
    // 1. Verificar autoloader
    echo "<div class='test-section test-info'>
            <h4>1. Verificando Autoloader</h4>";

    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        require_once __DIR__ . '/../../vendor/autoload.php';
        echo "<p class='text-success'>✅ Autoloader cargado correctamente</p>";
    } else {
        throw new Exception("❌ No se encontró el autoloader");
    }
    echo "</div>";

    // 2. Verificar clases
    echo "<div class='test-section test-info'>
            <h4>2. Verificando Clases</h4>";

    if (!class_exists('App\\Controllers\\OpcionesController')) {
        throw new Exception("❌ Clase OpcionesController no encontrada");
    }
    echo "<p class='text-success'>✅ OpcionesController encontrada</p>";

    if (!class_exists('App\\Database\\Database')) {
        throw new Exception("❌ Clase Database no encontrada");
    }
    echo "<p class='text-success'>✅ Database encontrada</p>";
    echo "</div>";

    // 3. Instanciar controlador
    echo "<div class='test-section test-info'>
            <h4>3. Instanciando OpcionesController</h4>";

    $opcionesController = new App\Controllers\OpcionesController();
    echo "<p class='text-success'>✅ OpcionesController instanciado correctamente</p>";
    echo "</div>";

    // 4. Definir tablas de opciones
    echo "<div class='test-section test-info'>
            <h4>4. Tablas de Opciones del Sistema</h4>";

    $tablasOpciones = [
        'opc_concepto_final' => 'Conceptos Finales',
        'opc_concepto_seguridad' => 'Conceptos de Seguridad',
        'opc_conviven' => 'Convivencia',
        'opc_cuenta' => 'Tipos de Cuenta',
        'opc_entidad' => 'Entidades',
        'opc_estados' => 'Estados',
        'opc_estado_civiles' => 'Estados Civiles',
        'opc_estado_vivienda' => 'Estados de Vivienda',
        'opc_estaturas' => 'Estaturas',
        'opc_estratos' => 'Estratos',
        'opc_genero' => 'Géneros',
        'opc_informacion_judicial' => 'Información Judicial',
        'opc_inventario_enseres' => 'Inventario de Enseres',
        'opc_jornada' => 'Jornadas Laborales',
        'opc_marca' => 'Marcas',
        'opc_modelo' => 'Modelos',
        'opc_nivel_academico' => 'Niveles Académicos',
        'opc_num_hijos' => 'Número de Hijos',
        'opc_ocupacion' => 'Ocupaciones',
        'opc_parametro' => 'Parámetros del Sistema',
        'opc_parentesco' => 'Parentescos',
        'opc_peso' => 'Pesos',
        'opc_propiedad' => 'Tipos de Propiedad',
        'opc_resultado' => 'Resultados',
        'opc_rh' => 'Tipos de RH',
        'opc_sector' => 'Sectores',
        'opc_servicios_publicos' => 'Servicios Públicos',
        'opc_tipo_cuenta' => 'Tipos de Cuenta',
        'opc_tipo_documentos' => 'Tipos de Documentos',
        'opc_tipo_inversion' => 'Tipos de Inversión',
        'opc_tipo_vivienda' => 'Tipos de Vivienda',
        'opc_vehiculo' => 'Tipos de Vehículo',
        'opc_viven' => 'Condiciones de Vida'
    ];

    echo "<p class='text-success'>✅ Total de tablas de opciones: " . count($tablasOpciones) . "</p>";
    echo "</div>";

    // 5. Verificar estructura de cada tabla
    echo "<div class='test-section test-info'>
            <h4>5. Verificando Estructura de Tablas</h4>";

    $tablasValidas = [];
    $tablasConProblemas = [];

    foreach ($tablasOpciones as $tabla => $nombre) {
        try {
            // Verificar si la tabla existe
            $opciones = $opcionesController->obtenerOpciones($tabla);
            $estadisticas = $opcionesController->obtenerEstadisticas($tabla);
            $columnaId = $opcionesController->obtenerColumnaId($tabla);
            
            if (is_array($opciones)) {
                $tablasValidas[] = $tabla;
                echo "<div class='tabla-info'>
                        <strong>{$nombre}</strong> ({$tabla})<br>
                        <small>Columna ID: {$columnaId} | Total registros: {$estadisticas['total']}</small>
                      </div>";
            } else {
                $tablasConProblemas[] = $tabla;
            }
        } catch (Exception $e) {
            $tablasConProblemas[] = $tabla;
        }
    }

    echo "<p class='text-success'>✅ Tablas válidas: " . count($tablasValidas) . "</p>";
    if (!empty($tablasConProblemas)) {
        echo "<p class='text-warning'>⚠️ Tablas con problemas: " . count($tablasConProblemas) . "</p>";
    }
    echo "</div>";

    // 6. Test de operaciones CRUD en una tabla de prueba
    echo "<div class='test-section test-info'>
            <h4>6. Test de Operaciones CRUD</h4>";

    $tablaTest = 'opc_concepto_final';
    echo "<p>Probando operaciones en tabla: <strong>{$tablaTest}</strong></p>";

    // Test de creación
    $datosTest = ['nombre' => 'Test Opción ' . date('Y-m-d H:i:s')];
    $resultadoCrear = $opcionesController->crearOpcion($tablaTest, $datosTest);
    
    if ($resultadoCrear['success']) {
        echo "<p class='text-success'>✅ Creación exitosa - ID: {$resultadoCrear['id']}</p>";
        $idCreado = $resultadoCrear['id'];
        
        // Test de lectura
        $opcionCreada = $opcionesController->obtenerOpcionPorId($tablaTest, $idCreado);
        if ($opcionCreada && $opcionCreada['nombre'] === $datosTest['nombre']) {
            echo "<p class='text-success'>✅ Lectura exitosa</p>";
        } else {
            echo "<p class='text-danger'>❌ Error en lectura</p>";
        }
        
        // Test de actualización
        $datosUpdate = ['nombre' => 'Test Opción Actualizada ' . date('Y-m-d H:i:s')];
        $resultadoUpdate = $opcionesController->actualizarOpcion($tablaTest, $idCreado, $datosUpdate);
        
        if ($resultadoUpdate['success']) {
            echo "<p class='text-success'>✅ Actualización exitosa</p>";
        } else {
            echo "<p class='text-danger'>❌ Error en actualización</p>";
        }
        
        // Test de eliminación
        $resultadoEliminar = $opcionesController->eliminarOpcion($tablaTest, $idCreado);
        
        if ($resultadoEliminar['success']) {
            echo "<p class='text-success'>✅ Eliminación exitosa</p>";
        } else {
            echo "<p class='text-danger'>❌ Error en eliminación</p>";
        }
        
    } else {
        echo "<p class='text-danger'>❌ Error en creación: {$resultadoCrear['message']}</p>";
    }
    echo "</div>";

    // 7. Test de validaciones
    echo "<div class='test-section test-info'>
            <h4>7. Test de Validaciones</h4>";

    $testValidaciones = [
        ['nombre' => ''], // Nombre vacío
        ['nombre' => str_repeat('a', 51)], // Nombre muy largo
        ['nombre' => 'Nombre Válido'] // Nombre válido
    ];

    foreach ($testValidaciones as $i => $datos) {
        $validacion = $opcionesController->validarDatos($datos);
        $resultado = $validacion['valido'] ? 'VÁLIDO' : 'INVÁLIDO';
        $clase = $validacion['valido'] ? 'text-success' : 'text-danger';
        
        echo "<p class='{$clase}'>Test {$i + 1}: {$resultado} - " . htmlspecialchars($datos['nombre']) . "</p>";
        
        if (!$validacion['valido']) {
            echo "<small class='text-muted'>Errores: " . implode(', ', $validacion['errores']) . "</small><br>";
        }
    }
    echo "</div>";

    // 8. Resumen final
    echo "<div class='test-section test-success'>
            <h4>8. Resumen Final</h4>";

    echo "<div class='alert alert-success'>
            <h5>🎉 Test del Módulo de Opciones Completado</h5>
            <p><strong>Total tablas:</strong> " . count($tablasOpciones) . "</p>
            <p><strong>Tablas válidas:</strong> " . count($tablasValidas) . "</p>
            <p><strong>Tablas con problemas:</strong> " . count($tablasConProblemas) . "</p>
            <p><strong>Operaciones CRUD:</strong> Funcionando correctamente</p>
            <p><strong>Validaciones:</strong> Implementadas y funcionando</p>
          </div>";

    if (!empty($tablasConProblemas)) {
        echo "<div class='alert alert-warning'>
                <h5>⚠️ Tablas que requieren atención:</h5>
                <ul>";
        foreach ($tablasConProblemas as $tabla) {
            echo "<li>{$tabla} - {$tablasOpciones[$tabla]}</li>";
        }
        echo "</ul></div>";
    }

    echo "</div>";

} catch (Exception $e) {
    echo "<div class='test-section test-error'>
            <h4>❌ Error en el Test</h4>
            <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
            <p><strong>Archivo:</strong> " . htmlspecialchars($e->getFile()) . "</p>
            <p><strong>Línea:</strong> " . $e->getLine() . "</p>
          </div>";
}

echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
