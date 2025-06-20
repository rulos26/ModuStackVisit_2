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

// Conexión a la base de datos
require_once __DIR__ . '/../../../../conn/conexion.php';

try {
    // Definir las tablas a verificar
    $tablas = [
        [
            'nombre' => 'Carta de Autorización',
            'tabla' => 'autorizaciones',
            'columna' => 'cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/carta_visita/carta_autorizacion/carta_autorizacion.php',
            'icono' => 'fa-envelope'
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
            'nombre' => 'Firma',
            'tabla' => 'firmas',
            'columna' => 'id_cedula',
            'completada' => false,
            'url' => '/ModuStackVisit_2/resources/views/evaluador/carta_visita/firma/firma.php',
            'icono' => 'fa-signature'
        ],
        [
            'nombre' => 'Foto de Perfil',
            'tabla' => 'foto_perfil_autorizacion',
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
    
    // Determinar si puede acceder (mínimo 3 de 4 tablas = 75%)
    $puede_acceder = $tablas_completadas >= 3;

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