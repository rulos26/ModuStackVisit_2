<?php
session_start();

// Verificar si hay una sesión activa de administrador
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['username'])) {
    header('Location: /ModuStackVisit_2/resources/views/error/error.php?from=admin_usuario_carta');
    exit();
}

// Verificar que se recibió la cédula por GET
if (!isset($_GET['cedula']) || empty($_GET['cedula'])) {
    echo "Error: Cédula no proporcionada";
    exit();
}

$cedula = $_GET['cedula'];

try {
    // Guardar la cédula en la sesión con el nombre específico solicitado
    $_SESSION['cedula_vista'] = $cedula;
    
    // También guardar en las otras variables para compatibilidad
    $_SESSION['cedula_autorizacion'] = $cedula;
    $_SESSION['id_cedula'] = $cedula;
    
    // Información adicional para el contexto
    $_SESSION['admin_viewing_user'] = true;
    $_SESSION['viewing_cedula'] = $cedula;
    
    // Variable específica para identificar origen del admin
    $_SESSION['pdf_origen'] = 'admin_panel';
    $_SESSION['admin_cedula_vista'] = $cedula;
    
    // Redirigir al DemoPdfController
    header('Location: /ModuStackVisit_2/app/Controllers/DemoPdfController.php');
    exit();

} catch (Exception $e) {
    echo "Error inesperado: " . $e->getMessage();
}
?> 