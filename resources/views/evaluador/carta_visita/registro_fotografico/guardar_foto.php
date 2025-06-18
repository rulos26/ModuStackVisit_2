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
        die('<div style="color:red; font-weight:bold;">No hay cédula en sesión</div>');
    }
    $resultado = RegistroFotograficoController::guardarFoto($fotoBase64, $id_cedula);
    if ($resultado === true) {
        header('Location: /ModuStackVisit_2/resources/views/evaluador/carta_visita/ubicacion/ubicacion.php');
        exit();
    } else {
        echo '<div style="color:red; font-weight:bold;">Error al guardar la foto: ' . htmlspecialchars($resultado) . '</div>';
        echo '<a href="registro_fotografico.php">Volver a intentar</a>';
        exit();
    }
} else {
    die('<div style="color:red; font-weight:bold;">Acceso no permitido o falta foto_digital</div>');
} 