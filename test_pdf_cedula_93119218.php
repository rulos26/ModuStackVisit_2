<?php
session_start();

// Configurar la cédula específica para la prueba
$cedula = '93119218';

try {
    // Guardar la cédula en la sesión para que el InformeFinalPdfController pueda acceder a ella
    $_SESSION['cedula_informe'] = $cedula;
    $_SESSION['id_cedula'] = $cedula;
    $_SESSION['pdf_origen'] = 'test_cedula_93119218';
    
    echo "<h2>Prueba de PDF con Cédula: $cedula</h2>";
    echo "<p>Redirigiendo al generador de PDF...</p>";
    echo "<p>Si no se redirige automáticamente, haz clic en el enlace:</p>";
    echo "<a href='/ModuStackVisit_2/app/Controllers/InformeFinalPdfController.php?action=generarInforme' target='_blank'>Generar PDF para Cédula $cedula</a>";
    
    // Redirigir al controlador del informe
    header('Location: /ModuStackVisit_2/app/Controllers/InformeFinalPdfController.php?action=generarInforme');
    exit();

} catch (Exception $e) {
    echo "<h2>Error en la prueba</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
