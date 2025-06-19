<?php
/**
 * ARCHIVO DE PRUEBA PARA EL CONTROLADOR DE PDF
 * Prueba básica del InformeFinalPdfController
 */

// Incluir el controlador
require_once __DIR__ . '/InformeFinalPdfController.php';

use App\Controllers\InformeFinalPdfController;

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
?> 