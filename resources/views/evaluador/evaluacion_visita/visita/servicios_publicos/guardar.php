<?php
session_start();

if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

require_once __DIR__ . '/ServiciosPublicosController.php';
use App\Controllers\ServiciosPublicosController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = ServiciosPublicosController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../Patrimonio/tiene_patrimonio.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
                header('Location: servicios_publicos.php');
                exit();
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
            header('Location: servicios_publicos.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Error en guardar.php de servicios públicos: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
        header('Location: servicios_publicos.php');
        exit();
    }
} else {
    header('Location: servicios_publicos.php');
    exit();
}
?>
