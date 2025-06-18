<?php
session_start();
require_once __DIR__ . '/../../../../../app/Controllers/UbicacionController.php';

use App\Controllers\UbicacionController;

// Verificar si hay una sesión activa
if (!isset($_SESSION['id_cedula'])) {
    $_SESSION['error'] = "No hay sesión activa";
    header('Location: ../../../login/login.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['latituds']) && isset($_POST['longituds'])) {
        $id_cedula = $_SESSION['id_cedula'];
        $latitud = $_POST['latituds'];
        $longitud = $_POST['longituds'];
        
        try {
            $controller = UbicacionController::getInstance();
            $resultado = $controller->guardarUbicacion($id_cedula, $latitud, $longitud);
            
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                echo '<script>
                    window.open("../informe/index.php", "_blank");
                    setTimeout(function() {
                        window.location.href = "../index.php";
                    }, 2000);
                </script>';
                exit();
            } else {
                throw new Exception($resultado['message']);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../ubicacion.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "No se recibieron todos los campos del formulario";
        header('Location: ../ubicacion.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Acceso denegado";
    header('Location: ../ubicacion.php');
    exit();
}
?>