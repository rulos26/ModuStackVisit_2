<?php

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
use Exception;

// Función para convertir imagen a base64
function img_to_base64($img_path) {
    if (!file_exists($img_path)) {
        return '';
    }
    $info = pathinfo($img_path);
    $ext = strtolower($info['extension']);
    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
    $data = base64_encode(file_get_contents($img_path));
    return 'data:' . $mime . ';base64,' . $data;
}

try {
    // Limpiar cualquier salida previa
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    $cedula = '1231211322';
    
    $logo_path = __DIR__ . '/../../public/images/header.jpg';
    $logo_b64 = img_to_base64($logo_path);

    // Preparar datos para la plantilla
    $data = [
        'cedula' => $cedula,
        'logo_b64' => $logo_b64
    ];
    
    // Extraer variables para la plantilla
    extract($data);
    
    // Generar HTML desde la plantilla
    ob_start();
    include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
    $html = ob_get_clean();
    
    // Crear instancia de Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Enviar el PDF al navegador
    $dompdf->stream('informe_final_test.pdf', ["Attachment" => false]);
    exit;
    
} catch (Exception $e) {
    echo "<h3>Error al generar PDF:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
}
?> 