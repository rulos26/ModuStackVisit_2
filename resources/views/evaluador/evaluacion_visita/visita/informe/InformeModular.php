<?php
/**
 * INFORME VISITA DOMICILIARIA - VERSIÓN MODULAR
 * Sistema completamente modularizado para mejor mantenimiento
 * 
 * @author Sistema de Informes
 * @version 3.0
 * @date 2024
 */

// Configuración de errores y seguridad
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['id_cedula']) || empty($_SESSION['id_cedula'])) {
    die('Error: Sesión no válida. Redirigiendo al login...');
}

// Configuración de constantes
define('REPORT_VERSION', '3.0');
define('REPORT_CODE', 'PSI-FR-11');
define('REPORT_VALIDITY', 'Enero 2024');
define('COMPANY_NAME', 'EL NOMBRE DE SU EMPRESA');
define('MAX_IMAGE_WIDTH', 1107);
define('MAX_IMAGE_HEIGHT', 206);
define('PROFILE_IMAGE_WIDTH', 200);
define('PROFILE_IMAGE_HEIGHT', 177);

// Cargar dependencias
require_once(__DIR__ . '/../../../../../../librery/tcpdf.php');
include $_SERVER['DOCUMENT_ROOT'] . '/ModuStackVisit_2/conn/conexion.php';

// Cargar clases base
require_once 'modules/BaseModule.php';
require_once 'modules/PerfilModule.php';
require_once 'modules/CamaraComercioModule.php';
require_once 'modules/EstadoSaludModule.php';
require_once 'modules/ComposicionFamiliarModule.php';
require_once 'modules/InformacionParejaModule.php';
require_once 'modules/InventarioModule.php';

// Variables globales
$id_cedula = $_SESSION['id_cedula'];
$fecha_actual = date('Y-m-d');

/**
 * CLASE PARA CONFIGURACIÓN DEL INFORME
 */
class ConfiguracionInforme {
    public $borderColor;
    public $borderWidth;
    public $styles;
    
    public function __construct() {
        $this->borderColor = [255, 255, 255];
        $this->borderWidth = 1;
        $this->styles = $this->getEstilosCSS();
    }
    
    /**
     * Obtener estilos CSS centralizados
     */
    private function getEstilosCSS() {
        return [
            'table' => 'width: 100%; border-collapse: collapse; margin-bottom: 10px;',
            'header' => 'font-weight: bold; background-color: #ABABAB; border: 1px solid black; text-align: center;',
            'cell' => 'border: 1px solid black; padding: 5px; text-align: center;',
            'logo' => 'width: ' . MAX_IMAGE_WIDTH . 'px; height: ' . MAX_IMAGE_HEIGHT . 'px;',
            'profile' => 'border: 2px solid black; height: ' . PROFILE_IMAGE_HEIGHT . 'px; width: ' . PROFILE_IMAGE_WIDTH . 'px;'
        ];
    }
    
    /**
     * Generar tabla HTML estándar
     */
    public function generarTabla($titulo, $headers, $data, $columnWidths = []) {
        $html = "<table class='customTable' style='{$this->styles['table']}'>";
        
        // Título
        if ($titulo) {
            $colspan = count($headers);
            $html .= "<thead><tr><th colspan='{$colspan}' style='{$this->styles['header']}'>{$titulo}</th></tr>";
        }
        
        // Headers
        if (!empty($headers)) {
            $html .= "<tr>";
            foreach ($headers as $index => $header) {
                $width = isset($columnWidths[$index]) ? "width: {$columnWidths[$index]};" : '';
                $html .= "<th style='{$this->styles['header']} {$width}'>{$header}</th>";
            }
            $html .= "</tr>";
        }
        
        // Datos
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $index => $cell) {
                $width = isset($columnWidths[$index]) ? "width: {$columnWidths[$index]};" : '';
                $html .= "<td style='{$this->styles['cell']} {$width}'>" . htmlspecialchars($cell) . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</table>";
        return $html;
    }
}

/**
 * CLASE PARA FORMATEO DE DATOS
 */
class DataFormatter {
    public function formatDate($date) {
        if (empty($date)) return 'No disponible';
        return date('d/m/Y', strtotime($date));
    }
    
    public function formatPhone($phone) {
        if (empty($phone)) return 'No disponible';
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $phone);
    }
    
    public function validateEmail($email) {
        if (empty($email)) return 'No disponible';
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : 'Email inválido';
    }
    
    public function formatMoney($amount) {
        if (empty($amount)) return '$ 0,00';
        return '$ ' . number_format($amount, 2, ',', '.');
    }
}

/**
 * CLASE PARA LOGGING
 */
class Logger {
    private $logFile = 'informe_errors.log';
    
    public function log($message, $type = 'INFO') {
        $logMessage = date('[Y-m-d H:i:s]') . " [{$type}] {$message}\n";
        error_log($logMessage, 3, $this->logFile);
    }
    
    public function logError($message, $exception = null) {
        $errorMessage = $message;
        if ($exception) {
            $errorMessage .= "\nException: " . $exception->getMessage();
            $errorMessage .= "\nStack Trace: " . $exception->getTraceAsString();
        }
        $this->log($errorMessage, 'ERROR');
    }
}

/**
 * CLASE PDF PERSONALIZADA
 */
class InformePDF extends TCPDF {
    public function __construct() {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->SetMargins(15, 15, 15);
        $this->SetAutoPageBreak(TRUE, 15);
        $this->setHeaderMargin(5);
        $this->setFooterMargin(10);
    }
    
    public function Header() {
        $image_file = 'header/header.jpg';
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'INFORME VISITA DOMICILIARIA', 0, false, 'C');
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

/**
 * CLASE PRINCIPAL PARA GENERACIÓN DE INFORME MODULAR
 */
class InformeVisitaDomiciliariaModular {
    private $pdf;
    private $id_cedula;
    private $mysqli;
    private $config;
    private $logger;
    private $modulos;
    
    public function __construct($id_cedula, $mysqli) {
        $this->id_cedula = $id_cedula;
        $this->mysqli = $mysqli;
        $this->config = new ConfiguracionInforme();
        $this->logger = new Logger();
        $this->inicializarModulos();
        $this->initializePDF();
    }
    
    /**
     * Inicializar todos los módulos
     */
    private function inicializarModulos() {
        $this->modulos = [
            'perfil' => new PerfilModule($this->id_cedula, $this->mysqli),
            'camara_comercio' => new CamaraComercioModule($this->id_cedula, $this->mysqli),
            'estado_salud' => new EstadoSaludModule($this->id_cedula, $this->mysqli),
            'composicion_familiar' => new ComposicionFamiliarModule($this->id_cedula, $this->mysqli),
            'informacion_pareja' => new InformacionParejaModule($this->id_cedula, $this->mysqli),
            'inventario' => new InventarioModule($this->id_cedula, $this->mysqli)
        ];
    }
    
    /**
     * Inicializar configuración del PDF
     */
    private function initializePDF() {
        $this->pdf = new InformePDF();
        $this->pdf->SetTitle('Informe Visita Domiciliaria');
        $this->pdf->SetAuthor('Sistema de Informes');
        $this->pdf->SetCreator('Informe Generator v' . REPORT_VERSION);
        $this->pdf->AddPage();
    }
    
    /**
     * Generar el informe completo
     */
    public function generarInforme() {
        try {
            $this->logger->log('Iniciando generación de informe modular');
            
            // Construir contenido del informe
            $contenido = $this->construirContenido();
            
            // Escribir contenido al PDF
            $this->pdf->writeHTML($contenido, true, false, true, false, '');
            
            // Generar archivo
            $filename = $this->generarNombreArchivo();
            $this->pdf->Output($filename, 'I');
            
            $this->logger->log('Informe modular generado exitosamente: ' . $filename);
            
        } catch (Exception $e) {
            $this->logger->logError('Error al generar informe modular: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Construir contenido completo del informe
     */
    private function construirContenido() {
        $secciones = [
            'encabezado' => $this->generarEncabezado(),
            'perfil' => $this->modulos['perfil']->generarSeccion(),
            'informacion_personal' => $this->modulos['perfil']->generarInformacionPersonal(),
            'camara_comercio' => $this->modulos['camara_comercio']->generarSeccion(),
            'estado_salud' => $this->modulos['estado_salud']->generarSeccion(),
            'composicion_familiar' => $this->modulos['composicion_familiar']->generarSeccion(),
            'informacion_pareja' => $this->modulos['informacion_pareja']->generarSeccion(),
            'inventario' => $this->modulos['inventario']->generarSeccion()
        ];
        
        return $this->construirHTML($secciones);
    }
    
    /**
     * Generar nombre de archivo único
     */
    private function generarNombreArchivo() {
        $timestamp = date('Y-m-d_H-i-s');
        return "informe_visita_{$this->id_cedula}_{$timestamp}.pdf";
    }
    
    /**
     * Construir HTML final
     */
    private function construirHTML($secciones) {
        $html = '<div style="border: 2px solid rgb(175, 0, 0);">';
        
        foreach ($secciones as $seccion) {
            if (!empty($seccion)) {
                $html .= $seccion;
            }
        }
        
        $html .= '</div>';
        return $html;
    }
    
    /**
     * Generar encabezado del informe
     */
    private function generarEncabezado() {
        $logo = $this->getLogoHTML();
        $info = $this->getInfoHTML();
        
        return "
        <table cellpadding='5' style='{$this->config->styles['table']}'>
            <tr style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>
                <td width='100%' style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>{$logo}</td>
            </tr>
        </table>
        {$info}";
    }
    
    /**
     * Generar HTML del logo
     */
    private function getLogoHTML() {
        return '<img src="header/header.jpg" alt="Logo" style="width: ' . MAX_IMAGE_WIDTH . 'px; height: ' . MAX_IMAGE_HEIGHT . 'px;">';
    }
    
    /**
     * Generar HTML de información del documento
     */
    private function getInfoHTML() {
        return "
        <table cellpadding='5' style='{$this->config->styles['table']}'>
            <tr style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>
                <td style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>Código: " . REPORT_CODE . "</td>
            </tr>
            <tr style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>
                <td style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>Versión: " . REPORT_VERSION . "</td>
            </tr>
            <tr style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>
                <td style='border: {$this->config->borderWidth}px solid rgb(" . implode(',', $this->config->borderColor) . ");'>Fecha de vigencia: " . REPORT_VALIDITY . "</td>
            </tr>
        </table>";
    }
}

// EJECUCIÓN PRINCIPAL
try {
    $informe = new InformeVisitaDomiciliariaModular($id_cedula, $mysqli);
    $informe->generarInforme();
} catch (Exception $e) {
    error_log('Error crítico en generación de informe modular: ' . $e->getMessage());
    echo "Error al generar el informe: " . $e->getMessage();
}
?> 