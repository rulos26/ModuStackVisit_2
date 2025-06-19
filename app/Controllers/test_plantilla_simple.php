<?php

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
use Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Test de Plantilla Simple</h1>";

try {
    // Definir variables de prueba
    $cedula = '1231211322';
    $logo_b64 = 'test_logo_data';
    
    echo "<h2>Variables definidas:</h2>";
    echo "<p>Cédula: $cedula</p>";
    echo "<p>Logo: $logo_b64</p>";
    
    // Crear array de datos
    $data = [
        'cedula' => $cedula,
        'logo_b64' => $logo_b64
    ];
    
    echo "<h2>Array de datos:</h2>";
    echo "<pre>" . print_r($data, true) . "</pre>";
    
    // Extraer variables
    extract($data);
    
    echo "<h2>Variables después del extract:</h2>";
    echo "<p>Cédula después extract: " . (isset($cedula) ? $cedula : 'NO DEFINIDA') . "</p>";
    echo "<p>Logo_b64 después extract: " . (isset($logo_b64) ? 'DEFINIDA' : 'NO DEFINIDA') . "</p>";
    
    // Incluir plantilla simple
    ob_start();
    include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_simple.php';
    $html = ob_get_clean();
    
    echo "<h2>HTML generado:</h2>";
    echo "<textarea style='width: 100%; height: 300px;'>" . htmlspecialchars($html) . "</textarea>";
    
    // Generar PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    echo "<h2>PDF generado correctamente</h2>";
    echo "<p>El PDF se mostrará a continuación:</p>";
    
    // Enviar el PDF al navegador
    $dompdf->stream('test_simple.pdf', ["Attachment" => false]);
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
}
?> 