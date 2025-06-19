<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();

class InformeFinalPdfController {
    
    // Función para convertir imagen a base64
    private static function img_to_base64($img_path) {
        if (!file_exists($img_path)) {
            return '';
        }
        $info = pathinfo($img_path);
        $ext = strtolower($info['extension']);
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
        $data = base64_encode(file_get_contents($img_path));
        return 'data:' . $mime . ';base64,' . $data;
    }
    
    public static function Informefinalpdf() {
        $db = Database::getInstance()->getConnection();
        $cedula = '1231211322';
        
        $logo_path = __DIR__ . '/../../public/images/header.jpg';
        $logo_b64 = self::img_to_base64($logo_path);

        // --- Renderizado usando plantilla externa ---
        $data = [
            'cedula' => $cedula,
            'logo_b64' => $logo_b64
        ];
        extract($data);
        ob_start();
        include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
        $html = ob_get_clean();
        // --- Fin renderizado plantilla ---

        // Crear instancia de Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        // Enviar el PDF al navegador
        $dompdf->stream('informe_final.pdf', ["Attachment" => false]);
        exit;
    }
} 