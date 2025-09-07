<?php
// Archivo de entrada para las acciones AJAX del explorador
require_once 'vendor/autoload.php';

use App\Controllers\ExploradorImagenesController;

$controller = new ExploradorImagenesController();

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

switch ($accion) {
    case 'obtener_contenido':
        $controller->obtenerContenido();
        break;
    case 'eliminar_imagen':
        $controller->eliminarImagen();
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
?>
