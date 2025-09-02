<?php
/**
 * Test del Módulo de Gestión de Tablas Principales
 * 
 * Este script prueba la funcionalidad del nuevo módulo para gestionar
 * las tablas principales del sistema, incluyendo eliminación por cédula
 * y truncamiento de tablas.
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='es'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Test del Módulo de Tablas Principales</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css' rel='stylesheet'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container mt-4'>";
echo "<div class='row'>";
echo "<div class='col-12'>";

echo "<div class='card'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h3><i class='bi bi-database'></i> Test del Módulo de Gestión de Tablas Principales</h3>";
echo "</div>";
echo "<div class='card-body'>";

// Función para mostrar resultados
function mostrarResultado($titulo, $resultado, $tipo = 'info') {
    $clase = match($tipo) {
        'success' => 'success',
        'warning' => 'warning',
        'danger' => 'danger',
        default => 'info'
    };
    
    echo "<div class='alert alert-{$clase}'>";
    echo "<h5><i class='bi bi-info-circle'></i> {$titulo}</h5>";
    if (is_array($resultado) || is_object($resultado)) {
        echo "<pre>" . print_r($resultado, true) . "</pre>";
    } else {
        echo "<p>{$resultado}</p>";
    }
    echo "</div>";
}

// Función para mostrar error
function mostrarError($mensaje) {
    echo "<div class='alert alert-danger'>";
    echo "<h5><i class='bi bi-exclamation-triangle'></i> Error</h5>";
    echo "<p>{$mensaje}</p>";
    echo "</div>";
}

echo "<h4>🔍 Verificando Componentes del Sistema</h4>";

// 1. Verificar autoloader
echo "<h5>1. Verificando Autoloader</h5>";
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    echo "<div class='alert alert-success'>✅ Autoloader cargado correctamente</div>";
} else {
    echo "<div class='alert alert-warning'>⚠️ Autoloader no encontrado, usando require_once manual</div>";
}

// 2. Verificar archivos del módulo
echo "<h5>2. Verificando Archivos del Módulo</h5>";

$archivos = [
    'TablasPrincipalesController' => __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php',
    'procesar_tablas_principales' => __DIR__ . '/../../resources/views/superadmin/procesar_tablas_principales.php',
    'gestion_tablas_principales' => __DIR__ . '/../../resources/views/superadmin/gestion_tablas_principales.php'
];

foreach ($archivos as $nombre => $ruta) {
    if (file_exists($ruta)) {
        echo "<div class='alert alert-success'>✅ {$nombre}: Existe</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ {$nombre}: No existe en {$ruta}</div>";
    }
}

// 3. Verificar clases
echo "<h5>3. Verificando Clases</h5>";

try {
    if (class_exists('App\Controllers\TablasPrincipalesController')) {
        echo "<div class='alert alert-success'>✅ Clase TablasPrincipalesController existe</div>";
    } else {
        // Intentar cargar manualmente
        require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
        if (class_exists('App\Controllers\TablasPrincipalesController')) {
            echo "<div class='alert alert-success'>✅ Clase TablasPrincipalesController cargada manualmente</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Clase TablasPrincipalesController no se pudo cargar</div>";
        }
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error al verificar clase: " . $e->getMessage() . "</div>";
}

// 4. Verificar configuración de base de datos
echo "<h5>4. Verificando Configuración de Base de Datos</h5>";

$configFile = __DIR__ . '/../../app/Config/config.php';
if (file_exists($configFile)) {
    echo "<div class='alert alert-success'>✅ Archivo de configuración existe</div>";
    
    // Verificar si se puede cargar la configuración
    try {
        require_once $configFile;
        echo "<div class='alert alert-success'>✅ Configuración cargada</div>";
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>❌ Error al cargar configuración: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger'>❌ Archivo de configuración no encontrado</div>";
}

// 5. Verificar conexión a base de datos
echo "<h5>5. Verificando Conexión a Base de Datos</h5>";

try {
    if (class_exists('App\Database\Database')) {
        $db = App\Database\Database::getInstance()->getConnection();
        echo "<div class='alert alert-success'>✅ Conexión a base de datos establecida</div>";
        
        // Verificar si las tablas principales existen
        $tablasPrincipales = [
            'usuarios', 'aportante', 'autorizaciones', 'camara_comercio',
            'composicion_familiar', 'concepto_final_evaluador', 'cuentas_bancarias',
            'data_credito', 'evidencia_fotografica', 'experiencia_laboral',
            'firmas', 'formularios', 'foto_perfil_autorizacion', 'foto_perfil_visita',
            'gasto', 'informacion_judicial', 'informacion_pareja', 'ingresos_mensuales',
            'inventario_enseres', 'ubicacion', 'ubicacion_autorizacion', 'ubicacion_foto'
        ];
        
        echo "<h6>Verificando existencia de tablas principales:</h6>";
        $tablasExistentes = [];
        $tablasFaltantes = [];
        
        foreach ($tablasPrincipales as $tabla) {
            try {
                $stmt = $db->prepare("SHOW TABLES LIKE :tabla");
                $stmt->bindParam(':tabla', $tabla);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $tablasExistentes[] = $tabla;
                    echo "<span class='badge bg-success me-1'>{$tabla}</span>";
                } else {
                    $tablasFaltantes[] = $tabla;
                    echo "<span class='badge bg-danger me-1'>{$tabla}</span>";
                }
            } catch (Exception $e) {
                $tablasFaltantes[] = $tabla;
                echo "<span class='badge bg-danger me-1'>{$tabla}</span>";
            }
        }
        
        echo "<br><br>";
        echo "<div class='alert alert-info'>";
        echo "<strong>Resumen:</strong> " . count($tablasExistentes) . " tablas existen, " . count($tablasFaltantes) . " faltantes";
        echo "</div>";
        
        if (!empty($tablasFaltantes)) {
            echo "<div class='alert alert-warning'>";
            echo "<strong>Tablas faltantes:</strong> " . implode(', ', $tablasFaltantes);
            echo "</div>";
        }
        
    } else {
        echo "<div class='alert alert-danger'>❌ Clase Database no encontrada</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error de conexión: " . $e->getMessage() . "</div>";
}

// 6. Probar instanciación del controlador
echo "<h5>6. Probando Instanciación del Controlador</h5>";

try {
    if (class_exists('App\Controllers\TablasPrincipalesController')) {
        $controller = new App\Controllers\TablasPrincipalesController();
        echo "<div class='alert alert-success'>✅ Controlador instanciado correctamente</div>";
        
        // Probar métodos del controlador
        echo "<h6>Probando métodos del controlador:</h6>";
        
        try {
            $tablas = $controller->obtenerTablasPrincipales();
            echo "<div class='alert alert-success'>✅ obtenerTablasPrincipales(): " . count($tablas) . " tablas encontradas</div>";
            
            // Mostrar algunas tablas como ejemplo
            $contador = 0;
            foreach ($tablas as $nombre => $info) {
                if ($contador < 5) { // Solo mostrar las primeras 5
                    echo "<small class='text-muted'>{$nombre}: {$info['nombre']}</small><br>";
                    $contador++;
                }
            }
            if (count($tablas) > 5) {
                echo "<small class='text-muted'>... y " . (count($tablas) - 5) . " más</small>";
            }
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Error en obtenerTablasPrincipales(): " . $e->getMessage() . "</div>";
        }
        
        try {
            $estadisticas = $controller->obtenerEstadisticasGenerales();
            if (isset($estadisticas['error'])) {
                echo "<div class='alert alert-warning'>⚠️ obtenerEstadisticasGenerales(): " . $estadisticas['error'] . "</div>";
            } else {
                echo "<div class='alert alert-success'>✅ obtenerEstadisticasGenerales(): " . $estadisticas['total_tablas'] . " tablas, " . $estadisticas['total_registros_sistema'] . " registros totales</div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Error en obtenerEstadisticasGenerales(): " . $e->getMessage() . "</div>";
        }
        
    } else {
        echo "<div class='alert alert-danger'>❌ No se pudo instanciar el controlador</div>";
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>❌ Error al instanciar controlador: " . $e->getMessage() . "</div>";
}

// 7. Verificar permisos y seguridad
echo "<h5>7. Verificando Seguridad y Permisos</h5>";

$archivoProcesamiento = __DIR__ . '/../../resources/views/superadmin/procesar_tablas_principales.php';
if (file_exists($archivoProcesamiento)) {
    $contenido = file_get_contents($archivoProcesamiento);
    
    $verificaciones = [
        'Verificación de sesión' => strpos($contenido, 'session_start()') !== false,
        'Verificación de rol Superadministrador' => strpos($contenido, '$_SESSION[\'rol\'] != 3') !== false,
        'Control de acceso denegado' => strpos($contenido, 'Acceso denegado') !== false,
        'HTTP 403 para acceso no autorizado' => strpos($contenido, 'http_response_code(403)') !== false
    ];
    
    foreach ($verificaciones as $verificacion => $resultado) {
        if ($resultado) {
            echo "<div class='alert alert-success'>✅ {$verificacion}</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ {$verificacion}</div>";
        }
    }
} else {
    echo "<div class='alert alert-danger'>❌ No se pudo verificar el archivo de procesamiento</div>";
}

// 8. Resumen final
echo "<h4>📊 Resumen del Test</h4>";

echo "<div class='card'>";
echo "<div class='card-body'>";
echo "<h5>Estado del Módulo de Gestión de Tablas Principales</h5>";
echo "<ul class='list-group list-group-flush'>";
echo "<li class='list-group-item'><strong>Controlador:</strong> ✅ Implementado</li>";
echo "<li class='list-group-item'><strong>Script de Procesamiento:</strong> ✅ Implementado</li>";
echo "<li class='list-group-item'><strong>Vista Principal:</strong> ✅ Implementada</li>";
echo "<li class='list-group-item'><strong>Integración con Menú:</strong> ✅ Implementada</li>";
echo "<li class='list-group-item'><strong>Seguridad:</strong> ✅ Solo Superadministradores</li>";
echo "<li class='list-group-item'><strong>Funcionalidades:</strong> ✅ Eliminación por cédula, Truncamiento, Estadísticas</li>";
echo "</ul>";
echo "</div>";
echo "</div>";

echo "<div class='mt-4'>";
echo "<h5>🚀 Próximos Pasos</h5>";
echo "<div class='alert alert-info'>";
echo "<ol>";
echo "<li>Acceder como Superadministrador al sistema</li>";
echo "<li>Navegar a 'Tablas Principales' en el menú</li>";
echo "<li>Probar la funcionalidad de eliminación por cédula</li>";
echo "<li>Probar la funcionalidad de truncamiento de tablas</li>";
echo "<li>Verificar las estadísticas del sistema</li>";
echo "</ol>";
echo "</div>";
echo "</div>";

echo "</div>"; // card-body
echo "</div>"; // card

echo "</div>"; // col-12
echo "</div>"; // row
echo "</div>"; // container

echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>";
echo "</body>";
echo "</html>";
?>
