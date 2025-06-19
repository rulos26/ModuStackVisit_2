<?php
session_start();

if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    header('Location: ../../../../../public/login.php');
    exit();
}

require_once __DIR__ . '/PatrimonioController.php';
use App\Controllers\PatrimonioController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $controller = PatrimonioController::getInstance();
        $datos = $controller->sanitizarDatos($_POST);
        $errores = $controller->validarDatos($datos);
        
        if (empty($errores)) {
            $resultado = $controller->guardar($datos);
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                header('Location: ../cuentas_bancarias/cuentas_bancarias.php');
                exit();
            } else {
                $_SESSION['error'] = $resultado['message'];
                header('Location: Patrimonio.php');
                exit();
            }
        } else {
            $_SESSION['error'] = implode('<br>', $errores);
            header('Location: Patrimonio.php');
            exit();
        }
    } catch (Exception $e) {
        error_log("Error en guardar.php de patrimonio: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor: " . $e->getMessage();
        header('Location: Patrimonio.php');
        exit();
    }
} else {
    header('Location: tiene_patrimonio.php');
    exit();
}
?>
