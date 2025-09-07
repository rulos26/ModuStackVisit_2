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
        case 'obtener_usuarios_evaluados':
            $resultado = $controller->obtenerUsuariosEvaluados();
            echo json_encode($resultado);
            break;
            
        case 'verificar_tablas_con_datos':
            $idCedula = $_POST['id_cedula'] ?? '';
            
            if (empty($idCedula)) {
                throw new Exception('ID de cédula es requerido');
            }
            
            if (!is_numeric($idCedula)) {
                throw new Exception('El ID de cédula debe ser un número válido');
            }
            
            $resultado = $controller->verificarTablasConDatos((int)$idCedula);
            echo json_encode($resultado);
            break;
            
        case 'eliminar_usuario_completo':
            $idCedula = $_POST['id_cedula'] ?? '';
            
            if (empty($idCedula)) {
                throw new Exception('ID de cédula es requerido');
            }
            
            if (!is_numeric($idCedula)) {
                throw new Exception('El ID de cédula debe ser un número válido');
            }
            
            // Confirmación adicional para eliminación completa
            $confirmacion = $_POST['confirmacion'] ?? '';
            if ($confirmacion !== 'ELIMINAR_USUARIO_COMPLETO') {
                throw new Exception('Se requiere confirmación explícita para eliminar el usuario');
            }
            
            $resultado = $controller->eliminarUsuarioCompleto((int)$idCedula);
            echo json_encode($resultado);
            break;
            
        case 'vaciar_todas_las_tablas':
            // Confirmación adicional para vaciar todas las tablas
            $confirmacion = $_POST['confirmacion'] ?? '';
            if ($confirmacion !== 'VACIAR_TODAS_LAS_TABLAS') {
                throw new Exception('Se requiere confirmación explícita para vaciar todas las tablas');
            }
            
            $resultado = $controller->vaciarTodasLasTablas();
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
