<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
use Exception;
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
        try {
            $db = Database::getInstance()->getConnection();
            $cedula = '1231211322';
            
            $logo_path = __DIR__ . '/../../public/images/header.jpg';
            $logo_b64 = self::img_to_base64($logo_path);

            // Debug: Verificar variables
            echo "<h3>Debug - Variables:</h3>";
            echo "<p>Cédula: " . $cedula . "</p>";
            echo "<p>Logo path: " . $logo_path . "</p>";
            echo "<p>Logo existe: " . (file_exists($logo_path) ? 'SÍ' : 'NO') . "</p>";
            echo "<p>Logo base64: " . (empty($logo_b64) ? 'VACÍO' : 'CON DATOS') . "</p>";

            // --- Renderizado usando plantilla externa ---
            $data = [
                'cedula' => $cedula,
                'logo_b64' => $logo_b64
            ];
            
            // Debug: Verificar array de datos
            echo "<h3>Debug - Array de datos:</h3>";
            echo "<pre>" . print_r($data, true) . "</pre>";
            
            extract($data);
            
            // Debug: Verificar variables después del extract
            echo "<h3>Debug - Variables después del extract:</h3>";
            echo "<p>Cédula después extract: " . (isset($cedula) ? $cedula : 'NO DEFINIDA') . "</p>";
            echo "<p>Logo_b64 después extract: " . (isset($logo_b64) ? 'DEFINIDA' : 'NO DEFINIDA') . "</p>";
            
            ob_start();
            include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
            $html = ob_get_clean();
            
            // Debug: Verificar HTML generado
            echo "<h3>Debug - HTML generado:</h3>";
            echo "<textarea style='width: 100%; height: 200px;'>" . htmlspecialchars($html) . "</textarea>";
            
            // Crear instancia de Dompdf
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Enviar el PDF al navegador
            $dompdf->stream('informe_final.pdf', ["Attachment" => false]);
            exit;
            
        } catch (Exception $e) {
            echo "<h3>Error:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<p>Línea: " . $e->getLine() . "</p>";
            echo "<p>Archivo: " . $e->getFile() . "</p>";
        }
    }
} 