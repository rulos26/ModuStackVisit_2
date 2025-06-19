<?php
/**
 * ARCHIVO DE PRUEBA PARA EL CONTROLADOR DE PDF
 * Prueba básica del InformeFinalPdfController
 */

// Incluir el controlador
require_once __DIR__ . '/InformeFinalPdfController.php';

use App\Controllers\InformeFinalPdfController;

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>Test Informe PDF</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; }";
echo ".test-link { display: inline-block; margin: 10px; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }";
echo ".test-link:hover { background-color: #0056b3; }";
echo ".success-link { background-color: #28a745; }";
echo ".success-link:hover { background-color: #1e7e34; }";
echo ".warning-link { background-color: #ffc107; color: #212529; }";
echo ".warning-link:hover { background-color: #e0a800; }";
echo ".info-link { background-color: #17a2b8; }";
echo ".info-link:hover { background-color: #138496; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>Test de Informe PDF</h1>";

echo "<h2>Pruebas Disponibles:</h2>";

echo "<a href='test_plantilla_simple.php' class='test-link'>";
echo "Test Plantilla Simple (debug completo)";
echo "</a>";

echo "<a href='test_controller_refactorizado.php' class='test-link info-link'>";
echo "Test Controlador Refactorizado (MVC)";
echo "</a>";

echo "<a href='test_pdf_final.php' class='test-link success-link'>";
echo "Generar PDF Final (sin debug)";
echo "</a>";

echo "<a href='InformeFinalPdfController.php?action=Informefinalpdf' class='test-link warning-link'>";
echo "Test Controlador Principal";
echo "</a>";

// Verificar si el archivo de logo existe
$logo_path = __DIR__ . '/../../public/images/header.jpg';
echo "<h2>Información de Debug</h2>";
echo "<p><strong>Ruta del logo:</strong> " . $logo_path . "</p>";
echo "<p><strong>¿Existe el archivo?</strong> " . (file_exists($logo_path) ? 'SÍ' : 'NO') . "</p>";

if (file_exists($logo_path)) {
    echo "<p><strong>Tamaño del archivo:</strong> " . filesize($logo_path) . " bytes</p>";
    echo "<p><strong>Tipo de archivo:</strong> " . mime_content_type($logo_path) . "</p>";
} else {
    echo "<p style='color: red;'><strong>ERROR:</strong> El archivo de logo no existe en la ruta especificada.</p>";
    echo "<p>Verifica que el archivo 'header.jpg' esté en la carpeta 'public/images/'</p>";
}

echo "<hr>";

// Botón para generar el PDF
echo "<h2>Generar PDF</h2>";
echo "<form method='post'>";
echo "<button type='submit' name='generar_pdf' style='padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>Generar Informe PDF</button>";
echo "</form>";

// Procesar la generación del PDF
if (isset($_POST['generar_pdf'])) {
    try {
        echo "<p style='color: green;'>Generando PDF...</p>";
        InformeFinalPdfController::Informefinalpdf();
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<h2>Estructura de Carpetas</h2>";
echo "<p><strong>Ruta actual:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Ruta del proyecto:</strong> " . dirname(__DIR__, 2) . "</p>";

// Verificar si existe la carpeta de imágenes
$images_dir = dirname(__DIR__, 2) . '/public/images';
echo "<p><strong>Carpeta de imágenes:</strong> " . $images_dir . "</p>";
echo "<p><strong>¿Existe la carpeta?</strong> " . (is_dir($images_dir) ? 'SÍ' : 'NO') . "</p>";

if (is_dir($images_dir)) {
    echo "<p><strong>Contenido de la carpeta images:</strong></p>";
    $files = scandir($images_dir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>" . $file . "</li>";
        }
    }
    echo "</ul>";
}

echo "<h3>Instrucciones:</h3>";
echo "<ol>";
echo "<li>Haz clic en 'Test Controlador Refactorizado' para ver la nueva arquitectura MVC</li>";
echo "<li>Si el debug funciona, haz clic en 'Generar PDF Final' para ver el PDF real</li>";
echo "<li>El PDF debe mostrar la cédula '1231211322' y el logo</li>";
echo "</ol>";

echo "<h3>Archivos creados:</h3>";
echo "<ul>";
echo "<li><strong>InformeFinalPdfController.php</strong> - Controlador principal refactorizado</li>";
echo "<li><strong>plantilla_simple.php</strong> - Plantilla de prueba simple</li>";
echo "<li><strong>test_plantilla_simple.php</strong> - Test completo con debug</li>";
echo "<li><strong>test_pdf_final.php</strong> - Genera PDF final sin debug</li>";
echo "<li><strong>test_controller_refactorizado.php</strong> - Test del controlador MVC</li>";
echo "<li><strong>debug_view.php</strong> - Vista de debug para test simple</li>";
echo "<li><strong>debug_controller.php</strong> - Vista de debug para controlador MVC</li>";
echo "</ul>";

echo "</body>";
echo "</html>";
?> 