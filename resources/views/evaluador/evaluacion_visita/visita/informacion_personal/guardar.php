<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: informacion_personal.php');
    exit();
}

// Incluir el controlador
require_once __DIR__ . '/../../../../../app/Controllers/InformacionPersonalController.php';
use App\Controllers\InformacionPersonalController;

try {
    // Obtener instancia del controlador
    $controller = InformacionPersonalController::getInstance();
    
    // Sanitizar y validar datos de entrada
    $datos = $controller->sanitizarDatos($_POST);
    
    // Validar datos
    $errores = $controller->validarDatos($datos);
    
    if (!empty($errores)) {
        // Si hay errores, redirigir de vuelta con mensaje de error
        $_SESSION['error_message'] = implode('<br>', $errores);
        $_SESSION['form_data'] = $datos; // Guardar datos para repoblar el formulario
        header('Location: informacion_personal.php');
        exit();
    }
    
    // Intentar guardar los datos
    $resultado = $controller->guardar($datos);
    
    if ($resultado['success']) {
        // Éxito
        $_SESSION['success_message'] = $resultado['message'];
        
        // Redirigir según la acción realizada
        if ($resultado['action'] === 'created') {
            // Si es un nuevo registro, continuar al siguiente paso
            header('Location: ../registro_fotografico/registro_fotografico.php');
        } else {
            // Si es una actualización, volver al formulario
            header('Location: informacion_personal.php');
        }
        exit();
    } else {
        // Error al guardar
        $_SESSION['error_message'] = $resultado['message'];
        $_SESSION['form_data'] = $datos; // Guardar datos para repoblar el formulario
        header('Location: informacion_personal.php');
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error en guardar.php: " . $e->getMessage());
    $_SESSION['error_message'] = "Error interno del servidor: " . $e->getMessage();
    $_SESSION['form_data'] = $_POST; // Guardar datos para repoblar el formulario
    header('Location: informacion_personal.php');
    exit();
}
?>
