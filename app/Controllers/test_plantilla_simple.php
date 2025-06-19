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

// Clase para manejar la lógica del test
class TestPlantillaSimple {
    
    private $cedula;
    private $logo_b64;
    private $data;
    
    public function __construct() {
        $this->cedula = '1231211322';
        $this->logo_b64 = 'test_logo_data';
        $this->data = [
            'cedula' => $this->cedula,
            'logo_b64' => $this->logo_b64
        ];
    }
    
    public function getVariables() {
        return [
            'cedula' => $this->cedula,
            'logo_b64' => $this->logo_b64
        ];
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function extractVariables() {
        extract($this->data);
        return [
            'cedula' => isset($cedula) ? $cedula : 'NO DEFINIDA',
            'logo_b64' => isset($logo_b64) ? 'DEFINIDA' : 'NO DEFINIDA'
        ];
    }
    
    public function generateHtml() {
        ob_start();
        include __DIR__ . '/../../resources/views/pdf/informe_final/plantilla_simple.php';
        return ob_get_clean();
    }
    
    public function generatePdf($html) {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf;
    }
}

// Inicializar la clase
$test = new TestPlantillaSimple();

// Obtener datos
$variables = $test->getVariables();
$data = $test->getData();
$extractedVars = $test->extractVariables();
$html = $test->generateHtml();

// Incluir la vista de debug
include __DIR__ . '/../../resources/views/test/debug_view.php';

?> 