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

try {
    // Guardar la cédula en la sesión para que el DemoPdfController pueda acceder a ella
    $_SESSION['cedula_autorizacion'] = $cedula;
    $_SESSION['id_cedula'] = $cedula;
    
    // También guardar información adicional que pueda ser útil
    $_SESSION['admin_viewing_user'] = true;
    $_SESSION['viewing_cedula'] = $cedula;
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Cédula guardada en sesión correctamente',
        'cedula' => $cedula
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error inesperado: ' . $e->getMessage()
    ]);
}
?> 