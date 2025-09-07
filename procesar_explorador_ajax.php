<?php
/**
 * PROCESADOR AJAX PARA EL EXPLORADOR DE IMÁGENES
 * Maneja las peticiones AJAX del explorador de imágenes
 * 
 * @author Sistema de Visitas
 * @version 1.0
 * @date 2024
 */

// Configurar headers para JSON
header('Content-Type: application/json');

// Iniciar sesión
session_start();

// Verificar autenticación y rol de superadministrador
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 3) {
    echo json_encode([
        'success' => false,
        'error' => 'Acceso denegado. Solo superadministradores pueden acceder a este módulo.'
    ]);
    exit();
}

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit();
}

// Incluir el controlador
require_once 'app/Controllers/ExploradorImagenesController.php';

try {
    $explorador = new ExploradorImagenesController();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete':
            $path = $_POST['path'] ?? '';
            if (empty($path)) {
                throw new Exception('Ruta no especificada');
            }
            
            $result = $explorador->deleteFile($path);
            echo json_encode($result);
            break;
            
        case 'get_content':
            $path = $_POST['path'] ?? '';
            $result = $explorador->getFolderContent($path);
            echo json_encode($result);
            break;
            
        case 'get_image_info':
            $path = $_POST['path'] ?? '';
            if (empty($path)) {
                throw new Exception('Ruta no especificada');
            }
            
            $result = $explorador->getImageInfo($path);
            echo json_encode($result);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>