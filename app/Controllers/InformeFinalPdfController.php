<?php

namespace App\Controllers;

require_once __DIR__ . '/../Database/Database.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use App\Database\Database;
use Exception;

class InformeFinalPdfController {
    
    private $db;
    private $cedula;
    private $logo_b64;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->cedula = '1231211322';
        $this->logo_b64 = $this->img_to_base64(__DIR__ . '/../../public/images/header.jpg');
    }
    
    // Función para convertir imagen a base64
    private function img_to_base64($img_path) {
        if (!file_exists($img_path)) {
            return '';
        }
        $info = pathinfo($img_path);
        $ext = strtolower($info['extension']);
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
        $data = base64_encode(file_get_contents($img_path));
        return 'data:' . $mime . ';base64,' . $data;
    }
    
    // Obtener datos para la plantilla
    private function getTemplateData() {
        return [
            'cedula' => $this->cedula,
            'logo_b64' => $this->logo_b64
        ];
    }
    
    // Generar HTML desde la plantilla
    private function generateHtml() {
        $data = $this->getTemplateData();
        extract($data);
        
        ob_start();
        include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_pdf.php';
        return ob_get_clean();
    }
    
    // Generar PDF
    private function generatePdf($html) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf;
    }
    
    // Método principal público
    public static function Informefinalpdf() {
        try {
            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            $controller = new self();
            $html = $controller->generateHtml();
            $dompdf = $controller->generatePdf($html);
            
            // Enviar el PDF al navegador
            $dompdf->stream('informe_final.pdf', ["Attachment" => false]);
            exit;
            
        } catch (Exception $e) {
            // Si hay error, mostrar en una página HTML
            echo "<h3>Error al generar PDF:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "<p>Línea: " . $e->getLine() . "</p>";
            echo "<p>Archivo: " . $e->getFile() . "</p>";
        }
    }
    
    // Método para obtener datos (útil para debug)
    public function getData() {
        return $this->getTemplateData();
    }
    
    // Método para obtener HTML (útil para debug)
    public function getHtml() {
        return $this->generateHtml();
    }
} 