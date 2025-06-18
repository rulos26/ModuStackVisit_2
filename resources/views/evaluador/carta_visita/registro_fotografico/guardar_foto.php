<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../../../../app/Controllers/RegistroFotograficoController.php';
use App\Controllers\RegistroFotograficoController;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['foto_digital'])) {
    $fotoBase64 = $_POST['foto_digital'];
    $id_cedula = $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;
    
    if (!$id_cedula) {
        $_SESSION['error'] = "No hay cédula en sesión";
        header('Location: registro_fotografico.php');
        exit();
    }

    $resultado = RegistroFotograficoController::guardarFoto($fotoBase64, $id_cedula);
    
    if ($resultado === true) {
        $_SESSION['success'] = "Foto guardada exitosamente";
        $_SESSION['id_cedula'] = $id_cedula; // Aseguramos que la cédula esté en sesión para el siguiente paso
        header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/ubicacion/ubicacion.php');
        exit();
    } else {
        $_SESSION['error'] = "Error al guardar la foto: " . $resultado;
        header('Location: registro_fotografico.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Acceso no permitido o falta foto_digital";
    header('Location: registro_fotografico.php');
    exit();
} 