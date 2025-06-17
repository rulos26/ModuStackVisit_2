<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../../../../app/Controllers/FirmaController.php';
use App\Controllers\FirmaController;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firma_digital'])) {
    $firmaBase64 = $_POST['firma_digital'];
    $id_cedula = $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;
    if (!$id_cedula) {
        header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=No hay cédula en sesión');
        exit();
    }
    $resultado = FirmaController::guardarFirma($firmaBase64, $id_cedula);
    if ($resultado !== true) {
        $_SESSION['error'] = $resultado;
        header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=' . urlencode($resultado));
        exit();
    }
} else {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=Acceso no permitido');
    exit();
} 