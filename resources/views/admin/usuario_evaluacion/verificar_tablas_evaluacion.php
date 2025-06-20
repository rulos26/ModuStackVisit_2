<?php
session_start();

// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Verificar que se recibió la cédula
if (!isset($_POST['cedula']) || empty($_POST['cedula'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Cédula no proporcionada']);
    exit();
}

$cedula = $_POST['cedula'];

// Guardar la cédula en la sesión para que las vistas puedan acceder a ella
$_SESSION['cedula_evaluacion'] = $cedula;
$_SESSION['id_cedula'] = $cedula; // También guardar como id_cedula para compatibilidad

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

try {
    // Definir las tablas a verificar (20 módulos de evaluación)
    $tablas = [
        [
            'nombre' => 'Cámara de Comercio',
            'tabla' => 'camara_comercio',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/camara_comercio/index.php',
            'icono' => 'fa-building'
        ],
        [
            'nombre' => 'Estado de Salud',
            'tabla' => 'estados_salud',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/salud/index.php',
            'icono' => 'fa-heartbeat'
        ],
        [
            'nombre' => 'Composición Familiar',
            'tabla' => 'composicion_familiar',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/composición_familiar/index.php',
            'icono' => 'fa-users'
        ],
        [
            'nombre' => 'Información de Pareja',
            'tabla' => 'informacion_pareja',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/informacion_pareja/index.php',
            'icono' => 'fa-heart'
        ],
        [
            'nombre' => 'Tipo de Vivienda',
            'tabla' => 'tipo_vivienda',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/tipo_vivienda/index.php',
            'icono' => 'fa-home'
        ],
        [
            'nombre' => 'Inventario de Enseres',
            'tabla' => 'inventario_enseres',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/inventario_enseres/index.php',
            'icono' => 'fa-couch'
        ],
        [
            'nombre' => 'Servicios Públicos',
            'tabla' => 'servicios_publicos',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/servicios_publicos/index.php',
            'icono' => 'fa-bolt'
        ],
        [
            'nombre' => 'Patrimonio',
            'tabla' => 'patrimonio',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/Patrimonio/index.php',
            'icono' => 'fa-piggy-bank'
        ],
        [
            'nombre' => 'Cuentas Bancarias',
            'tabla' => 'cuentas_bancarias',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/cuentas_bancarias/index.php',
            'icono' => 'fa-university'
        ],
        [
            'nombre' => 'Pasivos',
            'tabla' => 'pasivos',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/pasivos/index.php',
            'icono' => 'fa-credit-card'
        ],
        [
            'nombre' => 'Aportantes',
            'tabla' => 'aportante',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/aportante/index.php',
            'icono' => 'fa-hand-holding-usd'
        ],
        [
            'nombre' => 'Data Crédito',
            'tabla' => 'data_credito',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/data_credito/index.php',
            'icono' => 'fa-chart-line'
        ],
        [
            'nombre' => 'Ingresos Mensuales',
            'tabla' => 'ingresos_mensuales',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/ingresos_mensuales/index.php',
            'icono' => 'fa-money-bill-wave'
        ],
        [
            'nombre' => 'Gastos',
            'tabla' => 'gasto',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/gasto/index.php',
            'icono' => 'fa-receipt'
        ],
        [
            'nombre' => 'Estudios',
            'tabla' => 'estudios',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/estudios/index.php',
            'icono' => 'fa-graduation-cap'
        ],
        [
            'nombre' => 'Información Judicial',
            'tabla' => 'informacion_judicial',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/informacion_judicial/index.php',
            'icono' => 'fa-gavel'
        ],
        [
            'nombre' => 'Experiencia Laboral',
            'tabla' => 'experiencia_laboral',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/experiencia_laboral/index.php',
            'icono' => 'fa-briefcase'
        ],
        [
            'nombre' => 'Concepto Final Evaluador',
            'tabla' => 'concepto_final_evaluador',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/evaluacion_visita/visita/concepto_final_evaluador/index.php',
            'icono' => 'fa-clipboard-check'
        ],
        [
            'nombre' => 'Ubicación',
            'tabla' => 'ubicacion_autorizacion',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/carta_visita/ubicacion/ubicacion.php',
            'icono' => 'fa-map-marker-alt'
        ],
        [
            'nombre' => 'Evidencia Fotográfica',
            'tabla' => 'evidencia_fotografica',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/carta_visita/registro_fotografico/registro_fotografico.php',
            'icono' => 'fa-camera'
        ]
    ];

    $tablas_completadas = 0;
    $tablas_faltantes = [];

    // Verificar cada tabla
    foreach ($tablas as &$tabla) {
        $sql = "SELECT COUNT(*) as total FROM {$tabla['tabla']} WHERE {$tabla['columna']} = ?";
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $cedula);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            $tabla['completada'] = ($row['total'] > 0);
            
            if ($tabla['completada']) {
                $tablas_completadas++;
            } else {
                $tablas_faltantes[] = $tabla;
            }
            
            $stmt->close();
        } else {
            $tabla['completada'] = false;
            $tablas_faltantes[] = $tabla;
        }
    }

    $total_tablas = count($tablas);
    $porcentaje_completado = round(($tablas_completadas / $total_tablas) * 100, 1);
    
    // Determinar si puede acceder (mínimo 15 de 20 tablas = 75%)
    $puede_acceder = $tablas_completadas >= 15;

    // Preparar respuesta
    $response = [
        'success' => true,
        'cedula' => $cedula,
        'tablas' => $tablas,
        'tablas_completadas' => $tablas_completadas,
        'total_tablas' => $total_tablas,
        'porcentaje_completado' => $porcentaje_completado,
        'puede_acceder' => $puede_acceder,
        'tablas_faltantes' => $tablas_faltantes
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
?> 