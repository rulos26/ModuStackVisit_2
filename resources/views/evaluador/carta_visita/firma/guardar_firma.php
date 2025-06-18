<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

echo '<pre style="background:#222;color:#0f0;padding:1em;">DEBUG: Entrando a guardar_firma.php\n';
print_r([
    'POST' => $_POST,
    'SESSION' => $_SESSION,
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD']
]);
echo '</pre>';

require_once __DIR__ . '/../../../../../app/Controllers/FirmaController.php';
use App\Controllers\FirmaController;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firma_digital'])) {
    $firmaBase64 = $_POST['firma_digital'];
    $id_cedula = $_SESSION['cedula_autorizacion'] ?? $_SESSION['user_id'] ?? null;
    if (!$id_cedula) {
        echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG: No hay cédula en sesión</pre>';
        header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=No hay cédula en sesión');
        exit();
    }
    $resultado = FirmaController::guardarFirma($firmaBase64, $id_cedula);
    if ($resultado !== true) {
        echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG: Error al guardar firma: ' . htmlspecialchars($resultado) . '</pre>';
        $_SESSION['error'] = $resultado;
        header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=' . urlencode($resultado));
        exit();
    }
} else {
    echo '<pre style="background:#222;color:#f00;padding:1em;">DEBUG: Acceso no permitido o falta firma_digital</pre>';
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=guardar_firma&msg=Acceso no permitido');
    exit();
} 