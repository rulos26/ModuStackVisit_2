<?php
session_start();

// Verificar que el usuario esté autenticado y sea Superadministrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 3) {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso denegado. Solo Superadministradores pueden acceder a esta funcionalidad.']);
    exit();
}

require_once __DIR__ . '/../../app/Controllers/TablasPrincipalesController.php';
require_once __DIR__ . '/../../app/Services/LoggerService.php';

use App\Controllers\TablasPrincipalesController;
use App\Services\LoggerService;

$controller = new TablasPrincipalesController();
$logger = new LoggerService();

// Obtener la acción solicitada
$accion = $_POST['accion'] ?? '';

try {
    switch ($accion) {
        case 'obtener_estadisticas':
            $nombreTabla = $_POST['tabla'] ?? '';
            if (empty($nombreTabla)) {
                throw new Exception('Nombre de tabla requerido');
            }
            
            $resultado = $controller->obtenerEstadisticasTabla($nombreTabla);
            echo json_encode($resultado);
            break;
            
        case 'obtener_estadisticas_generales':
            $resultado = $controller->obtenerEstadisticasGenerales();
            echo json_encode($resultado);
            break;
            
        case 'eliminar_por_cedula':
            $nombreTabla = $_POST['tabla'] ?? '';
            $cedula = $_POST['cedula'] ?? '';
            
            if (empty($nombreTabla) || empty($cedula)) {
                throw new Exception('Nombre de tabla y cédula son requeridos');
            }
            
            if (!is_numeric($cedula)) {
                throw new Exception('La cédula debe ser un número válido');
            }
            
            $resultado = $controller->eliminarRegistrosPorCedula($nombreTabla, (int)$cedula);
            echo json_encode($resultado);
            break;
            
        case 'eliminar_por_cedula_todas_tablas':
            $cedula = $_POST['cedula'] ?? '';
            
            if (empty($cedula)) {
                throw new Exception('Cédula es requerida');
            }
            
            if (!is_numeric($cedula)) {
                throw new Exception('La cédula debe ser un número válido');
            }
            
            // Confirmación adicional para eliminación masiva
            $confirmacion = $_POST['confirmacion'] ?? '';
            if ($confirmacion !== 'ELIMINAR_TODOS_LOS_REGISTROS') {
                throw new Exception('Se requiere confirmación explícita para esta operación');
            }
            
            $resultado = $controller->eliminarRegistrosPorCedulaEnTodasLasTablas((int)$cedula);
            echo json_encode($resultado);
            break;
            
        case 'truncar_tabla':
            $nombreTabla = $_POST['tabla'] ?? '';
            
            if (empty($nombreTabla)) {
                throw new Exception('Nombre de tabla requerido');
            }
            
            // Confirmación adicional para truncamiento
            $confirmacion = $_POST['confirmacion'] ?? '';
            if ($confirmacion !== 'TRUNCAR_TABLA_COMPLETA') {
                throw new Exception('Se requiere confirmación explícita para truncar la tabla');
            }
            
            $resultado = $controller->truncarTabla($nombreTabla);
            echo json_encode($resultado);
            break;
            
        case 'obtener_tablas':
            $resultado = $controller->obtenerTablasPrincipales();
            echo json_encode($resultado);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    $logger->error('Error en procesar_tablas_principales', [
        'accion' => $accion,
        'error' => $e->getMessage(),
        'usuario' => $_SESSION['username'] ?? 'unknown'
    ]);
    
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'accion' => $accion
    ]);
}
