<?php
namespace App\Controllers;

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;

class DemoPdfController {
    public static function generarEjemplo() {
        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        // Contenido HTML básico
        $html = '<h1>¡Hola, este es un PDF generado con Dompdf!</h1><p>Generado desde el controlador DemoPdfController.</p>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('ejemplo.pdf', ["Attachment" => false]);
        exit;
    }
} 