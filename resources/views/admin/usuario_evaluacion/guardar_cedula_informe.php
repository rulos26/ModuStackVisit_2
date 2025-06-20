<?php
session_start();

// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_evaluacion');
    exit();
}

// Verificar que se recibió la cédula
if (!isset($_GET['cedula']) || empty($_GET['cedula'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_evaluacion');
    exit();
}

$cedula = $_GET['cedula'];

try {
    // Guardar la cédula en la sesión para que el InformeFinalPdfController pueda acceder a ella
    $_SESSION['cedula_informe'] = $cedula;
    $_SESSION['id_cedula'] = $cedula;
    $_SESSION['pdf_origen'] = 'admin_evaluacion';
    
    // Redirigir al controlador del informe
    header('Location: /ModuStackVisit_2/app/Controllers/InformeFinalPdfController.php?action=generarInforme');
    exit();

} catch (Exception $e) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_evaluacion');
    exit();
}
?> 