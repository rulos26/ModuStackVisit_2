<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../../../../app/Controllers/UbicacionController.php';

use App\Controllers\UbicacionController;

// Verificar si hay una sesión activa (revisamos todas las posibles fuentes de la cédula)
$id_cedula = $_SESSION['id_cedula'] ?? $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;

if (!$id_cedula) {
    $_SESSION['error'] = "No hay sesión activa o cédula no encontrada";
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=ubicacion&test=123');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['latituds']) && isset($_POST['longituds'])) {
        $latitud = $_POST['latituds'];
        $longitud = $_POST['longituds'];
        
        try {
            $controller = UbicacionController::getInstance();
            $resultado = $controller->guardarUbicacion($id_cedula, $latitud, $longitud);
            
            if ($resultado['success']) {
                $_SESSION['success'] = $resultado['message'];
                // Aseguramos que la cédula se mantenga en sesión para el siguiente paso
                $_SESSION['id_cedula'] = $id_cedula;
                
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